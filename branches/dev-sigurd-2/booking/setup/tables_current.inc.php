<?php
	$phpgw_baseline = array(
		'bb_activity' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => FALSE),
				'parent_id' => array('type' => 'int','precision' => '4','nullable' => TRUE),
				'name' => array('type' => 'varchar','precision' => '50','nullable' => FALSE),
				'description' => array('type' => 'varchar','precision' => '10000','nullable' => FALSE),
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_activity' => array('parent_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
		),
		'bb_building' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'active' => array('type' => 'int', 'nullable' => False,'precision' => '4', 'default' => 1), 
				'name' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'homepage' => array('type' => 'string', 'nullable' => False),
				'phone' => array('type' => 'varchar','precision' => '50','nullable' => False, 'default'=>''),
				'email' => array('type' => 'varchar','precision' => '50','nullable' => False, 'default'=>''),
				'street' => array('type' => 'varchar','precision' => '255', 'nullable' => False, 'default'=>''),
				'zip_code' => array('type' => 'varchar','precision' => '255', 'nullable' => False, 'default'=>''),
				'district' => array('type' => 'varchar','precision' => '255', 'nullable' => False, 'default'=>''),
				'city' => array('type' => 'varchar','precision' => '255', 'nullable' => False, 'default'=>''),
				'description' => array('type' => 'text', 'nullable' => False, 'default'=>''),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'bb_targetaudience' => array(
			'fd' => array(
					'id' => array('type' => 'auto', 'nullable' => False),
					'name' => array('type' => 'text', 'nullable' => False),
					'description' => array('type' => 'text', 'nullable' => False),
					'active' => array('type' => 'int', 'nullable' => False,'precision' => '4', 'default' => 1),
				),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'bb_contact_person' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'ssn' => array('type' => 'varchar', 'precision' => '12', 'nullable' => True,),
				'name' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'homepage' => array('type' => 'string', 'nullable' => True),
				'phone' => array('type' => 'varchar','precision' => '50','nullable' => False, 'default'=>''),
				'email' => array('type' => 'varchar','precision' => '50','nullable' => False, 'default'=>''),
				'description' => array('type' => 'varchar','precision' => '1000','nullable' => False, 'default'=>''),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'bb_organization' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'organization_number' => array('type' => 'varchar', 'precision' => '9', 'nullable' => False, 'default' => ''),
				'active' => array('type' => 'int', 'nullable' => False,'precision' => '4', 'default' => 1), 
				'name' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'homepage' => array('type' => 'string', 'nullable' => True),
				'phone' => array('type' => 'varchar','precision' => '50','nullable' => False, 'default'=>''),
				'email' => array('type' => 'varchar','precision' => '50','nullable' => False, 'default'=>''),
				'street' => array('type' => 'varchar','precision' => '255', 'nullable' => False, 'default'=>''),
				'zip_code' => array('type' => 'varchar','precision' => '255', 'nullable' => False, 'default'=>''),
				'district' => array('type' => 'varchar','precision' => '255', 'nullable' => False, 'default'=>''),
				'city' => array('type' => 'varchar','precision' => '255', 'nullable' => False, 'default'=>''),
				'description' => array('type' => 'text', 'nullable' => False, 'default'=>''),
				'activity_id' => array('type' => 'int','precision' => '4','nullable' => True),
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_activity' => array('activity_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		),
		'bb_resource' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'active' => array('type' => 'int', 'nullable' => False,'precision' => '4', 'default' => 1), 
				'building_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'name' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'type' => array('type' => 'varchar', 'precision' => '50', 'nullable' => False),
				'description' => array('type' => 'text', 'nullable' => False, 'default'=>''),
				'activity_id' => array('type' => 'int','precision' => '4','nullable' => False),
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_building' => array('building_id' => 'id'),
				'bb_activity' => array('activity_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		),
		'bb_group' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'active' => array('type' => 'int', 'nullable' => False,'precision' => '4', 'default' => 1), 
				'organization_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'description' => array('type' => 'text', 'nullable' => False, 'default'=>''),
				'name' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'activity_id' => array('type' => 'int','precision' => '4','nullable' => True),
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_organization' => array('organization_id' => 'id'),
				'bb_activity' => array('activity_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		),
		'bb_season' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'officer_id' => array('type' => 'int', 'precision'=> '4', 'nullable' => false),
				'active' => array('type' => 'int', 'nullable' => False,'precision' => '4', 'default' => 1), 
				'building_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'name' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'status' => array('type' => 'varchar','precision' => '10','nullable' => False),
				'from_' => array('type' => 'date','nullable' => False),
				'to_' => array('type' => 'date','nullable' => False),
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_building' => array('building_id' => 'id'),
				'phpgw_accounts' => array('officer_id' => 'account_id'),
			),
			'ix' => array(),
			'uc' => array()
		),
		'bb_season_boundary' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'season_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'wday' => array('type' => 'int','precision' => '4','nullable' => False),
				'from_' => array('type' => 'time','nullable' => False),
				'to_' => array('type' => 'time','nullable' => False),
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_season' => array('season_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
		),
		'bb_allocation' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'active' => array('type' => 'int', 'nullable' => False,'precision' => '4', 'default' => 1), 
				'organization_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'from_' => array('type' => 'timestamp','nullable' => False),
				'to_' => array('type' => 'timestamp','nullable' => False),
				'cost' => array('type' => 'decimal','precision' => '10', 'scale'=>'2', 'nullable' => False),
				'season_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'completed' => array('type' => 'int', 'precision' => '4', 'nullable' => False, 'default' => 0),
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_organization' => array('organization_id' => 'id'),
				'bb_season' => array('season_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		),
		'bb_booking' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'active' => array('type' => 'int', 'nullable' => False,'precision' => '4', 'default' => 1), 
				'activity_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'group_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'from_' => array('type' => 'timestamp','nullable' => False),
				'to_' => array('type' => 'timestamp','nullable' => False),
				'allocation_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'season_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'completed' => array('type' => 'int', 'precision' => '4', 'nullable' => False, 'default' => 0),
				'cost' => array('type' => 'decimal','precision' => '10', 'scale'=>'2', 'nullable' => False),
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_group' => array('group_id' => 'id'),
				'bb_season' => array('season_id' => 'id'),
				'bb_allocation' => array('allocation_id' => 'id'),
				'bb_activity' => array('activity_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		),
		'bb_booking_resource' => array(
			'fd' => array(
				'booking_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'resource_id' => array('type' => 'int','precision' => '4','nullable' => False),
			),
			'pk' => array('booking_id', 'resource_id'),
			'fk' => array(
				'bb_booking' => array('booking_id' => 'id'),
				'bb_resource' => array('resource_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
		),
		'bb_season_resource' => array(
			'fd' => array(
				'season_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'resource_id' => array('type' => 'int','precision' => '4','nullable' => False),
			),
			'pk' => array('season_id', 'resource_id'),
			'fk' => array(
				'bb_season' => array('season_id' => 'id'),
				'bb_resource' => array('resource_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
		),
		'bb_wtemplate_alloc' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'season_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'organization_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'wday' => array('type' => 'int','precision' => '4','nullable' => False),
				'cost' => array('type' => 'decimal','precision' => '10', 'scale'=>'2', 'nullable' => False),
				'from_' => array('type' => 'time','nullable' => False),
				'to_' => array('type' => 'time','nullable' => False),
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_season' => array('season_id' => 'id'),
				'bb_organization' => array('organization_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
		),
		'bb_wtemplate_alloc_resource' => array(
			'fd' => array(
				'allocation_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'resource_id' => array('type' => 'int','precision' => '4','nullable' => False),
			),
			'pk' => array('allocation_id', 'resource_id'),
			'fk' => array(
				'bb_wtemplate_alloc' => array('allocation_id' => 'id'),
				'bb_resource' => array('resource_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
		),
		'bb_allocation_resource' => array(
			'fd' => array(
				'allocation_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'resource_id' => array('type' => 'int','precision' => '4','nullable' => False),
			),
			'pk' => array('allocation_id', 'resource_id'),
			'fk' => array(
				'bb_allocation' => array('allocation_id' => 'id'),
				'bb_resource' => array('resource_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
		),
		'bb_application' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'active' => array('type' => 'int', 'nullable' => False,'precision' => '4', 'default' => 1), 
				'status' => array('type' => 'text', 'nullable'=> False),
				'created' => array('type' => 'timestamp', 'nullable'=> False, 'default' => 'now'),
				'modified' => array('type' => 'timestamp', 'nullable'=> False, 'default' => 'now'),
				'activity_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'description' => array('type' => 'text', 'nullable' => False),
				'contact_name' => array('type' => 'text', 'nullable' => False),
				'contact_email' => array('type' => 'text', 'nullable' => False),
				'contact_phone' => array('type' => 'text', 'nullable' => False),
				'secret' => array('type' => 'text', 'nullable' => False),
				'owner_id' => array('type' => 'int', 'precision' => '4', 'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_activity' => array('activity_id' => 'id'),
				'phpgw_accounts' => array('owner_id' => 'account_id'),
			),
			'ix' => array(),
			'uc' => array()
		),
		'bb_application_resource' => array(
			'fd' => array(
				'application_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'resource_id' => array('type' => 'int','precision' => '4','nullable' => False),
			),
			'pk' => array('application_id', 'resource_id'),
			'fk' => array(
				'bb_application' => array('application_id' => 'id'),
				'bb_resource' => array('resource_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
		),
		'bb_application_comment' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'application_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'time' => array('type' => 'timestamp', 'nullable' => False),
				'author' => array('type' => 'text', 'nullable' => False),
				'comment' => array('type' => 'text', 'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_application' => array('application_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		),
		'bb_application_date' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'application_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'from_' => array('type' => 'timestamp', 'nullable' => False),
				'to_' => array('type' => 'timestamp', 'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_application' => array('application_id' => 'id')),
			'ix' => array(),
			'uc' => array('application_id', 'from_', 'to_')
		),
		'bb_application_targetaudience' => array(
			'fd' => array(
				'application_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'targetaudience_id' => array('type' => 'int','precision' => '4','nullable' => False)
			),
			'pk' => array('application_id', 'targetaudience_id'),
			'fk' => array(
				'bb_application' => array('application_id' => 'id'),
				'bb_targetaudience' => array('targetaudience_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		),
		'bb_agegroup' => array( 
			'fd' => array( 
				'id' => array('type' => 'auto', 'nullable' => False), 
				'name' => array('type' => 'text', 'nullable' => False), 
				'description' => array('type' => 'text', 'nullable' => False), 
				'active' => array('type' => 'int', 'nullable' => False,'precision' => '4', 'default' => 1), 
			), 
			'pk' => array('id'), 
			'fk' => array(), 
			'ix' => array(), 
			'uc' => array() 
		), 
		'bb_application_agegroup' => array(
			'fd' => array(
				'application_id' => array('type' => 'int','precision' => '4', 'nullable' => False),
				'agegroup_id' => array('type' => 'int','precision' => '4', 'nullable' => False),
				'male' => array('type' => 'int','precision' => '4', 'nullable' => False),
				'female' => array('type' => 'int','precision' => '4', 'nullable' => False),
			),
			'pk' => array('application_id', 'agegroup_id'),
			'fk' => array(
				'bb_application' => array('application_id' => 'id'),
				'bb_agegroup' => array('agegroup_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		),
		'bb_booking_targetaudience' => array(
			'fd' => array(
				'booking_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'targetaudience_id' => array('type' => 'int','precision' => '4','nullable' => False)
			),
			'pk' => array('booking_id', 'targetaudience_id'),
			'fk' => array(
				'bb_booking' => array('booking_id' => 'id'),
				'bb_targetaudience' => array('targetaudience_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		),
		'bb_booking_agegroup' => array(
			'fd' => array(
				'booking_id' => array('type' => 'int','precision' => '4', 'nullable' => False),
				'agegroup_id' => array('type' => 'int','precision' => '4', 'nullable' => False),
				'male' => array('type' => 'int','precision' => '4', 'nullable' => False),
				'female' => array('type' => 'int','precision' => '4', 'nullable' => False),
			),
			'pk' => array('booking_id', 'agegroup_id'),
			'fk' => array(
				'bb_booking' => array('booking_id' => 'id'),
				'bb_agegroup' => array('agegroup_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		),
		'bb_document_building' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'name' => array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
				'owner_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'category' => array('type' => 'varchar', 'precision' => '150', 'nullable' => false),
				'description' => array('type' => 'text', 'nullable' => true),
			),
			'pk' => array('id'),
			'fk' => array(
				"bb_building" => array('owner_id' => 'id'),
			),
			'ix' => array(),
			'uc' => array()
		),
		'bb_document_resource' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'name' => array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
				'owner_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'category' => array('type' => 'varchar', 'precision' => '150', 'nullable' => false),
				'description' => array('type' => 'text', 'nullable' => true),
			),
			'pk' => array('id'),
			'fk' => array(
				"bb_resource" => array('owner_id' => 'id'),
			),
			'ix' => array(),
			'uc' => array()
		),
		'bb_permission' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'subject_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'object_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'object_type' => array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
				'role' => array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
			),
			'pk' => array('id'),
			'fk' => array(
				'phpgw_accounts' => array('subject_id' => 'account_id'),
			),
			'ix' => array(array('object_id', 'object_type'), array('object_type')),
			'uc' => array('subject_id', 'role', 'object_type', 'object_id'),
		),
		'bb_permission_root' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'subject_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'role' => array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
			),
			'pk' => array('id'),
			'fk' => array(
				'phpgw_accounts' => array('subject_id' => 'account_id'),
			),
			'ix' => array(),
			'uc' => array('subject_id', 'role'),
		),
		'bb_organization_contact' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'name' => array('type' => 'varchar', 'precision' => '50', 'nullable' => false, 'default'=>''),
				'ssn' => array('type' => 'varchar',  'precision' => '12', 'nullable' => false, 'default'=>''),
				'phone' => array('type' => 'varchar', 'precision' => '50', 'nullable' => false, 'default'=>''),
				'email' => array('type' => 'varchar', 'precision' => '50', 'nullable' => false, 'default'=>''),
				'organization_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_organization' => array('organization_id' => 'id'),
			),
			'ix' => array('ssn'),
			'uc' => array(),
		),
		'bb_group_contact' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'name' => array('type' => 'varchar', 'precision' => '50', 'nullable' => false, 'default'=>''),
				'phone' => array('type' => 'varchar', 'precision' => '50', 'nullable' => false, 'default'=>''),
				'email' => array('type' => 'varchar', 'precision' => '50', 'nullable' => false, 'default'=>''),
				'group_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_group' => array('group_id' => 'id'),
			),
			'ix' => array(),
			'uc' => array(),
		),
		'bb_event' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'active' => array('type' => 'int','precision' => '4','nullable' => False, 'default'=>'1'),
				'activity_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'description' => array('type' => 'text', 'nullable' => false, 'default'=>''),
				'from_' => array('type' => 'timestamp', 'nullable' => false),
				'to_' => array('type' => 'timestamp', 'nullable' => false),
				'cost' => array('type' => 'decimal','precision' => '10', 'scale'=>'2', 'nullable' => False),
				'contact_name' => array('type' => 'varchar', 'precision' => '50', 'nullable' => false, 'default'=>''),
				'contact_email' => array('type' => 'varchar', 'precision' => '50', 'nullable' => false, 'default'=>''),
				'contact_phone' => array('type' => 'varchar', 'precision' => '50', 'nullable' => false, 'default'=>''),
				'completed' => array('type' => 'int', 'precision' => '4', 'nullable' => False, 'default' => 0),
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_activity' => array('activity_id' => 'id'),
			),
			'ix' => array(),
			'uc' => array(),
		),
		'bb_event_resource' => array(
			'fd' => array(
				'event_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'resource_id' => array('type' => 'int','precision' => '4','nullable' => False),
			),
			'pk' => array('event_id', 'resource_id'),
			'fk' => array(
				'bb_event' => array('event_id' => 'id'),
				'bb_resource' => array('resource_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
		),
		'bb_event_targetaudience' => array(
			'fd' => array(
				'event_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'targetaudience_id' => array('type' => 'int','precision' => '4','nullable' => False)
			),
			'pk' => array('event_id', 'targetaudience_id'),
			'fk' => array(
				'bb_event' => array('event_id' => 'id'),
				'bb_targetaudience' => array('targetaudience_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		),
		'bb_event_agegroup' => array(
			'fd' => array(
				'event_id' => array('type' => 'int','precision' => '4', 'nullable' => False),
				'agegroup_id' => array('type' => 'int','precision' => '4', 'nullable' => False),
				'male' => array('type' => 'int','precision' => '4', 'nullable' => False),
				'female' => array('type' => 'int','precision' => '4', 'nullable' => False),
			),
			'pk' => array('event_id', 'agegroup_id'),
			'fk' => array(
				'bb_event' => array('event_id' => 'id'),
				'bb_agegroup' => array('agegroup_id' => 'id')),
			'ix' => array(),
			'uc' => array()
		),
		
		'bb_completed_reservation' => array(
			'fd' => array(
				'id' 						=> array('type' => 'auto', 'nullable' => False),
				'reservation_type' 	=> array('type' => 'varchar', 'precision' => '70', 'nullable' => False),
				'reservation_id' 		=> array('type' => 'int', 'precision' => '4', 'nullable' => False),
				'season_id' 			=> array('type' => 'int', 'precision' => '4'),
				'cost' => array('type' => 'decimal','precision' => '10', 'scale'=>'2', 'nullable' => False),
				'from_' => array('type' => 'timestamp', 'nullable' => false),
				'to_' => array('type' => 'timestamp', 'nullable' => false),
				'organization_id' 		=> array('type' => 'int', 'precision' => '4'),
				'payee_type' 		=> array('type' => 'varchar', 'precision' => '70', 'nullable' => False),
				'payee_organization_number' => array('type' => 'varchar', 'precision' => '9'),
				'payee_ssn' 		=> array('type' => 'varchar', 'precision' => '12'),
				'exported' 				=> array('type' => 'int', 'precision' => '4', 'nullable' => False, 'default' => 0),
				'description' => array('type' => 'text', 'nullable' => false),
				'article_description' => array('type' => 'varchar', 'precision' => '35', 'nullable' => False),
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_organization' => array('organization_id' => 'id'),
				'bb_season' => array('season_id' => 'id'),
			),
			'ix' => array(),
			'uc' => array()
		),
		'bb_completed_reservation_resource' => array(
			'fd' => array(
				'completed_reservation_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'resource_id' => array('type' => 'int','precision' => '4','nullable' => False),
			),
			'pk' => array('completed_reservation_id', 'resource_id'),
			'fk' => array(
				'bb_completed_reservation' => array('completed_reservation_id' => 'id'),
				'bb_resource' => array('resource_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
		),
	);
?>
