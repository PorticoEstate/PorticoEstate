<?php
	$phpgw_baseline = array(
		'bb_building' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'name' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'homepage' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'phone' => array('type' => 'varchar','precision' => '50','nullable' => False, 'default'=>''),
				'email' => array('type' => 'varchar','precision' => '50','nullable' => False, 'default'=>''),
				'address' => array('type' => 'varchar','precision' => '250','nullable' => False, 'default'=>''),
				'description' => array('type' => 'varchar','precision' => '1000','nullable' => False, 'default'=>'')
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
		'bb_organization' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'name' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'homepage' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'phone' => array('type' => 'varchar','precision' => '50','nullable' => False, 'default'=>''),
				'email' => array('type' => 'varchar','precision' => '50','nullable' => False, 'default'=>''),
				'description' => array('type' => 'varchar','precision' => '1000','nullable' => False, 'default'=>''),
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'bb_resource' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'building_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'name' => array('type' => 'varchar','precision' => '50','nullable' => False),
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_building' => array('building_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
		),
		'bb_group' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'organization_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'name' => array('type' => 'varchar','precision' => '50','nullable' => False),
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_organization' => array('organization_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
		),
		'bb_season' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'building_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'name' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'status' => array('type' => 'varchar','precision' => '10','nullable' => False),
				'from_' => array('type' => 'date','nullable' => False),
				'to_' => array('type' => 'date','nullable' => False),
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_building' => array('building_id' => 'id')
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
		'bb_booking' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'name' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'group_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'from_' => array('type' => 'timestamp','nullable' => False),
				'to_' => array('type' => 'timestamp','nullable' => False),
				'season_id' => array('type' => 'int','precision' => '4','nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_group' => array('group_id' => 'id'),
				'bb_season' => array('season_id' => 'id')),
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
		'bb_equipment' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'resource_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'name' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'description' => array('type' => 'varchar','precision' => '10000','nullable' => False),
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_resource' => array('resource_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
		),
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
		'bb_allocation' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => False),
				'organization_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'from_' => array('type' => 'timestamp','nullable' => False),
				'to_' => array('type' => 'timestamp','nullable' => False),
				'season_id' => array('type' => 'int','precision' => '4','nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_organization' => array('organization_id' => 'id'),
				'bb_season' => array('season_id' => 'id')),
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
				'status' => array('type' => 'text', 'nullable'=> False),
				'created' => array('type' => 'timestamp', 'nullable'=> False, 'default' => 'now'),
				'modified' => array('type' => 'timestamp', 'nullable'=> False, 'default' => 'now'),
				'activity_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'description' => array('type' => 'text', 'nullable' => False),
				'contact_name' => array('type' => 'text', 'nullable' => False),
				'contact_email' => array('type' => 'text', 'nullable' => False),
				'contact_phone' => array('type' => 'text', 'nullable' => False)
			),
			'pk' => array('id'),
			'fk' => array(
				'bb_activity' => array('activity_id' => 'id')),
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
				'time' => array('type' => 'text', 'nullable' => False),
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
		)
	);
?>
