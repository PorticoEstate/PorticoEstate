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

	/* $Id: index.php 12098 2003-03-21 23:07:24Z skwashd $ */

	/* Is this really needed, eh? */
	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp'              => 'chat',
		'navbar_target'           => '_top',
		'enable_nextmatchs_class' => True,
		'noheader'                => True
	);
	include('../header.inc.php');

	$loginid = $GLOBALS['phpgw_info']['user']['userid'];
	if ($action=='part')
	{
		if ($location == 'public')
		{
			$GLOBALS['phpgw']->db->query("DELETE FROM phpgw_chat_currentin WHERE loginid='$loginid' AND channel='$channel'");
		}
		if ($location == 'private')
		{
			$user2 = $channel;
			$GLOBALS['phpgw']->db->query("DELETE FROM phpgw_chat_privatechat WHERE ((user1='$loginid' AND user2='$user2') OR (user1='$user2' AND user2='$loginid'))");
			$GLOBALS['phpgw']->db->query("INSERT INTO phpgw_chat_privatechat (user1,user2,sentby,message,messagetype,timesent,closed) VALUES ('$loginid','$user2','System','This chat has been closed by $loginid.',0,'" . time() . " ',1)");
		}
		$GLOBALS['phpgw']->db->query("SELECT loginid FROM phpgw_chat_currentin WHERE loginid='$loginid'");
		if(!$GLOBALS['phpgw']->db->next_record())
		{
?>
<html>
<head></head>
<script>
window.close();
</script>
</html>
<?php
		}
		else
		{
			Header('Location: ' . $GLOBALS['phpgw']->link('/chat/load.php'));
		}
	}

	$GLOBALS['phpgw']->common->phpgw_header();
	echo parse_navbar();
?>
  <script>
  self.name="first_Window";
  function chatwindow()
  {
    var value = "";
    for(var i = 0; i < document.load.channel.length; i++) {
      if (document.load.channel[i].selected) {
        value += document.load.channel[i].value;
      }
    }
    if (value != "")
    {
      chatwin=window.open("<?php echo $GLOBALS['phpgw']->link('/chat/load.php'); echo (isset($GLOBALS['phpgw_info']['server']['usecookies']) && $GLOBALS['phpgw_info']['server']['usecookies']?'?':'&'); ?>channel="+value,"Chat","width=640,height=480,toolbar=no,scrollbars=yes,resizable=yes");
    }
  }
  </script>
  <table align="center">
    <tr>
      <td><?php echo lang('rooms'); ?></td>
      <td><?php echo lang('whowhere'); ?></td>
    </tr>
    <tr>
      <td>
        <form name="load" method="post" action="<?php echo $GLOBALS['phpgw']->link('/chat/load.php') ?>">
        <select name="channel" size="4" onChange="chatwindow();">
<?php
	$groups = $GLOBALS['phpgw']->accounts->membership();
	while(list($key,$group_info) = each($groups))
	{
		echo '          <option value="'.$group_info['account_name'].'">'.$group_info['account_name']."\n";
	}

	$GLOBALS['phpgw']->db->query("SELECT * FROM phpgw_chat_privatechat WHERE (user1='$loginid' OR user2='$loginid') AND (closed!='1' AND messagetype='0')");
	while ($GLOBALS['phpgw']->db->next_record())
	{
		if ($GLOBALS['phpgw']->db->f('user1') == $loginid)
		{
			echo '          <option value="~' . $GLOBALS['phpgw']->db->f('user2') . '">&lt ' . $GLOBALS['phpgw']->db->f('user2') . ' &gt\n';
		}
		else
		{
			echo '          <option value="~' . $GLOBALS['phpgw']->db->f('user1') . '">&lt ' . $GLOBALS['phpgw']->db->f('user1') . ' &gt\n';
		}
		$GLOBALS['phpgw']->db->next_record();
	}
	// Note the following ja-vascript has a channel named 'Default' passed to it.  This needs to be fixed...
?>
        </select><br>
        <noscript><input type="submit" value="<?php echo lang('Enter Chat'); ?>"></noscript>
        </form>
      </td>
      <td valign="top">
        <table border="1">
<?php
	$GLOBALS['phpgw']->db->limit_query("SELECT * FROM phpgw_chat_currentin $ordermethod",$start,__LINE__,__FILE__);
	$ordermethod = 'ORDER BY channel, loginid ASC';
	$GLOBALS['phpgw']->db->limit_query("SELECT * FROM phpgw_chat_currentin $ordermethod",$start,__LINE__,__FILE__);

	while ($GLOBALS['phpgw']->db->next_record())
	{
		$tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
		echo '          <tr bgcolor="' . $tr_color . '">' . "\n";
		echo '            <td>' . $GLOBALS['phpgw']->db->f('loginid') . ' - ' . $GLOBALS['phpgw']->db->f('channel') . "</td>\n";
		echo '          </tr>' . "\n";
	}
?>
        </table>
      </td>
    </tr>
  </table>
<?php
	$GLOBALS['phpgw']->common->phpgw_footer();
?>
