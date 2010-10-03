<?php
	/**************************************************************************\
	* phpGroupWare - Messenger                                                 *
	* http://www.phpgroupware.org                                              *
	* This application written by Joseph Engo <jengo@phpgroupware.org>         *
	* --------------------------------------------                             *
	* Funding for this program was provided by http://www.checkwithmom.com     *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	if ($GLOBALS['phpgw_info']['flags']['currentapp'] != 'messenger' &&
		$GLOBALS['phpgw_info']['flags']['currentapp'] != 'welcome')
	{
		$GLOBALS['phpgw']->db->query("SELECT COUNT(*) AS msg_cnt FROM phpgw_messenger_messages WHERE message_owner='"
				. $GLOBALS['phpgw_info']['user']['account_id'] . "' and message_status='N'",__LINE__,__FILE__);
	
		if ( $GLOBALS['phpgw']->db->next_record() 
			&& $GLOBALS['phpgw']->db->f('msg_cnt'))
		{
			echo '<div class="msg"><a href="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'messenger.uimessenger.inbox') )
				. '">' . lang('You have %1 new message' . ($GLOBALS['phpgw']->db->f('msg_cnt') > 1 ? 's' : '' ), $GLOBALS['phpgw']->db->f('msg_cnt')) . '</a>'
				. '</div>';
		}
	}
