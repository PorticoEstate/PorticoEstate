<?php
/**
 * Created by PhpStorm.
 * User: eskil.saatvedt
 * Date: 14.03.2018
 * Time: 09:16
 * For Handyman documentation, 6. Export form Handyman, 6.1 Orders and registrations
 * http://api.gsghandyman.no/
 * http://api.gsghandyman.no/6.1.Orders%20and%20registrations.xsd
 */

namespace AppBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager as EntityManager;
use AppBundle\Entity\FmTtsTicket;
use AppBundle\Entity\GwPreference;
use AppBundle\Entity\HmTechnicalContactForBuildingView;
use SimpleXMLElement;

class MessageService
{
	const FILE_PREFIX = 'eOrdr';

	/* @var array<string> $files */
	private $files = array();
	/* $var array<FmTtsTicket> $tickets */
	private $tickets = array();

	/* $var array<MessageData> $messages */
	private $messages = array();

	private $message = '';
	private $dir = '';
	private $ext = '';

	/* @var EntityManager $em */
	private $em;

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

	/*
	 * $return array<FmTtsTicket>
	 */
	public function get_tickets(): array
	{
		return $this->tickets;
	}

	/**
	 * @return string
	 */
	public function get_message(): string
	{
		return $this->message;
	}

	public function delete_files()
	{
		$this->message .= '\nERROR\tFiles not deleted';
	}

	public function find_files(): array
	{
		$pattern = $this->dir . DIRECTORY_SEPARATOR . Self::FILE_PREFIX . '*.' . $this->ext;
		$this->files = glob($pattern);
		if (count($this->files) == 0) {
			$this->message .= '\nERROR\t No export files found.';
		}
		return $this->files;
	}

	/*
	 * @var SimpleXMLElement $order
	 */
	private function validate_order(SimpleXMLElement $order): bool
	{
		if (!isset($order->OrderHead)) {
			return false;
		}

		if (!isset($order->OrderHead->OrderType)) {
			return false;
		}

		if ((int)$order->OrderHead->OrderType != 12) {
			return false;
		}

		if (!isset($order->OrderHead->Elements->Checklists)) {
			return false;
		}

		if ((int)$order->OrderHead->Elements->Checklists != 1) {
			return false;
		}

		if (!isset($order->OrderHead->InstallationList)) {
			return false;
		}

		if ($order->OrderHead->InstallationList->count() == 0) {
			return false;
		}

		if (!isset($order->HSOrderNumber)) {
			return false;
		}

		if (!$order->HSOrderNumber) {
			return false;
		}

		if (!isset($order->ChecklistList)) {
			return false;
		}

		if ($order->ChecklistList->count() == 0) {
			return false;
		}

		return true;
	}

	public function import_file(string $file)
	{
		if (!file_exists($file)) {
			$this->message .= '\nERROR\t File don\' exist: ' . $file;
			return;
		}

		/* @var SimpleXMLElement $xml */
		$xml = simplexml_load_file($file);
		if (!isset($xml->Order)) {
			// The xml list did not read correctly or is empty
			$this->message .= '\nERROR\t Unable to read the XML file: ' . $file;
			return;
		}

		/* @var SimpleXMLElement $order */
		foreach ($xml->Order as $order) {
			if (!$this->validate_order($order)) {
				continue;
			}

			if (!$this->checklist_includes_reportdata($order)) {
				continue;
			}

			$this->parse_order_and_create_ticket($order);
		}
	}

	/**
	 * @param SimpleXMLElement $order
	 */
	private function parse_order_and_create_ticket(SimpleXMLElement $order)
	{
		$this->messages = array();
		/* @var SimpleXMLElement $checlist */
		foreach ($order->ChecklistList->ChecklistType as $checklist) {
			$this->get_data_from_checklist($checklist, (int)$order->OrderHead->Manager, (int)$order->HSOrderNumber, (string)$order->OrderHead->OrderName);
		}

		$this->update_message_users();

		/* @var MessageData $message */
		foreach ($this->messages as $message) {
			if ($message->validate()) {
				$fm_ticket = new FmTtsTicket();
				$fm_ticket->set_default_values();
				$this->message_to_ticket($message, $fm_ticket);
				$this->tickets[] = $fm_ticket;
			}
		}
	}


	private function update_message_users()
	{
		$ids = array();
		/* @var MessageData $message */
		foreach ($this->messages as $message) {
			if ($message->emloyee_from_handyman) {
				$ids[] = $message->emloyee_from_handyman;
			}
			if ($message->soneleder_fra_handyman) {
				$ids[] = $message->soneleder_fra_handyman;
			}
		}

		// Get the users based on those IDs. they come from Agresso, and is located in the Instilliner->Eiendom->Ditt ressursnummer
		$preferences = $this->em->getRepository('AppBundle:GwPreference')->findUsersByAgressoID($ids);

		/* @var MessageData $message */
		foreach ($this->messages as $message) {
			if ($message->emloyee_from_handyman) {
				$message->user_id = $this->find_user_id_in_preferences($preferences, $message->emloyee_from_handyman);
			} else {
				$this->message .= '\nWARNING\tUser with Handyman ID ' . $message->emloyee_from_handyman . ' not found in BK Bygg.';
				$message->is_valid = false;
			}
			if ($message->soneleder_fra_handyman) {
				$message->assigned_to = $this->find_user_id_in_preferences($preferences, $message->soneleder_fra_handyman);
			} else {
				$this->message .= '\nWARNING\tManager with Handyman ID ' . $message->soneleder_fra_handyman . ' not found in BK Bygg.';
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
			if ($pref->resource_number == $agresso_id) {
				return $pref->preference_owner;
			}
		}
		return null;
	}

	/**
	 * @var MessageData $message
	 * @var FmTtsTicket $ticket
	 */
	private function message_to_ticket(MessageData $message, FmTtsTicket $ticket)
	{
		$title = '#' . $message->order_id . ' ' . implode($message->checklist_name, ' ');
		$description = implode($message->checklist_description, ' ') . '\r\n' . 'Laget av Handyman';
		$ticket->handyman_order_number = $message->order_id;
		$ticket->handyman_checklist_id = $message->checklist_id;
		$ticket->subject = $title;
		$ticket->details = $description;
		$ticket->location_code = $message->location_code;
		$ticket->loc1 = $message->loc1;
		$ticket->loc2 = $message->loc2;
		$ticket->user_id = $message->user_id;
		$ticket->assignedto = $message->assigned_to;
		$ticket->contact_id = $message->contact_id;
	}

	/* @var SimpleXMLElement $order
	 * @return bool
	 */
	private function checklist_includes_reportdata(SimpleXMLElement $order): bool
	{
		if (!(isset($order->OrderHead->FinishDate) && $order->OrderHead->FinishDate)) {
			return false;
		}

		$oneChecklistHasData = false;
		/* @var SimpleXMLElement $checklist_type */
		foreach ($order->ChecklistList->ChecklistType as $checklist_type) {
			$a = $this->do_checklist_item_have_report_data($checklist_type);
			$oneChecklistHasData = ($oneChecklistHasData OR $a);
		}
		return $oneChecklistHasData;
	}

	/* @var SimpleXMLElement $checklist_type
	 * @return bool
	 */
	private function do_checklist_item_have_report_data(SimpleXMLElement $checklist_type): bool
	{
		if (!(isset($checklist_type->Finished) && $checklist_type->Finished)) {
			return false;
		}
		if (!isset($checklist_type->Checklist)) {
			return false;
		}
		return $this->is_checklist_a_report($checklist_type->Checklist);
	}

	private function is_checklist_a_report(SimpleXMLElement $checklist): bool
	{
		if (!isset($checklist->Check)) {
			return false;
		}
		$found_discrepancy = false;

		/* @var SimpleXMLElement $check */
		foreach ($checklist->Check as $check) {
			if ($found_discrepancy) {
				// DataType 4 = Text field
				if ((int)$check->SubItem == 1 && (int)$check->DataType == 4) {
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
	private function is_installation_id_valid(string $installation_id): bool
	{
		// XXXX-XX
		$pattern = '/^([0-9]{4})(-){1}([0-9]{2})$/';
		return preg_match($pattern, $installation_id, $match);
	}

	/**
	 * @param string $installation_id
	 * @return string
	 */
	private function getLoc1Code(string $installation_id): string
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
	private function getLoc2Code(string $installation_id): string
	{
		// NNNN-XX
		$pattern = '/([0-9]{2})$/';
		if (preg_match($pattern, $installation_id, $match)) {
			return $match[0];
		}
		return '';
	}

	/**
	 * @param SimpleXMLElement $checklist
	 * @param int $soneleder_fra_handyman
	 * @param int $order_id
	 * @param string $order_name
	 */
	private function get_data_from_checklist(SimpleXMLElement $checklist, int $soneleder_fra_handyman, int $order_id, string $order_name)
	{
		if (!$this->do_checklist_item_have_report_data($checklist)) {
			return;
		}
		$message = new MessageData();
		$message->order_id = $order_id;
		$message->soneleder_fra_handyman = $soneleder_fra_handyman;
		$message->order_name = $order_name;
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
			$this->message .= '\nWARNING\tBuilding ID is not valid: ' . $installation_id;
		}

		/* @var array $name_and_descriptions */
		$name_and_descriptions = $this->checklist_item_titles($checklist->Checklist);
		if (count($name_and_descriptions) > 0) {
			foreach ($name_and_descriptions as $item) {
				$current_message = $message->clone();
				$current_message->checklist_name[] = $item['title'];
				$current_message->checklist_description[] = (string)$item['description'];
				$current_message->checklist_description[] = (string)$item['comment'];
				$current_message->checklist_description[] = (string)$checklist->ChecklistTypeName;
				$current_message->checklist_description[] = (string)$checklist->ChecklistName;
				$this->messages[] = $current_message;
			}
		}
	}

	/**
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
		return $contact->contact_id;
	}


	/**
	 * @var SimpleXMLElement $checklist
	 * @return array
	 **/
	private function checklist_item_titles(SimpleXMLElement $checklist): array
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

	public function clear_tickets()
	{
		$this->tickets = array();
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
}