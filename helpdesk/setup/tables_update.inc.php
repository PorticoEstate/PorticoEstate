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
				'uc' => array(array('set_id', 'email'))
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

	$test[] = '0.9.18.007';

	function helpdesk_upgrade0_9_18_007()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();


		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_helpdesk_cat_assignment', array(
				'fd' => array(
					'cat_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
					'group_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
					'created_on' => array('type' => 'int', 'precision' => 8, 'nullable' => true),
					'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				),
				'pk' => array('cat_id', 'group_id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);


		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['helpdesk']['currentver'] = '0.9.18.008';
			return $GLOBALS['setup_info']['helpdesk']['currentver'];
		}
	}

	$test[] = '0.9.18.008';
	function helpdesk_upgrade0_9_18_008()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();


		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_helpdesk_email_out_recipient_list', 'alias',array(
			'type' => 'varchar',
			'precision' => 25,
			'nullable' => True
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_helpdesk_email_out_recipient_list', 'office',array(
			'type' => 'varchar',
			'precision' => 255,
			'nullable' => True
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_helpdesk_email_out_recipient_list', 'department',array(
			'type' => 'varchar',
			'precision' => 255,
			'nullable' => True
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_helpdesk_email_out_recipient_list', 'alias_supervisor',array(
			'type' => 'varchar',
			'precision' => 25,
			'nullable' => True
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_helpdesk_email_out_recipient_list', 'name_supervisor',array(
			'type' => 'varchar',
			'precision' => 255,
			'nullable' => True
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_helpdesk_email_out_recipient_list', 'email_supervisor',array(
			'type' => 'varchar',
			'precision' => 255,
			'nullable' => True
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['helpdesk']['currentver'] = '0.9.18.009';
			return $GLOBALS['setup_info']['helpdesk']['currentver'];
		}
	}

	$test[] = '0.9.18.009';
	function helpdesk_upgrade0_9_18_009()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();


		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_helpdesk_tickets', 'external_origin_email',array(
			'type' => 'varchar',
			'precision' => 255,
			'nullable' => True
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['helpdesk']['currentver'] = '0.9.18.010';
			return $GLOBALS['setup_info']['helpdesk']['currentver'];
		}
	}

	$test[] = '0.9.18.010';
	function helpdesk_upgrade0_9_18_010()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_helpdesk_cat_respond_messages', array(
				'fd' => array(
					'cat_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
					'include_content' => array('type' => 'int', 'precision' => 2, 'nullable' => true),
					'new_message' => array('type' => 'text', 'nullable' => true),
					'set_user_message' => array('type' => 'text', 'nullable' => true),
					'update_message' => array('type' => 'text', 'nullable' => true),
					'close_message' => array('type' => 'text', 'nullable' => true),
					'created_on' => array('type' => 'int', 'precision' => 8, 'nullable' => true),
					'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				),
				'pk' => array('cat_id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['helpdesk']['currentver'] = '0.9.18.011';
			return $GLOBALS['setup_info']['helpdesk']['currentver'];
		}
	}

	$test[] = '0.9.18.011';
	function helpdesk_upgrade0_9_18_011()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_helpdesk_response_template', 'category',array(
			'type' => 'int',
			'precision' => 2,
			'nullable' => True
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query('UPDATE phpgw_helpdesk_response_template SET category = 1', __LINE__, __FILE__);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['helpdesk']['currentver'] = '0.9.18.012';
			return $GLOBALS['setup_info']['helpdesk']['currentver'];
		}
	}

	$test[] = '0.9.18.012';
	function helpdesk_upgrade0_9_18_012()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_helpdesk_tickets', 'on_behalf_of_name',array(
			'type' => 'varchar',
			'precision' => 255,
			'nullable' => True
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['helpdesk']['currentver'] = '0.9.18.013';
			return $GLOBALS['setup_info']['helpdesk']['currentver'];
		}
	}

	$test[] = '0.9.18.013';
	function helpdesk_upgrade0_9_18_013()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
				'phpgw_helpdesk_cat_anonyminizer', array(
				'fd' => array(
					'cat_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
					'active' => array('type' => 'int', 'precision' => 2, 'nullable' => true),
					'limit_days' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
					'created_on' => array('type' => 'int', 'precision' => 8, 'nullable' => true),
					'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				),
				'pk' => array('cat_id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['helpdesk']['currentver'] = '0.9.18.014';
			return $GLOBALS['setup_info']['helpdesk']['currentver'];
		}
	}

	/**
	 * convert data
	 */
	$test[] = '0.9.18.014';
	function helpdesk_upgrade0_9_18_014()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("SELECT id, content FROM phpgw_helpdesk_response_template");
		$response_templates = array();
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$response_templates[] = array(
				'id'		 => $GLOBALS['phpgw_setup']->oProc->f('id'),
				'content'	 => $GLOBALS['phpgw_setup']->oProc->m_odb->db_addslashes(nl2br($GLOBALS['phpgw_setup']->oProc->f('content', true)))
			);
		}

		foreach ($response_templates as $response_template)
		{
			$GLOBALS['phpgw_setup']->oProc->query("UPDATE phpgw_helpdesk_response_template SET content = '{$response_template['content']}'"
			. " WHERE id = {$response_template['id']}");
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['helpdesk']['currentver'] = '0.9.18.015';
			return $GLOBALS['setup_info']['helpdesk']['currentver'];
		}
	}

	/**
	 * alter column
	 */
	$test[] = '0.9.18.015';
	function helpdesk_upgrade0_9_18_015()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_helpdesk_external_communication', 'file_attachments', array(
			'type' => 'text',
			'nullable' => True
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_helpdesk_external_communication_msg', 'file_attachments', array(
			'type' => 'text',
			'nullable' => True
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['helpdesk']['currentver'] = '0.9.18.016';
			return $GLOBALS['setup_info']['helpdesk']['currentver'];
		}
	}

	/**
	 * alter column
	 */
	$test[] = '0.9.18.016';
	function helpdesk_upgrade0_9_18_016()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('phpgw_helpdesk_response_template', 'public', 'public_');
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('phpgw_helpdesk_email_out_recipient_set', 'public', 'public_');
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('phpgw_helpdesk_email_out_recipient_list', 'public', 'public_');
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('phpgw_helpdesk_email_template', 'public', 'public_');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['helpdesk']['currentver'] = '0.9.18.017';
			return $GLOBALS['setup_info']['helpdesk']['currentver'];
		}
	}
