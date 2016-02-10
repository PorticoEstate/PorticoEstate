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

	class TestBObimitem extends TestBimCommon
	{
	
	/**
	 * @var boolean $backupGlobals disable backup of GLOBALS which breaks things
	 */
	protected $backupGlobals = false;

	/**
	 * @var integer $fieldID The attribute ID used for all the tests
	 */
	protected $fieldID;
	private $sobimitem;
	private $bobimmodel;
	private $validIfcFileName = "valid_ifc_example.ifc";
	private $validIfcFileWithPath;
	private $testingFileName = "restTestFile.txt";
	private $testingFileWithPath;
	
		public function __construct()
		{
			$this->testingFileWithPath = getcwd() . DIRECTORY_SEPARATOR . $this->testingFileName;
			$this->validIfcFileWithPath = getcwd() . DIRECTORY_SEPARATOR . $this->validIfcFileName;
	}
	
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
	
		public function testGetStuff()
		{
		$theXml = $this->getFacilityManagementXmlWithValidIfc();
		
		$bobimitem = new bobimitem_impl();
		$this->sobimitem = new sobimitem_impl($this->db);
		$bobimitem->setModelId(3);
		$bobimitem->setIfcXml($theXml);
		$bobimitem->setSobimitem($this->sobimitem);
		$bobimitem->setSobimtype(new sobimtype_impl($this->db));
		
		$bobimitem->loadIfcItemsIntoDatabase();
	}
	
		private function getFacilityManagementXmlWithValidIfc()
		{
		$sobim_converter = new sobim_converter_impl();
		$sobim_converter->setFileToSend($this->validIfcFileWithPath);
			try
			{
			$returnedXml =  $sobim_converter->getFacilityManagementXml();
			$sxe = simplexml_load_string($returnedXml);
			return $sxe;			
			}
			catch(Exception $e)
			{
			echo $e;
		}
	}
	}