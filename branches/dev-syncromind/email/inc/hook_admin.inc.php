<?php
	/**
	* EMail - Admin hook
	*
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package email
	* @subpackage hooks
	* @version $Id$
	*/
{
// Only Modify the $file and $title variables.....
	$title = $appname;
	$file = array
	(
		'Site Configuration'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiconfig.index', 'appname' => $appname) )
	);
//Do not modify below this line
	display_section($appname,$title,$file);
}
?>
