<?php
class TestUIbim extends TestBimCommon
{
	protected $backupGlobals = false;
	
	public function __construct() {
		
	}
	protected function setUp()
	{
		$currentDirectory = dirname(__FILE__);
		$this->vfsFileNameWithFullPath = $currentDirectory.DIRECTORY_SEPARATOR.$this->vfsFileName;
		
	}
	
	public function testUploadFile() {
		$uibim = new bim_uibim();
		$this->modelName;
		$uploadedFileArray = array();
		$uploadedFileArray['name'] = $this->vfsFileName;
		$uploadedFileArray['tmp_name'] = $this->vfsFileNameWithFullPath;
		$result = $uibim->uploadFile($uploadedFileArray, $this->modelName, true);
		print_r($result);
	}
	
}
