<?php

namespace AppBundle\Controller;

use AppBundle\Entity\FmLocation1;
use AppBundle\Entity\FmLocation1Category;
use AppBundle\Service\FmLocation1Service;
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
     * Creates a new fmLocation1 entity.
     *
     * @Route("/new", name="fmlocation1_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $fmLocation1 = new Fmlocation1();
        $form = $this->createForm('AppBundle\Form\FmLocation1Type', $fmLocation1);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($fmLocation1);
            $em->flush();

            return $this->redirectToRoute('fmlocation1_show', array('loc1' => $fmLocation1->getLoc1()));
        }

        return $this->render('fmlocation1/new.html.twig', array(
            'fmLocation1' => $fmLocation1,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/number/{max}")
     */
    public function numberAction($max)
    {
        $number = mt_rand(0, $max);

        return new Response(
            '<html><body>Lucky number: ' . $number . '</body></html>'
        );
    }

//    /**
//     * @Route("/display/{loc1}")
//     */
//    public function displayAction($loc1){
//
//        $em = $this->getDoctrine()->getManager();
//        $fmLocation1 = $em->getRepository('AppBundle:FmLocation1')->find($loc1);
//        return $this->showAction($fmLocation1);
//    }

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
     * Displays a form to edit an existing fmLocation1 entity.
     *
     * @Route("/{loc1}/edit", name="fmlocation1_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, FmLocation1 $fmLocation1)
    {
        $deleteForm = $this->createDeleteForm($fmLocation1);
        $editForm = $this->createForm('AppBundle\Form\FmLocation1Type', $fmLocation1);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('fmlocation1_edit', array('loc1' => $fmLocation1->getLoc1()));
        }

        return $this->render('fmlocation1/edit.html.twig', array(
            'fmLocation1' => $fmLocation1,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a fmLocation1 entity.
     *
     * @Route("/{loc1/delete}", name="fmlocation1_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, FmLocation1 $fmLocation1)
    {
        $form = $this->createDeleteForm($fmLocation1);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($fmLocation1);
            $em->flush();
        }

        return $this->redirectToRoute('fmlocation1_index');
    }

    /**
     * Creates a form to delete a fmLocation1 entity.
     *
     * @param FmLocation1 $fmLocation1 The fmLocation1 entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(FmLocation1 $fmLocation1)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('fmlocation1_delete', array('loc1' => $fmLocation1->getLoc1())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @Route("/bar", name="fmlocation1_bar")
     */
    public function barAction(){
        $fmLocations = $this->getDoctrine()->getManager()->getRepository('AppBundle:FmLocation1')->findAll();
        $service = new FmLocation1Service($this->getDoctrine()->getManager());
        $service->addCustomFieldsForProperties($fmLocations);
        dump($fmLocations[400]->getCustomAttributes());
dump($fmLocations[10]->getValue('aktiv'));
        dump($fmLocations[11]->getValue('aktiv'));
        dump($fmLocations[12]->getValue('aktiv'));
        dump($fmLocations[13]->getValue('aktiv'));

        //        $repository->clear();
        return new Response('<html><body>Hello!</body></html>');
    }

}
