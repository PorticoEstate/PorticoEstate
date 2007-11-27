<?php
	/**
	* Todo - admin hook
	*
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2000-2002,2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package todo
	* @subpackage hooks
	* @version $Id: hook_preferences.inc.php 17520 2006-10-31 10:39:36Z sigurdne $
	*/

	{
// Only Modify the $file and $title variables.....

		$file = Array
		(
			'Preferences' => $GLOBALS['phpgw']->link('/index.php',array	('menuaction'=>'todo.uipreferences.preferences')),
			'Grant Access'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'preferences.uiadmin_acl.aclprefs', 'acl_app'=>$appname)),
			'Edit categories' => $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'preferences.uicategories.index','cats_app'=>$appname,'cats_level'=>'True','global_cats'=>'True'))
		);
// Do not modify below this line
		display_section($appname,$file);
	}
?>
