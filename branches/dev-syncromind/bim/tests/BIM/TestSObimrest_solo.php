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

	class TestSObim extends PHPUnit_Framework_TestCase
	{

	protected static $login = 'peturbjorn';
    // this is is a bit of a hack, but it should work
    protected static $sessionid = '';
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
        
       
		//$GLOBALS['phpgw_info']['user']['account_id'] = 7;
		//$GLOBALS['phpgw']->acl->set_account_id(7); // not sure why this is needed...
		//require('..\..\inc\class.sobim.inc.php');
		//require('..\..\inc\class.sobimtype.inc.php');
		phpgw::import_class('bim.sobimrest');
	}

	/**
	 * Clean up the environment after running a test
	 *
	 * @return void
	 */
	protected function tearDown()
	{
		
	}
	
		public function testGetCount()
		{
		$sobimrest = new sobimrest_impl();
		$sobimrest->getRepositoryCountJson();
	}
	
		public function testGetNames()
		{
		$sobimrest = new sobimrest_impl();
		$sobimrest->getRepositoryNames();
	}
	}