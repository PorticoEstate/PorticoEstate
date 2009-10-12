<?php
 /**************************************************************************\
 * phpGroupWare - fax                                                       *
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

 /* $Id$ */

	$setup_info['fax']['name']      = 'fax';
	$setup_info['fax']['title']     = 'FAX';
	$setup_info['fax']['version']   = '0.65';
	$setup_info['fax']['app_order'] = 5;
	$setup_info['fax']['enable']    = 1;
	$setup_info['fax']['app_group'] = 'office';
	$setup_info['fax']['tables']    = array('phpgw_fax_prefs', 'phpgw_fax_admin');

	$setup_info['fax']['author'] = 'Marco Andriolo-Stagno';
	$setup_info['fax']['license']  = 'GPL';
	$setup_info['fax']['description'] =   'A tool to send fax via HylaFAX';
	$setup_info['fax']['maintainer'] = 'Marco Andriolo-Stagno (MAS!)';
	$setup_info['fax']['maintainer_email'] = 'stagno@prosa.it';

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['fax']['hooks'][] = 'preferences';
	$setup_info['fax']['hooks'][] = 'admin';
	$setup_info['fax']['hooks'][] = 'manual';
	$setup_info['fax']['hooks'][] = 'about';
	
	/* Dependacies for this app to work */
	$setup_info['fax']['depends'][] = array
  			(
			 'appname'	=>	'phpgwapi',
			 'versions'	=>	array('0.9.16', '0.9.17', '0.9.18')
			 );

?>
