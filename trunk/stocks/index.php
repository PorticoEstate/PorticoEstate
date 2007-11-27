<?php
	/**************************************************************************\
	* phpGroupWare - Stock Quotes                                              *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	* This program is free software; you can redistribute it and/or modify it  *
	* under the terms of the GNU General Public License as published by the    *
	* Free Software Foundation; either version 2 of the License, or (at your   *
	* option) any later version.                                               *
	\**************************************************************************/
	/* $Id: index.php 17904 2007-01-24 16:13:29Z Caeies $ */

	$GLOBALS['phpgw_info'] = array();

	$GLOBALS['phpgw_info']['flags'] = array
	(
		'currentapp' => 'stocks',
		'noheader'   => True,
		'nonavbar'   => True
	);
	include('../header.inc.php');
	$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'stocks.uistock.index'));
?>
