<?php

namespace AppBundle\Controller;

use AppBundle\Entity\FmBuildingExportView;
use AppBundle\Entity\GwPreference;
use AppBundle\Entity\HmManagerForBuildingView;
use AppBundle\XmlModels\HmInstallationListXMLModel;
use AppBundle\Entity\FmLocation1;
use AppBundle\Entity\FmLocation2;
use AppBundle\Entity\FmLocation1Category;
use AppBundle\Service\FmLocationService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Query;

//use AppBundle\Component\Serializer\CustomObjectNormalizer;

/**
 * Fmlocation1 controller.
 *
 * @Route("fmlocation1")
 */
class FmLocation1Controller extends Controller
{
	private function generateSerializer(): Serializer
	{
		$encoders = array(new XmlEncoder(), new JsonEncoder());
		$classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
		$normalizer = new ObjectNormalizer($classMetadataFactory);
		$normalizer->setCircularReferenceLimit(1);
		$normalizer->setCircularReferenceHandler(function ($object) {
			return $object->getId();
		});
		$serializer = new Serializer([$normalizer], $encoders);
		return $serializer;
	}

//	/**
//	 * @Route("/foobar", name="fmlocation1_foobar")
//	 */
//	public function foobarAction()
//	{
//		$fmLocation1s = $this->getDoctrine()->getManager()->getRepository('AppBundle:FmLocation1')->findAll();
//		$serializer = $this->generateSerializer();
//		$jsonContent = $serializer->serialize($fmLocation1s, 'json', ['groups' => ['rest']]);
//		$response = new Response();
//		$response->setContent($jsonContent);
//		$response->headers->set('Content-Type', 'application/json');
//		return $response;
//	}

	/**
	 * @Route("/xml", name="fmlocation1_xml")
	 */
	public function xmlExportAction()
	{
		$fm_buildings = $this->getDoctrine()->getManager()->getRepository('AppBundle:FmBuildingExportView')->findAll();
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

	/**
	 * @Route("/foo", name="fmlocation1_foo")
	 */
	public function fooAction()
	{
		$buildings = $this->getDoctrine()->getManager()->getRepository('AppBundle:FmBuildingExportView')->findAll();
		$managers = $this->getManagers();
		$this->addAgressoIDToManager($managers);
		/* @var FmBuildingExportView $building */
		foreach ($buildings as $building) {
			$this->findManager($building, $managers);
		}

		dump($buildings);

		return new Response('<html><body>Hei</body></html>');
	}

	private function findManager(FmBuildingExportView &$building, array $managers)
	{
		/* @var HmManagerForBuildingView $manager */
		foreach ($managers as $manager) {
			if (empty($manager->getLocationCode())) {
				continue;
			}
			if (empty($manager->getAgressoId())) {
				continue;
			}
			if (empty($manager->getAccount())) {
				continue;
			}
			if ($building->getLoc1() == $manager->getLocationCode()) {
				$building->setManagerAgressoId($manager->getAgressoId() ?? '');
				$building->setManagerUserId($manager->getContactId() ?? '');
				$name = Trim(($manager->getFirstName() ?? '') . ' ' . ($manager->getLastName() ?? ''));
				$building->setManagerName($name);
				$building->setManagerAccountId($manager->getAccount()->getAccountId());
			}
		}
	}

	private function addAgressoIDToManager(array &$managers)
	{
		$users_with_agresso_id = $this->getDoctrine()->getManager()->getRepository('AppBundle:GwPreference')->findUsersWithPropertyResourceNr();
		/* @var HmManagerForBuildingView $manager */
		foreach ($managers as $key=>&$manager) {
			if(empty($manager->getContactId())){
				unset($managers[$key]);
				continue;
			}

			/* @var GwPreference $pref_user */
			foreach ($users_with_agresso_id as $pref_user) {
				if ($pref_user->getPreferenceOwner() == $manager->getAccount()->getAccountId()) {
					$manager->setAgressoId($pref_user->getResourceNumber());
				}
			}
			if(empty($manager->getAgressoId())){
				unset($managers[$key]);
			}
		}
	}

	/**
	 * @return array
	 */
	private function getManagers(): array
	{
		return $this->getDoctrine()->getManager()->getRepository('AppBundle:HmManagerForBuildingView')->findAllIncludingAccount();
	}
}
