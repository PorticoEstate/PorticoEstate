<?php
	/**
	* Addressbook - Setup
	*
	* @copyright Copyright (C) 2000-2002,2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package addressbook
	* @subpackage setup
	* @version $Id$
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

	$test[] = '0.9.17.500';
	/**
	* Allow custom fields on relation org_person.
	*
	* @return string the new version number
	*/
	function addressbook_upgrade0_9_17_500()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw']->locations->add('org_person', "Allow custom fields on relation org_person", 'addressbook', false, 'phpgw_contact_org_person');

		if ( $GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit() )
		{
			$GLOBALS['setup_info']['addressbook']['currentver'] = '0.9.17.501';
			return $GLOBALS['setup_info']['addressbook']['currentver'];
		}
	}

	$test[] = '0.9.17.501';
	/**
	* Allow custom fields on relation org_person.
	*
	* @return string the new version number
	*/
	function addressbook_upgrade0_9_17_501()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw']->locations->add('person', "Allow custom fields on table person", 'addressbook', false, 'phpgw_contact_person');
		$GLOBALS['phpgw']->locations->add('organisation', "Allow custom fields on table org", 'addressbook', false, 'phpgw_contact_org');

		if ( $GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit() )
		{
			$GLOBALS['setup_info']['addressbook']['currentver'] = '0.9.17.502';
			return $GLOBALS['setup_info']['addressbook']['currentver'];
		}
	}	
