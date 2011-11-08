<?php

	 /* Update Controller from v 0.1 to 0.1.1 */

	$test[] = '0.1';
	function controller_upgrade0_1()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_procedure','procedure_id',array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => True
		));
		
		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_procedure','revision_no',array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => True
		));
		
		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_procedure','revision_date',array(
			'type' => 'int',
			'precision' => 8,
			'nullable' => True
		));
		
		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.1';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}

	$test[] = '0.1.1';
	function controller_upgrade0_1_1()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_control_group','order_nr',array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => True
		));
		
		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.2';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}
	
	$test[] = '0.1.2';
	function controller_upgrade0_1_2()
	{
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'controller_control_group_list', array(
				'fd' => array(
					'id' => array('type' => 'auto', 'nullable' => false),
					'control_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
					'control_group_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
					'order_nr' => array('type' => 'varchar', 'precision' => '3', 'nullable' => false)
				),
				'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
			)
		);	
		
		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.3';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}
	
	$test[] = '0.1.3';
	function controller_upgrade0_1_3()
	{
		$GLOBALS['phpgw_setup']->oProc->DropColumn('controller_control_group', array(), 'order_nr');
			
		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.4';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}
	
	/* Update Controller from v 0.1.4 to 0.1.5 */
	
	$test[] = '0.1.4';
	function controller_upgrade0_1_4()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_procedure','control_area_id',array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => True
		));
		
		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.5';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}
	
	/* Update Controller from v 0.1.5 to 0.1.6 */
	
	$test[] = '0.1.5';
	function controller_upgrade0_1_5()
	{
		$GLOBALS['phpgw_setup']->oProc->DropColumn('controller_check_list', array(), 'check_list_id');
		
		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_check_list','planned_date',array(
			'type' => 'int',
			'precision' => 8,
			'nullable' => True
		));
		
		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_check_list','completed_date',array(
			'type' => 'int',
			'precision' => 8,
			'nullable' => True
		));
		
		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.6';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}
	
	/* Update Controller from v 0.1.6 to 0.1.7 */
	
	$test[] = '0.1.6';
	function controller_upgrade0_1_6()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_check_list','location_code',array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => True
		));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_check_list','equipment_id',array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => True
		));
		
		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.7';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}
?>