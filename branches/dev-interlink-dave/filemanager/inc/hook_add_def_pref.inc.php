<?php
	/***
	* Filemanager add def preferences hook
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package filemanager
	* @version $Id$
	*/

	$GLOBALS['pref']->change ('filemanager','name','name');
	$GLOBALS['pref']->change ('filemanager','size','size');
	$GLOBALS['pref']->change ('filemanager','created','created');
	$GLOBALS['pref']->change ('filemanager','version','version');
	$GLOBALS['pref']->change ('filemanager','mime_type','mime_type');
	$GLOBALS['pref']->change ('filemanager','comment','comment');
	$GLOBALS['pref']->change ('filemanager','viewtextplain','viewtextplain');
	$GLOBALS['pref']->change ('filemanager','show_help','show_help');
	$GLOBALS['pref']->change ('filemanager','show_upload_boxes','5');
?>
