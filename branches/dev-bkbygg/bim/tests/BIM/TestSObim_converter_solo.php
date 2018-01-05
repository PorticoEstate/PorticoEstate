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





	define('PHPGW_API_UNIT_TEST_PATH', dirname(__FILE__));

	class TestSObim_converter extends PHPUnit_Framework_TestCase
	{

	protected static $login = 'peturbjorn';
    // this is is a bit of a hack, but it should work
    protected static $sessionid = '';
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
		 $GLOBALS['phpgw_info']['flags'] = array
        (
            'currentapp'    => 'login',
            'login'            => true,
            'noapi'            => false,
            'noheader'        => true
        );

        $header = realpath(PHPGW_API_UNIT_TEST_PATH . '/../../..')
                . '/header.inc.php';
        include_once $header;

       /* self::$sessionid = $GLOBALS['phpgw']->session->create(self::$login,
			  '', false); */
        
       
		//$GLOBALS['phpgw_info']['user']['account_id'] = 7;
		//$GLOBALS['phpgw']->acl->set_account_id(7); // not sure why this is needed...
		//require('..\..\inc\class.sobim.inc.php');
		//require('..\..\inc\class.sobimtype.inc.php');
		phpgw::import_class('bim.sobim_converter');
		phpgw::import_class('bim.restrequest');
		
		$this->createTestingFile();
		echo "---------------------------\n";
	}

	/**
	 * Clean up the environment after running a test
	 *
	 * @return void
	 */
	protected function tearDown()
	{
		$this->deleteTestingfile();
	}
	
		public function testGet()
		{
		
		$rest = new RestRequest();
		$rest->setUrl("http://localhost:8080/bm/rest/uploadIfc");
		$rest->setAcceptType("text/html");
		$rest->execute();
		$body = $rest->getResponseBody();
		echo "Response Body:$body\n";
		$this->assertTrue(strlen(strstr($body, "You have accept type")) > 0);
	}

		public function testRestRequestPost()
		{
		
		$url = "http://localhost:8080/bm/rest/tests/testPut";
		$verb = "POST";
			$data = array(
				'file' => '@' . $this->testingFileWithPath
		);
		//var_dump( $data );
		
		$rest = new RestRequest($url, $verb, $data);
		//$rest->setUrl("http://145.247.163.52:8080/BIM_Facility_Management/rest/tests/testPut/ttt");
		// http://145.247.163.52:8080/BIM_Facility_Management/rest/uploadIfc
		
		$rest->setAcceptType("text/html");
		$rest->setLocalFile($this->testingFileWithPath);
		$rest->execute();
		echo $rest->getResponseBody();
		echo "\n resp info \n";
		var_dump($rest->getResponseInfo());
	}
		/* public function testRestRequestPut() {
		$rest = new RestRequest();
		$rest->setUrl("http://localhost:8080/BIM_Facility_Management/rest/tests/testPut/lala");
		$rest->setUrl("http://145.247.163.52:8080/BIM_Facility_Management/rest/tests/testPut/ttt");
		// http://145.247.163.52:8080/BIM_Facility_Management/rest/uploadIfc
		$rest->setVerb("PUT");
		$rest->setAcceptType("text/html");
		$rest->setLocalFile($this->testingFileWithPath);
		$rest->execute();
		echo $rest->getResponseBody();
		
		  } */
	
		public function testManual()
		{
		$url = "http://localhost:8080/BIM_Facility_Management/rest/tests/testPut";
		//$url = "http://10.0.0.1:8080/BIM_Facility_Management/rest/tests/testPut";
		$ch = curl_init(); 
			$data = array('type' => 'direct', 'file' => "@$this->testingFileWithPath", 'value1' => 'aaaaa1');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt($ch, CURLOPT_POST, 1);
		//curl_setopt($ch, CURLOPT_POSTFIELDS, array('type' => 'direct', 'file'=>"@$this->testingFileWithPath", 'value1'=>'aaaaa1'));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5 * 60); //seconds
		
			$result = curl_exec($ch);
		echo "\n Result : $result \n";
			echo "Error:" . curl_error($ch);
		
		$info = curl_getinfo($ch);
		
		var_dump($info);
	}

		private function createTestingFile()
		{
		$fileHandle = fopen($this->testingFileWithPath, 'w');
		fwrite($fileHandle, "This is a test file for the rest service\n Please delete this file if you come across it!");
		fclose($fileHandle);
	}	

		private function deleteTestingfile()
		{
		//unset($this->testingFileWithPath);
	}
	
		public function testgetFacilityManagementXmlWithInvalidIfc()
		{
		$sobim_converter = new sobim_converter_impl();
		$sobim_converter->setFileToSend($this->testingFileWithPath);
			try
			{
			$result =  $sobim_converter->getFacilityManagementXml();
				echo "Result is:" . $result . "\n -- result end \n";
			}
			catch(Exception $e)
			{
				echo "Exception thrown is:" . $e . "\n -- result end \n";
		}
	}
	
		public function testgetFacilityManagementXmlWithValidIfc()
		{
		$sobim_converter = new sobim_converter_impl();
		$sobim_converter->setFileToSend($this->validIfcFileWithPath);
		$sobim_converter->setBaseUrl("http://localhost:8080/bm/rest/");
			try
			{
			$returnedXml =  $sobim_converter->getFacilityManagementXml();
			$sxe = simplexml_load_string($returnedXml);
			$this->assertTrue(isset($sxe), "Invalid XML!");
			}
			catch(Exception $e)
			{
			echo $e;
		}
	}
	}