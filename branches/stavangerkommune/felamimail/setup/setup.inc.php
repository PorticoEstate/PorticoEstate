<?php
	/**************************************************************************\
	* EGroupWare - FeLaMiMail                                                  *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; version 2 of the License.                     *
	\**************************************************************************/

	/* $Id$ */

	$setup_info['felamimail']['name']      		= 'felamimail';
	$setup_info['felamimail']['title']     		= 'FeLaMiMail';
	$setup_info['felamimail']['version']     	= '1.5.003';
	$setup_info['felamimail']['app_order'] 		= 2;
	$setup_info['felamimail']['enable']    		= 1;

	$setup_info['felamimail']['author']		= 'Lars Kneschke';
	$setup_info['felamimail']['license']		= 'GPL';
	$setup_info['felamimail']['description']	=
		'IMAP emailclient for eGroupWare';
	$setup_info['felamimail']['maintainer'] 	= 'Klaus Leithoff';
	$setup_info['felamimail']['maintainer_email'] 	= 'kl@leithoff.net';

	$setup_info['felamimail']['tables']    = array('egw_felamimail_displayfilter','egw_felamimail_accounts','egw_felamimail_signatures');

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['felamimail']['hooks'][] = 'settings';
	$setup_info['felamimail']['hooks'][] = 'home';
	$setup_info['felamimail']['hooks'][] = 'notifywindow';
	$setup_info['felamimail']['hooks']['addaccount']	= 'felamimail.bofelamimail.addAccount';
	$setup_info['felamimail']['hooks']['deleteaccount']	= 'felamimail.bofelamimail.deleteAccount';
	$setup_info['felamimail']['hooks']['editaccount']	= 'felamimail.bofelamimail.updateAccount';
	$setup_info['felamimail']['hooks']['edit_user']		= 'felamimail.bofelamimail.adminMenu';
	$setup_info['felamimail']['hooks']['menu']    		= 'felamimail.menu.get_menu';

	/* Dependencies for this app to work */
	$setup_info['felamimail']['depends'][] = array(
		'appname'  => 'phpgwapi',
		'versions' => Array('0.9.17','0.9.18')
	);
	$setup_info['felamimail']['depends'][] = array(
		'appname'  => 'emailadmin',
		'versions' => Array('1.5.003')
	);

