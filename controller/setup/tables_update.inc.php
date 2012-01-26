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
