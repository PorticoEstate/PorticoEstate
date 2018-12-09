<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2009 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package helpdesk
	* @subpackage setup
	 * @version $Id: tables_update.inc.php 15163 2016-05-13 14:24:03Z sigurdne $
	*/
	/**
	* Update helpdesk version from 0.9.18.000 to 0.9.18.001
	*/
	$test[] = '0.9.18.000';

	function helpdesk_upgrade0_9_18_000()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_helpdesk_tickets', 'modified_date', array(
			'type' => 'int',
			'precision' => 8,
			'nullable' => True)
			);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['helpdesk']['currentver'] = '0.9.18.001';
			return $GLOBALS['setup_info']['helpdesk']['currentver'];
		}
	}

	/**
	* Update helpdesk version from 0.9.18.001 to 0.9.18.002
	*/
	$test[] = '0.9.18.001';

	function helpdesk_upgrade0_9_18_001()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_helpdesk_response_template', array(
				'fd' => array(
					'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
					'name' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False),
					'content' => array('type' => 'text', 'nullable' => True),
					'public' => array('type' => 'int', 'precision' => 2, 'nullable' => True),
					'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
					'entry_date' => array('type' => 'int', 'precision' => 8, 'nullable' => True, 'default' => 'current_timestamp'),
					'modified_date' => array('type' => 'int', 'precision' => 8, 'nullable' => True, 'default' => 'current_timestamp'),
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['helpdesk']['currentver'] = '0.9.18.002';
			return $GLOBALS['setup_info']['helpdesk']['currentver'];
		}
	}

	/**
	* Update helpdesk version from 0.9.18.002 to 0.9.18.003
	*/
	$test[] = '0.9.18.002';

	function helpdesk_upgrade0_9_18_002()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_helpdesk_custom_menu_items', array(
				'fd' => array(
					'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
					'parent_id' => array('type' => 'int', 'precision' => '4', 'nullable' => True),
					'text' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False),
					'url' => array('type' => 'text', 'nullable' => True),
					'target' => array('type' => 'varchar', 'precision' => '15', 'nullable' => True),
					'location' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False),
					'local_files' => array('type' => 'int', 'precision' => 2, 'nullable' => true),
					'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
					'entry_date' => array('type' => 'int', 'precision' => 8, 'nullable' => True, 'default' => 'current_timestamp'),
					'modified_date' => array('type' => 'int', 'precision' => 8, 'nullable' => True, 'default' => 'current_timestamp'),
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw']->locations->add('.custom', 'Custom reports', 'helpdesk', $allow_grant = false, $custom_tbl = false, $c_function = false);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['helpdesk']['currentver'] = '0.9.18.003';
			return $GLOBALS['setup_info']['helpdesk']['currentver'];
		}
	}

	/**
	* Update helpdesk version from 0.9.18.003 to 0.9.18.004
	*/
	$test[] = '0.9.18.003';

	function helpdesk_upgrade0_9_18_003()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();


		$GLOBALS['phpgw']->locations->add('.email_out', 'email out', 'helpdesk');

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_helpdesk_email_template', array(
				'fd' => array(
					'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
					'name' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False),
					'content' => array('type' => 'text', 'nullable' => True),
					'public' => array('type' => 'int', 'precision' => 2, 'nullable' => True),
					'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
					'created' => array('type' => 'int', 'precision' => 8, 'nullable' => True, 'default' => 'current_timestamp'),
					'modified' => array('type' => 'int', 'precision' => 8, 'nullable' => True, 'default' => 'current_timestamp'),
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_helpdesk_email_out', array(
				'fd' => array(
					'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
					'name' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False),
					'remark' => array('type' => 'text', 'nullable' => True),
					'subject' => array('type' => 'text', 'nullable' => false),
					'content' => array('type' => 'text', 'nullable' => True),
					'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
					'created' => array('type' => 'int', 'precision' => 8, 'nullable' => True, 'default' => 'current_timestamp'),
					'modified' => array('type' => 'int', 'precision' => 8, 'nullable' => True, 'default' => 'current_timestamp'),
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);


		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_helpdesk_email_out_recipient_set',  array(
				'fd' => array(
					'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
					'name' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False),
					'active' => array('type' => 'int', 'precision' => 2, 'nullable' => True, 'default' => '0'),
					'public' => array('type' => 'int', 'precision' => 2, 'nullable' => True),
					'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
					'created' => array('type' => 'int', 'precision' => 8, 'nullable' => True, 'default' => 'current_timestamp'),
					'modified' => array('type' => 'int', 'precision' => 8, 'nullable' => True, 'default' => 'current_timestamp'),
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
		));

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_helpdesk_email_out_recipient_list',  array(
				'fd' => array(
					'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
					'set_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
					'name' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False),
					'email' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False),
					'active' => array('type' => 'int', 'precision' => 2, 'nullable' => True, 'default' => '0'),
					'public' => array('type' => 'int', 'precision' => 2, 'nullable' => True),
					'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
					'created' => array('type' => 'int', 'precision' => 8, 'nullable' => True, 'default' => 'current_timestamp'),
					'modified' => array('type' => 'int', 'precision' => 8, 'nullable' => True, 'default' => 'current_timestamp'),
				),
				'pk' => array('id'),
				'fk' => array(
					'phpgw_helpdesk_email_out_recipient_set' => array('set_id' => 'id'),
				),
				'ix' => array(),
				'uc' => array('set_id', 'email')
		));

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_helpdesk_email_out_recipient',  array(
				'fd' => array(
					'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
					'email_out_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
					'recipient_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
					'status' => array('type' => 'int', 'precision' => 2, 'nullable' => True, 'default' => '0'),
				),
				'pk' => array('id'),
				'fk' => array(
					'phpgw_helpdesk_email_out' => array('email_out_id' => 'id'),
					'phpgw_helpdesk_email_out_recipient_list' => array('recipient_id' => 'id'),
				),
				'ix' => array(),
				'uc' => array()
		));


		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['helpdesk']['currentver'] = '0.9.18.004';
			return $GLOBALS['setup_info']['helpdesk']['currentver'];
		}
	}
	/**
	* Update helpdesk version from 0.9.18.004 to 0.9.18.005
	*/
	$test[] = '0.9.18.004';

	function helpdesk_upgrade0_9_18_004()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_helpdesk_tickets', 'reverse_id', array(
			'type' => 'int',
			'precision' => '4',
			'nullable' => true
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['helpdesk']['currentver'] = '0.9.18.005';
			return $GLOBALS['setup_info']['helpdesk']['currentver'];
		}
	}

	/**
	* Update helpdesk version from 0.9.18.005 to 0.9.18.006
	*/
	$test[] = '0.9.18.005';

	function helpdesk_upgrade0_9_18_005()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw']->locations->add('.ticket.response_template', 'email out', 'helpdesk');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['helpdesk']['currentver'] = '0.9.18.006';
			return $GLOBALS['setup_info']['helpdesk']['currentver'];
		}
	}

	$test[] = '0.9.18.006';

	function helpdesk_upgrade0_9_18_006()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_helpdesk_tickets', 'external_ticket_id',array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => true
			)
		);

		$GLOBALS['phpgw']->locations->add('.ticket.external_communication', 'Helpdesk external communication', 'helpdesk', $allow_grant = false, $custom_tbl = false, $c_function = true);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_helpdesk_external_communication', array(
				'fd' => array(
					'id' => array('type' => 'auto', 'nullable' => False),
					'ticket_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
					'order_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
					'type_id' => array('type' => 'int', 'precision' => 2, 'nullable' => False),
					'vendor_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
					'subject' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False),
					'mail_recipients' => array('type' => 'text', 'nullable' => True),
					'file_attachments' => array('type' => 'varchar', 'precision' => 255, 'nullable' => True),
					'deadline' => array('type' => 'int', 'precision' => 8, 'nullable' => True),
					'deadline2' => array('type' => 'int', 'precision' => 8, 'nullable' => True),
					'created_on' => array('type' => 'int', 'precision' => 8, 'nullable' => true),
					'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
					'modified_date' => array('type' => 'int', 'precision' => 8, 'nullable' => True),
				),
				'pk' => array('id'),
				'ix' => array(),
				'fk' => array(
					'phpgw_helpdesk_tickets' => array('ticket_id' => 'id')
					),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_helpdesk_external_communication_type', array(
				'fd' => array(
					'id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
					'name' => array('type' => 'varchar', 'precision' => 100, 'nullable' => true),
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_helpdesk_external_communication_msg',  array(
				'fd' => array(
					'id' => array('type' => 'auto', 'nullable' => False),
					'excom_id' => array('type' => 'int', 'precision' => 4, 'nullable' => False),
					'message' => array('type' => 'text', 'nullable' => False),
					'timestamp_sent' => array('type' => 'int', 'precision' => 8, 'nullable' => True),
					'mail_recipients' => array('type' => 'text', 'nullable' => True),
					'file_attachments' => array('type' => 'varchar', 'precision' => 255, 'nullable' => True),
					'sender_email_address' => array('type' => 'varchar', 'precision' => 255, 'nullable' => True),
					'created_on' => array('type' => 'int', 'precision' => 8, 'nullable' => true),
					'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				),
				'pk' => array('id'),
				'ix' => array(),
				'fk' => array(
					'phpgw_helpdesk_external_communication' => array('excom_id' => 'id')
					),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['helpdesk']['currentver'] = '0.9.18.007';
			return $GLOBALS['setup_info']['helpdesk']['currentver'];
		}
	}
