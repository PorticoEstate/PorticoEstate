<?php
	/****************************************************************************\
	* phpGroupWare - Forums                                                      *
	* http://www.phpgroupware.org                                                *
	* Written by Jani Hirvinen <jpkh@shadownet.com>                              *
	* -------------------------------------------                                *
	*  This program is free software; you	can redistribute it and/or modify it  *
	*  under the terms of	the GNU	General	Public License as published by the *
	*  Free Software Foundation; either version 2	of the License, or (at your  *
	*  option) any later version.                                                *
	\****************************************************************************/

	/* $Id$ */

	$setup_info['forum']['name'] = 'forum';
	$setup_info['forum']['version'] = '0.9.13.006';
	$setup_info['forum']['app_order'] = 7;
	$setup_info['forum']['enable'] = 1;
	$setup_info['forum']['app_group']	= 'other';

	$setup_info['forum']['author'] = 'Joseph Engo';
	$setup_info['forum']['license']  = 'GPL';
	$setup_info['forum']['description'] =
		'Subject matter message board.';
	$setup_info['forum']['maintainer'] = 'Mark Peters';
	$setup_info['forum']['maintainer_email'] = 'skeeter@phpgroupware.org';

	/* the table info */
	$setup_info['forum']['tables'] = array(
		'phpgw_forum_body',
		'phpgw_forum_categories',
		'phpgw_forum_forums',
		'phpgw_forum_threads'
	);

	/* the hooks */
	$setup_info['forum']['hooks'][] = 'admin';
	$setup_info['forum']['hooks'][] = 'settings';
	$setup_info['forum']['hooks'][] = 'preferences';

	/* the dependencies */
	$setup_info['forum']['depends'][] = array(
		'appname' => 'phpgwapi',
		'versions' => Array('0.9.16', '0.9.17', '0.9.18')
	);
?>
