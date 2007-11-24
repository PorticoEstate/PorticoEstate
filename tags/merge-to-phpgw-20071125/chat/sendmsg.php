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

	$GLOBALS['phpgw_info']['flags'] = array
	(
		'currentapp' => 'chat',
		'noheader'   => True,
		'nonavbar'   => True
	);
	include('../header.inc.php');

	$loginid = $GLOBALS['phpgw_info']['user']['userid'];
	$channel = phpgw::get_var('channel');
	$send = phpgw::get_var('send', 'bool', 'POST');
	$location = phpgw::get_var('location');

	if ( $send )
	{
		$message = $GLOBALS['phpgw']->db->db_addslashes(phpgw::get_var('message'));
		$channel = $GLOBALS['pphgw']->db->db_addslashes($channel);

		phpgw::import_class('phpgwapi.datetime');
		$gmtnow = phpgwapi_datetime::gmtnow();
		if ($location=='public')
		{
			$GLOBALS['phpgw']->db->query('INSERT INTO phpgw_chat_messages '
				. '(channel, loginid, message, messagetype, timesent)'
				. " VALUES ('$channel', '$loginid','$message', 1, $gmtnow)");
		}
		else
		{
			$GLOBALS['phpgw']->db->query('INSERT INTO phpgw_chat_privatechat '
				. '(user1,user2,sentby, message, messagetype, timesent)'
				. " VALUES ('$loginid', '$channel', '$loginid', '$message', 1, $gmtnow)");
		}
	}

	$sendmsg = $GLOBALS['phpgw']->link('/chat/sendmsg.php');
	$lang_title = lang('please enter your message');
	$lang_send = lang('send message');

	echo <<<HTML
<html>
	<head>
		<title>$lang_title</title>
	</head>
	<body>
		<form  name="sendmsg" method="post" action="$sendmsg">
			<input type="hidden" name="channel" value="{$channel}">
			<input type="hidden" name="message" value="{$message}">
			<input type="hidden" name="location" value="{$location}">
			<input type="text"   size="50" name="message"><br>
			<input type="submit" name="send" value="{$lang_send}">
		</form>
		<script type="text/javascript">form.sendmsg.message.focus();</script>
	</body>
</html>

HTML;
