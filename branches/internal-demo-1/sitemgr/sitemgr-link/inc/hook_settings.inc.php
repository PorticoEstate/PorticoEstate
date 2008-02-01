<?php
 /**********************************************************************\
 * phpGroupWare - SiteMgr						*
 * http://www.phpgroupware.org						*
 * This program is part of the GNU project, see http://www.gnu.org/	*
 *									*
 * Copyright 2002, 2003 Free Software Foundation, Inc.			*
 *									*
 * Written by Dave Hall - skwashd at phpgroupware.org			*
 * --------------------------------------------			*
 * This program is Free Software; you can redistribute it and/or modify *
 * it under the terms of the GNU General Public License as published by *
 * the Free Software Foundation; either version 2 of the License, or 	*
 * at your option) any later version.					*
 \**********************************************************************/
 /* $Id: hook_settings.inc.php 16075 2005-08-29 04:26:02Z skwashd $ */

	
	$bo = createObject('sitemgr.Sites_BO');
	$sites = array(0 => lang('display list'));
	foreach($bo->list_sites(False) as $key => $data)
	{
		$sites[$key] = $data['site_name'];
	}
	create_select_box('Default Website','default_site',$sites,
		'This is the website that sitemgr-link will load when you click the application icon.');
