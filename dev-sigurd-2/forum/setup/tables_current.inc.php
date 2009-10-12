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

  // table array for forum
	$phpgw_baseline = array(
		'phpgw_forum_body' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'cat_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'for_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'message' => array('type' => 'blob','nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_forum_categories' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'name' => array('type' => 'varchar', 'precision' => 255,'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => 255,'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_forum_forums' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'name' => array('type' => 'varchar', 'precision' => 255,'nullable' => False),
				'perm' => array('type' => 'int', 'precision' => 2,'nullable' => False),
				'groups' => array('type' => 'varchar', 'precision' => 255,'nullable' => False),
				'descr' => array('type' => 'varchar', 'precision' => 255,'nullable' => False),
				'cat_id' => array('type' => 'int', 'precision' => 4,'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_forum_threads' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'postdate' => array('type' => 'timestamp','nullable' => False,'default' => 'current_timestamp'),
				'main' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'parent' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'cat_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'for_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'thread_owner' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'subject' => array('type' => 'varchar', 'precision' => 255,'nullable' => False),
				'stat' => array('type' => 'int', 'precision' => 2,'nullable' => False),
				'thread' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'depth' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'pos' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'n_replies' => array('type' => 'int', 'precision' => 4,'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);
?>
