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

	class TestSObimmodel extends PHPUnit_Framework_TestCase
	{

	private $modelName = "unitTestModel";
	private $vfsFileName = "dummyFile.txt";
	private $vfsFileContents = "This is a file made for unit testing, please ignore or delete";
	private $vfsSubModule = "ifc";
	private $vfsFileId = 10101010;
	//
	private $bimTypeTableName = 'fm_bim_type';
	private $bimItemTableName = 'fm_bim_item';
	private $projectGuid;
		private $projectType = 'ifcprojecttest';
	private $newProjectName = 'New_project name';
	private $projectXml;
	private $buildingStorey1Guid;
	private $buildingStorey2Guid;
	private $buildingStorey1Type;
	private $buildingStorey2Type;
	private $buildingStorey1xml;
	private $buildingStorey2xml;
	private $db;
	
	/**
	 * @var boolean $backupGlobals disable backup of GLOBALS which breaks things
	 */
	protected $backupGlobals = false;

	/**
	 * @var integer $fieldID The attribute ID used for all the tests
	 */
	protected $fieldID;
		protected static $addedNoteId = 0;
	protected $noteContent = "My dummy note content";
	protected $editedNoteContent = "My edited dummy note content";

	/**
	 * Setup the environment for the tests
	 *
	 * @return void
	 */
	protected function setUp()
	{
		$GLOBALS['phpgw_info']['user']['account_id'] = 7;
		//$GLOBALS['phpgw']->acl->set_account_id(7); // not sure why this is needed...
		$this->db = & $GLOBALS['phpgw']->db;
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
	
		public function testSetGetModelName()
		{
		$sobimmodel = new sobimmodel_impl($this->db);
		$sobimmodel->setModelName($this->modelName);
		$this->assertEquals($this->modelName, $sobimmodel->getModelName());
	}
	
		public function testSetGetVfsId()
		{
		$sobimmodel = new sobimmodel_impl($this->db);
		$sobimmodel->setVfsdatabaseid($this->vfsFileId);
		$this->assertEquals($this->vfsFileId, $sobimmodel->getVfsdatabaseid());
	}
	/*
	 * Not the best unit test since it does so many things, but that's what you get with databases...
	 * @depends testDb
	 */

		public function testAddRemoveCheckBimModel()
		{
		$this->createDummyVfsFile();
		
		
		$sobimmodel = new sobimmodel_impl($this->db);
		$this->addBimModel($sobimmodel);
		$this->assertTrue($sobimmodel->checkIfModelExists());
		$sobimmodel->removeBimModelFromDatabase();
		$this->assertTrue(!$sobimmodel->checkIfModelExists());
		
		$this->removeDummyVfsFile();
	}
	
		public function testRetrieveBimModelList()
		{
		$this->createDummyVfsFile();
		$sobimmodel = new sobimmodel_impl($this->db);
		$this->addBimModel($sobimmodel);
		$bimModelArray = $sobimmodel->retrieveBimModelList();
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
		$this->assertTrue($modelFound);
		
		
		
		$sobimmodel->removeBimModelFromDatabase();
		$this->removeDummyVfsFile();
	}
	/*
	 * WARNING, if the assertEquals statement fails, then the test suite will fail without warning!!!
	 * 
	 */

		public function testRetrieveBimModelInformationById()
		{
		$this->createDummyVfsFile();
		$sobimmodel = new sobimmodel_impl($this->db);
		$this->addBimModel($sobimmodel);
		$bimModelArray = $sobimmodel->retrieveBimModelList();
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
			$modelId = $bimModel->getDatabaseId();
			$sobimmodel->setModelId($modelId);
			/* @var $retrievedBimModel BimModel */
			$retrievedBimModel = $sobimmodel->retrieveBimModelInformationById();
			$retrievedBimModel->setFileSize(null);
			$retrievedBimModel->setUsedItemCount(null);
			$retrievedBimModel->setCreationDate(null);
			$expectedBimModel = new BimModel();
			$expectedBimModel->setDatabaseId($modelId);
			$expectedBimModel->setName($this->modelName);
			$expectedBimModel->setVfsFileId($this->vfsFileId);
			$expectedBimModel->setFileName($this->vfsFileName);
			
			$this->assertEquals($expectedBimModel, $retrievedBimModel);
			}
			else
			{
			$sobimmodel->removeBimModelFromDatabase();
			$this->removeDummyVfsFile();
			
			$this->fail("Model not found");
		}
		
		
		$sobimmodel->removeBimModelFromDatabase();
		$this->removeDummyVfsFile();
	}
	
		public function testRemoveBimModelByIdFromDatabase()
		{
		$this->createDummyVfsFile();
		
		$sobimmodel = new sobimmodel_impl($this->db);
		$this->addBimModel($sobimmodel);
		$bimModelArray = $sobimmodel->retrieveBimModelList();
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
			
			$modelId = $bimModel->getDatabaseId();
			$sobimmodel->setModelId($modelId);
			/* @var $retrievedBimModel BimModel */
			$retrievedBimModel = $sobimmodel->retrieveBimModelInformationById();
			
			$retrievedBimModel->setFileSize(null);
			$retrievedBimModel->setUsedItemCount(null);
			$retrievedBimModel->setCreationDate(null);
			
			$expectedBimModel = new BimModel();
			$expectedBimModel->setDatabaseId($modelId);
			$expectedBimModel->setName($this->modelName);
			$expectedBimModel->setVfsFileId($this->vfsFileId);
			$expectedBimModel->setFileName($this->vfsFileName);
			
			
			$this->assertEquals($expectedBimModel, $retrievedBimModel);
			
			$sobimmodel->removeBimModelByIdFromDatabase();
			
			$sobimmodel->setModelName($this->modelName);
			$sobimmodel->setVfsdatabaseid($this->vfsFileId);
			
			$this->assertTrue(!$sobimmodel->checkIfModelExists());
			}
			else
			{
			$sobimmodel->removeBimModelFromDatabase();
			$this->removeDummyVfsFile();
			
			$this->fail("Model not found");
		}
		
		
		$this->removeDummyVfsFile();
	}
	
		private function addBimModel(& $sobimmodel)
		{
		$sobimmodel->setModelName($this->modelName);
		
		
		$sobimmodel->setVfsdatabaseid($this->vfsFileId);
			try
			{
			$sobimmodel->addBimModel();
			}
			catch(ModelExistsException $e)
			{
			echo "Warning, model already exists\n";
		}
	}
	
		private function createDummyVfsFile()
		{
		$currentDirectory = dirname(__FILE__);
			$fileNameWithPath = $currentDirectory . DIRECTORY_SEPARATOR . $this->vfsFileName;
		
		$fileHandle = fopen($fileNameWithPath, 'w') or die("Can't open file");
		$result = fwrite($fileHandle, $this->vfsFileContents);
		fclose($fileHandle);
		
		$sovfs = new sovfs_impl();
		//$sovfs->debug = true;
		$sovfs->setFilename($this->vfsFileName);
		$sovfs->setFileNameWithFullPath($fileNameWithPath);
		$sovfs->setSubModule($this->vfsSubModule);
			try
			{
			$sovfs->addFileToVfs();
			}
			catch(FileExistsException $e)
			{
			echo "File already exists\n";
		}
		$this->vfsFileId = $sovfs->retrieveVfsFileId();
		unlink($fileNameWithPath);
	}
	
		private function removeDummyVfsFile()
		{
		$sovfs = new sovfs_impl();
		$sovfs->setFilename($this->vfsFileName);
		$sovfs->setSubModule($this->vfsSubModule);
		
		$sovfs->removeFileFromVfs();
	}
	}