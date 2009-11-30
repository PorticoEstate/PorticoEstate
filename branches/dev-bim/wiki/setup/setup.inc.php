<?php
	/**************************************************************************\
	* phpGroupWare - Setup                                                     *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/
	/* $Id$ */

	$setup_info['wiki']['name']		= 'wiki';
	$setup_info['wiki']['title']		= 'Wiki';
	$setup_info['wiki']['version']		= '0.9.15.001';
	$setup_info['wiki']['app_order']	= 100;
	$setup_info['wiki']['enable']		= 1;
	$setup_info['wiki']['app_group']	= 'other';

	$setup_info['wiki']['author']    = 'Tavi Team';
	$setup_info['wiki']['license']   = 'GPL';
	$setup_info['wiki']['description'] =
		'Wiki is a modified version of <a href="http://tavi.sf.net" target="_new">WikkiTikkiTavi</a> for use with phpGroupware.';
	$setup_info['wiki']['maintainer'] = 'phpGroupWare Coordination Team';
	$setup_info['wiki']['maintainer_email'] = 'phpgroupware-developers@gnu.org';

	$setup_info['wiki']['tables'][] = 'phpgw_wiki_links';
	$setup_info['wiki']['tables'][] = 'phpgw_wiki_pages';
	$setup_info['wiki']['tables'][] = 'phpgw_wiki_rate';
	$setup_info['wiki']['tables'][] = 'phpgw_wiki_interwiki';
	$setup_info['wiki']['tables'][] = 'phpgw_wiki_sisterwiki';
	$setup_info['wiki']['tables'][] = 'phpgw_wiki_remote_pages';
	
	/* The hooks this app includes, needed for hooks registration */
	$setup_info['wiki']['hooks'] = array();

	/* Dependencies for this app to work */
	$setup_info['wiki']['depends'][] = array
	(
		'appname'  => 'phpgwapi',
		'versions' => Array('0.9.16', '0.9.17', '0.9.18')
	);
?>
