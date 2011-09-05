<?php

	 /* Update Controller from v 0.1 to 0.1.1
	 */

	$test[] = '0.1';
	function controller_upgrade0_1()
	{
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
		
		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.1';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}
	
?>