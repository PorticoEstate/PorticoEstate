<?php
	/**************************************************************************\
	* phpGroupWare - FeLaMiMail                                              *
	* http://www.phpgroupware.org                                              *
	* http://www.phpgw.de                                                      *
	* http://www.linux-at-work.de                                              *
	* Written by Lars Kneschke [lkneschke@linux-at-work.de]                    *
	* -----------------------------------------------                          *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/
	/* $Id: index.php,v 1.5 2006/12/18 20:03:32 sigurdne Exp $ */

	$phpgw_info['flags'] = array
	(
		'currentapp' => 'felamimail',
		'noheader'   => True,
		'nonavbar'   => True
	);
	include('../header.inc.php');

	$obj = CreateObject('felamimail.uifelamimail');
	$obj->viewMainScreen();
?>
