<?php
	/**************************************************************************\
	* phpGroupWare - Registration                                              *
	* http://www.phpgroupware.org                                              *
	* This application written by Joseph Engo <jengo@phpgroupware.org>         *
	* --------------------------------------------                             *
	* Funding for this program was provided by http://www.checkwithmom.com     *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	/*
	** This program is non-standard, we will create and manage our sessions manually.
	** We don't want users to be kicked out half way through, and we really don't need a true
	** session for it.
	*/

	$GLOBALS['sessionid'] = get_var('sessionid',Array('COOKIE','GET'));

	// Note: This is current not a drop in install, it requires some manual installation
	//       Take a look at the README file
	$domain         = 'default';
	$template_set   = 'checkwithmom';

	if ($menuaction)
	{
		list($app,$class,$method) = explode('.',$menuaction);
		if (! $app || ! $class || ! $method)
		{
			$invaild_data = True;
		}
	}
	else
	{
		$app = 'registration';
		$invaild_data = True;
	}

	$phpgw_info['flags'] = array(
		'noheader'   => True,
		'nonavbar'   => True,
		'noapi'      => True,
		'currentapp' => $app
	);
	include('../header.inc.php');

	function CreateObject($classname, $constructor_param = '')
	{
		global $phpgw, $phpgw_info, $phpgw_domain;
		$classpart = explode (".", $classname);
		$appname = $classpart[0];
		$classname = $classpart[1];
		if (!isset($phpgw_info['flags']['included_classes'][$classname])
		|| !$phpgw_info['flags']['included_classes'][$classname])
		{
			$phpgw_info['flags']['included_classes'][$classname] = True;   
			include(PHPGW_INCLUDE_ROOT.'/'.$appname.'/inc/class.'.$classname.'.inc.php');
		}
		if ($constructor_param == '')
		{
			$obj = new $classname;
		}
		else
		{
			$obj = new $classname($constructor_param);
		}
		return $obj;
	}

	/*!
	@function lang
	@abstract function to deal with multilanguage support
	*/
	function lang($key, $m1='', $m2='', $m3='', $m4='', $m5='', $m6='', $m7='', $m8='', $m9='', $m10='') 
	{
		global $phpgw;
		$vars  = array($m1,$m2,$m3,$m4,$m5,$m6,$m7,$m8,$m9,$m10);
		$value = $phpgw->translation->translate($key,$vars);
		return $value;
	}

	function get_account_id($account_id = '',$default_id = '')
	{
		global $phpgw, $phpgw_info;

		if (gettype($account_id) == 'integer')
		{
			return $account_id;
		}
		elseif ($account_id == '')
		{
			if ($default_id == '')
			{
				return $phpgw_info['user']['account_id'];
			}
			elseif (gettype($default_id) == 'string')
			{
				return $phpgw->accounts->name2id($default_id);
			}
			return intval($default_id);
		}
		elseif (gettype($account_id) == 'string')
		{
			if($phpgw->accounts->exists(intval($account_id)) == True)
			{
				return intval($account_id);
			}
			else
			{
				return $phpgw->accounts->name2id($account_id);
			}
		}
	}

	$phpgw_info['server'] = $phpgw_domain[$domain];
	$phpgw                = createobject('phpgwapi.phpgw');
	$phpgw->db            = createobject('phpgwapi.db');
	$phpgw->db->Host      = $phpgw_info['server']['db_host'];
	$phpgw->db->Type      = $phpgw_info['server']['db_type'];
	$phpgw->db->Database  = $phpgw_info['server']['db_name'];
	$phpgw->db->User      = $phpgw_info['server']['db_user'];
	$phpgw->db->Password  = $phpgw_info['server']['db_pass'];

	/* Fill phpgw_info["server"] array */
	$phpgw->db->query("select * from phpgw_config WHERE config_app='phpgwapi'",__LINE__,__FILE__);
	while ($phpgw->db->next_record())
	{
		$phpgw_info['server'][$phpgw->db->f('config_name')] = stripslashes($phpgw->db->f('config_value'));
	}
	$phpgw_info['server']['template_set'] = $template_set;

	$phpgw->common        = createobject('phpgwapi.common');
	$phpgw->auth          = createobject('phpgwapi.auth');
	$phpgw->accounts      = createobject('phpgwapi.accounts');
	$phpgw->acl           = createobject('phpgwapi.acl');
	$phpgw->preferences   = createobject('phpgwapi.preferences');
	$phpgw->applications  = createobject('phpgwapi.applications');
	$phpgw->hooks         = createobject('phpgwapi.hooks');
	$phpgw->session       = createobject('phpgwapi.sessions');

	$phpgw->common->key  = md5($GLOBALS['kp3'] . $GLOBALS['sessionid'] . $phpgw_info['server']['encryptkey']);
	$phpgw->common->iv   = $phpgw_info['server']['mcrypt_iv'];

	$cryptovars[0] = $phpgw->common->key;
	$cryptovars[1] = $phpgw->common->iv;
	$phpgw->crypto = createobject('phpgwapi.crypto', $cryptovars);

	define('PHPGW_APP_ROOT', $phpgw->common->get_app_dir());
	define('PHPGW_APP_INC', $phpgw->common->get_inc_dir());
	define('PHPGW_APP_TPL', $phpgw->common->get_tpl_dir());
	define('PHPGW_IMAGES', $phpgw->common->get_image_path());
	define('PHPGW_IMAGES_DIR', $phpgw->common->get_image_dir());

	$phpgw->template      = createobject('phpgwapi.Template',PHPGW_APP_TPL);
	$phpgw->translation   = createobject('phpgwapi.translation');

	$c = createobject('phpgwapi.config','registration');
	$c->read_repository();
	$config = $c->config_data;

	if (! $sessionid)
	{
		$sessionid = $phpgw->session->create($config['anonymous_user'] . '@' . $domain,$config['anonymous_pass'],'text');
	}
	else
	{
		if (! $phpgw->session->verify())
		{
			// Lets hope this works
			$sessionid = $phpgw->session->create($config['anonymous_user'] . '@' . $domain,$config['anonymous_pass'],'text');
		}
	}

	if ($app && $class)
	{
		$obj = createobject(sprintf('%s.%s',$app,$class));

		if ((is_array($obj->public_functions) && $obj->public_functions[$method]) && ! $invalid_data)
		{
			eval("\$obj->$method();");
		}
	}
	else
	{
		$_obj = createobject('registration.uireg');
		$_obj->step1();
	}
