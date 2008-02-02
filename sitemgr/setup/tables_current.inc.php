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

	/* $Id$ */

	$phpgw_baseline = array(
		'phpgw_sitemgr_pages' => array(
			'fd' => array(
				'page_id' => array('type' => 'auto', 'nullable' => false),
				'cat_id' => array('type' => 'int', 'precision' => 4),
				'sort_order' => array('type' => 'int', 'precision' => 4),
				'hide_page' => array('type' => 'int', 'precision' => 4),
				'name' => array('type' => 'varchar', 'precision' => 100),
				//one of constants SITEMGR_STATE_DRAFT, SITEMGR_STATE_PREPUBLISH, SITEMGR_STATE_PUBLISH,
				//SITEMGR_STATE_PREUNPUBLISH, SITEMGR_STATE_ARCHIVE
				'state' => array('type' => 'int', 'precision' => 2)
			),
			'pk' => array('page_id'),
			'fk' => array(),
			'ix' => array('cat_id'),
			'uc' => array()
		),
		'phpgw_sitemgr_pages_lang' => array(
			'fd' => array(
				'page_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'lang' => array('type' => 'varchar', 'precision' => 2, 'nullable' => false),
				'title' => array('type' => 'varchar', 'precision' => 255),
				'subtitle' => array('type' => 'varchar', 'precision' => 255)
			),
			'pk' => array('page_id','lang'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_sitemgr_categories_state' => array(
			'fd' => array(
				'cat_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'state' => array('type' => 'int', 'precision' => 2)
			),
			'pk' => array('cat_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_sitemgr_categories_lang' => array(
			'fd' => array(
				'cat_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'lang' => array('type' => 'varchar', 'precision' => 2, 'nullable' => false),
				'name' => array('type' => 'varchar', 'precision' => 100),
				'description' => array('type' => 'varchar', 'precision' => 255)
			),
			'pk' => array('cat_id','lang'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_sitemgr_modules' => array(
			'fd' => array(
				'module_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
				'module_name' => array('type' => 'varchar', 'precision' => 25),
				'description' => array('type' => 'varchar', 'precision' => 255)
			),
			'pk' => array('module_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_sitemgr_blocks' => array(
			'fd' => array(
				'block_id' => array('type' => 'auto', 'nullable' => false),
				'area' => array('type' => 'varchar', 'precision' => 50),
				//if page_id != NULL scope=page, elseif cat_id != CURRENT_SITE_ID scope=category, else scope=site
				'cat_id' => array('type' => 'int', 'precision' => 4),
				'page_id' => array('type' => 'int', 'precision' => 4),
				'module_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'sort_order' => array('type' => 'int', 'precision' => 4),
				'viewable' => array('type' => 'int', 'precision' => 4),
			),
			'pk' => array('block_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_sitemgr_blocks_lang' => array(
			'fd' => array(
				'block_id' => array('type' => 'auto', 'nullable' => false),
				'lang' => array('type' => 'varchar', 'precision' => 2, 'nullable' => false),
				'title' => array('type' => 'varchar', 'precision' => 255),
			),
			'pk' => array('block_id','lang'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_sitemgr_content' => array(
			'fd' => array(
				'version_id' => array('type' => 'auto', 'nullable' => false),
				'block_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'arguments' => array('type' => 'text'),
				//one of constants SITEMGR_STATE_PUBLISH, SITEMGR_STATE_PREPUBLISH, SITEMGR_STATE_PREUNPUBLISH, SITEMGR_STATE_DRAFT
				'state' => array('type' => 'int', 'precision' => 2)
			),
			'pk' => array('version_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_sitemgr_content_lang' => array(
			'fd' => array(
				'version_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'lang' => array('type' => 'varchar', 'precision' => 2, 'nullable' => false),
				'arguments_lang' => array('type' => 'text'),
			),
			'pk' => array('version_id','lang'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_sitemgr_active_modules' => array(
			'fd' => array(
				// area __PAGE__ stands for master list
				'area' => array('type' => 'varchar', 'precision' => 50, 'nullable' => false),
				// cat_id 0 stands for site wide
				'cat_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'module_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
			),
			'pk' => array('area','cat_id','module_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_sitemgr_properties' => array(
			'fd' => array(
				// area __PAGE__ stands for all areas
				'area' => array('type' => 'varchar', 'precision' => 50, 'nullable' => false),
				// cat_id 0 stands for site wide 
				'cat_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false), 
				'module_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'properties' => array('type' => 'text')
			),
			'pk' => array('area','cat_id','module_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_sitemgr_sites' => array(
			'fd' => array(
				'site_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'site_name' => array('type' => 'varchar', 'precision' => 255),
				'site_url' => array('type' => 'varchar', 'precision' => 255),
				'site_dir' => array('type' => 'varchar', 'precision' => 255),
				'themesel' => array('type' => 'varchar', 'precision' => 50),
				'site_languages' => array('type' => 'varchar', 'precision' => 50),
				'home_page_id' => array('type' => 'int', 'precision' => 4),
				'anonymous_user' => array('type' => 'varchar', 'precision' => 50),
				'anonymous_passwd' => array('type' => 'varchar', 'precision' => 50),
			),
			'pk' => array('site_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
	);