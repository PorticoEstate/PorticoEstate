<?php
	/**************************************************************************\
	* phpGroupWare - NNTP Network News                                         *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: setup.inc.php 16493 2006-03-11 23:24:02Z skwashd $ */

	/* Basic information about this app */
	$setup_info['nntp']['name']		= 'nntp';
	$setup_info['nntp']['version']		= '0.9.13.002';
	$setup_info['nntp']['app_order']	= 9;
	$setup_info['nntp']['enable']		= 1;
	$setup_info['nntp']['app_group']	= 'internet';

	/* The tables this app creates */
	$setup_info['nntp']['tables']    = array(
		'newsgroups',
		'news_msg'
	);

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['nntp']['hooks'][] = 'preferences';
	$setup_info['nntp']['hooks'][] = 'admin';

	/* Dependencies for this app to work */
	$setup_info['nntp']['depends'][] = array(
		'appname' => 'phpgwapi',
		'versions' => Array('0.9.16', '0.9.17', '0.9.18')
	);
?>
