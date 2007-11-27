<?php
	/**************************************************************************\
	* phpGroupWare - FeLaMiMail                                                *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: setup.inc.php 18014 2007-03-06 14:32:11Z sigurdne $ */

	$setup_info['felamimail']['name']      = 'felamimail';
	$setup_info['felamimail']['title']     		= 'FeLaMiMail';
	$setup_info['felamimail']['version']		= '0.9.4';
	$setup_info['felamimail']['app_order'] = 2;
	$setup_info['felamimail']['enable']    = 1;
	$setup_info['felamimail']['app_group']  = 'office';

	$setup_info['felamimail']['author']    = 'Lars Kneschke';
	$setup_info['felamimail']['license']   = 'GPL';
	$setup_info['felamimail']['description'] =
		'Email reader originally based on Squirrelmail, ported to phpGroupWare by Lars Kneschke.';
	$setup_info['felamimail']['based_on'] = 
		'This port is based on Squirrelmail, which is a standalone IMAP client.';
	$setup_info['felamimail']['based_on_url'] = 'http://www.squirrelmail.org';
	$setup_info['felamimail']['maintainer'] 	= 'phpGroupWare Coordination Team';
	$setup_info['felamimail']['maintainer_email'] 	= 'phpgroupware-developers@gnu.org';

	$setup_info['felamimail']['tables']    = array(
		'phpgw_felamimail_cache',
		'phpgw_felamimail_folderstatus',
		'phpgw_felamimail_displayfilter'
	);

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['felamimail']['hooks'][] = 'preferences';
	$setup_info['felamimail']['hooks'][] = 'admin';
	$setup_info['felamimail']['hooks'][] = 'manual';
	$setup_info['felamimail']['hooks'][] = 'help';
	$setup_info['felamimail']['hooks'][] = 'settings';
	$setup_info['felamimail']['hooks'][] = 'home';
	$setup_info['felamimail']['hooks'][] = 'sidebox_menu';

	/* Dependacies for this app to work */
	$setup_info['felamimail']['depends'][] = array(
		'appname'  => 'phpgwapi',
		'versions' => Array('0.9.17','0.9.18')
	);
?>
