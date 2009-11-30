<?php
	/**************************************************************************\
	* phpGroupWare - News                                                      *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	/* Basic information about this app */
	$setup_info['news_admin']['name']      = 'news_admin';
	//$setup_info['news_admin']['title']     = 'News Admin';
	$setup_info['news_admin']['version']   = '0.9.17.503';
	$setup_info['news_admin']['app_order'] = 4;
	$setup_info['news_admin']['enable']    = 1;
	$setup_info['news_admin']['app_group']	= 'accessories';

	/* The tables this app creates */
	$setup_info['news_admin']['tables']    = array('phpgw_news','phpgw_news_export');

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['news_admin']['hooks'][] = 'admin';
	$setup_info['news_admin']['hooks'][] = 'home';
	$setup_info['news_admin']['hooks'][] = 'sidebox_menu';
	$setup_info['news_admin']['hooks']['cat_add'] = 'news_admin.news_admin_bo_hooks.cat_add';
	$setup_info['news_admin']['hooks']['cat_delete'] = 'news_admin.news_admin_bo_hooks.cat_delete';
	$setup_info['news_admin']['hooks']['cat_edit'] = 'news_admin.news_admin_bo_hooks.cat_edit';

	$setup_info['news_admin']['depends'][] = array(
		 'appname' => 'phpgwapi',
		 'versions' => Array('0.9.16', '0.9.17', '0.9.18')
	);



