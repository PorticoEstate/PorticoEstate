<?php
	/**************************************************************************\
	* phpGroupWare - Communik8r                                                *
	* http://www.phpgroupware.org                                              *
	* Written by Dave Hall skwashd at phpgroupware.org                         *
	* Copyright 2005, Free Software Foundation Inc                             *
	* -----------------------------------------------                          *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/
	/* $Id: index.php,v 1.1.1.1 2005/08/23 05:01:21 skwashd Exp $ */

	$GLOBALS['phpgw_info']['flags'] = array
	(
		'currentapp'		=> 'communik8r',
		'noheader'		=> True,
		'nonavbar'		=> True,
		//'nocachecontrol'	=> True
	);
	include('../header.inc.php');

	@ini_set('memory_limit', 63554432); //32M seems to be enough for huge spam folders
	@set_time_limit(0); //no limit

	@ini_set('log_errors', True);
//	@ini_set('display_errors', False);
	set_error_handler('handle_error');

	/*
	$prefs = createObject('phpgwapi.preferences');
	$prefs->read();
	unset($prefs->user['communik8r']);
	$prefs->save_repository();
	exit;
	*/

	$GLOBALS['phpgw_info']['flags']['mailparse'] = False;
	if ( extension_loaded('mailparse') )
	{
		$GLOBALS['phpgw_info']['flags']['mailparse'] = True;
	}
	
	$server_url_parts = parse_url($GLOBALS['phpgw_info']['server']['webserver_url'] . '/communik8r' );
	$uri_parts = parse_url( substr( $_SERVER['REQUEST_URI'], strlen( $server_url_parts['path'] ) ) );
	$uri = $uri_parts['path'];

	$uri_parts = explode('/', $uri);

	$section		= phpgw::get_var('section', 'string', 'REQUEST', 'index');

//_debug_array($section);
//	switch ($uri_parts[1])//first is empty!
	switch ($section)
	{
		case 'accounts':	
			$action		= phpgw::get_var('action', 'string');
			$type		= phpgw::get_var('section', 'type');
			ExecMethod('communik8r.boaccounts.rest', array('action' => $action, 'type' => $type));
			break;

		case 'attachments':
			ExecMethod('communik8r.boattachments.rest', $uri_parts);
			break;

		case 'buttons':
			ExecMethod('communik8r.bobase.buttons', $uri_parts);
			break;

		case 'contacts':
			ExecMethod('communik8r.bocontacts.rest', $uri_parts);
			break;

		case 'email':
			$action		= phpgw::get_var('action', 'string');
			$acct_id	= phpgw::get_var('acct_id', 'int');
			$mbox_name = phpgw::get_var('mbox_name', 'string');
			ExecMethod('communik8r.boemail.rest', array('action' => $action, 'acct_id' => $acct_id, 'mbox_name' => $mbox_name));
			break;

		case 'help':
			die('Insert Help Here!');
			break;

		case 'index':
		case 'index.php':
			ExecMethod('communik8r.uibase.index');
			break;

		case 'jabber':
			echo 'Jabber called'; //does nothing else atm
			//jabber_request($uri_parts);
			break;

		case 'menu':
			ExecMethod('communik8r.bobase.menu', $uri_parts);
			break;

		case 'newsfeed':
			echo 'news feed request'; //coming 1 day :P
			//newsfeed_request($uri_parts);
			break;

		case 'ping':
			echo '<pong>ping</pong>';
			exit;

		case 'new':
		case 'forward':
		case 'reply':
		case 'reply_to_all':
			ExecMethod('communik8r.uibase.compose', $uri_parts);
			break;

		case 'settings':
			ExecMethod('communik8r.bobase.settings');
			break;

		case 'start':
			ExecMethod('communik8r.bobase.start');
			break;

		case 'xsl':
			ExecMethod('communik8r.bobase.xsl', $uri_parts);
			break;

		default:
			header('HTTP/1.0 400 Bad Request');
			exit;
	}

	function jabber_request($uri_parts)
	{
		echo '<pre>'; print_r($uri_parts); echo '</pre>';
	}

	function newsfeed_request($uri_parts)
	{
		echo '<pre>'; print_r($uri_parts); echo '</pre>';
	}

	function handle_error($errno, $errstr, $errfile, $errline)
	{
		$errfile_name = '/path/to/phpgroupware' . substr($errfile, strpos($errfile, PHPGW_SERVER_ROOT) + strlen(PHPGW_SERVER_ROOT));
		$errno_str = errno2string($errno);
		switch($errno)
		{
			case E_NOTICE:
			case E_WARNING:
				//ignore me
				break;
			case E_USER_NOTICE:
				//error_log("DEBUG: {$errno_str}: {$errstr} in {$errfile_name} at {$errline}, be alert, not alarmed!");
				break;
			
			case E_USER_ERROR:
			case E_USER_WARNING:
				error_log("{$errno_str}: {$errstr} in {$errfile_name} at {$errline}, please report it at bugs.phpgroupware.org!");
				header('HTTP/1.0 500 Internal Server Error');
				exit;
			default: //pass thru
				error_log("{$errno_str}: {$errstr} in {$errfile_name} at {$errline}");
				break;
		}
	}

	function errno2string($errno)
	{
		$errors = array
				(
					E_ERROR		=> 'ERROR',
					E_WARNING	=> 'WARNING',
					E_PARSE		=> 'PARSING ERROR',
					E_NOTICE	=> 'NOTICE',
					E_CORE_ERROR	=> 'CORE ERROR',
					E_CORE_WARNING	=> 'CORE WARNING',
					E_COMPILE_ERROR	=> 'COMPILE ERROR',
					E_COMPILE_WARNING => 'COMPILE WARNING',
					E_USER_ERROR	=> 'PHPGW ERROR',
					E_USER_WARNING	=> 'PHPGW WARNING',
					E_USER_NOTICE	=> 'PHPGW NOTICE',
					E_STRICT	=> 'STRICT'
               			);

		return $errors[$errno];
	}
?>
