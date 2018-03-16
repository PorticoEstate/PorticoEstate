<?php
/**
 * Created by PhpStorm.
 * User: eskil.saatvedt
 * Date: 08.03.2018
 * Time: 10:31
 */

namespace AppBundle\Controller;

use Doctrine\Common\Annotations\AnnotationReader;
use SimpleXMLElement;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\FmTtsTicket;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use \DOMDocument;
use Symfony\Component\Routing\Loader\DirectoryLoader;
use AppBundle\Service\MessageService;

/**
 * Fmlocation1 controller.
 *
 * @Route("message")
 */
class MessageController extends Controller
{
	/**
	 * @Route("/import", name="message_import")
	 */
	public function importAction()
	{
		$dir = $this->getParameter('handyman_file_dir');
		$ext = $this->getParameter('handyman_export_ext');

		$em = $this->getDoctrine()->getManager();
		$response = new Response();
		$message_service = new MessageService($dir, $ext);
		$message_service->import_files();
		/* @var array<FmTtsTicket> $fm_tickets */
		$fm_tickets = $message_service->get_tickets();

		/* @var FmTtsTicket $fm_ticket */
		foreach($fm_tickets as $fm_ticket){
//			$em->persist($fm_ticket);
		}
//		$em->flush();
//		$em->clear();

		$response->setContent($message_service->get_message());
		$response->headers->set('Content-Type', 'text/plain');
		return $response;
		// Import file from Handyman
		// Validate file
		// Is the tasklist done?
		// Is there images with tasklist ID, store this
		// Pick out tasklist information
		// Create a Message based on tasklist infomration
		// Figure out any Id to ID mapping
		// Save the model

		// filename is eOrdrXXX.BYT where BYT is set on the customer in handyman config, XXX is a running number and increase if there is files here

//		$filePreFix = 'eOrdr';
//		$files = $this->findFiles($filePreFix);
//		$response = new Response();
//		if ($this->validateFiles($files)) {
//			$response->setContent(implode($files));
//
//			/* @var $file string */
//			foreach ($files as &$file) {
//				$this->importFile($file);
//			}
//
//		};
//		$response->headers->set('Content-Type', 'text/plain');
//		return $response;
	}

//	private function validateFiles(array $files): bool
//	{
//		return true; // To many XML vs Schemastructure errors forcing us to drop this...
//		$result = true;
//		/* @var $file string */
//		foreach ($files as &$file) {
//			$result = $result AND $this->validateFile($file);
//		}
//		return $result;
//	}
//
//	private function validateFile(string $file): bool
//	{
//		/* @var DOMDocument $xml */
//		$xml = new DOMDocument();
//		$xml->load($file);
////		6.1.Orders and registrations.xsd
//		$xsd = $this->getParameter('handyman_file_dir') . DIRECTORY_SEPARATOR . $this->getParameter('handyman_export_schema_file');
//		return $xml->schemaValidate($xsd);
//	}

//	private function findFiles(string $prefix): array
//	{
//		$file_dir = $this->getParameter('handyman_file_dir');
//		$ext = $this->getParameter('handyman_export_ext');
//		$pattern = $file_dir . DIRECTORY_SEPARATOR . $prefix . '*.' . $ext;
//		return glob($pattern);
//	}



//	private function validateChecklistType(SimpleXMLElement $checkList): bool
//	{
//		return true;
//	}

//	private function importFile(string $file)
//	{
//		if (!file_exists($file)) {
//			return nill;
//		}
//
//		/* @var SimpleXMLElement $xml */
//		$xml = simplexml_load_file($file);
//		if (!isset($xml->Order)) {
//			// The xml list did not read correctly or is empty
//			return;
//		}
//
//		$arr = array();
//
//		/* @var SimpleXMLElement $order */
//		foreach ($xml->Order as $order) {
//			if (!$this->validateOrder($order)) {
//				continue;
//			}
//
//			$arr[] = $this->retrieveDataFromOrder($order);
//		}
//
//		$count = count($arr);
//	}
//
//	/**
//	 * @param SimpleXMLElement $order
//	 * @return array
//	 */
//	private function retrieveDataFromOrder(SimpleXMLElement $order): array
//	{
//		$result = array();
//		$result['HSOrderNumber'] = $order->HSOrderNumber->__toString();
//		$result['OrderDate'] = $order->OrderHead->OrderDate->__toString();
//		$result['OrderName'] = $order->OrderHead->OrderName->__toString();
//		$result['Checklists'] = array();
//
//		/* @var SimpleXMLElement $checkListType */
//		foreach ($order->ChecklistList->ChecklistType as $checkListType) {
//			$foo = $checkListType->count();
//			if (!$this->validateChecklistType($checkListType)) {
//				continue;
//			}
//			$result['Checklists'][] = $this->retrieveDataFromChecklistType($checkListType);
//		}
//
//		return $result;
//	}
//
//	/**
//	 * @param SimpleXMLElement $checklistType
//	 * @return array
//	 */
//	private function retrieveDataFromChecklistType(SimpleXMLElement $checklistType): array
//	{
//		$data = Self::simpleXmlToArray($checklistType);
//		$filter = array('HSChecklistID', 'ChecklistTypeName', 'ChecklistName', 'Description', 'Document', 'Finished', 'EmployeeNo', 'InstallationID');
//		$result = array_filter(
//			$data,
//			function ($key) use ($filter) {
//				return in_array($key, $filter);
//			},
//			ARRAY_FILTER_USE_KEY
//		);
//
//
//		/* @var SimpleXMLElement $check */
//		foreach ($checklistType->Checklist->Check as $check) {
//
//		}
//
//		return $result;
//
//	}
//
//	static function simpleXmlToArray(SimpleXMLElement $xmlObject, bool $recursive = false): array
//	{
//		$array = [];
//		foreach ($xmlObject->children() as $node) {
//			if ($recursive) {
//				$array[$node->getName()] = is_array($node) ? Self::simpleXmlToArray($node) : (string)$node;
//			} else {
//				if (is_array($node)) {
//					continue;
//				} else {
//					$array[$node->getName()] = (string)$node;
//				}
//			}
//		}
//		return $array;
//	}



	/**
	 * @Route("/j", name="fmlocation1_bygg")
	 **/
	public function byggAction()
	{

		$tickets = $this->getDoctrine()->getManager()->getRepository('AppBundle:FmTtsTicket')->find('123346 ');

		dump($tickets);
		return new Response('<html><body>Hello!</body></html>');
	}

}