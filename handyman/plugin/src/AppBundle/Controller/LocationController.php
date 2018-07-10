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
		$reply = '';
		/* @var FmLocationService $location_service */
		$location_service = new FmLocationService($this->getDoctrine()->getManager());
		$fm_buildings = $location_service->get_buildings();
		$this->filter_buildings($fm_buildings);

		/* @var array user_agressoid_in_groupid */
		$user_agressoid_in_groupid = $this->getParameter('user_agressoid_in_groupid');

		$buildings = HmInstallationListXMLModel::construct_from_building_export($fm_buildings, $user_agressoid_in_groupid);
		$encoders = array(new XmlEncoder('InstallationList'));
		$normalizers = array(new ObjectNormalizer());
		$serializer = new Serializer($normalizers, $encoders);
		$xml = $serializer->serialize($buildings, 'xml');
		if (!$this->save_xml($xml)) {
			$reply = '<data><error>Unable to write xml file</error></data>';
		} else {
			$reply = '<data><success>File sucessfully written to disk ' . $this->get_xml_file_path() . '</success></data>';
		}
		$response = new Response();
		$response->setContent($reply);
		$response->headers->set('Content-Type', 'application/xml');
		return $response;
	}

	private function save_xml(string $xml): bool
	{
		$file = fopen($this->get_xml_file_path(), "w");
		if (!$file) {
			return false;
		}
		fwrite($file, $xml);
		fclose($file);
		return true;
	}

	private function get_xml_file_path(): string
	{
		$dir = $this->getParameter('handyman_file_dir');
		$file_name_prefix = 'iInst';
		$ext = $this->getParameter('handyman_export_ext');
		return $dir . DIRECTORY_SEPARATOR . $file_name_prefix . '001.' . $ext;
	}

	/**
	 * Filter on blacklist of buildings we don't want
	 * @param array $fm_buildings
	 */
	private function filter_buildings(array &$fm_buildings)
	{
		$blacklist = array('0000', '2004', '2005', '2006', '2007', '2008', '2009', '2010', '2011');
		/* @var FmBuildingExportView $building */
		foreach ($fm_buildings as $key => $building) {
			if (in_array($building->getLoc1(), $blacklist)) {
				unset($fm_buildings[$key]);
			}
		}
	}
}
