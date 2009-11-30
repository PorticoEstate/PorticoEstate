<?php
/**
 * phpGroupWare custom functions tests
 *
 * @category   UnitTest
 * @package    PHPGroupWare
 * @subpackage PHPGWAPI
 * @author     Dave Hall <dave.hall@skwashd.com>
 * @copyright  2008 Dave Hall
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3 or later
 * @version    SVN: $Id$
 * @link       http://davehall.com.au
 */

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


/**
 * Require PHP Unit to run the test
 */
require_once 'PHPUnit/Framework.php';

/**
 * phpGroupWare custom functions tests
 *
 * @category   UnitTest
 * @package    PHPGroupWare
 * @subpackage PHPGWAPI
 * @author     Dave Hall <dave.hall@skwashd.com>
 * @copyright  2008 Dave Hall
 * @license    http://www.gnu.org/licenses/gpl.html GPLv2 or later
 * @version    Release: 0.9.18
 * @link       http://davehall.com.au
 */
class TestCustomFunctions extends PHPUnit_Framework_TestCase
{
    /**
     * @var boolean $backupGlobals disable backup of GLOBALS which breaks things
     */
    protected $backupGlobals = false;

    /**
     * @var integer $functionID the ID of the function used for testing
     */
    protected $functionID = 0; //apparantly seems to be useless - the $this->functionID does not survive into the next test.

    /**
     * Setup the environment for the tests
     *
     * @return void
     */
    protected function setUp()
    {
        // enable this for one run if it dies badly and you need to clean up
        // $this->tearDown();

    }

    /**
     * Test Custom Functions add method
     *
     * @return void
     */
    public function testAdd()
    {
        $GLOBALS['phpgw']->locations->add('.test', 'Custom Functions Unit Test',
                                        'phpgwapi', false);

        $args = array
        (
            'appname'                => 'phpgwapi',
            'location'                => '.test',
            'custom_function_file'    => 'test.php',
            'descr'                    => 'This is a test',
            'active'                => true
        );

        $this->functionID = $GLOBALS['phpgw']->custom_functions->add($args);

        $this->assertNotEquals(0, $this->functionID);
    }

    /**
     * Test Custom Functions get method
     *
     * @return void
     */
    public function testGet()
    {
		//apparantly - the $this->functionID does not survive into the next test.
        $func = $GLOBALS['phpgw']->custom_functions->get('phpgwapi',
                                                        '.test', 1); //$this->functionID);
        $this->assertNotNull(1, $func);
    }

    /**
     * Test Custom Functions edit method
     *
     * @return void
     */
    public function testEdit()
    {
        $old_func = $GLOBALS['phpgw']->custom_functions->get('phpgwapi', '.test',1);
                                                          // $this->functionID);
        $new_values = array
        (
            'appname'                => 'phpgwapi',
            'location'                => '.test',
            'descr'                    => 'this was renamed by a unit test',
            'custom_function_file'    => 'crackme.php'
        );
        $new_values = array_merge($old_func, $new_values);

        $GLOBALS['phpgw']->custom_functions->edit($new_values);

        $new_func = $GLOBALS['phpgw']->custom_functions->get('phpgwapi', '.test',1);
                                                          //  $this->functionID);

        $this->assertNotEquals($old_func, $new_func);
    }

    /**
     * Test number of entries returned from Custom Functions find method
     *
     * @return void
     */
    public function testFindCount()
    {
        $criteria = array
        (
            'appname'    => 'phpgwapi',
            'location'    => '.test',
            'start'        => 0,
            'sort'        => 'DESC',
            'order'        => 'file_name',
            'query'        => 'crackme.php'
        );

        $funcs = $GLOBALS['phpgw']->custom_functions->find($criteria);

        $this->assertEquals(1, count($funcs));
    }

    /**
     * Test Custom Functions get method
     *
     * @return void
     */
    public function testGetFail()
    {
        $func = $GLOBALS['phpgw']->custom_functions->get('phpgwapi',
                                                        '.test', 2, true);
        
        $this->clean();
        $this->assertNull($func);
    }

    /**
     * Clean up the environment after running a test
     *
     * @return void
     */
    protected function clean()
    {
        $args = array
        (
        	'appname'	=> 'phpgwapi',
        	'location'  => '.test'
        );
        $funcs = $GLOBALS['phpgw']->custom_functions->find($args);

        foreach ( $funcs as $func )
        {
            $GLOBALS['phpgw']->custom_functions->delete('phpgwapi',
                                                        '.test', $func['id']);
        }

        $GLOBALS['phpgw']->locations->delete('phpgwapi', '.test', true);
    }

}
