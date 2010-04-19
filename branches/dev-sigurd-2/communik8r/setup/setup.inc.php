<?php
	/**************************************************************************\
	* phpGroupWare                                                             *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/
	/* $Id: setup.inc.php,v 1.1.1.1 2005/08/23 05:04:14 skwashd Exp $ */

	/* Basic information about this app */
	$setup_info['communik8r']['name']      = 'communik8r';
	$setup_info['communik8r']['title']     = 'Communik8r';
	$setup_info['communik8r']['version']   = '0.9.17.504';
	$setup_info['communik8r']['app_order'] = 2;
	$setup_info['communik8r']['enable']    = 1;
	
	/* some info's for about.php and apps.phpgroupware.org */
	$setup_info['communik8r']['author']    = 'Dave Hall';
	$setup_info['communik8r']['license']   = 'GPL';
	$setup_info['communik8r']['description'] =
		'Communications application, currently supports email, but will
		eventually support RSS, atomz, sms and jabber all in 1 RESTful app.
		Development of this application was funded by XXXXX';
	$setup_info['communik8r']['maintainer'] = 'Dave Hall';
	$setup_info['communik8r']['maintainer_email'] = 'skwashd@phpgroupware.org';
	
	/* The tables this app creates */
	//$setup_info['communik8r']['tables']    = 

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['communik8r']['hooks'] = array
						(
							'admin',
							'menu'	=> 'communik8r.menu.get_menu',
							'after_navbar',
							//'preferences',
							//'manual',
							//'add_def_prefs',
						);

	$setup_info['communik8r']['depends'][] = array(
			 'appname' => 'phpgwapi',
			 'versions' => Array('0.9.16', '0.9.17', '0.9.18')
		);

	$setup_info['communik8r']['tables'] = array
		(
			'phpgw_communik8r_acct_types',
			'phpgw_communik8r_accts',
			'phpgw_communik8r_email_msgs',
			'phpgw_communik8r_email_mboxes',
			'phpgw_communik8r_email_headers'
		);
