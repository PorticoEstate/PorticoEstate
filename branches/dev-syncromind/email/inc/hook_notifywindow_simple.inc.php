<?php
	/**
	* EMail - Simple notify window hook
	*
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package email
	* @subpackage hooks
	* @version $Id$
	*/

	$d1 = strtolower(substr(APP_INC,0,3));
	if($d1 == 'htt' || $d1 == 'ftp' )
	{
		echo "Failed attempt to break in via an old Security Hole!<br />\n";
		$GLOBALS['phpgw']->common->phpgw_exit();
	}
	unset($d1);

	if (($GLOBALS['phpgw_info']["user"]["preferences"]["email"]["mainscreen_showmail"])
	&& (isset($GLOBALS['phpgw_info']["user"]["apps"]["email"]))
	&& ($GLOBALS['phpgw_info']["user"]["apps"]["email"]))
	{
		$my_msg_bootstrap = '';
		$my_msg_bootstrap = CreateObject("email.msg_bootstrap");
		$my_msg_bootstrap->ensure_mail_msg_exists('email.hook_notifywindow_simple', 0);
		/*  // this is the structure you will get
		  $inbox_data['is_imap'] boolean - pop3 server do not know what is "new" or not
		  $inbox_data['folder_checked'] string - the folder checked, as processed by the msg class
		  $inbox_data['alert_string'] string - what to show the user about this inbox check
		  $inbox_data['number_new'] integer - for IMAP is number "unseen"; for pop3 is number messages
		  $inbox_data['number_all'] integer - for IMAP and pop3 is total number messages in that inbox
		*/
		$inbox_data = Array();
		$inbox_data = $GLOBALS['phpgw']->msg->new_message_check();		
		if ($inbox_data['is_imap'])
		{
			if ($inbox_data['number_new'] > 0) 
			{
				echo 'action:newmail:'.$inbox_data["number_all"].chr(13);
			}
		}
		else
		{
			if ($inbox_data['number_all'] > 0) 
			{
				echo 'action:newmail'.$inbox_data["number_all"].chr(13);
			}
		}
		// end the mailserver request
		$GLOBALS['phpgw']->msg->end_request();
	}
?>
