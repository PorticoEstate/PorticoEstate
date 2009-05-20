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
		
		'bk_rental_composite' => array(
			'fd' => array(
				'rental_composite_id' =>	array('type' => 'auto', 'nullable' => false),
				'name' =>					array('type' => 'varchar','precision' => '45','nullable' => false),
				'description' => 			array('type' => 'text','precision' => '1000','nullable' => false, 'default'=>''),
				
				'is_active' =>	 			array('type' => 'varchar','precision' => '1000','nullable' => false, 'default'=>'')
				//'area' => 					array('type' => 'int','precision' => '1000','nullable' => false, 'default'=>0),
				//'account' => 				array('type' => '','precision' => '1000','nullable' => false, 'default'=>'')
			),
			'pk' => array('rental_composite_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		
		'bk_rental_units' => array(
			'fd' => array(
				'rental_composite_id' => 	array('type' => 'int', 'nullable' => false),
				'property_id' => 			array('type' => 'varchar', 'precision' => '20', 'nullable' => false)
			),
			'pk' => array('rental_composite_id'),
			'fk' => array(
				'bk_rental_composite' => array( 'rental_composite_id' => 'rental_compisite_id')
			),
			'ix' => array(),
			'uc' => array()
		),
		
		'bk_rental_contracts' => array(
			'fd' => array(
				'rental_contract_id' =>		array()
			),
			'pk' => array('rental_contract_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);
?>
