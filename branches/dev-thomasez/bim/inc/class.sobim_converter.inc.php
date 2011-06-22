<?php
/*
 * Requires the following to work:
 * Curl library
 * HTTP_Request (Pear)
 */

phpgw::import_class('bim.restrequest');
/*
 * 
 */
interface sobim_converter {
	public function getFacilityManagementXml();
	public function setBaseUrl($url);
}

class sobim_converter_impl implements sobim_converter {
	private $baseUrl = "http://localhost:8080/bm/rest/";
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
		$this->checkArgumentsForXmlDownload();
		$restCall = "uploadIfc";
		$url = $this->baseUrl.$restCall;
		$verb = "POST";
		$data = array (
			'file'=>'@'.$this->fileToSend
		);
		
		$rest = new RestRequest($url, $verb, $data);
		
		$rest->setAcceptType("application/xml");
		$rest->execute();
		//echo "SObim converter: response info\n";
		//print_r($rest->getResponseInfo());
		if( $rest->isError()) {
			$info = $rest->getResponseInfo();
			$http_code = $info["http_code"];
			if($http_code == 0) {
				throw new NoResponseException();
			}
			throw new Exception("Rest call error : ".var_export($rest->getResponseInfo()));
		}
		return $rest->getResponseBody();
	}
	public function checkArgumentsForXmlDownload() {
		if(empty($this->fileToSend)) {
			throw new InvalidArgumentException("File to send has not been specified");
		}
		if(!file_exists($this->fileToSend)) {
			throw new InvalidArgumentException("File to send not found in filesystem");
		}
		if(empty($this->baseUrl) || strlen($this->baseUrl) < 2) {
			throw new InvalidArgumentException("Base url not set!");
		}
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
	public function setBaseUrl($url) {
		if(substr($url, -1) != "/") {
			$url = $url . "/";
		}
		$this->baseUrl = $url;
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
