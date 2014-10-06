<?php
/**
 * phpGroupWare custom fields tests
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
 * phpGroupWare custom fields tests
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
class TestCustomFields extends PHPUnit_Framework_TestCase
{
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


    }

    /**
     * Clean up the environment after running a test
     *
     * @return void
     */ 
    protected function clean()
    {
        $fields = $GLOBALS['phpgw']->custom_fields->find('phpgwapi', '.test');
        foreach ( $fields as $field ) {
            $GLOBALS['phpgw']->custom_fields->delete('phpgwapi', 
                                                    '.test', $field['id']);
        }

        $GLOBALS['phpgw']->locations->delete('phpgwapi', '.test', true);
    }

    /**
     * Test Custom Fields add method
     *
     * @return void
     */
    public function testAdd()
    {
        $GLOBALS['phpgw']->locations->add('.test', 'Custom Functions Unit Test',
                                        'phpgwapi', false, 'aaaa_test');

        $oProc        = createObject('phpgwapi.schema_proc',
                            $GLOBALS['phpgw_info']['server']['db_type']);
        $oProc->m_odb =& $GLOBALS['phpgw']->db;

        $table_def = array
        (
            'fd' => array
                    (
                        'aaaa_id'    => array
                                        (
                                            'type' => 'auto',
                                            'precision' => 4,
                                            'nullable' => false
                                        )
                    ),
            'pk' => array('aaaa_id'),
            'fk' => array(),
            'ix' => array(),
            'uc' => array()
        );

        $oProc->createTable('aaaa_test', $table_def);

		$status = 'this value is ignored by modern browsers default configs';
        $field = array
        (
            'appname'       => 'phpgwapi',
            'location'      => '.test',
            'column_name'   => 'test_entry',
            'input_text'    => 'this is a test entry',
            'statustext'    => $status,
            'search'        => true,
            'list'          => true,
            'history'       => true,
            'disabled'      => false,
            'helpmsg'       => 'i am helpful - i think',
            'attrib_sort'   => 0,
            'nullable'      => true,
            'column_info'   => array
                                (
                                    'type'      => 'V',
                                    'precision' => 50,
                                    'scale'     => '',
                                    'default'   => ''
                                )
        );

        $this->fieldID = $GLOBALS['phpgw']->custom_fields->add($field);
        $this->assertTrue($this->fieldID > 0);
    }

    /**
     * Test Custom Fields get method
     *
     * @return void
     */
    public function testGet()
    {
        $field = $GLOBALS['phpgw']->custom_fields->get('phpgwapi',
                                                        '.test', 1); //$this->fieldID);

        $this->assertNotEquals(0, count($field));
    }

    /**
     * Test Custom Fields edit method
     *
     * @return void
     */
    public function testEdit()
    {
        $old_field = $GLOBALS['phpgw']->custom_fields->get('phpgwapi',
                                                        '.test', 1); //$this->fieldID);

        $loc = $GLOBALS['phpgw']->locations->get_name($old_field['location_id']);

        $new_values = array
        (
            'id'           => 1,//$this->fieldID,
            'appname'      => $loc['appname'],
            'location'     => $loc['location'],
            'datatype'     => 'R',
            'column_name'  => 'renamed_column',
            'input_text'   => 'this was renamed by a unit test',
            'statustext'   => 'yes I really am useless',
            'search'       => false,
            'new_choice'   => 'new_entry'
        );

        $values = array_merge($old_field, $new_values);

        $GLOBALS['phpgw']->custom_fields->edit($values);

        $new_field = $GLOBALS['phpgw']->custom_fields->get('phpgwapi',
                                                        '.test', 1);//$this->fieldID);

        $this->assertNotEquals($old_field, $new_field);
    }

    /**
     * Test number of entries returned from Custom Fields find method
     *
     * @return void
     */
    public function testFindCount()
    {
        $fields = $GLOBALS['phpgw']->custom_fields->find('phpgwapi', '.test', 
                                                        0, '', 'ASC', 'attrib_sort',
                                                        false, true);

        $this->assertEquals(1, count($fields));
    }

    /**
     * Test keys of entries returned from Custom Fields find method
     *
     * @return void
     */
    public function testFindCountNoChoice()
    {
        $fields = $GLOBALS['phpgw']->custom_fields->find('phpgwapi', '.test');

        $this->assertArrayHasKey(1, $fields);
    }

    /**
     * Test Custom Fields get method
     *
     * @return void
     */
    public function testGetFound()
    {
        $field = $GLOBALS['phpgw']->custom_fields->get('phpgwapi',
                                                    '.test', 1); //$this->fieldID);
        
        $this->assertNotNull($field);
    }

    /**
     * Test Custom Fields get method
     *
     * @return void
     */
    public function testGetFail()
    {
        $field = $GLOBALS['phpgw']->custom_fields->get('phpgwapi',
                                                    '.test', 2);
        
        $this->assertNull($field);
    }

    /**
     * Test Custom Fields get method
     *
     * @return void
     */
    public function testGetChoices()
    {
        $field = $GLOBALS['phpgw']->custom_fields->get('phpgwapi', '.test', 1, true);
                                                       // $this->fieldID, true);
        
        $this->clean();
        $this->assertArrayNotHasKey('choice', $field);
    }
}
