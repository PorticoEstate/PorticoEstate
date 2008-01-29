<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: index.php 13837 2003-11-01 22:57:15Z skwashd $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

	/* $Id: index.php 13837 2003-11-01 22:57:15Z skwashd $ */

	ignore_user_abort(true);
	set_magic_quotes_runtime(0);

	/* security check to prevent execution */
	if (strpos(realpath(__FILE__), 'setup/index.php') !== false) {
		exit;
	}

	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp'	=>	'fudforum',
		'noheader'	=>	true,
		'nonavbar'	=>	true,
		'noappheader'	=>	true,
		'noappfooter'	=>	true,
		'nofooter'	=>	true
	);
	
	require_once('../header.inc.php');

	/* sanity checks, if any of these are true, notify the user & abort the process */
	if (empty($GLOBALS['phpgw_info']['server']['files_dir'])) {
		exit("Please make sure that that 'files/' directory exists & is writeable.");
	} else if (!is_writeable($GLOBALS['phpgw_info']['server']['files_dir'])) {
		exit("The 'files/' ({$GLOBALS['phpgw_info']['server']['files_dir']}) directory exists, however webserver has no write permissions to that directory.");
	}
	if (!is_writeable(PHPGW_SERVER_ROOT."/fudforum")) {
		$check_list = array('blank.gif', 'index.php', 'lib.js', 'adm', 'images', 'rdf.php', 'pdf.php', 'theme', 'GLOBALS.php');
		$path = PHPGW_SERVER_ROOT."/fudforum/";
		foreach ($check_list as $f) {
			if (!is_writeable($path.$check_list)) {
				echo <<< FUD_ERR

FUDforum installation requires write permission to the following files &amp; directories:<br /><br />
Files: {$path}blank.gif, {$path}index.php, {$path}lib.js, {$path}rdf.php, {$path}pdf.php, {path}GLOBALS.php<br />
Directories: {$path}adm, {$path}theme, {$path}images<br />
<br /><br />
You can add the necessary permissions by performing one of the following commands:<br /><br />
touch {$path}blank.gif {$path}index.php {$path}lib.js {$path}rdf.php {$path}pdf.php {path}GLOBALS.php<br />
chmod 666 {$path}blank.gif {$path}index.php {$path}lib.js {$path}rdf.php {$path}pdf.php {path}GLOBALS.php<br />
mkdir {$path}adm {$path}theme {$path}images<br />
chmod 777 {$path}adm {$path}theme {$path}images<br />
<br /><br />
<b>OR</b><br />
chmod 777 {$path}

FUD_ERR;
				exit;
			}
		}
	}

if (!function_exists('file_get_contents')) {
	function file_get_contents($fname)
	{
		if (!($fp = @fopen($fname, 'rb'))) {
			return false;
		}
		$data = fread($fp, filesize($fname));
		fclose($fp);
		return $data;
	}
}

	/* Create Directories needed for FUDforum Operation */
	$fud_write_dir		= $GLOBALS['phpgw_info']['server']['files_dir'] . "/fudforum";
	$DATA_DIR		= $fud_write_dir . "/";
	$INCLUDE		= $fud_write_dir . "/include/";
	$ERROR_PATH		= $fud_write_dir . "/errors/";
	$TMP			= $fud_write_dir . "/tmp/";
	$FILE_STORE		= $fud_write_dir . "/files/";
	$FORUM_SETTINGS_PATH	= $fud_write_dir . "/cache/";
	$MSG_STORE_DIR		= $fud_write_dir . "/messages/";
	$WWW_ROOT_DISK		= PHPGW_SERVER_ROOT."/fudforum/";
	$WWW_ROOT		= $GLOBALS['phpgw_info']['server']['webserver_url']."/fudforum/";

	$u = umask(0);

	/* Create non-web directories needed for FUDforum operation */
	$dir_ar = array('include', 'src', 'errors', 'messages', 'files', 'thm', 'sql', 'tmp', 'cache', 'errors/.nntp', 'errors/.mlist');
	if (!is_dir($fud_write_dir)) {
		mkdir($fud_write_dir, 0700);
	}
	while (list(,$d) = each($dir_ar)) {
		if (!is_dir("{$fud_write_dir}/{$d}")) {
			mkdir("{$fud_write_dir}/{$d}", 0700);
		}
		if (is_dir(PHPGW_SERVER_ROOT."/fudforum/setup/base/{$d}")) {
			$dir = opendir(PHPGW_SERVER_ROOT."/fudforum/setup/base/{$d}");
			readdir($dir); readdir($dir);
			while ($f = readdir($dir)) {
				if (!is_dir(PHPGW_SERVER_ROOT."/fudforum/setup/base/{$d}/{$f}")) {
					copy(PHPGW_SERVER_ROOT."/fudforum/setup/base/{$d}/{$f}", "{$DATA_DIR}{$d}/{$f}");
					chmod("{$DATA_DIR}{$d}/{$f}", 0600);
				} else {
					$dir_ar[] = "{$d}/{$f}";
				}
			}
			closedir($dir);
		}
	}

	/* Create web directories & files needed FUDforum operations */
	copy(PHPGW_SERVER_ROOT."/fudforum/setup/base/www_root/blank.gif", PHPGW_SERVER_ROOT."/fudforum/blank.gif");
	copy(PHPGW_SERVER_ROOT."/fudforum/setup/base/www_root/lib.js", PHPGW_SERVER_ROOT."/fudforum/lib.js");
	$dir_ar = array('adm', 'images');
	while (list(,$d) = each($dir_ar)) {
		if (!is_dir("{$WWW_ROOT_DISK}/{$d}")) {
			mkdir("{$WWW_ROOT_DISK}/{$d}", 0700);
		}
		if (is_dir(PHPGW_SERVER_ROOT."/fudforum/setup/base/www_root/{$d}")) {
			$dir = opendir(PHPGW_SERVER_ROOT."/fudforum/setup/base/www_root/{$d}");
			readdir($dir); readdir($dir);
			while ($f = readdir($dir)) {
				if (!is_dir(PHPGW_SERVER_ROOT."/fudforum/setup/base/www_root/{$d}/{$f}")) {
					copy(PHPGW_SERVER_ROOT."/fudforum/setup/base/www_root/{$d}/{$f}", "{$WWW_ROOT_DISK}{$d}/{$f}");
					chmod("{$WWW_ROOT_DISK}{$d}/{$f}", 0600);
				} else {
					$dir_ar[] = "{$d}/{$f}";
				}
			}
			closedir($dir);
		}
	}

	/* symlinks to GLOBALS.php */
	if (function_exists("symlink")) {
		@unlink("{$WWW_ROOT_DISK}GLOBALS.php");
		@unlink("{$WWW_ROOT_DISK}adm/GLOBALS.php");
		symlink("{$INCLUDE}GLOBALS.php", "{$WWW_ROOT_DISK}GLOBALS.php");
		symlink("{$INCLUDE}GLOBALS.php", "{$WWW_ROOT_DISK}adm/GLOBALS.php");
	} else {
		$fp = fopen("{$WWW_ROOT_DISK}GLOBALS.php", "w");
		fwrite($fp, '<?php require "'.$INCLUDE.'GLOBALS.php"; ?>');
		fclose($fp);

		$fp = fopen("{$WWW_ROOT_DISK}adm/GLOBALS.php", "w");
		fwrite($fp, '<?php require "'.$INCLUDE.'GLOBALS.php"; ?>');
		fclose($fp);
	}

	/* Modify FUDforum Configuration Parameters options */
	$fud_set = array(
		'INCLUDE' => $INCLUDE,
		'WWW_ROOT' => $WWW_ROOT,
		'WWW_ROOT_DISK' => $WWW_ROOT_DISK,
		'DATA_DIR' => $DATA_DIR,
		'ERROR_PATH' => $ERROR_PATH,
		'MSG_STORE_DIR' => $MSG_STORE_DIR,
		'TMP' => $TMP,
		'FILE_STORE' => $FILE_STORE,
		'FORUM_SETTINGS_PATH' => $FORUM_SETTINGS_PATH
	);
	require("{$INCLUDE}glob.inc");
	change_global_settings($fud_set);

	/* create default theme */
	$langl = array('bg'=>'bulgarian', 'zh'=>'chinese_big5', 'cs'=>'czech', 'nl'=>'dutch', 'fr'=>'french', 'de'=>'german', 'it'=>'italian', 'lv'=>'latvian', 'no'=>'norwegian', 'pl'=>'polish', 'pt'=>'portuguese', 'ro'=>'romanian', 'ru'=>'russian', 'sk'=>'slovak', 'es'=>'spanish', 'sv'=>'swedish', 'tr'=>'turkish');
	if (isset($langl[$GLOBALS['phpgw_info']['user']['preferences']['common']['lang']])) {
		$lang = $langl[$GLOBALS['phpgw_info']['user']['preferences']['common']['lang']];
	} else {
		$lang = 'english';
	}

	$locale = trim(fread(fopen("{$DATA_DIR}/thm/default/i18n/{$lang}/locale", "r"), 1024));
	$pspell_lang = trim(fread(fopen("{$DATA_DIR}/thm/default/i18n/{$lang}/pspell_lang", "r"), 1024));

	$GLOBALS['phpgw']->db->query("DELETE FROM phpgw_fud_themes");
	$GLOBALS['phpgw']->db->query("INSERT INTO phpgw_fud_themes(name, theme, lang, locale, theme_opt, pspell_lang) VALUES('default', 'default', '{$lang}', '{$locale}', 3, '{$pspell_lang}')");
	$theme = $GLOBALS['phpgw']->db->get_last_insert_id('phpgw_fud_themes', 'id');

	/* this is a little tricky, basically it makes sure that any users created before the forum
	 * was activated with the default theme of 1, have the correct theme, in case the primary theme
	 * id != 1
	 */
	if ($theme != 1) {
		$GLOBALS['phpgw']->db->query("UPDATE phpgw_fud_users SET theme={$theme}");
	}

	/* compile default theme */
	define('__dbtype__', $GLOBALS['phpgw']->db->type);
	$DBHOST_TBL_PREFIX	= "phpgw_fud_";
	require("{$INCLUDE}/compiler.inc");
	compile_all('default', $lang);

	/* Create an Acccount for every existing phpgw user in the forum */
	$GLOBALS['phpgw']->db->query("DELETE FROM phpgw_fud_users WHERE id>1");
	$users = $GLOBALS['phpgw']->accounts->get_list('accounts', 0,'ASC','account_lid', '');
	foreach ($users as $row) {
		$preferences = CreateObject('phpgwapi.preferences', $row['account_id']);
		$preferences->read_repository();
		$email = $preferences->email_address($row['account_id']);
		$email = $GLOBALS['phpgw']->db->db_addslashes($email);

		$name = $GLOBALS['phpgw']->db->db_addslashes($row['account_firstname'] . ' ' . $row['account_lastname']);
		$phpgw_id = $row['account_id'];
		$alias = $GLOBALS['phpgw']->db->db_addslashes(htmlspecialchars($row['account_lid']));
		$login = $GLOBALS['phpgw']->db->db_addslashes($row['account_lid']);
		$users_opt = 2|4|16|32|64|128|256|512|2048|4096|8192|16384|131072|4194304;
		if ($row['account_status'] != 'A') {
			$user_opts |= 2097152;
		}
		$GLOBALS['phpgw']->db->query("INSERT INTO phpgw_fud_users (last_visit, join_date, theme, alias, login, email, passwd, name, users_opt, phpgw_id) VALUES(".time().", ".time().", {$theme}, '{$alias}', '{$login}', '{$email}', '{$row['account_pwd']}', '{$name}', {$users_opt}, {$phpgw_id})");
	}

	header('Location: '.$WWW_ROOT.'index.php?S='.$_GET['sessionid']);
	$GLOBALS['phpgw']->common->phpgw_exit();
?>
