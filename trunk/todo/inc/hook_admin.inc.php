<?php
	/**
	* Todo - admin hook
	*
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package todo
	* @subpackage hooks
	* @version $Id: hook_admin.inc.php 17106 2006-09-09 09:04:58Z skwashd $
	* @internal $Source$
	*/

	$file = array
	(
		'Global Categories'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicategories.index', 'appname' => 'todo', 'global_cats' => 'True') )
	);
	$GLOBALS['phpgw']->common->display_mainscreen($appname,$file);
?>
