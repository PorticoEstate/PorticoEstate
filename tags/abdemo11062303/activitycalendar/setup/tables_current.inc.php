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
				'time' => array('type' => 'varchar','precision' => '255'),
				'create_date' => array('type' => 'int', 'precision' => 4, 'precision' => '8', 'nullable' => false),
				'last_change_date' => array('type' => 'int', 'precision' => 4, 'precision' => '8', 'nullable' => true),
				'contact_person_1' => array('type' => 'varchar','precision' => '255'),
				'contact_person_2' => array('type' => 'varchar','precision' => '255'),
				'secret' => array('type' => 'text','nullable' => False),
				'special_adaptation' => array('type' => 'bool','nullable' => true,'default' => 'false')
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
		)
	);
