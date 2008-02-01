<?php
	/**
	* Bookmarks admin hook
	* @author Michael Totschnig
	* @copyright Copyright (C) 2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package bookmarks
	* @version $Id: hook_preferences.inc.php 17909 2007-01-24 17:26:17Z Caeies $
	*/

	$file = array(
		'Import Bookmarks' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bookmarks.ui.import')),
		'Export Bookmarks' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bookmarks.ui.export')),
		'Grant Access'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uiaclprefs.index', 'acl_app' => $appname)),
		'Edit Categories' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uicategories.index', 'cats_app' => $appname, 'global_cats' => 'True'))
	);
	display_section('bookmarks','Bookmarks',$file);
?>
