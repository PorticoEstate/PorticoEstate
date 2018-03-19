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

use Doctrine\ORM\EntityManager as EntityManager;
use AppBundle\Entity\FmTtsTicket;
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

	/**
	 * MessageService constructor.
	 * @param $dir
	 * @param $ext
	 */
	public function __construct($dir, $ext)
	{
		$this->dir = $dir;
		$this->ext = $ext;
	}

	public function import_files()
	{
		// Read the files
		$this->find_files();
		/* @var $file string */
		foreach ($this->files as &$file) {
			$this->import_file($file);
		}
		$this->delete_files();
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

	private function delete_files()
	{
		$this->message .= '\nFiles not deleted';
	}

	private function find_files()
	{
		$pattern = $this->dir . DIRECTORY_SEPARATOR . Self::FILE_PREFIX . '*.' . $this->ext;
		$this->files = glob($pattern);
		if (count($this->files) > 0) {
			$this->message .= '\n' . count($this->files) . ' files red';
		} else {
			$this->message .= 'No files found';
		}
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

	private function import_file(string $file)
	{
		if (!file_exists($file)) {
			$this->message .= $file . ' not found';
			return;
		}

		/* @var SimpleXMLElement $xml */
		$xml = simplexml_load_file($file);
		if (!isset($xml->Order)) {
			// The xml list did not read correctly or is empty
			$this->message .= 'Error in the XML file: ' . $file;
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

	/*
	 * @var SimpleXMLElement $order
	 */
	private function parse_order_and_create_ticket(SimpleXMLElement $order)
	{
		/* @var SimpleXMLElement $checlist */
		foreach ($order->ChecklistList->ChecklistType as $checklist) {
			$this->get_data_from_checklist($checklist, (int)$order->OrderHead->Manager, (int)$order->HSOrderNumber, (string)$order->OrderHead->OrderName);
		}

		/* @var MessageData $message */
		foreach($this->messages as $message){
			$title = implode($message->checklist_name);
			$description = implode($message->checklist_description);
		}


		$fm_ticket = new FmTtsTicket();
		$fm_ticket->set_default_values();

//		$user_id = null;
//		$assignedto = null;
//		$subject = null;
//		$location_code = null;
//		$loc1 = null;
//		$loc2 = null;
//		$contact_id = null;
//		$checklist_id = null;
//		$order_id = null;
//		$soneleder_fra_handyman = null;
//		$emloyee_from_handyman = null;
//		$order_name = null;


		$this->tickets[] = $fm_ticket;
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
			$oneChecklistHasData = $oneChecklistHasData OR $this->do_checklist_item_have_report_data($checklist_type);
		}
		return $oneChecklistHasData;
	}

	/* @var SimpleXMLElement $checklist_type
	 * @return bool
	 */
	private function do_checklist_item_have_report_data(SimpleXMLElement $checklist_type)
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
		$message->checklist_description[] = $checklist->ChecklistTypeName;
		$message->checklist_description[] = $checklist->ChecklistName;

		/* @var array $name_and_descriptions */
		$name_and_descriptions = $this->checklist_item_titles();
		if (count($name_and_descriptions) > 0) {
			foreach ($name_and_descriptions as $item) {
				$current_message = $message->clone();
				$current_message->checklist_name[] = $item['title'];
				$current_message->checklist_description[] = $item['description'];
				$current_message->checklist_description[] = $item['comment'];
				$current_message->checklist_description[] = $checklist->ChecklistTypeName;
				$current_message->checklist_description[] = $checklist->ChecklistName;
				$this->messages[] = $current_message;
			}
		}
	}

	/**
	 * @var SimpleXMLElement $checklist
	 * @return array
	 **/
	private function checklist_item_titles(SimpleXMLElement $checklist): array
	{
		$result = array();
		$comment = '';
		if ((string)$checklist->Check[count($checklist->Check) - 1]->Text == 'Kommentar') {
			$comment = (string)$checklist->Check[count($checklist->Check) - 1]->Text;
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

}

class MessageData
{
	public $user_id = null;
	public $assignedto = null;
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


	public $checklist_name = array();
	public $checklist_description = array();

	/* @return MessageData */
	public function clone(): MessageData
	{
		return clone $this;
	}
}