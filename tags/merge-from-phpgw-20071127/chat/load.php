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

	/* $Id: load.php 10128 2002-04-30 23:24:20Z skeeter $ */

	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'chat',
		'noheader'   => True,
		'nonavbar'   => True
	);
	include('../header.inc.php');

	$channel = get_var('channel',Array('POST','GET'));
	$action = get_var('action',Array('POST','GET'));
	$location = get_var('location',Array('POST','Array'));

	if ($channel == '')
	{
		Header('Location: ' . $GLOBALS['phpgw']->link('/chat/index.php'));
	}

	$loginid = $GLOBALS['phpgw_info']['user']['userid'];

	if ($channel[0] == '~')
	{
		$location = 'private';
		$newchannel = str_replace('~','',$channel);
	}
	else
	{
		$location = 'public';
		$newchannel = $channel;
	}

	if ($action == 'newprivate')
	{
		$user2 = $channel;
		$location = 'private';
		$GLOBALS['phpgw']->db->query("DELETE FROM phpgw_chat_privatechat WHERE ((user1='$loginid' AND user2='$user2') OR (user1='$user2' AND user2='$loginid'))");
		if(!$notnew)
		{
			$GLOBALS['phpgw']->db->query("INSERT INTO phpgw_chat_privatechat (user1,user2,sentby,message,messagetype,timesent,closed) VALUES ('$loginid','$user2','System','New chat with $loginid and $user2','0','" . time() . "','0')");
		}
	}

	if ($location != 'private')
	{
		$GLOBALS['phpgw']->db->query("SELECT * FROM phpgw_chat_currentin WHERE loginid='$loginid' AND channel='$newchannel'");
		if ($GLOBALS['phpgw']->db->nf() == 0 && $newchannel)
		{
			$GLOBALS['phpgw']->db->query("INSERT INTO phpgw_chat_currentin (loginid, channel, lastmessage) VALUES ('$loginid','$newchannel','" . time() . "')");
		}
		else
		{
			$GLOBALS['phpgw']->db->query("UPDATE phpgw_chat_currentin SET lastmessage='" . time() . "' WHERE loginid='$loginid' AND channel='$newchannel'");
		}
	}

	echo '<html>
<head>
<title>' . lang('Chat') . '</title>
</head>';

	echo '  <FRAMESET COLS="130,*,126" FRAMEBORDER="0">' . "\n";
	echo '    <FRAME SRC="' . $GLOBALS['phpgw']->link('/chat/channels.php','channel='.$newchannel.'&location='.$location) . '" NAME="static" SCROLLING="AUTO">' . "\n";
	echo '    <FRAME SRC="' . $GLOBALS['phpgw']->link('/chat/body.php','channel='.$newchannel.'&action='.$action.'&location='.$location.'&user2='.$user2) . '" NAME="body">' . "\n";
	echo '    <FRAME SRC="' . $GLOBALS['phpgw']->link('/chat/users.php','channel='.$newchannel.'&location='.$location) . '" NAME="users" SCROLLING="AUTO">' . "\n";
	echo "  </FRAMESET>\n";
	echo "</html>\n";
?>
