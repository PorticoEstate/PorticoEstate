<?php
	/**
	* Addressbook - Setup
	*
	* @copyright Copyright (C) 2000-2002,2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package addressbook
	* @subpackage setup
	* @version $Id: tables_update.inc.php 17922 2007-02-07 20:37:22Z sigurdne $
	*/


	$test[] = '0.9.13.002';
	/**
	* Update from 0.9.13.002 to 0.9.13.003
	*
	* @return string New version number
	*/
	function addressbook_upgrade0_9_13_002()
	{
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_addressbook_servers', array(
				'fd' => array(
					'name'    => array('type' => 'varchar', 'precision' => 64,  'nullable' => False),
					'basedn'  => array('type' => 'varchar', 'precision' => 255, 'nullable' => True),
					'search'  => array('type' => 'varchar', 'precision' => 32,  'nullable' => True),
					'attrs'   => array('type' => 'varchar', 'precision' => 255, 'nullable' => True),
					'enabled' => array('type' => 'int', 'precision' => 4)
				),
				'pk' => array('name'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['setup_info']['addressbook']['currentver'] = '0.9.13.003';
		return $GLOBALS['setup_info']['addressbook']['currentver'];
	}

	$test[] = '0.9.16.000';
	function addressbook_upgrade0_9_16_000()
	{
		$GLOBALS['setup_info']['addressbook']['currentver'] = '0.9.17.500';
		return $GLOBALS['setup_info']['addressbook']['currentver'];
	}
?>
