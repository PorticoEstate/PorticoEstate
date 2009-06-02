<?php

	$test[] = '0.0.1';
	function rental_upgrade0_0_1()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'rental_composite', array(
				'fd' => array(
					'id' => 		array('type' => 'auto', 'nullable' => false),
					'name' => 				array('type' => 'varchar','precision' => '45','nullable' => false),
					'description' => 		array('type' => 'text'),
					'is_active' => 			array('type' => 'bool','nullable' => false,'default' => 'true'),
					'address_1' =>			array('type' => 'varchar','precision' => '255'),
					'address_2' =>			array('type' => 'varchar','precision' => '255'),
					'house_number' =>		array('type' => 'varchar','precision' => '255'),
					'postcode' =>			array('type' => 'varchar','precision' => '255'),
					'place' =>				array('type' => 'varchar','precision' => '255'),
					'has_custom_address' =>	array('type' => 'bool','nullable' => false,'default' => 'false')
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'rental_unit', array(
				'fd' => array(
					'composite_id' => 		array('type' => 'int', 'precision' => '4', 'nullable' => false),
					'location_id' => 		array('type' => 'int', 'precision' => '4', 'nullable' => false)
				),
				'pk' => array('composite_id','location_id'),
				'fk' => array(
					'rental_composite' => array( 'composite_id' => 'id'),
					'fm_locations' => array( 'location_id' => 'id')
				
				),
				'ix' => array(),
				'uc' => array()
			)
		);
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['rental']['currentver'] = '0.0.2';
			return $GLOBALS['setup_info']['rental']['currentver'];
		}
	}

	$test[] = '0.0.2';
	function rental_upgrade0_0_2()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE rental_unit ADD COLUMN loc1 VARCHAR(50) NOT NULL DEFAULT '-1'"); // We need a default value as this table probably already contains data
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['rental']['currentver'] = '0.0.3';
			return $GLOBALS['setup_info']['rental']['currentver'];
		}
	}
	
	
	$test[] = '0.0.3';
	function rental_upgrade0_0_3()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		
	
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'rental_permission', array(
				'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'subject_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'object_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'object_type' => array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
				'role' => array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
			),
			'pk' => array('id'),
			'fk' => array(
				'phpgw_accounts' => array('subject_id' => 'account_id'),
			),
			'ix' => array(array('object_id', 'object_type'), array('object_type')),
			'uc' => array('subject_id', 'role', 'object_type', 'object_id')
			)
		);
		
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'rental_permission_root', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'subject_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'role' => array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
			),
			'pk' => array('id'),
			'fk' => array(
				'phpgw_accounts' => array('subject_id' => 'account_id'),
			),
			'ix' => array(),
			'uc' => array('subject_id', 'role')
			)
		);
		
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'rental_document_composite', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'name' => array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
				'owner_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'category' => array('type' => 'varchar', 'precision' => '150', 'nullable' => false),
				'description' => array('type' => 'text', 'nullable' => true),
			),
			'pk' => array('id'),
			'fk' => array(
				"rental_composite" => array('owner_id' => 'composite_id'),
			),
			'ix' => array(),
			'uc' => array()
			)
		);
		
		// Change column name composite_id to just id
		$GLOBALS['phpgw_setup']->oProc->m_odb->query("ALTER TABLE table RENAME COLUMN composite_id TO id");
		
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['rental']['currentver'] = '0.0.4';
			return $GLOBALS['setup_info']['rental']['currentver'];
		}
	}
	
?>