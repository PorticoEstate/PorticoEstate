<?php
/**
 * Created by PhpStorm.
 * User: eskil.saatvedt
 * Date: 23.02.2018
 * Time: 10:59
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Application;
use AppBundle\Entity\CustAttribute;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Repository\ApplicationRepository;

/**
 * Application controller.
 *
 * @Route("app")
 */
class ApplicationController extends Controller
{
    /**
     * @Route("/list", name="app_application")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $applications = $em->getRepository('AppBundle:Application')->findOneBy(['name' => 'property']);


        dump($applications->getLocations());
        dump($applications);
        return new Response('<html><body>Hello!</body></html>');
    }

    /**
     * @Route("/gw", name="app_gw")
     */
    public function gwAction()
    {
        $em = $this->getDoctrine()->getManager();
        $applications = $em->getRepository('AppBundle:GwLocation')->findOneBy(['appId' => '14', 'name' => '.location']);


        dump($applications);
        return new Response('<html><body>Hello!</body></html>');
    }

    /**
     * @Route("/rep", name="app_rep")
     */
    public function repAction()
    {
        $repository = $this->getDoctrine()
            ->getRepository(Application::class);
        $app = $repository->findAppForBuildings();
        $gwLocationId = $app->getLocations()->first()->getId();
        $custAttributeRepository = $this->getDoctrine()
            ->getRepository(CustAttribute::class);
        $custAttributes = $custAttributeRepository->findProperties($gwLocationId);
        dump($custAttributes);


        //        $repository->clear();
        return new Response('<html><body>Hello!</body></html>');
    }


}