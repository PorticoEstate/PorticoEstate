<?php
	/**************************************************************************\
	* phpGroupWare - administration                                            *
	* http://www.phpgroupware.org                                              *
	* Written by coreteam <phpgroupware-developers@gnu.org>                    *
	*               & Stephen Brown <steve@dataclarity.net>                    *
	* to distribute admin across the application directories                   *
	* ------------------------------------------------------                   *
	* This program is free software; you can redistribute it and/or modify it  *
	* under the terms of the GNU General Public License as published by the    *
	* Free Software Foundation; either version 2 of the License, or (at your   *
	* option) any later version.                                               *
	\**************************************************************************/
	/* $Id$ */

	$GLOBALS['phpgw_info']['flags'] = array
	(
		'noheader'	=> True,
		'currentapp'	=> 'admin'
	);
	include('../header.inc.php');

	$GLOBALS['phpgw']->redirect_link( '/index.php', array('menuaction' => 'admin.uimainscreen.mainscreen') );
?>
