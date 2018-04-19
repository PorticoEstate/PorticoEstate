<?php
/**
 * Created by PhpStorm.
 * User: eskil.saatvedt
 * Date: 08.03.2018
 * Time: 10:31
 */

namespace AppBundle\Controller;

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
	public function reAction(){
		$dir = $this->getParameter('handyman_file_dir');
		$ext = $this->getParameter('handyman_export_ext');
		$url = $this->getParameter('handyman_document_url');
		$hm_user = $this->getParameter('bkbygg_handyman_user');
		$admin_user = $this->getParameter('bkbygg_user_id_to_use_when_not_found');


		$em = $this->getDoctrine()->getManager();

		$xml_message_service= new ParseMessageXMLService($em, $dir, $ext, $url, $hm_user, $admin_user);
		$xml_message_service->parseDir();
		return new Response('<html><body>Hei</body></html>');
	}


	/**
	 * @Route("/j", name="message_bygg")
	 **/
	public function byggAction()
	{

		$tickets = $this->getDoctrine()->getManager()->getRepository('AppBundle:FmTtsTicket')->find('123346 ');

		dump($tickets);
		return new Response('<html><body>Hello!</body></html>');
	}

	/**
	 * @Route("/vt", name="message_vt")
	 */
	public function vtAction(){
		$dir = $this->getParameter('handyman_file_dir');
		$file = $dir."/".'VT.csv';
		$csv = $this->csv_to_array($file, ';');
//		dump($csv);
		$file2 = $dir."/".'people.csv';
		$people = $this->csv_to_array($file2, ';');
//		dump($people);
		foreach($csv as &$vt){
			foreach($people as $person){
				if($vt['vt'] == $person['vt']){
					$vt['id'] = $person['id'];
				}
			}
		}
		dump($csv);

		$outcsv = array();
		$outcsv[] = array_keys($csv[0]);
		foreach($csv as $p){
			$outcsv[] = array_values($p);
		}
		dump($outcsv);

		$file3 = $dir."/".'filtered.csv';
		$fp = fopen($file3, 'w');
		foreach($outcsv as $row){
			fputcsv($fp,$row,";");
		}
		fclose($fp);
		return new Response('<html><body>Hello!</body></html>');
	}
	/**
	 * @Route("/sl", name="message_sl")
	 */
	public function slAction()
	{$dir = $this->getParameter('handyman_file_dir');
		$file = $dir."/".'soneledere.csv';
		$csv = $this->csv_to_array($file, ';');
		dump($csv);

		$file2 = $dir."/".'people.csv';
		$people = $this->csv_to_array($file2, ';');
//		dump($people);
		foreach($csv as &$vt){
			foreach($people as $person){
				if($vt['soneleder'] == $person['vt']){
					$vt['id'] = $person['id'];
				}
			}
		}
		dump($csv);

		$outcsv = array();
		$outcsv[] = array_keys($csv[0]);
		foreach($csv as $p){
			$outcsv[] = array_values($p);
		}
		dump($outcsv);

		$file3 = $dir."/".'soneledere_id.csv';
		$fp = fopen($file3, 'w');
		foreach($outcsv as $row){
			fputcsv($fp,$row,";");
		}
		fclose($fp);
		return new Response('<html><body>Hello!</body></html>');
	}

	private function csv_to_array($filename = '', $delimiter = ',', $asHash = true) {
		if (!(is_readable($filename) || (($status = get_headers($filename)) && strpos($status[0], '200')))) {
			return FALSE;
		}

		$header = NULL;
		$data = array();
		if (($handle = fopen($filename, 'r')) !== FALSE) {
			if ($asHash) {
				while ($row = fgetcsv($handle, 0, $delimiter)) {
					if (!$header) {
						$header = $row;
					} else {
						$data[] = array_combine($header, $row);
					}
				}
			} else {
				while ($row = fgetcsv($handle, 0, $delimiter)) {
					$data[] = $row;
				}
			}

			fclose($handle);
		}

		return $data;
	}
}