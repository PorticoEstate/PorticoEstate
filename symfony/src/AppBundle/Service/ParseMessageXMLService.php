<?php
/**
 * Created by PhpStorm.
 * User: eskil.saatvedt
 * Date: 05.04.2018
 * Time: 15:11
 */

namespace AppBundle\Service;

use SimpleXMLElement;
use Doctrine\ORM\EntityManager as EntityManager;

class ParseMessageXMLService
{
	const FILE_PREFIX = 'eOrdr';

	/* @var EntityManager $em */
	private $em;
	private $dir;
	private $ext;

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
			if (!file_exists($file)) {
				return;
			}

			/* @var SimpleXMLElement $xml */
			$xml = simplexml_load_file($file);
			$this->parse_xml($xml);
		}
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
			$this->collect_document_data($order);
		}

		if(!$this->contain_checklist($order)){
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
	 * @return bool
	 */
	private function collect_order_data(SimpleXMLElement $order)
	{

	}

	/**
	 * Store the metadata from the DocumentList in BKByggs db
	 *
	 * @param SimpleXMLElement $order
	 */
	private function collect_document_data(SimpleXMLElement $order)
	{
		$documents = $order->xpath('DocumentList/Document');
		/* @var SimpleXMLElement $document */
		foreach ($documents as $document) {
			$document_data = array();
			if ($this->document_contain_data($document)) {
				$document_data['HSDocumentID'] = (string)$document->HSDocumentID;
				$document_data['Name'] = (string)$document->Name;
				$document_data['File'] = (string)$document->FilePath;
				$document_data['FileExtension'] = (string)$document->FileExtension;
				$document_data['InstallationID'] = (string)$document->InstallationID;
				$document_data['HSOrderNumber'] = (string)$order->HSOrderNumber;
				$document_data['base64'] = (string)$document->DocumentData;
				$lengh = strlen($document_data['base64']);

				file_put_contents("/vagrant/GSGroup/ikes.jpg",base64_decode($document_data['base64']));

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

		if (!(bool)$documents){
			return false;
		}
		$result = false;
		/* @var SimpleXMLElement $document */
		foreach ($documents as $document) {
			if ($this->document_contain_data($document))
			{
				$result = true;
			}
		}
		return $result;
	}

	/**
	 * @param SimpleXMLElement $document
	 * @return bool
	 */
	private function document_contain_data(SimpleXMLElement $document){
		if (isset($document->HSDocumentID)
			&& isset($document->Name)
			&& isset($document->FilePath)
			&& isset($document->FileExtension)
			&& isset($document->InstallationID)) {
			return true;
		}
		return false;
	}

}