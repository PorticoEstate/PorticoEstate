<?php
	/**************************************************************************\
	* phpGroupWare - administration                                            *
	* http://www.phpgroupware.org                                              *
	* Written by Joseph Engo <jengo@phpgroupware.org>                          *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	$GLOBALS['phpgw_info']['flags'] = array(
		'admin_only'           => True,
		'currentapp'           => 'headlines',
		'enable_network_class' => True,
		'noheader'             => True,
		'nonavbar'             => True
	);
	include('../header.inc.php');

	$dropall = get_var('dropall',Array('GET'));

	$headlines = CreateObject('headlines.headlines');
	$headlines->getList($dropall);

	header('Location: ' . $GLOBALS['phpgw']->link('/headlines/admin.php'));
?>

