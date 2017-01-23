<?php
	/**
	 * phpGroupWare - eventplanner.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package eventplanner
	 * @subpackage setup
	 * @version $Id: tables_update.inc.php 14728 2016-02-11 22:28:46Z sigurdne $
	 */
	/**
	 * Update eventplanner version from 0.9.17.000 to 0.9.17.001
	 */
	$test[] = '0.9.18.001';

	function eventplanner_upgrade0_9_18_001()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'eventplanner_booking_comment', array(
					'fd' => array(
						'id' => array('type' => 'auto', 'nullable' => False),
						'booking_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
						'time' => array('type' => 'int', 'precision' => '8', 'nullable' => False),
						'author' => array('type' => 'text', 'nullable' => False),
						'comment' => array('type' => 'text', 'nullable' => False),
						'type' => array('type' => 'varchar', 'precision' => '20', 'nullable' => false,'default' => 'comment'),
					),
					'pk' => array('id'),
					'fk' => array(
						'eventplanner_booking' => array('booking_id' => 'id')),
					'ix' => array(),
					'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['eventplanner']['currentver'] = '0.9.18.002';
		}
		return $GLOBALS['setup_info']['eventplanner']['currentver'];
	}

	$test[] = '0.9.18.002';

	function eventplanner_upgrade0_9_18_002()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('eventplanner_application', 'num_granted_events', array(
			'type' => 'int', 'precision' => 4, 'nullable' => True,'default' => 0));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['eventplanner']['currentver'] = '0.9.18.003';
		}
		return $GLOBALS['setup_info']['eventplanner']['currentver'];
	}

	$test[] = '0.9.18.003';

	function eventplanner_upgrade0_9_18_003()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('eventplanner_vendor', 'vendor_organization_number', 'organization_number');
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('eventplanner_customer', 'customer_organization_number', 'organization_number');

		$GLOBALS['phpgw_setup']->oProc->DropColumn('eventplanner_vendor', array(), 'vendor_identifier_type');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('eventplanner_vendor', array(), 'vendor_ssn');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('eventplanner_customer', array(), 'customer_identifier_type');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('eventplanner_customer', array(), 'customer_ssn');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['eventplanner']['currentver'] = '0.9.18.004';
		}
		return $GLOBALS['setup_info']['eventplanner']['currentver'];
	}
