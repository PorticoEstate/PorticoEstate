<?php
	/**************************************************************************\
	* phpGroupWare - Chora                                                     *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	/* Basic information about this app */
	$setup_info['chora']['name']		= 'chora';
	$setup_info['chora']['version']		= '0.0.4';
	$setup_info['chora']['app_order']	= 25;
	$setup_info['chora']['enable']		= 1;
	$setup_info['chora']['app_group']	= 'systools';

	$setup_info['chora']['author'] = 'Miles Lott';
	$setup_info['chora']['license']  = 'GPL';
	$setup_info['chora']['phpver'][] = 4;
	$setup_info['chora']['description'] =
		'View CVS repositories.';
	$setup_info['chora']['maintainer'] = 'Miles Lott';
	$setup_info['chora']['maintainer_email']    = 'milosch@phpgroupware.org';
	$setup_info['chora']['based_on']   =
		'Chora by <a href="mailto:anil@recoil.org">Anil Madhavapeddy</a>';
	/* The tables this app creates */
	$setup_info['chora']['tables']    = array(
		'phpgw_chora_sites'
	);

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['chora']['hooks'] = array
	(
		'admin',
		'about'
	);

	/* Dependencies for this app to work */
	$setup_info['chora']['depends'][] = array(
		 'appname' => 'phpgwapi',
		 'versions' => Array('0.9.16', '0.9.17','0.9.18')
	);
?>
