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

	class TestSObimmodelinformation extends PHPUnit_Framework_TestCase
	{

	private $bimTypeTableName = 'fm_bim_type';
	private $bimItemTableName = 'fm_bim_item';
	private $modelId;
	private $modelInformation;
	private $db;
	
	/**
	 * @var boolean $backupGlobals disable backup of GLOBALS which breaks things
	 */
	protected $backupGlobals = false;

	/**
	 * @var integer $fieldID The attribute ID used for all the tests
	 */
	protected $fieldID;

	/**
	 * Setup the environment for the tests
	 *
	 * @return void
	 */
	protected function setUp()
	{
		$GLOBALS['phpgw_info']['user']['account_id'] = 7;
		$this->db = & $GLOBALS['phpgw']->db;
		$this->loadXmlVariables();
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
		$xml = simplexml_load_file('wholeModelOutputExample.xml');
		$modelInformationXml = $xml->modelInformation;
		$this->modelInformation = new BimModelInformation();
		$this->modelInformation->loadVariablesFromXml($modelInformationXml);
	}
	
		public function testUpdateModelInfo()
		{
		$this->modelId = 3;
		
		$sobimInfo = new sobimmodelinformation_impl($this->db, $this->modelId, $this->modelInformation);
		$sobimInfo->updateModelInformation();
		$var = 0;
			echo "Testing empty" . empty($var);
	}

		public function testGetModelInfo()
		{
		$this->modelId = 3;
		
		$sobimInfo = new sobimmodelinformation_impl($this->db, $this->modelId);
		$modelInfo = $sobimInfo->getModelInformation();
		
		$this->assertEquals("reference file created for the Basic FM Handover View", $modelInfo->getAuthorization());
		$this->assertEquals("Thomas Liebich", $modelInfo->getAuthor());
		$this->assertEquals(1296118479, $modelInfo->getChangeDate());
		$this->assertEquals("ViewDefinition [CoordinationView, FMHandOverView]", $modelInfo->getDescription());
		$this->assertEquals("AEC3", $modelInfo->getOrganization());
		$this->assertEquals("IFC text editor", $modelInfo->getOriginatingSystem());
		$this->assertEquals("IFC text editor", $modelInfo->getPreProcessor());
		$this->assertEquals(null, $modelInfo->getValDate());
		$this->assertEquals("IFC2X3", $modelInfo->getNativeSchema());
	}
	}