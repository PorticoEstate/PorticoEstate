<?php
	$setup_info['rental']['name'] = 'rental';
	$setup_info['rental']['version'] = '0.0.1';
	$setup_info['rental']['app_order'] = 51;
	$setup_info['rental']['tables'] = array(
		'phpgw_rental_tenant',									// Tenants
		'phpgw_rental_contract',								// Contracts, has tenants and a rental object
		'phpgw_rental_composite',								// Rental object, an aggregation of rental units or areas
		'phpgw_rental_contract_price_item',			// Price items from the price book tied to a contract
		'phpgw_rental_bill_price_item',					// Price items from the price book tied to a past bill
		'phpgw_rental_bill',										// A sent bill tied to a contract
		'phpgw_rental_contract_tenant',					// Link table between tenants and contracts
		'phpgw_rental_contract_metadata_item',	// Custom field storage for contracts
		'phpgw_rental_unit',										// Link table between property register and rental objects or composites
		'phpgw_rental_comment',									// Comments to rental composites and tenants
		// Admin tables
		'phpgw_rental_contract_status',
		'phpgw_rental_billing_term',
		'phpgw_rental_contract_type',
		'phpgw_rental_price_item',
		'phpgw_rental_tenant_type',
		'phpgw_rental_custom_field_type',
		'phpgw_rental_rental_object_type'
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
