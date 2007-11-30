<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: tables_current.inc.php 13837 2003-11-01 22:57:15Z skwashd $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

  /**************************************************************************\
  * This file should be generated for you. It should never be edited by hand *
  \**************************************************************************/

  /* $Id: tables_current.inc.php 13837 2003-11-01 22:57:15Z skwashd $ */

	// table array for FUDforum
	$phpgw_baseline = array(
		'phpgw_fud_action_log' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'logtime' => array('type' => 'int','precision' => '8','nullable' => False,'default' => '0'),
				'logaction' => array('type' => 'varchar','precision' => '100'),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'a_res' => array('type' => 'varchar','precision' => '100'),
				'a_res_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(array('user_id', 'logtime')),
			'uc' => array()
		),
		'phpgw_fud_ann_forums' => array(
			'fd' => array(
				'ann_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'forum_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0')
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(array('ann_id','forum_id'), array('ann_id')),
			'uc' => array()
		),
		'phpgw_fud_announce' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'date_started' => array('type' => 'int','precision' => '8','nullable' => False,'default' => '0'),
				'date_ended' => array('type' => 'int','precision' => '8','nullable' => False,'default' => '0'),
				'subject' => array('type' => 'varchar','precision' => '255'),
				'text' => array('type' => 'text')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(array('date_started', 'date_ended')),
			'uc' => array()
		),
		'phpgw_fud_attach' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'location' => array('type' => 'varchar','precision' => '255'),
				'original_name' => array('type' => 'varchar','precision' => '255'),
				'owner' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'attach_opt' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'message_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'dlcount' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'mime_type' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'fsize' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(array('message_id', 'attach_opt')),
			'uc' => array()
		),
		'phpgw_fud_avatar' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'img' => array('type' => 'varchar','precision' => '255'),
				'descr' => array('type' => 'varchar','precision' => '255')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('img')
		),
		'phpgw_fud_blocked_logins' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'login' => array('type' => 'varchar','precision' => '255')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_fud_buddy' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'bud_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(array('user_id', 'bud_id')),
			'uc' => array()
		),
		'phpgw_fud_cat' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'name' => array('type' => 'varchar','precision' => '50'),
				'description' => array('type' => 'varchar','precision' => '255'),
				'cat_opt' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'view_order' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '3')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_fud_custom_tags' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'name' => array('type' => 'varchar','precision' => '255'),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array('user_id'),
			'uc' => array()
		),
		'phpgw_fud_email_block' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'email_block_opt' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '1'),
				'string' => array('type' => 'varchar','precision' => '255')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('string')
		),
		'phpgw_fud_ext_block' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'ext' => array('type' => 'varchar','precision' => '32')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_fud_forum' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'cat_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'name' => array('type' => 'varchar','precision' => '100'),
				'descr' => array('type' => 'text'),
				'post_passwd' => array('type' => 'varchar','precision' => '32'),
				'forum_icon' => array('type' => 'varchar','precision' => '255'),
				'date_created' => array('type' => 'int','precision' => '8','nullable' => False,'default' => '0'),
				'thread_count' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'post_count' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'last_post_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'view_order' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'max_attach_size' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'max_file_attachments' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '1'),
				'moderators' => array('type' => 'text'),
				'message_threshold' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'forum_opt' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '16')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(array('cat_id'),array('last_post_id')),
			'uc' => array()
		),
		'phpgw_fud_fc_view' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'c' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'f' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array('f')
		),
		'phpgw_fud_forum_notify' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'forum_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(array('user_id','forum_id'), array('forum_id')),
			'uc' => array()
		),
		'phpgw_fud_forum_read' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'forum_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'last_view' => array('type' => 'int','precision' => '8','nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array(array('forum_id', 'user_id'))
		),
		'phpgw_fud_group_cache' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'resource_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'group_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'group_cache_opt' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(array('resource_id','user_id'), array('group_id')),
			'uc' => array()
		),
		'phpgw_fud_group_members' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'group_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'group_members_opt' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '65536')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(array('group_members_opt')),
			'uc' => array(array('group_id', 'user_id'))
		),
		'phpgw_fud_group_resources' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'group_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'resource_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(array('group_id','resource_id'),array('resource_id')),
			'uc' => array()
		),
		'phpgw_fud_groups' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'name' => array('type' => 'varchar','precision' => '255'),
				'inherit_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'forum_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'groups_opt' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'groups_opti' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(array('inherit_id'),array('forum_id')),
			'uc' => array()
		),
		'phpgw_fud_index' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'word_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'msg_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(array('word_id','msg_id'), array('msg_id')),
			'uc' => array()
		),
		'phpgw_fud_ip_block' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'ca' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'cb' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'cc' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'cd' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_fud_level' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'name' => array('type' => 'varchar','precision' => '255'),
				'post_count' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'img' => array('type' => 'varchar','precision' => '255'),
				'level_opt' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array('post_count'),
			'uc' => array()
		),
		'phpgw_fud_mime' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'fl_ext' => array('type' => 'varchar','precision' => '10'),
				'mime_hdr' => array('type' => 'varchar','precision' => '255'),
				'descr' => array('type' => 'varchar','precision' => '255'),
				'icon' => array('type' => 'varchar','precision' => '100','nullable' => False,'default' => 'unknown.gif')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array('fl_ext'),
			'uc' => array()
		),
		'phpgw_fud_mlist' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'forum_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'name' => array('type' => 'varchar','precision' => '255'),
				'subject_regex_haystack' => array('type' => 'text'),
				'subject_regex_needle' => array('type' => 'text'),
				'body_regex_haystack' => array('type' => 'text'),
				'body_regex_needle' => array('type' => 'text'),
				'additional_headers' => array('type' => 'text'),
				'mlist_opt' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '76')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array('forum_id'),
			'uc' => array()
		),
		'phpgw_fud_mod' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'forum_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array(array('user_id', 'forum_id'))
		),
		'phpgw_fud_mod_que' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'msg_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'forum_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_fud_msg' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'thread_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'poster_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'reply_to' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'ip_addr' => array('type' => 'varchar','precision' => '15','nullable' => False,'default' => '0.0.0.0'),
				'host_name' => array('type' => 'varchar','precision' => '255'),
				'post_stamp' => array('type' => 'int','precision' => '8','nullable' => False,'default' => '0'),
				'update_stamp' => array('type' => 'int','precision' => '8','nullable' => False,'default' => '0'),
				'updated_by' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'icon' => array('type' => 'varchar','precision' => '100'),
				'subject' => array('type' => 'varchar','precision' => '100'),
				'attach_cnt' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'poll_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'foff' => array('type' => 'int','precision' => '8','nullable' => False,'default' => '0'),
				'length' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'file_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '1'),
				'offset_preview' => array('type' => 'int','precision' => '8','nullable' => False,'default' => '0'),
				'length_preview' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'file_id_preview' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'attach_cache' => array('type' => 'text'),
				'poll_cache' => array('type' => 'text'),
				'mlist_msg_id' => array('type' => 'varchar','precision' => '100'),
				'msg_opt' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '1'),
				'apr' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(
					array('thread_id', 'apr'),
					array('poster_id', 'apr'),
					array('apr'),
					array('post_stamp'),
					array('attach_cnt'),
					array('poll_id'),
					array('ip_addr','post_stamp'),
					array('subject'),
					array('mlist_msg_id')
				),
			'uc' => array()
		),
		'phpgw_fud_msg_report' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'msg_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'reason' => array('type' => 'varchar','precision' => '255'),
				'stamp' => array('type' => 'int','precision' => '8','nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(array('msg_id','user_id'), array('user_id')),
			'uc' => array()
		),
		'phpgw_fud_nntp' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'forum_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'nntp_opt' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '44'),
				'server' => array('type' => 'varchar','precision' => '255'),
				'newsgroup' => array('type' => 'varchar','precision' => '255'),
				'port' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'timeout' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'login' => array('type' => 'varchar','precision' => '255'),
				'pass' => array('type' => 'varchar','precision' => '255')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array('forum_id'),
			'uc' => array()
		),
		'phpgw_fud_pmsg' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'to_list' => array('type' => 'text'),
				'ouser_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'duser_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'pdest' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'ip_addr' => array('type' => 'varchar','precision' => '15','nullable' => False,'default' => '0.0.0.0'),
				'host_name' => array('type' => 'varchar','precision' => '255'),
				'post_stamp' => array('type' => 'int','precision' => '8','nullable' => False,'default' => '0'),
				'read_stamp' => array('type' => 'int','precision' => '8','nullable' => False,'default' => '0'),
				'icon' => array('type' => 'varchar','precision' => '100'),
				'subject' => array('type' => 'varchar','precision' => '100'),
				'attach_cnt' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'foff' => array('type' => 'int','precision' => '8','nullable' => False,'default' => '0'),
				'length' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'ref_msg_id' => array('type' => 'varchar','precision' => '11'),
				'fldr' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'pmsg_opt' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '49')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(array('duser_id', 'fldr', 'read_stamp'), array('duser_id', 'fldr', 'id')),
			'uc' => array()
		),
		'phpgw_fud_poll' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'name' => array('type' => 'varchar','precision' => '255'),
				'owner' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'creation_date' => array('type' => 'int','precision' => '8','nullable' => False,'default' => '0'),
				'expiry_date' => array('type' => 'int','precision' => '8','nullable' => False,'default' => '0'),
				'max_votes' => array('type' => 'int','precision' => '4'),
				'total_votes' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'forum_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array('owner'),
			'uc' => array()
		),
		'phpgw_fud_poll_opt' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'poll_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'name' => array('type' => 'varchar','precision' => '255'),
				'count' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array('poll_id'),
			'uc' => array()
		),
		'phpgw_fud_poll_opt_track' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'poll_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'poll_opt' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array(array('poll_id', 'user_id'))
		),
		'phpgw_fud_read' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'thread_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'msg_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'last_view' => array('type' => 'int','precision' => '8','nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array(array('thread_id', 'user_id'))
		),
		'phpgw_fud_replace' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'replace_str' => array('type' => 'varchar','precision' => '255'),
				'with_str' => array('type' => 'varchar','precision' => '255'),
				'from_post' => array('type' => 'varchar','precision' => '255'),
				'to_msg' => array('type' => 'varchar','precision' => '255'),
				'replace_opt' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '1')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_fud_search' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'word' => array('type' => 'varchar','precision' => '50')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array(array('word'))
		),
		'phpgw_fud_search_cache' => array(
			'fd' => array(
				'srch_query' => array('type' => 'varchar','precision' => '32'),
				'query_type' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'expiry' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'msg_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'n_match' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0')
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(array('srch_query', 'query_type'), array('expiry')),
			'uc' => array()
		),
		'phpgw_fud_ses' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'ses_id' => array('type' => 'varchar','precision' => '32','nullable' => False,'default' => '0'),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'action' => array('type' => 'varchar','precision' => '255'),
				'forum_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'time_sec' => array('type' => 'int','precision' => '8','nullable' => False,'default' => '0'),
				'data' => array('type' => 'text'),
				'returnto' => array('type' => 'varchar','precision' => '255')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(array('time_sec', 'user_id'), array('user_id')),
			'uc' => array(array('ses_id'))
		),
		'phpgw_fud_smiley' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'img' => array('type' => 'varchar','precision' => '255'),
				'descr' => array('type' => 'varchar','precision' => '255'),
				'code' => array('type' => 'varchar','precision' => '25'),
				'vieworder' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_fud_stats_cache' => array(
			'fd' => array(
				'user_count' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'last_user_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'online_users_reg' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'online_users_anon' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'online_users_hidden' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'online_users_text' => array('type' => 'text'),
				'cache_age' => array('type' => 'int','precision' => '8','nullable' => False,'default' => '0')
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array('cache_age'),
			'uc' => array()
		),
		'phpgw_fud_themes' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'name' => array('type' => 'varchar','precision' => '255'),
				'theme' => array('type' => 'varchar','precision' => '255'),
				'lang' => array('type' => 'varchar','precision' => '255'),
				'locale' => array('type' => 'varchar','precision' => '32'),
				'pspell_lang' => array('type' => 'varchar','precision' => '32'),
				'theme_opt' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '1')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(array('theme_opt'), array('lang')),
			'uc' => array()
		),
		'phpgw_fud_thr_exchange' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'th' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'frm' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'req_by' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'reason_msg' => array('type' => 'text')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array('frm'),
			'uc' => array()
		),
		'phpgw_fud_thread' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'forum_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'root_msg_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'last_post_date' => array('type' => 'int','precision' => '8','nullable' => False,'default' => '0'),
				'replies' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'views' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'rating' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'n_rating' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'last_post_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'moved_to' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'orderexpiry' => array('type' => 'int','precision' => '8','nullable' => False,'default' => '0'),
				'thread_opt' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(array('forum_id', 'last_post_date', 'moved_to'), array('root_msg_id'), array('replies'), array('thread_opt')),
			'uc' => array()
		),
		'phpgw_fud_thread_notify' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'thread_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(array('user_id','thread_id'), array('thread_id')),
			'uc' => array()
		),
		'phpgw_fud_thread_rate_track' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'thread_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'stamp' => array('type' => 'int','precision' => '8','nullable' => False,'default' => '0'),
				'rating' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array(array('thread_id', 'user_id'))
		),
		'phpgw_fud_thread_view' => array(
			'fd' => array(
				'forum_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'page' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'thread_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'pos' => array('type' => 'int','precision' => '4', 'nullable' => False, 'default' => '0'),
				'tmp' => array('type' => 'int','precision' => '4')
			),
			'pk' => array('forum_id','page','pos'),
			'fk' => array(),
			'ix' => array(array('forum_id', 'thread_id')),
			'uc' => array()
		),
		'phpgw_fud_title_index' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'word_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'msg_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(array('word_id','msg_id'), array('msg_id')),
			'uc' => array()
		),
		'phpgw_fud_user_ignore' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'ignore_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array(array('user_id', 'ignore_id'))
		),
		'phpgw_fud_users' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'login' => array('type' => 'varchar','precision' => '50'),
				'alias' => array('type' => 'varchar','precision' => '50'),
				'passwd' => array('type' => 'varchar','precision' => '32'),
				'name' => array('type' => 'varchar','precision' => '255'),
				'email' => array('type' => 'varchar','precision' => '255'),
				'location' => array('type' => 'varchar','precision' => '255'),
				'interests' => array('type' => 'varchar','precision' => '255'),
				'occupation' => array('type' => 'varchar','precision' => '255'),
				'avatar' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'avatar_loc' => array('type' => 'text'),
				'icq' => array('type' => 'int','precision' => '8'),
				'aim' => array('type' => 'varchar','precision' => '255'),
				'yahoo' => array('type' => 'varchar','precision' => '255'),
				'msnm' => array('type' => 'varchar','precision' => '255'),
				'jabber' => array('type' => 'varchar','precision' => '255'),
				'affero' => array('type' => 'varchar','precision' => '255'),
				'posts_ppg' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'time_zone' => array('type' => 'varchar','precision' => '255','nullable' => False,'default' => 'America/Montreal'),
				'bday' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'join_date' => array('type' => 'int','precision' => '8','nullable' => False,'default' => '0'),
				'conf_key' => array('type' => 'varchar','precision' => '32','nullable' => False,'default' => '0'),
				'user_image' => array('type' => 'varchar','precision' => '255'),
				'theme' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'posted_msg_count' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'last_visit' => array('type' => 'int','precision' => '8','nullable' => False,'default' => '0'),
				'referer_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'last_read' => array('type' => 'int','precision' => '8','nullable' => False,'default' => '0'),
				'custom_status' => array('type' => 'text'),
				'sig' => array('type' => 'text'),
				'level_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'reset_key' => array('type' => 'varchar','precision' => '32','nullable' => False,'default' => '0'),
				'u_last_post_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'home_page' => array('type' => 'varchar','precision' => '255'),
				'bio' => array('type' => 'text'),
				'cat_collapse_status' => array('type' => 'text'),
				'custom_color' => array('type' => 'varchar','precision' => '255'),
				'buddy_list' => array('type' => 'text'),
				'ignore_list' => array('type' => 'text'),
				'group_leader_list' => array('type' => 'text'),
				'users_opt' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '4488117'),
				'phpgw_id' => array('type' => 'int','precision' => '4')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(array('conf_key'), array('last_visit'), array('referer_id'), array('reset_key'), array('users_opt')),
			'uc' => array(array('login'), array('alias'), array('email'), array('phpgw_id'))
		)
	);
?>
