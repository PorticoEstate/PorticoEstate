<?php
	/**************************************************************************\
	* phpGroupWare - Webpage news admin                                        *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	* --------------------------------------------                             *
	* This program was sponsered by Golden Glair productions                   *
	* http://www.goldenglair.com                                               *
	\**************************************************************************/

	/* $Id: index.php 16046 2005-08-04 03:54:33Z skwashd $ */

	$phpgw_info = array();
	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'news_admin',
		'noheader' => True,
		'nonavbar' => True,
	);

	include_once('../header.inc.php');

	$ui= createobject('news_admin.uinews');
	$ui->read_news();

	$GLOBALS['phpgw']->common->phpgw_footer();
?>
