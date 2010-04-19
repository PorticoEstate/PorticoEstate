<?php
  /**************************************************************************\
  * phpGroupWare - Setup                                                     *
  * http://www.phpgroupware.org                                              *
  * Created by eTemplates DB-Tools written by ralfbecker@outdoor-training.de *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: tables_update.inc.php,v 1.1.1.1 2005/08/23 05:04:14 skwashd Exp $ */

	$test[] = '0.9.17.500';
	function communik8r_upgrade0_9_17_500()
	{
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_communik8r_email_msgs','msg_uid',array(
			'type' => 'int',
			'precision' => '4',
			'nullable' => True,
			'default' => '0'
		));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_communik8r_email_msgs','msg_uidl',array(
			'type' => 'varchar',
			'precision' => '75',
			'nullable' => True
		));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_communik8r_email_msgs','structure',array(
			'type' => 'longtext',
			'nullable' => True
		));


		$GLOBALS['setup_info']['communik8r']['currentver'] = '0.9.17.501';
		return $GLOBALS['setup_info']['communik8r']['currentver'];
	}


	$test[] = '0.9.17.501';
	function communik8r_upgrade0_9_17_501()
	{
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_communik8r_email_msgs','subject',array(
			'type' => 'varchar',
			'precision' => '250',
			'nullable' => False
		));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_communik8r_email_msgs','sender',array(
			'type' => 'varchar',
			'precision' => '250',
			'nullable' => False
		));


		$GLOBALS['setup_info']['communik8r']['currentver'] = '0.9.17.502';
		return $GLOBALS['setup_info']['communik8r']['currentver'];
	}


	$test[] = '0.9.17.502';
	function communik8r_upgrade0_9_17_502()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_communik8r_accts','signature_id',array(
			'type' => 'int',
			'precision' => '4',
			'nullable' => True,
			'default' => '0'
		));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_communik8r_accts','org',array(
			'type' => 'varchar',
			'precision' => '200',
			'nullable' => True
		));


		$GLOBALS['setup_info']['communik8r']['currentver'] = '0.9.17.503';
		return $GLOBALS['setup_info']['communik8r']['currentver'];
	}


	$test[] = '0.9.17.503';
	function communik8r_upgrade0_9_17_503()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_communik8r_email_mboxes','open_state',array(
			'type' => 'int',
			'precision' => '4',
			'nullable' => True,
			'default' => '0'
		));


		$GLOBALS['setup_info']['communik8r']['currentver'] = '0.9.17.504';
		return $GLOBALS['setup_info']['communik8r']['currentver'];
	}
