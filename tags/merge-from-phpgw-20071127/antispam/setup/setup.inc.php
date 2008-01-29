<?php
/**************************************************************************\
* phpGroupWare - Antispam                                                  *
* http://www.phpgroupware.org                                              *
* This application written by:                                             *
*                             Marco Andriolo-Stagno <stagno@prosa.it>      *
*                             PROSA <http://www.prosa.it>                  *
* -------------------------------------------------------------------------*
* Funding for this program was provided by http://www.seeweb.com           *
* -------------------------------------------------------------------------*
*  This program is free software; you can redistribute it and/or modify it *
*  under the terms of the GNU General Public License as published by the   *
*  Free Software Foundation; either version 2 of the License, or (at your  *
*  option) any later version.                                              *
\**************************************************************************/

   /* $Id: setup.inc.php 16493 2006-03-11 23:24:02Z skwashd $ */

	$setup_info['antispam']['name']      = 'antispam';
	$setup_info['antispam']['title']     = 'Antispam';
	$setup_info['antispam']['version']   = '0.45';
	$setup_info['antispam']['app_order'] = 12;
	$setup_info['antispam']['app_group']	= 'systools';

	$setup_info['antispam']['tables']    = array('phpgw_antispam');

	$setup_info['antispam']['enable']    = 1;

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['antispam']['author'] = 'Marco Andriolo-Stagno';
	$setup_info['antispam']['license'] = 'GPL' ;
	$setup_info['antispam']['description'] =   'Module to set preferences for Spamassassin';
	$setup_info['antispam']['maintainer'] = 'Marco Andriolo-Stagno';
	$setup_info['antispam']['maintainer_email'] = 'stagno@prosa.it';
		       
	$setup_info['antispam']['hooks'][] = 'about';
	$setup_info['antispam']['hooks'][] = 'preferences';
	$setup_info['antispam']['hooks'][] = 'manual';
	$setup_info['antispam']['hooks'][] = 'admin';

	/* Dependacies for this app to work */
	$setup_info['antispam']['depends'][] = array
  	(
	 	'appname'	=>	'phpgwapi',
	 	'versions'	=>	array('0.9.16', '0.9.17', '0.9.18')
	 );

?>
