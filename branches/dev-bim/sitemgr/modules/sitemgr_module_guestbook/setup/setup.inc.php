<?php
	/**************************************************************************\
	* phpGroupWare - Sitemgr                                                   *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	$setup_info['sitemgr_module_guestbook']['name']      = 'sitemgr_module_guestbook';
	$setup_info['sitemgr_module_guestbook']['title']     = 'Guestbook for SiteMgr';
	$setup_info['sitemgr_module_guestbook']['version']   = '0.1';
	$setup_info['sitemgr_module_guestbook']['app_order'] = 0;
	$setup_info['sitemgr_module_guestbook']['app_group'] = 'accessories';
	
	$setup_info['sitemgr_module_guestbook']['tables']    = array(
		'phpgw_sitemgr_module_guestbook_books','phpgw_sitemgr_module_guestbook_entries'
	);
	$setup_info['sitemgr_module_guestbook']['enable']    = 1;

	/* Dependacies for this app to work */
	$setup_info['sitemgr_module_guestbook']['depends'][] = array(
		'appname'  => 'sitemgr',
		'versions' => Array('0.9.16', '0.9.17', '0.9.18')
	);
?>
