<?php
  /**************************************************************************\
  * phpGroupWare - Administration                                            *
  * http://www.phpgroupware.org                                              *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/
  /* $Id$ */

	{
// Only Modify the $file and $title variables.....
		$file = array
		(
			'Site Configuration'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiconfig.index', 'appname' => $appname) ),
			'Backup Administration' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'backup.uibackup.backup_admin') )
		);

//Do not modify below this line
		$GLOBALS['phpgw']->common->display_mainscreen($appname,$file);
	}
?>
