<?php
	/**
	 * This class handle request for messages in BK Bygg
	 * Import xml files to generate messages from handyman
	 * Show message data for schemabuilder
	 * Save status for schemabuilder
	 */

	namespace AppBundle\Controller;

	use AppBundle\Entity\FmGab;
	use AppBundle\Entity\FmHandymanLog;
	use AppBundle\Entity\FmLocation1;
	use AppBundle\Entity\FmLocation2;
	use Doctrine\Common\Annotations\AnnotationReader;
	use Doctrine\ORM\ORMException;
	use SimpleXMLElement;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use AppBundle\Entity\FmTtsTicket;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpKernel\HttpKernel;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\Routing\Annotation\Route;
	use \DOMDocument;
	use Symfony\Component\Routing\Loader\DirectoryLoader;
	use AppBundle\Service\ParseMessageXMLService;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
	use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

	/**
	 * Fmlocation1 controller.
	 *
	 * @Route("/message")
	 */
	class MessageController extends Controller
	{
		//region GET
		/**
		 * @Route("/import", name="message_re")
		 **/
		public function import_action()
		{
			$dir = $this->getParameter('handyman_file_dir');
			$ext = $this->getParameter('handyman_export_ext');
			$url = $this->getParameter('handyman_document_url');
			$hm_user = $this->getParameter('bkbygg_handyman_user');
			$admin_user = $this->getParameter('bkbygg_user_id_to_use_when_not_found');
			$em = $this->getDoctrine()->getManager();

			try {
				$xml_message_service = new ParseMessageXMLService($em, $dir, $ext, $url, $hm_user, $admin_user);
			} catch (ORMException $e) {
	//			dump($e);
	//			return new Response('<html><body>Hello!</body></html>');
				$response = new Response();
				$response->setContent('<data><success>false</success><error>Failed to parse Handyman XML files</error></data>');
				$response->headers->set('Content-Type', 'application/xml');
				return $response;
			}

			$xml_message_service->parse_dir();
			$log = new FmHandymanLog();
			$log->setComment('XML file parsed');
			$log->setSuccess(true);
			$log->setNumOfMessages($xml_message_service->get_number_of_tickets());
			$em->persist($log);
			$em->flush();

	//		dump($xml_message_service);
	//		return new Response('<html><body>Hello!</body></html>');

			$response = new Response();
			$response->setContent('<data><success>true</success><numberOfMessagesSaved>' . (string)$xml_message_service->get_number_of_tickets() . '</numberOfMessagesSaved></data>');
			$response->headers->set('Content-Type', 'application/xml');
			return $response;
		}

		/**
		 * Return the data needed for schemabuilder
		 *
		 * @Route("/show/{id}", name="message_show")
		 */
		public function show_action($id)
		{
			$em = $this->getDoctrine()->getManager();

			/* @var FmTtsTicket $ticket */
			$ticket = $em->getRepository('AppBundle:FmTtsTicket')->find($id);
			if (!$ticket) {
				throw new NotFoundHttpException('Ticket not found');
			}
			$data = $this->collect_ticket_data($ticket);
			$response = new JsonResponse();
			$response->setContent(json_encode(array('data' => $data)));
			return $response;
		}
		//endregion

		//region POST
		/**
		 * Changing the status from shcema builder to tell if a message need documentation
		 * @Method({"POST"})
		 * @Route("/update", name="message_update")
		 */
		public function update_action(Request $request)
		{
			$id = $request->request->get('id');
			$dr = $request->request->get('documentation_required');

			if (empty($id) || empty($dr)) {
				throw $this->createNotFoundException('Invalid ticket parameters.');
			}

			$em = $this->getDoctrine()->getManager();
			/* @var FmTtsTicket $ticket */
			$ticket = $em->getRepository('AppBundle:FmTtsTicket')->find($id);
			if (!$ticket) {
				throw $this->createNotFoundException('Unable to find Ticket entity.');
			}

			$ticket->setDocumentRequired($dr);
			$em->persist($ticket);
			$em->flush();

			$data = $this->collect_ticket_data($ticket);
			$response = new JsonResponse();
			$response->setContent(json_encode(array('data' => $data, 'success' => true)));
			return $response;
		}
		//endregion

		//region Private functions
		/**
		 * @param FmTtsTicket $ticket
		 * @return array
		 */
		private function collect_ticket_data(FmTtsTicket $ticket): array
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

			$em = $this->getDoctrine()->getManager();
			/* @var FmLocation1 $location */
			$location = $em->getRepository('AppBundle:FmLocation1')->findOneBy(array('loc1' => $ticket->getLoc1()));
			if (!empty($location)) {
				$data['location_name'] = $location->getLoc1Name();
			}

			/* @var FmLocation2 $building */
			$building = $em->getRepository('AppBundle:FmLocation2')->findOneBy(array('loc1' => $ticket->getLoc1(), 'loc2' => $ticket->getLoc2()));
			if (!empty($building)) {
				$data['building_name'] = $building->getLoc2Name();

				$data['building_number'] = $building->getBygningsnummer();
				/* @var FmGab $gab */
				$gab = $em->getRepository('AppBundle:FmGab')->find($building->getLocationCode());
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
			return $data;
		}
		//endregion
	}