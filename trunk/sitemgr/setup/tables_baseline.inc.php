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
				'name' => array('type' => 'varchar', 'precision' => 100),
				'title' => array('type' => 'varchar', 'precision' => 256),
				'subtitle' => array('type' => 'varchar', 'precision' => 256),
				'content' => array('type' => 'text')
			),
			'pk' => array('page_id'),
			'fk' => array(),
			'ix' => array('cat_id'),
			'uc' => array()
		),
		'phpgw_sitemgr_categories' => array(
			'fd' => array(
				'cat_id' => array('type' => 'auto', 'nullable' => false),
				'name' => array('type' => 'varchar', 'precision' => 100),
				'description' => array('type' => 'varchar', 'precision' => 256)
			),
			'pk' => array('cat_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_sitemgr_blocks' => array(
			'fd' => array(
				'block_id' => array('type' => 'auto', 'nullable' => false),
				'side' => array('type' => 'int', 'precision' => 4),
				'position' => array('type' => 'int', 'precision' => 4),
				'filename' => array('type' => 'varchar', 'precision' => 300),
				'title' => array('type' => 'varchar', 'precision' => 256)
			),
			'pk' => array('block_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_sitemgr_preferences' => array(
			'fd' => array(
				'pref_id' => array('type' => 'auto', 'nullable' => false),
				'name' => array('type' => 'varchar', 'precision' => 256),
				'value' => array('type' => 'text')
			),
			'pk' => array('pref_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);
?>
