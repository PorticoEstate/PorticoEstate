<?php
phpgw::import_class('bim.bimobject');
class BimItem extends BimObject{
	private $databaseId;
	private $guid;
	private $type;
	private $xml;
	private $modelId;
	 
	function __construct($databaseId = null, $guid = null, $type = null, $xml = null, $modelId = null) {
		//$this->databaseId = (is_null($databaseId)) ? null : (int)$databaseId;
		$this->databaseId = (int)$databaseId;
		$this->guid =  $guid;
		$this->type = $type;
		$this->xml = $xml;
		$this->modelId = $modelId;
	}
	function getDatabaseId() {
		return $this->databaseId;
	}
	function setDatabaseId($databaseId) {
		$this->databaseId = $databaseId;
	}
	function getGuid() {
		return $this->guid;
	}
	function getType() {
		return $this->type;
	}
	function setType($type) {
		$this->type = $type;
	}
	function getXml() {
		return $this->xml;
	}
	function setXml($xml) {
		$this->xml = $xml;
	}
	function getModelId() {
		return $this->modelId;
	}
	function setModelId($id) {
		$this->modelId = $id;
	}
	 
}
