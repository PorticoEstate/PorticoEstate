<?php
	/**************************************************************************\
	* phpGroupWare - Setup                                                     *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	* This program is free software; you can redistribute it and/or modify it  *
	* under the terms of the GNU General Public License as published by the    *
	* Free Software Foundation; either version 2 of the License, or (at your   *
	* option) any later version.                                               *
	\**************************************************************************/
	/* $Id: tables_baseline.inc.php 5696 2001-06-10 10:52:09Z milosch $ */

	$phpgw_baseline = array(
		'inv_products' => array(
			'fd' => array(
				'con' => array('type' => 'auto', 'nullable' => False),
				'id' => array('type' => 'varchar','precision' => 7,'nullable' => True),
				'name' => array('type' => 'varchar','precision' => 255,'nullable' => True),
				'des' => array('type' => 'text','nullable' => True),
				'category' => array('type' => 'int','precision' => 4,'nullable' => True),
				'status' => array('type' => 'int','precision' => 4,'nullable' => True),
				'weight' => array('type' => 'int','precision' => 4,'nullable' => True),
				'cost' => array('type' => 'decimal','precision' => 10,'scale' => 2,'nullable' => True),
				'price' => array('type' => 'decimal','precision' => 10,'scale' => 2,'nullable' => True),
				'retail' => array('type' => 'decimal','precision' => 10,'scale' => 2,'nullable' => True),
				'stock' => array('type' => 'int','precision' => 4,'nullable' => True),
				'mstock' => array('type' => 'int','precision' => 4,'nullable' => True),
				'url' => array('type' => 'varchar','precision' => 255,'nullable' => True),
				'dist' => array('type' => 'int','precision' => 4,'nullable' => True)
			),
			'pk' => array('con'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'inv_dist' => array(
			'fd' => array(
				'con' => array('type' => 'auto', 'nullable' => False),
				'name' => array('type' => 'varchar','precision' => 255,'nullable' => True)
			),
			'pk' => array('con'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'inv_categorys' => array(
			'fd' => array(
				'con' => array('type' => 'auto', 'nullable' => False),
				'number' => array('type' => 'varchar','precision' => 3,'nullable' => True),
				'name' => array('type' => 'varchar','precision' => 255,'nullable' => True)
			),
			'pk' => array('con'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'inv_status_list' => array(
			'fd' => array(
				'status_id' => array('type' => 'auto', 'nullable' => False),
				'status_name' => array('type' => 'varchar','precision' => 255,'nullable' => True)
			),
			'pk' => array('status_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);
?>
