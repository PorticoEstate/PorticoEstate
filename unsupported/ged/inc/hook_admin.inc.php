<?php
	/**************************************************************************
	* phpGroupWare - ged
	* http://www.phpgroupware.org
	* Written by Pascal Vilarem <pascal.vilarem@steria.org>
	*
	* --------------------------------------------------------------------------
	*  This program is free software; you can redistribute it and/or modify it
	*  under the terms of the GNU General Public License as published by the
	*  Free Software Foundation; either version 2 of the License, or (at your
	*  option) any later version
	***************************************************************************/

  {
// Only Modify the $file and $title variables.....
    $file = array
    (
      'Types Configuration' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'ged.ged_admin.types', 'appname' => 'ged') ),
      'Places Configuration' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'ged.ged_admin.places', 'appname' => 'ged') )
    );
//Do not modify below this line
	$GLOBALS['phpgw']->common->display_mainscreen($appname,$file);
  }
?>
