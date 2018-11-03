<?php
	/**
	 * Update Rental from v 0.0.27 to 0.1.0
	 */
	$test[] = '0.0.27';

	function rental_upgrade0_0_27()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_party', 'org_enhet_id', array(
			'type' => 'int', 'precision' => 8, 'nullable' => true));

		$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0';
		return $GLOBALS['setup_info']['rental']['currentver'];
	}
	$test[] = '0.1.0';

	function rental_upgrade0_1_0()
	{
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('rental_contract', 'adjustment_share', array(
			'type' => 'int',
			'precision' => '4',
			'nullable' => true,
			'default' => 100
		));

		$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.1';
		return $GLOBALS['setup_info']['rental']['currentver'];
	}
	$test[] = '0.1.0.1';

	function rental_upgrade0_1_0_1()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_adjustment', 'adjustment_type', array(
			'type' => 'varchar', 'precision' => '255', 'nullable' => true));

		$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.2';
		return $GLOBALS['setup_info']['rental']['currentver'];
	}
	$test[] = '0.1.0.2';

	function rental_upgrade0_1_0_2()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_adjustment', 'is_executed', array(
			'type' => 'bool', 'nullable' => false, 'default' => 'false'));

		$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.3';
		return $GLOBALS['setup_info']['rental']['currentver'];
	}
	$test[] = '0.1.0.3';

	function rental_upgrade0_1_0_3()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_contract', 'publish_comment', array(
			'type' => 'bool', 'nullable' => true, 'default' => 'false'));

		$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.4';
		return $GLOBALS['setup_info']['rental']['currentver'];
	}
	$test[] = '0.1.0.4';

	function rental_upgrade0_1_0_4()
	{
		$asyncservice = CreateObject('phpgwapi.asyncservice');
		$asyncservice->set_timer(
			array('day' => "*/1"), 'rental_run_adjustments', 'rental.soadjustment.run_adjustments', null
		);

		$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.5';
		return $GLOBALS['setup_info']['rental']['currentver'];
	}
	$test[] = '0.1.0.5';

	function rental_upgrade0_1_0_5()
	{
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('rental_notification_workbench', 'notification_id', array(
			'type' => 'int',
			'precision' => '4',
			'nullable' => true
		));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_notification_workbench', 'workbench_message', array(
			'type' => 'text'));

		$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.6';
		return $GLOBALS['setup_info']['rental']['currentver'];
	}
	$test[] = '0.1.0.6';

	function rental_upgrade0_1_0_6()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_invoice', 'serial_number', array(
			'type' => 'int',
			'precision' => '8',
			'nullable' => true
		));

		$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.7';
		return $GLOBALS['setup_info']['rental']['currentver'];
	}
	$test[] = '0.1.0.7';

	function rental_upgrade0_1_0_7()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_price_item', 'standard', array(
			'type' => 'bool',
			'nullable' => true,
			'default' => 'false'
		));

		$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.8';
		return $GLOBALS['setup_info']['rental']['currentver'];
	}
	$test[] = '0.1.0.8';

	function rental_upgrade0_1_0_8()
	{
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('rental_contract_responsibility', 'agresso_export_format', 'export_format');

		$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.9';
		return $GLOBALS['setup_info']['rental']['currentver'];
	}
	$test[] = '0.1.0.9';

	function rental_upgrade0_1_0_9()
	{


		$sql = 'SELECT config_name,config_value FROM phpgw_config'
			. " WHERE config_name = 'files_dir'"
			. " OR config_name = 'file_repository'";

		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$GLOBALS['phpgw_info']['server'][$GLOBALS['phpgw_setup']->oProc->f('config_name', true)] = $GLOBALS['phpgw_setup']->oProc->f('config_value', true);
		}
		$GLOBALS['phpgw']->db = & $GLOBALS['phpgw_setup']->oProc->m_odb;
		$acl = CreateObject('phpgwapi.acl');

		$admins = $acl->get_ids_for_location('run', 1, 'admin');
		$GLOBALS['phpgw_info']['user']['account_id'] = $admins[0];

		//used in vfs
		define('PHPGW_ACL_READ', 1);
		define('PHPGW_ACL_ADD', 2);
		define('PHPGW_ACL_EDIT', 4);
		define('PHPGW_ACL_DELETE', 8);

		$GLOBALS['phpgw']->session = createObject('phpgwapi.sessions');

		//Prepare paths
		$vfs = CreateObject('phpgwapi.vfs');
		$vfs->override_acl = 1;

		$path = "/rental";
		$dir = array('string' => $path, 'relatives' => array( RELATIVE_NONE));
		if (!$vfs->file_exists($dir))
		{
			if (!$vfs->mkdir($dir))
			{
				return;
			}
		}

		$path .= "/billings";
		$dir = array('string' => $path, 'relatives' => array( RELATIVE_NONE));
		if (!$vfs->file_exists($dir))
		{
			if (!$vfs->mkdir($dir))
			{
				return;
			}
		}

		$sql = "SELECT id, export_data FROM rental_billing";
		$db = clone $GLOBALS['phpgw']->db;
		$result = $db->query($sql, __LINE__, __FILE__);

		while ($db->next_record())
		{
			$id = $db->f('id', true);
			$export_data = $db->f('export_data', 'string');
			$file_path = $path . "/{$id}";
			if ($export_data != "")
			{
				$result = $vfs->write
					(
					array
						(
						'string' => $file_path,
						'relatives' => array( RELATIVE_NONE),
						'content' => $export_data
					)
				);
			}
		}

		$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.10';
		return $GLOBALS['setup_info']['rental']['currentver'];
	}
	$test[] = '0.1.0.10';

	function rental_upgrade0_1_0_10()
	{
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('rental_adjustment', 'interval', 'adjustment_interval');

		$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.11';
		return $GLOBALS['setup_info']['rental']['currentver'];
	}
	$test[] = '0.1.0.11';

	function rental_upgrade0_1_0_11()
	{
		// Add adjustment year column
		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_adjustment', 'year', array(
			'type' => 'int',
			'nullable' => true,
			'precision' => '4'
		));

		// Update year column to match the adjustment_date of all existing adjustments
		$so = CreateObject('rental.soadjustment');
		foreach ($so->get(0, NULL, NULL, true, NULL, NULL, NULL) as $adjustment)
		{
			$year = strftime('%Y', $adjustment->get_adjustment_date());
			$adjustment->set_year($year);
			$so->store($adjustment);
		}

		$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.12';
		return $GLOBALS['setup_info']['rental']['currentver'];
	}
	$test[] = '0.1.0.12';

	function rental_upgrade0_1_0_12()
	{
		$asyncservice = CreateObject('phpgwapi.asyncservice');
		$asyncservice->set_timer(
			array('day' => "*/1"), 'rental_sync_party_name', 'rental.uiparty.syncronize_party_name', null
		);

		$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.13';
		return $GLOBALS['setup_info']['rental']['currentver'];
	}
	$test[] = '0.1.0.13';

	function rental_upgrade0_1_0_13()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_composite', 'furnish_type_id', array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => 'True'
		));

		$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.14';
		return $GLOBALS['setup_info']['rental']['currentver'];
	}
	$test[] = '0.1.0.14';

	function rental_upgrade0_1_0_14()
	{
		// Add unit_leader column
		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_party', 'unit_leader', array(
			'type' => 'varchar',
			'nullable' => true,
			'precision' => '255'
		));

		$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.15';
		return $GLOBALS['setup_info']['rental']['currentver'];
	}
	/*
	 * function moved to so-class
	 */
	$test[] = '0.1.0.15';

	function rental_upgrade0_1_0_15()
	{
		$asyncservice = CreateObject('phpgwapi.asyncservice');
		$asyncservice->delete('rental_sync_party_name');
		$asyncservice->set_timer(
			array('day' => "*/1"), 'rental_sync_party_name', 'rental.soparty.syncronize_party_name', null
		);

		$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.16';
		return $GLOBALS['setup_info']['rental']['currentver'];
	}
	$test[] = '0.1.0.16';

	function rental_upgrade0_1_0_16()
	{
		$sql = "INSERT INTO rental_billing_term (title, months) VALUES ('free_of_charge','0')";
		$db = clone $GLOBALS['phpgw']->db;
		$result = $db->query($sql, __LINE__, __FILE__);

		$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.17';
		return $GLOBALS['setup_info']['rental']['currentver'];
	}
	$test[] = '0.1.0.17';

	function rental_upgrade0_1_0_17()
	{
		$GLOBALS['phpgw']->locations->add('.admin', 'Admin section', 'rental');

		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_composite', 'standard_id', array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => 'True'
		));

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'rental_composite_standard', array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'name' => array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
				'factor' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2', 'nullable' => true),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'rental_contract_responsibility_unit', array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'name' => array('type' => 'varchar', 'precision' => '255', 'nullable' => false)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
			)
		);

		$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.18';
		return $GLOBALS['setup_info']['rental']['currentver'];
	}
	$test[] = '0.1.0.18';

	function rental_upgrade0_1_0_18()
	{

		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_contract', 'billing_end', array(
			'type' => 'int',
			'precision' => 8,
			'nullable' => 'True'
		));

		$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.19';
		return $GLOBALS['setup_info']['rental']['currentver'];
	}
	$test[] = '0.1.0.19';

	function rental_upgrade0_1_0_19()
	{
		// Add unit_leader column
		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_adjustment', 'extra_adjustment', array(
			'type' => 'bool',
			'nullable' => true,
			'default' => 'false'
		));

		$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.20';
		return $GLOBALS['setup_info']['rental']['currentver'];
	}
	$test[] = '0.1.0.20';

	function rental_upgrade0_1_0_20()
	{
		$GLOBALS['phpgw']->locations->add('.contract', 'Contract', 'rental', $allow_grant = false, $custom_tbl = false, $c_function = true);
		$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.21';
		return $GLOBALS['setup_info']['rental']['currentver'];
	}
	$test[] = '0.1.0.21';

	function rental_upgrade0_1_0_21()
	{

		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_price_item', 'type', array(
			'type' => 'int',
			'precision' => 2,
			'nullable' => false,
			'default' => 1
		));

		$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.22';
		return $GLOBALS['setup_info']['rental']['currentver'];
	}

	$test[] = '0.1.0.22';
	function rental_upgrade0_1_0_22()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_contract', 'override_adjustment_start', array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => true
		));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_invoice_price_item', 'is_one_time', array(
			'type' => 'bool',
			'nullable' => true,
			'default' => 'false'
		));

		$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.23';
		return $GLOBALS['setup_info']['rental']['currentver'];
	}


	$test[] = '0.1.0.23';
	function rental_upgrade0_1_0_23()
	{

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
					'rental_application', array(
						'fd' => array(
							'id' => array('type' => 'auto', 'nullable' => false),
							'ecodimb' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
							'district_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
							'composite_type' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
							'cleaning' => array('type' => 'int', 'precision' => '2', 'nullable' => true),
							'payment_method' => array('type' => 'int', 'precision' => '2', 'nullable' => false),
							'date_start' => array('type' => 'int', 'precision' => '8', 'nullable' => true),
							'date_end' => array('type' => 'int', 'precision' => '8', 'nullable' => true),
							'assign_date_start' => array('type' => 'int', 'precision' => '8', 'nullable' => true),
							'assign_date_end' => array('type' => 'int', 'precision' => '8', 'nullable' => true),
							'entry_date' => array('type' => 'int', 'precision' => '8', 'nullable' => true),
							'identifier' => array('type' => 'varchar', 'precision' => '20', 'nullable' => false),
							'adjustment_type' => array('type' => 'varchar', 'precision' => '255', 'nullable' => true),
							'firstname' => array('type' => 'varchar', 'precision' => '64', 'nullable' => true),
							'lastname' => array('type' => 'varchar', 'precision' => '64', 'nullable' => true),
							'title' => array('type' => 'varchar', 'precision' => '255', 'nullable' => true),
							'company_name' => array('type' => 'varchar', 'precision' => '255', 'nullable' => true),
							'department' => array('type' => 'varchar', 'precision' => '255', 'nullable' => true),
							'address1' => array('type' => 'varchar', 'precision' => '255', 'nullable' => true),
							'address2' => array('type' => 'varchar', 'precision' => '255', 'nullable' => true),
							'postal_code' => array('type' => 'varchar', 'precision' => '255', 'nullable' => true),
							'place' => array('type' => 'varchar', 'precision' => '255', 'nullable' => true),
							'phone' => array('type' => 'varchar', 'precision' => '255', 'nullable' => true),
							'email' => array('type' => 'varchar', 'precision' => '255', 'nullable' => true),
							'account_number' => array('type' => 'varchar', 'precision' => '255', 'nullable' => true),
							'unit_leader' => array('type' => 'varchar', 'precision' => '255', 'nullable' => true),
							'comment' => array('type' => 'text', 'nullable' => true),
							'status' => array('type' => 'int', 'precision' => '2', 'nullable' => false),

						),
						'pk' => array('id'),
						'fk' => array(),
						'ix' => array(),
						'uc' => array()
					)
				);
			$GLOBALS['phpgw_setup']->oProc->CreateTable(
					'rental_application_composite', array(
						'fd' => array(
							'id' => array('type' => 'auto', 'nullable' => false),
							'application_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
							'composite_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false)
						),
						'pk' => array('id'),
						'fk' => array(
							'rental_application' => array('application_id' => 'id'),
							'rental_composite' => array('composite_id' => 'id')
						),
						'ix' => array(),
						'uc' => array()
					)
				);

		$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.24';
		return $GLOBALS['setup_info']['rental']['currentver'];
	}


	$test[] = '0.1.0.24';
	function rental_upgrade0_1_0_24()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_composite', 'part_of_town_id', array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => true
		));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_composite', 'custom_prize_factor', array(
			'type' => 'decimal',
			'precision' => '20',
			'scale' => '2',
			'nullable' => true,
			'default' => '1.00'
			));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_application', 'executive_officer', array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => true
			));

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('rental_application', 'title', 'job_title');
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('rental_application', 'ecodimb', 'ecodimb_id');

		$GLOBALS['phpgw_setup']->oProc->query("SELECT DISTINCT composite_id, part_of_town_id  FROM rental_unit"
			. " JOIN fm_locations ON rental_unit.location_code = fm_locations.location_code"
			. " JOIN fm_location1 ON fm_locations.loc1 = fm_location1.loc1"
			. " ORDER BY part_of_town_id", __LINE__, __FILE__);

		$composites = array();
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$composites[] = array(
				'composite_id' => $GLOBALS['phpgw_setup']->oProc->f('composite_id'),
				'part_of_town_id' => $GLOBALS['phpgw_setup']->oProc->f('part_of_town_id'),
			);
		}

		foreach ($composites as $composite)
		{
			$GLOBALS['phpgw_setup']->oProc->query("UPDATE rental_composite SET part_of_town_id = {$composite['part_of_town_id']} WHERE id = {$composite['composite_id']}", __LINE__, __FILE__);
		}

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
				'rental_location_factor', array(
					'fd' => array(
						'id' => array('type' => 'auto', 'nullable' => false),
						'part_of_town_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'factor' => array('type' => 'decimal', 'precision' => '20', 'scale' => '2', 'nullable' => false, 'default' => '1.00'),
						'remark' => array('type' => 'text', 'nullable' => true),
						'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
						'entry_date' => array('type' => 'int', 'precision' => 8, 'nullable' => true),
						'modified_date' => array('type' => 'int', 'precision' => 8, 'nullable' => true),
					),
					'pk' => array('id'),
					'fk' => array('fm_part_of_town' => array('part_of_town_id' => 'id')),
					'ix' => array(),
					'uc' => array()
				)
			);

		$GLOBALS['phpgw_setup']->oProc->query("SELECT id FROM fm_part_of_town ORDER BY id", __LINE__, __FILE__);
		$part_of_towns = array();
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$part_of_towns[] =  $GLOBALS['phpgw_setup']->oProc->f('id');
		}

		$now = time();
		foreach ($part_of_towns as $part_of_town)
		{
			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO rental_location_factor"
				. " (part_of_town_id, factor, entry_date, modified_date) VALUES ({$part_of_town},'1.00',{$now}, {$now} )", __LINE__, __FILE__);
		}

		$GLOBALS['phpgw']->locations->add('.application', 'Application', 'rental', $allow_grant = false, $custom_tbl = false, $c_function = true);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.25';
			return $GLOBALS['setup_info']['rental']['currentver'];
		}
	}

	$test[] = '0.1.0.25';
	function rental_upgrade0_1_0_25()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->DropColumn('rental_application', array(), 'comment');

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
				'rental_application_comment', array(
					'fd' => array(
						'id' => array('type' => 'auto', 'nullable' => False),
						'application_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
						'time' => array('type' => 'int', 'precision' => '8', 'nullable' => False),
						'author' => array('type' => 'text', 'nullable' => False),
						'comment' => array('type' => 'text', 'nullable' => False),
						'type' => array('type' => 'varchar', 'precision' => '20', 'nullable' => false,
							'default' => 'comment'),
					),
					'pk' => array('id'),
					'fk' => array(
						'rental_application' => array('application_id' => 'id')),
					'ix' => array(),
					'uc' => array()
				)
			);


		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.26';
			return $GLOBALS['setup_info']['rental']['currentver'];
		}
	}

	$test[] = '0.1.0.26';
	function rental_upgrade0_1_0_26()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_contract_price_item', 'location_factor', array(
			'type' => 'decimal',
			'precision' => '20',
			'scale' => '2',
			'nullable' => true,
			'default' => '1.00'
			));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_contract_price_item', 'standard_factor', array(
			'type' => 'decimal',
			'precision' => '20',
			'scale' => '2',
			'nullable' => true,
			'default' => '1.00'
			));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_contract_price_item', 'custom_factor', array(
			'type' => 'decimal',
			'precision' => '20',
			'scale' => '2',
			'nullable' => true,
			'default' => '1.00'
			));


		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.27';
			return $GLOBALS['setup_info']['rental']['currentver'];
		}
	}

	$test[] = '0.1.0.27';
	function rental_upgrade0_1_0_27()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$metadata = $GLOBALS['phpgw_setup']->oProc->m_odb->metadata('rental_application');
		if(isset($metadata['composite_type']))
		{
			$GLOBALS['phpgw_setup']->oProc->RenameColumn('rental_application', 'composite_type', 'composite_type_id');
		}
		if(isset($metadata['address_1']))
		{
			$GLOBALS['phpgw_setup']->oProc->RenameColumn('rental_application', 'address_1', 'address1');
		}
		if(isset($metadata['address_2']))
		{
			$GLOBALS['phpgw_setup']->oProc->RenameColumn('rental_application', 'address_2', 'address2');
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.28';
			return $GLOBALS['setup_info']['rental']['currentver'];
		}
	}

	$test[] = '0.1.0.28';
	function rental_upgrade0_1_0_28()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_composite', 'composite_type_id', array(
			'type' => 'int',
			'precision' => '2',
			'nullable' => true,
			));

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'rental_composite_type', array(
				'fd' => array(
					'id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
					'name' => array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO rental_composite_type"
			. " (id, name) VALUES (1, 'Type 1' )", __LINE__, __FILE__);
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO rental_composite_type"
			. " (id, name) VALUES (2, 'Type 2' )", __LINE__, __FILE__);

		$GLOBALS['phpgw_setup']->oProc->query("UPDATE rental_composite SET composite_type_id = 1", __LINE__, __FILE__);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.29';
			return $GLOBALS['setup_info']['rental']['currentver'];
		}
	}

	$test[] = '0.1.0.29';
	function rental_upgrade0_1_0_29()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_composite', 'prize_type_id', array(
			'type' => 'int',
			'precision' => '2',
			'nullable' => true,
			'default' => 2
			));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_composite', 'custom_prize',array(
			'type' => 'decimal',
			'precision' => '20',
			'scale' => '2',
			'nullable' => true,
			'default' => '0.00'
			));


		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.30';
			return $GLOBALS['setup_info']['rental']['currentver'];
		}
	}


	$test[] = '0.1.0.30';
	function rental_upgrade0_1_0_30()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('rental_composite', 'custom_prize_factor', 'custom_price_factor');
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('rental_composite', 'custom_prize', 'custom_price');
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('rental_composite', 'prize_type_id', 'price_type_id');


		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.31';
			return $GLOBALS['setup_info']['rental']['currentver'];
		}
	}


	$test[] = '0.1.0.31';
	function rental_upgrade0_1_0_31()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw']->locations->add('.moveout', 'Moveout', 'rental', $allow_grant = true, $custom_tbl = 'rental_moveout', $c_function = true, $c_attrib = true);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'rental_moveout', array(
					'fd' => array(
						'id' => array('type' => 'auto', 'nullable' => false),
						'contract_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
						'account_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
						'created' => array('type' => 'int', 'precision' => '8',  'nullable' => false, 'default' => 'current_timestamp'),
						'modified' => array('type' => 'int', 'precision' => '8',  'nullable' => false, 'default' => 'current_timestamp'),
					),
					'pk' => array('id'),
					'fk' => array(
						'rental_contract' => array('contract_id' => 'id'),
						'phpgw_accounts' => array('account_id' => 'account_id')
					),
					'ix' => array(),
					'uc' => array('contract_id')
				)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'rental_moveout_comment',  array(
					'fd' => array(
						'id' => array('type' => 'auto', 'nullable' => False),
						'moveout_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
						'time' => array('type' => 'int', 'precision' => '8', 'nullable' => False, 'default' => 'current_timestamp'),
						'author' => array('type' => 'text', 'nullable' => False),
						'comment' => array('type' => 'text', 'nullable' => False),
						'type' => array('type' => 'varchar', 'precision' => '20', 'nullable' => false,'default' => 'comment'),
					),
					'pk' => array('id'),
					'fk' => array(
						'rental_moveout' => array('moveout_id' => 'id')),
					'ix' => array(),
					'uc' => array()
				)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.32';
			return $GLOBALS['setup_info']['rental']['currentver'];
		}
	}

	$test[] = '0.1.0.32';
	function rental_upgrade0_1_0_32()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw']->locations->add('.movein', 'Movein', 'rental', $allow_grant = true, $custom_tbl = 'rental_movein', $c_function = true, $c_attrib = true);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'rental_movein', array(
					'fd' => array(
						'id' => array('type' => 'auto', 'nullable' => false),
						'contract_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
						'account_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
						'created' => array('type' => 'int', 'precision' => '8',  'nullable' => false, 'default' => 'current_timestamp'),
						'modified' => array('type' => 'int', 'precision' => '8',  'nullable' => false, 'default' => 'current_timestamp'),
					),
					'pk' => array('id'),
					'fk' => array(
						'rental_contract' => array('contract_id' => 'id'),
						'phpgw_accounts' => array('account_id' => 'account_id')
					),
					'ix' => array(),
					'uc' => array('contract_id')
				)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'rental_movein_comment',  array(
					'fd' => array(
						'id' => array('type' => 'auto', 'nullable' => False),
						'movein_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False),
						'time' => array('type' => 'int', 'precision' => '8', 'nullable' => False, 'default' => 'current_timestamp'),
						'author' => array('type' => 'text', 'nullable' => False),
						'comment' => array('type' => 'text', 'nullable' => False),
						'type' => array('type' => 'varchar', 'precision' => '20', 'nullable' => false,'default' => 'comment'),
					),
					'pk' => array('id'),
					'fk' => array(
						'rental_movein' => array('movein_id' => 'id')),
					'ix' => array(),
					'uc' => array()
				)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.33';
			return $GLOBALS['setup_info']['rental']['currentver'];
		}
	}

	$test[] = '0.1.0.33';
	function rental_upgrade0_1_0_33()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_contract', 'notify_on_expire', array(
			'type' => 'int', 'precision' => 2, 'nullable' => true, 'default' => 0));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_contract', 'notified_time', array(
			'type' => 'int', 'precision' => 8, 'nullable' => true));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.34';
			return $GLOBALS['setup_info']['rental']['currentver'];
		}
	}

	$test[] = '0.1.0.34';
	function rental_upgrade0_1_0_34()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();


		$GLOBALS['phpgw']->locations->add('.email_out', 'email out', 'rental');

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'rental_email_template', array(
				'fd' => array(
					'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
					'name' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False),
					'content' => array('type' => 'text', 'nullable' => True),
					'public' => array('type' => 'int', 'precision' => 2, 'nullable' => True),
					'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
					'created' => array('type' => 'int', 'precision' => 8, 'nullable' => True, 'default' => 'current_timestamp'),
					'modified' => array('type' => 'int', 'precision' => 8, 'nullable' => True, 'default' => 'current_timestamp'),
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'rental_email_out', array(
				'fd' => array(
					'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
					'name' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False),
					'remark' => array('type' => 'text', 'nullable' => True),
					'subject' => array('type' => 'text', 'nullable' => false),
					'content' => array('type' => 'text', 'nullable' => True),
					'user_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
					'created' => array('type' => 'int', 'precision' => 8, 'nullable' => True, 'default' => 'current_timestamp'),
					'modified' => array('type' => 'int', 'precision' => 8, 'nullable' => True, 'default' => 'current_timestamp'),
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'rental_email_out_party', array(
				'fd' => array(
					'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => False),
					'email_out_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
					'party_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
					'status' => array('type' => 'int', 'precision' => 2, 'nullable' => True, 'default' => '0'),
				),
				'pk' => array('id'),
				'fk' => array(
					'rental_email_out' => array('email_out_id' => 'id'),
					'rental_party' => array('party_id' => 'id')
				),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.35';
			return $GLOBALS['setup_info']['rental']['currentver'];
		}
	}



	$test[] = '0.1.0.35';
	function rental_upgrade0_1_0_35()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_composite', 'status_id', array(
			'type' => 'int',
			'precision' => 2,
			'nullable' => true,
			'default' => 1
		));

		$GLOBALS['phpgw_setup']->oProc->query("UPDATE rental_composite SET status_id = 2 WHERE is_active = FALSE", __LINE__, __FILE__);

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('rental_composite', 'status_id', array(
			'type' => 'int',
			'precision' => 2,
			'nullable' => false,
			'default' => 1
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.36';
			return $GLOBALS['setup_info']['rental']['currentver'];
		}
	}

	$test[] = '0.1.0.36';
	function rental_upgrade0_1_0_36()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_contract_price_item', 'billing_id', array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => true
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.37';
			return $GLOBALS['setup_info']['rental']['currentver'];
		}
	}

	$test[] = '0.1.0.37';
	function rental_upgrade0_1_0_37()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_party', 'customer_id', array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => true
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.38';
			return $GLOBALS['setup_info']['rental']['currentver'];
		}
	}

	$test[] = '0.1.0.38';
	function rental_upgrade0_1_0_38()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_contract', 'customer_order_id', array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => true
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.39';
			return $GLOBALS['setup_info']['rental']['currentver'];
		}
	}

	$test[] = '0.1.0.39';
	function rental_upgrade0_1_0_39()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_idgenerator(name,value,descr) "
			. "VALUES('faktura_buntnr', 0, 'buntnr utgående faktura')", __LINE__, __FILE__);

		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_billing', 'voucher_id', array(
			'type' => 'int',
			'precision' => 8,
			'nullable' => true
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.40';
			return $GLOBALS['setup_info']['rental']['currentver'];
		}
	}

