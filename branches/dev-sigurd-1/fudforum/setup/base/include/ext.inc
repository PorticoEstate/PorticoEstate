<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: ext.inc 13837 2003-11-01 22:57:15Z skwashd $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

function ext_cache_rebuild()
{
	$c = uq('SELECT ext FROM '.$GLOBALS['DBHOST_TBL_PREFIX'].'ext_block');
	while ($r = db_rowarr($c)) {
		$a[] = strtolower(addcslashes($r[0], '\'\\'));
	}

	$data = isset($a) ? "'" . implode("', '", $a) . "'" : '';

	$fp = fopen($GLOBALS['FORUM_SETTINGS_PATH'] . 'file_filter_regexp', 'w');
	fwrite($fp, '<?php $GLOBALS[\'__FUD_EXT_FILER__\'] = array(' . $data . '); ?>');
	fclose($fp);
}
?>