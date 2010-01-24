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
	/* $Id$ */

	$test[] = '0.8.1';
	function stocks_upgrade0_8_1()
	{
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_stocks', array(
				'fd' => array(
					'stock_id' => array('type' => 'auto', 'nullable' => False),
					'stock_owner' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
					'stock_access' => array('type' => 'varchar','precision' => 7),
					'stock_name' => array('type' => 'varchar','precision' => 255,'nullable' => False),
					'stock_symbol' => array('type' => 'varchar','precision' => 255,'nullable' => False)
				),
				'pk' => array('stock_id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);
		$GLOBALS['setup_info']['stocks']['currentver'] = '0.8.3.001';
		return $GLOBALS['setup_info']['stocks']['currentver'];
	}

	$test[] = '0.8.3.001';
	function stocks_upgrade0_8_3_001()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_stocks','stock_country',array('type' => 'char','precision' => 2,'default' => 'US','nullable' => False));

		$GLOBALS['setup_info']['stocks']['currentver'] = '0.8.3.002';
		return $GLOBALS['setup_info']['stocks']['currentver'];
	}
?>
