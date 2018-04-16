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