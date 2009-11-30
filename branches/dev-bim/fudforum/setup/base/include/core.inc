<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: core.inc 13837 2003-11-01 22:57:15Z skwashd $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

$FORUM_VERSION = "2.6.0 (FUD phpGW)";

ignore_user_abort(true);
set_magic_quotes_runtime(0);

function rls_db_lck()
{
	if (connection_status() && defined('fud_sql_lnk') && db_locked()) {
		db_unlock();
	}
}

register_shutdown_function('rls_db_lck');

$GLOBALS['TITLE_EXTRA'] = '';

if (get_magic_quotes_gpc()) {
	function strip_quotes(&$var)
	{
        	if (is_array($var)) {
			array_walk($var, 'strip_quotes');
		} else {
			$var = stripslashes($var);
		}
	}

	if (@count($_GET)) {
		array_walk($_GET, 'strip_quotes');
	}
        if (@count($_POST)) {
		array_walk($_POST, 'strip_quotes');
	}
        if (@count($_COOKIES)) {
		array_walk($_COOKIES, 'strip_quotes');
	}
	if (@count($_FILES)) {
		array_walk($_FILES, 'strip_quotes');
	}
}

$GLOBALS['PAGE_TIME'] = gettimeofday();
define('__request_timestamp__', $GLOBALS['PAGE_TIME']['sec']);
mt_srand($GLOBALS['PAGE_TIME']['usec']);

function fud_use($file, $static=false)
{
	if ($static) {
		include_once $GLOBALS['INCLUDE'] . $file;
		return;
	}
	include_once $GLOBALS['INCLUDE'] . (defined('fud_theme') ? fud_theme : 'theme/default/') . $file;
}

function get_random_value($bitlength=32)
{
	$n = round($bitlength/32);
	$v='';
	while (--$n > -1) {
		$v .= mt_rand();
	}
	return $v;
}

function get_ip()
{
	if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = strtok($_SERVER['HTTP_X_FORWARDED_FOR'], ',');
		do {
			if (($lip = ip2long($ip)) === -1) {
				continue;
			}
			/* some disallowed ip ranges, that are normally used for private networks
			 * 10.0.0.0 - 10.255.255.255
			 * 172.16.0.0 - 172.31.255.255
			 * 192.168.0.0 - 192.168.255.255
			 * 127.0.0.1, 255.255.255.255, 0.0.0.0
			*/
			if (!$lip || (($lip >= 167772160) && ($lip <= 184549375)) || (($lip >= -1408237568) && ($lip <= -1407188993)) || (($lip >= -1062731776) && ($lip <= -1062666241)) || ($lip == 2130706433)) {
				continue;
			}
			return $ip;
		} while (($ip = strtok(',')));
	}
	return (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0');
}

function __ffilesize($fp)
{
	$st = fstat($fp);
	return (isset($st['size']) ? $st['size'] : $st[7]);
}

if (!function_exists('file_get_contents')) {
	function file_get_contents($fname)
	{
		if (!($fp = @fopen($fname, 'rb'))) {
			return false;
		}
		/* this handles urls */
		if (!($size = @__ffilesize($fp))) {
			$size = 2 * 1024 * 1024;
		}
		$data = fread($fp, $size);
		fclose($fp);
		return $data;
	}
}

function fud_phpgw($t='index', $plain=0)
{
	global $sessionid;

	/* Import session id */
	$sessionid = null;
        if (isset($_GET['S'])) {
		$sessionid = $_GET['S'];
	} else if (isset($_GET['sessionid'])) {
		$sessionid = $_GET['sessionid'];
	}
	if (isset($_POST['S'])) {
		$sessionid = $_POST['S'];
	}
	if (isset($_COOKIE['sessionid'])) {
		$sessionid = $_COOKIE['sessionid'];
	}

        /* validate session id */
	if ($sessionid && preg_match('![^A-Za-z0-9]!', $sessionid)) {
		$sessionid = null;
	}

	$_COOKIE['sessionid'] = $_GET['sessionid'] = $sessionid;

	$GLOBALS['phpgw_info']['flags'] = array('currentapp' => 'fudforum', 'noappheader' => true, 'noappfooter' => true, 'nofooter' => false, 'noheader' => true, 'nonavbar' => true);
	$GLOBALS['phpgw_info']['flags']['java_script'] = '<script language="javascript" src="'.$GLOBALS['WWW_ROOT'].'lib.js" type="text/javascript"></script>';

	if (!$sessionid) {
		$GLOBALS['phpgw_info']['flags']['login'] = true;
		/* In order to create a session, currentapp must be 'login' */
		$GLOBALS['phpgw_info']['flags']['currentapp'] = 'login';
	}

	require_once($GLOBALS['WWW_ROOT_DISK'].'../header.inc.php');

	/* anon user handling */
	if ($GLOBALS['ANON_NICK'] && !$GLOBALS['phpgw']->session->verify()) {
		$sessionid = $GLOBALS['phpgw']->session->create($GLOBALS['ANON_NICK'], $GLOBALS['ANON_PASS']);
		/* still no session, failed to create session for anon user */
		if (!$sessionid) {
			echo "<p>Can't create session for user '".$GLOBALS['ANON_NICK']."' !!!</p>\n";
			$GLOBALS['phpgw']->common->phpgw_exit();
		}
		if (isset($_SERVER['PHP_SELF'], $_SERVER['QUERY_STRING'])) {
			header("Location: ".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."&S=".$GLOBALS['phpgw']->session->sessionid);
		} else {
			header("Location: ".$GLOBALS['WWW_ROOT']."index.php?S=".$GLOBALS['phpgw']->session->sessionid);
		}
		exit;
	}

	/* define constants */
	define('s', $sessionid); define('_hs', '<input type="hidden" name="S" value="'.$sessionid.'">');
	define('_rsid', 'S='.$sessionid); define('_rsidl', 'S='.$sessionid);
	define('fud_sql_lnk', $GLOBALS['phpgw']->db->Link_ID);

	if (!$plain) {
		ob_start();
		$GLOBALS['phpgw']->common->phpgw_header();
		/* nav bar is for actual users only */
		if ($GLOBALS['phpgw_info']['user']['account_lid'] && $GLOBALS['ANON_NICK'] != $GLOBALS['phpgw_info']['user']['account_lid']) {
			parse_navbar();
		}
		$GLOBALS['fud_phpgw_hdr'] = ob_get_contents();
		ob_end_clean();
	} else {
		$GLOBALS['fud_phpgw_hdr'] = null;
	}
}
?>
