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
	
	
/*
 * 	$test[] = '0.1.0.1';
	function rental_upgrade0_1_0_1()
	{
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('rental_contract','date_end',array(
			'type' => 'decimal', 
			'precision' => '20',
			'scale' => '0',
			'nullable' => true
		));
		
		$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0.2';
		return $GLOBALS['setup_info']['rental']['currentver'];
	}
*/
	
?>