<?php
	/***
	* Filemanager admin hook
	* @author Bettina Gille <ceb@phpgroupware.org>
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package filemanager
	* @version $Id$
	*/

	{
// Only Modify the $file and $title variables.....
		$file = array
		(
			'site configuration'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'filemanager.uifilemanager.admin') ),
			'edit user menu actions'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'filemanager.uifilemanager.edit_actions') )
		);
// Do not modify below this line
	$GLOBALS['phpgw']->common->display_mainscreen($appname,$file);
	}
?>
