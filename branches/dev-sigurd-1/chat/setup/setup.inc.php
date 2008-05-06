<?php
	/**************************************************************************\
	* phpGroupWare - Chat                                                      *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	/* Basic information about this app */
	$setup_info['chat']['name']		= 'chat';
	$setup_info['chat']['version']		= '0.9.13.002';
	$setup_info['chat']['app_order']	= 4;
	$setup_info['chat']['enable']		= 1;
	$setup_info['chat']['app_group']	= 'internet';

	$setup_info['chat']['author'] = 'Joseph Engo';
	$setup_info['chat']['license']  = 'GPL';
	$setup_info['chat']['description'] =
		'Chat module.';
	$setup_info['chat']['maintainer'] = 'Joseph Engo';
	$setup_info['chat']['maintainer_email'] = 'jengo@phpgroupware.org';

	$setup_info['chat']['tables'] = array(
		'phpgw_chat_channel',
		'phpgw_chat_messages',
		'phpgw_chat_currentin',
		'phpgw_chat_privatechat'
	);

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['chat']['hooks'][] = 'admin';
	$setup_info['chat']['hooks'][] = 'preferences';

	/* Dependencies for this app to work */
	$setup_info['chat']['depends'][] = array(
		 'appname' => 'phpgwapi',
		 'versions' => Array('0.9.16', '0.9.17','0.9.18')
	);
?>
