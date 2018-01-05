<?php

$phpgw_baseline = array(
		'lg_project_type' => array(
				'fd' => array(
						'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
						'name' => array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
						'create_user' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'create_date' => array('type' => 'int', 'precision' => 8, 'nullable' => false),
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
		),
		'lg_project' => array(
				'fd' => array(
						'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
						'name' => array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
						'project_type_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'description' => array('type' => 'text', 'nullable' => false),
						'create_user' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'create_date' => array('type' => 'int', 'precision' => 8, 'nullable' => false),
						'start_date' => array('type' => 'int', 'precision' => 8, 'nullable' => true),
						'end_date' => array('type' => 'int', 'precision' => 8, 'nullable' => true),
				),
				'pk' => array('id'),
				'fk' => array('lg_project_type' => array('project_type_id' => 'id')),
				'ix' => array(),
				'uc' => array()
		),
		'lg_activity' => array(
				'fd' => array(
						'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
						'parent_activity_id' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
						'name' => array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
						'description' => array('type' => 'text', 'nullable' => false),
						'project_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'start_date' => array('type' => 'int', 'precision' => 8, 'nullable' => true),
						'end_date' => array('type' => 'int', 'precision' => 8, 'nullable' => true),
						'responsible_user_id' => array('type' => 'int', 'precision' => 4, 'nullable'=> false),
						'create_user' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'create_date' => array('type' => 'int', 'precision' => 8, 'nullable' => false),
						'update_user' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'update_date' => array('type' => 'int', 'precision' => 8, 'nullable' => false)
				),
				'pk' => array('id'),
				'fk' => array(
						'lg_project' => array('project_id' => 'id'),
						'lg_activity' => array('parent_activity_id' => 'id')
				),
				'ix' => array('name'),
				'uc' => array()
		),
		'lg_requirement' => array(
				'fd' => array(
						'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
						'activity_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'start_date' => array('type' => 'int', 'precision' => 8, 'nullable' => false),
						'end_date' => array('type' => 'int', 'precision' => 8, 'nullable' => false),
						'no_of_elements' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'location_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'create_user' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'create_date' => array('type' => 'int', 'precision' => 8, 'nullable' => false),
				),
				'pk' => array('id'),
				'fk' => array(
						'lg_activity' => array('activity_id' => 'id'),
						'phpgw_locations' => array('location_id' => 'location_id')
				),
				'ix' => array(),
				'uc' => array()
		),
		'lg_calendar' => array(
				'fd' => array(
						'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
						'location_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'item_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'item_inventory_id' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
						'item_inventory_amount' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
						'create_user' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'create_date' => array('type' => 'int', 'precision' => 8, 'nullable' => false),
						'start_date' => array('type' => 'int', 'precision' => 8, 'nullable' => false),
						'end_date' => array('type' => 'int', 'precision' => 8, 'nullable' => false),
				),
				'pk' => array('id'),
				'fk' => array(
						'phpgw_locations' => array('location_id' => 'location_id')
				),
				'ix' => array(),
				'uc' => array()
		),
		'lg_requirement_resource_allocation' => array(
				'fd' => array(
						'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
						'requirement_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'resource_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'location_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'calendar_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'create_user' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'create_date' => array('type' => 'int', 'precision' => 8, 'nullable' => false),
						'ticket_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						
				),
				'pk' => array('id'),
				'fk' => array(
						'lg_requirement' => array('requirement_id' => 'id'),
						'phpgw_locations' => array('location_id' => 'location_id'),
						'lg_calendar' => array('calendar_id' => 'id')
				),
				'ix' => array(),
				'uc' => array()
		),
		'lg_resource_type_requirement' => array(
				'fd' => array(
						'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
						'location_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'project_type_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'cust_attribute_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'create_user' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'create_date' => array('type' => 'int', 'precision' => 8, 'nullable' => false),
				),
				'pk' => array('id'),
				'fk' => array(
						'lg_project_type' => array('project_type_id' => 'id'),
						'phpgw_cust_attribute' => array('location_id' => 'location_id','cust_attribute_id' => 'id')
				),
				'ix' => array(),
				'uc' => array()
		),
		'lg_requirement_value' => array(
				'fd' => array(
						'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
						'requirement_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'operator' => array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
						'value' => array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
						'create_user' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'create_date' => array('type' => 'int', 'precision' => 8, 'nullable' => false),
						'cust_attribute_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				),
				'pk' => array('id'),
				'fk' => array(
						'lg_requirement' => array('requirement_id' => 'id')
				),
				'ix' => array(),
				'uc' => array()
		),

);
