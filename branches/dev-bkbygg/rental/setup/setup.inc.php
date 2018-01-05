<?php
	$setup_info['rental']['name'] = 'rental';  // Module identifier
	$setup_info['rental']['version'] = '0.1.0.39'; // Current module version
	$setup_info['rental']['app_order'] = 51;  // (?)
	$setup_info['rental']['tables'] = array(
		'rental_party', // All contract participants, tenants etc.
		'rental_contract', // Contracts, has parties and rental objects
		'rental_contract_composite', // Connection between contracts and composites
		'rental_contract_party', // Connection between tenants and contracts
		'rental_composite', // Rental object, an aggregation of rental units
		'rental_location_factor', // for location based prizing
		'rental_composite_type', // classification
		'rental_composite_standard', // Optional standard classes
		'rental_contract_price_item', // Price items from the price book tied to a contract
		'rental_contract_responsibility_unit', // optional list of candidates
		'rental_billing', // Contains information about the job creating invoices
		'rental_invoice', // Contract invoices
		'rental_invoice_price_item', // Price items from the contract tied to a past invoice
		'rental_unit', // Link table between property register and rental objects or composites
		'rental_document', // Holds document meta data for both contracts and parties
		'rental_document_types', // Document types
		'rental_contract_last_edited', // 'Last edited' information for eash user
		'rental_contract_responsibility', // Responsility areas
		'rental_notification', // User notifications for contracts
		'rental_notification_workbench', // Notifications on users' workbenches
		'rental_billing_term', // The different billing terms
		'rental_price_item', // Price items in concept 'Prisbok'
		'rental_contract_types', // Contract types
		'rental_billing_info', // Term information for each billing
		'rental_adjustment',  // Price regulations
		'rental_application',
		'rental_application_comment',
		'rental_application_composite',
		'rental_moveout',
		'rental_moveout_comment',
		'rental_movein',
		'rental_movein_comment',
		'rental_email_out',
		'rental_email_out_party',
		'rental_email_template'
	);
	$setup_info['rental']['enable'] = 1;
	$setup_info['rental']['app_group'] = 'office';
	$setup_info['rental']['description'] = 'Bergen kommune rental';
	$setup_info['rental']['author'][] = array
		(
		'name' => 'Bouvet ASA',
		'email' => 'info@bouvet.no'
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
		'menu' => 'rental.menu.get_menu',
		'settings'
	);
