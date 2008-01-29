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
	/* $Id: tables_update.inc.php 12744 2003-05-16 22:51:06Z ceb $ */

	// Note; The first two functions are used in the third function, which is the
	// Version 0.0.0 upgrade test.  The test is not complete. -- Milosch

	function inv_table_exists($table)
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

	function inv_table_column($table,$column)
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

	if ($GLOBALS['setup_info']['inv']['currentver'] == '')
	{
		$GLOBALS['setup_info']['inv']['currentver'] == '0.0.0';
	}

	$test[] = '0.0.0';
	function inv_upgrade0_0_0()
	{
		if (inv_table_exists('phpgw_inv_products'))
		{
			if (inv_table_exists('phpgw_inv_categorys'))
			{
				return '0.8.3';
			}
			elseif (!inv_table_column('phpgw_inv_orders','owner'))
			{
				return '0.8.3.001';
			}
			elseif (inv_table_exists('phpgw_inv_dist'))
			{
				return '0.8.3.002';
			}
			elseif (!inv_table_column('phpgw_inv_products','ftp'))
			{
				return '0.8.3.003';
			}
			elseif (!inv_table_column('phpgw_inv_orders','status'))
			{
				return '0.8.3.004';
			}
			elseif (!inv_table_column('phpgw_inv_orders','access'))
			{
				return '0.8.3.005';
			}
			elseif (!inv_table_exists('phpgw_inv_stockrooms'))
			{
				return '0.8.3.006';
			}
			elseif (!inv_table_column('phpgw_inv_orderpos','istatus'))
			{
				return '0.8.3.008';
			}
			else
			{
				return '0.8.3.009';
			}
		}
		else
		{
			if (!inv_table_column('inv_categorys','tax'))
			{
				return '0.8.1.001';
			}
			elseif (!inv_table_exists('inv_orders'))
			{
				return '0.8.1.002';
			}
			elseif (!inv_table_exists('inv_delivery'))
			{
				return '0.8.1.003';
			}
			elseif (!inv_table_exists('inv_invoice'))
			{
				return '0.8.1.004';
			}
			elseif (!inv_table_exists('inv_deliverypos'))
			{
				return '0.8.1.005';
			}
			elseif (!inv_table_exists('inv_invoicepos'))
			{
				return '0.8.1.006';
			}
			elseif (!inv_table_column('inv_categorys','level'))
			{
				return '0.8.1.008';
			}
			elseif(!inv_table_exists('phpgw_inv_products'))
			{
				return '0.8.1.009';
			}
		}
		return False;
	}

	$test[] = '0.8.1.001';
	function inv_upgrade0_8_1_001()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('inv_categorys','tax',array('type' => 'decimal','precision' => 6,'scale' => 2,'nullable' => True));

		$GLOBALS['setup_info']['inv']['currentver'] = '0.8.1.002';
		return $GLOBALS['setup_info']['inv']['currentver'];
	}

	$test[] = '0.8.1.002';
	function inv_upgrade0_8_1_002()
	{
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'inv_orders', array(
				'fd' => array(
					'id' => array('type' => 'auto','nullable' => False),
					'num' => array('type' => 'varchar','decision' => 11,'nullable' => False),
					'date' => array('type' => 'int','decision' => 4,'nullable' => True),
					'customer' => array('type' => 'int','decision' => 4,'nullable' => True),
					'descr' => array('type' => 'text','nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array('num')
			),
			'inv_orderpos', array(
				'fd' => array(
					'id' => array('type' => 'auto','nullable' => False),
					'order_id' => array('type' => 'varchar','precision' => 11,'nullable' => False),
					'product_id' => array('type' => 'varchar','precision' => 11,'nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['setup_info']['inv']['currentver'] = '0.8.1.003';
		return $GLOBALS['setup_info']['inv']['currentver'];
	}

	$test[] = '0.8.1.003';
	function inv_upgrade0_8_1_003()
	{
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'inv_delivery', array(
				'fd' => array(
					'id' => array('type' => 'auto','nullable' => False),
					'num' => array('type' => 'varchar','precision' => 11,'nullable' => False),
					'date' => array('type' => 'int','precision' => 4,'nullable' => True),
					'order_id' => array('type' => 'varchar','precision' => 11,'nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array('num')
			)
		);

		$GLOBALS['setup_info']['inv']['currentver'] = '0.8.1.004';
		return $GLOBALS['setup_info']['inv']['currentver'];
	}

	$test[] = '0.8.1.004';
	function inv_upgrade0_8_1_004()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('inv_orderpos','tax',array('type' => 'decimal','precision' => 6,'scale' => 2,'default' => 0,'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'inv_invoice', array(
				'fd' => array(
					'id' => array('type' => 'auto','nullable' => False),
					'num' => array('type' => 'varchar','precision' => 11,'nullable' => False),
					'date' => array('type' => 'int','precision' => 4,'nullable' => True),
					'order_id' => array('type' => 'varchar','precision' => 11,'nullable' => True),
					'sum' => array('type' => 'decimal','precision' => 20,'scale' => 2,'nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array('num')
			)
		);

		$GLOBALS['setup_info']['inv']['currentver'] = '0.8.1.005';
		return $GLOBALS['setup_info']['inv']['currentver'];
	}

	$test[] = '0.8.1.005';
	function inv_upgrade0_8_1_005()
	{
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'inv_deliverypos', array(
				'fd' => array(
					'id' => array('type' => 'auto','nullable' => False),
					'invoice_id' => array('type' => 'varchar','precision' => 11,'nullable' => False),
					'product_id' => array('type' => 'varchar','precision' => 11,'nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['setup_info']['inv']['currentver'] = '0.8.1.006';
		return $GLOBALS['setup_info']['inv']['currentver'];
	}

	$test[] = '0.8.1.006';
	function inv_upgrade0_8_1_006()
	{
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'inv_invoicepos', array(
				'fd' => array(
					'id' => array('type' => 'auto','nullable' => False),
					'invoice_id' => array('type' => 'varchar','precision' => 11,'nullable' => False),
					'product_id' => array('type' => 'varchar','precision' => 11,'nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['setup_info']['inv']['currentver'] = '0.8.1.007';
		return $GLOBALS['setup_info']['inv']['currentver'];
	}

	$test[] = '0.8.1.007';
	function inv_upgrade0_8_1_007()
	{
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'inv_invoicepos', array(
				'fd' => array(
					'id' => array('type' => 'auto','nullable' => False),
					'invoice_id' => array('type' => 'varchar','precision' => 11,'nullable' => False),
					'product_id' => array('type' => 'varchar','precision' => 11,'nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['setup_info']['inv']['currentver'] = '0.8.1.008';
		return $GLOBALS['setup_info']['inv']['currentver'];
	}

	$test[] = '0.8.1.008';
	function inv_upgrade0_8_1_008()
	{
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('inv_products','category',array('type' => 'varchar','precision' => 3,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('inv_categorys','level',array('type' => 'int','precision' => 3,'default' => 0,'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('inv_categorys','par_cat',array('type' => 'varchar','precision' => 3,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('inv_categorys','main_cat',array('type' => 'varchar','precision' => 3,'nullable' => True));

		$GLOBALS['setup_info']['inv']['currentver'] = '0.8.1.009';
		return $GLOBALS['setup_info']['inv']['currentver'];
	}

	$test[] = '0.8.1.009';
	function inv_upgrade0_8_1_009()
	{
		$GLOBALS['phpgw_setup']->oProc->RenameTable('inv_products','phpgw_inv_products');
		$GLOBALS['phpgw_setup']->oProc->RenameTable('inv_dist','phpgw_inv_dist');
		$GLOBALS['phpgw_setup']->oProc->RenameTable('inv_categorys','phpgw_inv_categorys');
		$GLOBALS['phpgw_setup']->oProc->RenameTable('inv_status_list','phpgw_inv_statuslist');
		$GLOBALS['phpgw_setup']->oProc->RenameTable('inv_orders','phpgw_inv_orders');
		$GLOBALS['phpgw_setup']->oProc->RenameTable('inv_orderpos','phpgw_inv_orderpos');
		$GLOBALS['phpgw_setup']->oProc->RenameTable('inv_delivery','phpgw_inv_delivery');
		$GLOBALS['phpgw_setup']->oProc->RenameTable('inv_deliverypos','phpgw_inv_deliverypos');
		$GLOBALS['phpgw_setup']->oProc->RenameTable('inv_invoice','phpgw_inv_invoice');
		$GLOBALS['phpgw_setup']->oProc->RenameTable('inv_invoicepos','phpgw_inv_invoicepos');

		$GLOBALS['setup_info']['inv']['currentver'] = '0.8.2';
		return $GLOBALS['setup_info']['inv']['currentver'];
	}

	$test[] = '0.8.2';
	function inv_upgrade0_8_2()
	{
		$GLOBALS['setup_info']['inv']['currentver'] = '0.8.3';
		return $GLOBALS['setup_info']['inv']['currentver'];
	}

	$test[] = '0.8.3';
	function inv_upgrade0_8_3()
	{
		$db = $GLOBALS['phpgw_setup']->db;

		$GLOBALS['phpgw_setup']->oProc->query("select * from phpgw_inv_categorys");

		if ($GLOBALS['phpgw_setup']->oProc->num_rows())
		{
			while ($GLOBALS['phpgw_setup']->oProc->next_record())
			{
				$data = Array();
				$data['number'] = $GLOBALS['phpgw_setup']->oProc->f('number');
				$data['tax'] = $GLOBALS['phpgw_setup']->oProc->f('tax');
				$data = serialize($data);

				$db->query("select con as main from phpgw_inv_categorys where number='" . $GLOBALS['phpgw_setup']->oProc->f('main_cat') . "'");
				$db->next_record();
				$cat_main = $db->f('main');

				$db->query("select con as parent from phpgw_inv_categorys where number='" . $GLOBALS['phpgw_setup']->oProc->f('par_cat') . "'");
				$db->next_record();
				$cat_parent = $db->f('parent');

				$db->query("INSERT into phpgw_categories(cat_name,cat_appname,cat_owner,cat_access,cat_level,cat_parent,cat_main,cat_data) values ('"
					. $GLOBALS['phpgw_setup']->oProc->f('name') . "','inv',0,'public','" . $GLOBALS['phpgw_setup']->oProc->f('level') . "','$cat_parent','$cat_main',$data)");
			}
		}

		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_inv_categorys');

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_inv_products','id',array('type' => 'varchar','precision' => 11,'nullable' => True));

		$GLOBALS['setup_info']['inv']['currentver'] = '0.8.3.001';
		return $GLOBALS['setup_info']['inv']['currentver'];
	}

	$test[] = '0.8.3.001';
	function inv_upgrade0_8_3_001()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_inv_orders','owner',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_inv_products','category',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_inv_orderpos','order_id',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_inv_orderpos','product_id',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_inv_deliverypos','delivery_id',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_inv_deliverypos','product_id',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_inv_invoicepos','invoice_id',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_inv_invoicepos','product_id',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_inv_invoice','order_id',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_inv_delivery','order_id',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));

		$GLOBALS['setup_info']['inv']['currentver'] = '0.8.3.002';
		return $GLOBALS['setup_info']['inv']['currentver'];
	}

	$test[] = '0.8.3.002';
	function inv_upgrade0_8_3_002()
	{
		$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_inv_dist');

		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_inv_products','date',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_inv_products','serial',array('type' => 'varchar','precision' => 64,'nullable' => False));

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('phpgw_inv_products','des','descr');

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_inv_products','id',array('type' => 'varchar','precision' => 20,'nullable' => False));

		$GLOBALS['phpgw_setup']->oProc->query("CREATE INDEX phpgw_inv_products_key ON phpgw_inv_products(con,id)");
		$GLOBALS['phpgw_setup']->oProc->query("CREATE INDEX phpgw_inv_orders_key ON phpgw_inv_orders(id,num)");
		$GLOBALS['phpgw_setup']->oProc->query("CREATE INDEX phpgw_inv_delivery_key ON phpgw_inv_delivery(id,num)");
		$GLOBALS['phpgw_setup']->oProc->query("CREATE INDEX phpgw_inv_invoice_key ON phpgw_inv_invoice(id,num)");

		$GLOBALS['setup_info']['inv']['currentver'] = '0.8.3.003';
		return $GLOBALS['setup_info']['inv']['currentver'];
	}

	$test[] = '0.8.3.003';
	function inv_upgrade0_8_3_003()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_inv_products','ftp',array('type' => 'varchar','precision' => 255,'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_inv_products','sdate',array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False));

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('phpgw_inv_products','date','pdate');

		$GLOBALS['phpgw_setup']->oProc->query("insert into phpgw_inv_statuslist (status_name) values ('saled')");

		$GLOBALS['setup_info']['inv']['currentver'] = '0.8.3.004';
		return $GLOBALS['setup_info']['inv']['currentver'];
	}

	$test[] = '0.8.3.004';
	function inv_upgrade0_8_3_004()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_inv_orders','status',array('type' => 'varchar','precision' => 7,'default' => 'open','nullable' => False));

		$GLOBALS['phpgw_setup']->oProc->query("insert into phpgw_inv_statuslist (status_name) values ('archive')");

		$GLOBALS['setup_info']['inv']['currentver'] = '0.8.3.005';
		return $GLOBALS['setup_info']['inv']['currentver'];
	}

	$test[] = '0.8.3.005';
	function inv_upgrade0_8_3_005()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_inv_orders','access',array('type' => 'varchar','precision' => 7,'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->query("insert into phpgw_inv_statuslist (status_name) values ('archive')");

		$GLOBALS['setup_info']['inv']['currentver'] = '0.8.3.006';
		return $GLOBALS['setup_info']['inv']['currentver'];
	}

	$test[] = '0.8.3.006';
	function inv_upgrade0_8_3_006()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_inv_products','bin',array('type' => 'int','precision' => 11,'default' => 0,'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_inv_products','product_note',array('type' => 'text','nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->query("DELETE from phpgw_inv_statuslist where status_name='saled'");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT into phpgw_inv_statuslist (status_name) values ('sold')");

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_inv_stockrooms', array(
				'fd' => array(
					'id' => array('type' => 'auto','nullable' => False),
					'room_owner' => array('type' => 'int','precision' => 4,'default' => 0,'nullable' => False),
					'room_access' => array('type' => 'varchar','precision' => 7,'nullable' => True),
					'room_name' => array('type' => 'varchar','precision' => 255,'nullable' => False),
					'room_note' => array('type' => 'text','nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['setup_info']['inv']['currentver'] = '0.8.3.007';
		return $GLOBALS['setup_info']['inv']['currentver'];
	}

	$test[] = '0.8.3.007';
	function inv_upgrade0_8_3_007()
	{
		$GLOBALS['phpgw_setup']->oProc->query("CREATE UNIQUE INDEX delivery_num ON phpgw_inv_delivery(num)");
		$GLOBALS['phpgw_setup']->oProc->query("CREATE UNIQUE INDEX invoice_num ON phpgw_inv_invoice(num)");
		$GLOBALS['phpgw_setup']->oProc->query("CREATE UNIQUE INDEX order_num ON phpgw_inv_orders(num)");

		$GLOBALS['setup_info']['inv']['currentver'] = '0.8.3.008';
		return $GLOBALS['setup_info']['inv']['currentver'];
	}

	$test[] = '0.8.3.008';
	function inv_upgrade0_8_3_008()
	{
        $GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_inv_orderpos','dstatus',array('type' => 'char','precision' => 4,'default' => 'open','nullable' => False));
        $GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_inv_orderpos','istatus',array('type' => 'char','precision' => 4,'default' => 'open','nullable' => False));

		$GLOBALS['setup_info']['inv']['currentver'] = '0.8.3.009';
		return $GLOBALS['setup_info']['inv']['currentver'];
	}

	$test[] = '0.8.3.009';
	function inv_upgrade0_8_3_009()
	{
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_hooks (hook_appname,hook_location,hook_filename) VALUES ('inv','add_def_pref','hook_add_def_pref.inc.php')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_hooks (hook_appname,hook_location,hook_filename) VALUES ('inv','manual','hook_manual.inc.php')");
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_hooks (hook_appname,hook_location,hook_filename) VALUES ('inv','about','hook_about.inc.php')");
		$GLOBALS['setup_info']['inv']['currentver'] = '0.8.3.010';
		return $GLOBALS['setup_info']['inv']['currentver'];
	}

	$test[] = '0.8.3.010';
	function inv_upgrade0_8_3_010()
	{
		$GLOBALS['setup_info']['inv']['currentver'] = '0.8.5.001';
		return $GLOBALS['setup_info']['inv']['currentver'];
	}

	$test[] = '0.8.4';
	function inv_upgrade0_8_4()
	{
		$GLOBALS['setup_info']['inv']['currentver'] = '0.8.5.001';
		return $GLOBALS['setup_info']['inv']['currentver'];
	}
?>
