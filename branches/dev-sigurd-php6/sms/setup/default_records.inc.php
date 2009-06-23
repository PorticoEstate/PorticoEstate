<?php
	/**
	* phpGroupWare - sms: A SMS Gateway
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
	 * Description
	 * @package property
	 */

	
	//$app_id = $GLOBALS['phpgw']->applications->name2id('sms');
	$GLOBALS['phpgw_setup']->oProc->query("SELECT app_id FROM phpgw_applications WHERE app_name = 'sms'");
	$GLOBALS['phpgw_setup']->oProc->next_record();
	$app_id = $GLOBALS['phpgw_setup']->oProc->f('app_id');


	$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM phpgw_locations where app_id = {$app_id} AND name != 'run'");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.', 'Top')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_grant) VALUES ({$app_id}, '.inbox', 'InBox',1)");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_grant) VALUES ({$app_id}, '.outbox', 'OutBox',1)");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.autoreply', 'Autoreply')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.board', 'Board')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.command', 'Command')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.custom', 'Custom')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.poll', 'Poll')");

// -- start config

	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_type (id,name, descr) VALUES ('1', 'common', 'common config values')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_type (id,name, descr) VALUES ('2', 'gnokii', 'The gnokii Gateway')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_type (id,name, descr) VALUES ('3', 'clickatell', 'The clickatell Gateway')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_type (id,name, descr) VALUES ('4', 'uplink', 'The Uplink Gateway')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_type (id,name, descr) VALUES ('5', 'kannel', 'The Kannel Gateway')");

	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_attrib (type_id,id,input_type,name, descr) VALUES ('1', '1','listbox', 'gateway_module', 'Active gateway module')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_attrib (type_id,id,input_type,name, descr) VALUES ('1', '2', 'text', 'gateway_number', 'Gateway number')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_attrib (type_id,id,input_type,name, descr) VALUES ('2', '1', 'text', 'gnokii_cfg', 'Gnokii Installation Path')");
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

	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_choice (type_id,attrib_id,id,value) VALUES (1, 1, 1, 'gnokii')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_choice (type_id,attrib_id,id,value) VALUES (1, 1, 2, 'clickatell')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_choice (type_id,attrib_id,id,value) VALUES (1, 1, 3, 'uplink')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_choice (type_id,attrib_id,id,value) VALUES (1, 1, 4, 'kannel')");

	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_value (type_id,attrib_id,id,value) VALUES (1, 1, 1, 'gnokii')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_value (type_id,attrib_id,id,value) VALUES (1, 2, 1, '99999999')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_value (type_id,attrib_id,id,value) VALUES (2, 1, 1, '/usr/local')");

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

//	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_value (type_id,attrib_id,id,value) VALUES (4, 4, 1, '')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_value (type_id,attrib_id,id,value) VALUES (4, 5, 1, '/usr/local')");

	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_value (type_id,attrib_id,id,value) VALUES (5, 1, 1, 'phpgwsms')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_value (type_id,attrib_id,id,value) VALUES (5, 2, 1, 'pwd')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_value (type_id,attrib_id,id,value) VALUES (5, 3, 1, '92824')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_value (type_id,attrib_id,id,value) VALUES (5, 4, 1, '127.0.0.1')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_value (type_id,attrib_id,id,value) VALUES (5, 5, 1, '13131')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_value (type_id,attrib_id,id,value) VALUES (5, 6, 1, 'http://localhost/~phpgroupware/sms')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_config_value (type_id,attrib_id,id,value) VALUES (5, 7, 1, '/usr/local')");


// -- end config

	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_featautoreply (uid, autoreply_code) VALUES (1,'HELP')");

	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_featautoreply_scenario (autoreply_id,autoreply_scenario_param1,autoreply_scenario_param2,autoreply_scenario_param3,autoreply_scenario_param4,autoreply_scenario_param5,autoreply_scenario_param6,autoreply_scenario_param7,autoreply_scenario_result) VALUES (1,'INTERNET','DOWN','','','','','','Please contact sysadmin via phone: +62 21 8613027')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_featautoreply_scenario (autoreply_id,autoreply_scenario_param1,autoreply_scenario_param2,autoreply_scenario_param3,autoreply_scenario_param4,autoreply_scenario_param5,autoreply_scenario_param6,autoreply_scenario_param7,autoreply_scenario_result) VALUES (1,'WEBMAIL','PASSWORD','ERROR','','','','','Please use forgot password link, and follow given instructions')");

	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_featboard (uid,board_code,board_forward_email,board_pref_template) VALUES (1,'PHP','dummy@dummy.org','<font color=black size=-1><b>##SENDER##</b></font><br><font color=black size=-2><i>##DATETIME##</i></font><br><font color=black size=-1>##MESSAGE##</font>')");
//	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_featcommand (uid,command_code,command_exec) VALUES (1,'UPTIME','/home/playsms/public_html/phpgroupware/sms/bin/uptime.sh ##SMSSENDER##')");

	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_featcustom (uid,custom_code,custom_url) VALUES (1,'CURR','http://www.ngoprek.org/currency.php?toeuro=##CUSTOMPARAM##&sender=##SMSSENDER##')");

	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_featpoll (uid,poll_title,poll_code,poll_enable) VALUES (1,'Which do you prefer ?','PREFER',1)");

	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_featpoll_choice (poll_id,choice_title,choice_code) VALUES (1,'Love Without Sex','LWS')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_featpoll_choice (poll_id,choice_title,choice_code) VALUES (1,'Sex Without Love','SWL')");

	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_tblsmsoutgoing ( flag_deleted, uid, p_gateway, p_src, p_dst, p_footer, p_msg, p_datetime, p_status, p_gpid, p_credit, p_sms_type, unicode) VALUES ( 0, 1, 'gnokii', '+628568809027', '+628562283138', ' - anton', 'Hi u there, good morning!!', '2004-12-12 2:39:13', 0, 0, 0, 'text', 0)");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_tblsmsoutgoing ( flag_deleted, uid, p_gateway, p_src, p_dst, p_footer, p_msg, p_datetime, p_status, p_gpid, p_credit, p_sms_type, unicode) VALUES ( 0, 1, 'gnokii', '+628568809027', '+628562205510', ' - anton', 'Hi u there, good morning!!', '2004-12-12 2:39:13', 0, 0, 0, 'text', 0)");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_tblsmsoutgoing ( flag_deleted, uid, p_gateway, p_src, p_dst, p_footer, p_msg, p_datetime, p_update, p_status, p_gpid, p_credit, p_sms_type, unicode) VALUES ( 0, 1, 'uplink', '+628568809027', '+628568809027', '- anton', 'Hello my dear me, testing testing the message', '2004-12-12 2:54:12', '2004-12-12 2:55:11', 1, 0, 0, 'text', 0)");

	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_tblsmstemplate ( uid, t_title, t_text) VALUES ( 1, 'Good morning', 'Hi u there, good morning!!')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_tblsmstemplate ( uid, t_title, t_text) VALUES ( 1, 'Good night have a sweet dream', 'Hi sweetheart, good night and have a sweet dream :*')");


	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_tblusergroupphonebook ( uid, gp_name, gp_code) VALUES ( 1, 'Friends', 'FR')");

	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_tbluserphonebook ( gpid, uid, p_num, p_desc, p_email) VALUES ( 1, 1, '+628562283138', 'Nero', 'nero@phpgwsms.bogus')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_tbluserphonebook ( gpid, uid, p_num, p_desc, p_email) VALUES ( 1, 1, '+628562205510', 'Sleepless', 'sleepless@somedomain.bogus')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_tbluserphonebook ( gpid, uid, p_num, p_desc, p_email) VALUES ( 1, 1, '+39933993399339', 'Crafted Chees', '')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_tbluserphonebook ( gpid, uid, p_num, p_desc, p_email) VALUES ( 1, 1, '+122334455', 'Morfren', 'morfren@nidyumac.doe')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_sms_tbluserphonebook ( gpid, uid, p_num, p_desc, p_email) VALUES ( 1, 1, '+628568809027', 'My Self', 'anton@ngoprek.org')");

?>

