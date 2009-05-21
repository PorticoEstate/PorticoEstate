<?php

	/**
	 * Types:
 	 * 'varchar','int','auto','blob','char','date','decimal','float','longtext','text','timestamp','bool'
 	 * 
 	 * Abbreviations:
 	 * 	fd = fields
 	 * 	pk = primary key
 	 * 	fk = foreign key
 	 * 	ix = index
 	 * 	uc = unique constraint
 	 * 
 	 */

	$phpgw_baseline = array(
		'rental_composite' => array(
				'fd' => array(
					'composite_id' => 		array('type' => 'auto', 'nullable' => false),
					'name' => 				array('type' => 'varchar','precision' => '45','nullable' => false),
					'description' => 		array('type' => 'text'),
					'is_active' => 			array('type' => 'bool','nullable' => false,'default' => 'true'),
					'address_1' =>			array('type' => 'varchar','precision' => '255'),
					'address_2' =>			array('type' => 'varchar','precision' => '255'),
					'house_number' =>		array('type' => 'varchar','precision' => '255'),
					'postcode' =>			array('type' => 'varchar','precision' => '255'),
					'place' =>				array('type' => 'varchar','precision' => '255'),
					'has_custom_address' =>	array('type' => 'bool','nullable' => false,'default' => 'false'),
				),
				'pk' => array('composite_id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
		),
		'rental_unit' => array(
				'fd' => array(
					'composite_id' => 		array('type' => 'int', 'precision' => 4, 'nullable' => false),
					'location_id' => 		array('type' => 'int', 'precision' => 4, 'nullable' => false)
				),
				'pk' => array('composite_id','location_id'),
				'fk' => array(
					'rental_composite' => array( 'composite_id' => 'composite_id'),
					'fm_locations' => array( 'location_id' => 'id')
				),
				'ix' => array(),
				'uc' => array()
		)
	);
