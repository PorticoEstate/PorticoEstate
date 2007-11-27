<?php
	/**
	* Project Manager
	*
	* @author Bettina Gille [ceb@phpgroupware.org]
	* @copyright Copyright (C) 2000-2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package projects
	* @version $Id: hook_admin.inc.php,v 1.26 2006/12/05 19:40:45 sigurdne Exp $
	* $Source: /sources/phpgroupware/projects/inc/hook_admin.inc.php,v $
	*/

	{
// Only Modify the $file and $title variables.....
		$file = array
		(
			'Site Configuration'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=>'admin.uiconfig.index','appname'=> $appname)),
			'Worktime statusmail'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=>'projects.uiconfig.config_worktime_statusmail')),
			'Worktime warnmail'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=>'projects.uiconfig.config_worktime_warnmail')),
			'Workhours booking'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=>'projects.uiconfig.config_workhours_booking')),
			'managing committee'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=>'projects.uiconfig.list_admins','action'=>'pmanager')),
			'project administrators'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=>'projects.uiconfig.list_admins','action'=>'pad')),
			'sales department'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=>'projects.uiconfig.list_admins','action'=>'psale')),
			'Global Categories'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=>'admin.uicategories.index','appname'=> $appname)),
			'edit project id help msg'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=>'projects.uiconfig.config_proid_help_msg')),
			'edit locations'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=>'projects.uiconfig.config_locations')),
			'accounting'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=>'projects.uiconfig.config_accounting'))
		);
//Do not modify below this line
		display_section($appname, $appname, $file);
	}
?>
