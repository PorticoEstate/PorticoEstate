<?php
	$setup_info['rental']['name'] = 'rental';
	$setup_info['rental']['version'] = '0.0.19';
	$setup_info['rental']['app_order'] = 51;
	$setup_info['rental']['tables'] = array(
		'rental_party',						// All contract participants, tenants etc.
		'rental_contract',					// Contracts, has parties and a rental object
		'rental_contract_composite',		// Connection between contracts and composites
		'rental_contract_party',			// Link table between tenants and contracts
		'rental_composite',					// Rental object, an aggregation of rental units or areas
		'rental_contract_price_item',		// Price items from the price book tied to a contract
		'rental_billing',					// Contains information about the job creating invoices
		'rental_invoice',					// Contratcs' invoices
		'rental_invoice_price_item',		// Price items from the contract tied to a past invoice
	//	'rental_contract_metadata_item',	// Custom field storage for contracts
		'rental_unit',						// Link table between property register and rental objects or composites
	//	'rental_comment',					// Comments to rental composites and tenants
		'rental_document_composite',
		'rental_contract_last_edited',
		'rental_contract_responsibility',
		'rental_notification',				// Stores user notifications
		'rental_notification_workbench',	
		// Admin tables
		'rental_billing_term',
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
		'versions' => array('0.9.17.566')
	);

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['rental']['hooks'] = array
	(
		'config',
		'menu'	=> 'rental.menu.get_menu',
		'settings'
	);
?>
