<?php
	/**************************************************************************\
	* eGroupWare - PHPBrain                                                    *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: setup.inc.php 16713 2006-05-09 05:14:07Z skwashd $ */

	/* Basic information about this app */
	$setup_info['phpbrain']['name']      = 'phpbrain';
	$setup_info['phpbrain']['title']     = 'Knowledge Base';
	$setup_info['phpbrain']['version']   = '0.9.17.505';
	$setup_info['phpbrain']['app_order'] = 25;
	$setup_info['phpbrain']['enable']    = 1;

	$setup_info['phpbrain']['author'] = 'Dave Hall';
	$setup_info['phpbrain']['note']   = 'Knowledge Base repository';
	$setup_info['phpbrain']['license']  = 'GPL';
	$setup_info['phpbrain']['description'] = 'Searchable Knowledge Base.';
	$setup_info['phpbrain']['maintainer'] = 'Dave Hall';
	$setup_info['phpbrain']['maintainer_email'] = 'skwashd at phpgroupware.org';

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['phpbrain']['hooks'][] = 'about';
	$setup_info['phpbrain']['hooks'][] = 'admin';
	$setup_info['phpbrain']['hooks'][] = 'add_def_pref';
	$setup_info['phpbrain']['hooks'][] = 'config';
	$setup_info['phpbrain']['hooks'][] = 'preferences';
	$setup_info['phpbrain']['hooks'][] = 'settings';
	$setup_info['phpbrain']['hooks'][] = 'sidebox_menu';
	$setup_info['phpbrain']['hooks'][] = 'deleteaccount';

	$setup_info['phpbrain']['tables'][] = 'phpgw_kb_articles';
	$setup_info['phpbrain']['tables'][] = 'phpgw_kb_comment';
	$setup_info['phpbrain']['tables'][] = 'phpgw_kb_questions';
	$setup_info['phpbrain']['tables'][] = 'phpgw_kb_ratings';
	$setup_info['phpbrain']['tables'][] = 'phpgw_kb_related_art';
	$setup_info['phpbrain']['tables'][] = 'phpgw_kb_search';
	$setup_info['phpbrain']['tables'][] = 'phpgw_kb_files';
	$setup_info['phpbrain']['tables'][] = 'phpgw_kb_urls';

	/* Dependencies for this app to work */
	$setup_info['phpbrain']['depends'][] = array(
		'appname' => 'phpgwapi',
		'versions' => Array('0.9.17', '0.9.18')
	);



