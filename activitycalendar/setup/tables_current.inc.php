<?php
	$phpgw_baseline = array(
		'activity_activity' => array(
			'fd' => array(
				'id' => array('type' => 'auto','precision' => 4,'nullable' => False),
				'title' => array('type' => 'varchar','precision' => '255', 'nullable' => False),
				'organization_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'group_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'district' => array('type' => 'varchar','precision' => '255'),
				'category' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'target' => array('type' => 'varchar', 'precision' => '255'),
				'office' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'state' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'description' => array('type' => 'varchar','precision' => '255'),
				'arena' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'internal_arena' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'time' => array('type' => 'varchar','precision' => '255'),
				'create_date' => array('type' => 'int', 'precision' => 4, 'precision' => '8', 'nullable' => false),
				'last_change_date' => array('type' => 'int', 'precision' => 4, 'precision' => '8', 'nullable' => true),
				'contact_person_1' => array('type' => 'varchar','precision' => '255'),
				'contact_person_2' => array('type' => 'varchar','precision' => '255'),
				'secret' => array('type' => 'text','nullable' => False),
				'special_adaptation' => array('type' => 'bool','nullable' => true,'default' => 'false'),
				'contact_person_2_address' => array('type' => 'varchar','precision' => '255'),
				'contact_person_2_zip' => array('type' => 'varchar','precision' => '255'),
				'frontend' => array('type' => 'bool','nullable' => true,'default' => 'false'),
				'new_org' => array('type' => 'bool','nullable' => true,'default' => 'false')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'activity_arena' => array(
			'fd' => array(
				'id' => array('type' => 'auto','precision' => 4,'nullable' => False),
				'internal_arena_id' => array('type' => 'int', 'precision' => 4, 'nullable' => True),
				'arena_name' => array('type' => 'varchar','precision' => '255','nullable' => false),
				'address' => array('type' => 'varchar','precision' => '255','nullable' => True),
				'active' => array('type' => 'bool','nullable' => true,'default' => 'true')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'activity_organization' => array(
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
				'change_type' => array('type' => 'varchar','precision' => '255','nullable' => false),
				'transferred' => array('type' => 'bool','nullable' => true,'default' => 'false')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'activity_group' => array(
			'fd' => array(
				'id' => array('type' => 'auto','precision' => 4,'nullable' => False),
				'organization_id' => array('type' => 'varchar','precision' => '255','nullable' => false),
				'name' => array('type' => 'varchar','precision' => '255','nullable' => false),
				'description' => array('type' => 'text','nullable' => false),
				'change_type' => array('type' => 'varchar','precision' => '255','nullable' => false),
				'transferred' => array('type' => 'bool','nullable' => true,'default' => 'false')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'activity_contact_person' => array(
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
