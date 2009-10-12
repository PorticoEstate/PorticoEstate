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

  // table array for headlines
	$phpgw_baseline = array(
		'news_site' => array(
			'fd' => array(
				'con' => array('type' => 'auto','nullable' => False),
				'display' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'base_url' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'newsfile' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'lastread' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'newstype' => array('type' => 'varchar', 'precision' => 15,'nullable' => True),
				'cachetime' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'listings' => array('type' => 'int', 'precision' => 4,'nullable' => True)
			),
			'pk' => array('con'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'news_headlines' => array(
			'fd' => array(
				'site' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'title' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'link' => array('type' => 'varchar', 'precision' => 255,'nullable' => True)
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'users_headlines' => array(
			'fd' => array(
				'owner' => array('type' => 'varchar', 'precision' => 25,'nullable' => False),
				'site' => array('type' => 'int', 'precision' => 4,'nullable' => False)
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);
?>
