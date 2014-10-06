<?php
phpgw::import_class('bim.bimobject');
class BimModel extends BimObject{
	private $databaseId;
	private $name;
	private $creationDate;
	private $fileSize;
	private $fileName;
	private $usedItemCount;
	private $vfsFileId;
	private $used;
	 
	function __construct($databaseId = null, $name = null, $creationDate = null, $fileSize = null,$fileName= null,$usedItemCount= null, $vfsFileId = null) {
		$this->databaseId = (int)$databaseId;
		$this->name = $name;
		$this->creationDate =  $creationDate;
		$this->fileSize  = $fileSize;
		$this->fileName = $fileName;
		$this->usedItemCount = $usedItemCount;
		if($usedItemCount && $usedItemCount > 0) {
			$this->used =  true;
		} else {
			$this->used =  false;
		}
		
	}
	function getDatabaseId() {
		return $this->databaseId;
	}
	function setDatabaseId($databaseId) {
		$this->databaseId = $databaseId;
	}
	function getName() {
		return $this->name;
	}
	function setName($item) {
		$this->name = $item;
	}
	function getCreationDate() {
		return $this->creationDate;
	}
	function setCreationDate($item) {
		$this->creationDate = $item;
	}
	function getFileSize() {
		return $this->fileSize;
	}
	function setFileSize($item) {
		$this->fileSize = $item;
	}
	function getFileName() {
		return $this->fileName;
	}
	function setFileName($item) {
		$this->fileName = $item;
	}
	function getUsedItemCount() {
		return $this->usedItemCount;
	}
	function setUsedItemCount($item) {
		$this->usedItemCount = $item;
	}
	function getVfsFileId() {
		return $this->vfsFileId;
	}
	function setVfsFileId($item) {
		$this->vfsFileId = $item;
	}
	
	function getUsed() {
		if($this->used) {
			return $this->used;
		} else {
			return (bool)($this->usedItemCount > 0);
		}
	}
}
