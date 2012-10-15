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
	