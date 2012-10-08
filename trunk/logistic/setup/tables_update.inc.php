<?php
	 /* Update Logistic from v 0.0.1 to 0.0.2
	  * Add column 'description' to table activity
	  */

	$test[] = '0.0.1';
	function logistic_upgrade0_0_1()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('lg_activity','description',array(
			'type' => 'text',
			'nullable' => True
		));

		$GLOBALS['setup_info']['logistic']['currentver'] = '0.0.2';
		return $GLOBALS['setup_info']['logistic']['currentver'];
	}

	/* Update Logistic from v 0.0.2 to 0.0.3
	* Add locations
	*/

	$test[] = '0.0.2';
	function logistic_upgrade0_0_2()
	{
		$GLOBALS['phpgw']->locations->add('.', 'Topp', 'logistic');
		$GLOBALS['phpgw']->locations->add('.project', 'Prosjekt', 'logistic');
		$GLOBALS['phpgw']->locations->add('.activity', 'Aktivitet', 'logistic');

		$GLOBALS['setup_info']['logistic']['currentver'] = '0.0.3';
		return $GLOBALS['setup_info']['logistic']['currentver'];
	}

	/*
	 * Update Logistic from v 0.0.3 to 0.0.4
	 * Add columns for custom attribute id and location id to lg_requirement_value
	 * Alter lg_bim_item_type_requirement: add cust_attribute_id column, remove attribute-colums
	 */

	$test[] = '0.0.3';
	function logistic_upgrade0_0_3()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('lg_requirement_value','location_id',array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => True
		));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('lg_requirement_value','cust_attribute_id',array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => True
		));

		$GLOBALS['phpgw_setup']->oProc->DropColumn('lg_bim_item_type_requirement', array(), 'attribute_name');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('lg_bim_item_type_requirement', array(), 'attribute_type');

		$GLOBALS['phpgw_setup']->oProc->AddColumn('lg_bim_item_type_requirement','cust_attribute_id_id',array(
			'type' => 'varchar',
			'precision' => '255',
			'nullable' => True
		));

		$GLOBALS['setup_info']['logistic']['currentver'] = '0.0.4';
		return $GLOBALS['setup_info']['logistic']['currentver'];
	}