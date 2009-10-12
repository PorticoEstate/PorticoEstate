<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/
  /**************************************************************************\
  * This file should be generated for you by setup. It should not need to be *
  * edited by hand.                                                          *
  \**************************************************************************/

  /* $Id$ */

  /* table array for fudforum */
	$phpgw_baseline = array(
		'phpgw_fud_action_log' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'logtime' => array('type' => 'int', 'precision' => 8,'nullable' => False,'default' => '0'),
				'logaction' => array('type' => 'text','nullable' => True),
				'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'a_res' => array('type' => 'varchar', 'precision' => 100,'nullable' => True),
				'a_res_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(user_idlogtime),
			'uc' => array()
		),
		'phpgw_fud_ann_forums' => array(
			'fd' => array(
				'ann_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'forum_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0')
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(array(forum_id,ann_id),array(ann_id)),
			'uc' => array()
		),
		'phpgw_fud_announce' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'date_started' => array('type' => 'int', 'precision' => 8,'nullable' => False,'default' => '0'),
				'date_ended' => array('type' => 'int', 'precision' => 8,'nullable' => False,'default' => '0'),
				'subject' => array('type' => 'varchar', 'precision' => 255,'nullable' => False,'default' => ''),
				'text' => array('type' => 'text','nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(date_starteddate_ended),
			'uc' => array()
		),
		'phpgw_fud_attach' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'location' => array('type' => 'varchar', 'precision' => 255,'nullable' => False,'default' => ''),
				'original_name' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'owner' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'attach_opt' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'message_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'dlcount' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'mime_type' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'fsize' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(message_idattach_opt),
			'uc' => array()
		),
		'phpgw_fud_avatar' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'img' => array('type' => 'varchar', 'precision' => 255,'nullable' => False,'default' => ''),
				'descr' => array('type' => 'varchar', 'precision' => 255,'nullable' => False),
				'gallery' => array('type' => 'varchar', 'precision' => 255,'nullable' => False,'default' => 'default')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(gallery),
			'uc' => array()
		),
		'phpgw_fud_blocked_logins' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'login' => array('type' => 'varchar', 'precision' => 255,'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_fud_buddy' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'bud_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(user_idbud_id),
			'uc' => array()
		),
		'phpgw_fud_cat' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 50,'nullable' => False,'default' => ''),
				'description' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'cat_opt' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'view_order' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '3'),
				'parent' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(parent),
			'uc' => array()
		),
		'phpgw_fud_custom_tags' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 255,'nullable' => False,'default' => ''),
				'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(user_id),
			'uc' => array()
		),
		'phpgw_fud_email_block' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'email_block_opt' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '1'),
				'string' => array('type' => 'varchar', 'precision' => 255,'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(string),
			'uc' => array('string')
		),
		'phpgw_fud_ext_block' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'ext' => array('type' => 'varchar', 'precision' => 32,'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_fud_fc_view' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'c' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'f' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'lvl' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(array(f),array(c)),
			'uc' => array('f')
		),
		'phpgw_fud_fl_1' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => 4,'nullable' => True)
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_fud_fl_pm' => array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => 4,'nullable' => True)
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_fud_forum' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'cat_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'name' => array('type' => 'varchar', 'precision' => 255,'nullable' => False,'default' => ''),
				'descr' => array('type' => 'text','nullable' => True),
				'post_passwd' => array('type' => 'varchar', 'precision' => 32,'nullable' => True),
				'forum_icon' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'date_created' => array('type' => 'int', 'precision' => 8,'nullable' => False,'default' => '0'),
				'thread_count' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'post_count' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'last_post_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'view_order' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'max_attach_size' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'max_file_attachments' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '1'),
				'moderators' => array('type' => 'text','nullable' => True),
				'message_threshold' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'forum_opt' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '16')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(array(cat_id),array(last_post_id)),
			'uc' => array()
		),
		'phpgw_fud_forum_notify' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'forum_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(array(user_id,forum_id),array(forum_id)),
			'uc' => array('forum_id','user_id')
		),
		'phpgw_fud_forum_read' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'forum_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'last_view' => array('type' => 'int', 'precision' => 8,'nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(forum_iduser_id),
			'uc' => array('forum_id','user_id')
		),
		'phpgw_fud_geoip' => array(
			'fd' => array(
				'ips' => array('type' => 'int', 'precision' => 8,'nullable' => True),
				'ipe' => array('type' => 'int', 'precision' => 8,'nullable' => True),
				'cc' => array('type' => 'char', 'precision' => 2,'nullable' => True),
				'country' => array('type' => 'varchar', 'precision' => 50,'nullable' => True)
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(ipsipe),
			'uc' => array()
		),
		'phpgw_fud_group_cache' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'resource_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'group_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'group_cache_opt' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(array(resource_id,user_id),array(group_id),array(user_id)),
			'uc' => array('resource_id','user_id')
		),
		'phpgw_fud_group_members' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'group_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'group_members_opt' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '65536')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(array(group_id,user_id),array(group_members_opt)),
			'uc' => array('group_id','user_id')
		),
		'phpgw_fud_group_resources' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'group_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'resource_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(array(group_id,resource_id),array(resource_id)),
			'uc' => array()
		),
		'phpgw_fud_groups' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 255,'nullable' => False,'default' => ''),
				'inherit_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'forum_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'groups_opt' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'groups_opti' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(array(forum_id),array(inherit_id)),
			'uc' => array()
		),
		'phpgw_fud_index' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'word_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'msg_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(array(word_id,msg_id),array(msg_id)),
			'uc' => array()
		),
		'phpgw_fud_ip_block' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'ca' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'cb' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'cc' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'cd' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_fud_level' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 255,'nullable' => False,'default' => ''),
				'post_count' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'img' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'level_opt' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(post_count),
			'uc' => array()
		),
		'phpgw_fud_mime' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'fl_ext' => array('type' => 'varchar', 'precision' => 10,'nullable' => False,'default' => ''),
				'mime_hdr' => array('type' => 'varchar', 'precision' => 255,'nullable' => False,'default' => ''),
				'descr' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'icon' => array('type' => 'varchar', 'precision' => 100,'nullable' => False,'default' =>'current_timestamp')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(fl_ext),
			'uc' => array()
		),
		'phpgw_fud_mlist' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'forum_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'name' => array('type' => 'varchar', 'precision' => 255,'nullable' => False,'default' => ''),
				'subject_regex_haystack' => array('type' => 'text','nullable' => True),
				'subject_regex_needle' => array('type' => 'text','nullable' => True),
				'body_regex_haystack' => array('type' => 'text','nullable' => True),
				'body_regex_needle' => array('type' => 'text','nullable' => True),
				'additional_headers' => array('type' => 'text','nullable' => True),
				'mlist_opt' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '76'),
				'custom_sig' => array('type' => 'text','nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(forum_id),
			'uc' => array()
		),
		'phpgw_fud_mod' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'forum_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(user_idforum_id),
			'uc' => array('forum_id','user_id')
		),
		'phpgw_fud_mod_que' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'msg_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'forum_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_fud_msg' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'thread_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'poster_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'reply_to' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'ip_addr' => array('type' => 'varchar', 'precision' => 15,'nullable' => False,'default' => '0.0.0.0'),
				'host_name' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'post_stamp' => array('type' => 'int', 'precision' => 8,'nullable' => False,'default' => '0'),
				'update_stamp' => array('type' => 'int', 'precision' => 8,'nullable' => False,'default' => '0'),
				'updated_by' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'icon' => array('type' => 'varchar', 'precision' => 100,'nullable' => True),
				'subject' => array('type' => 'varchar', 'precision' => 100,'nullable' => False,'default' => ''),
				'attach_cnt' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'poll_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'foff' => array('type' => 'int', 'precision' => 8,'nullable' => False,'default' => '0'),
				'length' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'file_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '1'),
				'offset_preview' => array('type' => 'int', 'precision' => 8,'nullable' => False,'default' => '0'),
				'length_preview' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'file_id_preview' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'attach_cache' => array('type' => 'text','nullable' => True),
				'poll_cache' => array('type' => 'text','nullable' => True),
				'mlist_msg_id' => array('type' => 'varchar', 'precision' => 100,'nullable' => True),
				'msg_opt' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '1'),
				'apr' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'flag_cc' => array('type' => 'char', 'precision' => 2,'nullable' => True),
				'flag_country' => array('type' => 'varchar', 'precision' => 50,'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(array(thread_id,apr),array(post_stamp),array(poster_id,apr),array(apr),array(attach_cnt),array(poll_id),array(ip_addr,post_stamp),array(mlist_msg_id),array(subject)),
			'uc' => array()
		),
		'phpgw_fud_msg_report' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'msg_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'reason' => array('type' => 'text','nullable' => True),
				'stamp' => array('type' => 'int', 'precision' => 8,'nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(array(msg_id,user_id),array(user_id)),
			'uc' => array()
		),
		'phpgw_fud_msg_store' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'data' => array('type' => 'text','nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_fud_nntp' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'forum_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'nntp_opt' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '44'),
				'server' => array('type' => 'varchar', 'precision' => 255,'nullable' => False,'default' => ''),
				'newsgroup' => array('type' => 'varchar', 'precision' => 255,'nullable' => False,'default' => ''),
				'port' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'timeout' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'login' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'pass' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'imp_limit' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'custom_sig' => array('type' => 'text','nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(forum_id),
			'uc' => array()
		),
		'phpgw_fud_pmsg' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'to_list' => array('type' => 'text','nullable' => True),
				'ouser_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'duser_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'pdest' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'ip_addr' => array('type' => 'varchar', 'precision' => 15,'nullable' => False,'default' => '0.0.0.0'),
				'host_name' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'post_stamp' => array('type' => 'int', 'precision' => 8,'nullable' => False,'default' => '0'),
				'read_stamp' => array('type' => 'int', 'precision' => 8,'nullable' => False,'default' => '0'),
				'icon' => array('type' => 'varchar', 'precision' => 100,'nullable' => True),
				'subject' => array('type' => 'varchar', 'precision' => 100,'nullable' => False,'default' => ''),
				'attach_cnt' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'foff' => array('type' => 'int', 'precision' => 8,'nullable' => False,'default' => '0'),
				'length' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'ref_msg_id' => array('type' => 'varchar', 'precision' => 11,'nullable' => True),
				'fldr' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'pmsg_opt' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '49')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(array(duser_id,fldr,read_stamp),array(duser_id,fldr,id)),
			'uc' => array()
		),
		'phpgw_fud_poll' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 255,'nullable' => False,'default' => ''),
				'owner' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'creation_date' => array('type' => 'int', 'precision' => 8,'nullable' => False,'default' => '0'),
				'expiry_date' => array('type' => 'int', 'precision' => 8,'nullable' => False,'default' => '0'),
				'max_votes' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'total_votes' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'forum_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(owner),
			'uc' => array()
		),
		'phpgw_fud_poll_opt' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'poll_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'name' => array('type' => 'varchar', 'precision' => 255,'nullable' => False,'default' => ''),
				'count' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(poll_id),
			'uc' => array()
		),
		'phpgw_fud_poll_opt_track' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'poll_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'poll_opt' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(poll_iduser_id),
			'uc' => array('poll_id','user_id')
		),
		'phpgw_fud_read' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'thread_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'msg_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'last_view' => array('type' => 'int', 'precision' => 8,'nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(array(thread_id,user_id),array(user_id)),
			'uc' => array('thread_id','user_id')
		),
		'phpgw_fud_replace' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'replace_str' => array('type' => 'varchar', 'precision' => 255,'nullable' => False,'default' => ''),
				'with_str' => array('type' => 'varchar', 'precision' => 255,'nullable' => False,'default' => ''),
				'from_post' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'to_msg' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'replace_opt' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '1')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_fud_search' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'word' => array('type' => 'varchar', 'precision' => 50,'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(word),
			'uc' => array('word')
		),
		'phpgw_fud_search_cache' => array(
			'fd' => array(
				'srch_query' => array('type' => 'varchar', 'precision' => 32,'nullable' => False,'default' => ''),
				'query_type' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'expiry' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'msg_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'n_match' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0')
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(array(srch_query,query_type),array(expiry)),
			'uc' => array()
		),
		'phpgw_fud_ses' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'ses_id' => array('type' => 'varchar', 'precision' => 32,'nullable' => False,'default' => '0'),
				'sys_id' => array('type' => 'varchar', 'precision' => 32,'nullable' => False,'default' => '0'),
				'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'action' => array('type' => 'text','nullable' => True),
				'forum_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'time_sec' => array('type' => 'int', 'precision' => 8,'nullable' => False,'default' => '0'),
				'data' => array('type' => 'text','nullable' => True),
				'returnto' => array('type' => 'varchar', 'precision' => 255,'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(array(ses_id),array(user_id),array(time_sec,user_id)),
			'uc' => array('ses_id','user_id')
		),
		'phpgw_fud_smiley' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'img' => array('type' => 'varchar', 'precision' => 255,'nullable' => False,'default' => ''),
				'descr' => array('type' => 'varchar', 'precision' => 255,'nullable' => False,'default' => ''),
				'code' => array('type' => 'varchar', 'precision' => 25,'nullable' => False,'default' => ''),
				'vieworder' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_fud_stats_cache' => array(
			'fd' => array(
				'user_count' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'last_user_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'online_users_reg' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'online_users_anon' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'online_users_hidden' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'online_users_text' => array('type' => 'text','nullable' => True),
				'most_online' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'most_online_time' => array('type' => 'int', 'precision' => 8,'nullable' => False,'default' => '0'),
				'cache_age' => array('type' => 'int', 'precision' => 8,'nullable' => False,'default' => '0')
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_fud_themes' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'theme' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'lang' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'locale' => array('type' => 'varchar', 'precision' => 32,'nullable' => True),
				'pspell_lang' => array('type' => 'varchar', 'precision' => 32,'nullable' => True),
				'theme_opt' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '1')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(theme_opt),
			'uc' => array()
		),
		'phpgw_fud_thr_exchange' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'th' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'frm' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'req_by' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'reason_msg' => array('type' => 'text','nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(frm),
			'uc' => array()
		),
		'phpgw_fud_thread' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'forum_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'root_msg_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'last_post_date' => array('type' => 'int', 'precision' => 8,'nullable' => False,'default' => '0'),
				'replies' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'views' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'rating' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'n_rating' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'last_post_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'moved_to' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'orderexpiry' => array('type' => 'int', 'precision' => 8,'nullable' => False,'default' => '0'),
				'thread_opt' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'tdescr' => array('type' => 'varchar', 'precision' => 200,'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(array(forum_id,moved_to),array(thread_opt),array(root_msg_id),array(replies)),
			'uc' => array()
		),
		'phpgw_fud_thread_notify' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'thread_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(array(user_id,thread_id),array(thread_id)),
			'uc' => array('thread_id','user_id')
		),
		'phpgw_fud_thread_rate_track' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'thread_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'stamp' => array('type' => 'int', 'precision' => 8,'nullable' => False,'default' => '0'),
				'rating' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(thread_iduser_id),
			'uc' => array('thread_id','user_id')
		),
		'phpgw_fud_title_index' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'word_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'msg_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(array(word_id,msg_id),array(msg_id)),
			'uc' => array()
		),
		'phpgw_fud_tv_1' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'seq' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'thread_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'iss' => array('type' => 'int', 'precision' => 4,'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(array(thread_id),array(seq)),
			'uc' => array('thread_id')
		),
		'phpgw_fud_user_ignore' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'ignore_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'user_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(user_idignore_id),
			'uc' => array('ignore_id','user_id')
		),
		'phpgw_fud_users' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'login' => array('type' => 'varchar', 'precision' => 50,'nullable' => False,'default' => ''),
				'alias' => array('type' => 'varchar', 'precision' => 50,'nullable' => False,'default' => ''),
				'passwd' => array('type' => 'varchar', 'precision' => 32,'nullable' => False,'default' => ''),
				'name' => array('type' => 'varchar', 'precision' => 255,'nullable' => False,'default' => ''),
				'email' => array('type' => 'varchar', 'precision' => 255,'nullable' => False,'default' => ''),
				'location' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'interests' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'occupation' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'avatar' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'avatar_loc' => array('type' => 'text','nullable' => True),
				'icq' => array('type' => 'int', 'precision' => 8,'nullable' => True),
				'aim' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'yahoo' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'msnm' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'jabber' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'affero' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'google' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'skype' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'posts_ppg' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'time_zone' => array('type' => 'varchar', 'precision' => 255,'nullable' => False,'default' => 'America/Montreal'),
				'bday' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'join_date' => array('type' => 'int', 'precision' => 8,'nullable' => False,'default' => '0'),
				'conf_key' => array('type' => 'varchar', 'precision' => 32,'nullable' => False,'default' => '0'),
				'user_image' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'theme' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'posted_msg_count' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'last_visit' => array('type' => 'int', 'precision' => 8,'nullable' => False,'default' => '0'),
				'referer_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'last_read' => array('type' => 'int', 'precision' => 8,'nullable' => False,'default' => '0'),
				'custom_status' => array('type' => 'text','nullable' => True),
				'sig' => array('type' => 'text','nullable' => True),
				'level_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'reset_key' => array('type' => 'varchar', 'precision' => 32,'nullable' => False,'default' => '0'),
				'u_last_post_id' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '0'),
				'home_page' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'bio' => array('type' => 'text','nullable' => True),
				'cat_collapse_status' => array('type' => 'text','nullable' => True),
				'custom_color' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'buddy_list' => array('type' => 'text','nullable' => True),
				'ignore_list' => array('type' => 'text','nullable' => True),
				'group_leader_list' => array('type' => 'text','nullable' => True),
				'users_opt' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '4488117'),
				'sq' => array('type' => 'varchar', 'precision' => 32,'nullable' => True),
				'reg_ip' => array('type' => 'int', 'precision' => 8,'nullable' => False,'default' => '0'),
				'ban_expiry' => array('type' => 'int', 'precision' => 8,'nullable' => False,'default' => '0'),
				'topics_per_page' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => '40'),
				'last_login' => array('type' => 'int', 'precision' => 8,'nullable' => False,'default' => '0'),
				'flag_cc' => array('type' => 'char', 'precision' => 2,'nullable' => True),
				'flag_country' => array('type' => 'varchar', 'precision' => 50,'nullable' => True),
				'last_known_ip' => array('type' => 'int', 'precision' => 8,'nullable' => False,'default' => '0')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(array(login),array(email),array(alias),array(reset_key),array(last_visit),array(conf_key),array(referer_id),array(users_opt),array(join_date)),
			'uc' => array('alias','email','login')
		),
	);
?>
