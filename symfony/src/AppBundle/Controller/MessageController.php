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
use AppBundle\Service\ParseMessageXMLService;


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
		$message_service = new MessageService($em, $dir, $ext);

		$files = $message_service->find_files();
		/* @var $file string */
		foreach ($files as &$file) {
			$message_service->clear_tickets();
			$message_service->import_file($file);
			/* @var array<FmTtsTicket> $fm_tickets */
			$fm_tickets = $message_service->get_tickets();


			$handyman_order_numbers = array();
			/* @var FmTtsTicket $fm_ticket */
			foreach ($fm_tickets as $fm_ticket) {
				if ($fm_ticket->__get('handyman_order_number')) {
					$handyman_order_numbers[] = $fm_ticket->getHandymanOrderNumber();
				}
			}

			$listOfTicketsToPreventDuplicates = $this->getDoctrine()->getManager()->getRepository('AppBundle:FmTtsTicket')->findTicketsWithHandymanOrderIDinArray($handyman_order_numbers);

			$arrOfIds = array();
			/* @var array $ticket */
			foreach ($listOfTicketsToPreventDuplicates as $ticket) {
				$arrOfIds[] = $ticket['handyman_order_number'];
			}


			/* @var FmTtsTicket $fm_ticket */
			foreach ($fm_tickets as $fm_ticket) {
				if (in_array($fm_ticket->getHandymanOrderNumber(), $arrOfIds)) {
					continue;
				}

				$em->persist($fm_ticket);
				$em->flush();
				$em->clear();
			}

		}
		$message_service->delete_files();

		return new Response('<html><body>'.$message_service->get_message().'</body></html>');


//		$response->setContent($message_service->get_message());
//		$response->headers->set('Content-Type', 'text/plain');
//		return $response;
	}

	/**
	 * @Route("/re", name="message_re")
	 **/
	public function reAction(){
		$dir = $this->getParameter('handyman_file_dir');
		$ext = $this->getParameter('handyman_export_ext');
		$em = $this->getDoctrine()->getManager();

		$xml_message_service= new ParseMessageXMLService($em, $dir, $ext);
		$xml_message_service->parseDir();
		return new Response('<html><body>Hei</body></html>');
	}


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