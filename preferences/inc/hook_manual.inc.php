<?php
	/**
	* Preferences - manual hook
	*
	* @author Mark Peters <skeeter@phpgroupware.org>
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package preferences
	* @version $Id: hook_manual.inc.php 15840 2005-04-17 15:14:31Z powerstat $
	*/

// Only Modify the $file variable.....
	$file = Array(
		'Settings'	=> 'settings.php',
		'Other'		=> 'other.php'
	);
//Do not modify below this line
	display_manual_section($appname,$file);
?>
