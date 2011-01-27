<?php
/*
 * Requires the following to work:
 * Curl library
 * HTTP_Request (Pear)
 */

phpgw::import_class('property.restrequest');
/*
 * 
 */
interface sobim_converter {
	public function getFacilityManagementXml();
}

class sobim_converter_impl implements sobim_converter {
	private $baseUrl = "http://localhost:8080/BIM_Facility_Management/rest/";
	private $fileToSend;
	
	public function __construct() {
		if(!$this->iscurlinstalled()) {
			throw new Exception("Curl library is required for this to work!");
		}
	}
	private function iscurlinstalled() {
		return  (in_array  ('curl', get_loaded_extensions()));
	}
	
	public function getFacilityManagementXml() {
		$restCall = "uploadIfc";
		$url = $this->baseUrl.$restCall;
		$verb = "POST";
		$data = array (
			'file'=>'@'.$this->fileToSend
		);
		
		$rest = new RestRequest($url, $verb, $data);
		$rest->setAcceptType("application/xml");
		$rest->execute();
		if( $rest->isError()) {
			throw new Exception("Rest call error : ".var_export($rest->getResponseInfo()));
		}
		return $rest->getResponseBody();
	}
	public function getRepositoryCountJson() {
		
		$url = $this->baseUrl."uploadIfc";
		$method = "GET";
		$rest = new RestRequest($url, $method);
		//$rest->setAcceptType("application/xml");
		$rest->execute();
		$output = $rest->getResponseBody();
		echo $output;
	}
	
	public function getRepositoryNames() {
		$url = $this->baseUrl."/names";
		$method = "GET";
		$rest = new RestRequest($url, $method);
		//$rest->setAcceptType("application/xml");
		$rest->execute();
		$output = $rest->getResponseBody();
		echo $output;
	}
	public function setFileToSend($name) {
		$this->fileToSend = $name;
	}
}