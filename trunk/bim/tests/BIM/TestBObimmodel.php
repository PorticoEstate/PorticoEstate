<?php
	/*
 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 2 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU Lesser General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
	include_once './TestBimCommon.php';

	class TestBObimmodel extends TestBimCommon
	{
	
	/**
	 * @var boolean $backupGlobals disable backup of GLOBALS which breaks things
	 */
	protected $backupGlobals = false;

	/**
	 * @var integer $fieldID The attribute ID used for all the tests
	 */
	protected $fieldID;
	private $sovfs;
	private $sobimmodel;
	private $bobimmodel;
	
	/**
	 * Setup the environment for the tests
	 *
	 * @return void
	 */
	protected function setUp()
	{
		$this->initDatabase();	
	}

	/**
	 * Clean up the environment after running a test
	 *
	 * @return void
	 */
	protected function tearDown()
	{
		
	}

		public function testDb()
		{
		$this->assertNotNull($this->db);
	}
	/*
	 * @depends testDB
	 * @test
	 */

		public function testAddUploadedIfcModel()
		{
		$this->addIfcModel();
		$this->assertTrue(true);
	}
	
		private function addIfcModel()
		{
		$this->createDummyFile();
     	
     	$filename = $this->vfsFileName;
		$filenameWithPath = $this->vfsFileNameWithFullPath;
     	$this->bobimmodel = new bobimmodel_impl();
     	$this->sovfs = new sovfs_impl($filename, $filenameWithPath, $this->vfsSubModule);
     	$this->sovfs->debug = true;
     	$this->bobimmodel->setVfsObject($this->sovfs);
     	$this->sobimmodel = new sobimmodel_impl($this->db);
     	$this->bobimmodel->setSobimmodel($this->sobimmodel);
     	$this->bobimmodel->setModelName($this->modelName);
     	$error = "";
			try
			{
     		$this->bobimmodel->addUploadedIfcModel();
     		//var_dump($this->sovfs->getFileInformation());
			}
			catch(FileExistsException $e)
			{
     		$error =  $e;
			}
			catch(Exception $e)
			{
     		$error =  $e;
     	}
			if($error)
			{
				echo "error in testAddUploadedIfcModel:" . $error;
     	}
     	$this->removeDummyFile();
	}
	/*
	 * @depends testAddUploadedIfcModel
	 */

		public function testCheckUploadedIfcModel()
		{
		$bobimmodel = new bobimmodel_impl();
		//init dependancies
		$sovfs = new sovfs_impl($this->vfsFileName, null, $this->vfsSubModule);
     	$vls_db_id = $sovfs->retrieveVfsFileId();
     	$sobimmodel = new sobimmodel_impl($this->db);
     	$sobimmodel->setModelName($this->modelName);
     	$sobimmodel->setVfsdatabaseid($vls_db_id);
     	
     	$bobimmodel->setVfsObject($sovfs);
     	$bobimmodel->setSobimmodel($sobimmodel);
     	
		$this->assertTrue($bobimmodel->checkBimModelExists());
	}
	/*
	 * @depends testCheckUploadedIfcModel
	 */

		public function testCreateBimModelList()
		{
		$bobimmodel = new bobimmodel_impl();
		$sobimmodel = new sobimmodel_impl($this->db);
		$bobimmodel->setSobimmodel($sobimmodel);
		
		$output = $bobimmodel->createBimModelList();
		
		$modelFound = false;
		/* @var $bimModel BimModel */
			foreach($output as $bimModelArray)
			{
				if($bimModelArray['fileName'] == $this->vfsFileName && $bimModelArray['name'] == $this->modelName)
				{
				$modelFound = true;
				break;
			}
		}
		$this->assertTrue($modelFound);
	}
	/*
	 * @depends testCreateBimModelList
	 */

		public function testRemoveUploadedIfcModel()
		{
		$bobimmodel = new bobimmodel_impl();
		//init dependancies
		$sovfs = new sovfs_impl($this->vfsFileName, null, $this->vfsSubModule);
     	$vls_db_id = $sovfs->retrieveVfsFileId();
     	$sobimmodel = new sobimmodel_impl($this->db);
     	$sobimmodel->setModelName($this->modelName);
     	$sobimmodel->setVfsdatabaseid($vls_db_id);
     	
     	$bobimmodel->setVfsObject($sovfs);
     	$bobimmodel->setSobimmodel($sobimmodel);
     	
		$this->assertTrue($bobimmodel->removeIfcModel());
	}
	/*
	 * @covers removeIfcModelByModelId
	 */

		public function testRemoveIfcModelByModelId()
		{
		$this->addIfcModel();
		$bimModelArray = $this->sobimmodel->retrieveBimModelList();
		$modelFound = false;
		/* @var $bimModel BimModel */
			foreach($bimModelArray as $bimModel)
			{
				if($bimModel->getFileName() == $this->vfsFileName && $bimModel->getVfsFileId() == $this->vfsFileId)
				{
				$modelFound = true;
				break;
			}
		}
			if($modelFound)
			{
			$this->sobimmodel->setModelId($bimModel->getDatabaseId());
			$this->sovfs->setSubModule($this->vfsSubModule);
			$this->bobimmodel->removeIfcModelByModelId();
			$this->assertThat($this->bobimmodel->checkBimModelExistsByModelId());
		}
	}
	}