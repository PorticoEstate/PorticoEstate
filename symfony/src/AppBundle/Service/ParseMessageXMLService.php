<?php
/**
 * Created by PhpStorm.
 * User: eskil.saatvedt
 * Date: 05.04.2018
 * Time: 15:11
 */

namespace AppBundle\Service;

use AppBundle\Entity\FmTtsTicket;
use AppBundle\Entity\GwPreference;
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

class ParseMessageXMLService
{
	const FILE_PREFIX = 'eOrdr';

	/* @var EntityManager $em */
	private $em;
	private $dir;
	private $ext;

	private $messagesData = array();
	private $tickets;

	private $error_message;

	/**
	 * MessageService constructor.
	 * @param EntityManager $em
	 * @param $dir
	 * @param $ext
	 */
	public function __construct(EntityManager $em, $dir, $ext)
	{
		$this->em = $em;
		$this->dir = $dir;
		$this->ext = $ext;
	}


	public function parseDir()
	{
		$files = $this->find_files();
		/* @var $file string */
		foreach ($files as $file) {
			$this->tickets = array();
			if (!file_exists($file)) {
				return;
			}

			/* @var SimpleXMLElement $xml */
			$xml = simplexml_load_file($file);
			$this->parse_xml($xml);

			$this->filter_and_save_tickets();
		}
		$this->delete_files($files);
	}

	private function find_files(): array
	{
		$pattern = $this->dir . DIRECTORY_SEPARATOR . Self::FILE_PREFIX . '*.' . $this->ext;
		return glob($pattern);
	}

	/**
	 * @param SimpleXMLElement $xml
	 */
	private function parse_xml(SimpleXMLElement $xml)
	{
		$orders = $xml->xpath('Order');
		if(empty($orders)){
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
		$this->messagesData = array();
		/* @var SimpleXMLElement $checlist */
		foreach ($order->xpath('ChecklistList/ChecklistType') as $checklist) {
			$this->mine_data_from_checklist($checklist, $order);
		}

		$this->update_message_users();
		/* @var MessageData $message */
		foreach ($this->messagesData as $message) {
			if ($message->validate()) {
				$fm_ticket = $message->message_to_ticket();
				$this->tickets[] = $fm_ticket;
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
			$document_data = array();
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
		if(!$this->checklist_contain_data($checklist)){
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
			$message->loc1 = $this->getLoc1Code($installation_id);
			$message->loc2 = $this->getLoc2Code($installation_id);
			$message->contact_id = $this->find_contact_id_from_location_code($message->loc1);
		} else {
			$message->is_valid = false;
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
				$this->messagesData[] = $current_message;
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
				$found_name = (string)$check->Instructions;
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
	private function checklist_contain_data( SimpleXMLElement $checklist): bool
	{
		if(empty($checklist->Finished)){
			return false;
		}
		if(empty($checklist->Checklist)){
			return false;
		}

		// Was the previous item a checkbox
		$found_discrepancy = false;
		/* @var SimpleXMLElement $checkItem */
		foreach($checklist->xpath('Checklist/Check') as $check){
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
	private static function getLoc1Code(string $installation_id): string
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
	private static function getLoc2Code(string $installation_id): string
	{
		// NNNN-XX
		$pattern = '/([0-9]{2})$/';
		if (preg_match($pattern, $installation_id, $match)) {
			return $match[0];
		}
		return '';
	}

	/**
	 * Retrieve the BKBygg ID of the technical responsible person for a bulding/property in BK Bygg
	 * @param string $location_code
	 * @return int
	 */
	public function find_contact_id_from_location_code(string $location_code): ?int
	{
		/* @var HmTechnicalContactForBuildingView $contact*/
		$contact = $this->em->getRepository('AppBundle:HmTechnicalContactForBuildingView')->find($location_code);
		if(!$contact){
			return null;
		}
		return $contact->getContactId();
	}

	private function update_message_users()
	{
		$ids = array();
		/* @var MessageData $message */
		foreach ($this->messagesData as $message) {
			if ($message->emloyee_from_handyman) {
				$ids[] = $message->emloyee_from_handyman;
			}
			if ($message->soneleder_fra_handyman) {
				$ids[] = $message->soneleder_fra_handyman;
			}
		}

		try {
			// Get the users based on those IDs. they come from Agresso, and is located in the Instilliner->Eiendom->Ditt ressursnummer
			/* @var GwPreferenceRepository $rep */
			$rep = $this->em->getRepository('AppBundle:GwPreference');
			$preferences = $rep->findUsersByAgressoID($ids);
		} catch (ORMException $e) {
			$this->error_message .= '\nWARNING\tUnable to find User from phpgw_preferences.';
		}
		/* @var MessageData $message */
		foreach ($this->messagesData as $message) {
			if ($message->emloyee_from_handyman) {
				$message->user_id = $this->find_user_id_in_preferences($preferences, $message->emloyee_from_handyman);
			} else {
				$this->error_message .= '\nWARNING\tUser with Handyman ID ' . $message->emloyee_from_handyman . ' not found in BK Bygg.';
				$message->is_valid = false;
			}
			if ($message->soneleder_fra_handyman) {
				$message->assigned_to = $this->find_user_id_in_preferences($preferences, $message->soneleder_fra_handyman);
			} else {
				$this->error_message .= '\nWARNING\tManager with Handyman ID ' . $message->soneleder_fra_handyman . ' not found in BK Bygg.';
				$message->is_valid = false;
			}
		}
	}

	/**
	 * @param $preferences
	 * @param $agresso_id
	 * @return int
	 */
	private function find_user_id_in_preferences($preferences, $agresso_id): ?int
	{
		/* @var GwPreference $pref */
		foreach ($preferences as $pref) {
			if ($pref->getResourceNumber() == $agresso_id) {
				return $pref->getPreferenceOwner();
			}
		}
		return null;
	}

	/**
	 * @return array
	 */
	private function getHMOrderNumbers(): array
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
	private function extractIdsFromTicketList($listOfTicketsToPreventDuplicates): array
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
		$handyman_order_numbers = $this->getHMOrderNumbers();
		/* @var FmTtsTicketRepository $rep */
		$rep = $this->em->getRepository('AppBundle:FmTtsTicket');
		$listOfTicketsToPreventDuplicates = $rep->findTicketsWithHandymanOrderIDasArray($handyman_order_numbers);
		$arrOfIds = $this->extractIdsFromTicketList($listOfTicketsToPreventDuplicates);

		/* @var FmTtsTicket $fm_ticket */
		foreach ($this->tickets as $fm_ticket) {
			if (in_array($fm_ticket->getHandymanOrderNumber(), $arrOfIds)) {
				continue;
			}

			try {
				$this->em->persist($fm_ticket);
				$this->em->flush();
				$this->em->clear();
			} catch (OptimisticLockException $e) {
			} catch (ORMException $e) {
			} catch (MappingException $e) {
			}
		}
	}

	private function delete_files($files)
	{
		foreach($files as $file){
			if(is_file($file)) {
				unlink($file); // delete file
			}
		}
	}
}


class MessageData
{
	public $user_id = null;
	public $assigned_to = null;
	public $subject = null;
	public $description = null;
	public $location_code = null;
	public $loc1 = null;
	public $loc2 = null;
	public $contact_id = null;
	public $checklist_id = null;
	public $order_id = null;
	public $soneleder_fra_handyman = null;
	public $emloyee_from_handyman = null;
	public $order_name = null;
	public $is_valid = false;


	public $checklist_name = array();
	public $checklist_description = array();

	/* @return MessageData */
	public function clone(): MessageData
	{
		return clone $this;
	}

	public function validate(){
		return $this->is_valid AND isset($this->user_id) AND isset($this->assigned_to) AND isset($this->location_code) AND isset($this->loc1);
	}

	/**
	 * @return FmTtsTicket|null
	 */
	public function message_to_ticket(): ?FmTtsTicket
	{
		/* @var FmTtsTicket $result*/
		$result = new FmTtsTicket();
		$result->set_default_values();
		$result->setSubject('#' . $this->order_id . ' ' . implode($this->checklist_name, ' '));
		$result->setDetails(implode($this->checklist_description, ' ') . '\r\n' . 'Laget av Handyman');
		$result->setHandymanOrderNumber($this->order_id);
		$result->setHandymanChecklistId($this->checklist_id);
		$result->setLocationCode($this->location_code);
		$result->setLoc1($this->loc1);
		$result->setLoc2($this->loc2);
		$result->setUserId($this->user_id);
		$result->setAssignedto($this->assigned_to);
		$result->setContactId($this->contact_id);
		return $result;
	}
}