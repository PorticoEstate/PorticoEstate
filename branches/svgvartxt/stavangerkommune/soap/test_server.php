<?php
/**************************************************************************\
* phpGroupWare - addressbook                                               *
* http://www.phpgroupware.org                                              *
* Written by Joseph Engo <jengo@phpgroupware.org>                          *
* --------------------------------------------                             *
*  This program is free software; you can redistribute it and/or modify it *
*  under the terms of the GNU General Public License as published by the   *
*  Free Software Foundation; either version 2 of the License, or (at your  *
*  option) any later version.                                              *
\**************************************************************************/

/* $Id$ */
/*
	$phpgw_info['flags'] = array(
		'currentapp' => 'soap',
		'noheader' => True
	);

	include('../header.inc.php');
*/
	$login  = 'anonymous';
	$passwd = 'anonymous1';

	$phpgw_info['flags'] = array(
		'disable_template_class' => True,
		'login' => True,
		'currentapp' => 'login',
		'noheader'  => True);

	include('../header.inc.php');
	include('./vars.php');

	//$sessionid = $phpgw->session->create($login,$passwd);

	$server = CreateObject('phpgwapi.soap_server');

	include('./soaplib.soapinterop.php');

	$server->service($HTTP_RAW_POST_DATA);
?>
