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



class phpGroupWareTestSuite extends PHPUnit_Framework_TestSuite
{
    protected static $login = 'peturbjorn';

    // this is is a bit of a hack, but it should work
    protected static $sessionid = '';

    /**
     * @protected array $suite_tests the tests which are part of this test suite
     */
    //protected static $file1 = dirname("TestCustomFields.php").'TestCustomFields.php';
    
    protected static $suite_tests = array
    (
       // 'TestCustomFunctions.php',
        'TestCustomFields.php'
    );

    /**
     * Get the list of tests for the suite
     *
     * @return object Test Suite to be run
     */
    public static function suite()
    {
        $suite = new phpGroupWareTestSuite();
		
        //$suite->addTestFiles(self::$suite_tests);
        
        $suite_tests = array(dirname(__FILE__).'\TestSObim.php');
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

        self::$sessionid = $GLOBALS['phpgw']->session->create(self::$login,
                                                            '', false);
    }

    /**
     * Clean up the environment after running the test suite
     *
     * @return void
     */
    public function tearDown()
    {
        $GLOBALS['phpgw']->session->destroy(self::$sessionid);
    }
}
