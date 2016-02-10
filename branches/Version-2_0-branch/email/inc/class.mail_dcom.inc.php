<?php
	/**
	* phpGroupWare - Email - Communication Object includers "magic file"
	*
	* Handles initializing the appropriate class dcom object
	* @author Angelo (Angles) Puglisi <angles@aminvestments.com>
	* @author Mark Peters <skeeter@phpgroupware.org>
	* @copyright Copyright (C) 2001-2002 Angelo Tony Puglisi (Angles)
	* @copyright Copyright (C) 2001-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package email
	* @version $Id$
	* @internal Based on AngleMail http://www.anglemail.org/
	*/


	//$debug_dcom = True;
	$debug_dcom = False;

	$mail_server_type = 'imap';
	if ( isset($GLOBALS['phpgw_info']['user']['preferences']['email']['mail_server_type']) )
	{
		$mail_server_type = strtolower($GLOBALS['phpgw_info']['user']['preferences']['email']['mail_server_type']);
	}


	if ( extension_loaded('imap') && function_exists('imap_open') && !$debug_dcom )
	{
		$imap_builtin = True;
		$sock_fname = '';
		if ($debug_dcom) { echo 'imap builtin extension is available<br />'; }
	}
	else if ( $debug_dcom 
		&& ( strtolower($mail_server_type) == 'pop3'
			|| strtolower($mail_server_type) == 'imap' ) )
	{
		$imap_builtin = False;
		$sock_fname = '_sock';
		if ($debug_dcom) { echo 'DCOM DEBUG: force socket class for $mail_server_type ['.$mail_server_type.']<br />'; }
	}
	else
	{
		$imap_builtin = False;
		$sock_fname = '_sock';
		if ($debug_dcom) { echo 'imap builtin extension NOT available, using socket class<br />'; }
	}

	$inc_basedir = PHPGW_INCLUDE_ROOT . '/email/inc/';
	
	/**
	 * Include dcom base or base_sock
	 */
	require_once "{$inc_basedir}class.mail_dcom_base{$sock_fname}.inc.php";
	if ($debug_dcom) { echo "including: {$inc_basedir}class.mail_dcom_base{$sock_fname}.inc.php<br>\n"; }

	switch ( $mail_server_type )
	{
		case 'pop3':
		case 'pop3s':
			$server_type = 'pop3';
			break;

		case 'nntp':
			$type = 'nntp';

		case 'imap':
		case 'imaps':
		default:
			$server_type = 'imap';
	}

	require_once "{$inc_basedir}class.mail_dcom_{$server_type}{$sock_fname}.inc.php";
	if ($debug_dcom) { echo "including: '{$inc_basedir}class.mail_dcom_{$server_type}{$sock_fname}.inc.php'<br>"; }

