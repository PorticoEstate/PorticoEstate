<?php
	/**************************************************************************\
	* phpGroupWare - administration                                            *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/
	/* $Id: phpinfo.php 17029 2006-08-29 15:33:54Z skwashd $ */

	$GLOBALS['phpgw_info']['flags'] = array
	(
		'currentapp'		=> 'admin',
		'menu_selection'	=> 'admin::admin::phpinfo'
	);

	if ( isset($_GET['noheader']) && $_GET['noheader'] )
	{
		$GLOBALS['phpgw_info']['flags'] = array
		(
			'nofooter'			=> true,
			'noframework'		=> true,
			'noheader'			=> true,
			'nonavbar'			=> true,
			'currentapp'		=> 'admin',
			'menu_selection'	=> 'admin::admin::phpinfo'
		);
	}

	include_once('../header.inc.php');
	phpinfo();
?>
