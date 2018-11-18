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

	/* Update Controller from v 0.1.7 to 0.1.8 */

	$test[] = '0.1.7';
	function controller_upgrade0_1_7()
	{
		$GLOBALS['phpgw_setup']->oProc->DropColumn('controller_check_item', array(), 'status');

		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_check_item','status',array(
			'type' => 'int',
			'precision' => 2,
			'nullable' => true,
			'default' => 0
		));

		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.8';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}

	/* Update Controller from v 0.1.8 to 0.1.9 */

	$test[] = '0.1.8';
	function controller_upgrade0_1_8()
	{
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'controller_control_location_list', array(
				'fd' => array(
					'id' => array('type' => 'auto', 'nullable' => false),
					'control_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
					'location_code' => array('type' => 'int', 'precision' => '4', 'nullable' => false)
				),
				'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
			)
		);

		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.9';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}

	/* Update Controller from v 0.1.9 to 0.1.10
	 * Add table for connecting equipment (BIM) and control
	*/

	$test[] = '0.1.9';
	function controller_upgrade0_1_9()
	{
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'controller_control_equipment_list', array(
				'fd' => array(
					'id' => array('type' => 'auto', 'nullable' => false),
					'control_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
					'equipment_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false)
				),
				'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
			)
		);

		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.10';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}

	/* Update Controller from v 0.1.10 to 0.1.11
	 * Alter from naming from equipment to more generic component
	*/

	$test[] = '0.1.10';
	function controller_upgrade0_1_10()
	{
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'controller_control_component_list', array(
				'fd' => array(
					'id' => array('type' => 'auto', 'nullable' => false),
					'control_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
					'component_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false)
				),
				'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('controller_check_list','equipment_id','component_id');

		$GLOBALS['phpgw_setup']->oProc->DropTable('controller_control_equipment_list');

		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.11';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}

	/* Update Controller from v 0.1.11 to 0.1.12
	 * Add locations for control and checklist
	*/

	$test[] = '0.1.11';
	function controller_upgrade0_1_11()
	{
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('controller_control','equipment_type_id','component_type_id');
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('controller_control','equipment_id','component_id');

		$GLOBALS['phpgw']->locations->add('.control', 'Control', 'controller');
		$GLOBALS['phpgw']->locations->add('.checklist', 'Checklist', 'controller');
		$GLOBALS['phpgw']->locations->add('.procedure', 'Procedure', 'controller');

		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.12';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}

	$test[] = '0.1.12';
	function controller_upgrade0_1_12()
	{
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'controller_document_types', array(
				'fd' => array(
					'id' => array('type' => 'auto', 'nullable' => false),
					'title' => array('type' => 'varchar', 'precision' => '255', 'nullable' => false)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'controller_document', array(
				'fd' => array(
					'id'            => array('type' => 'auto', 'nullable' => false),
					'name'          => array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
					'procedure_id'   => array('type' => 'int', 'precision' => '4', 'nullable' => true),
					'title'         => array('type' => 'varchar', 'precision' => '255', 'nullable' => true),
					'description'   => array('type' => 'text', 'nullable' => true),
					'type_id'       => array('type' => 'int', 'precision' => '4', 'nullable' => false)
				),
				'pk' => array('id'),
				'fk' => array(
					'controller_procedure'   => array('procedure_id' => 'id'),
					'controller_document_types' => array('type_id' => 'id')
				),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.13';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}

	$test[] = '0.1.13';
	function controller_upgrade0_1_13()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_check_item', 'message_ticket_id',array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => true,
			'default' => 0
		));

		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.14';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}

	$test[] = '0.1.14';
	function controller_upgrade0_1_14()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_check_item', 'measurement',array(
			'type' => 'int',
			'precision' => 8,
			'nullable' => true,
			'default' => 0
		));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_control_item', 'type',array(
			'type' => 'varchar',
			'precision' => 255,
			'nullable' => true,
			'default' => 0
		));

		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.15';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}

	$test[] = '0.1.15';
	function controller_upgrade0_1_15()
	{
		$GLOBALS['phpgw_setup']->oProc->DropColumn('controller_check_item', array(), 'message_ticket_id');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('controller_check_item', array(), 'measurement');

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'controller_check_item_case', array(
				'fd' => array(
					'id'            	=> array('type' => 'auto', 'nullable' => false),
					'check_item_id' 	=> array('type' => 'int', 'precision' => '4', 'nullable' => false),
					'message_ticket_id' => array('type' => 'int', 'precision' => '4', 'nullable' => true),
					'measurement' 		=> array('type' => 'int', 'precision' => '4', 'nullable' => true)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.16';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}

	$test[] = '0.1.16';
	function controller_upgrade0_1_16()
	{
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('controller_check_item_case','message_ticket_id',array(
			'type' => 'int',
			'precision' => '8',
			'nullable' => true
		));

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('controller_check_item_case','measurement',array(
			'type' => 'varchar',
			'precision' => '50',
			'nullable' => true
		));

		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.17';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}

	$test[] = '0.1.17';
	function controller_upgrade0_1_17()
	{
		$GLOBALS['phpgw_setup']->oProc->DropTable('controller_check_item_case');

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'controller_check_item_case', array(
				'fd' => array(
					'id' => array('type' => 'auto', 'nullable' => false),
					'check_item_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
					'status' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
					'location_id' => array('type' => 'int', 'precision' => '4', 'nullable' => true), // representer meldingsfregisteret
					'location_item_id' => array('type' => 'int', 'precision' => '8', 'nullable' => true), //meldings id
					'descr' => array('type' => 'text','nullable' => true),
					'user_id' => array('type' => 'int','precision' => '4','nullable' => true),
					'entry_date' => array('type' => 'int', 'precision' => 4,'nullable' => false),
					'modified_date' => array('type' => 'int', 'precision' => 4,'nullable' => True),
					'modified_by' => array('type' => 'int', 'precision' => 4,'nullable' => True),
	              ),
	                'pk' => array('id'),
	                'fk' => array('controller_check_item' => array('check_item_id' => 'id')),
	                'ix' => array(),
	                'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_check_item', 'measurement',array(
			'type' => 'varchar',
			'precision' => 50,
			'nullable' => true
		));

		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.18';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}

	$test[] = '0.1.18';
	function controller_upgrade0_1_18()
	{
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('controller_check_item_case','entry_date',array(
			'type' => 'int',
			'precision' => 8,
			'nullable' => false
		));

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('controller_check_item_case','modified_date',array(
			'type' => 'int',
			'precision' => 8,
			'nullable' => true
		));

		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.19';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}

	$test[] = '0.1.19';
	function controller_upgrade0_1_19()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_check_list','num_open_cases',array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => True
		));

		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.20';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}

	$test[] = '0.1.20';
	function controller_upgrade0_1_20()
	{

		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		//Drop and reinsert because og the datatype int can't be altered to varchar
		$GLOBALS['phpgw_setup']->oProc->DropColumn('controller_control_location_list', array(), 'location_code');

		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_control_location_list','location_code',array(
			'type' => 'varchar',
			'precision' => 30,
			'nullable' => false
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['controller']['currentver'] = '0.1.21';
			return $GLOBALS['setup_info']['controller']['currentver'];
		}
	}

	$test[] = '0.1.21';
	function controller_upgrade0_1_21()
	{
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('controller_check_item','comment',array(
			'type' => 'varchar',
			'precision' => '255',
			'nullable' => true
		));

		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.22';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}

	/* Update Controller from v 0.1.22 to 0.1.23
	 * Add table for configurable status
	*/

	$test[] = '0.1.22';
	function controller_upgrade0_1_22()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'controller_check_item_status', array(
				'fd' => array(
					'id' => array('type' => 'auto','nullable' => False),
					'name' => array('type' => 'varchar','precision' => '50','nullable' => False),
					'open' => array('type' => 'int','precision' => '2','nullable' => True),
					'closed' => array('type' => 'int','precision' => '2','nullable' => True),
					'pending' => array('type' => 'int','precision' => '2','nullable' => True),
					'sorting' => array('type' => 'int','precision' => '4','nullable' => True)
				),
				'pk' => array('id'),
				'ix' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['controller']['currentver'] = '0.1.23';
			return $GLOBALS['setup_info']['controller']['currentver'];
		}
	}

	$test[] = '0.1.23';
	function controller_upgrade0_1_23()
	{
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('controller_procedure','reference',array(
			'type' => 'text',
			'nullable' => true
		));

		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.24';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}

	$test[] = '0.1.24';
	function controller_upgrade0_1_24()
	{

		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		//Drop and reinsert because og the datatype int can't be altered to varchar
		$GLOBALS['phpgw_setup']->oProc->DropColumn('controller_check_list', array(), 'location_code');

		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_check_list','location_code',array(
			'type' => 'varchar',
			'precision' => 30,
			'nullable' => true
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['controller']['currentver'] = '0.1.25';
			return $GLOBALS['setup_info']['controller']['currentver'];
		}
	}

	/* Update Controller from v 0.1.25 to 0.1.26
	 * Added table for connecting gontrol groups to components
	*/

	$test[] = '0.1.25';
	function controller_upgrade0_1_25()
	{
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'controller_control_group_component_list', array(
				'fd' => array(
					'id' => array('type' => 'auto', 'nullable' => false),
					'control_group_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
					'component_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false)
				),
				'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
			)
		);

		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.26';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}

	$test[] = '0.1.26';
	function controller_upgrade0_1_26()
	{

		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		//Drop and reinsert because og the datatype int can't be altered to varchar
		$GLOBALS['phpgw_setup']->oProc->DropColumn('controller_control_group', array(), 'building_part_id');

		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_control_group','building_part_id',array(
			'type' => 'varchar',
			'precision' => 30,
			'nullable' => true
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['controller']['currentver'] = '0.1.27';
			return $GLOBALS['setup_info']['controller']['currentver'];
		}
	}

	$test[] = '0.1.27';
	function controller_upgrade0_1_27()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_check_item_case','measurement',array(
			'type' => 'varchar',
			'precision' => 50,
			'nullable' => true
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['controller']['currentver'] = '0.1.28';
			return $GLOBALS['setup_info']['controller']['currentver'];
		}
	}

	$test[] = '0.1.28';
	function controller_upgrade0_1_28()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_check_list','num_pending_cases',array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => True
		));

		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.29';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}

	$test[] = '0.1.29';
	function controller_upgrade0_1_29()
	{
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('controller_procedure','responsibility',array(
			'type' => 'text',
			'nullable' => true
		));

		$sql = "INSERT INTO controller_document_types (title) values('procedures')";
		$db = clone $GLOBALS['phpgw']->db;
		$result = $db->query($sql, __LINE__, __FILE__);

		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.30';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}

	$test[] = '0.1.30';
	function controller_upgrade0_1_30()
	{
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('controller_control','description',array(
			'type' => 'text',
			'nullable' => true
		));

		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.31';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}

	$test[] = '0.1.31';
	function controller_upgrade0_1_31()
	{
		$GLOBALS['phpgw_setup']->oProc->DropColumn('controller_check_item', array(), 'status');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('controller_check_item', array(), 'comment');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('controller_check_item', array(), 'measurement');

		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.32';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}

	$test[] = '0.1.32';
	function controller_upgrade0_1_32()
	{
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('controller_check_list','comment',array(
			'type' => 'text',
			'nullable' => true
		));

		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.33';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}

	$test[] = '0.1.33';
	function controller_upgrade0_1_33()
	{
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'controller_control_item_option', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' =>  4, 'nullable' => false),
				'option_value' =>  array('type' =>  'varchar','precision' =>  '255','nullable' =>  False),
				'control_item_id' =>  array('type' =>  'int', 'precision' =>  4, 'nullable' =>  True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
			)
		);

		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.34';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}

	$test[] = '0.1.34';
	function controller_upgrade0_1_34()
	{
		$GLOBALS['phpgw_setup']->oProc->DropColumn('controller_control', array(), 'location_code');

		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.35';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}

	$test[] = '0.1.35';
	function controller_upgrade0_1_35()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_control_component_list','location_id',array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => false
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['controller']['currentver'] = '0.1.36';
			return $GLOBALS['setup_info']['controller']['currentver'];
		}
	}

	$test[] = '0.1.36';
	function controller_upgrade0_1_36()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_check_list','location_id',array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => true
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['controller']['currentver'] = '0.1.37';
			return $GLOBALS['setup_info']['controller']['currentver'];
		}
	}

	$test[] = '0.1.37';
	function controller_upgrade0_1_37()
	{
		$GLOBALS['phpgw_setup']->oProc->DropColumn('controller_control', array(), 'component_type_id');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('controller_control', array(), 'component_id');

		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.38';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}

	$test[] = '0.1.38';
	function controller_upgrade0_1_38()
	{

		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$sql = 'SELECT id,status FROM controller_check_list';
		$GLOBALS['phpgw_setup']->oProc->query($sql,__LINE__,__FILE__);

		$status_list = array();
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$status_list[] = array
			(
				'id'		=> $GLOBALS['phpgw_setup']->oProc->f('id'),
				'status'	=> (int) $GLOBALS['phpgw_setup']->oProc->f('status'),
			);
		}


		$GLOBALS['phpgw_setup']->oProc->DropColumn('controller_check_list', array(), 'status');
		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_check_list','status',array(
			'type' => 'int',
			'precision' => 2,
			'nullable' => true
		));


		foreach ($status_list as $entry)
		{
			$sql = "UPDATE controller_check_list SET status = {$entry['status']} WHERE id = {$entry['id']} ";
			$GLOBALS['phpgw_setup']->oProc->query($sql,__LINE__,__FILE__);
		}


		$GLOBALS['phpgw_setup']->oProc->AlterColumn('controller_check_list','status',array(
			'type' => 'int',
			'precision' => 2,
			'nullable' => false
		));

		$GLOBALS['phpgw_setup']->oProc->AlterColumn('controller_check_list','location_code',array(
			'type' => 'varchar',
			'precision' => '30',
			'nullable' => true
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['controller']['currentver'] = '0.1.39';
			return $GLOBALS['setup_info']['controller']['currentver'];
		}
	}

	$test[] = '0.1.39';
	function controller_upgrade0_1_39()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->DropColumn('controller_control_group_component_list', array(), 'component_id');

		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_control_group_component_list','location_id',array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => false
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['controller']['currentver'] = '0.1.40';
			return $GLOBALS['setup_info']['controller']['currentver'];
		}
	}

	$test[] = '0.1.40';
	function controller_upgrade0_1_40()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

	$GLOBALS['phpgw_setup']->oProc->DropTable('controller_control_area');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['controller']['currentver'] = '0.1.41';
			return $GLOBALS['setup_info']['controller']['currentver'];
		}
	}

	$test[] = '0.1.41';
	function controller_upgrade0_1_41()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_check_item_case','location_code',array(
			'type' => 'varchar',
			'precision' => '30',
			'nullable' => true
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['controller']['currentver'] = '0.1.42';
			return $GLOBALS['setup_info']['controller']['currentver'];
		}
	}

	$test[] = '0.1.42';
	function controller_upgrade0_1_42()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_control_group_list','temp_order_nr',array(
			'type' => 'int',
			'precision' => '4',
			'nullable' => true
		));

		$sql = "UPDATE controller_control_group_list SET temp_order_nr = CAST(order_nr AS integer) ";
		$GLOBALS['phpgw_setup']->oProc->query($sql,__LINE__,__FILE__);

		$GLOBALS['phpgw_setup']->oProc->DropColumn('controller_control_group_list', array(), 'order_nr');

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('controller_control_group_list','temp_order_nr','order_nr');

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['controller']['currentver'] = '0.1.43';
			return $GLOBALS['setup_info']['controller']['currentver'];
		}
	}

	$test[] = '0.1.43';
	function controller_upgrade0_1_43()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_check_list','assigned_to',array(
			'type' => 'int',
			'precision' => '4',
			'nullable' => true
		));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_check_list','billable_hours',array(
			'type' => 'decimal',
			'precision' => '20',
			'scale' => '2',
			'nullable' => true
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['controller']['currentver'] = '0.1.44';
			return $GLOBALS['setup_info']['controller']['currentver'];
		}
	}

	$test[] = '0.1.44';
	function controller_upgrade0_1_44()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_control_group','component_location_id',array(
			'type' => 'int',
			'precision' => '4',
			'nullable' => true
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['controller']['currentver'] = '0.1.45';
			return $GLOBALS['setup_info']['controller']['currentver'];
		}
	}

	$test[] = '0.1.45';
	function controller_upgrade0_1_45()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_check_item_case','component_id',array(
			'type' => 'int',
			'precision' => '4',
			'nullable' => true
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['controller']['currentver'] = '0.1.46';
			return $GLOBALS['setup_info']['controller']['currentver'];
		}
	}

	$test[] = '0.1.46';
	function controller_upgrade0_1_46()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_control_group','component_criteria',array(
			'type' => 'text',
			'nullable' => true
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['controller']['currentver'] = '0.1.47';
			return $GLOBALS['setup_info']['controller']['currentver'];
		}
	}


	/**
	* Allow controlgroup assigned to parent level of components
	**/
	$test[] = '0.1.47';
	function controller_upgrade0_1_47()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_check_item_case','component_location_id',array(
			'type' => 'int',
			'precision' => '4',
			'nullable' => true
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['controller']['currentver'] = '0.1.48';
			return $GLOBALS['setup_info']['controller']['currentver'];
		}
	}

	/**
	* Assign responsible user to control at componant from date.
	**/
	$test[] = '0.1.48';
	function controller_upgrade0_1_48()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_control_component_list','assigned_to',array(
			'type' => 'int',
			'precision' => '4',
			'nullable' => true
		));

			$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_control_component_list','start_date',array(
			'type' => 'int',
			'precision' => '8',
			'nullable' => true
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['controller']['currentver'] = '0.1.49';
			return $GLOBALS['setup_info']['controller']['currentver'];
		}
	}

	/**
	* Assign responsible user to control at componant from date.
	**/
	$test[] = '0.1.49';
	function controller_upgrade0_1_49()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_control_component_list','repeat_type',array(
			'type' => 'int',
			'precision' => '2',
			'nullable' => true
		));

			$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_control_component_list','repeat_interval',array(
			'type' => 'int',
			'precision' => '4',
			'nullable' => true
		));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_control_component_list','service_time',array(
			'type' => 'decimal',
			'precision' => 20,
			'nullable' => true,
			'scale' => '2',
			'default' => '0.00'
		));

			$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_control_component_list','controle_time',array(
			'type' => 'decimal',
			'precision' => 20,
			'nullable' => true,
			'scale' => '2',
			'default' => '0.00'
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['controller']['currentver'] = '0.1.50';
			return $GLOBALS['setup_info']['controller']['currentver'];
		}
	}

	$test[] = '0.1.50';
	function controller_upgrade0_1_50()
	{
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'controller_control_serie', array(
					'fd' => array(
						'id'					=> array('type' => 'auto', 'nullable' => false),
						'control_relation_id'	=> array('type' => 'int', 'precision' => '4', 'nullable' => false),
						'control_relation_type'	=> array('type' => 'varchar', 'precision' => '10', 'nullable' => false),
						'assigned_to'			=> array('type' => 'int', 'precision' => '4', 'nullable' => true),
						'start_date'			=> array('type' => 'int', 'precision' => '8', 'nullable' => true),
						'repeat_type'			=> array('type' => 'int', 'precision' => '2', 'nullable' => true),
						'repeat_interval'		=> array('type' => 'int', 'precision' => '4', 'nullable' => true),
						'service_time'			=> array('type' => 'decimal','precision' => '20','scale' => '2','nullable' => True,'default' => '0.00'),
						'controle_time'			=> array('type' => 'decimal','precision' => '20','scale' => '2','nullable' => True,'default' => '0.00'),
					),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_control_component_list','enabled',array(
				'type' => 'int',
				'precision' => '2',
				'nullable' => true,
				'default' => 1,
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_check_list','serie_id',array(
				'type' => 'int',
				'precision' => 4,
				'nullable' => true
			)
		);

		$GLOBALS['phpgw_setup']->oProc->DropColumn('controller_control_component_list',array(),'assigned_to');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('controller_control_component_list',array(),'start_date');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('controller_control_component_list',array(),'repeat_type');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('controller_control_component_list',array(),'repeat_interval');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('controller_control_component_list',array(),'service_time');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('controller_control_component_list',array(),'controle_time');

		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.51';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}

	$test[] = '0.1.51';
	function controller_upgrade0_1_51()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_control_serie','enabled',array(
				'type' => 'int',
				'precision' => '2',
				'nullable' => true,
				'default' => 1,
			)
		);

		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.52';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}

	$test[] = '0.1.52';
	function controller_upgrade0_1_52()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'controller_control_serie_history', array(
					'fd' => array(
						'id'					=> array('type' => 'auto', 'nullable' => false),
						'serie_id'				=> array('type' => 'int', 'precision' => '4', 'nullable' => false),
						'assigned_to'			=> array('type' => 'int', 'precision' => '4', 'nullable' => false),
						'assigned_date'			=> array('type' => 'int', 'precision' => '8', 'nullable' => false),
					),
				'pk' => array('id'),
				'fk' => array('controller_control_serie' => array('serie_id' => 'id')),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->query('SELECT * FROM controller_control_serie');

		$series = array();
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$series[] = array(
				'serie_id' => $GLOBALS['phpgw_setup']->oProc->f('id'),
				'assigned_to' => $GLOBALS['phpgw_setup']->oProc->f('assigned_to'),
				'assigned_date'	=> time()
			);
		}

		foreach($series as $serie)
		{
			$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO controller_control_serie_history (serie_id, assigned_to, assigned_date)"
			. " VALUES ({$serie['serie_id']}, {$serie['assigned_to']}, {$serie['assigned_date']})  ");
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['controller']['currentver'] = '0.1.53';
			return $GLOBALS['setup_info']['controller']['currentver'];
		}
	}
	
	$test[] = '0.1.53';
	function controller_upgrade0_1_53()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_check_list','original_deadline',array(
				'type' => 'int',
				'precision' => '8',
				'nullable' => true
			)
		);

		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.54';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}

	$test[] = '0.1.54';
	function controller_upgrade0_1_54()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_procedure','modified_date',array(
				'type' => 'int',
				'precision' => 8,
				'nullable' => true
			)
		);
		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_procedure','modified_by',array(
				'type' => 'int',
				'precision' => 4,
				'nullable' => true
			)
		);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['controller']['currentver'] = '0.1.55';
			return $GLOBALS['setup_info']['controller']['currentver'];
		}
	}

	$test[] = '0.1.55';
	function controller_upgrade0_1_55()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_check_item_case','component_child_location_id',array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => true
		));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_check_item_case','component_child_item_id',array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => true
		));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_check_item_case','condition_degree',array(
			'type' => 'int',
			'precision' => 2,
			'nullable' => true,
			'default'=> 2
		));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_check_item_case','consequence',array(
			'type' => 'int',
			'precision' => 2,
			'nullable' => true,
			'default'=> 2
		));

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['controller']['currentver'] = '0.1.56';
			return $GLOBALS['setup_info']['controller']['currentver'];
		}
	}

