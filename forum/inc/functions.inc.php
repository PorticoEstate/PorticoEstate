<?php
	/*****************************************************************************\
	* phpGroupWare - Forums                                                       *
	* http://www.phpgroupware.org                                                 *
	* Written by Mark A Peters <skeeter@phpgroupware.org>                         *
	* Based off of Jani Hirvinen <jpkh@shadownet.com>                             *
	* -------------------------------------------                                 *
	*  This program is free software; you can redistribute it and/or modify it    *
	*  under the terms of the GNU General Public License as published by the      *
	*  Free Software Foundation; either version 2 of the License, or (at your     *
	*  option) any later version.                                                 *
	\*****************************************************************************/

	/* $Id$ */

	// Keep track of what they are doing
	$GLOBALS['session_info'] = $GLOBALS['phpgw']->session->appsession('session_data','forum');

	$GLOBALS['cat_id'] = get_var('cat_id',Array('DEFAULT','GET'),$GLOBALS['session_info']['cat_id']);
	$GLOBALS['forum_id'] = get_var('forum_id',Array('DEFAULT','GET'),$GLOBALS['session_info']['forum_id']);

	if (!is_array($GLOBALS['session_info']))
	{
		$GLOBALS['session_info'] = array(
			'view'     => $GLOBALS['phpgw_info']['user']['preferences']['forum']['default_view'],
			'location' => '', // Not used ... yet
			'cat_id'   => ($GLOBALS['cat_id']   ? $GLOBALS['cat_id']   : ''),
			'forum_id' => ($GLOBALS['forum_id'] ? $GLOBALS['forum_id'] : '')
		);
	}

	$GLOBALS['session_info']['cat_id'] = $GLOBALS['cat_id'];
	$GLOBALS['session_info']['forum_id'] = $GLOBALS['forum_id'];

	$GLOBALS['phpgw']->session->appsession('session_data','forum',$GLOBALS['session_info']);

// Global functions for phpgw forums

	//
	// showthread shows thread in threaded mode :)
	//  params are: $thread = id from master message, father of all messages in this thread
	//          $current = maybe NULL or message number where we are at the moment,
	//         used only in reply (read.php) section to show our current
	//         message with little different color ($phpgw_info["theme"]["bg05"])
	//
	function showthread ($cat)
	{
		while($GLOBALS['phpgw']->db->next_record())
		{
			$GLOBALS['tr_color'] = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($GLOBALS['tr_color']);

			if($GLOBALS['phpgw']->db->f('id') == $current) $GLOBALS['tr_color'] = $GLOBALS['phpgw_info']['theme']['bg05'];
			echo '<tr bgcolor="'.$GLOBALS['tr_color'].'">';

			$move = '';
			for($tmp = 1;$tmp <= $GLOBALS['phpgw']->db->f('depth'); $tmp++)
			{
				$move .= '&nbsp;&nbsp;';
			}

			$pos = $GLOBALS['phpgw']->db->f('pos');
			$cat = $GLOBALS['phpgw']->db->f('cat_id');
			$for = $GLOBALS['phpgw']->db->f('for_id');
			$subject = $GLOBALS['phpgw']->db->f('subject');
			if (! $subject)
			{
				$subject = '[ No subject ]';
			}
			echo '<td>' . $move . '<a href="' . $GLOBALS['phpgw']->link('/forum/read.php',"cat=$cat&for=$for&pos=$pos&col=1&msg=" . $GLOBALS['phpgw']->db->f('id')) .">"
			. $subject . '</a></td>'."\n";

			echo '<td align="left" valign="top">' . ($GLOBALS['phpgw']->db->f('thread_owner')?$GLOBALS['phpgw']->accounts->id2name($GLOBALS['phpgw']->db->f('thread_owner')):lang('Unknown')) .'</td>'."\n";
			echo '<td align="left" valign="top">' . $GLOBALS['phpgw']->common->show_date($GLOBALS['phpgw']->db->from_timestamp($GLOBALS['phpgw']->db->f('postdate'))) .'</td>'."\n";

			if($debug)
			{
				echo '<td>' . $GLOBALS['phpgw']->db->f('id')." " . $GLOBALS['phpgw']->db->f('parent') .' '
				. $GLOBALS['phpgw']->db->f('depth') .' ' . $GLOBALS['phpgw']->db->f('pos') .'</td>';
			}
		}
	}

	function show_topics($cat,$for)
	{
		while($GLOBALS['phpgw']->db->next_record())
		{
			$GLOBALS['tr_color'] = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($GLOBALS['tr_color']);
			echo '<tr bgcolor="'.$GLOBALS['tr_color'].'">';
			$subject = $GLOBALS['phpgw']->db->f('subject');
			if (! $subject)
			{
				$subject = '[ No subject ]';
			}
			echo '<td><a href="'.$GLOBALS['phpgw']->link('read.php','cat='.$cat.'&for='.$for.'&msg='.$msg.$phpgw->db->f('id')).'>'.$subject.'</a></td>'."\n";
			$lastreply = $GLOBALS['phpgw']->db->f('postdate');
			echo '<td align="left" valign="top">' . ($GLOBALS['phpgw']->db->f('thread_owner')?$GLOBALS['phpgw']->accounts->id2name($GLOBALS['phpgw']->db->f('thread_owner')):lang('Unknown')) . '</td>'."\n";
			$msgid = $GLOBALS['phpgw']->db->f('id');
			$mainid = $GLOBALS['phpgw']->db->f('main');

			echo '<td align="left" valign="top">' . $GLOBALS['phpgw']->db->f('n_replies') . '</td>'."\n";
			echo '<td align="left" valign="top">'.$lastreply.'</td>'."\n";
			echo '</tr>'."\n";
		}
	}
