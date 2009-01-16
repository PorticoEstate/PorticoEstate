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

  /**************************************************************************\
  * This file should be generated for you. It should never be edited by hand *
  \**************************************************************************/

  /* $Id$ */

  // table array for developer_tools
	$phpgw_baseline = array(
		'phpgw_devtools_diary' => array(
			'fd' => array(
				'diary_id' => array('type' => 'auto','nullable' => False),
				'diary_owner' => array('type' => 'int', 'precision' => 8,'nullable' => True),
				'diary_date' => array('type' => 'int', 'precision' => 8,'nullable' => True),
				'diary_access' => array('type' => 'varchar', 'precision' => 4,'nullable' => True),
				'diary_summary' => array('type' => 'varchar', 'precision' => 8,'nullable' => True),
				'diary_details' => array('type' => 'text','nullable' => True)
			),
			'pk' => array('diary_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'phpgw_devtools_sf_cache' => array(
			'fd' => array(
				'cache_id' => array('type' => 'int', 'precision' => 8,'nullable' => True),
				'cache_timestamp' => array('type' => 'int', 'precision' => 8,'nullable' => True),
				'cache_content' => array('type' => 'text','nullable' => True)
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),

		'phpgw_devtools_changelogs' => array(
			'fd' => array(
				'changelog_id' => array('type' => 'auto','nullable' => False),
				'changelog_cat' => array('type' => 'int','precision' => 8,'nullable' => False),
				'changelog_timestamp' => array('type' => 'int', 'precision' => 8,'nullable' => False),
				'changelog_version' => array('type' => 'varchar', 'precision' => 20,'nullable' => False),
				'changelog_content' => array('type' => 'text','nullable' => False)
			),
			'pk' => array('changelog_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);
?>
