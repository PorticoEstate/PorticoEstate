<?php
	$setup_info['rental']['name'] = 'rental';
	$setup_info['rental']['version'] = '0.0.11';
	$setup_info['rental']['app_order'] = 51;
	$setup_info['rental']['tables'] = array(
		'rental_party',						// All contract participants, tenants etc.
		'rental_contract',					// Contracts, has parties and a rental object
		'rental_contract_composite',		// Connection between contracts and composites
		'rental_contract_party',			// Link table between tenants and contracts
		'rental_composite',					// Rental object, an aggregation of rental units or areas
		'rental_contract_price_item',		// Price items from the price book tied to a contract
	//	'rental_bill_price_item',			// Price items from the price book tied to a past bill
	//	'rental_bill',						// A sent bill tied to a contract
	//	'rental_contract_metadata_item',	// Custom field storage for contracts
		'rental_unit',						// Link table between property register and rental objects or composites
	//	'rental_comment',					// Comments to rental composites and tenants
		'rental_permission',
		'rental_permission_root',
		'rental_document_composite',
		'rental_contract_last_edited',
		'rental_notification',				// Stores user notifications
		// Admin tables
		'rental_billing_term',
		'rental_contract_type',
		'rental_price_item',
	//	'rental_custom_field_type',
	//	'rental_rental_object_type'
	);
	$setup_info['rental']['enable'] = 1;
	$setup_info['rental']['app_group']	= 'office';
	$setup_info['rental']['description'] = 'Bergen kommune rental';
	$setup_info['rental']['author'][] = array
	(
		'name'	=> 'Bouvet ASA',
		'email'	=> 'info@bouvet.no'
	);

	/* Dependencies for this app to work */
	$setup_info['rental']['depends'][] = array
	(
		'appname' => 'phpgwapi',
		'versions' => array('0.9.17', '0.9.18')
	);

	$setup_info['rental']['depends'][] = array
	(
		'appname' => 'property',
		'versions' => array('0.9.17')
	);

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['rental']['hooks'] = array
	(
		'menu'	=> 'rental.menu.get_menu'
	);
?>
