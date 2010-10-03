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

	function projects_table_exists($table)
	{
		$tablenames = $GLOBALS['phpgw_setup']->db->table_names();
		while(list($key,$val) = @each($tablenames))
		{
			$all_tables[] = $val['table_name'];
		}
		if(in_array($table,$all_tables))
		{
			if ($GLOBALS['DEBUG']) { echo '<br>' . $table . ' exists.'; }
			return True;
		}
		else
		{
			if ($GLOBALS['DEBUG']) { echo '<br>' . $table . ' does not exist.'; }
			return False;
		}
	}

	function projects_table_column($table,$column)
	{
		$GLOBALS['phpgw_setup']->db->HaltOnError = False;

		$GLOBALS['phpgw_setup']->db->query("SELECT COUNT($column) FROM $table");
		$GLOBALS['phpgw_setup']->db->next_record();
		if (!$GLOBALS['phpgw_setup']->db->f(0))
		{
			if ($GLOBALS['DEBUG']) { echo '<br>' . $table . ' has no column named ' . $column; }
			return False;
		}
		if ($GLOBALS['DEBUG']) { echo '<br>' . $table . ' has a column named ' . $column; }
		return True;
	}

	if ($GLOBALS['setup_info']['projects']['currentver'] == '')
	{
		$GLOBALS['setup_info']['projects']['currentver'] == '0.0.0';
	}

	$test[] = '0.0';
	function projects_upgrade0_0()
	{
		$GLOBALS['setup_info']['projects']['currentver'] == '0.0.0';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.2';
	function projects_upgrade0_8_2()
	{
		$GLOBALS['setup_info']['projects']['currentver'] == '0.0.0';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.0.0';
	function projects_upgrade0_0_0()
	{
		if (projects_table_exists('phpgw_p_projects'))
		{
			if (!projects_table_column('phpgw_p_hours','start_date'))
			{
				return '0.8.4';
			}
			elseif (!projects_table_column('phpgw_p_hours','hours_descr'))
			{
				return '0.8.4.001';
			}
			elseif (!projects_table_column('phpgw_p_projects','category'))
			{
				return '0.8.4.002';
			}
			elseif (!projects_table_column('phpgw_p_projectmembers','type'))
			{
				return '0.8.4.003';
			}
			else
			{
				return '0.8.4.004';
			}
		}
		else
		{
			if (projects_table_exists('p_projectaddress'))
			{
				return '0.8.3';
			}
			else
			{
				return '0.8.3.001';
			}
		}
		return False;
	}

	$test[] = '0.8.3';
	function projects_upgrade0_8_3()
	{
        $GLOBALS['phpgw_setup']->oProc->DropTable('p_projectaddress');

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.3.001';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.3.001';
	function projects_upgrade0_8_3_001()
	{
        $GLOBALS['phpgw_setup']->oProc->AlterColumn('p_projects','access',array('type' => 'varchar','precision' => 25,'nullable' => True));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.3.002';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.3.002';
	function projects_upgrade0_8_3_002()
	{
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('p_invoicepos','invoice_id',array('type' => 'int','precision' => 4,'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('p_deliverypos','delivery_id',array('type' => 'int','precision' => 4,'nullable' => False));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.3.003';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.3.003';
	function projects_upgrade0_8_3_003()
	{
		$newtabledefinition = array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'num' => array('type' => 'varchar','precision' => 20,'nullable' => False),
				'owner' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'entry_date' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'start_date' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'end_date' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'coordinator' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'customer' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'status' => array('type' => 'varchar','precision' => 9,'default' => 'active','nullable' => False),
				'descr' => array('type' => 'text','nullable' => True),
				'title' => array('type' => 'varchar','precision' => 255,'nullable' => False),
				'budget' => array('type' => 'decimal','precision' => 20,'scale' => 2,'default' => 0,'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array('id','num'),
			'uc' => array()
		);

		$GLOBALS['phpgw_setup']->oProc->RenameTable('p_projects','phpgw_p_projects');
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('phpgw_p_projects','date','start_date');
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_p_projects','start_date',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_p_projects','title',array('type' => 'varchar','precision' => 255,'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_p_projects','num',array('type' => 'varchar','precision' => 20,'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_p_projects',$newtabledefinition,'access');
//		$GLOBALS['phpgw_setup']->oProc->query("CREATE INDEX phpgw_p_projects_key ON phpgw_p_projects(id,num)");
		$GLOBALS['phpgw_setup']->oProc->CreateIndex(array('id','num'),'phpgw_p_projects');


		$GLOBALS['phpgw_setup']->oProc->RenameTable('p_activities','phpgw_p_activities');
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_p_activities','descr',array('type' => 'varchar','precision' => 255,'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_p_activities','num',array('type' => 'varchar','precision' => 20,'nullable' => False));
//		$GLOBALS['phpgw_setup']->oProc->query("CREATE INDEX phpgw_p_activities_key ON phpgw_p_activities(id,num)");
		$GLOBALS['phpgw_setup']->oProc->CreateIndex(array('id','num'),'phpgw_p_activities');
		
		$GLOBALS['phpgw_setup']->oProc->RenameTable('p_projectactivities','phpgw_p_projectactivities');
		$GLOBALS['phpgw_setup']->oProc->RenameTable('p_hours','phpgw_p_hours');
		$GLOBALS['phpgw_setup']->oProc->RenameTable('p_projectmembers','phpgw_p_projectmembers');

		$GLOBALS['phpgw_setup']->oProc->RenameTable('p_invoice','phpgw_p_invoice');
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_p_invoice','num',array('type' => 'varchar','precision' => 20,'nullable' => False));
//		$GLOBALS['phpgw_setup']->oProc->query("CREATE INDEX phpgw_p_invoice_key ON phpgw_p_invoice(id,num)");
		$GLOBALS['phpgw_setup']->oProc->CreateIndex(array('id','num'),'phpgw_p_invoice');

		$GLOBALS['phpgw_setup']->oProc->RenameTable('p_invoicepos','phpgw_p_invoicepos');

		$GLOBALS['phpgw_setup']->oProc->RenameTable('p_delivery','phpgw_p_delivery');
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_p_delivery','num',array('type' => 'varchar','precision' => 20,'nullable' => False));
//		$GLOBALS['phpgw_setup']->oProc->query("CREATE INDEX phpgw_p_delivery_key ON phpgw_p_delivery(id,num)");
		$GLOBALS['phpgw_setup']->oProc->CreateIndex(array('id','num'),'phpgw_p_delivery');

		$GLOBALS['phpgw_setup']->oProc->RenameTable('p_deliverypos','phpgw_p_deliverypos');

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.4';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.4';
	function projects_upgrade0_8_4()
	{
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('phpgw_p_hours','date','start_date');
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_p_hours','start_date',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.4.001';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.4.001';
	function projects_upgrade0_8_4_001()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_hours','hours_descr',array('type' => 'varchar','precision' => 255,'nullable' => False));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.4.002';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.4.002';
	function projects_upgrade0_8_4_002()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projects','access',array('type' => 'varchar','precision' => 7,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projects','category',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_p_projects','status',array('type' => 'varchar','precision' => 9,'default' => 'active','nullable' => False));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.4.003';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.4.003';
	function projects_upgrade0_8_4_003()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projectmembers','type',array('type' => 'char','precision' => 2,'nullable' => False));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.4.004';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.4.004';
	function projects_upgrade0_8_4_004()
	{
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_p_activities','remarkreq',array('type' => 'char','precision' => 1,'default' => 'N','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_p_projectactivities','billable',array('type' => 'char','precision' => 1,'default' => 'N','nullable' => False));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.4.005';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.4.005';
	function projects_upgrade0_8_4_005()
	{
		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.4.006';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.4.006';
	function projects_upgrade0_8_4_006()
	{
//		$GLOBALS['phpgw_setup']->oProc->query("CREATE UNIQUE INDEX project_num ON phpgw_p_projects(num)");
//		$GLOBALS['phpgw_setup']->oProc->query("CREATE UNIQUE INDEX invoice_num ON phpgw_p_invoice(num)");
//		$GLOBALS['phpgw_setup']->oProc->query("CREATE UNIQUE INDEX delivery_num ON phpgw_p_delivery(num)");

		$GLOBALS['phpgw_setup']->oProc->CreateIndex(array('num'),'phpgw_p_projects');
		$GLOBALS['phpgw_setup']->oProc->CreateIndex(array('num'),'phpgw_p_invoice');
		$GLOBALS['phpgw_setup']->oProc->CreateIndex(array('num'),'phpgw_p_delivery');
				
		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.5.001';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.5.001';
	function projects_upgrade0_8_5_001()
	{
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_p_hours','status',array('type' => 'varchar','precision' => 6,'default' => 'done','nullable' => False));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.5.002';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.5.002';
	function projects_upgrade0_8_5_002()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_hours','dstatus',array('type' => 'char','precision' => 1,'default' => 'o','nullable' => False));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.5.003';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.5.003';
	function projects_upgrade0_8_5_003()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projects','parent',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.5.004';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.5.004';
	function projects_upgrade0_8_5_004()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_activities','category',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.5.005';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.5.005';
	function projects_upgrade0_8_5_005()
	{
        $GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_p_projects','num',array('type' => 'varchar','precision' => 25,'nullable' => False));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.5.006';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.5.006';
	function projects_upgrade0_8_5_006()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_hours','pro_parent',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));

		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_hooks (hook_appname,hook_location,hook_filename) VALUES ('projects','add_def_pref','hook_add_def_pref.inc.php')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_hooks (hook_appname,hook_location,hook_filename) VALUES ('projects','manual','hook_manual.inc.php')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_hooks (hook_appname,hook_location,hook_filename) VALUES ('projects','about','hook_about.inc.php')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_hooks (hook_appname,hook_location,hook_filename) VALUES ('projects','deleteaccount','hook_deleteaccount.inc.php')");
		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.5.007';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.5.007';
	function projects_upgrade0_8_5_007()
	{
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_p_activities','minperae',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_p_hours','minperae',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.5.008';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.5.008';
	function projects_upgrade0_8_5_008()
	{
		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.001';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.6';
	function projects_upgrade0_8_6()
	{
		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.001';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.6.001';
	function projects_upgrade0_8_6_001()
	{
		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.002';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.6.002';
	function projects_upgrade0_8_6_002()
	{
		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.003';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.6.003';
	function projects_upgrade0_8_6_003()
	{
		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.004';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.6.004';
	function projects_upgrade0_8_6_004()
	{
		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.005';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.6.005';
	function projects_upgrade0_8_6_005()
	{
		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.006';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.6.006';
	function projects_upgrade0_8_6_006()
	{
		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.007';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.6.009';
	function projects_upgrade0_8_6_009()
	{
		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.010';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.001';
	function projects_upgrade0_8_7_001()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projects','time_planned',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projects','date_created',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projects','processor',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.002';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.002';
	function projects_upgrade0_8_7_002()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projects','investment_nr',array('type' => 'varchar','precision' => 50,'nullable' => False));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.003';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.003';
	function projects_upgrade0_8_7_003()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projects','pcosts',array('type' => 'decimal','precision' => 20,'scale' => 2,'default' => 0,'nullable' => False));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.004';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.004';
	function projects_upgrade0_8_7_004()
	{
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_p_pcosts', array(
				'fd' => array(
					'c_id' => array('type' => 'auto','nullable' => False),
					'project_id' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
					'month' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
					'pcosts' => array('type' => 'decimal','precision' => 20,'scale' => 2,'default' => 0,'nullable' => False)
				),
				'pk' => array('c_id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);
		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.005';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.005';
	function projects_upgrade0_8_7_005()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projects','main',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projects','level',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_p_projects','num',array('type' => 'varchar','precision' => 255,'nullable' => False));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.006';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.006';
	function projects_upgrade0_8_7_006()
	{
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_p_mstones', array(
				'fd' => array(
					's_id' => array('type' => 'auto','nullable' => False),
					'project_id' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
					'title' => array('type' => 'varchar','precision' => 255,'nullable' => False),
					'edate' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False)
				),
				'pk' => array('s_id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.007';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.007';
	function projects_upgrade0_8_7_007()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projects','previous',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.008';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.008';
	function projects_upgrade0_8_7_008()
	{
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_p_projects','investment_nr',array('type' => 'varchar','precision' => 50,'nullable' => True));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.009';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.009';
	function projects_upgrade0_8_7_009()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_invoice','owner',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_delivery','owner',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.010';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.010';
	function projects_upgrade0_8_7_010()
	{
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('phpgw_p_projects','num','p_number');
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('phpgw_p_activities','num','a_number');

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('phpgw_p_invoice','num','i_number');
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('phpgw_p_invoice','date','i_date');
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('phpgw_p_delivery','num','d_number');
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('phpgw_p_delivery','date','d_date');

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.011';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.011';
	function projects_upgrade0_8_7_011()
	{
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('phpgw_p_invoice','sum','i_sum');

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.012';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.012';
	function projects_upgrade0_8_7_012()
	{
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_p_projects','p_number',array('type' => 'varchar','precision' => 255,'nullable' => False));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.013';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.013';
	function projects_upgrade0_8_7_013()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projects','customer_nr',array('type' => 'varchar','precision' => 50,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projects','reference',array('type' => 'varchar','precision' => 255,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projects','url',array('type' => 'varchar','precision' => 255,'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projects','result',array('type' => 'text','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projects','test',array('type' => 'text','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projects','quality',array('type' => 'text','nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projects','accounting',array('type' => 'varchar','precision' => 8,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projects','acc_factor',array('type' => 'decimal','precision' => 20,'scale' => 2,'default' => 0,'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projects','billable',array('type' => 'char','precision' => 1,'default' => 'N','nullable' => False));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.014';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.014';
	function projects_upgrade0_8_7_014()
	{
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_p_roles', array(
				'fd' => array(
					'role_id' => array('type' => 'auto','nullable' => False),
					'role_name' => array('type' => 'varchar','precision' => 255,'nullable' => False)
				),
				'pk' => array('role_id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.015';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.015';
	function projects_upgrade0_8_7_015()
	{
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_p_projectmembers','type',array('type' => 'varchar','precision' => 20,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projectmembers','accounting',array('type' => 'decimal','precision' => 20,'scale' => 2,'default' => 0,'nullable' => True));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.016';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.016';
	function projects_upgrade0_8_7_016()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projectmembers','role_id',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => True));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.017';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.017';
	function projects_upgrade0_8_7_017()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_hours','pro_main',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_hours','pro_level',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.018';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.018';
	function projects_upgrade0_8_7_018()
	{
		$new_def = array(
			'fd' => array(
				'id' => array('type' => 'auto','nullable' => False),
				'employee' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'project_id' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'activity_id' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'entry_date' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'start_date' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'end_date' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'remark' => array('type' => 'text','nullable' => True),
				'minutes' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'status' => array('type' => 'varchar','precision' => 6,'default' => 'done','nullable' => False),
				'hours_descr' => array('type' => 'varchar','precision' => 255,'nullable' => False),
				'dstatus' => array('type' => 'char','precision' => 1,'default' => 'o','nullable' => True),
				'pro_parent' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'pro_main' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		);

		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_p_hours',$newdef,'billperae');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_p_hours',$newdef,'minperae');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_p_hours',$newdef,'pro_level');

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.019';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.019';
	function projects_upgrade0_8_7_019()
	{
//		$GLOBALS['phpgw_setup']->oProc->query('alter table phpgw_p_projects drop INDEX p_number');
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('phpgw_p_projects','id','project_id');
		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.020';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.020';
	function projects_upgrade0_8_7_020()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projects','psdate',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projects','pedate',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));
		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.021';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.021';
	function projects_upgrade0_8_7_021()
	{
		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_p_ttracker',array(
			'fd' => array(
				'track_id' => array('type' => 'auto','nullable' => False),
				'employee' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'project_id' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'activity_id' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'start_date' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'end_date' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'remark' => array('type' => 'text','nullable' => True),
				'hours_descr' => array('type' => 'varchar','precision' => 255,'nullable' => False),
				'status' => array('type' => 'varchar','precision' => 8,'nullable' => True)
			),
			'pk' => array('track_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		));
		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.022';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.022';
	function projects_upgrade0_8_7_022()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_ttracker','minutes',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.023';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.023';
	function projects_upgrade0_8_7_023()
	{
		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_p_events',array(
			'fd' => array(
				'event_id'		=> array('type' => 'auto','nullable' => False),
				'event_name'	=> array('type' => 'varchar','precision' => 255,'nullable' => False),
				'event_type'	=> array('type' => 'varchar','precision' => 20,'nullable' => False),
				'event_extra'	=> array('type' => 'int','precision' => 2,'default' => 0,'nullable' => True)
			),
			'pk' => array('event_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		));

		$events[] = array('type'	=> 'assignment',
						'event'		=> 'assignment to project');

		$events[] = array('type'	=> 'assignment',
						'event'		=> 'assignment to role');

		$events[] = array('type'	=> 'limits',
						'event'		=> 'project date due');

		$events[] = array('type'	=> 'limits',
						'event'		=> 'milestone date due');

		$events[] = array('type'	=> 'limits',
						'event'		=> 'budget limit');

		$events[] = array('type'	=> 'dependencies',
						'event'		=> 'project dependencies');

		$events[] = array('type'	=> 'dependencies',
						'event'		=> 'changes of project data');


		while (is_array($events) && (list($not_used,$val) = each($events)))
		{
			$GLOBALS['phpgw_setup']->oProc->query("INSERT into phpgw_p_events (event_name,event_type) values('" . $val['event'] . "','" . $key['type'] . "')",__LINE__,__FILE__);
		}

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.024';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.024';
	function projects_upgrade0_8_7_024()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projectmembers','events',array('type' => 'varchar','precision' => 255,'nullable' => True));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.025';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.025';
	function projects_upgrade0_8_7_025()
	{
		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_p_alarm',array(
			'fd' => array(
				'alarm_id'		=> array('type' => 'auto','nullable' => False),
				'alarm_type'	=> array('type' => 'varchar','precision' => 20,'nullable' => False),
				'project_id'	=> array('type' => 'int','precision' => 4,'default' => 0,'nullable' => True)
			),
			'pk' => array('alarm_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.026';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.026';
	function projects_upgrade0_8_7_026()
	{
		$GLOBALS['phpgw_setup']->oProc->query("INSERT into phpgw_p_events (event_name,event_type,event_extra) values('hours limit','percent',90)",__LINE__,__FILE__);
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE phpgw_p_events set event_extra=90, event_type='percent' where event_name='budget limit'",__LINE__,__FILE__);
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE phpgw_p_events set event_extra=7 where event_name='project date due'",__LINE__,__FILE__);
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE phpgw_p_events set event_extra=7 where event_name='milestone date due'",__LINE__,__FILE__);

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.027';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.027';
	function projects_upgrade0_8_7_027()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_alarm','alarm_extra',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_alarm','alarm_send',array('type' => 'char','precision' => 1,'default' => '1','nullable' => True));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.028';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.028';
	function projects_upgrade0_8_7_028()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projects','priority',array('type' => 'int','precision' => 2,'default' => 0,'nullable' => True));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.029';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.029';
	function projects_upgrade0_8_7_029()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projects','e_budget',array('type' => 'decimal','precision' => 20,'scale' => 2,'default' => 0,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projects','discount',array('type' => 'decimal','precision' => 20,'scale' => 2,'default' => 0,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projects','inv_method',array('type' => 'varchar','precision' => 50,'nullable' => True));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.030';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.030';
	function projects_upgrade0_8_7_030()
	{
//		$GLOBALS['phpgw_setup']->oProc->query('CREATE UNIQUE INDEX project_id on phpgw_p_projects(project_id)');
		$GLOBALS['phpgw_setup']->oProc->CreateIndex(array('project_id'),'phpgw_p_projects');

		$newdef = array(
			'fd' => array(
				'project_id' => array('type' => 'auto','nullable' => False),
				'p_number' => array('type' => 'varchar','precision' => 255,'nullable' => False),
				'owner' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'access' => array('type' => 'varchar','precision' => 7,'nullable' => True),
				'entry_date' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'start_date' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'end_date' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'coordinator' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'customer' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'status' => array('type' => 'varchar','precision' => 9,'default' => 'active','nullable' => False),
				'descr' => array('type' => 'text','nullable' => True),
				'title' => array('type' => 'varchar','precision' => 255,'nullable' => False),
				'budget' => array('type' => 'decimal','precision' => 20,'scale' => 2,'default' => 0,'nullable' => False),
				'category' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'parent' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'time_planned' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'date_created' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'processor' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'investment_nr' => array('type' => 'varchar','precision' => 50,'nullable' => True),
				'main' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'level' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'previous' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'customer_nr' => array('type' => 'varchar','precision' => 50,'nullable' => True),
				'reference' => array('type' => 'varchar','precision' => 255,'nullable' => True),
				'url' => array('type' => 'varchar','precision' => 255,'nullable' => True),
				'result' => array('type' => 'text','nullable' => True),
				'test' => array('type' => 'text','nullable' => True),
				'quality' => array('type' => 'text','nullable' => True),
				'accounting' => array('type' => 'varchar','precision' => 8,'nullable' => True),
				'acc_factor' => array('type' => 'decimal','precision' => 20,'scale' => 2,'default' => 0,'nullable' => False),
				'billable' => array('type' => 'char','precision' => 1,'default' => 'N','nullable' => False),
				'psdate' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'pedate' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
				'priority' => array('type' => 'int','precision' => 2,'default' => 0,'nullable' => True),
				'discount' => array('type' => 'decimal','precision' => 20,'scale' => 2,'default' => 0,'nullable' => True),
				'e_budget' => array('type' => 'decimal','precision' => 20,'scale' => 2,'default' => 0,'nullable' => True),
				'inv_method' => array('type' => 'varchar','precision' => 50,'nullable' => True)
			),
			'pk' => array('project_id'),
			'fk' => array(),
			'ix' => array('project_id'),
			'uc' => array('project_id')
		);

		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_p_projects',$newdef,'pcosts');
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_p_pcosts');

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.031';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.031';
	function projects_upgrade0_8_7_031()
	{
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_p_projects','inv_method',array('type' => 'text','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_hours','billable',array('type' => 'char','precision' => 1,'default' => 'Y','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_hours','km_distance',array('type' => 'decimal','precision' => 20,'scale' => 2,'default' => 0,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_hours','t_journey',array('type' => 'decimal','precision' => 20,'scale' => 2,'default' => 0,'nullable' => True));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.032';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.032';
	function projects_upgrade0_8_7_032()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projectmembers','d_accounting',array('type' => 'decimal','precision' => 20,'scale' => 2,'default' => 0,'nullable' => True));
		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.033';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.033';
	function projects_upgrade0_8_7_033()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_ttracker','km_distance',array('type' => 'decimal','precision' => 20,'scale' => 2,'default' => 0,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_ttracker','t_journey',array('type' => 'decimal','precision' => 20,'scale' => 2,'default' => 0,'nullable' => True));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.034';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.034';
	function projects_upgrade0_8_7_034()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_ttracker','stopped',array('type' => 'char','precision' => 1,'default' => 'N','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projects','acc_factor_d',array('type' => 'decimal','precision' => 20,'scale' => 2,'default' => 0,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projects','discount_type',array('type' => 'varchar','precision' => 7,'nullable' => True));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.035';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.035';
	function projects_upgrade0_8_7_035()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projectmembers','sdate',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projectmembers','edate',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.036';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.036';
	function projects_upgrade0_8_7_036()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projects','plan_bottom_up',array('type' => 'char','precision' => 1,'default' => 'N','nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_p_surcharges',array
		(
			'fd' => array
			(
				'charge_id'			=> array('type' => 'auto','nullable' => False),
				'charge_name'		=> array('type' => 'varchar','precision' => 255,'nullable' => False),
				'charge_percent'	=> array('type' => 'decimal','precision' => 20,'scale' => 2,'default' => 0,'nullable' => True)
			),
			'pk' => array('charge_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		));
		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.037';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.037';
	function projects_upgrade0_8_7_037()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_hours','surcharge',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_ttracker','surcharge',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => True));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.038';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.038';
	function projects_upgrade0_8_7_038()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projectmembers','weekly_workhours',array('type' => 'decimal','precision' => 20,'scale' => 2,'default' => 0,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projectmembers','cost_centre',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => True));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.039';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.039';
	function projects_upgrade0_8_7_039()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projects','customer_org',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.040';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}
	
	$test[] = '0.8.7.040';
	function projects_upgrade0_8_7_040()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_hours','booked',array('type' => 'char','precision' => 1,'default' => 'N','nullable' => False));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.041';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}
	
	$test[] = '0.8.7.041';
	function projects_upgrade0_8_7_041()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projects','direct_work',array('type' => 'char','precision' => 1,'default' => 'Y','nullable' => False));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.042';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}
	
	$test[] = '0.8.7.042';
	function projects_upgrade0_8_7_042()
	{

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.043';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.043';
	function projects_upgrade0_8_7_043()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_ttracker','billable',array('type' => 'char','precision' => 1,'nullable' => false));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.044';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.044';
	function projects_upgrade0_8_7_044()
	{
		// correct project acls
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE phpgw_acl SET acl_appname='project_members' WHERE acl_appname='projects' AND acl_rights='7'",__LINE__,__FILE__);

		// correct pro_main values in p_hours
		$GLOBALS['phpgw_setup']->oProc->query("update phpgw_p_projects, phpgw_p_hours set phpgw_p_hours.pro_main = phpgw_p_projects.main where phpgw_p_projects.project_id = phpgw_p_hours.project_id and phpgw_p_projects.main != phpgw_p_hours.pro_main",__LINE__,__FILE__);
		
		// convert existing journey times in p_hours and p_ttracker from decimal h.m to minutes
		$GLOBALS['phpgw_setup']->oProc->query("update phpgw_p_hours set t_journey=FLOOR(t_journey)*60 + (t_journey-FLOOR(t_journey))*100",__LINE__,__FILE__);
		$GLOBALS['phpgw_setup']->oProc->query("update phpgw_p_ttracker set t_journey=FLOOR(t_journey)*60 + (t_journey-FLOOR(t_journey))*100",__LINE__,__FILE__);

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_p_hours','t_journey',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => true));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_p_ttracker','t_journey',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => true));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.045';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.045';
	function projects_upgrade0_8_7_045()
	{
		/*
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_p_locmap', array(
				'fd' => array(
					'p_group'		=> array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
					'p_location'	=> array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
					'p_locprojnum'	=> array('type' => 'varchar','precision' => 255,'nullable' => False)
				),
				'pk' => array(),
				'fk' => array('p_group'),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query("INSERT into phpgw_p_locmap (p_group,p_location,p_locprojnum) values(103,1,'910186')",__LINE__,__FILE__); // Hannover
		$GLOBALS['phpgw_setup']->oProc->query("INSERT into phpgw_p_locmap (p_group,p_location,p_locprojnum) values(921,2,'910113')",__LINE__,__FILE__); // Berlin
		$GLOBALS['phpgw_setup']->oProc->query("INSERT into phpgw_p_locmap (p_group,p_location,p_locprojnum) values(123,3,'910166')",__LINE__,__FILE__); // Frankfurt / Böblingen 739
		$GLOBALS['phpgw_setup']->oProc->query("INSERT into phpgw_p_locmap (p_group,p_location,p_locprojnum) values(719,4,'910360')",__LINE__,__FILE__); // Düsseldorf
		$GLOBALS['phpgw_setup']->oProc->query("INSERT into phpgw_p_locmap (p_group,p_location,p_locprojnum) values(224,5,'910476')",__LINE__,__FILE__); // München
		// $GLOBALS['phpgw_setup']->oProc->query("INSERT into phpgw_p_locmap (p_group,p_location,p_locprojnum) values(840,6,'')",__LINE__,__FILE__); // Hamburg

		// Hannover D04 01 910186
		// Berlin D04 01 910113
		// Frankfurt/Böblingen D04 01 910166
		// Düsseldorf D04 01 910360
		// München D04 01 910476

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.046';
		*/
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.046';
	function projects_upgrade0_8_7_046()
	{
		// update start_
		$GLOBALS['phpgw_setup']->db->query("SELECT project_id, start_date, end_date, psdate, pedate FROM phpgw_p_projects");
		while($GLOBALS['phpgw_setup']->db->next_record())
		{
			$pro_id   = intval($GLOBALS['phpgw_setup']->db->f('project_id'));
			$s_date   = intval($GLOBALS['phpgw_setup']->db->f('start_date'));
			$e_date   = intval($GLOBALS['phpgw_setup']->db->f('end_date'));
			$p_s_date = intval($GLOBALS['phpgw_setup']->db->f('psdate'));
			$p_e_date = intval($GLOBALS['phpgw_setup']->db->f('pedate'));

			if(date("G", $s_date) == 12)
				$s_date = $s_date - 43200; // 12h*60min*60s = 43200s

			if(date("G", $e_date) == 12)
				$e_date = $e_date - 43200; // 12h*60min*60s = 43200s

			if(date("G", $p_s_date) == 12)
				$p_s_date = $p_s_date - 43200; // 12h*60min*60s = 43200s

			if(date("G", $p_e_date) == 12)
				$p_e_date = $p_e_date - 43200; // 12h*60min*60s = 43200s

			$GLOBALS['phpgw_setup']->oProc->query('UPDATE phpgw_p_projects set start_date=' . $s_date . ', end_date=' . $e_date . ', psdate=' . $p_s_date . ', pedate=' .$p_e_date
											. ' where project_id='.$pro_id ,__LINE__,__FILE__);
		}

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.047';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}


	$test[] = '0.8.7.047';
	function projects_upgrade0_8_7_047()
	{
		$GLOBALS['phpgw_setup']->oProc->CreateIndex(array('project_id'),'phpgw_p_hours');
		$GLOBALS['phpgw_setup']->oProc->CreateIndex(array('start_date'),'phpgw_p_hours');

//		$GLOBALS['phpgw_setup']->oProc->query('CREATE INDEX project_id_3 ON phpgw_p_hours(project_id)');
//		$GLOBALS['phpgw_setup']->oProc->query('CREATE INDEX hours_start ON phpgw_p_hours(start_date)');
		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.048';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.048';
	function projects_upgrade0_8_7_048()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projects','time_planned_childs',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => false));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projects','budget_childs',array('type' => 'decimal','precision' => 20,'scale' => 2,'default' => 0,'nullable' => false));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projects','e_budget_childs',array('type' => 'decimal','precision' => 20,'scale' => 2,'default' => 0,'nullable' => true));

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.049';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.049';
	function projects_upgrade0_8_7_049()
	{
		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.051';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}
	
	$test[] = '0.8.7.051';
	function projects_upgrade0_8_7_051()
	{
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_p_locations', array(
				'fd' => array(
					'location_id'      => array('type' => 'auto','nullable' => False),
					'location_name'    => array('type' => 'varchar','precision' => 255,'nullable' => False),
					'location_ident'   => array('type' => 'varchar','precision' => 255,'nullable' => True),
					'location_custnum' => array('type' => 'varchar','precision' => 255,'nullable' => True)
				),
				'pk' => array('location_id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.052';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.052';
	function projects_upgrade0_8_7_052()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn(
			'phpgw_p_projectmembers',
			'location_id',
			array('type' => 'int','precision' => 4,'default' => 0,'nullable' => True)
		);

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.053';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.053';
	function projects_upgrade0_8_7_053()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn(
			'phpgw_p_projects',
			'salesmanager',
			array('type' => 'int','precision' => 4,'default' => 0,'nullable' => false)
		);

		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.054';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}

	$test[] = '0.8.7.054';
	function projects_upgrade0_8_7_054()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_p_projects','acc_type',array('type' => 'char','precision' => 1,'default' => 'T','nullable' => False));
		$GLOBALS['setup_info']['projects']['currentver'] = '0.8.7.055';
		return $GLOBALS['setup_info']['projects']['currentver'];
	}
?>
