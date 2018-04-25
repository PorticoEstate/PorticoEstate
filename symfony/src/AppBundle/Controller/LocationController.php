<?php

namespace AppBundle\Controller;

use AppBundle\Entity\FmBuildingExportView;
use AppBundle\XmlModels\HmInstallationListXMLModel;
use AppBundle\Service\FmLocationService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;

/**
 * Location1 controller.
 *
 * @Route("location")
 */
class LocationController extends Controller
{
	/**
	 * @Route("/xml", name="Location_xml")
	 */
	public function xml_export_action()
	{
		/* @var FmLocationService $location_service */
		$location_service = new FmLocationService($this->getDoctrine()->getManager());
		$fm_buildings = $location_service->getBuildings();
		$this->filter_buildings($fm_buildings);
		$buildings = HmInstallationListXMLModel::construct_from_building_export($fm_buildings);
		$encoders = array(new XmlEncoder('InstallationList'));
		$normalizers = array(new ObjectNormalizer());
		$serializer = new Serializer($normalizers, $encoders);
		$xml = $serializer->serialize($buildings, 'xml');
		$response = new Response();
		$response->setContent($xml);
		$response->headers->set('Content-Type', 'application/xml');
		return $response;
	}

	private function filter_buildings(array &$fm_buildings)
	{
		$blacklist = array('0000', '2004', '2005', '2006', '2007', '2008', '2009', '2010', '2011');
		/* @var FmBuildingExportView $building */
		foreach($fm_buildings as $key=>$building){
			if(in_array($building->getLoc1(),$blacklist)){
				unset($fm_buildings[$key]);
			}
		}
	}
}
