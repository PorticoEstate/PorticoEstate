<?php
/**
 * Created by PhpStorm.
 * User: eskil.saatvedt
 * Date: 08.03.2018
 * Time: 10:31
 */

namespace AppBundle\Controller;

use AppBundle\Entity\FmGab;
use AppBundle\Entity\FmLocation1;
use AppBundle\Entity\FmLocation2;
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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
	public function reAction()
	{
		$dir = $this->getParameter('handyman_file_dir');
		$ext = $this->getParameter('handyman_export_ext');
		$url = $this->getParameter('handyman_document_url');
		$hm_user = $this->getParameter('bkbygg_handyman_user');
		$admin_user = $this->getParameter('bkbygg_user_id_to_use_when_not_found');


		$em = $this->getDoctrine()->getManager();

		$xml_message_service = new ParseMessageXMLService($em, $dir, $ext, $url, $hm_user, $admin_user);
		$xml_message_service->parseDir();
		return new Response('<html><body>Hei</body></html>');
	}


	/**
	 * @Route("/j", name="message_bygg")
	 *
	 **/
	public function byggAction()
	{

		$tickets = $this->getDoctrine()->getManager()->getRepository('AppBundle:FmTtsTicket')->find('123346');

		dump($tickets);
		return new Response('<html><body>Hello!</body></html>');
	}

	/**
	 * @Route("/show/{id}", name="message_show")
	 */
	public function showAction($id)
	{
		$data = array('id' => null,
			'subject' => null,
			'loc1' => null,
			'loc2' => null,
			'location_name' => null,
			'building_name' => null,
			'building_number' => null,
			'gnr' => null,
			'bnr' => null,
			'street_name' => null,
			'street_number' => null,
			'documentation_required' => null);

		/* @var FmTtsTicket $ticket */
		$ticket = $this->getDoctrine()->getManager()->getRepository('AppBundle:FmTtsTicket')->find($id);
		if (!empty($ticket)) {
			/* @var FmLocation1 $location */
			$location = $this->getDoctrine()->getManager()->getRepository('AppBundle:FmLocation1')->findOneBy(array('loc1' => $ticket->getLoc1()));
			if (!empty($location)) {
				$data['location_name'] = $location->getLoc1Name();
			}

			/* @var FmLocation2 $building */
			$building = $this->getDoctrine()->getManager()->getRepository('AppBundle:FmLocation2')->findOneBy(array('loc1' => $ticket->getLoc1(), 'loc2' => $ticket->getLoc2()));
			if (!empty($building)) {
				$data['building_name'] = $building->getLoc2Name();

				$data['building_number'] = $building->getBygningsnummer();
				/* @var FmGab $gab */
				$gab = $this->getDoctrine()->getManager()->getRepository('AppBundle:FmGab')->find($building->getLocationCode());
				if (!empty($gab)) {
					$data['gnr'] = $gab->getFormattedGab()['gnr'];
					$data['bnr'] = $gab->getFormattedGab()['bnr'];
				}
				$data['street_number'] = $building->getStreetNumber();
				$data['street_name'] = $building->getStreet()->getDescr();
			}
			$data['id'] = $ticket->getId();
			$data['subject'] = $ticket->getSubject();
			$data['loc1'] = $ticket->getLoc1();
			$data['loc2'] = $ticket->getLoc2();
			$data['documentation_required'] = $ticket->getDocumentRequired();
		} else {
			throw new NotFoundHttpException("Ticket not found");
		}

		$response = new Response();
		$response->setContent(json_encode($data));
		$response->headers->set('Content-Type', 'application/json');
		return $response;
	}

}