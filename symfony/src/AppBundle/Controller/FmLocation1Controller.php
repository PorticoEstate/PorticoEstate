<?php

namespace AppBundle\Controller;

use AppBundle\XmlModels\HmInstallationListXMLModel;
use AppBundle\Entity\FmLocation1;
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

//use AppBundle\Component\Serializer\CustomObjectNormalizer;

/**
 * Fmlocation1 controller.
 *
 * @Route("fmlocation1")
 */
class FmLocation1Controller extends Controller
{
    /**
     * @Route("/i", name="fmlocation1_locations")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $fmLocation1s = $em->getRepository('AppBundle:FmLocation1')->findAll();

        return $this->render('FmLocation1/index.html.twig', array(
            'fmLocation1s' => $fmLocation1s,
        ));
    }


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

    /**
     * @Route("/foobar", name="fmlocation1_foobar")
     */
    public function foobarAction()
    {
        $fmLocation1s = $this->getDoctrine()->getManager()->getRepository('AppBundle:FmLocation1')->findAll();
        $serializer = $this->generateSerializer();
        $jsonContent = $serializer->serialize($fmLocation1s, 'json', ['groups' => ['rest']]);
        $response = new Response();
        $response->setContent($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Finds and displays a fmLocation1 entity.
     *
     * @Route("/{loc1}/show", name="fmlocation1_show")
     * @Method("GET")
     */
    public function showAction($loc1)
    {
        $em = $this->getDoctrine()->getManager();
        $fmLocation1 = $em->getRepository('AppBundle:FmLocation1')->find($loc1);
        $deleteForm = $this->createDeleteForm($fmLocation1);

        return $this->render('fmlocation1/show.html.twig', array(
            'fmLocation1' => $fmLocation1,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @Route("/bar", name="fmlocation1_bar")
     */
    public function barAction()
    {
        $fmLocations = $this->getDoctrine()->getManager()->getRepository('AppBundle:FmLocation1')->findAll();
        $service = new FmLocationService($this->getDoctrine()->getManager());
        $service->addCustomFieldsForProperties($fmLocations);
        dump($fmLocations[400]->getCustomAttributes());
        dump($fmLocations[13]->getValue('aktiv'));

        //        $repository->clear();
        return new Response('<html><body>Hello!</body></html>');
    }


    /**
     * @Route("/xml", name="fmlocation1_xml")
     */
    public function xmlExportAction()
    {
        $fmLocations = $this->getDoctrine()->getManager()->getRepository('AppBundle:FmLocation1')->findAllFmLocation1();
        $installations = new HmInstallationListXMLModel($fmLocations);
        $encoders = array(new XmlEncoder('InstallationList'));
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $xml = $serializer->serialize($installations, 'xml');
        $response = new Response();
        $response->setContent($xml);
        $response->headers->set('Content-Type', 'application/xml');
        return $response;
    }

    /**
     * @Route("/import", name="fmlocation1_importxml")
     */
    public function importXML(){
        $file = dirname(__FILE__) . '/../../../../OrderAndRegistration.xml';
        if (file_exists($file)){
            $xml = simplexml_load_file($file);
        }

        $orderHead = $xml->Order[0]->OrderHead;
        dump($orderHead);
        dump($orderHead->InstallationID->__toString());
        dump($orderHead->InstallationList[0]->Installation);
        $documentItem = $xml->Order[0]->DocumentList[0]->Document;
        $checklistItem = $xml->Order[0]->ChecklistList[0]->ChecklistType;
        $data = array();
        $data['ChecklistTypeName']= $checklistItem->ChecklistTypeName->__toString();
        $data['ChecklistName']= $checklistItem->ChecklistName->__toString();
        $data['Description']= $checklistItem->Description->__toString();
        $data['Document']= $checklistItem->Document->__toString();
        $data['Comment']= $checklistItem->Comment->__toString();
        $data['Finished']= $checklistItem->Finished->__toString();
        dump($data);
        return new Response('<html><body>Hello!</body></html>');
    }
}
