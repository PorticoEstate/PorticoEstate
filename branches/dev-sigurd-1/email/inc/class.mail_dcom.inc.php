<?php
	/**
	* EMail - php IMAP SO access object constructor
	*
	* Handles initializing the appropriate class dcom object
	* @author Angelo (Angles) Puglisi <angles@aminvestments.com>
	* @author Mark Peters <skeeter@phpgroupware.org>
	* @copyright Copyright (C) 2001-2002 Angelo Tony Puglisi (Angles)
	* @copyright Copyright (C) 2001-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package email
	* @version $Id: class.mail_dcom.inc.php 17706 2006-12-17 11:21:02Z sigurdne $
	* @internal Based on AngleMail http://www.anglemail.org/
	*/


	//$debug_dcom = True;
	$debug_dcom = False;

	
	/*!
	Implements communication with the mail server. (not related to anything else called "dcom")

	PHP may or may not have IMAP extension built in. This class will AUTO-DETECT that and 
	load either (a) a class which mostly wraps the available builtin functions, or (b) a TOTAL REPLACEMENT 
	to PHPs builtin imap extension. Currently, the POP3 socket class is fully implemented, basically a re-write 
	of the UWash c-client, because all the logic contained in an imap server had to be emulated locally here, 
	since a pop server provides only the most basic information, the rest must be deduced.
	NOTE: the imap socket class is NOT COMPLETE!

	@access private
	*/
	
	/* -----  any constructor params? ---- */
	if (isset($p1)
	&& ($p1)
	&& ( (stristr($p1, 'imap') || stristr($p1, 'pop3') || stristr($p1, 'nntp')) )
	)
	{
		$mail_server_type = $p1;
		if ($debug_dcom) { echo 'DCOM DEBUG: found class feed arg $p1 ['.serialize($p1).']<br />'; }
		//{ echo 'DCOM DEBUG: found class feed arg $p1 ['.serialize($p1).']<br />'; }
	}
	else
	{
		if ($debug_dcom) { echo 'DCOM DEBUG: did NOT find class feed arg $p1 ['.serialize($p1).']<br />'; }
		//{ echo 'DCOM DEBUG: did NOT find class feed arg $p1 ['.serialize($p1).']<br />'; }
		$mail_server_type = (isset($GLOBALS['phpgw_info']['user']['preferences']['email']['mail_server_type'])?$GLOBALS['phpgw_info']['user']['preferences']['email']['mail_server_type']:'');
	}

	/* -----  is IMAP compiled into PHP */
	//if (($debug_dcom == True)
	//&& ((stristr($mail_server_type, 'pop'))
	//	|| (stristr($mail_server_type, 'imap')))
	//)
	if (($debug_dcom == True)
	&& ((strtolower($mail_server_type) == 'pop3')
		|| (strtolower($mail_server_type) == 'imap'))
	)
	{
		$imap_builtin = False;
		$sock_fname = '_sock';
		if ($debug_dcom) { echo 'DCOM DEBUG: force socket class for $mail_server_type ['.$mail_server_type.']<br />'; }
	}
	elseif (extension_loaded('imap') && function_exists('imap_open'))
	{
		$imap_builtin = True;
		$sock_fname = '';
		if ($debug_dcom) { echo 'imap builtin extension is available<br />'; }
	}
	else
	{
		$imap_builtin = False;
		$sock_fname = '_sock';
		if ($debug_dcom) { echo 'imap builtin extension NOT available, using socket class<br />'; }
	}

	/* -----  include SOCKET or PHP-BUILTIN classes as necessary */
	if ($imap_builtin == False)
	{
		CreateObject('phpgwapi.network');
		if ($debug_dcom) { echo 'created phpgwapi network class used with sockets<br />'; }
	}

	//CreateObject('email.mail_dcom_base'.$sock_fname);
	
	/**
	 * Include dcom base or base_sock
	 */
	include(PHPGW_INCLUDE_ROOT.'/email/inc/class.mail_dcom_base'.$sock_fname.'.inc.php');

	if ($debug_dcom) { echo 'including :'.PHPGW_INCLUDE_ROOT.'/email/inc/class.mail_dcom_base'.$sock_fname.'.inc.php<br />'; }

	if (($mail_server_type == 'imap')
	|| ($mail_server_type == 'imaps'))
        {
		include(PHPGW_INCLUDE_ROOT.'/email/inc/class.mail_dcom_imap'.$sock_fname.'.inc.php');
		if ($debug_dcom) { echo 'including :'.PHPGW_INCLUDE_ROOT.'/email/inc/class.mail_dcom_imap'.$sock_fname.'.inc.php<br />'; }
	}
	elseif (($mail_server_type == 'pop3')
	|| ($mail_server_type == 'pop3s'))
	{
		include(PHPGW_INCLUDE_ROOT.'/email/inc/class.mail_dcom_pop3'.$sock_fname.'.inc.php');
		if ($debug_dcom) { echo 'including :'.PHPGW_INCLUDE_ROOT.'/email/inc/class.mail_dcom_pop3'.$sock_fname.'.inc.php<br />'; }
	}
	elseif ($mail_server_type == 'nntp')
	{
		include(PHPGW_INCLUDE_ROOT.'/email/inc/class.mail_dcom_nntp'.$sock_fname.'.inc.php');
		if ($debug_dcom) { echo 'including :'.PHPGW_INCLUDE_ROOT.'/email/inc/class.mail_dcom_nntp'.$sock_fname.'.inc.php<br />'; }
	}
	elseif ((isset($mail_server_type))
	&& ($mail_server_type != ''))
	{
		/* educated guess based on info being available: */
		include(PHPGW_INCLUDE_ROOT.'/email/inc/class.mail_dcom_'.$GLOBALS['phpgw_info']['user']['preferences']['email']['mail_server_type'].$sock_fname.'.inc.php');
		if ($debug_dcom) { echo 'Educated Guess: including :'.PHPGW_INCLUDE_ROOT.'/email/inc/class.mail_dcom_'.$GLOBALS['phpgw_info']['user']['preferences']['email']['mail_server_type'].$sock_fname.'.inc.php<br />'; }
  	}
	else
	{
		/* DEFAULT FALL BACK: */
		include(PHPGW_INCLUDE_ROOT.'/email/inc/class.mail_dcom_imap.inc.php');
		if ($debug_dcom) { echo 'NO INFO DEFAULT: including :'.PHPGW_INCLUDE_ROOT.'/email/inc/class.mail_dcom_imap.inc.php<br />'; }
	}
?>
