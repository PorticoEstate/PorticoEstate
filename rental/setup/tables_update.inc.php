<?php

	/**
	 * Update Rental from v 0.0.27 to 0.1.0
	 */

	$test[] = '0.0.27';
	function rental_upgrade0_0_27()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_party','org_enhet_id', array ('type' => 'int','precision' => 8, 'nullable' => true));
		
		$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0';
		return $GLOBALS['setup_info']['rental']['currentver'];
	}
	
	$test[] = '0.1.0';
	function rental_upgrade0_1_0()
	{
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('rental_contract','adjustment_share',array(
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
		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_adjustment','adjustment_type', array('type' => 'varchar','precision' => '255','nullable' => true));
		
		$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.2';
		return $GLOBALS['setup_info']['rental']['currentver'];
	}
	
	$test[] = '0.1.0.2';
	function rental_upgrade0_1_0_2()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_adjustment','is_executed', array('type' => 'bool','nullable' => false,'default' => 'false'));
		
		$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.3';
		return $GLOBALS['setup_info']['rental']['currentver'];
	}
	
	$test[] = '0.1.0.3';
	function rental_upgrade0_1_0_3()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_contract','publish_comment', array('type' => 'bool','nullable' => true,'default' => 'false'));
		
		$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.4';
		return $GLOBALS['setup_info']['rental']['currentver'];
	}
	
	$test[] = '0.1.0.4';
	function rental_upgrade0_1_0_4()
	{
		$asyncservice = CreateObject('phpgwapi.asyncservice');
		$asyncservice->set_timer(
			array('day' => "*/1"),
			'rental_run_adjustments',
			'rental.soadjustment.run_adjustments',
			null
			);
		
		$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.5';
		return $GLOBALS['setup_info']['rental']['currentver'];
	}
	
	$test[] = '0.1.0.5';
	function rental_upgrade0_1_0_5()
	{
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('rental_notification_workbench','notification_id',array(
			'type' => 'int', 
			'precision' => '4',
			'nullable' => true
		));
		
		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_notification_workbench','workbench_message', array('type' => 'text'));
		
		$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.6';
		return $GLOBALS['setup_info']['rental']['currentver'];
	}
	
	$test[] = '0.1.0.6';
	function rental_upgrade0_1_0_6()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_invoice','serial_number',array(
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
		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_price_item','standard',array(
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
		while ( $GLOBALS['phpgw_setup']->oProc->next_record() )
		{
		$GLOBALS['phpgw_info']['server'][$GLOBALS['phpgw_setup']->oProc->f('config_name', true)] = $GLOBALS['phpgw_setup']->oProc->f('config_value', true);
		}
		$GLOBALS['phpgw']->db = & $GLOBALS['phpgw_setup']->oProc->m_odb;
		$acl = CreateObject('phpgwapi.acl');
		
		$admins = $acl->get_ids_for_location('run', 1, 'admin');
		$GLOBALS['phpgw_info']['user']['account_id'] = $admins[0];
		
		//used in vfs
		define('PHPGW_ACL_READ',1);
		define('PHPGW_ACL_ADD',2);
		define('PHPGW_ACL_EDIT',4);
		define('PHPGW_ACL_DELETE',8);
		
		$GLOBALS['phpgw']->session	 = createObject('phpgwapi.sessions');
		
		//Prepare paths
		$vfs = CreateObject('phpgwapi.vfs');
		$vfs->override_acl = 1;
		
		$path = "/rental";
		$dir = array('string' => $path, RELATIVE_NONE);
		if(!$vfs->file_exists($dir)){
			if(!$vfs->mkdir($dir))
			{
				return;
			}
		}
		
		$path .= "/billings";
		$dir = array('string' => $path, RELATIVE_NONE);
		if(!$vfs->file_exists($dir)){
			if(!$vfs->mkdir($dir))
			{
				return;
			}
		}
		
		$sql = "SELECT id, export_data FROM rental_billing";
		$db = clone $GLOBALS['phpgw']->db;
		$result = $db->query($sql, __LINE__, __FILE__);
		
		while($db->next_record())
		{
			$id = $db->f('id',true);
			$export_data = $db->f('export_data','string');
			$file_path = $path."/{$id}";
			if($export_data != ""){			
				$result = $vfs->write
				(
					array
					(
						'string' => $file_path,
						RELATIVE_NONE,
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
		foreach ($so->get(0, NULL, NULL, true, NULL, NULL, NULL) as $adjustment) {
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
			array('day' => "*/1"),
			'rental_sync_party_name',
			'rental.uiparty.syncronize_party_name',
			null
			);
		
		$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.13';
		return $GLOBALS['setup_info']['rental']['currentver'];
	}
		
	$test[] = '0.1.0.13';
	function rental_upgrade0_1_0_13()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_composite','furnish_type_id',array(
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
?>