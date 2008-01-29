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

	/* $Id: tables_update.inc.php 6387 2001-06-30 06:07:04Z milosch $ */

	$test[] = '0.0.4';
	function eldaptir_upgrade0_0_4()
	{
		global $setup_info,$phpgw_setup;

		$phpgw_setup->oProc->CreateTable(
			'phpgw_eldaptir_schema', array(
				'fd' => array(
					'id'    => array('type' => 'int', 'precision' => 4,'nullable' => False),
					'_oid'  => array('type' => 'varchar', 'precision' => 64,'nullable' => True),
					'name'  => array('type' => 'varchar', 'precision' => 64,'nullable' => True),
					'extra' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
					'descr' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
					'must'  => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
					'may'   => array('type' => 'varchar', 'precision' => 255,'nullable' => True)
				),
				'pk' => array(),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$setup_info['eldaptir']['currentver'] = '0.0.5';
		return $setup_info['eldaptir']['currentver'];
	}

	$test[] = '0.0.5';
	function eldaptir_upgrade0_0_5()
	{
		global $setup_info,$phpgw_setup;
		$phpgw_setup->oProc->AddColumn('phpgw_eldaptir_servers','type', array('type' => 'varchar', 'precision' => 32,'nullable' => True));
		$setup_info['eldaptir']['currentver'] = '0.0.6';
		return $setup_info['eldaptir']['currentver'];
	}
?>
