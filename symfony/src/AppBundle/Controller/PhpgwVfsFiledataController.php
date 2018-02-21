<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\PhpgwVfsFiledata;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Doctrine\Common\Annotations\AnnotationReader;

/**
 * PhpgwVfsFiledata controller.
 *
 * @Route("phpgwfiledata")
 */
class PhpgwVfsFiledataController extends Controller
{


//    private function generateSerializer(): Serializer
//    {
//        $encoders = array(new XmlEncoder(), new JsonEncoder());
//        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
//        $normalizer = new ObjectNormalizer($classMetadataFactory);
//        $normalizer->setCircularReferenceLimit(1);
//        $normalizer->setCircularReferenceHandler(function ($object) {
//            return $object->getId();
//        });
//        $serializer = new Serializer([$normalizer], $encoders);
//        return $serializer;
//    }

    /**
     * @Route("/", name="phpgwfiledata_index")
     */
    public function indexAction(Request $request)
    {
        $arrOfData = $this->getDoctrine()
            ->getRepository(PhpgwVfsFiledata::class)
            ->findAll();


        dump(array_slice($arrOfData, 1245, 10), $request);
        return new Response('Hello, world');
//        $fmLocation1s = $this->getDoctrine()->getManager()->getRepository('AppBundle:FmLocation1')->findAll();
//        $serializer = $this->generateSerializer();
//        $jsonContent = $serializer->serialize($arrOfData, 'json', ['groups' => ['rest']]);
//        $response = new Response();
//       $response->setContent('{"foo":"bar"}');
//        $response->headers->set('Content-Type', 'application/json');
//        return $response;
    }
}