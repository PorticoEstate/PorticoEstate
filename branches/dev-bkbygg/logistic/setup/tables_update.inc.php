<?php
	 
	$test[] = '0.0.1';
	function logistic_upgrade0_0_1()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('lg_requirement','date_from','start_date');
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('lg_requirement','date_to','end_date');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['logistic']['currentver'] = '0.0.2';
			return $GLOBALS['setup_info']['logistic']['currentver'];
		}
	}
	
	$test[] = '0.0.2';
	function logistic_upgrade0_0_2()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->DropColumn('lg_requirement_value', array(), 'type_requirement_id');
		
		$GLOBALS['phpgw_setup']->oProc->AddColumn('lg_requirement_value','cust_attribute_id',array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => False
		));
	
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['logistic']['currentver'] = '0.0.3';
			return $GLOBALS['setup_info']['logistic']['currentver'];
		}
	}
	
	$test[] = '0.0.3';
	function logistic_upgrade0_0_3()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
	
		$GLOBALS['phpgw_setup']->oProc->AddColumn('lg_project','start_date',array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => true
		));
		
		$GLOBALS['phpgw_setup']->oProc->AddColumn('lg_project','end_date',array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => true
		));
	
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['logistic']['currentver'] = '0.0.4';
			return $GLOBALS['setup_info']['logistic']['currentver'];
		}
	}

	$test[] = '0.0.4';
	function logistic_upgrade0_0_4()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('lg_project_type','create_date',array(
			'type' => 'int',
			'precision' => 8,
			'nullable' => false
		));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('lg_project','start_date',array(
			'type' => 'int',
			'precision' => 8,
			'nullable' => true
		));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('lg_project','end_date',array(
			'type' => 'int',
			'precision' => 8,
			'nullable' => true
		));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('lg_project','create_date',array(
			'type' => 'int',
			'precision' => 8,
			'nullable' => false
		));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('lg_activity','start_date',array(
			'type' => 'int',
			'precision' => 8,
			'nullable' => true
		));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('lg_activity','end_date',array(
			'type' => 'int',
			'precision' => 8,
			'nullable' => true
		));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('lg_activity','create_date',array(
			'type' => 'int',
			'precision' => 8,
			'nullable' => false
		));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('lg_activity','update_date',array(
			'type' => 'int',
			'precision' => 8,
			'nullable' => false
		));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('lg_requirement','start_date',array(
			'type' => 'int',
			'precision' => 8,
			'nullable' => true
		));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('lg_requirement','end_date',array(
			'type' => 'int',
			'precision' => 8,
			'nullable' => true
		));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('lg_requirement','create_date',array(
			'type' => 'int',
			'precision' => 8,
			'nullable' => false
		));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('lg_requirement_resource_allocation','create_date',array(
			'type' => 'int',
			'precision' => 8,
			'nullable' => false
		));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('lg_resource_type_requirement','create_date',array(
			'type' => 'int',
			'precision' => 8,
			'nullable' => false
		));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('lg_requirement_value','create_date',array(
			'type' => 'int',
			'precision' => 8,
			'nullable' => false
		));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('lg_calendar','create_date',array(
			'type' => 'int',
			'precision' => 8,
			'nullable' => false
		));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('lg_calendar','start_date',array(
			'type' => 'int',
			'precision' => 8,
			'nullable' => false
		));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('lg_calendar','end_date',array(
			'type' => 'int',
			'precision' => 8,
			'nullable' => false
		));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('lg_calendar','allocation_id',array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => true
		));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('lg_requirement_resource_allocation','calendar_id',array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => true,
			'default' => 0//FIXME
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['logistic']['currentver'] = '0.0.5';
			return $GLOBALS['setup_info']['logistic']['currentver'];
		}
	}

	$test[] = '0.0.5';
	function logistic_upgrade0_0_5()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

//		$GLOBALS['phpgw_setup']->oProc->DropColumn('lg_calendar', array(), 'allocation_id');

/*
		$GLOBALS['phpgw_setup']->oProc->AddColumn('lg_calendar','item_inventory_id',array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => true
		));
*/
		$GLOBALS['phpgw_setup']->oProc->AddColumn('lg_calendar','item_inventory_amount',array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => true
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['logistic']['currentver'] = '0.0.6';
			return $GLOBALS['setup_info']['logistic']['currentver'];
		}
	}


	$test[] = '0.0.6';
	function logistic_upgrade0_0_6()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('lg_requirement_resource_allocation','ticket_id',array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => true
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['logistic']['currentver'] = '0.0.7';
			return $GLOBALS['setup_info']['logistic']['currentver'];
		}
	}
