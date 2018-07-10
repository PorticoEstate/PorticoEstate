<?php
	/**
	 * Created by PhpStorm.
	 * User: eskil.saatvedt
	 * Date: 05.04.2018
	 * Time: 15:11
	 */

	namespace AppBundle\Service;

	use AppBundle\Entity\FmHandymanLog;
	use AppBundle\Entity\FmTtsTicket;
	use AppBundle\Entity\GwAccount;
	use AppBundle\Entity\GwPreference;
	use AppBundle\Entity\GwVfs;
	use AppBundle\Entity\HmManagerForBuildingView;
	use AppBundle\Entity\HmTechnicalContactForBuildingView;
	use AppBundle\Repository\GwPreferenceRepository;
	use AppBundle\Repository\FmTtsTicketRepository;
	use DateTime;
	use Doctrine\Common\Persistence\Mapping\MappingException;
	use Doctrine\ORM\OptimisticLockException;
	use Doctrine\ORM\ORMException;
	use PHPMailer\PHPMailer\Exception;
	use SimpleXMLElement;
	use Doctrine\ORM\EntityManager as EntityManager;
	use AppBundle\Entity\FmHandymanDocument;
	use AppBundle\Entity\GwConfig;

	class ParseMessageXMLService
	{
		const FILE_PREFIX = 'eOrdr';

		/* @var EntityManager $em */
		private $em;
		private $dir;
		private $ext;
		private $hm_user;
		private $admin_user;
		private $messages_data = array();
		private $tickets;
		private $error_message;
		private $current_file = '';
		private $number_of_tickets = 0;
		/* @var DateTime $previous_import_date */
		private $previous_import_date;

		/* @var GetDocumentFromHandymanService $document_service */
		private $document_service;

		/* @var array<GwPreference> $users_with_agresso_id */
		private $users_with_agresso_id;

		/**
		 * MessageService constructor.
		 * @param EntityManager $em
		 * @param string $dir Where do we pick ut the files
		 * @param string $ext Extension to the Handyman files
		 * @param string $url URL to web server, serving the files form Handyman
		 * @param int $hm_user The user named Handyman in BKBygg, used as owner of images
		 * @param int $admin_user The admin/soneleder user to use if the responsible person did not match any user
		 * @throws ORMException
		 */
		public function __construct(EntityManager $em, $dir, $ext, $url, $hm_user, $admin_user)
		{
			$this->em = $em;
			$this->dir = $dir;
			$this->ext = $ext;
			$this->hm_user = $hm_user;
			$this->admin_user = $admin_user;
			$this->document_service = new GetDocumentFromHandymanService($url);
			$this->users_with_agresso_id = $this->em->getRepository('AppBundle:GwPreference')->findUsersWithPropertyResourceNr();

			/* @var FmHandymanLog $previous_import_log */
			$previous_import_log = $this->em->getRepository('AppBundle:FmHandymanLog')->findLast();
			if ($previous_import_log) {
				$this->previous_import_date = $previous_import_log->getLogDate();
			} else {
				$this->previous_import_date = new DateTime('2000-01-01'); // some date in the far past
			}
		}

		public function parse_dir()
		{
			$files = $this->find_files();
			/* @var $file string */
			foreach ($files as $file) {
				if (!file_exists($file)) {
					continue;
				}
				if (!is_readable($file)) {
					continue;
				}
				if (filectime($file) <= $this->previous_import_date->getTimestamp()) {
					continue;
				}

				$this->current_file = $file;
				$this->tickets = array();

				/* @var SimpleXMLElement $xml */
				$xml = simplexml_load_file($file);
				$this->parse_xml($xml);

				$this->filter_and_save_tickets();
				$this->number_of_tickets += count($this->tickets);
			}
			$this->delete_files($files);
		}

		private function find_files(): array
		{
			$pattern = $this->dir . DIRECTORY_SEPARATOR . $this::FILE_PREFIX . '*.' . $this->ext;
			return glob($pattern);
		}

		/**
		 * @param SimpleXMLElement $xml
		 */
		private function parse_xml(SimpleXMLElement $xml)
		{
			$orders = $xml->xpath('Order');
			if (empty($orders)) {
				return;
			}
			/* @var SimpleXMLElement $order */
			foreach ($orders as $order) {
				$this->parse_order_xml($order);
			}
		}

		/**
		 * @param SimpleXMLElement $order
		 */
		private function parse_order_xml(SimpleXMLElement $order)
		{
			if (!$this->validate_order($order)) {
				return;
			}

			if ($this->has_document_data($order)) {
				$this->collect_and_store_document_data($order);
			}

			if (!$this->contain_checklist($order)) {
				return;
			}

			if (!$this->is_order_completed($order)) {
				return;
			}

			if (!$this->validate_checklist($order->xpath('ChecklistList/ChecklistType'))) {
				return;
			}

			$this->collect_order_data($order);
		}

		/**
		 * Do a minimal test on the order xml to discard orders we are not interested in as fast as possible
		 * @param SimpleXMLElement $order
		 * @return bool
		 */
		private function validate_order(SimpleXMLElement $order): bool
		{
			if (empty($order->xpath('OrderHead'))) {
				return false;
			}
			if ($order->xpath('OrderHead[OrderType != 12]')) {
				return false;
			}
			if ($order->xpath('OrderHead/Elements[Checklists != 1]')) {
				return false;
			}
			if (empty($order->xpath('OrderHead/InstallationList'))) {
				return false;
			}
			if (empty($order->xpath('HSOrderNumber'))) {
				return false;
			}
			return true;
		}

		/**
		 * @param SimpleXMLElement $order
		 * @return bool
		 */
		private function contain_checklist(SimpleXMLElement $order): bool
		{
			if (empty($order->xpath('ChecklistList/ChecklistType'))) {
				return false;
			}

			return true;
		}

		/* @var array<SimpleXMLElement> $checklist
		 * @return bool
		 */
		private function validate_checklist(array $checklist): bool
		{
			$result = true;
			/* @var SimpleXMLElement $checklist_item */
			foreach ($checklist as $checklist_item) {
				$result &= $this->validate_checklist_item($checklist_item);
			}
			return $result;
		}

		/**
		 * @param SimpleXMLElement $checklist_item
		 * @return bool
		 */
		private function validate_checklist_item(SimpleXMLElement $checklist_item): bool
		{
			if (count($checklist_item->xpath('Finished')) == 0) {
				return false;
			}
			if ($checklist_item->xpath('Finished')[0][0] == 0) {
				return false;
			}
			if (count($checklist_item->xpath('Checklist/Check')) == 0) {
				return false;
			}
			// DataType 3 = OK/Discrepancy
			// State 2 : No
			if (count($checklist_item->xpath('Checklist/Check[DataType = 3][State = 2][SubItem = 0]')) == 0) {
				return false;
			}
			// DataType 3 = Text
			// SubItem = 1 (is a child)
			if (count($checklist_item->xpath('Checklist//Check[DataType = 4][SubItem=1][State != ""]')) == 0) {
				return false;
			}
			return true;
		}

		/**
		 * @param SimpleXMLElement $order
		 * @return bool
		 */
		private function is_order_completed(SimpleXMLElement $order): bool
		{
			return (bool)$order->xpath('OrderHead[Completed = 1]');
		}

		/**
		 * @param SimpleXMLElement $order
		 */
		private function collect_order_data(SimpleXMLElement $order)
		{
			$this->messages_data = array();
			/* @var SimpleXMLElement $checlist */
			foreach ($order->xpath('ChecklistList/ChecklistType') as $checklist) {
				$this->mine_data_from_checklist($checklist, $order);
			}

			$this->update_message_users();
			/* @var MessageData $message */
			foreach ($this->messages_data as $message) {
				if ($message->validate()) {
					$message->description .= $this->error_message;
					$this->tickets[] = $message->message_to_ticket();
				}
			}
		}

		/**
		 * Store the metadata from the DocumentList in BKByggs db
		 *
		 * @param SimpleXMLElement $order
		 */
		private function collect_and_store_document_data(SimpleXMLElement $order)
		{
			$documents = $order->xpath('DocumentList/Document');
			/* @var SimpleXMLElement $document */
			foreach ($documents as $document) {
				if ($this->document_contain_data($document)) {
					try {
						// Do this exist in the DB
						$existing = $this->em->getRepository('AppBundle:FmHandymanDocument')->findOneBy(array('hs_document_id' => (int)$document->HSDocumentID));
						if (!empty($existing)) {
							continue;
						}

						/* @var FmHandymanDocument $fHDoc */
						$fHDoc = new FmHandymanDocument();
						$fHDoc->setHsDocumentId((int)$document->HSDocumentID);
						$fHDoc->setName((string)$document->Name);
						$fHDoc->setFilePath((string)$document->FilePath);
						$fHDoc->setFileExtension((string)$document->FileExtension);
						$fHDoc->setHmInstallationId((string)$document->InstallationID);
						$fHDoc->setCreatedDate(new DateTime());
						$fHDoc->setRetrievedFromHandyman(false);
						$fHDoc->setHsOrderNumber((int)$order->HSOrderNumber);
						// This field is optional in the xml, but will be present if and only if it is added in Handyman
						$fHDoc->setHsChecklistId((int)$document->HSChecklistID);
						$this->em->persist($fHDoc);
						$this->em->flush();
					} catch (ORMException $e) {
						continue;
					}
				}
			}
		}

		/**
		 * Do this Order have documents
		 * @param SimpleXMLElement $order
		 * @return bool
		 */
		private function has_document_data(SimpleXMLElement $order): bool
		{
			$documents = $order->xpath('DocumentList/Document');
			if (!(bool)$documents) {
				return false;
			}
			$result = false;
			/* @var SimpleXMLElement $document */
			foreach ($documents as $document) {
				if ($this->document_contain_data($document)) {
					$result = true;
				}
			}
			return $result;
		}

		/**
		 * @param SimpleXMLElement $document
		 * @return bool
		 */
		private function document_contain_data(SimpleXMLElement $document)
		{
			if (!empty($document->HSDocumentID)
				&& !empty($document->Name)
				&& !empty($document->FilePath)
				&& !empty($document->FileExtension)
				&& !empty($document->InstallationID)
				&& !empty($document->HSChecklistID)
			) {
				return true;
			}
			return false;
		}

		/**
		 * @param SimpleXMLElement $checklist
		 * @param SimpleXMLElement $order
		 */
		private function mine_data_from_checklist(SimpleXMLElement $checklist, SimpleXMLElement $order)
		{
			if (!$this->checklist_contain_data($checklist)) {
				return;
			}

			$message = new MessageData();
			$message->order_id = (int)$order->HSOrderNumber;
			$message->soneleder_fra_handyman = (int)$order->OrderHead->Manager;
			$message->order_name = (string)$order->OrderHead->OrderName;
			$message->checklist_id = (int)$checklist->HSChecklistID;
			$message->emloyee_from_handyman = (int)$checklist->EmployeeNo;
			$message->checklist_description[] = (string)$checklist->ChecklistTypeName;
			$message->checklist_description[] = (string)$checklist->ChecklistName;
			$message->is_valid = true;

			$installation_id = trim((string)$checklist->InstallationID);
			if ($this->is_installation_id_valid($installation_id)) {
				$message->location_code = $installation_id;
				$message->loc1 = $this->get_loc1_code($installation_id);
				$message->loc2 = $this->get_loc2_code($installation_id);
			} else {
				// We are not able to figure out the building ID
				$message->is_valid = false;
				return;
			}

			// Loop through all Checklists to collect one title and one message for each checklist containing a diversion
			$tiles_and_descriptions = $this->find_checklist_titles_and_descriptions($checklist->Checklist);
			if (count($tiles_and_descriptions) > 0) {
				foreach ($tiles_and_descriptions as $item) {
					$current_message = $message->clone();
					$current_message->checklist_name[] = $item['title'];
					$current_message->checklist_description[] = (string)$item['description'];
					$current_message->checklist_description[] = (string)$item['comment'];
					$current_message->checklist_description[] = (string)$checklist->ChecklistTypeName;
					$current_message->checklist_description[] = (string)$checklist->ChecklistName;
					$this->messages_data[] = $current_message;
				}
			}
		}

		/**
		 * @var SimpleXMLElement $checklist
		 * @return array
		 **/
		private function find_checklist_titles_and_descriptions(SimpleXMLElement $checklist): array
		{
			$result = array();
			$comment = '';

			/* @var SimpleXMLElement checks */
			$checks = $checklist->Check;
			if ((string)$checks[$checks->count() - 1]->Text == 'Kommentar') {
				$comment = (string)$checks[$checks->count() - 1]->Text;
			}

			$found_discrepancy = false;
			$found_name = '';
			/* @var SimpleXMLElement $check */
			foreach ($checklist->Check as $check) {
				if ($found_discrepancy) {
					// DataType 4 = Text field
					if ((int)$check->SubItem == 1 && (int)$check->DataType == 4) {
						$title = (string)$check->State;
						$result[] =
							array('title' => (string)$check->State,
								'description' => $found_name,
								'comment' => $comment
							);
					}
				}

				// DataType 3 = OK/Discrepancy
				// State 2 : No
				if ((int)$check->DataType == 3 && (int)$check->State == 2) {
					$found_discrepancy = true;
					$found_name = (string)$check->Text . " " . (string)$check->Instructions;
				} else {
					$found_discrepancy = false;
					$found_name = '';
				}
			}
			return $result;
		}

		/**
		 * @param SimpleXMLElement $checklist
		 * @return bool
		 */
		private function checklist_contain_data(SimpleXMLElement $checklist): bool
		{
			if (empty($checklist->Finished)) {
				return false;
			}
			if (empty($checklist->Checklist)) {
				return false;
			}

			// Was the previous item a checkbox
			$found_discrepancy = false;
			/* @var SimpleXMLElement $checkItem */
			foreach ($checklist->xpath('Checklist/Check') as $check) {
				if ($found_discrepancy) {
					// DataType 4 = Text field
					if ((int)$check->SubItem == 1 && (int)$check->DataType == 4) {
						// We found at least one
						return true;
					}
				}
				// DataType 3 = OK/Discrepancy
				// State 2 : No
				if ((int)$check->DataType == 3 && (int)$check->State == 2) {
					$found_discrepancy = true;
				} else {
					$found_discrepancy = false;
				}
			}
			return false;
		}

		/**
		 * @param string $installation_id
		 * @return bool
		 */
		private static function is_installation_id_valid(string $installation_id): bool
		{
			// XXXX-XX
			$pattern = '/^([0-9]{4})(-){1}([0-9]{2})$/';
			return preg_match($pattern, $installation_id, $match);
		}

		/**
		 * @param string $installation_id
		 * @return string
		 */
		private static function get_loc1_code(string $installation_id): string
		{
			// XXXX
			$pattern = '/^([0-9]{4})/';
			if (preg_match($pattern, $installation_id, $match)) {
				return $match[0];
			}
			return '';
		}

		/**
		 * @param string $installation_id
		 * @return string
		 */
		private static function get_loc2_code(string $installation_id): string
		{
			// NNNN-XX
			$pattern = '/([0-9]{2})$/';
			if (preg_match($pattern, $installation_id, $match)) {
				return $match[0];
			}
			return '';
		}

		/**
		 * @param MessageData $message
		 * @return int
		 */
		private function get_user_id(MessageData $message): int
		{
			// Who created this message, as account ID
			// We have the agresso ID for the techinican and a default user "Handyman"

			/* @var GwPreference $pref */
			$pref = $this->find_account_id_in_preferences($message->emloyee_from_handyman);
			if ($pref) {
				return $pref->getPreferenceOwner();
			} else {
				$message->post_message .= '<br/>Vedlikeholds teknikker med Agresso ID ' . $message->emloyee_from_handyman . ' ble ikke funnet i BK Bygg.';
				return $this->hm_user;
			}
		}

		/**
		 * @param MessageData $message
		 * @return int
		 */
		private function get_contact_id(MessageData $message): int
		{
			// Who is the recipient of the Ticket
			// This is a contact user, and not a account ID in BK Bygg

			// We have the agresso id for the manager in Handyman, the building number, and the fallback manager in bk bygg.
			/* @var GwPreference $pref */
			$pref = $this->find_account_id_in_preferences($message->soneleder_fra_handyman);
			$contact_id = null;
			if ($pref) {
				$contact_id = $pref->getAccount()->getPersonId();
			}

			if (!$contact_id) {

				/* @var HmManagerForBuildingView $contact */
				$manager = $this->em->getRepository('AppBundle:HmManagerForBuildingView')->find($message->loc1);
				if (!empty($manager)) {
					$contact_id = $manager->getAccount()->getPersonId();
					$message->post_message .= '<br/>Manager med Agresso ID ' . $message->soneleder_fra_handyman . ' ble ikke funnet i BK Bygg. Overført til Soneleder.';
				}
			}
			if (!$contact_id) {
				/* @var GwAccount $account */
				$account = $this->em->getRepository('AppBundle:GwAccount')->findOneBy(array('account_id' => $this->admin_user));
				$contact_id = $account->getPersonId();
				$message->post_message .= '<br/>Hverken Manager eller Soneleder for bygget ble funnet. Overført til admin.';
			}

			return $contact_id;
		}

		private function update_message_users()
		{
			if (empty($this->messages_data)) {
				return;
			}

			/* @var MessageData $message */
			foreach ($this->messages_data as $message) {
				$message->user_id = $this->get_user_id($message);
				$message->contact_id = $this->get_contact_id($message);
			}
		}

		/**
		 * @param $preferences
		 * @param $agresso_id
		 * @return GwPreference
		 */
		private function find_account_id_in_preferences($agresso_id)
		{
			if (empty($agresso_id)) {
				return null;
			}
			/* @var GwPreference $pref */
			foreach ($this->users_with_agresso_id as $pref) {
				if ($pref->getResourceNumber() == $agresso_id) {
					return $pref;
				}
			}
			return null;
		}

		/**
		 * @return array
		 */
		private function get_hm_order_numbers(): array
		{
			$result = array();
			/* @var FmTtsTicket $fm_ticket */
			foreach ($this->tickets as $fm_ticket) {
				if ($fm_ticket->getHandymanOrderNumber()) {
					$result[] = $fm_ticket->getHandymanOrderNumber();
				}
			}
			return $result;
		}

		/**
		 * @param $listOfTicketsToPreventDuplicates
		 * @return array
		 */
		private function extract_tds_from_ticket_list($listOfTicketsToPreventDuplicates): array
		{
			$result = array();
			/* @var array $ticket */
			foreach ($listOfTicketsToPreventDuplicates as $ticket) {
				$result[] = $ticket['handyman_order_number'];
			}
			return $result;
		}

		/**
		 * Will save any new tickets, old tickets will not be duplicated
		 */
		private function filter_and_save_tickets()
		{
			if (empty($this->tickets)) {
				return;
			}
			$handyman_order_numbers = $this->get_hm_order_numbers();
			/* @var FmTtsTicketRepository $rep */
			$rep = $this->em->getRepository('AppBundle:FmTtsTicket');
			$listOfTicketsToPreventDuplicates = $rep->findTicketsWithHandymanOrderIDasArray($handyman_order_numbers);
			$arrOfIds = $this->extract_tds_from_ticket_list($listOfTicketsToPreventDuplicates);
			$doc_rep = $this->em->getRepository('AppBundle:FmHandymanDocument');
			$config_rep = $this->em->getRepository('AppBundle:GwConfig');

			/* @var GwConfig $file_dir_config */
			$file_dir_config = $config_rep->findOneBy(array('config_app' => 'phpgwapi', 'config_name' => 'files_dir'));
			$file_dir = $file_dir_config->getConfigValue();

			/* @var FmTtsTicket $fm_ticket */
			foreach ($this->tickets as $fm_ticket) {
				if (in_array($fm_ticket->getHandymanOrderNumber(), $arrOfIds)) {
					continue;
				}

				try {
					$this->em->persist($fm_ticket);
					$this->em->flush();
					$id = $fm_ticket->getId();
					$user_id = $fm_ticket->getUserId();
					$hs_checklist_id = $fm_ticket->getHandymanChecklistId();
					$this->em->clear();

					$docs = $doc_rep->findBy(array('hs_checklist_id' => $hs_checklist_id));
					// Is there files to fetch?
					if (!empty($docs)) {
						$dir = '/property/fmticket/' . (string)$id;
						$full_dir = $file_dir . $dir;
						mkdir($full_dir, 0700, true);
						/* @var FmHandymanDocument $doc */
						foreach ($docs as $doc) {
							$this->create_and_save_vfs_documents($doc, $full_dir, $dir, $user_id, $id);
						}
					}
				} catch (OptimisticLockException $e) {
				} catch (ORMException $e) {
				} catch (MappingException $e) {
				}
			}
		}


		private function delete_files($files)
		{
			foreach ($files as $file) {
				if (is_file($file)) {
					unlink($file); // delete file
				}
			}
		}

		/**
		 * @param FmHandymanDocument $doc The Handymandocument we are to retrieve
		 * @param string $full_dir Full path to the file
		 * @param string $dir Part of the path as stored in phpgw_vfs
		 * @param int $user_id The BKBygg ID of the user who created the ticket
		 * @param int $fm_ticket_id
		 * @throws MappingException
		 * @throws ORMException
		 */
		private function create_and_save_vfs_documents(FmHandymanDocument $doc, string $full_dir, string $dir, int $user_id, int $id)
		{
			$file_path = $this->document_service->retrieve_file_from_handyman($doc, $full_dir);
			if (!empty($file_path)) {
				try {
					/* @var GwVfs $vfs */

					$vfs = new GwVfs();
					$vfs->setOwnerId($user_id);
					$vfs->setCreatedbyId($user_id);
					$vfs->setCreated(new DateTime());
					$vfs->setSize(4096);
					$vfs->setMimeType('Directory');
					$vfs->setApp('property');
					$vfs->setDirectory('/property/fmticket');
					$vfs->setName((string)$id);
					$vfs->setVersion('0.0.0.1');
					$this->em->persist($vfs);
					$this->em->flush();
					$this->em->clear();

					$vfs = new GwVfs();
					$vfs->setOwnerId($user_id);
					$vfs->setCreatedbyId($user_id);
					$vfs->setCreated(new DateTime());
					$vfs->setSize(4096);
					$vfs->setMimeType('journal');
					$vfs->setMimeType('journal');
					$vfs->setComment('Created');
					$vfs->setApp('property');
					$vfs->setDirectory('/property/fmticket');
					$vfs->setName((string)$id);
					$vfs->setVersion('0.0.0.0');
					$this->em->persist($vfs);
					$this->em->flush();
					$this->em->clear();

					$vfs = new GwVfs();
					$vfs->setOwnerId($user_id);
					$vfs->setCreatedbyId($user_id);
					$vfs->setCreated(new DateTime());
					$vfs->setSize(filesize($file_path));
					$vfs->setMimeType(mime_content_type($file_path));
					$vfs->setApp('property');
					$vfs->setDirectory($dir);
					$vfs->setName($this->document_service->sanitize_file_name($doc->getFilePath()));
					$vfs->setVersion('0.0.0.1');
					$this->em->persist($vfs);
					$this->em->flush();
					$this->em->clear();

					$vfs = new GwVfs();
					$vfs->setOwnerId($user_id);
					$vfs->setCreatedbyId($user_id);
					$vfs->setCreated(new DateTime());
					$vfs->setMimeType('journal');
					$vfs->setComment('Created');
					$vfs->setApp('property');
					$vfs->setDirectory($dir);
					$vfs->setName($this->document_service->sanitize_file_name($doc->getFilePath()));
					$vfs->setVersion('0.0.0.0');
					$this->em->persist($vfs);
					$this->em->flush();
					$this->em->clear();

					$doc->setRetrievedFromHandyman(true);
					$doc->setRetrievedDate(new DateTime());
					$doc->setMessageId($id);
					$this->em->persist($vfs);
					$this->em->flush();
					$this->em->clear();
				} catch (ORMException $e) {
					throw $e;
				} catch (MappingException $e) {
					throw $e;
				}
			}
		}

		/**
		 * @return int
		 */
		public function get_number_of_tickets(): int
		{
			return $this->number_of_tickets;
		}
	}


	class MessageData
	{
		public $user_id = null;
	//	public $assigned_to = null;
		public $subject = null;
		public $description = null;
		public $location_code = null;
		public $loc1 = null;
		public $loc2 = null;
		public $contact_id = null;
		public $checklist_id = null;
		public $order_id = null;
		public $soneleder_fra_handyman = null;
	//	public $soneleder_fra_byggid = null;
		public $emloyee_from_handyman = null;
		public $order_name = null;
		public $is_valid = false;
		public $post_message = '';


		public $checklist_name = array();
		public $checklist_description = array();

		/* @return MessageData */
		public function clone(): MessageData
		{
			return clone $this;
		}

		public function validate()
		{
			return $this->is_valid && isset($this->user_id) && isset($this->location_code) && isset($this->loc1);
		}

		/**
		 * @return FmTtsTicket|null
		 */
		public function message_to_ticket(): FmTtsTicket
		{
			/* @var FmTtsTicket $result */
			$result = new FmTtsTicket();
			$result->set_default_values();
			$result->setSubject('#' . $this->order_id . ' ' . implode($this->checklist_name, ' '));
			$result->setDetails(implode($this->checklist_description, '<br/>') . $this->post_message . '<br/> Laget av Handyman');
			$result->setHandymanOrderNumber($this->order_id);
			$result->setHandymanChecklistId($this->checklist_id);
			$result->setLocationCode($this->location_code);
			$result->setLoc1($this->loc1);
			$result->setLoc2($this->loc2);
			$result->setUserId($this->user_id);
	//		$result->setAssignedto($this->assigned_to);
			$result->setContactId($this->contact_id);
			return $result;
		}
	}