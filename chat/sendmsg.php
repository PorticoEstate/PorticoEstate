<?php
	/**************************************************************************\
	* phpGroupWare - Chat                                                      *
	* http://www.phpgroupware.org                                              *
	* This application written by Joseph Engo <jengo@phpgroupware.org>         *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: sendmsg.php,v 1.10 2006/11/10 13:34:36 sigurdne Exp $ */

	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'chat',
		'noheader'   => True,
		'nonavbar'   => True
	);
	include('../header.inc.php');

	$loginid = $GLOBALS['phpgw_info']['user']['userid'];
	$channel = get_var('channel',Array('POST','GET'));
	$action = get_var('action',Array('POST','GET'));
	$location = get_var('location',Array('POST','Array'));

//	$date=date("YmdHis");

	if ($action=='post')
	{
		$datetime = createobject('phpgwapi.datetimefunctions');
		if ($location=='public')
		{
			$GLOBALS['phpgw']->db->query("INSERT INTO phpgw_chat_messages (channel,"
				."loginid, message, messagetype,"
				."timesent) values ('$channel',"
				."'$loginid','" . addslashes($message)."','1','"
			. $datetime->gmtnow . "')");
		}
		else
		{
			$GLOBALS['phpgw']->db->query("INSERT INTO phpgw_chat_privatechat "
				. "(user1,user2,sentby,"
				. "message, messagetype,"
				. "timesent) values ('$loginid',"
				. "'$channel','$loginid','" . addslashes($message)."','1','"
				. $datetime->gmtnow . "')");
			$GLOBALS['phpgw']->db->query("INSERT INTO phpgw_chat_privatechat (user1, user2, sentby, message, messagetype, timesent) VALUES ('$loginid','$channel','$loginid','$message','1','" . $datetime->gmtnow . "')");			
		}
	}

	echo "<html><body><center>";
	echo '<form  name="sendmsg" method="post" action="' . $GLOBALS['phpgw']->link('/chat/sendmsg.php') . '">';
	echo '<input type="hidden" name="channel" value="' . $channel . '">';
	echo '<input type="hidden" name="message" value="' . $message . '">';
	echo '<input type="hidden" name="action" value="post">';
	echo '<input type="hidden" name="location" value="' . $location . '">';
	echo '<input type="text"   size="50" name="message"><br>';
	echo '<input type="submit" value="' . lang('Send Message') . '"></center></form>';
	echo '<script> form.sendmsg.message.focus(); </script></body></html>';
?>
