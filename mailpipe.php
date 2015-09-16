#!/usr/bin/php
<?php
/**
 * phpGroupWare generic mail pipe interface
 *
 * This script allows you to add add/update data via email
 * @author Dave Hall skwashd at phpgroupware org
 * @copyright (c) 2006-2007 Free Software Foundation Inc
 */
	
	/* anonymous user */
	$user = 'demo';
	/* anonymous password */
	$pass = 'guest';

	/*** DO NOT EDIT BELOW THIS LINE ***/

	ini_set('html_errors', false);
	ini_set('error_append_string', "\n");
	
	if ( $_SERVER['argc'] != 3 
		|| !isset($_SERVER['argv'][1]) || $_SERVER['argv'][1] != '--email'
		|| !isset($_SERVER['argv'][2]) || !strlen($_SERVER['argv'][2]) )
	{
		fwrite(STDERR, "ERROR: phpGroupWare mailpipe called improperly!\n");
		fwrite(STDERR, "Usage: mailpipe.php --email user@domain.com\n");
		fwrite(STDERR, "Exiting.\n");
		fwrite(STDERR, print_r($_SERVER['argv'], true) . "\nExiting.\n");
		exit(1);
	}
	$email_to = $_SERVER['argv'][2];
	
	// Hack around error in session class, need a flag to disable cookies on a per call basis ? - skwashd Jan07
	$_SERVER['REMOTE_ADDR'] =& $_SERVER['HTTP_HOST'];
	$_SERVER['HTTP_HOST'] = '0.0.0.0';
	
	$GLOBALS['phpgw_info']['flags'] = array
	(
		'disable_Template_class'	=> true,
		'login'						=> true,
		'currentapp'				=> 'login',
		'noheader'					=> true
	);
	$phpgw_root = dirname(__FILE__);
	include_once($phpgw_root . '/header.inc.php');
	error_reporting(E_ALL);
	ini_set('display_errors', true);
	
	$msg = createObject('phpgwapi.mail2array');
	$msg->parse_input(file_get_contents('php://stdin'));
	$msg->fetch_useful_headers();

	$login = '';
	if ( $msg->from_email )
	{
		$login = sender2phpgw_lid($msg->from_email);
	}

	if ( !$login )
	{
		fwrite(STDERR, "ERROR: Sender's email address [{$msg->from_email}] does not match any user's account, rejecting message\n");
		$error = true;
	}
	else
	{
		// This is a hack and the data is a little dodgy, but it works, so we have a more accurate source IP address
		$_SERVER['HTTP_HOST'] = $msg->ip;

		if ( $GLOBALS['phpgw']->session->create($login, '', true) ) //assume email is legit - possible DDoS vector ?
		{
			$mail_handlers = createObject('phpgwapi.mail_handlers');
			$handler = $mail_handlers->get_handler($email_to);
			
			if ( count($handler) )
			{
				$msg->handler_id = $handler['handler_id'];
				$error = execMethod($handler['handler'], $msg);
			}
			else
			{
				fwrite(STDERR, "ERROR: No handler found for $email_to, rejecting message\n");
				$error = true;
			}

			$GLOBALS['phpgw']->session->destroy($GLOBALS['phpgw_info']['user']['sessionid'], $GLOBALS['phpgw']->session->kp3);
		}
		else
		{
			fwrite(STDERR, "ERROR: Unable to create session for {$login} (reason: {$GLOBALS['phpgw']->session->cd_reason})\n");
			$error = true;
		}
	}

	if ( $error )
	{
		exit(1);
	}
	exit;

	function sender2phpgw_lid($sender_email)
	{
		$contacts = createObject('phpgwapi.contacts');
		$contact_id = $contacts->search(array('comm_data'), $sender_email);
		unset($contacts);
		if ( is_array($contact_id) && count($contact_id) )
		{
			$userid = $GLOBALS['phpgw']->accounts->search_person($contact_id[0]);
			if ( $userid )
			{
				return $GLOBALS['phpgw']->accounts->id2lid($userid);
			}
		}
		return 0;
	}
