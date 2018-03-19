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