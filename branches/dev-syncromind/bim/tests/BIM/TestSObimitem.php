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

	class TestSObimitem extends PHPUnit_Framework_TestCase
	{

	private $modelId;
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
		//$GLOBALS['phpgw_info']['user']['account_id'] = 7;
		//$GLOBALS['phpgw']->acl->set_account_id(7); // not sure why this is needed...
		$this->db = & $GLOBALS['phpgw']->db;
		$this->loadXmlVariables();
		$this->modelId = $this->getModelId(bimBimSuite::$modelName);
	}

	/**
	 * Clean up the environment after running a test
	 *
	 * @return void
	 */
	protected function tearDown()
	{
		
	}
	
		private function loadXmlVariables()
		{
		$xml = simplexml_load_file('testData.xml');
		$this->projectXml = $xml->project;
			$this->projectGuid = $this->projectXml->attributes->guid . "++"; //add ++ just in case the test data is in use
			$this->projectType = $this->projectXml['ifcObjectType'] . "_test"; //add _test in case object type already exists
		
		$this->buildingStorey1xml = $xml->buildingStoreys->buildingStorey[0];
			$this->buildingStorey1Guid = $this->buildingStorey1xml->attributes->guid . "++";
			$this->buildingStorey1Type = $this->buildingStorey1xml['ifcObjectType'] . "_test";
		
		$this->buildingStorey2xml = $xml->buildingStoreys->buildingStorey[1];
			$this->buildingStorey2Guid = $this->buildingStorey2xml->attributes->guid . "++";
			$this->buildingStorey2Type = $this->buildingStorey2xml['ifcObjectType'] . "_test";
		
		//echo $this->projectXml->();
	}

		public function testDb()
		{
		$this->assertNotNull($this->db);
	}
	/*
	 * @depends testDb
	 */

		public function testGetAll()
		{
		$sobim = new sobimitem_impl($this->db);
		$bimItems = $sobim->getAll();
		$this->assertGreaterThanOrEqual(3, count($bimItems));
			foreach($bimItems as $bimItem)
			{
			/* @var $bimItem BimItem */
			$this->assertTrue(strlen($bimItem->getType()) > 0);
			$this->assertTrue(strlen($bimItem->getDatabaseId()) > 0);
			$this->assertTrue(strlen($bimItem->getGuid()) > 0);
			$this->assertTrue(strlen($bimItem->getXml()) > 0);
		}
	}
	/*
	 * @depends testGetAll
	 */

		public function testGetBimItem()
		{
		$sobim = new sobimitem_impl($this->db);
		/* @var $bimItem BimItem */  
		$bimItem = $sobim->getBimItem($this->projectGuid);
		
		$this->assertNotNull($bimItem);
		$bimItem->setDatabaseId(0);
		$localBimItem = new BimItem(0, $this->projectGuid, $this->projectType, $this->projectXml->asXML(), $this->modelId);
		
		$this->assertEquals($localBimItem, $bimItem);
	}
	/*
	 * @depends testGetBimItem
	 */

		public function testIfBimItemExists()
		{
		$sobim = new sobimitem_impl($this->db);
		$this->assertTrue($sobim->checkIfBimItemExists($this->projectGuid));
	}
	/*
	 * @depends testIfBimItemExists
	 */

		public function testDeleteBimItem()
		{
		$sobim = new sobimitem_impl($this->db);
		$this->assertEquals(1, $sobim->deleteBimItem($this->projectGuid)); // used to be 3
	}
	/*
	 * @depends testDeleteBimItem
	 */

		public function testAddBimItem()
		{
		$sobim = new sobimitem_impl($this->db);
		$itemToBeAdded = new BimItem(null, $this->projectGuid, $this->projectType, $this->projectXml->asXML(), $this->modelId);
		$this->assertEquals(1, $sobim->addBimItem($itemToBeAdded));
	}
	/*
	 * @depends testAddBimItem
	 */

		public function testUpdateBimItem()
		{
		$sobim = new sobimitem_impl($this->db);
		$bimItem = $sobim->getBimItem($this->projectGuid);
		$xml = new SimpleXMLElement($bimItem->getXml());
		$xml->attributes->name = $this->newProjectName;
		
		$bimItem->setXml($xml->asXML());
		
		$this->assertTrue($sobim->updateBimItem($bimItem));
	}

		public function testExistingAttributeValue()
		{
		$sobim = new sobimitem_impl($this->db);
			try
			{
			$result = $sobim->getBimItemAttributeValue($this->projectGuid, "name");
			$this->assertTrue(in_array($this->newProjectName, $result));
			}
			catch(Exception $e)
			{
			$this->fail($e);
		}
	}

		public function testNonExistingAttributeValue()
		{
		$sobim = new sobimitem_impl($this->db);
			try
			{
			$result = $sobim->getBimItemAttributeValue($this->projectGuid, "nonExisting");
			
			$this->assertFalse(count($result) > 0);
			}
			catch(Exception $e)
			{
			$this->assertTrue(true);
		}
	}
	
		private function getModelId($modelName)
		{
		$resultAlias = "id";
			$sql = "select id as $resultAlias from " . sobim::bimModelTable . " where name = '$modelName'";
			if(is_null($this->db->query($sql, __LINE__, __FILE__)))
			{
			throw new Exception('Error getting model Id');
			}
			else
			{
			$this->db->next_record();
			return  $this->db->f($resultAlias);
		}	
	}
	}