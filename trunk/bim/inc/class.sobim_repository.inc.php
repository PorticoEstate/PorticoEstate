<?php
/*
 * Requires the following to work:
 * Curl library
 * HTTP_Request (Pear)
 */

phpgw::import_class('bim.restrequest');

interface sobimrest {
	public function getRepositoryCountJson();
}

class sobimrest_impl implements sobimrest{
	private $baseUrl = "http://localhost:8080/RestTests/rest/repositories";
	
	public function __construct() {
		if(!$this->iscurlinstalled()) {
			throw new Exception("Curl library is required for this to work!");
		}
	}
	private function iscurlinstalled() {
		return  (in_array  ('curl', get_loaded_extensions()));
	}
	public function getRepositoryCountJson() {
		
		$url = $this->baseUrl."/count";
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
}
