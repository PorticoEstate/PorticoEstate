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

	$test[] = '0.9.13';
	function forum_upgrade0_9_13()
	{
		global $setup_info, $phpgw_setup;

		$phpgw_setup->oProc->RenameTable('f_body','phpgw_forum_body');
		$phpgw_setup->oProc->RenameTable('f_categories','phpgw_forum_categories');
		$phpgw_setup->oProc->RenameTable('f_forums','phpgw_forum_forums');
		$phpgw_setup->oProc->RenameTable('f_threads','phpgw_forum_threads');

		$setup_info['forum']['currentver'] = '0.9.13.001';
		return $setup_info['forum']['currentver'];
	}

	$test[] = '0.9.13.001';
	function forum_upgrade0_9_13_001()
	{
		// If for some odd reason this fields are blank, the upgrade will fail without these
		$GLOBALS['phpgw_setup']->db->query("update phpgw_forum_threads set subject=' ' where subject=''",__LINE__,__FILE__);
		$GLOBALS['phpgw_setup']->db->query("update phpgw_forum_threads set host=' ' where host=''",__LINE__,__FILE__);

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_forum_threads','postdate',array('type' => 'timestamp','nullable' => False,'default' => 'current_timestamp'));

		$GLOBALS['setup_info']['forum']['currentver'] = '0.9.13.002';
		return $GLOBALS['setup_info']['forum']['currentver'];
	}

	$test[] = '0.9.13.002';
	function forum_upgrade0_9_13_002()
	{
		// If for some odd reason this fields are blank, the upgrade will fail without these
		$GLOBALS['phpgw_setup']->oProc->query("update phpgw_forum_threads set author='0'",__LINE__,__FILE__);
		$GLOBALS['phpgw_setup']->oProc->query("update phpgw_forum_threads set email=' '",__LINE__,__FILE__);
		$GLOBALS['phpgw_setup']->oProc->query("update phpgw_forum_threads set host=' '",__LINE__,__FILE__);

		$GLOBALS['phpgw_setup']->oProc->altercolumn('phpgw_forum_threads','author',array('type' => 'int', 'precision' => 4,'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->renamecolumn('phpgw_forum_threads','author','thread_owner');

		$GLOBALS['setup_info']['forum']['currentver'] = '0.9.13.003';
		return $GLOBALS['setup_info']['forum']['currentver'];
	}


	$test[] = '0.9.13.003';
	function forum_upgrade0_9_13_003()
	{
		$new_table_def = array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'postdate' => array('type' => 'timestamp','nullable' => False,'default' => 'current_timestamp'),
				'main' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'parent' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'cat_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'for_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'thread_owner' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'subject' => array('type' => 'varchar', 'precision' => 255,'nullable' => False),
				'host' => array('type' => 'varchar', 'precision' => 255,'nullable' => False),
				'stat' => array('type' => 'int', 'precision' => 2,'nullable' => False),
				'thread' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'depth' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'pos' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'n_replies' => array('type' => 'int', 'precision' => 4,'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		);
		$GLOBALS['phpgw_setup']->oProc->dropcolumn('phpgw_forum_threads',$new_table_def,'email');

		$GLOBALS['setup_info']['forum']['currentver'] = '0.9.13.004';
		return $GLOBALS['setup_info']['forum']['currentver'];
	}

	$test[] = '0.9.13.004';
	function forum_upgrade0_9_13_004()
	{
		$new_table_def = array(
			'fd' => array(
				'id' => array('type' => 'auto'),
				'postdate' => array('type' => 'timestamp','nullable' => False,'default' => 'current_timestamp'),
				'main' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'parent' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'cat_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'for_id' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'thread_owner' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'subject' => array('type' => 'varchar', 'precision' => 255,'nullable' => False),
				'stat' => array('type' => 'int', 'precision' => 2,'nullable' => False),
				'thread' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'depth' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'pos' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'n_replies' => array('type' => 'int', 'precision' => 4,'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		);
		$GLOBALS['phpgw_setup']->oProc->dropcolumn('phpgw_forum_threads',$new_table_def,'host');

		$GLOBALS['setup_info']['forum']['currentver'] = '0.9.13.005';
		return $GLOBALS['setup_info']['forum']['currentver'];
	}

	$test[] = '0.9.13.005';
	function forum_upgrade0_9_13_005()
	{
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_forum_categories','name',array('type' => 'varchar', 'precision' => 255,'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_forum_categories','descr',array('type' => 'varchar', 'precision' => 255,'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_forum_forums','name',array('type' => 'varchar', 'precision' => 255,'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_forum_forums','groups',array('type' => 'varchar', 'precision' => 255,'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_forum_forums','descr',array('type' => 'varchar', 'precision' => 255,'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_forum_threads','subject',array('type' => 'varchar', 'precision' => 255,'nullable' => False));

		$GLOBALS['setup_info']['forum']['currentver'] = '0.9.13.006';
		return $GLOBALS['setup_info']['forum']['currentver'];
	}

