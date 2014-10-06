<?php

class TestBimCommon extends PHPUnit_Framework_TestCase
{
	
	protected $db;
	// variables for bim model
	protected $modelName = "unitTestModel";
	protected $vfsFileName = "dummyFile.txt";
	protected $vfsFileNameWithFullPath;
	protected $vfsFileContents = "This is a file made for unit testing, please ignore or delete";
	protected $vfsSubModule = "ifc";
	protected $vfsFileId = 10101010;
	
	public function __construct() {
		//$GLOBALS['phpgw_info']['user']['account_id'] = 7;
		$currentDirectory = dirname(__FILE__);
		$this->vfsFileNameWithFullPath = $currentDirectory.DIRECTORY_SEPARATOR.$this->vfsFileName;
		echo "Var set to:".$this->vfsFileNameWithFullPath;
	}
	
	protected function initDatabase() {
		$this->db = & $GLOBALS['phpgw']->db;
	}
	
	protected function createDummyFile() {
		
		
		$fileHandle = fopen($this->vfsFileNameWithFullPath, 'w') or die("Can't open file");
		$result = fwrite($fileHandle, $this->vfsFileContents);
		fclose($fileHandle);
	}
	protected function removeDummyFile() {
		unlink($this->vfsFileNameWithFullPath);
	}
}