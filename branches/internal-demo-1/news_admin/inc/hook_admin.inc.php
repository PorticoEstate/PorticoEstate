<?php
  /**************************************************************************\
  * phpGroupWare                                                             *
  * http://www.phpgroupware.org                                              *
  * Written by Joseph Engo, Michael Totschnig  and Dave Hall                 *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/
  /* $Id: hook_admin.inc.php 16981 2006-08-19 05:35:19Z skwashd $ */
	{
		$file = array
		(
				'Site Configuration'	=> $GLOBALS['phpgw']->link('/index.php', 
								array
								(
									'menuaction'	=> 'admin.uiconfig.index',
									'appname'	=> 'news_admin'
								) ),
				'Global Categories'	=> $GLOBALS['phpgw']->link('/index.php',
								array
								(
									'menuaction'	=> 'admin.uicategories.index',
									'appname'	=> 'news_admin'
								) ),
				'configure access permissions'	=> $GLOBALS['phpgw']->link('/index.php', 
								array
								(
									'menuaction' => 'preferences.uiadmin_acl.list_acl',
									'acl_app' => 'news_admin'
								)),
				'Configure RSS exports' => $GLOBALS['phpgw']->link('/index.php',
								array
								(
									'menuaction'	=> 'news_admin.uiexport.exportlist'
								) )
		);
		display_section($appname,$appname,$file);
	}
?>
