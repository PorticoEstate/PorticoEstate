<?php
	/**************************************************************************\
	* phpGroupWare - XML-RPC                                                   *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	/* Basic information about this app */
	$setup_info['xmlrpc']['name']      = 'xmlrpc';
	$setup_info['xmlrpc']['version']   = '0.0.1';
	$setup_info['xmlrpc']['app_order'] = 4;
	$setup_info['xmlrpc']['enable']    = 1;
	$setup_info['xmlrpc']['app_group']	= 'development';

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['xmlrpc']['hooks'][] = 'preferences';
	$setup_info['xmlrpc']['hooks'][] = 'admin';

	/* Dependencies for this app to work */
	$setup_info['xmlrpc']['depends'][] = array(
		 'appname' => 'phpgwapi',
		 'versions' => Array('0.9.16', '0.9.17','0.9.18')
	);
?>
