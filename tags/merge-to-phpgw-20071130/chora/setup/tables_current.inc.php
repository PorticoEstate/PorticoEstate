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

  /* $Id: tables_current.inc.php 7355 2001-08-18 00:32:56Z skeeter $ */

  // table array for chora
	$phpgw_baseline = array(
		'phpgw_chora_sites' => array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'name' => array('type' => 'varchar', 'precision' => 32,'nullable' => True),
				'location' => array('type' => 'varchar', 'precision' => 64,'nullable' => True),
				'title' => array('type' => 'varchar', 'precision' => 32,'nullable' => True),
				'intro' => array('type' => 'varchar', 'precision' => 32,'nullable' => True),
				'cvsusers' => array('type' => 'varchar', 'precision' => 64,'nullable' => True),
				'is_default' => array('type' => 'int', 'precision' => 2,'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);
?>
