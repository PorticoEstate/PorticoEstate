<?php
	/**************************************************************************\
	* phpGroupWare - Webpage news admin                                        *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	* --------------------------------------------                             *
	* This program was sponsered by Golden Glair productions                   *
	* http://www.goldenglair.com                                               *
	\**************************************************************************/

	/* $Id: tables_update.inc.php 16990 2006-08-23 13:32:03Z skwashd $ */

	$test[] = '0.0.1';
	function news_admin_upgrade0_0_1()
	{
		$GLOBALS['setup_info']['news_admin']['currentver'] = '0.8.1';
		return $GLOBALS['setup_info']['news_admin']['currentver'];
	}

	$test[] = '0.8.1';
	function news_admin_upgrade0_8_1()
	{
		$GLOBALS['phpgw_setup']->oProc->RenameTable('webpage_news','phpgw_news');

		$GLOBALS['setup_info']['news_admin']['currentver'] = '0.8.1.001';
		return $GLOBALS['setup_info']['news_admin']['currentver'];
	}

	$test[] = '0.8.1.001';
	function news_admin_upgrade0_8_1_001()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_news','news_cat',array('type' => 'int','precision' => 4,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->query("update phpgw_news set news_cat='0'",__LINE__,__FILE__);

		$GLOBALS['setup_info']['news_admin']['currentver'] = '0.8.1.002';
		return $GLOBALS['setup_info']['news_admin']['currentver'];
	}

	$test[] = '0.8.1.002';
	function news_admin_upgrade0_8_1_002()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_news','news_teaser',array(
			'type' => 'varchar',
			'precision' => '255',
			'nullable' => True
		));


		$GLOBALS['setup_info']['news_admin']['currentver'] = '0.9.14.500';
		return $GLOBALS['setup_info']['news_admin']['currentver'];
	}

	$test[] = '0.9.14.500';
	function news_admin_upgrade0_9_14_500()
	{
		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_news_export',array(
			'fd' => array(
				'cat_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'export_type' => array('type' => 'int','precision' => '2','nullable' => True),
				'export_itemsyntax' => array('type' => 'int','precision' => '2','nullable' => True),
				'export_title' => array('type' => 'varchar','precision' => '255','nullable' => True),
				'export_link' => array('type' => 'varchar','precision' => '255','nullable' => True),
				'export_description' => array('type' => 'text', 'nullable' => True),
				'export_img_title' => array('type' => 'varchar','precision' => '255','nullable' => True),
				'export_img_url' => array('type' => 'varchar','precision' => '255','nullable' => True),
				'export_img_link' => array('type' => 'varchar','precision' => '255','nullable' => True),
			),
			'pk' => array('cat_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		));

		$GLOBALS['setup_info']['news_admin']['currentver'] = '0.9.14.501';
		return $GLOBALS['setup_info']['news_admin']['currentver'];
	}

	$test[] = '0.9.14.501';
	function news_admin_upgrade0_9_14_501()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_news','news_begin',array(
			'type' => 'int','precision' => '4','nullable' => True
		));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_news','news_end',array(
			'type' => 'int','precision' => '4','nullable' => True
		));
		$db2 = $GLOBALS['phpgw_setup']->db;
		$GLOBALS['phpgw_setup']->oProc->query('SELECT news_id,news_status FROM phpgw_news');
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$unixtimestampmax = 2147483647;
			$db2->query('UPDATE phpgw_news SET news_begin=news_date,news_end=' . 
				(($GLOBALS['phpgw_setup']->oProc->f('news_status') == 'Active') ? $unixtimestampmax : 'news_date') .
				' WHERE news_id=' . $GLOBALS['phpgw_setup']->oProc->f('news_id'));
		}
		$newtbldef = array(
			'fd' => array(
				'news_id' => array('type' => 'auto','nullable' => False),
				'news_date' => array('type' => 'int','precision' => '4','nullable' => True),
				'news_subject' => array('type' => 'varchar','precision' => '255','nullable' => True),
				'news_submittedby' => array('type' => 'varchar','precision' => '255','nullable' => True),
				'news_content' => array('type' => 'blob','nullable' => True),
				'news_begin' => array('type' => 'int','precision' => '4','nullable' => True),
				'news_end' => array('type' => 'int','precision' => '4','nullable' => True),
				'news_cat' => array('type' => 'int','precision' => '4','nullable' => True),
				'news_teaser' => array('type' => 'varchar','precision' => '255','nullable' => True)
			),
			'pk' => array('news_id'),
			'fk' => array(),
			'ix' => array('news_date','news_subject'),
			'uc' => array()
		);
		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_news',$newtbldef,'news_status');

		$GLOBALS['setup_info']['news_admin']['currentver'] = '0.9.14.502';
		return $GLOBALS['setup_info']['news_admin']['currentver'];
	}

		


	$test[] = '0.9.14.502';
	function news_admin_upgrade0_9_14_502()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_news','is_html',array(
			'type' => 'int',
			'precision' => '2',
			'nullable' => False,
			'default' => '0'
		));


		$GLOBALS['setup_info']['news_admin']['currentver'] = '0.9.14.503';
		return $GLOBALS['setup_info']['news_admin']['currentver'];
	}


	$test[] = '0.9.14.503';
	function news_admin_upgrade0_9_14_503()
	{
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_news','news_teaser',array(
			'type' => 'text',
			'nullable' => True
		));

		$GLOBALS['setup_info']['news_admin']['currentver'] = '0.9.17.501';
		return $GLOBALS['setup_info']['news_admin']['currentver'];
	}

	$test[] = '0.9.17.501';
	function news_admin_upgrade0_9_17_501()
	{
		$cats = array();
		//We skip using cats class as it is too problematic using it in setup
		$GLOBALS['phpgw_setup']->oProc->query("SELECT cat_id, cat_name FROM phpgw_categories WHERE cat_appname = 'news_admin'", __LINE__, __FILE__);
		while ( $GLOBALS['phpgw_setup']->oProc->next_record() )
		{
			$cats[$GLOBALS['phpgw_setup']->oProc->f('cat_id')] = $GLOBALS['phpgw_setup']->oProc->f('cat_name', true);
		}

		//The ACL class is useless when run in setup, so we do the insert directly - our api is sooo gr8 d00dz! - not!
		$GLOBALS['phpgw_setup']->oProc->query('INSERT INTO phpgw_acl_location (appname, id, descr, allow_grant)'
		 			. " VALUES ('news_admin', '.', 'Top', 1)", __LINE__, __FILE__);
		foreach ( $cats as $cat_id => $cat_name )
		{
			$GLOBALS['phpgw_setup']->oProc->query('INSERT INTO phpgw_acl_location (appname, id, descr, allow_grant)'
		 			. " VALUES ('news_admin', 'L{$cat_id}', '{$cat_name}', 1)", __LINE__, __FILE__);

		}
		$GLOBALS['setup_info']['news_admin']['currentver'] = '0.9.17.502';
		return $GLOBALS['setup_info']['news_admin']['currentver'];
	}


	$test[] = '0.9.17.502';
	function news_admin_upgrade0_9_17_502()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_news','lastmod',array(
			'type' => 'int',
			'precision' => '4',
			'nullable' => False
		));

		$GLOBALS['phpgw_setup']->oProc->query('UPDATE phpgw_news SET lastmod = ' . time(), __LINE__, __FILE__);

		$GLOBALS['setup_info']['news_admin']['currentver'] = '0.9.17.503';
		return $GLOBALS['setup_info']['news_admin']['currentver'];
	}
?>
