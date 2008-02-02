<?php
	/**************************************************************************\
	* phpGroupWare                                                             *
	* http://www.phpgroupware.org                                              *
	* Written by Joseph Engo <jengo@phpgroupware.org>                          *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	// $Id$
	// $Source$

	// Only Modify the $file and $title variables.....
	$title = $appname;
	$file = array
	(
		'Site Configuration'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiconfig.index', 'appname' => 'addressbook') ),
		'Edit custom fields'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uifields.index') ),
		'Global Categories'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicategories.index', 'appname' => 'addressbook') ),
		'Communication Types Manager'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uicatalog_contact_comm_type.view') ),
		'Communication Descriptions Manager' =>  $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uicatalog_contact_comm_descr.view') ),
		'Location Manager'				=>  $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uicatalog_contact_addr_type.view') ),
		'Notes Types Manager'			=>  $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uicatalog_contact_note_type.view') )
	);
	//Do not modify below this line
	$GLOBALS['phpgw']->common->display_mainscreen($appname,$file);
?>
