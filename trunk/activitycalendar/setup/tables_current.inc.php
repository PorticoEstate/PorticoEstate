<?php
	$phpgw_baseline = array(
		'activity_activity' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => FALSE),
				'organization_id' => array('type' => 'int', 'nullable' => True),
				'group_id' => array('type' => 'int', 'nullable' => True),
				'district' => array('type' => 'varchar','precision' => '255'),
				'category' => array('type' => 'int', 'nullable' => True),
				'target' => array('type' => 'int', 'nullable' => True),
				'description' => array('type' => 'varchar','precision' => '255'),
				'arena' => array('type' => 'int', 'nullable' => True),
				'date_start' => array('type' => 'int', 'precision' => '8', 'nullable' => true),
				'date_end' => array('type' => 'int', 'precision' => '8', 'nullable' => true),
				'contact_person_1' => array('type' => 'varchar','precision' => '255'),
				'contact_person_2' => array('type' => 'varchar','precision' => '255')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'activity_arena' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => FALSE),
				'internal_arena_id' => array('type' => 'int', 'nullable' => True),
				'arena_name' => array('type' => 'varchar','precision' => '255','nullable' => false),
				'address' => array('type' => 'varchar','precision' => '255')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);
?>
