<?php
	/**************************************************************************\
	* phpGroupWare - SiteMgr                                                   *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: setup.inc.php 16493 2006-03-11 23:24:02Z skwashd $ */

	$setup_info['sitemgr-link']['name']	= 'sitemgr-link';
	$setup_info['sitemgr-link']['title']	= 'SiteMgr Public Web Site';
	$setup_info['sitemgr-link']['version']	= '0.9.13.001';
	$setup_info['sitemgr-link']['app_order']= 9;
	$setup_info['sitemgr-link']['tables']	= array();
	$setup_info['sitemgr-link']['enable']	= 1;
	$setup_info['sitemgr-link']['app_group']	= 'internet';

	$setup_info['sitemgr-link']['hooks'][]	= 'preferences';
	$setup_info['sitemgr-link']['hooks'][]	= 'settings';

	/* Dependacies for this app to work */
	$setup_info['sitemgr-link']['depends'][] = array(
		'appname'  => 'phpgwapi',
		'versions' => Array('0.9.16', '0.9.17', '0.9.18')
	);
	$setup_info['sitemgr-link']['depends'][] = array(
		'appname' => 'sitemgr',
		'versions' => array('0.9.16','0.9.17','0.9.18')
	);
?>
