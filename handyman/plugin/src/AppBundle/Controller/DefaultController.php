<?php

	namespace AppBundle\Controller;

	use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use Symfony\Component\HttpFoundation\Request;

	class DefaultController extends Controller
	{
		/**
		 * @Route("/", name="homepage")
		 */
		public function index_action(Request $request)
		{
			header('HTTP/1.0 403 Forbidden');
			exit('You don\'t have permission to access /portico/handyman/app/ on this server.');
		}
	}
