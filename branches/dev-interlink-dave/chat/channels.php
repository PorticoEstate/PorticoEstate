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
	echo '<head>
<META HTTP-EQUIV="Refresh" Content="30" HREF="#bottom">
</head>
';

	$GLOBALS['phpgw']->db->query("SELECT * FROM phpgw_chat_privatechat WHERE (user1='$loginid' OR user2='$loginid') AND (closed!='1' AND messagetype='0')",$start,__LINE__,__FILE__);
	$size = $GLOBALS['phpgw']->db->nf();

	$groups = $GLOBALS['phpgw']->accounts->membership();
	$size = $size + count($groups);
	if (($size == 0) || ($size == 1))
	{
		$size = 2;
	}

	echo "<center><b>\n";
	echo lang('Channels') . ":\n";

	$GLOBALS['phpgw']->db->query("SELECT account_lid AS group_name FROM phpgw_accounts where account_type = 'g'");
	$GLOBALS['phpgw']->db->next_record();

	echo '</b><form method="post" action="' . $GLOBALS['phpgw']->link('/chat/load.php') . '" target="_top">' . "\n";
	echo '<input type="hidden" name="channel" value="' . $channel . '">' . "\n";
	echo '<select name="channel" size="' . $size . '">' . "\n";

	while(list($key,$group_info) = each($groups))
	{
		echo '<option value="'.$group_info['account_name'].'">'.$group_info['account_name']."\n";
	}

	$GLOBALS['phpgw']->db->query("SELECT * FROM phpgw_chat_privatechat WHERE (user1='$loginid' OR user2='$loginid') AND (closed!='1' AND messagetype='0')");
	$GLOBALS['phpgw']->db->next_record();

	for ($i = 0; $i < $GLOBALS['phpgw']->db->nf(); $i++)
	{
		if ($GLOBALS['phpgw']->db->f('user1') == $loginid)
		{
			echo "<option value= '~" . $GLOBALS['phpgw']->db->f('user2') . "'>&lt " . $GLOBALS['phpgw']->db->f('user2') . " &gt\n";
		}
		else
		{
			echo "<option value= '~" . $GLOBALS['phpgw']->db->f('user1') . "'>&lt " . $GLOBALS['phpgw']->db->f('user1') . " &gt\n";
		}
		$GLOBALS['phpgw']->db->next_record();
	}

	$ordermethod = 'ORDER BY channel';

	$GLOBALS['phpgw']->db->query("SELECT * FROM phpgw_chat_privatechat WHERE (user1='$loginid' OR user2='$loginid') AND (closed!='1' AND messagetype='0')");
	$GLOBALS['phpgw']->db->next_record();
	$size = $GLOBALS['phpgw']->db->nf();

	$GLOBALS['phpgw']->db->query("select * FROM phpgw_chat_currentin WHERE loginid='$loginid' $ordermethod",$start,__LINE__,__FILE__);
	$GLOBALS['phpgw']->db->next_record();

	$size = $size + $GLOBALS['phpgw']->db->nf();
	if (($size == 0) || ($size == 1))
	{
		$size = 2;
	}

	echo '</select><br><br>';
	echo '<input type="submit" value="' . lang('Switch Room') . '"></form>' . "\n";
	echo '<form method="post" action="' . $GLOBALS['phpgw']->link('/chat/index.php') .'" target="_parent">' . "\n";
	echo '<input type="hidden" name="action" value="part">' . "\n";
	echo '<input type="hidden" name="location" value="' . $location . '">' . "\n";
	echo '<input type="hidden" name="channel" value="' . $channel . '">' . "\n";
	echo '<select name="channel" size="' . $size . '">' . "\n";

	for ($i = 0; $i < $GLOBALS['phpgw']->db->nf(); $i++)
	{
		echo '<option value="' . $GLOBALS['phpgw']->db->f('channel') . '">' . $GLOBALS['phpgw']->db->f('channel') . "\n";
		$GLOBALS['phpgw']->db->next_record();
	}
	if(!$i)
	{
		$js = ' onClick="window.close();"';
	}

	$GLOBALS['phpgw']->db->query("SELECT * from phpgw_chat_privatechat WHERE (user1='$loginid' OR user2='$loginid') AND (closed!='1' AND messagetype='0')");
	$GLOBALS['phpgw']->db->next_record();

	for ($i = 0; $i < $GLOBALS['phpgw']->db->nf(); $i++)
	{
		if ($GLOBALS['phpgw']->db->f('user1') == $loginid)
		{
			echo '<option value="' . $GLOBALS['phpgw']->db->f('user2') . '">&lt ' . $GLOBALS['phpgw']->db->f('user2') . " &gt\n";
		}
		else
		{
			echo '<option value="' . $GLOBALS['phpgw']->db->f('user1') . '">&lt ' . $GLOBALS['phpgw']->db->f('user1') . " &gt\n";
		}
		$GLOBALS['phpgw']->db->next_record();
	}

	echo "</select>\n";
	echo '<input type="submit" value="' . lang('Part Channel') . '"' . $js . '></form></center><p> '. "\n";
?>
