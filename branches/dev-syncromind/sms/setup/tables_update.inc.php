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
	
	/**
	* Update property version from 0.9.17.506 to 0.9.17.507
	*/

	$test[] = '0.9.17.506';
	function sms_upgrade0_9_17_506()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_sms_featautoreply_scenario','autoreply_scenario_param2',array('type' => 'varchar', 'precision' => 20,'nullable' => true));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_sms_featautoreply_scenario','autoreply_scenario_param3',array('type' => 'varchar', 'precision' => 20,'nullable' => true));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_sms_featautoreply_scenario','autoreply_scenario_param4',array('type' => 'varchar', 'precision' => 20,'nullable' => true));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_sms_featautoreply_scenario','autoreply_scenario_param5',array('type' => 'varchar', 'precision' => 20,'nullable' => true));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_sms_featautoreply_scenario','autoreply_scenario_param6',array('type' => 'varchar', 'precision' => 20,'nullable' => true));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_sms_featautoreply_scenario','autoreply_scenario_param7',array('type' => 'varchar', 'precision' => 20,'nullable' => true));

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_sms_tblsmsoutgoing','p_src',array('type' => 'varchar', 'precision' => 100,'nullable' => true));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_sms_tblsmsoutgoing','p_footer',array('type' => 'varchar', 'precision' => 11,'nullable' => true));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['sms']['currentver'] = '0.9.17.507';
			return $GLOBALS['setup_info']['sms']['currentver'];
		}
	}

	/**
	* Update sms version from 0.9.17.507 to 0.9.17.508
	*/

	$test[] = '0.9.17.507';
	function sms_upgrade0_9_17_507()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("SELECT name FROM phpgw_sms_config_attrib WHERE type_id = 6 AND id = 1");
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$test = $GLOBALS['phpgw_setup']->oProc->f('name');

		if($test != 'wsdl')
		{
			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_choice (section_id,attrib_id,id,value) VALUES (1, 1, 5, 'carrot')");
			$GLOBALS['phpgw_setup']->oProc->query("SELECT max(type_id) as type_id FROM phpgw_sms_config_attrib");
			$GLOBALS['phpgw_setup']->oProc->next_record();
			$type_id = $GLOBALS['phpgw_setup']->oProc->f('type_id') +1;

			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_attrib (type_id,id,input_type,name, descr) VALUES ({$type_id}, 1, 'text', 'wsdl', 'Carrot wsdl')");
			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_attrib (type_id,id,input_type,name, descr) VALUES ({$type_id}, 2, 'text', 'send_url', 'send url using GET')");
			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_attrib (type_id,id,input_type,name, descr) VALUES ({$type_id}, 3, 'text', 'service_url', 'service_url using SOAP')");
			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_attrib (type_id,id,input_type,name, descr) VALUES ({$type_id}, 4, 'text', 'login', 'Carrot login')");
			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_attrib (type_id,id,input_type,name, descr) VALUES ({$type_id}, 5, 'password', 'password', 'Carrot password')");
			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_attrib (type_id,id,input_type,name, descr) VALUES ({$type_id}, 6, 'text', 'proxy_host', 'proxy_host')");
			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_attrib (type_id,id,input_type,name, descr) VALUES ({$type_id}, 7, 'text', 'proxy_port', 'proxy_port')");
			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_attrib (type_id,id,input_type,name, descr) VALUES ({$type_id}, 8, 'text', 'originator', 'originator')");
			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_attrib (type_id,id,input_type,name, descr) VALUES ({$type_id}, 9, 'text', 'originatortype', 'originatortype')");
			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_attrib (type_id,id,input_type,name, descr) VALUES ({$type_id}, 10, 'text', 'serviceid', 'serviceid')");
			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_attrib (type_id,id,input_type,name, descr) VALUES ({$type_id}, 11, 'text', 'differentiator', 'differentiator')");
			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_attrib (type_id,id,input_type,name, descr) VALUES ({$type_id}, 12, 'listbox', 'type', 'Send type')");

			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_choice (type_id,attrib_id,id,value) VALUES ({$type_id}, 12, 1, 'GET')");
			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_choice (type_id,attrib_id,id,value) VALUES ({$type_id}, 12, 2, 'SOAP')");
		}

		$GLOBALS['phpgw_setup']->oProc->query("SELECT name FROM phpgw_sms_config_type WHERE id = 6");
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$test = $GLOBALS['phpgw_setup']->oProc->f('name');
		if(!$test)
		{
			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_type (id,name,descr) VALUES (6, 'carrot', 'TDC gateway')");
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['sms']['currentver'] = '0.9.17.508';
			return $GLOBALS['setup_info']['sms']['currentver'];
		}
	}

	/**
	* Update sms version from 0.9.17.508 to 0.9.17.509
	* Convert to new config scheme
	*/

	$test[] = '0.9.17.508';
	function sms_upgrade0_9_17_508()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$location_id = $GLOBALS['phpgw']->locations->get_id('sms', 'run');

		$db =& $GLOBALS['phpgw_setup']->oProc->m_odb;
		$db->query("SELECT count(*) as num_sections FROM phpgw_config2_section");
		$db->next_record();
		$num_sections = $db->f('num_sections');

		$db->query("SELECT * FROM phpgw_sms_config_type");
		$sections = array();
		while ($db->next_record())
		{
			$sections[] = array
			(
				'location_id'	=> $location_id,
				'id'			=> $db->f('id') + $num_sections,
				'name'			=> $db->f('name'),
				'descr'			=> $db->f('descr')
			);
		}

		foreach ($sections as $section)
		{	
			$sql = 'INSERT INTO phpgw_config2_section(' . implode(',',array_keys($section)) . ') '
				 . ' VALUES (' . $db->validate_insert(array_values($section)) . ')';
			$db->query($sql, __LINE__, __FILE__);
		}

		$db->query("SELECT * FROM phpgw_sms_config_attrib");
		$attribs = array();
		while ($db->next_record())
		{
			$attribs[] = array
			(
				'section_id'	=> $db->f('type_id') + $num_sections,
				'id'			=> $db->f('id'),
				'input_type'	=> $db->f('input_type'),
				'name'			=> $db->f('name'),
				'descr'			=> $db->f('descr')
			);
		}

		foreach ($attribs as $attrib)
		{	
			$sql = 'INSERT INTO phpgw_config2_attrib(' . implode(',',array_keys($attrib)) . ') '
				 . ' VALUES (' . $db->validate_insert(array_values($attrib)) . ')';
			$db->query($sql, __LINE__, __FILE__);
		}

		$db->query("SELECT * FROM phpgw_sms_config_choice");
		$choices = array();
		while ($db->next_record())
		{
			$choices[] = array
			(
				'section_id'	=> $db->f('type_id') + $num_sections,
				'attrib_id'		=> $db->f('attrib_id'),
				'id'			=> $db->f('id'),
				'value'			=> $db->f('value')
			);
		}

		foreach ($choices as $choice)
		{	
			$sql = 'INSERT INTO phpgw_config2_choice(' . implode(',',array_keys($choice)) . ') '
				 . ' VALUES (' . $db->validate_insert(array_values($choice)) . ')';
			$db->query($sql, __LINE__, __FILE__);
		}

		$db->query("SELECT * FROM phpgw_sms_config_value");
		$values = array();
		while ($db->next_record())
		{
			$values[] = array
			(
				'section_id'	=> $db->f('type_id') + $num_sections,
				'attrib_id'		=> $db->f('attrib_id'),
				'id'			=> $db->f('id'),
				'value'			=> $db->f('value')
			);
		}

		foreach ($values as $value)
		{	
			$sql = 'INSERT INTO phpgw_config2_value(' . implode(',',array_keys($value)) . ') '
				 . ' VALUES (' . $db->validate_insert(array_values($value)) . ')';
			$db->query($sql, __LINE__, __FILE__);
		}

		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_sms_config_type');
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_sms_config_attrib');
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_sms_config_choice');
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_sms_config_value');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['sms']['currentver'] = '0.9.17.509';
			return $GLOBALS['setup_info']['sms']['currentver'];
		}
	}

	/**
	* Update sms version from 0.9.17.509 to 0.9.17.510
	* Convert to new config scheme
	*/

	$test[] = '0.9.17.509';
	function sms_upgrade0_9_17_509()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$db =& $GLOBALS['phpgw_setup']->oProc->m_odb;
		$db->query("UPDATE phpgw_config2_attrib SET name = 'gateway_module_get', descr = 'Active gateway module GET' WHERE name = 'gateway_module'");

		$db->query("SELECT * FROM phpgw_config2_attrib WHERE name = 'gateway_number'");
		$db->next_record();
		$section_id = $db->f('section_id');
		$id = $db->f('id') + 1;
		$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES ({$section_id}, {$id},'listbox', 'gateway_module_send', 'Active gateway module SEND')");
		$db->query("INSERT INTO phpgw_config2_choice (section_id,attrib_id,id,value) VALUES ({$section_id}, {$id}, 1, 'gnokii')");
		$db->query("INSERT INTO phpgw_config2_choice (section_id,attrib_id,id,value) VALUES ({$section_id}, {$id}, 2, 'clickatell')");
		$db->query("INSERT INTO phpgw_config2_choice (section_id,attrib_id,id,value) VALUES ({$section_id}, {$id}, 3, 'uplink')");
		$db->query("INSERT INTO phpgw_config2_choice (section_id,attrib_id,id,value) VALUES ({$section_id}, {$id}, 4, 'kannel')");
		$db->query("INSERT INTO phpgw_config2_choice (section_id,attrib_id,id,value) VALUES ({$section_id}, {$id}, 5, 'carrot')");

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['sms']['currentver'] = '0.9.17.510';
			return $GLOBALS['setup_info']['sms']['currentver'];
		}
	}

	/**
	* Update sms version from 0.9.17.510 to 0.9.17.511
	* new to new config section
	*/

	$test[] = '0.9.17.510';
	function sms_upgrade0_9_17_510()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$custom_config	= CreateObject('admin.soconfig',$GLOBALS['phpgw']->locations->get_id('sms', 'run'));

		$receipt_section_bergen = $custom_config->add_section(array
			(
				'name' => 'bergen_kommune',
				'descr' => 'bergen kommune SMS config-section'
			)
		);

		$receipt = $custom_config->add_attrib(array
			(
				'section_id'	=> $receipt_section_bergen['section_id'],
				'input_type'	=> 'text',
				'name'			=> 'service_url',
				'descr'			=> 'service_url'
			)
		);
		$receipt = $custom_config->add_attrib(array
			(
				'section_id'	=> $receipt_section_bergen['section_id'],
				'input_type'	=> 'text',
				'name'			=> 'login',
				'descr'			=> 'login'
			)
		);
		$receipt = $custom_config->add_attrib(array
			(
				'section_id'	=> $receipt_section_bergen['section_id'],
				'input_type'	=> 'password',
				'name'			=> 'password',
				'descr'			=> 'password'
			)
		);
		$receipt = $custom_config->add_attrib(array
			(
				'section_id'	=> $receipt_section_bergen['section_id'],
				'input_type'	=> 'text',
				'name'			=> 'wsdl',
				'descr'			=> 'wsdl'
			)
		);
		$receipt = $custom_config->add_attrib(array
			(
				'section_id'	=> $receipt_section_bergen['section_id'],
				'input_type'	=> 'text',
				'name'			=> 'proxy_host',
				'descr'			=> 'proxy_host'
			)
		);
		$receipt = $custom_config->add_attrib(array
			(
				'section_id'	=> $receipt_section_bergen['section_id'],
				'input_type'	=> 'text',
				'name'			=> 'proxy_port',
				'descr'			=> 'proxy_port'
			)
		);
		$receipt = $custom_config->add_attrib(array
			(
				'section_id'	=> $receipt_section_bergen['section_id'],
				'input_type'	=> 'text',
				'name'			=> 'orgnr',
				'descr'			=> 'orgnr'
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_sms_tblsmsoutgoing','external_id',
			array
			(
				'type' => 'int',
				'precision' => 4,
				'nullable' => True
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['sms']['currentver'] = '0.9.17.511';
			return $GLOBALS['setup_info']['sms']['currentver'];
		}
	}


	/**
	* Update sms version from 0.9.17.511 to 0.9.17.512
	* 
	*/

	$test[] = '0.9.17.511';
	function sms_upgrade0_9_17_511()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();


		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_sms_received_data', array(
				'fd' => array(
					'id' => array('type' => 'auto', 'nullable' => False),
					'type' => array('type' => 'varchar', 'precision' => 15, 'nullable' => False),/*sms/mms/report*/
					'data' => array('type' => 'text', 'nullable' => False),
					'status' => array('type' => 'int', 'precision' => 4, 'nullable' => False, 'default' => '0'),
					'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => False, 'default' => '0'),
					'modified_date' => array('type' => 'int', 'precision' => 4, 'nullable' => False, 'default' => '0'),
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['sms']['currentver'] = '0.9.17.512';
			return $GLOBALS['setup_info']['sms']['currentver'];
		}
	}
