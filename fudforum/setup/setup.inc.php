<?php
	/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: setup.inc.php 16493 2006-03-11 23:24:02Z skwashd $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

	/* $Id: setup.inc.php 16493 2006-03-11 23:24:02Z skwashd $ */

	$setup_info['fudforum']['name']		= 'fudforum';
	$setup_info['fudforum']['title']	= 'FUDforum for phpGroupWare';
	$setup_info['fudforum']['version']	= '0.0.1';
	$setup_info['fudforum']['app_order']	= 7;
	$setup_info['fudforum']['enable']	= 1;
	$setup_info['fudforum']['app_group']	= 'other';

	$setup_info['fudforum']['author'] = 'Ilia Alshanetsky';
	$setup_info['fudforum']['license']  = 'GPL';
	$setup_info['fudforum']['description'] = 'Fully featured web bulletin board.';
	$setup_info['fudforum']['maintainer'] = 'Ilia Alshanetsky';
	$setup_info['fudforum']['maintainer_email'] = 'ilia@prohost.org';

	/* the table info */
	$setup_info['fudforum']['tables'] = array('phpgw_fud_action_log','phpgw_fud_ann_forums','phpgw_fud_announce','phpgw_fud_attach','phpgw_fud_avatar','phpgw_fud_blocked_logins','phpgw_fud_buddy','phpgw_fud_cat','phpgw_fud_custom_tags','phpgw_fud_email_block','phpgw_fud_ext_block','phpgw_fud_forum','phpgw_fud_fc_view','phpgw_fud_forum_notify','phpgw_fud_forum_read','phpgw_fud_group_cache','phpgw_fud_group_members','phpgw_fud_group_resources','phpgw_fud_groups','phpgw_fud_index','phpgw_fud_ip_block','phpgw_fud_level','phpgw_fud_mime','phpgw_fud_mlist','phpgw_fud_mod','phpgw_fud_mod_que','phpgw_fud_msg','phpgw_fud_msg_report','phpgw_fud_nntp','phpgw_fud_pmsg','phpgw_fud_poll','phpgw_fud_poll_opt','phpgw_fud_poll_opt_track','phpgw_fud_read','phpgw_fud_replace','phpgw_fud_search','phpgw_fud_search_cache','phpgw_fud_ses','phpgw_fud_smiley','phpgw_fud_stats_cache','phpgw_fud_themes','phpgw_fud_thr_exchange','phpgw_fud_thread','phpgw_fud_thread_notify','phpgw_fud_thread_rate_track','phpgw_fud_thread_view','phpgw_fud_title_index','phpgw_fud_user_ignore','phpgw_fud_users');

	/* the hooks */
	$setup_info['fudforum']['hooks']['addaccount'] = 'fudforum.ufud.add_account';
	$setup_info['fudforum']['hooks']['deleteaccount'] = 'fudforum.ufud.del_account';
	$setup_info['fudforum']['hooks']['changepassword'] = 'fudforum.ufud.chg_settings';
	$setup_info['fudforum']['hooks']['sidebox_menu'] = 'fudforum.fud_sidebox_hooks.all_hooks';
	$setup_info['fudforum']['hooks'][] = 'admin';
	$setup_info['fudforum']['hooks'][] = 'preferences';

	/* the dependencies */
	$setup_info['fudforum']['depends'][] = array(
		'appname' => 'phpgwapi',
		'versions' => Array('0.9.14', '0.9.16', '0.9.17', '0.9.18')
	);
?>
