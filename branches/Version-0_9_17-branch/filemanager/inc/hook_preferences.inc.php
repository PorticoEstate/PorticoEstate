<?php
	/***
	* Filemanager preferencest hook
	* @author Bettina Gille <ceb@phpgroupware.org>
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package filemanager
	* @version $Id: hook_preferences.inc.php 17909 2007-01-24 17:26:17Z Caeies $
	*/

	{
		$file = array('Preferences'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'filemanager.uifilemanager.preferences')));
					//'Grant Access'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uiaclprefs.index', 'acl_app' => $appname)));
		//Do not modify below this line
		display_section($appname,$file);
	}
?>
