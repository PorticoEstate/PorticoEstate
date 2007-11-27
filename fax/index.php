<?php
/**************************************************************************\
* phpGroupWare - fax                                                       *
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

 /* $Id: index.php 11615 2002-12-05 15:08:27Z stagno $ */

	$GLOBALS['phpgw_info']['flags'] = array
  	(
	 'currentapp'	=>	'fax',
	 'enable_browser_class'	=>	True,
	 'noheader'	=>	True
	 );

	include('../header.inc.php');

	$GLOBALS['phpgw']->common->phpgw_header();

	$fax = CreateObject('fax.manager');
	$fax->compose();

	$GLOBALS['phpgw']->common->phpgw_footer();
?>
