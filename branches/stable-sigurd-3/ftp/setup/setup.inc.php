<?php
	/**************************************************************************\
	* phpGroupWare - FTP                                                       *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	/* Basic information about this app */
	$setup_info['ftp']['name']      = 'ftp';
	$setup_info['ftp']['title']     = 'FTP';
	$setup_info['ftp']['version']   = '0.8.1';
	$setup_info['ftp']['app_order'] = 25;
	$setup_info['ftp']['enable']    = 1;
	$setup_info['ftp']['app_group']	= 'internet';

	$setup_info['ftp']['author'] = 'Joseph Engo';
	$setup_info['ftp']['license']  = 'GPL';
	$setup_info['ftp']['description'] =
		'FTP client.';
	$setup_info['ftp']['maintainer'] = 'phpGroupWare Coordination Team';
	$setup_info['ftp']['maintainer_email'] = 'phpgwroupware-developers@gnu.org';

	/* Dependencies for this app to work */
	$setup_info['ftp']['depends'][] = array(
		'appname'  => 'phpgwapi',
		'versions' => Array('0.9.16', '0.9.17', '0.9.18')
	);
?>
