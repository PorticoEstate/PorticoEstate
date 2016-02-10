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

//include('..\..\inc\class.sobim.inc.php');
//include('..\..\inc\class.sobimtype.inc.php');

	class bimBimSuite extends PHPUnit_Framework_TestSuite
	{

		protected static $login = 'peturbjorn';
    // this is is a bit of a hack, but it should work
    protected static $sessionid = '';
	public static $modelName = "dummyModel";
    private $modelId;
    private $bimTypeTableName = 'fm_bim_type';
	private $bimItemTableName = 'fm_bim_item';
	private $projectGuid;
		private $projectType = 'ifcprojecttest';
	private $projectXml;
	private $buildingStorey1Guid;
	private $buildingStorey2Guid;
	private $buildingStorey1Type;
	private $buildingStorey2Type;
	private $buildingStorey1xml;
	private $buildingStorey2xml;
	private $db;
	
    /**
     * @protected array $suite_tests the tests which are part of this test suite
     */
    //protected static $file1 = dirname("TestCustomFields.php").'TestCustomFields.php';
    
    protected static $suite_tests = array
    (
    //    'TestSObimitem.php',
    //	'TestSObimtype.php',
    //	'TestSObimmodel.php',
   // 	'TestSOvfs.php',
    	'TestBObimmodel.php',
   // 	'TestBObimitem.php'
   		'TestUIbim.php',
    	'TestSObimmodelinformation.php'
    );

    /**
     * Get the list of tests for the suite
     *
     * @return object Test Suite to be run
     */
    public static function suite()
    {
        $suite = new bimBimSuite();
		
        //$suite->addTestFiles(self::$suite_tests);
        
       	
        $suite_tests = self::$suite_tests;
			foreach($suite_tests as & $entry)
			{
				$entry = dirname(__FILE__) . DIRECTORY_SEPARATOR . $entry;
        }
       	//$suite_tests = array(dirname(__FILE__).'\TestSObimtype.php');
        
		$suite->addTestFiles($suite_tests);
        return $suite;
    }

    /**
     * Prepare the environment for the test suite to run
     *
     * @return void
     */
    public function setUp()
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

			self::$sessionid = $GLOBALS['phpgw']->session->create(self::$login, '', false);
        $GLOBALS['phpgw_info']['user']['account_id'] = 7;
        phpgw::import_class('bim.sobim');
        phpgw::import_class('bim.sobimitem');
        phpgw::import_class('bim.sobimtype');
        phpgw::import_class('bim.sobimmodel');
        phpgw::import_class('bim.sovfs');
        phpgw::import_class('bim.bobimmodel');
        phpgw::import_class('bim.sobim_converter');
        phpgw::import_class('bim.bobimitem');
        phpgw::import_class('bim.uibim');
        phpgw::import_class('bim.bimmodelinformation');
        phpgw::import_class('bim.sobimmodelinformation');
        $this->db = & $GLOBALS['phpgw']->db;
		$this->loadXmlVariables();
		$this->addDummyModel();
		$this->addTestTypes();
		$this->removeTestItems();
		$this->addTestItems();
    }

    /**
     * Clean up the environment after running the test suite
     *
     * @return void
     */
    public function tearDown()
    {
    	//$this->removeTestItems();
		//$this->removeTestTypes();
        $GLOBALS['phpgw']->session->destroy(self::$sessionid);
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
	
		private function addDummyModel()
		{
		$modelName = self::$modelName;
			if(!$this->checkIfModelExists($modelName))
			{
			$sobimmodel = new sobimmodel_impl($this->db);
		
			$bogusId = $this->getBogusId();
			$sobimmodel->setModelName($modelName);
			$sobimmodel->setVfsdatabaseid($bogusId);
			$sobimmodel->addBimModel();
		}
		$this->modelId = $this->getModelId($modelName);
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

		private function checkIfModelExists($modelName)
		{
		$resultAlias = "id";
			$sql = "select count(*) as $resultAlias from " . sobim::bimModelTable . " where name = '$modelName'";
			if(is_null($this->db->query($sql, __LINE__, __FILE__)))
			{
			throw new Exception('Error checking if model exists!');
			}
			else
			{
			$this->db->next_record();
			$rowCountOfModels =  $this->db->f($resultAlias);
			return ($rowCountOfModels > 0);
		}
	}
	/*
	 * gets an entry from the vfs table in order to satisfy the foreign key constraint
	 * If the table is empty then there will be a problem!
	 */

		private function getBogusId()
		{
		$resultAlias = "id";
		$sql = "select file_id as $resultAlias from phpgw_vfs limit 1";
			if(is_null($this->db->query($sql, __LINE__, __FILE__)))
			{
			throw new Exception('phpgw_vfs table is empty! An item must be added or the tests should be altered!');
			}
			else
			{
			$this->db->next_record();
			return  $this->db->f($resultAlias);
		}
	}

		private function addTestItems()
		{
		/*
		$sobim = new sobim_impl($this->db);
		
		if(!$sobim->checkIfBimItemExists($this->projectGuid)) {
			$sobim->addBimItem(new BimItem(null,$this->projectGuid,$this->projectType, $this->projectXml->asXML() ));
		}
		*/
			if($this->checkIfItemsAlreadyExist())
			{
			//throw new Exception('At least one item already exists in database');
			}
			else
			{
			$this->insertTestItem($this->projectXml->asXML(), $this->projectType, $this->projectGuid);
			$this->insertTestItem($this->buildingStorey1xml->asXML(), $this->buildingStorey1Type, $this->buildingStorey1Guid);
			$this->insertTestItem($this->buildingStorey2xml->asXML(), $this->buildingStorey2Type, $this->buildingStorey2Guid);
		}
	}

		private function removeTestItems()
		{
			if($this->checkIfItemsAlreadyExist())
			{
			$this->removeTestItem($this->projectGuid);
			$this->removeTestItem($this->buildingStorey1Guid);
			$this->removeTestItem($this->buildingStorey2Guid);
		}
	}

		private function removeTestItem($guid)
		{
		$sql = "DELETE FROM $this->bimItemTableName where guid='$guid'";
		$this->db->query($sql);
	}

		private function insertTestItem($itemXml, $itemType, $itemGuid)
		{
		$itemXml = $this->db->db_addslashes($itemXml);
		$sql = "INSERT INTO $this->bimItemTableName (type, guid, xml_representation, model) values (";
			$sql = $sql . "(select id from $this->bimTypeTableName where name = '$itemType'),";
			$sql = $sql . "'$itemGuid', '$itemXml', '$this->modelId')";
		//echo $sql;
			$this->db->query($sql, __LINE__, __FILE__);
	}

		private function addTestTypes()
		{
			if($this->checkIfTestTypesAlreadyExist())
			{
			//throw new Exception('Test type already exists in database!');
			}
			else
			{
			$this->insertTestType($this->buildingStorey1Type);
			$this->insertTestType($this->projectType);
		}
	}

		private function removeTestTypes()
		{
			if($this->checkIfTestTypesAlreadyExist())
			{
			$this->removeTestType($this->buildingStorey1Type);
			$this->removeTestType($this->projectType);
		}
	}

		private function insertTestType($testTypeName)
		{
			$sql = 'INSERT INTO ' . $this->bimTypeTableName . ' (name) VALUES (\'' . $testTypeName . '\')';
		$this->db->query($sql);
	}

		private function removeTestType($testTypeName)
		{
			$sql = "DELETE FROM " . $this->bimTypeTableName . " where name='" . $testTypeName . "'";
		$this->db->query($sql);
	}

		private function checkIfItemsAlreadyExist()
		{
		$resultAlias = 'test_item_count';
			$sql = "SELECT count($this->bimItemTableName.id) as $resultAlias from public.$this->bimItemTableName where " .
			"guid = '$this->projectGuid' OR " .
			"guid = '$this->buildingStorey1Guid' OR " .
			"guid = '$this->buildingStorey2Guid'";
		
			if(is_null($this->db->query($sql, __LINE__, __FILE__)))
			{
			throw new Exception('Query to check items was unsuccessful');
			}
			else
			{
			$this->db->next_record();
			$rowCountOfItemTypes =  $this->db->f($resultAlias);
			
				if($rowCountOfItemTypes != 0)
				{
				return true;
				}
				else
				{
				return false;
			}
		}
	}
	
		private function checkIfTestTypesAlreadyExist()
		{
		$resultAlias = 'test_type_count';
			$sql = 'SELECT  count(' . $this->bimTypeTableName . '.id) as ' . $resultAlias . ' FROM public.' . $this->bimTypeTableName . ' WHERE ' . $this->bimTypeTableName . '.name = \'' . $this->buildingStorey1Type . '\' OR ' . $this->bimTypeTableName . '.name = \'' . $this->projectType . '\'';
		//echo "sql is ::".$sql."::";
		$q = $this->db->query($sql);
			if(is_null($q))
			{
			throw new Exception('Query to check types was unsuccessful');
		}
		$this->db->next_record();
		$rowCountOfTestTypes =  $this->db->f($resultAlias);
			if($rowCountOfTestTypes != 0)
			{
			return true;
			}
			else
			{
			return false;
		}
	}
	}