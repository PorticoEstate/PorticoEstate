<?php
/**************************************************************************\
* phpGroupWare - Antispam                                                  *
* http://www.phpgroupware.org                                              *
* This application written by:                                             *
*                             Marco Andriolo-Stagno <stagno@prosa.it>      *
*                             PROSA <http://www.prosa.it>                  *
* -------------------------------------------------------------------------*
* Funding for this program was provided by http://www.seeweb.com           *
* -------------------------------------------------------------------------*
*  This program is free software; you can redistribute it and/or modify it *
*  under the terms of the GNU General Public License as published by the   *
*  Free Software Foundation; either version 2 of the License, or (at your  *
*  option) any later version.                                              *
 \**************************************************************************/

/* $Id: index.php 11580 2002-11-26 17:57:08Z ceb $ */

	$GLOBALS['phpgw_info']['flags'] = array
	(
		'currentapp'	=>	'antispam',
		'enable_browser_class'	=>	True,
		'noheader'	=>	True
	 );
	include('../header.inc.php');
	
	$GLOBALS['phpgw']->common->phpgw_header();

	$antispam = CreateObject('antispam.checker');
	$antispam->handle_settings(FALSE);

?>
