<?php
/**************************************************************************\
* phpGroupWare - KnowledgeBase                                             *
* http://www.phpgroupware.org                                              *
*                                                                          *
* Copyright (c) 2003-2006 Free Sofware Foundation Inc                      *
* Written by Dave Hall skwashd at phpgropware.org                          *
* ------------------------------------------------------------------------ *
*  Started off as a port of phpBrain - http://vrotvrot.com/phpBrain/	   *
*  but quickly became a full rewrite					                   *
* ------------------------------------------------------------------------ *
*  This program is free software; you can redistribute it and/or modify it *
*  under the terms of the GNU General Public License as published by the   *
*  Free Software Foundation; either version 2 of the License, or (at your  *
*  option) any later version.                                              *
\**************************************************************************/

	/* $Id$ */

	{
		$file = array
		(
			'Site Configuration'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiconfig.index', 'appname' => 'phpbrain') ),
			'Global Categories'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicategories.index', 'appname' => 'phpbrain') )
		);
		$GLOBALS['phpgw']->common->display_mainscreen($appname,$file);
	}
?>
