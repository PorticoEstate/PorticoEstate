<?php
	/**************************************************************************\
	* phpGroupWare - Interserver XML-RPC/SOAP Test app                         *
	* http://www.phpgroupware.org                                              *
	* This file written by Miles Lott <milosch@phpgroupware.org                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	$GLOBALS['phpgw_info'] = array();
	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'xmlrpc'
	);

	include('../header.inc.php');

	$server_id  = $HTTP_POST_VARS['server_id'];
	$xsessionid = $HTTP_POST_VARS['xsessionid'];
	$xkp3       = $HTTP_POST_VARS['xkp3'];

	$is = CreateObject('phpgwapi.interserver',intval($server_id));

	function applist()
	{
		$select  = "\n" .'<select name="xappname" >' . "\n";
		if($default)
		{
			$select .= '<option value="">' . lang('Please Select') . '</option>'."\n";
		}

		$GLOBALS['phpgw']->db->query("SELECT * FROM phpgw_applications WHERE app_enabled<3",__LINE__,__FILE__);
		if($GLOBALS['phpgw']->db->num_rows())
		{
			while ($GLOBALS['phpgw']->db->next_record())
			{
				$select .= '<option value="' . $GLOBALS['phpgw']->db->f('app_name') . '"';
				if($GLOBALS['phpgw']->db->f('app_name') == $GLOBALS['HTTP_POST_VARS']['xappname'])
				{
					$select .= ' selected';
				}
				$select .= '>' . $GLOBALS['phpgw']->db->f('app_name') . '</option>'."\n";
			}
		}
		$select .= '</select>'."\n";
		$select .= '<noscript><input type="submit" name="' . $name . '_select" value="True"></noscript>' . "\n";

		return $select;
	}

	if(!$xsessionid && !$xusername)
	{
		$xserver_name = $GLOBALS['HTTP_HOST'];
	}
	else
	{
		$xserver_name = $HTTP_POST_VARS['xserver_name'];
	}

	/* _debug_array($is->server); */
	if($HTTP_POST_VARS['login'])
	{
		if($HTTP_POST_VARS['xserver'])
		{
			$is->send(
				'system.login', array(
					'server_name' => $HTTP_POST_VARS['xserver_name'],
					'username'    => $HTTP_POST_VARS['xusername'],
					'password'    => $HTTP_POST_VARS['xpassword']
				),
				$is->server['server_url']
			);
		}
		else
		{
			$is->send(
				'system.login', array(
					'domain'      => $HTTP_POST_VARS['xserver_name'],
					'username'    => $HTTP_POST_VARS['xusername'],
					'password'    => $HTTP_POST_VARS['xpassword']
				),
				$is->server['server_url']
			);
		}
		/* _debug_array($is->result); */
		$xserver_name = $is->result['domain'];
		$xsessionid = $is->result['sessionid'];
		$xkp3       = $is->result['kp3'];
	}
	elseif($HTTP_POST_VARS['logout'])
	{
		$is->send(
			'system.logout', array(
				'sessionid' => $xsessionid,
				'kp3'       => $xkp3
			),
			$is->server['server_url']
		);
		$xsessionid = '';
		$xkp3       = '';
	}
	elseif($HTTP_POST_VARS['methods'])
	{
		if(!$server_id)
		{
			echo '<br>Please select a server...';
		}

		$is->sessionid = $xsessionid;
		$is->kp3 = $xkp3;

		if($xsessionid & $HTTP_POST_VARS['xappname'])
		{
			$method_str = $HTTP_POST_VARS['xappname'] . '.bo' . $HTTP_POST_VARS['xappname'] . '.list_methods';
			$server_id ? $is->send($method_str,'xmlrpc',$is->server['server_url']) : '';
		}
		else
		{
			$server_id ? $is->send('system.listMethods','',$is->server['server_url']) : '';
		}
	}
	elseif($HTTP_POST_VARS['apps'])
	{
		$is->sessionid = $xsessionid;
		$is->kp3 = $xkp3;

		$is->send('system.list_apps','',$is->server['server_url']);
	}
	elseif($HTTP_POST_VARS['users'])
	{
		$is->sessionid = $xsessionid;
		$is->kp3 = $xkp3;

		$is->send('system.listUsers','',$is->server['server_url']);
	}
	elseif($HTTP_POST_VARS['bogus'])
	{
		$is->sessionid = $xsessionid;
		$is->kp3 = $xkp3;

		$is->send('system.bogus','',$is->server['server_url']);
	}
	elseif($HTTP_POST_VARS['addressbook'])
	{
		$is->sessionid = $xsessionid;
		$is->kp3 = $xkp3;
		/* TODO - Adjust the values below as desired */
		$is->send(
			'service.contacts.read_list',array(
				'start' => 1,
				'limit' => 5,
				'fields' => array(
					'n_given'  => 'n_given',
					'n_family' => 'n_family'
				),
				'query'  => '',
				'filter' => '',
				'sort'   => '',
				'order'  => ''
			),
			$is->server['server_url']
		);
	}
	elseif($HTTP_POST_VARS['calendar'])
	{
		$is->sessionid = $xsessionid;
		$is->kp3 = $xkp3;
		/* TODO - Adjust the values below as desired */
		$is->send(
			'calendar.bocalendar.store_to_cache', array(
				'syear' => '2001', 
				'smonth' => '08',
				'sday' => '03',
				'eyear' => '2001',
				'emonth' => '08',
				'eday' => '06'
			),
			$is->server['server_url']
		);
	}
	elseif($HTTP_POST_VARS['appbyid'])
	{
		$param = Array(
			'server'    => $server_id,
			'sessionid' => $xsessionid,
			'kp3'       => $xkp3
		);

		echo ExecMethod('phpgwapi.app_registry.request_packaged_app','etemplate',3,$param);
//		echo ExecMethod('phpgwapi.app_registry.request_packaged_app','nntp',3,$param);
//		echo ExecMethod('phpgwapi.app_registry.request_newer_applist','',3,$param);
//		echo ExecMethod('phpgwapi.app_registry.request_appbyid',10,3,$param);
//		echo ExecMethod('phpgwapi.app_registry.request_appbyname','infolog',3,$param);
//		echo _debug_array(ExecMethod('phpgwapi.app_registry.request_appbyname','infolog'));
		/* TODO - Adjust the values below as desired */
//		$is->send('phpgwapi.app_registry.get_appbyid',1,$is->server['server_url']);
	}

	$GLOBALS['phpgw']->template->set_file('interserv','interserv.tpl');

	$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/xmlrpc/interserv.php'));
	$GLOBALS['phpgw']->template->set_var('lang_title',lang('phpGroupWare XML-RPC/SOAP Client<->Server and Server<->Server Test (SOAP pending...)'));
	$GLOBALS['phpgw']->template->set_var('lang_select_target',lang('Select target server'));
	$GLOBALS['phpgw']->template->set_var('lang_st_note',lang('Configure using admin - Peer servers'));
	$GLOBALS['phpgw']->template->set_var('lang_this_servername',lang('Servername/Domain'));
	$GLOBALS['phpgw']->template->set_var('lang_sd_note',lang('(optional: set domain for user/client login, required: set this servername for server login)'));
	$GLOBALS['phpgw']->template->set_var('lang_addressbook',lang('Addressbook test'));
	$GLOBALS['phpgw']->template->set_var('lang_calendar',lang('Calendar test'));
	$GLOBALS['phpgw']->template->set_var('lang_login',lang('Login'));
	$GLOBALS['phpgw']->template->set_var('lang_logout',lang('Logout'));
	$GLOBALS['phpgw']->template->set_var('lang_list',lang('List'));
	$GLOBALS['phpgw']->template->set_var('lang_apps',lang('Apps'));
	$GLOBALS['phpgw']->template->set_var('lang_bogus',lang('Bogus Request'));
	$GLOBALS['phpgw']->template->set_var('lang_appbyid',lang('App Registry by ID'));
	$GLOBALS['phpgw']->template->set_var('lang_users',lang('Users'));
	$GLOBALS['phpgw']->template->set_var('lang_methods',lang('Methods'));
	$GLOBALS['phpgw']->template->set_var('lang_username',lang('Username'));
	$GLOBALS['phpgw']->template->set_var('lang_password',lang('Password'));
	$GLOBALS['phpgw']->template->set_var('lang_session',lang('Assigned sessionid'));
	$GLOBALS['phpgw']->template->set_var('lang_kp3',lang('Assigned kp3'));
	$GLOBALS['phpgw']->template->set_var('login_type',lang('Server<->Server'));
	$GLOBALS['phpgw']->template->set_var('note',lang('NOTE: listapps and listusers are disabled by default in xml_functions.php') . '.');

	$GLOBALS['phpgw']->template->set_var('xserver',$HTTP_POST_VARS['xserver'] ? ' checked' : '');
	$GLOBALS['phpgw']->template->set_var('xsessionid',$xsessionid ? $xsessionid : lang('none'));
	$GLOBALS['phpgw']->template->set_var('xkp3',$xkp3 ? $xkp3 : lang('none'));
	$GLOBALS['phpgw']->template->set_var('xusername',$xusername);
	$GLOBALS['phpgw']->template->set_var('xpassword',$xpassword);
	$GLOBALS['phpgw']->template->set_var('xserver_name',$xserver_name);
	$GLOBALS['phpgw']->template->set_var('server_list',$is->formatted_list($server_id));
	$GLOBALS['phpgw']->template->set_var('method_type',(($xsessionid == lang('none')) || !$xsessionid) ? lang('System') . ' ' : lang('App') . ' ');
	$GLOBALS['phpgw']->template->set_var('applist',(($xsessionid == lang('none')) || !$xsessionid) ? '' : 'for&nbsp;' . applist());

	$GLOBALS['phpgw']->template->pfp('out','interserv');

	$GLOBALS['phpgw']->common->phpgw_footer();
?>
