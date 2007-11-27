<?php
	/**
	* Trouble Ticket System
	*
	* @author Mark Peters <skeeter@phpgroupware.org>
	* @copyright Copyright (C) 2001,2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package tts
	* @subpackage hooks
	* @version $Id: hook_manual.inc.php 15932 2005-05-10 16:12:38Z powerstat $
	*/

// Only Modify the $file variable.....
	$file = Array(
		'View'	=>	'view.php',
		'Create'	=> 'create.php',
		'Edit/Close'	=> 'edit_close.php'
	);
// Do not modify below this line
	display_manual_section($appname,$file);
?>
