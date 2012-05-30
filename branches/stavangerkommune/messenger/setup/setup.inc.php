<?php
	/**************************************************************************\
	* phpGroupWare - Messenger                                                 *
	* http://www.phpgroupware.org                                              *
	* This application written by Joseph Engo <jengo@phpgroupware.org>         *
	* --------------------------------------------                             *
	* Funding for this program was provided by http://www.checkwithmom.com     *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	/* Basic information about this app */
	$setup_info['messenger']['name']      = 'messenger';
	$setup_info['messenger']['version']   = '0.9.17.500';
	$setup_info['messenger']['app_order'] = '10';
	$setup_info['messenger']['enable']    = 1;
	$setup_info['messenger']['app_group']	= 'office';

	/* The tables this app creates */
	$setup_info['messenger']['tables']    = array(
		'phpgw_messenger_messages'
	);

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['messenger']['hooks'] = array
	(
		'admin',
		'preferences',
		'home',
		'after_navbar',
		'config',
		'menu'	=> 'messenger.menu.get_menu',
		'registration'	=> 'messenger.hook_helper.add_welkome_message'
	);

	/* Dependencies for this app to work */
	$setup_info['messenger']['depends'][] = array(
		'appname'  => 'phpgwapi',
		'versions' => Array('0.9.17', '0.9.18')
	);
?>
