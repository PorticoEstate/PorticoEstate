<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: admsync.php 13837 2003-11-01 22:57:15Z skwashd $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

	require('./GLOBALS.php'); fud_phpgw();
	fud_use('adm.inc', true);
	fud_use('compiler.inc', true);

function copy_dir($base, $dest, $dir_ar)
{
	while (list(,$d) = each($dir_ar)) {
		echo "&nbsp;&nbsp;&nbsp;Syncronizing: {$d}<br />\n";
		if (is_dir($base . $d)) {
			$dir = opendir($base . $d);
			readdir($dir); readdir($dir);
			while ($f = readdir($dir)) {
				if (!is_dir("{$base}{$d}/{$f}") && $f != 'GLOBALS.php') {
					copy("{$base}{$d}/{$f}", "{$dest}{$d}/{$f}");
					chmod("{$dest}{$d}/{$f}", 0600);
				} else if ($f != 'CVS') {
					$dir_ar[] = "{$d}/{$f}";
				}
			}
			closedir($dir);
		}
	}	
}
	require($WWW_ROOT_DISK . 'adm/admpanel.php');

	echo "Syncronizing FUDforum sources!<br />\n";

	/* data directories */
	copy_dir(PHPGW_SERVER_ROOT."/fudforum/setup/base/", $DATA_DIR, array('include', 'src', 'thm'));

	/* web directories */
	copy_dir(PHPGW_SERVER_ROOT."/fudforum/setup/base/www_root/", $WWW_ROOT_DISK, array('adm', 'images'));

	$remove_list = array(
		'thm/default/tmpl/admincp.tmpl',
		'thm/default/tmpl/curtime.tmpl',
		'src/admincp.inc.t'
	);

	/* remove old files */
	foreach ($remove_list as $f) {
		@unlink($DATA_DIR . $f);
	}

	/* recompile all enabled themes */
	$c = uq("SELECT theme, lang, name FROM ".$DBHOST_TBL_PREFIX."themes WHERE (theme_opt > 0) AND (theme_opt & 1) > 0");
	while ($r = db_rowobj($c)) {
		compile_all($r->theme, $r->lang, $r->name);
	}

	echo "FUDforum sources are now syncronized!<br />\n";

	require($WWW_ROOT_DISK . 'adm/admclose.php'); 
?>
