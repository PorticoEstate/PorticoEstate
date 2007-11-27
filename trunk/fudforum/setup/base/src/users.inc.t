<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: users.inc.t 13837 2003-11-01 22:57:15Z skwashd $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

function init_user()
{
	$o1 =& $GLOBALS['FUD_OPT_1'];
	$o2 =& $GLOBALS['FUD_OPT_2'];

	$phpgw =& $GLOBALS['phpgw_info']['user'];

	/* delete old sessions */
	if (!(rand() % 10)) {
		q("DELETE FROM {SQL_TABLE_PREFIX}ses WHERE time_sec+".$GLOBALS['phpgw_info']['server']['sessions_timeout']." < ".__request_timestamp__);
	}

	$u = db_sab("SELECT 
			s.id AS sid, s.data, s.returnto, 
			t.id AS theme_id, t.lang, t.name AS theme_name, t.locale, t.theme, t.pspell_lang, t.theme_opt, 
			u.alias, u.posts_ppg, u.time_zone, u.sig, u.last_visit, u.last_read, u.cat_collapse_status, u.users_opt, u.ignore_list, u.ignore_list, u.buddy_list, u.id, u.group_leader_list, u.email, u.login 
			FROM {SQL_TABLE_PREFIX}ses s
			INNER JOIN {SQL_TABLE_PREFIX}users u ON u.id=(CASE WHEN s.user_id>2000000000 THEN 1 ELSE s.user_id END) 
			INNER JOIN {SQL_TABLE_PREFIX}themes t ON t.id=u.theme WHERE s.ses_id='".s."'");
	if (!$u) {
		/* registered user */
		if ($phpgw['account_lid'] != $GLOBALS['ANON_NICK']) {
			/* this means we do not have an entry for this user in the sessions table */
			$uid = q_singleval("SELECT id FROM {SQL_TABLE_PREFIX}users WHERE phpgw_id=".(int)$phpgw['account_id']);
			$id = db_qid("INSERT INTO {SQL_TABLE_PREFIX}ses (user_id, ses_id, time_sec) VALUES(".$uid.", '".s."', ".__request_timestamp__.")");
			$u = db_sab('SELECT s.id AS sid, s.data, s.returnto, t.id AS theme_id, t.lang, t.name AS theme_name, t.locale, t.theme, t.pspell_lang, t.theme_opt, u.alias, u.posts_ppg, u.time_zone, u.sig, u.last_visit, u.last_read, u.cat_collapse_status, u.users_opt, u.ignore_list, u.ignore_list, u.buddy_list, u.id, u.group_leader_list, u.email, u.login FROM {SQL_TABLE_PREFIX}ses s INNER JOIN {SQL_TABLE_PREFIX}users u ON u.id=s.user_id INNER JOIN {SQL_TABLE_PREFIX}themes t ON t.id=u.theme WHERE s.id='.$id);
		} else { /* anonymous user */
			do {
				$uid = 2000000000 + mt_rand(1, 147483647);
			} while (!($id = db_li("INSERT INTO {SQL_TABLE_PREFIX}ses (time_sec, ses_id, user_id) VALUES (".__request_timestamp__.", '".s."', ".$uid.")", $ef, 1)));
			$u = db_sab('SELECT s.id AS sid, s.data, s.returnto, t.id AS theme_id, t.lang, t.name AS theme_name, t.locale, t.theme, t.pspell_lang, t.theme_opt, u.alias, u.posts_ppg, u.time_zone, u.sig, u.last_visit, u.last_read, u.cat_collapse_status, u.users_opt, u.ignore_list, u.ignore_list, u.buddy_list, u.id, u.group_leader_list, u.email, u.login FROM {SQL_TABLE_PREFIX}ses s INNER JOIN {SQL_TABLE_PREFIX}users u ON u.id=1 INNER JOIN {SQL_TABLE_PREFIX}themes t ON t.id=u.theme WHERE s.id='.$id);
		}
	}
	/* grant admin access */
	if (!empty($phpgw['apps']['admin'])) {
		$u->users_opt |= 1048576;
	}

	/* this is ugly, very ugly, but there is no way around it, we need to see if the 
	 * user's language had changed and we can only do it this way.
	 */
	$langl = array('bg'=>'bulgarian', 'zh'=>'chinese_big5', 'cs'=>'czech', 'nl'=>'dutch', 'fr'=>'french', 'de'=>'german', 'it'=>'italian', 'lv'=>'latvian', 'no'=>'norwegian', 'pl'=>'polish', 'pt'=>'portuguese', 'ro'=>'romanian', 'ru'=>'russian', 'sk'=>'slovak', 'es'=>'spanish', 'sv'=>'swedish', 'tr'=>'turkish', 'en'=>'english');
	$lang =& $phpgw['preferences']['common']['lang'];
	if (isset($langl[$lang]) && $langl[$lang] != $u->lang) {
		if (!($o = db_sab("SELECT * FROM {SQL_TABLE_PREFIX}themes WHERE lang='{$langl[$lang]}'"))) {
			fud_use('compiler.inc', true);
			fud_use('theme.inc', true);
			$thm = new fud_theme;
			$thm->name = $thm->lang = $langl[$lang];
			$thm->theme = 'default';
			$thm->pspell_lang = file_get_contents($GLOBALS['DATA_DIR'].'thm/default/i18n/'.$langl[$lang].'/pspell_lang');
			$thm->locale = file_get_contents($GLOBALS['DATA_DIR'].'thm/default/i18n/'.$langl[$lang].'/locale');
			$thm->theme_opt = 1;
			$thm->add();
			compile_all('default', $langl[$lang], $langl[$lang]);
			$o = db_sab("SELECT * FROM {SQL_TABLE_PREFIX}themes WHERE lang='{$langl[$lang]}'");
		}
		$u->lang = $o->lang;
		$u->theme_name = $o->name;
		$u->locale = $o->locale;
		$u->theme_id = $o->id;
		$u->theme = $o->theme;
		$u->pspell_lang = $o->pspell_lang;
		$u->theme_opt = $o->theme_opt;

		q("UPDATE {SQL_TABLE_PREFIX}users SET theme=".$u->theme_id." WHERE id=".$u->id);
	}

	if ($u->data) {
		$u->data = @unserialize($u->data);
	}
	$u->users_opt = (int) $u->users_opt;

	/* set timezone */
	@putenv('TZ=' . $u->time_zone);
	/* set locale */
	setlocale(LC_ALL, $u->locale);

	/* view format for threads & messages */
	define('d_thread_view', $u->users_opt & 256 ? 'msg' : 'tree');
	define('t_thread_view', $u->users_opt & 128 ? 'thread' : 'threadt');

	/* theme path */
	@define('fud_theme', 'theme/' . str_replace(' ', '_', $u->theme_name) . '/');

	/* define _uid, which, will tell us if this is a 'real' user or not */
	define('_uid', !($u->users_opt & 2097152) ? $u->id : 0);
	define('__fud_real_user__', ($u->id != 1 ? $u->id : 0));

	if (__fud_real_user__) {
		q('UPDATE {SQL_TABLE_PREFIX}users SET last_visit='.__request_timestamp__.' WHERE id='.$u->id);
	}

	return $u;
}

function user_alias_by_id($id)
{
	return q_singleval('SELECT alias FROM {SQL_TABLE_PREFIX}users WHERE id='.$id);
}

function user_register_forum_view($frm_id)
{
	q('UPDATE {SQL_TABLE_PREFIX}forum_read SET last_view='.__request_timestamp__.' WHERE forum_id='.$frm_id.' AND user_id='._uid);
	if (!db_affected()) {
		db_li('INSERT INTO {SQL_TABLE_PREFIX}forum_read (forum_id, user_id, last_view) VALUES ('.$frm_id.', '._uid.', '.__request_timestamp__.')', $ef);
	}
}

function user_register_thread_view($thread_id, $tm=0, $msg_id=0)
{
	if (!$tm) {
		$tm = __request_timestamp__;
	}

	if (!db_li('INSERT INTO {SQL_TABLE_PREFIX}read (last_view, msg_id, thread_id, user_id) VALUES('.$tm.', '.$msg_id.', '.$thread_id.', '._uid.')', $ef)) {
		q('UPDATE {SQL_TABLE_PREFIX}read SET last_view='.$tm.', msg_id='.$msg_id.' WHERE thread_id='.$thread_id.' AND user_id='._uid);
	}
}

function user_set_post_count($uid)
{
	$pd = db_saq("SELECT MAX(id),count(*) FROM {SQL_TABLE_PREFIX}msg WHERE poster_id=".$uid." AND apr=1");
	$level_id = (int) q_singleval('SELECT id FROM {SQL_TABLE_PREFIX}level WHERE post_count <= '.$pd[1].' ORDER BY post_count DESC LIMIT 1');
	q('UPDATE {SQL_TABLE_PREFIX}users SET u_last_post_id='.(int)$pd[0].', posted_msg_count='.(int)$pd[1].', level_id='.$level_id.' WHERE id='.$uid);
}

function user_mark_all_read($id)
{
	q('UPDATE {SQL_TABLE_PREFIX}users SET last_read='.__request_timestamp__.' WHERE id='.$id);
	q('DELETE FROM {SQL_TABLE_PREFIX}read WHERE user_id='.$id);
	q('DELETE FROM {SQL_TABLE_PREFIX}forum_read WHERE user_id='.$id);
}

function user_mark_forum_read($id, $fid, $last_view)
{
	if (__dbtype__ == 'mysql') {
		q('REPLACE INTO {SQL_TABLE_PREFIX}read (user_id, thread_id, msg_id, last_view) SELECT '.$id.', id, last_post_id, '.__request_timestamp__.' FROM {SQL_TABLE_PREFIX}thread WHERE forum_id='.$fid);
	} else {
		if (!db_li('INSERT INTO {SQL_TABLE_PREFIX}read (user_id, thread_id, msg_id, last_view) SELECT '.$id.', id, last_post_id, '.__request_timestamp__.' FROM {SQL_TABLE_PREFIX}thread WHERE forum_id='.$fid)) {
			q("UPDATE {SQL_TABLE_PREFIX}read SET user_id=".$id.", thread_id=id, msg_id=last_post_id, last_view=".__request_timestamp__." WHERE user_id=".$id." SELECT id, last_post_id FROM {SQL_TABLE_PREFIX}thread WHERE forum_id=".$fid);
		}
	}
}

if (!defined('forum_debug')) {
	$GLOBALS['usr'] =& init_user();
}
?>
