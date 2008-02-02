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

	/* $Id$ */

	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'chat',
		'noheader'   => True,
		'nonavbar'   => True
	);
	include('../header.inc.php');

	$loginid = $GLOBALS['phpgw_info']['user']['userid'];
	$refresh = 3;

	$GLOBALS['phpgw']->db->query("SELECT * FROM phpgw_chat_messages WHERE channel='$channel'");
	$GLOBALS['phpgw']->db->next_record();
	$status = $GLOBALS['phpgw']->db->f('status');
	$title = $GLOBALS['phpgw']->db->f('title');
	$user2 = $channel;

	// below line is abusively bad HTML form, but works in NS so far.
	echo '<head><META HTTP-EQUIV="Refresh" Content="10"' . $GLOBALS['phpgw_info']['user']['preferences']['chat']['phpgw_chat_refresh'] . ';URL=#bottom"></head><body>';

	echo '<center><strong>' . $loginid . ' in ' . $channel . '</strong></center>';
	if ($location == 'public')
	{
		$GLOBALS['phpgw']->db->query("SELECT * FROM phpgw_chat_messages WHERE channel='$channel' AND timesent>'" . $GLOBALS['phpgw_info']["user"]["logintime"]. "' ORDER BY timesent");
		while($GLOBALS['phpgw']->db->next_record())
		{
			echo '<font color="blue">' . $GLOBALS['phpgw']->db->f('loginid') . ' (' . $GLOBALS['phpgw']->common->show_date($GLOBALS['phpgw']->db->f('timesent'),'H:i.s') . '):  </font><font color="red">' . $GLOBALS['phpgw']->db->f('message') . '</font><br>';
		}
	}
	if ($location == 'private')
	{
		$GLOBALS['phpgw']->db->query("SELECT * FROM phpgw_chat_privatechat WHERE (user1='$loginid' AND user2='$user2') OR (user1='$user2' AND user2='$loginid') ORDER BY timesent");
		while($GLOBALS['phpgw']->db->next_record())
		{
			echo '<font color="blue">' . $GLOBALS['phpgw']->db->f('sentby') . ' (' . $GLOBALS['phpgw']->common->show_date($GLOBALS['phpgw']->db->f('timesent'),'H:i.s') . '):  </font><font color="red">' . $GLOBALS['phpgw']->db->f('message') . '</font><br>';
		}		
	}

	echo '<a name="bottom"></a>';
	echo '</body></html>';
?>
