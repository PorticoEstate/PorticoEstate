<?php
	$phpgw_baseline = array(
		'controller_control' => array(
			'fd' => array(
				'id' => array('type' => 'auto','precision' => 4,'nullable' => False),
				'title' => array('type' => 'varchar','precision' => '100', 'nullable' => False),
				'description' => array('type' => 'varchar', 'precision' => '255', 'nullable' => True),
				'start_date' => array('type' => 'int', 'precision' => 8, 'nullable' => True),
				'end_date' => array('type' => 'int', 'precision' => 8, 'nullable' => True),
				'procedure_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'requirement_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'costResponsibility_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'responsibility_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'control_area_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'component_type_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'component_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'location_code' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
	 			'repeat_type' => array('type' => 'int', 'precision' => 2, 'nullable' => True),
				'repeat_interval' => array('type' => 'int', 'precision' => 2, 'nullable' => True),
				'enabled' => array('type' => 'int', 'precision' => 2, 'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'controller_control_item_list' => array(
			'fd' => array(
				'id' => array('type' => 'auto','precision' => 4,'nullable' => False),
				'control_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'control_item_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'order_nr' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'controller_control_item' => array(
			'fd' => array(
				'id' => array('type' => 'auto','precision' => 4,'nullable' => False),
				'title' => array('type' => 'varchar','precision' => '255','nullable' => false),
				'required' => array('type' => 'bool','nullable' => true,'default' => 'false'),
				'what_to_do' => array('type' => 'text','nullable' => false),
				'how_to_do' => array('type' => 'text','nullable' => false),
				'control_group_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'control_area_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'controller_check_item' => array(
			'fd' => array(
				'id' => array('type' => 'auto','precision' => 4,'nullable' => False),
				'control_item_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'status' => array('type' => 'bool','nullable' => true,'default' => 'false'),
				'comment' => array('type' => 'varchar','precision' => '255','nullable' => false),
				'check_list_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'controller_check_list' => array(
			'fd' => array(
				'id' => array('type' => 'auto','precision' => 4,'nullable' => False),
				'control_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'status' => array('type' => 'varchar','precision' => '255','nullable' => false),
				'comment' => array('type' => 'varchar','precision' => '255','nullable' => false),
				'deadline' => array('type' => 'int', 'precision' => 8, 'nullable' => True),
				'planned_date' => array('type' => 'int', 'precision' => 8, 'nullable' => True),
				'completed_date' => array('type' => 'int', 'precision' => 8, 'nullable' => True),
				'component_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'location_code' => array('type' => 'int', 'precision' => 4, 'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'controller_procedure' => array(
			'fd' => array(
				'id' => array('type' => 'auto','precision' => 4,'nullable' => False),
				'title' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False),
				'purpose' => array('type' => 'text', 'nullable' => True),
				'responsibility' => array('type' => 'varchar', 'precision' => 255, 'nullable' => True),
				'description' => array('type' => 'text', 'nullable' => True),
				'reference' => array('type' => 'varchar', 'precision' => 255, 'nullable' => True),
				'attachment' => array('type' => 'varchar', 'precision' => 255, 'nullable' => True),
				'start_date' => array('type' => 'int', 'precision' => 8, 'nullable' => True),
				'end_date' => array('type' => 'int', 'precision' => 8, 'nullable' => True),
				'procedure_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'revision_no' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'revision_date' => array('type' => 'int', 'precision' => 8, 'nullable' => True),
				'control_area_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'controller_control_group' => array(
			'fd' => array(
				'id' => array('type' => 'auto','precision' => 4,'nullable' => False),
				'group_name' => array('type' => 'varchar','precision' => '255','nullable' => false),
				'procedure_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'control_area_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'building_part_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'controller_control_area' => array(
			'fd' => array(
				'id' => array('type' => 'auto','precision' => 4,'nullable' => False),
				'title' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'controller_control_group_list' => array(
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
		),
		'controller_control_location_list' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
					'control_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
					'location_code' => array('type' => 'int', 'precision' => '4', 'nullable' => false)
				),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'controller_control_component_list' => array(
				'fd' => array(
					'id' => array('type' => 'auto', 'nullable' => false),
					'control_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
					'component_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false)
				),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'controller_document_types' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'title' => array('type' => 'varchar', 'precision' => '255', 'nullable' => false)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'controller_document' => array(
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
		),
	);
