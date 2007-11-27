<?php
	/**************************************************************************\
	* phpGroupWare - eLDAPtir                                                  *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: setup.inc.php 16493 2006-03-11 23:24:02Z skwashd $ */

	/* Basic information about this app */
	$setup_info['eldaptir']['name']		= 'eldaptir';
	$setup_info['eldaptir']['version']	= '0.0.6';
	$setup_info['eldaptir']['app_order']	= 25;
	$setup_info['eldaptir']['enable']	= 1;
	$setup_info['eldaptir']['app_group']	= 'systools';

	/* The tables this app creates */
	$setup_info['eldaptir']['tables']    = array(
		'phpgw_eldaptir_servers',
		'phpgw_eldaptir_schema'
	);

	$setup_info['eldaptir']['author'] = 'Miles Lott';
	$setup_info['eldaptir']['license']  = 'GPL';
	$setup_info['eldaptir']['phpver'][] = 4;
	$setup_info['eldaptir']['description'] =
		'<b>eLDAPtir</b> - Edit LDAP Trees in Realtime<br> 
		An LDAP administration tool for phpGroupWare.  It makes use of its own LDAP class 
		to store and retrieve LDAP information with strict adherence to schema.';
	$setup_info['eldaptir']['maintainer'] = 'Miles Lott';
	$setup_info['eldaptir']['maintainer_email'] = 'milosch@phpgroupware.org';

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['eldaptir']['hooks'][] = 'admin';

	/* Dependencies for this app to work */
	$setup_info['eldaptir']['depends'][] = array(
		 'appname' => 'phpgwapi',
		 'versions' => Array('0.9.16', '0.9.17','0.9.18')
	);
?>
