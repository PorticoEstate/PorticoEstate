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
		$messages = array();
		$message_data = new MessageData();
		$message_data->soneleder_fra_handyman = (int)$order->OrderHead->Manager;
		$message_data->order_id =  (int)$order->HSOrderNumber;
		$message_data->order_name = (string)$order->OrderHead->OrderName;

		/* @var SimpleXMLElement $checlist*/
		foreach($order->ChecklistList->ChecklistType as $checklist){
			$messages = $this->get_data_from_checklis($checklist, $message_data);
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
		// is the order finished
		// is the checklist finished
		// is there any deviations on the checklist?
		if (!(isset($order->OrderHead->FinishDate) && $order->OrderHead->FinishDate)) {
			return false;
		}

		/* @var SimpleXMLElement $checklist_type */
		foreach ($order->ChecklistList->ChecklistType as $checklist_type) {
			if (!(isset($checklist_type->Finished) && $checklist_type->Finished)) {
				return false;
			}
			if (!isset($checklist_type->Checklist)) {
				return false;
			}
			$this->is_checklist_a_report($checklist_type->Checklist);

		}
		return true;
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
	 * @param MessageData $message_data
	 * @return array<MessageData>
	 */
	private function get_data_from_checklis(SimpleXMLElement $checklist, MessageData $message_data): array
	{
		return array();
	}
}

class MessageData{
	public $user_id = null;
	public $assignedto = null;
	public $subject = null;
	public $location_code = null;
	public $loc1 = null;
	public $loc2 = null;
	public $contact_id = null;
	public $checklist_id = null;
	public $order_id = null;
	public $soneleder_fra_handyman = null;
	public $emloyee_from_handyman = null;
	public $order_name = null;
}