<?php
	/**
	* phpGroupWare - sms: A SMS Gateway
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2012 Free Software Foundation, Inc. http://www.fsf.org/
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

	$db =& $GLOBALS['phpgw_setup']->oProc->m_odb;
	$db->query("SELECT app_id FROM phpgw_applications WHERE app_name = 'sms'");
	$db->next_record();
	$app_id = $db->f('app_id');

	$location_id = $GLOBALS['phpgw']->locations->get_id('sms', 'run');

/*
	$db->query("SELECT id FROM phpgw_config2_section WHERE location_id = '$location_id'");
	
	$old_sections = array();
	while($db->next_record())
	{
		$old_sections[] = $db->f('id');
	}
	
	if ($old_sections)
	{
		$db->query('DELETE FROM phpgw_config2_section WHERE id IN (' . explode(',',$old_sections ) . ')');
		$db->query('DELETE FROM phpgw_config2_attrib WHERE section_id IN (' . explode(',',$old_sections ) . ')');
		$db->query('DELETE FROM phpgw_config2_choice WHERE section_id IN (' . explode(',',$old_sections ) . ')');
		$db->query('DELETE FROM phpgw_config2_value WHERE section_id IN (' . explode(',',$old_sections ) . ')');
	}
*/

	$db->query("DELETE FROM phpgw_locations where app_id = {$app_id} AND name != 'run'");
	$db->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.', 'Top')");
	$db->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_grant) VALUES ({$app_id}, '.inbox', 'InBox',1)");
	$db->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_grant) VALUES ({$app_id}, '.outbox', 'OutBox',1)");
	$db->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.autoreply', 'Autoreply')");
	$db->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.board', 'Board')");
	$db->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.command', 'Command')");
	$db->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.custom', 'Custom')");
	$db->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.poll', 'Poll')");

// -- start config

	$db->query("SELECT max(id) as num_sections FROM phpgw_config2_section");
	$db->next_record();
	$num_sections = (int)$db->f('num_sections');

	$db->query("INSERT INTO phpgw_config2_section (id, location_id, name, descr) VALUES (" . (1 + $num_sections) . ", $location_id, 'common', 'common config values')");
	$db->query("INSERT INTO phpgw_config2_section (id, location_id, name, descr) VALUES (" . (2 + $num_sections) . ", $location_id, 'gnokii', 'The gnokii Gateway')");
	$db->query("INSERT INTO phpgw_config2_section (id, location_id, name, descr) VALUES (" . (3 + $num_sections) . ", $location_id, 'clickatell', 'The clickatell Gateway')");
	$db->query("INSERT INTO phpgw_config2_section (id, location_id, name, descr) VALUES (" . (4 + $num_sections) . ", $location_id, 'uplink', 'The Uplink Gateway')");
	$db->query("INSERT INTO phpgw_config2_section (id, location_id, name, descr) VALUES (" . (5 + $num_sections) . ", $location_id, 'kannel', 'The Kannel Gateway')");
	$db->query("INSERT INTO phpgw_config2_section (id, location_id, name, descr) VALUES (" . (6 + $num_sections) . ", $location_id, 'carrot', 'The Carrot Gateway')");
	$db->query("INSERT INTO phpgw_config2_section (id, location_id, name, descr) VALUES (" . (7 + $num_sections) . ", $location_id, 'pswin', 'The Pswin Gateway')");

	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (1 + $num_sections) . ", 1,'listbox', 'gateway_module_get', 'Active gateway module GET')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (1 + $num_sections) . ", 2,'listbox', 'gateway_module_send', 'Active gateway module SEND')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (1 + $num_sections) . ", 3, 'text', 'gateway_number', 'Gateway number')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (1 + $num_sections) . ", 4, 'text', 'anonymous_user', 'anonymous user for delivering data via wev-service')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (1 + $num_sections) . ", 5, 'password', 'anonymous_pass', 'Password for anonymous user for delivering data via wev-service')");


	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (2 + $num_sections) . ", 1, 'text', 'gnokii_cfg', 'Gnokii Installation Path')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (3 + $num_sections) . ", 1, 'text', 'api_id', 'Clickatell API ID')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (3 + $num_sections) . ", 2, 'text', 'username', 'Clickatell username')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (3 + $num_sections) . ", 3, 'password', 'password', 'Clickatell password')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (3 + $num_sections) . ", 4, 'text', 'sender', 'Clickatell Global Sender')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (3 + $num_sections) . ", 5, 'text', 'send_url', 'Clickatell API URL')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (3 + $num_sections) . ", 6, 'text', 'incoming_path', 'Clickatell Incoming Path')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (3 + $num_sections) . ", 7, 'text', 'credit', 'What is this')");


	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (4 + $num_sections) . ", 1, 'text', 'master', 'Uplink Master URL')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (4 + $num_sections) . ", 2, 'text', 'username', 'Uplink username')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (4 + $num_sections) . ", 3, 'password', 'password', 'Uplink password')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (4 + $num_sections) . ", 4, 'text', 'global_sender', 'Uplink Global Sender')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (4 + $num_sections) . ", 5, 'text', 'incoming_path', 'Uplink Incoming Path')");

	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (5 + $num_sections) . ", 1, 'text', 'username', 'Kannel username')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (5 + $num_sections) . ", 2, 'password', 'password', 'Kannel password')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (5 + $num_sections) . ", 3, 'text', 'global_sender', 'Kannel global sender')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (5 + $num_sections) . ", 4, 'text', 'bearerbox_host', 'Kannel bearerbox_host')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (5 + $num_sections) . ", 5, 'text', 'sendsms_port', 'Kannel Send SMS Port')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (5 + $num_sections) . ", 6, 'text', 'phpgwsms_web', 'phpgwsms Web URL')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (5 + $num_sections) . ", 7, 'text', 'incoming_path', 'Kannel incoming path')");

	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (6 + $num_sections) . ", 1, 'text', 'wsdl', 'Carrot wsdl')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (6 + $num_sections) . ", 2, 'text', 'send_url', 'send url using GET')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (6 + $num_sections) . ", 3, 'text', 'service_url', 'service_url using SOAP')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (6 + $num_sections) . ", 4, 'text', 'login', 'Carrot login')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (6 + $num_sections) . ", 5, 'password', 'password', 'Carrot password')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (6 + $num_sections) . ", 6, 'text', 'proxy_host', 'proxy_host')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (6 + $num_sections) . ", 7, 'text', 'proxy_port', 'proxy_port')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (6 + $num_sections) . ", 8, 'text', 'originator', 'originator')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (6 + $num_sections) . ", 9, 'text', 'originatortype', 'originatortype')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (6 + $num_sections) . ", 10, 'text', 'serviceid', 'serviceid')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (6 + $num_sections) . ", 11, 'text', 'differentiator', 'differentiator')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (6 + $num_sections) . ", 12, 'listbox', 'type', 'Send type')");

	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (7 + $num_sections) . ", 1, 'text', 'wsdl', 'pswin wsdl')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (7 + $num_sections) . ", 2, 'text', 'send_url', 'send url using GET')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (7 + $num_sections) . ", 3, 'text', 'service_url', 'service_url using SOAP')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (7 + $num_sections) . ", 4, 'text', 'login', 'pswin login')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (7 + $num_sections) . ", 5, 'password', 'password', 'pswin password')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (7 + $num_sections) . ", 6, 'text', 'proxy_host', 'proxy_host')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (7 + $num_sections) . ", 7, 'text', 'proxy_port', 'proxy_port')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (7 + $num_sections) . ", 8, 'text', 'originator', 'originator')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (7 + $num_sections) . ", 9, 'listbox', 'type', 'Send type')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (7 + $num_sections) . ", 10, 'text', 'receive_url', 'receive_url using SOAP')");
	$db->query("INSERT INTO phpgw_config2_attrib (section_id,id,input_type,name, descr) VALUES (" . (7 + $num_sections) . ", 11, 'text', 'strip_code', 'Strip code from message')");

	$db->query("INSERT INTO phpgw_config2_choice (section_id,attrib_id,id,value) VALUES (" . (1 + $num_sections) . ", 1, 1, 'gnokii')");
	$db->query("INSERT INTO phpgw_config2_choice (section_id,attrib_id,id,value) VALUES (" . (1 + $num_sections) . ", 1, 2, 'clickatell')");
	$db->query("INSERT INTO phpgw_config2_choice (section_id,attrib_id,id,value) VALUES (" . (1 + $num_sections) . ", 1, 3, 'uplink')");
	$db->query("INSERT INTO phpgw_config2_choice (section_id,attrib_id,id,value) VALUES (" . (1 + $num_sections) . ", 1, 4, 'kannel')");
	$db->query("INSERT INTO phpgw_config2_choice (section_id,attrib_id,id,value) VALUES (" . (1 + $num_sections) . ", 1, 5, 'carrot')");
	$db->query("INSERT INTO phpgw_config2_choice (section_id,attrib_id,id,value) VALUES (" . (1 + $num_sections) . ", 1, 6, 'pswin')");
	
	$db->query("INSERT INTO phpgw_config2_choice (section_id,attrib_id,id,value) VALUES (" . (1 + $num_sections) . ", 2, 1, 'gnokii')");
	$db->query("INSERT INTO phpgw_config2_choice (section_id,attrib_id,id,value) VALUES (" . (1 + $num_sections) . ", 2, 2, 'clickatell')");
	$db->query("INSERT INTO phpgw_config2_choice (section_id,attrib_id,id,value) VALUES (" . (1 + $num_sections) . ", 2, 3, 'uplink')");
	$db->query("INSERT INTO phpgw_config2_choice (section_id,attrib_id,id,value) VALUES (" . (1 + $num_sections) . ", 2, 4, 'kannel')");
	$db->query("INSERT INTO phpgw_config2_choice (section_id,attrib_id,id,value) VALUES (" . (1 + $num_sections) . ", 2, 5, 'carrot')");
	$db->query("INSERT INTO phpgw_config2_choice (section_id,attrib_id,id,value) VALUES (" . (1 + $num_sections) . ", 2, 6, 'pswin')");

	$db->query("INSERT INTO phpgw_config2_choice (section_id,attrib_id,id,value) VALUES (" . (6 + $num_sections) . ", 12, 1, 'GET')");
	$db->query("INSERT INTO phpgw_config2_choice (section_id,attrib_id,id,value) VALUES (" . (6 + $num_sections) . ", 12, 2, 'SOAP')");

	$db->query("INSERT INTO phpgw_config2_choice (section_id,attrib_id,id,value) VALUES (" . (7 + $num_sections) . ", 9, 1, 'GET')");
	$db->query("INSERT INTO phpgw_config2_choice (section_id,attrib_id,id,value) VALUES (" . (7 + $num_sections) . ", 9, 2, 'SOAP')");

	$db->query("INSERT INTO phpgw_config2_value (section_id,attrib_id,id,value) VALUES (" . (1 + $num_sections) . ", 1, 1, 'gnokii')");
	$db->query("INSERT INTO phpgw_config2_value (section_id,attrib_id,id,value) VALUES (" . (1 + $num_sections) . ", 2, 1, '99999999')");
	$db->query("INSERT INTO phpgw_config2_value (section_id,attrib_id,id,value) VALUES (" . (2 + $num_sections) . ", 1, 1, '/usr/local')");

	$db->query("INSERT INTO phpgw_config2_value (section_id,attrib_id,id,value) VALUES (" . (3 + $num_sections) . ", 1, 1, '123456')");
	$db->query("INSERT INTO phpgw_config2_value (section_id,attrib_id,id,value) VALUES (" . (3 + $num_sections) . ", 2, 1, 'phpgwsms')");
	$db->query("INSERT INTO phpgw_config2_value (section_id,attrib_id,id,value) VALUES (" . (3 + $num_sections) . ", 3, 1, 'pwd')");
	$db->query("INSERT INTO phpgw_config2_value (section_id,attrib_id,id,value) VALUES (" . (3 + $num_sections) . ", 4, 1, 'phpgwsms')");
	$db->query("INSERT INTO phpgw_config2_value (section_id,attrib_id,id,value) VALUES (" . (3 + $num_sections) . ", 5, 1, 'http://api.clickatell.com/http')");
	$db->query("INSERT INTO phpgw_config2_value (section_id,attrib_id,id,value) VALUES (" . (3 + $num_sections) . ", 6, 1, 'usr/local')");
	$db->query("INSERT INTO phpgw_config2_value (section_id,attrib_id,id,value) VALUES (" . (3 + $num_sections) . ", 7, 1, '10')");

	$db->query("INSERT INTO phpgw_config2_value (section_id,attrib_id,id,value) VALUES (" . (4 + $num_sections) . ", 1, 1, 'http://cpanel.smsrakyat.net')");
	$db->query("INSERT INTO phpgw_config2_value (section_id,attrib_id,id,value) VALUES (" . (4 + $num_sections) . ", 2, 1, 'phpgwsms')");
	$db->query("INSERT INTO phpgw_config2_value (section_id,attrib_id,id,value) VALUES (" . (4 + $num_sections) . ", 3, 1, 'pwd')");

	$db->query("INSERT INTO phpgw_config2_value (section_id,attrib_id,id,value) VALUES (" . (4 + $num_sections) . ", 5, 1, '/usr/local')");

	$db->query("INSERT INTO phpgw_config2_value (section_id,attrib_id,id,value) VALUES (" . (5 + $num_sections) . ", 1, 1, 'phpgwsms')");
	$db->query("INSERT INTO phpgw_config2_value (section_id,attrib_id,id,value) VALUES (" . (5 + $num_sections) . ", 2, 1, 'pwd')");
	$db->query("INSERT INTO phpgw_config2_value (section_id,attrib_id,id,value) VALUES (" . (5 + $num_sections) . ", 3, 1, '92824')");
	$db->query("INSERT INTO phpgw_config2_value (section_id,attrib_id,id,value) VALUES (" . (5 + $num_sections) . ", 4, 1, '127.0.0.1')");
	$db->query("INSERT INTO phpgw_config2_value (section_id,attrib_id,id,value) VALUES (" . (5 + $num_sections) . ", 5, 1, '13131')");
	$db->query("INSERT INTO phpgw_config2_value (section_id,attrib_id,id,value) VALUES (" . (5 + $num_sections) . ", 6, 1, 'http://localhost/~phpgroupware/sms')");
	$db->query("INSERT INTO phpgw_config2_value (section_id,attrib_id,id,value) VALUES (" . (5 + $num_sections) . ", 7, 1, '/usr/local')");


// -- end config

	$db->query("INSERT INTO phpgw_sms_featautoreply (uid, autoreply_code) VALUES (1,'HELP')");

	$db->query("INSERT INTO phpgw_sms_featautoreply_scenario (autoreply_id,autoreply_scenario_param1,autoreply_scenario_param2,autoreply_scenario_param3,autoreply_scenario_param4,autoreply_scenario_param5,autoreply_scenario_param6,autoreply_scenario_param7,autoreply_scenario_result) VALUES (1,'INTERNET','DOWN','','','','','','Please contact sysadmin via phone: +62 21 8613027')");
	$db->query("INSERT INTO phpgw_sms_featautoreply_scenario (autoreply_id,autoreply_scenario_param1,autoreply_scenario_param2,autoreply_scenario_param3,autoreply_scenario_param4,autoreply_scenario_param5,autoreply_scenario_param6,autoreply_scenario_param7,autoreply_scenario_result) VALUES (1,'WEBMAIL','PASSWORD','ERROR','','','','','Please use forgot password link, and follow given instructions')");

	$db->query("INSERT INTO phpgw_sms_featboard (uid,board_code,board_forward_email,board_pref_template) VALUES (1,'PHP','dummy@dummy.org','<font color=black size=-1><b>##SENDER##</b></font><br><font color=black size=-2><i>##DATETIME##</i></font><br><font color=black size=-1>##MESSAGE##</font>')");
//	$db->query("INSERT INTO phpgw_sms_featcommand (uid,command_code,command_exec) VALUES (1,'UPTIME','/home/playsms/public_html/phpgroupware/sms/bin/uptime.sh ##SMSSENDER##')");

	$db->query("INSERT INTO phpgw_sms_featcustom (uid,custom_code,custom_url) VALUES (1,'CURR','http://www.ngoprek.org/currency.php?toeuro=##CUSTOMPARAM##&sender=##SMSSENDER##')");

	$db->query("INSERT INTO phpgw_sms_featpoll (uid,poll_title,poll_code,poll_enable) VALUES (1,'Which do you prefer ?','PREFER',1)");

	$db->query("INSERT INTO phpgw_sms_featpoll_choice (poll_id,choice_title,choice_code) VALUES (1,'Love Without Sex','LWS')");
	$db->query("INSERT INTO phpgw_sms_featpoll_choice (poll_id,choice_title,choice_code) VALUES (1,'Sex Without Love','SWL')");

	$db->query("INSERT INTO phpgw_sms_tblsmsoutgoing ( flag_deleted, uid, p_gateway, p_src, p_dst, p_footer, p_msg, p_datetime, p_status, p_gpid, p_credit, p_sms_type, unicode) VALUES ( 0, 1, 'gnokii', '+628568809027', '+628562283138', ' - anton', 'Hi u there, good morning!!', '2004-12-12 2:39:13', 0, 0, 0, 'text', 0)");
	$db->query("INSERT INTO phpgw_sms_tblsmsoutgoing ( flag_deleted, uid, p_gateway, p_src, p_dst, p_footer, p_msg, p_datetime, p_status, p_gpid, p_credit, p_sms_type, unicode) VALUES ( 0, 1, 'gnokii', '+628568809027', '+628562205510', ' - anton', 'Hi u there, good morning!!', '2004-12-12 2:39:13', 0, 0, 0, 'text', 0)");
	$db->query("INSERT INTO phpgw_sms_tblsmsoutgoing ( flag_deleted, uid, p_gateway, p_src, p_dst, p_footer, p_msg, p_datetime, p_update, p_status, p_gpid, p_credit, p_sms_type, unicode) VALUES ( 0, 1, 'uplink', '+628568809027', '+628568809027', '- anton', 'Hello my dear me, testing testing the message', '2004-12-12 2:54:12', '2004-12-12 2:55:11', 1, 0, 0, 'text', 0)");

	$db->query("INSERT INTO phpgw_sms_tblsmstemplate ( uid, t_title, t_text) VALUES ( 1, 'Good morning', 'Hi u there, good morning!!')");
	$db->query("INSERT INTO phpgw_sms_tblsmstemplate ( uid, t_title, t_text) VALUES ( 1, 'Good night have a sweet dream', 'Hi sweetheart, good night and have a sweet dream :*')");


	$db->query("INSERT INTO phpgw_sms_tblusergroupphonebook ( uid, gp_name, gp_code) VALUES ( 1, 'Friends', 'FR')");

	$db->query("INSERT INTO phpgw_sms_tbluserphonebook ( gpid, uid, p_num, p_desc, p_email) VALUES ( 1, 1, '+628562283138', 'Nero', 'nero@phpgwsms.bogus')");
	$db->query("INSERT INTO phpgw_sms_tbluserphonebook ( gpid, uid, p_num, p_desc, p_email) VALUES ( 1, 1, '+628562205510', 'Sleepless', 'sleepless@somedomain.bogus')");
	$db->query("INSERT INTO phpgw_sms_tbluserphonebook ( gpid, uid, p_num, p_desc, p_email) VALUES ( 1, 1, '+39933993399339', 'Crafted Chees', '')");
	$db->query("INSERT INTO phpgw_sms_tbluserphonebook ( gpid, uid, p_num, p_desc, p_email) VALUES ( 1, 1, '+122334455', 'Morfren', 'morfren@nidyumac.doe')");
	$db->query("INSERT INTO phpgw_sms_tbluserphonebook ( gpid, uid, p_num, p_desc, p_email) VALUES ( 1, 1, '+628568809027', 'My Self', 'anton@ngoprek.org')");

