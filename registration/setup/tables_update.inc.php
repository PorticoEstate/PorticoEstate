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

	/* $Id$ */

	$test[] = '0.8.1';
	function registration_upgrade0_8_1()
	{
		$phpgw_setup->oProc->CreateTable('phpgw_reg_fields', array(
			'fd' => array(
				'field_name' => array('type' => 'varchar', 'precision' => 255,'nullable' => False),
				'field_text' => array('type' => 'text','nullable' => False),
				'field_type' => array('type' => 'varchar', 'precision' => 255,'nullable' => True),
				'field_values' => array('type' => 'text','nullable' => True),
				'field_required' => array('type' => 'char', 'precision' => 1,'nullable' => True),
				'field_order' => array('type' => 'int', 'precision' => 4,'nullable' => True)
			),
			'pk' => array(),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		));

		$GLOBALS['setup_info']['registration']['currentver'] = '0.8.2';
		return $GLOBALS['setup_info']['registration']['currentver'];
	}

	$test[] = '0.8.2';
	function registration_upgrade0_8_2()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_reg_accounts','reg_approved',array('type' => 'int','precision' => 2, 'nullable' => True));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['registration']['currentver'] = '0.8.3';
			return $GLOBALS['setup_info']['registration']['currentver'];
		}
	}
