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

  /* $Id: tables_baseline.inc.php 6087 2001-06-20 01:00:04Z milosch $ */

  // table array for eldaptir
	$phpgw_baseline = array(
		'phpgw_eldaptir_servers' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 32,'nullable' => True),
				'basedn' => array('type' => 'varchar', 'precision' => 64,'nullable' => True),
				'rootdn' => array('type' => 'varchar', 'precision' => 64,'nullable' => True),
				'rootpw' => array('type' => 'varchar', 'precision' => 64,'nullable' => True),
				'is_default' => array('type' => 'int', 'precision' => 2,'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);
?>
