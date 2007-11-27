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

	/* $Id: tables_update.inc.php 17722 2006-12-18 20:03:33Z sigurdne $ */

	$test[] = '0.8.2';
	function felamimail_upgrade0_8_2()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_felamimail_cache','to_name',array('type' => 'varchar', 'precision' => 120));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_felamimail_cache','to_address',array('type' => 'varchar', 'precision' => 120));
		
		$GLOBALS['setup_info']['felamimail']['currentver'] = '0.8.3';
		return $GLOBALS['setup_info']['felamimail']['currentver'];
	}

	$test[] = '0.8.3';
	function felamimail_upgrade0_8_3()
	{

		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_felamimail_cache','attachments',array('type' => 'varchar', 'precision' => 120));
		
		$GLOBALS['setup_info']['felamimail']['currentver'] = '0.8.4';
		return $GLOBALS['setup_info']['felamimail']['currentver'];
	}

	$test[] = '0.8.4';
	function felamimail_upgrade0_8_4()
	{
		$GLOBALS['setup_info']['felamimail']['currentver'] = '0.9.0';
		return $GLOBALS['setup_info']['felamimail']['currentver'];
	}

	$test[] = '0.9.0';
	function felamimail_upgrade0_9_0()
	{
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_felamimail_folderstatus', 'accountname', array('type' => 'varchar', 'precision' => 200, 'nullable' => false));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_felamimail_cache', 'accountname', array('type' => 'varchar', 'precision' => 200, 'nullable' => false));

		$GLOBALS['setup_info']['felamimail']['currentver'] = '0.9.1';
		return $GLOBALS['setup_info']['felamimail']['currentver'];
	}

	$test[] = '0.9.1';
	function felamimail_upgrade0_9_1()
	{
		$GLOBALS['setup_info']['felamimail']['currentver'] = '0.9.2';
		return $GLOBALS['setup_info']['felamimail']['currentver'];
	}

	$test[] = '0.9.2';
	function felamimail_upgrade0_9_2()
	{
		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_felamimail_displayfilter',
			Array(
				'fd' => array(
					'accountid' 	=> array('type' => 'int', 'precision' => 4, 'nullable' => false),
					'filter' 	=> array('type' => 'text')
				),
				'pk' => array('accountid'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)

		);

		$GLOBALS['setup_info']['felamimail']['currentver'] = '0.9.3';
		return $GLOBALS['setup_info']['felamimail']['currentver'];
	}

	$test[] = '0.9.3';
	function felamimail_upgrade0_9_3()
	{
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_felamimail_cache');
		$GLOBALS['phpgw_setup']->oProc->query('delete from phpgw_felamimail_folderstatus',__LINE__,__FILE__);
		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_felamimail_cache',
			Array(
				'fd' => array(
					'accountid' 	=> array('type' => 'int', 'precision' => 4, 'nullable' => false),
					'hostname' 	=> array('type' => 'varchar', 'precision' => 60, 'nullable' => false),
					'accountname' 	=> array('type' => 'varchar', 'precision' => 200, 'nullable' => false),
					'foldername' 	=> array('type' => 'varchar', 'precision' => 200, 'nullable' => false),
					'uid' 		=> array('type' => 'int', 'precision' => 4, 'nullable' => false),
					'subject'	=> array('type' => 'text'),
					'striped_subject'=> array('type' => 'text'),
					'sender_name'	=> array('type' => 'varchar', 'precision' => 120),
					'sender_address'=> array('type' => 'varchar', 'precision' => 120),
					'to_name'	=> array('type' => 'varchar', 'precision' => 120),
					'to_address'	=> array('type' => 'varchar', 'precision' => 120),
					'date'		=> array('type' => 'varchar', 'precision' => 120),
					'size'		=> array('type' => 'int', 'precision' => 4),
					'attachments'	=> array('type' => 'varchar', 'precision' =>120)
				),
				'pk' => array('accountid','hostname','accountname','foldername','uid'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['setup_info']['felamimail']['currentver'] = '0.9.4';
		return $GLOBALS['setup_info']['felamimail']['currentver'];
	}

?>