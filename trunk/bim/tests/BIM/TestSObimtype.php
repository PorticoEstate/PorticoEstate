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

	class TestSObimtype extends PHPUnit_Framework_TestCase
	{

	private $db;
	private $testBimObjectType = "testType010101";
	private $testBimTypeDescription = "My description";

	/**
	 * @var boolean $backupGlobals disable backup of GLOBALS which breaks things
	 */
	protected $backupGlobals = false;

	/**
	 * Setup the environment for the tests
	 *
	 * @return void
	 */
	protected function setUp()
	{
		$this->db = & $GLOBALS['phpgw']->db;
	}
	
		public function testDb()
		{
		$this->assertNotNull($this->db);
	}
	
		public function testAddBimObjectTypeWithoutDescription()
		{
		$sobimtype = new sobimtype_impl($this->db);//impl($this->db);
		$this->assertTrue($sobimtype->addBimObjectType($this->testBimObjectType));
	}
	/*
	 * This test will usually fail if the test items are already in the db before the test begins!
	 */

		public function testCheckIfBimTypeWasAdded()
		{
		$sobimtype = new sobimtype_impl($this->db);
		/* @var $bimTypeFromDb BimType */
		$bimTypeFromDb = $sobimtype->getBimObjectType($this->testBimObjectType);
		$bimTypeLocal = new BimType(null, $this->testBimObjectType);
		$bimTypeLocal->setId($bimTypeFromDb->getId());
		$this->assertEquals($bimTypeLocal, $bimTypeFromDb);
	}

		public function testAddDescription()
		{
		$sobimtype = new sobimtype_impl($this->db);
		$this->assertTrue($sobimtype->updateBimObjectTypeDescription($this->testBimObjectType, $this->testBimTypeDescription));
		$bimTypeFromDb = $sobimtype->getBimObjectType($this->testBimObjectType);
		$bimTypeLocal = new BimType(null, $this->testBimObjectType, $this->testBimTypeDescription);
		$bimTypeLocal->setId($bimTypeFromDb->getId());
		$this->assertEquals($bimTypeLocal, $bimTypeFromDb);
	}
	
		public function testDeleteAddedBimObject()
		{
		$sobimtype = new sobimtype_impl($this->db);
		$result = $sobimtype->deleteBimObjectType($this->testBimObjectType);
		$this->assertTrue($result);
	}
	}