<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: users_reg.inc.t 13837 2003-11-01 22:57:15Z skwashd $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

class fud_user
{
	var $id, $login, $alias, $passwd, $plaintext_passwd, $name, $email, $location, $occupation, $interests,
	    $icq, $aim, $yahoo, $msnm, $jabber, $affero, $avatar, $avatar_loc, $posts_ppg, $time_zone, $bday, $home_page,
	    $sig, $bio, $posted_msg_count, $last_visit, $last_event, $conf_key, $user_image, $join_date, $theme, $last_read,
	    $mod_list, $mod_cur, $level_id, $u_last_post_id, $users_opt, $cat_collapse_status, $ignore_list, $buddy_list;
}

function make_alias($text)
{
	if (strlen($text) > $GLOBALS['MAX_LOGIN_SHOW']) {
		$text = substr($text, 0, $GLOBALS['MAX_LOGIN_SHOW']);
	}
	return htmlspecialchars($text);
}

class fud_user_reg extends fud_user
{
	function sync_user()
	{
		$rb_mod_list = (!($this->users_opt & 524288) && ($is_mod = q_singleval("SELECT id FROM {SQL_TABLE_PREFIX}mod WHERE user_id={$this->id}")) && (q_singleval("SELECT alias FROM {SQL_TABLE_PREFIX}users WHERE id={$this->id}") == $this->alias));

		q("UPDATE {SQL_TABLE_PREFIX}users SET ".$passwd."
			icq=".in($this->icq).",
			aim=".ssn(urlencode($this->aim)).",
			yahoo=".ssn(urlencode($this->yahoo)).",
			msnm=".ssn(urlencode($this->msnm)).",
			jabber=".ssn(urlencode($this->jabber)).",
			affero=".ssn(urlencode($this->affero)).",
			posts_ppg='".iz($this->posts_ppg)."',
			time_zone='".addslashes($this->time_zone)."',
			bday=".iz($this->bday).",
			user_image=".ssn(htmlspecialchars($this->user_image)).",
			location=".ssn(htmlspecialchars($this->location)).",
			occupation=".ssn(htmlspecialchars($this->occupation)).",
			interests=".ssn(htmlspecialchars($this->interests)).",
			avatar=".iz($this->avatar).",
			theme=".iz($this->theme).",
			avatar_loc=".ssn($this->avatar_loc).",
			sig=".ssn($this->sig).",
			home_page=".ssn(htmlspecialchars($this->home_page)).",
			bio=".ssn($this->bio).",
			users_opt=".$this->users_opt."
		WHERE id=".$this->id);

		if ($rb_mod_list) {
			rebuildmodlist();
		}
	}
}

function get_id_by_email($email)
{
	return q_singleval("SELECT id FROM {SQL_TABLE_PREFIX}users WHERE email='".addslashes($email)."'");
}

function get_id_by_login($login)
{
	return q_singleval("SELECT id FROM {SQL_TABLE_PREFIX}users WHERE login='".addslashes($login)."'");
}

function &usr_reg_get_full($id)
{
	if (($r = db_sab('SELECT * FROM {SQL_TABLE_PREFIX}users WHERE id='.$id))) {
		if (!function_exists('aggregate_methods')) {
			$o = new fud_user_reg;
			foreach ($r as $k => $v) {
				$o->{$k} = $v;
			}
			$r = $o;
		} else {
			aggregate_methods($r, 'fud_user_reg');
		}
	}
	return $r;
}

function rebuildmodlist()
{
	$tbl =& $GLOBALS['DBHOST_TBL_PREFIX'];
	$lmt =& $GLOBALS['SHOW_N_MODS'];
	$c = uq('SELECT u.id, u.alias, f.id FROM '.$tbl.'mod mm INNER JOIN '.$tbl.'users u ON mm.user_id=u.id INNER JOIN '.$tbl.'forum f ON f.id=mm.forum_id ORDER BY f.id,u.alias');
	while ($r = db_rowarr($c)) {
		$u[] = $r[0];
		if (isset($ar[$r[2]]) && count($ar[$r[2]]) >= $lmt) {
			continue;
		}
		$ar[$r[2]][$r[0]] = $r[1];
	}

	q('UPDATE '.$tbl.'forum SET moderators=NULL');
	if (isset($ar)) {
		foreach ($ar as $k => $v) {
			q('UPDATE '.$tbl.'forum SET moderators='.strnull(addslashes(@serialize($v))).' WHERE id='.$k);
		}
	}
	q('UPDATE '.$tbl.'users SET users_opt=users_opt & ~ 524288 WHERE users_opt>=524288 AND (users_opt & 524288) > 0');
	if (isset($u)) {
		q('UPDATE '.$tbl.'users SET users_opt=users_opt|524288 WHERE id IN('.implode(',', $u).') AND (users_opt & 1048576)=0');
	}
}
?>