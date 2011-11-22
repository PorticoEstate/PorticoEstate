<?php

	/**
	 * Update Activitycalendar from v 0.1 to 0.1.1
	 */

	$test[] = '0.1';
	function activitycalendar_upgrade0_1()
	{
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('activity_activity','target',array(
			'type' => 'varchar', 
			'precision' => '255'
		));
		
		$GLOBALS['setup_info']['activitycalendar']['currentver'] = '0.1.1';
		return $GLOBALS['setup_info']['activitycalendar']['currentver'];
	}
	
	$test[] = '0.1.1';
	function activitycalendar_upgrade0_1_1()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('activity_arena','active',array(
			'type' => 'bool',
			'default' => 'true'
		));
		
		$GLOBALS['setup_info']['activitycalendar']['currentver'] = '0.1.2';
		return $GLOBALS['setup_info']['activitycalendar']['currentver'];
	}
	
	$test[] = '0.1.2';
	function activitycalendar_upgrade0_1_2()
	{
		$def_val = substr(base64_encode(rand(1000000000,9999999999)),0, 10);
		$GLOBALS['phpgw_setup']->oProc->AddColumn('activity_activity','secret',array(
			'type' => 'text',
			'default' => $def_val,
			'nullable' => 'False'
		));
		
		$GLOBALS['setup_info']['activitycalendar']['currentver'] = '0.1.3';
		return $GLOBALS['setup_info']['activitycalendar']['currentver'];
	}
	
	$test[] = '0.1.3';
	function activitycalendar_upgrade0_1_3()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('activity_activity','internal_arena',array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => 'True'
		));
		
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'activity_organization', array(
			'fd' => array(
				'id' => array('type' => 'auto','precision' => 4,'nullable' => False),
				'name' => array('type' => 'varchar','precision' => '255','nullable' => false),
				'district' => array('type' => 'varchar','precision' => '255','nullable' => false),
				'homepage' => array('type' => 'varchar','precision' => '255','nullable' => false),
				'description' => array('type' => 'text','nullable' => false),
				'email' => array('type' => 'varchar','precision' => '255','nullable' => false),
				'phone' => array('type' => 'varchar','precision' => '255','nullable' => false),
				'address' => array('type' => 'varchar','precision' => '255','nullable' => false),
				'orgno' => array('type' => 'varchar','precision' => '255','nullable' => false),
				'change_type' => array('type' => 'varchar','precision' => '255','default' => 'new','nullable' => false),
				'transferred' => array('type' => 'bool','nullable' => true,'default' => 'false')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
			)
		);
		
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'activity_group', array(
				'fd' => array(
				'id' => array('type' => 'auto','precision' => 4,'nullable' => False),
				'organization_id' => array('type' => 'varchar','precision' => '255','nullable' => false),
				'name' => array('type' => 'varchar','precision' => '255','nullable' => false),
				'description' => array('type' => 'text','nullable' => false),
				'change_type' => array('type' => 'varchar','precision' => '255','default' => 'new', 'nullable' => false),
				'transferred' => array('type' => 'bool','nullable' => true,'default' => 'false')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
			)
		);
		
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'activity_contact_person', array(
			'fd' => array(
				'id' => array('type' => 'auto','precision' => 4,'nullable' => False),
				'organization_id' => array('type' => 'varchar','precision' => '255','nullable' => false),
				'group_id' => array('type' => 'varchar','precision' => '255','nullable' => false),
				'name' => array('type' => 'varchar','precision' => '255','nullable' => false),
				'phone' => array('type' => 'varchar','precision' => '255','nullable' => false),
				'email' => array('type' => 'varchar','precision' => '255','nullable' => false),
				'address' => array('type' => 'varchar','precision' => '255','nullable' => false),
				'zipcode' => array('type' => 'varchar','precision' => '255','nullable' => false),
				'city' => array('type' => 'varchar','precision' => '255','nullable' => false),
				'transferred' => array('type' => 'bool','nullable' => true,'default' => 'false')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
			)
		);
		
		$GLOBALS['setup_info']['activitycalendar']['currentver'] = '0.1.4';
		return $GLOBALS['setup_info']['activitycalendar']['currentver'];
	}
	$test[] = '0.1.4';
	function activitycalendar_upgrade0_1_4()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('activity_activity','contact_person_2_address',array(
			'type' => 'varchar',
			'precision' => '255',
			'nullable' => true
		));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('activity_activity','contact_person_2_zip',array(
			'type' => 'varchar',
			'precision' => '255',
			'nullable' => true
		));
		
		$GLOBALS['setup_info']['activitycalendar']['currentver'] = '0.1.5';
		return $GLOBALS['setup_info']['activitycalendar']['currentver'];
	}
	
	$test[] = '0.1.5';
	function activitycalendar_upgrade0_1_5()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('activity_activity','frontend',array(
			'type' => 'bool',
			'nullable' => 'false',
			'nullable' => true
		));
		
		$GLOBALS['setup_info']['activitycalendar']['currentver'] = '0.1.6';
		return $GLOBALS['setup_info']['activitycalendar']['currentver'];
	}
	
	$test[] = '0.1.6';
	function activitycalendar_upgrade0_1_6()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('activity_activity','new_org',array(
			'type' => 'bool',
			'default' => 'false',
			'nullable' => true
		));
		
		$GLOBALS['setup_info']['activitycalendar']['currentver'] = '0.1.7';
		return $GLOBALS['setup_info']['activitycalendar']['currentver'];
	}
	
	$test[] = '0.1.7';
	function activitycalendar_upgrade0_1_7()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('activity_organization','original_org_id',array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => true
		));
		
		$GLOBALS['setup_info']['activitycalendar']['currentver'] = '0.1.8';
		return $GLOBALS['setup_info']['activitycalendar']['currentver'];
	}
	
?>
