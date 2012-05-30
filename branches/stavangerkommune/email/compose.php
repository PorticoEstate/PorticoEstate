<?php
	/**
	* EMail
	*
	* @author Mark C3ushman <mark@cushman.net>
	* @author Angles <angles@phpgroupware.org>
	* @copyright Copyright (C) xxxx Mark C3ushman
	* @copyright Copyright (C) xxxx Angles
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package email
	* @version $Id$
	* @internal Based on Aeromail http://the.cushman.net/
	*/
	
	Header('Cache-Control: no-cache');
	Header('Pragma: no-cache');
	Header('Expires: Sat, Jan 01 2000 01:01:01 GMT');
  
	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'email',
		'noheader' => True,
		'nofooter' => True,
		'nonavbar' => True,
		'noappheader' => True,
		'noappfooter' => True
	);
	
	/**
	* Include phpgroupware header
	*/
	include('../header.inc.php');
	
	// we need a msg object BUT NO LOGIN IS NEEDED
	$my_msg_bootstrap = '';
	$my_msg_bootstrap = CreateObject("email.msg_bootstrap");
	$my_msg_bootstrap->set_do_login(False);
	$my_msg_bootstrap->ensure_mail_msg_exists('email: compose.php', 0);
	
	// time limit should be controlled elsewhere
	//@set_time_limit(0);
	$pass_the_ball_uri = array();
	
	if ($GLOBALS['phpgw']->msg->get_isset_arg('fldball'))
	{
		$my_fldball = $GLOBALS['phpgw']->msg->get_arg_value('fldball');
		$pass_the_ball_uri = array('fldball[folder]'=>$my_fldball['folder'],
						'fldball[acctnum]'=>$my_fldball['acctnum']);
	}
	elseif ($GLOBALS['phpgw']->msg->get_isset_arg('msgball'))
	{
		$my_msgball = $GLOBALS['phpgw']->msg->get_arg_value('msgball');
		$pass_the_ball_uri = array('msgball[folder]'=>$my_msgball['folder'],
						'msgball[acctnum]'=>$my_msgball['acctnum'],
						'msgball[msgnum]'=>$my_msgball['msgnum']);
	}
	else
	{
		$pass_the_ball_uri = array('fldball[folder]'=>'INBOX',
						'fldball[acctnum]'=>'0');
	}
	
	$GLOBALS['phpgw']->redirect_link(
				'/index.php',array(
				'menuaction'=>'email.uicompose.compose',
				'to'=>$to,
				'cc'=>$cc,
				'bcc'=>$bcc,
				'subject'=>$subject,
				'body'=>$body,
				'personal'=>$personal,
				'sort'=>$sort,
				'order'=>$order,
				'start'=>$start)
				+$pass_the_ball_uri
				);
	
	if (is_object($GLOBALS['phpgw']->msg))
	{
		$terminate = True;
	}
	else
	{
		$terminate = False;
	}
	
	if ($terminate == True)
	{
		// close down ALL mailserver streams
		$GLOBALS['phpgw']->msg->end_request();
		// destroy the object
		$GLOBALS['phpgw']->msg = '';
		unset($GLOBALS['phpgw']->msg);
	}
	// shut down this transaction
	$GLOBALS['phpgw']->common->phpgw_exit(False);

?>
