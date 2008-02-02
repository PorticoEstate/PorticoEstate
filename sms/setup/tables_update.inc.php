<?php
	/**
	* phpGroupWare - sms: A SMS Gateway.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package sms
	* @subpackage setup
 	* @version $Id$
	*/

	/**
	* Update sms version from 0.9.17.000 to 0.9.17.001
	*/

	$test[] = '0.9.17.501';
	function sms_upgrade0_9_17_501()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_sms_config_type',array(
				'fd' => array(
					'id' => array('type' => 'int','precision' => 4,'nullable' => False),
					'name' => array('type' => 'varchar', 'precision' => 50,'nullable' => False),
					'descr' => array('type' => 'varchar', 'precision' => 200,'nullable' => true)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_sms_config_attrib',array(
				'fd' => array(
					'type_id' => array('type' => 'int','precision' => 4,'nullable' => False),
					'id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'input_type' => array('type' => 'varchar', 'precision' => 10,'nullable' => False),
					'name' => array('type' => 'varchar', 'precision' => 50,'nullable' => False),
					'descr' => array('type' => 'varchar', 'precision' => 200,'nullable' => true)
				),
				'pk' => array('type_id','id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_sms_config_choice',array(
				'fd' => array(
					'type_id' => array('type' => 'int','precision' => 4,'nullable' => False),
					'attrib_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'value' => array('type' => 'varchar', 'precision' => 20,'nullable' => False)
				),
				'pk' => array('type_id','attrib_id','id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array('type_id','attrib_id','value')
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_sms_config_value',array(
				'fd' => array(
					'type_id' => array('type' => 'int','precision' => 4,'nullable' => False),
					'attrib_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'value' => array('type' => 'varchar', 'precision' => 200,'nullable' => False)
				),
				'pk' => array('type_id','attrib_id','id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array('type_id','attrib_id','value')
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_type (id,name, descr) VALUES ('1', 'common', 'common config values')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_type (id,name, descr) VALUES ('2', 'gnokii', 'The gnokii Gateway')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_attrib (type_id,id,input_type,name, descr) VALUES (1, 1,'listbox', 'gateway_module', 'Active gateway module')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_attrib (type_id,id,input_type,name, descr) VALUES (1, 2, 'text', 'gateway_number', 'Gateway number')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_attrib (type_id,id,input_type,name, descr) VALUES (2, 1, 'text', 'gnokii_cfg', 'Gnokii Installation Path')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_choice (type_id,attrib_id,id,value) VALUES (1, 1, 1, 'gnokii')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_choice (type_id,attrib_id,id,value) VALUES (1, 1, 2, 'clickatell')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_choice (type_id,attrib_id,id,value) VALUES (1, 1, 3, 'uplink')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_choice (type_id,attrib_id,id,value) VALUES (1, 1, 4, 'kannel')");
		
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_value (type_id,attrib_id,id,value) VALUES (1, 1, 1, 'gnokii')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_value (type_id,attrib_id,id,value) VALUES (1, 2, 1, '99999999')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_value (type_id,attrib_id,id,value) VALUES (2, 1, 1, '/usr/local')");
		
		$GLOBALS['setup_info']['sms']['currentver'] = '0.9.17.502';
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit();
		return $GLOBALS['setup_info']['sms']['currentver'];
	}

	$test[] = '0.9.17.502';
	function sms_upgrade0_9_17_502()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_sms_gwmodclickatell_config');
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_sms_gwmodgnokii_config');
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_sms_gwmodkannel_config');
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_sms_gwmodtemplate_config');
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_sms_gwmoduplink_config');
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_sms_tblconfig_main');
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_sms_tbluser');
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_sms_tbluser_country');
	
		$GLOBALS['phpgw_setup']->oProc->query("DELETE from phpgw_config WHERE config_app='sms'");
		
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_type (id,name, descr) VALUES ('3', 'clickatell', 'The clickatell Gateway')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_type (id,name, descr) VALUES ('4', 'uplink', 'The Uplink Gateway')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_type (id,name, descr) VALUES ('5', 'kannel', 'The Kannel Gateway')");
	
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_attrib (type_id,id,input_type,name, descr) VALUES (3, 1, 'text', 'api_id', 'Clickatell API ID')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_attrib (type_id,id,input_type,name, descr) VALUES (3, 2, 'text', 'username', 'Clickatell username')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_attrib (type_id,id,input_type,name, descr) VALUES (3, 3, 'text', 'password', 'Clickatell password')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_attrib (type_id,id,input_type,name, descr) VALUES (3, 4, 'text', 'sender', 'Clickatell Global Sender')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_attrib (type_id,id,input_type,name, descr) VALUES (3, 5, 'text', 'send_url', 'Clickatell API URL')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_attrib (type_id,id,input_type,name, descr) VALUES (3, 6, 'text', 'incoming_path', 'Clickatell Incoming Path')");			
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_attrib (type_id,id,input_type,name, descr) VALUES (3, 7, 'text', 'credit', 'What is this')");			

		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_attrib (type_id,id,input_type,name, descr) VALUES (4, 1, 'text', 'master', 'Uplink Master URL')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_attrib (type_id,id,input_type,name, descr) VALUES (4, 2, 'text', 'username', 'Uplink username')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_attrib (type_id,id,input_type,name, descr) VALUES (4, 3, 'text', 'password', 'Uplink password')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_attrib (type_id,id,input_type,name, descr) VALUES (4, 4, 'text', 'global_sender', 'Uplink Global Sender')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_attrib (type_id,id,input_type,name, descr) VALUES (4, 5, 'text', 'incoming_path', 'Uplink Incoming Path')");	

		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_attrib (type_id,id,input_type,name, descr) VALUES (5, 1, 'text', 'username', 'Kannel username')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_attrib (type_id,id,input_type,name, descr) VALUES (5, 2, 'text', 'password', 'Kannel password')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_attrib (type_id,id,input_type,name, descr) VALUES (5, 3, 'text', 'global_sender', 'Kannel global sender')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_attrib (type_id,id,input_type,name, descr) VALUES (5, 4, 'text', 'bearerbox_host', 'Kannel bearerbox_host')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_attrib (type_id,id,input_type,name, descr) VALUES (5, 5, 'text', 'sendsms_port', 'Kannel Send SMS Port')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_attrib (type_id,id,input_type,name, descr) VALUES (5, 6, 'text', 'phpgwsms_web', 'phpgwsms Web URL')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_attrib (type_id,id,input_type,name, descr) VALUES (5, 7, 'text', 'incoming_path', 'Kannel incoming path')");
	
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_value (type_id,attrib_id,id,value) VALUES (3, 1, 1, '123456')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_value (type_id,attrib_id,id,value) VALUES (3, 2, 1, 'phpgwsms')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_value (type_id,attrib_id,id,value) VALUES (3, 3, 1, 'pwd')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_value (type_id,attrib_id,id,value) VALUES (3, 4, 1, 'phpgwsms')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_value (type_id,attrib_id,id,value) VALUES (3, 5, 1, 'http://api.clickatell.com/http')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_value (type_id,attrib_id,id,value) VALUES (3, 6, 1, 'usr/local')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_value (type_id,attrib_id,id,value) VALUES (3, 7, 1, '10')");
	
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_value (type_id,attrib_id,id,value) VALUES (4, 1, 1, 'http://cpanel.smsrakyat.net')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_value (type_id,attrib_id,id,value) VALUES (4, 2, 1, 'phpgwsms')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_value (type_id,attrib_id,id,value) VALUES (4, 3, 1, 'pwd')");

		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_value (type_id,attrib_id,id,value) VALUES (4, 5, 1, '/usr/local')");

		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_value (type_id,attrib_id,id,value) VALUES (5, 1, 1, 'phpgwsms')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_value (type_id,attrib_id,id,value) VALUES (5, 2, 1, 'pwd')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_value (type_id,attrib_id,id,value) VALUES (5, 3, 1, '92824')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_value (type_id,attrib_id,id,value) VALUES (5, 4, 1, '127.0.0.1')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_value (type_id,attrib_id,id,value) VALUES (5, 5, 1, '13131')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_value (type_id,attrib_id,id,value) VALUES (5, 6, 1, 'http://localhost/~phpgroupware/sms')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_value (type_id,attrib_id,id,value) VALUES (5, 7, 1, '/usr/local')");					

		$GLOBALS['setup_info']['sms']['currentver'] = '0.9.17.503';
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit();
		return $GLOBALS['setup_info']['sms']['currentver'];
	}

	$test[] = '0.9.17.503';
	function sms_upgrade0_9_17_503()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_sms_featcommand','command_type',array('type' => 'varchar','precision' => 10,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_sms_featcommand','command_descr',array('type' => 'text', 'nullable' => True));
		
		$GLOBALS['setup_info']['sms']['currentver'] = '0.9.17.504';
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit();
		return $GLOBALS['setup_info']['sms']['currentver'];
	}

	$test[] = '0.9.17.504';
	function sms_upgrade0_9_17_504()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_sms_featcommand_log','command_log_param',array('type' => 'varchar','precision' => 150,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_sms_featcommand_log','command_log_success',array('type' => 'int','precision' => 2,'nullable' => True));
		
		$GLOBALS['setup_info']['sms']['currentver'] = '0.9.17.505';
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit();
		return $GLOBALS['setup_info']['sms']['currentver'];
	}	
	
	$test[] = '0.9.17.505';
	function sms_upgrade0_9_17_505()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_acl_location (appname,id, descr) VALUES ('sms', '.autoreply', 'Autoreply')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_acl_location (appname,id, descr) VALUES ('sms', '.board', 'Board')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_acl_location (appname,id, descr) VALUES ('sms', '.command', 'Command')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_acl_location (appname,id, descr) VALUES ('sms', '.custom', 'Custom')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_acl_location (appname,id, descr) VALUES ('sms', '.poll', 'Poll')");

		
		$GLOBALS['setup_info']['sms']['currentver'] = '0.9.17.506';
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit();
		return $GLOBALS['setup_info']['sms']['currentver'];
	}
