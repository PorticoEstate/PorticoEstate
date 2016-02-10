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
	include('..\..\inc\class.bimmodelinformation.inc.php');

	class TestBimmodelinformation extends PHPUnit_Framework_TestCase
	{

	private $modelInformation;

	/**
	 * Setup the environment for the tests
	 *
	 * @return void
	 */
	protected function setUp()
	{
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
		$this->modelInformation = $xml->modelInformation;
		
		//echo $this->projectXml->();
	}
		/* public function testDisplayModelInfo() {
		echo "---------\n";
		var_dump($this->modelInformation);
		echo "\n---------\n";
		  } */
	
		public function testConvertDate()
		{
		$example8601 = "2001-09-09T01:46:40";
		$bimModelInformation = new BimModelInformation();
		$timeStamp = $bimModelInformation->convertToTimestamp($example8601);
		$this->assertEquals(999992800, $timeStamp);
	}

		public function testLoadXmlModelInformation()
		{
		$bimModelInformation = new BimModelInformation();
		$bimModelInformation->loadVariablesFromXml($this->modelInformation);
		
		$this->assertEquals("reference file created for the Basic FM Handover View", $bimModelInformation->getAuthorization());
		$this->assertEquals("Thomas Liebich", $bimModelInformation->getAuthor());
		$this->assertEquals(1296118479, $bimModelInformation->getChangeDate());
		$this->assertEquals("ViewDefinition [CoordinationView, FMHandOverView]", $bimModelInformation->getDescription());
		$this->assertEquals("AEC3", $bimModelInformation->getOrganization());
		$this->assertEquals("IFC text editor", $bimModelInformation->getOriginatingSystem());
		$this->assertEquals("IFC text editor", $bimModelInformation->getPreProcessor());
		$this->assertEquals(null, $bimModelInformation->getValDate());
		$this->assertEquals("IFC2X3", $bimModelInformation->getNativeSchema());
	}
	}