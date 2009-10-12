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
		'phpgw_sitemgr_module_guestbook_books' => array(
			'fd' => array(
				'book_id' => array('type' => 'auto', 'nullable' => false),
				'book_title' => array('type' => 'varchar', 'precision' => 255,'nullable' => false),
			),
			'pk' => array('book_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_sitemgr_module_guestbook_entries' => array(
			'fd' => array(
				'entry_id' => array('type' => 'auto', 'nullable' => false),
				'book_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'name' => array('type' => 'varchar', 'precision' => 100,'nullable' => false),
				'comment' => array('type' => 'text', 'nullable' => True),
				'timestamp' => array('type' => 'int', 'precision' => 8,'nullable' => True)
			),
			'pk' => array('entry_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
	);