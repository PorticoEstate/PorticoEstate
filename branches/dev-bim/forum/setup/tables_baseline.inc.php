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

	$phpgw_baseline = array(
		'f_body' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'cat_id' => array('type' => 'int','nullable' => false, 'precision' => 4, 'default' => 0),
				'for_id' => array('type' => 'int','nullable' => false, 'precision' => 4, 'default' => 0),
				'message' => array('type' => 'blob')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'f_categories' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'name' => array('type' => 'varchar','nullable' => false, 'precision' => 50),
				'descr' => array('type' => 'varchar','nullable' => false, 'precision' => 255)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'f_forums' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'name' => array('type' => 'varchar','nullable' => false, 'precision' => 50),
				'perm' => array('type' => 'int','nullable' => false, 'precision' => 2, 'default' => 0),
				'groups' => array('type' => 'varchar','nullable' => false, 'precision' => 50, 'default' => 0),
				'descr' => array('type' => 'varchar','nullable' => false, 'precision' => 255),
				'cat_id' => array('type' => 'int','nullable' => false, 'precision' => 4, 'default' => 0)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'f_threads' => array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'postdate' => array('type' => 'date','nullable' => false),
				'main' => array('type' => 'int','nullable' => false, 'precision' => 4, 'default' => 0),
				'parent' => array('type' => 'int','nullable' => false, 'precision' => 4, 'default' => 0),
				'cat_id' => array('type' => 'int','nullable' => false, 'precision' => 4, 'default' => 0),
				'for_id' => array('type' => 'int','nullable' => false, 'precision' => 4, 'default' => 0),
				'author' => array('type' => 'varchar','nullable' => false, 'precision' => 50, 'default' => 0),
				'subject' => array('type' => 'varchar','nullable' => false, 'precision' => 50),
				'email' => array('type' => 'varchar','nullable' => false, 'precision' => 11),
				'host' => array('type' => 'varchar','nullable' => false, 'precision' => 18),
				'stat' => array('type' => 'int','nullable' => false, 'precision' => 2,'default' => 0),
				'thread' => array('type' => 'int','nullable' => false, 'precision' => 4, 'default' => 0),
				'depth' => array('type' => 'int','nullable' => false, 'precision' => 4, 'default' => 0),
				'pos' => array('type' => 'int','nullable' => false, 'precision' => 4, 'default' => 0),
				'n_replies' => array('type' => 'int','nullable' => false, 'precision' => 4, 'default' => 0)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);
?>
