<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: usercp.inc.t 13837 2003-11-01 22:57:15Z skwashd $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

if ($GLOBALS['fudh_uopt'] & 524288 || $GLOBALS['fudh_uopt'] & 1048576) {
	if ($GLOBALS['fudh_uopt'] & 1048576) {
		$GLOBALS['adm_file'][] = array('text' => '{TEMPLATE: admin_control_panel}', 'link' => 'adm/admglobal.php?'._rsid, 'no_lang' => true);
		if ($GLOBALS['FUD_OPT_1'] & 32 && ($avatar_count = q_singleval("SELECT count(*) FROM {SQL_TABLE_PREFIX}users WHERE users_opt>=16777216 AND (users_opt & 16777216) > 0"))) {
			$GLOBALS['adm_file'][] = array('text' => '{TEMPLATE: custom_avatar_queue}', 'link' => 'adm/admapprove_avatar.php?'._rsid, 'no_lang' => true);
		}
		if ($report_count = q_singleval('SELECT count(*) FROM {SQL_TABLE_PREFIX}msg_report')) {
			$GLOBALS['adm_file'][] = array('text' => '{TEMPLATE: reported_msgs}', 'link' => '{TEMPLATE: reported_msgs_lnk}', 'no_lang' => true);
		}
		if ($thr_exchc = q_singleval('SELECT count(*) FROM {SQL_TABLE_PREFIX}thr_exchange')) {
			$GLOBALS['adm_file'][] = array('text' => '{TEMPLATE: thr_exch}', 'link' => '{TEMPLATE: thr_exch_lnk}', 'no_lang' => true);
		}
		$q_limit = '';
	} else {
		if ($report_count = q_singleval('SELECT count(*) FROM {SQL_TABLE_PREFIX}msg_report mr INNER JOIN {SQL_TABLE_PREFIX}msg m ON mr.msg_id=m.id INNER JOIN {SQL_TABLE_PREFIX}thread t ON m.thread_id=t.id INNER JOIN {SQL_TABLE_PREFIX}mod mm ON t.forum_id=mm.forum_id AND mm.user_id='.(int)$GLOBALS['phpgw_info']['user']['account_id'])) {
			$GLOBALS['adm_file'][] = array('text' => '{TEMPLATE: reported_msgs}', 'link' => '{TEMPLATE: reported_msgs_lnk}', 'no_lang' => true);
		}
		if ($thr_exchc = q_singleval('SELECT count(*) FROM {SQL_TABLE_PREFIX}thr_exchange te INNER JOIN {SQL_TABLE_PREFIX}mod m ON m.user_id='.(int)$GLOBALS['phpgw_info']['user']['account_id'].' AND te.frm=m.forum_id')) {
			$GLOBALS['adm_file'][] = array('text' => '{TEMPLATE: thr_exch}', 'link' => '{TEMPLATE: thr_exch_lnk}', 'no_lang' => true);
		}
		$q_limit = ' INNER JOIN {SQL_TABLE_PREFIX}mod mm ON f.id=mm.forum_id AND mm.user_id='.(int)$GLOBALS['phpgw_info']['user']['account_id'];
	}
	if ($approve_count = q_singleval("SELECT count(*) FROM {SQL_TABLE_PREFIX}msg m INNER JOIN {SQL_TABLE_PREFIX}thread t ON m.thread_id=t.id INNER JOIN {SQL_TABLE_PREFIX}forum f ON t.forum_id=f.id ".$q_limit." WHERE m.apr=0 AND (f.forum_opt>=2 AND (f.forum_opt & 2) > 0)")) {
		$GLOBALS['adm_file'][] = array('text' => '{TEMPLATE: mod_que}', 'link' => '{TEMPLATE: mod_que_lnk}', 'no_lang' => true);
	}
}
if ($GLOBALS['fudh_uopt'] & 1048576 || $usr->group_leader_list) {
	$GLOBALS['adm_file'][] = array('text' => '{TEMPLATE: group_mgr}', 'link' => '{TEMPLATE: group_mgr_lnk}', 'no_lang' => true);
}

$GLOBALS['usr_file'][] = array('text' => '{TEMPLATE: profile}', 'link' => '{TEMPLATE: register_lnk}', 'no_lang' => true);
if ($GLOBALS['FUD_OPT_1'] & 1024) {
	$c = q_singleval('SELECT count(*) FROM {SQL_TABLE_PREFIX}pmsg WHERE duser_id='.(int)$GLOBALS['phpgw_info']['user']['account_id'].' AND fldr=1 AND read_stamp=0');
	$GLOBALS['usr_file'][] = array('text' => ($c ? '{TEMPLATE: private_msg_empty}' : '{TEMPLATE: private_msg_unread}'), 'link' => '{TEMPLATE: private_msg_lnk}', 'no_lang' => true);
}
if ($GLOBALS['FUD_OPT_1'] & 4194304 || $GLOBALS['fudh_uopt'] & 1048576) {
	$GLOBALS['usr_file'][] = array('text' => '{TEMPLATE: member_search}', 'link' => '{TEMPLATE: member_search_lnk}', 'no_lang' => true);
}
if ($GLOBALS['FUD_OPT_1'] & 16777216) {
	$GLOBALS['usr_file'][] = array('text' => '{TEMPLATE: uc_search}', 'link' => '{TEMPLATE: usercp_lnk}', 'no_lang' => true);
}
$GLOBALS['usr_file'][] = array('text' => '{TEMPLATE: uc_faq}', 'link' => '{TEMPLATE: usercp_lnk2}', 'no_lang' => true);
$GLOBALS['usr_file'][] = array('text' => '{TEMPLATE: uc_home}', 'link' => '{TEMPLATE: usercp_lnk3}', 'no_lang' => true);
?>