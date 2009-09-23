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
					'id' => 				array('type' => 'auto', 'nullable' => false),
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
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
		),
		'rental_unit' => array(
				'fd' => array(
					'composite_id' => 		array('type' => 'int', 'precision' => 4, 'nullable' => false),
					'location_code' => 		array('type' => 'varchar', 'precision' => 50, 'nullable' => false)
				),
				'pk' => array('composite_id','location_code'),
				'fk' => array(
					'rental_composite' => array( 'composite_id' => 'id'),
				),
				'ix' => array(),
				'uc' => array()
		),
		'rental_document_composite' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'name' => array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
				'owner_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'category' => array('type' => 'varchar', 'precision' => '150', 'nullable' => false),
				'description' => array('type' => 'text', 'nullable' => true),
			),
			'pk' => array('id'),
			'fk' => array(
				"rental_composite" => array('owner_id' => 'id'),
			),
			'ix' => array(),
			'uc' => array()
		),
		'rental_contract_responsibility' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'location_id'	=>	array('type' => 'int','precision' => '4', 'nullable' => false),
				'title'			=>	array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
				'notify_before' =>	array('type' => 'int','precision' => '4','nullable' => false)
			),
			'pk' => array('id'),
			'fk' => array(
				'phpgw_locations' => array( 'location_id' => 'location_id')
			),
			'ix' => array(),
			'uc' => array()
		),
		// Describes different billing terms like 'Monthly', 'Yearly', etc.
		'rental_billing_term' => array(
			'fd' => array(
				'id' => 			array('type' => 'auto', 'nullable' => false),
				'title' => 			array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
				'months' => 		array('type' => 'int', 'precision' => '4', 'nullable' => false)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'rental_contract' => array(
			'fd' => array(
				'id' => 				array('type' => 'auto', 'nullable' => false),
				'date_start' => 		array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'date_end' => 			array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'billing_start' => 		array('type' => 'int', 'precision' => '4', 'nullable' => true),						
				'location_id' =>	 	array('type' => 'int', 'precision' => '4', 'nullable' => false), // Contract type
				'term_id' =>			array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'security_type' =>		array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'security_amount' =>	array('type' => 'varchar', 'precision' => '255', 'nullable' => true),
				'old_contract_id' => 	array('type' => 'varchar', 'precision' => '255', 'nullable' => true),
				'executive_officer' => 	array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'created' =>			array('type' => 'int', 'precision' => '4', 'nullable' => true), // timestamp
				'created_by' =>			array('type' => 'int', 'precision' => '4', 'nullable' => true),
                'comment' =>            array('type' => 'text'),
				'last_updated' =>		array('type' => 'int', 'precision' => '4', 'nullable' => true) // timestamp
			),
			'pk' => array('id'),
			'fk' => array(
					'phpgw_locations' => array('location_id' => 'location_id'),
					'rental_billing_term' => array('term_id' => 'id'),
					'phpgw_accounts' => array('executive_officer' => 'account_id'),
					'phpgw_accounts' => array('created_by' => 'account_id'),
			),
			'ix' => array(),
			'uc' => array()
		),
		'rental_contract_last_edited' => array(
			'fd' => array(
				'contract_id' =>	array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'account_id' =>		array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'edited_on' =>		array('type' => 'int', 'precision' => '4', 'nullable' => false)	//timestamp
			),
			'pk' => array('contract_id','account_id'),
			'fk' => array(
				'rental_contract' => array('contract_id' => 'id'),
				'phpgw_accounts' => array('account_id' => 'account_id')
			),
			'ix' => array(
			),
			'uc' => array(
			)
		),
		// The connection between a contract and a composite. A composite can belong to several contracts (if they aren't active at the same time) and a contract can contain several composites.
		'rental_contract_composite' => array(
			'fd' => array(
				'id' => 			array('type' => 'auto', 'nullable' => false),
				'contract_id' =>	array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'composite_id' =>	array('type' => 'int', 'precision' => '4', 'nullable' => false)
			),
			'pk' => array('id'),
			'fk' => array(
					'rental_contract' => array('contract_id' => 'id'),
					'rental_composite' => array('composite_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
		),
		// A tenant
		'rental_party' => array(
				'fd' => array(
					'id' =>	array('type' => 'auto', 'nullable' => false),
					'agresso_id' =>	array('type' => 'varchar','precision' => '45','nullable' => true),
					'personal_identification_number' =>	array('type' => 'varchar','precision' => '45','nullable' => true),
					'first_name' =>	array('type' => 'varchar','precision' => '45','nullable' => true),
					'last_name' =>	array('type' => 'varchar','precision' => '45','nullable' => true),
					'comment' =>	array('type' => 'text'),
					'is_active' =>	array('type' => 'bool', 'nullable' => false, 'default' => 'true'),
					'title' =>	array('type' => 'varchar','precision' => '45','nullable' => true),
					'company_name' =>	array('type' => 'varchar','precision' => '45','nullable' => true),
					'department' =>	array('type' => 'varchar','precision' => '45','nullable' => true),
					'organisation_number' =>	array('type' => 'varchar','precision' => '45','nullable' => true),
					'address_1' =>	array('type' => 'varchar','precision' => '45','nullable' => true),
					'address_2' =>	array('type' => 'varchar','precision' => '45','nullable' => true),
					'postal_code' =>	array('type' => 'varchar','precision' => '45','nullable' => true),
					'place' =>	array('type' => 'varchar','precision' => '45','nullable' => true),
					'phone' =>	array('type' => 'varchar','precision' => '45','nullable' => true),
					'fax' =>	array('type' => 'varchar','precision' => '45','nullable' => true),
					'email' =>	array('type' => 'varchar','precision' => '45','nullable' => true),
					'url' =>	array('type' => 'varchar','precision' => '45','nullable' => true),
					'account_number' =>	array('type' => 'varchar','precision' => '45','nullable' => true),
					'reskontro' =>	array('type' => 'varchar','precision' => '45','nullable' => true),
					'location_id' => array('type' => 'int', 'precision' => '4', 'nullable' => true)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
		),
		// Tenant to contract relationship
		'rental_contract_party' => array(
			'fd' => array(
				'contract_id' =>	array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'party_id' =>	array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'is_payer'	=>	array('type' => 'bool','nullable' => false, 'default' => 'false')
			),
			'pk' => array('contract_id','party_id'),
			'fk' => array(
					'rental_contract' => array('contract_id' => 'id'),
					'rental_party' => array('party_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
		),
		// Price list
		'rental_price_item' => array(
				'fd' => array(
					'id' =>	array('type' => 'auto', 'nullable' => false),
					'title' =>	array('type' => 'varchar','precision' => '45','nullable' => false),
					'agresso_id' =>	array('type' => 'varchar','precision' => '45','nullable' => true),
					'is_area' =>	array('type' => 'bool','nullable' => false,'default' => 'true'),
					'price' =>	array('type' => 'float', 'precision' => 4,'nullable' => true)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
		),
		// Price item related to contract
		'rental_contract_price_item' => array(
			'fd' => array(
				'id' =>	array('type' => 'auto', 'nullable' => false),
				'price_item_id' =>	array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'contract_id' =>	array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'title' =>	array('type' => 'varchar','precision' => '45','nullable' => false),
				'area' =>	array('type' => 'float', 'precision' => 4,'nullable' => true),
				'count' =>	array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'agresso_id' =>	array('type' => 'varchar','precision' => '45','nullable' => true),
				'is_area' =>	array('type' => 'bool','nullable' => false,'default' => 'true'),
				'price' =>	array('type' => 'float', 'precision' => 4,'nullable' => true),
				'total_price' =>	array('type' => 'float', 'precision' => 4,'nullable' => true),
				'date_start' => 	array('type' => 'date'),
				'date_end' => 		array('type' => 'date')
			),
			'pk' => array('id'),
			'fk' => array(
				'rental_price_item' => array('price_item_id' => 'id'),
				'rental_contract' => array('contract_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
		),
		'rental_notification' => array(
			'fd' => array(
				'id'			=>	array('type' => 'auto', 'nullable' => false),
				'location_id' =>	 	array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'account_id'	=>	array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'contract_id'	=>	array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'message'		=> 	array('type' => 'text'),
				'date'			=>	array('type' => 'int', 'precision' => '4', 'nullable' => false),	// timestamp, from
				'last_notified' =>	array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'recurrence'	=>	array('type' => 'int', 'precision' => '4', 'nullable' => false, 'default' => 0),
				'deleted'		=>	array('type' => 'bool', 'default' => 'false')
			),
			'pk' => array('id'),
			'fk' => array(
				'rental_contract' => array('contract_id' => 'id'),
				'phpgw_accounts' => array('account_id' => 'account_id'),
				'phpgw_locations' => array('location_id' => 'location_id')
			),
			'ix' => array(),
			'uc' => array()
		),
		'rental_notification_workbench' => array(
			'fd' => array(
				'id'		=> array('type' => 'auto', 'nullable' => false),
				'account_id'	=> array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'date'			=>	array('type' => 'int', 'precision' => '4', 'nullable' => false), 	// timestamp, deadline  
				'notification_id' => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'dismissed' => array('type' => 'bool', 'default' => false)
			),
			'pk' => array('id'),
			'fk' => array(
				'phpgw_accounts' => array('account_id' => 'account_id'),
				'rental_notification' => array('notification_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
		),
		'rental_billing' => array(
			'fd' => array(
				'id'				=>	array('type' => 'auto', 'nullable' => false),
				'total_sum'			=>	array('type' => 'float', 'precision' => '8'),
				'success'			=>	array('type' => 'bool','nullable' => false,'default' => 'false'),
				'timestamp_start'	=>	array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'timestamp_stop'	=>	array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'location_id'		=>	array('type' => 'int', 'precision' => '4', 'nullable' => false), // Contract type
				'term_id'			=>	array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'year'				=>	array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'month'				=>	array('type' => 'int', 'precision' => '4', 'nullable' => true),
			),
			'pk' => array('id'),
			'fk' => array(
				'phpgw_locations'		=> array('location_id' => 'location_id'),
				'rental_billing_term'	=> array('term_id' => 'id'),
			),
			'ix' => array(),
			'uc' => array()
		),
		'rental_invoice' => array(
			'fd' => array(
				'id'				=>	array('type' => 'auto', 'nullable' => false),
				'contract_id'		=>	array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'billing_id'		=>	array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'party_id' =>	array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'timestamp_created'	=>	array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'timestamp_start'	=>	array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'timestamp_end'		=>	array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'total_sum'			=>	array('type' => 'float', 'precision' => '8')
			),
			'pk' => array('id'),
			'fk' => array(
				'rental_contract'	=> array('contract_id' => 'id'),
				'rental_billing'	=> array('billing_id' => 'id'),
				'rental_party'		=> array('party_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
		),
		// Price item related to invoice
		'rental_invoice_price_item' => array(
			'fd' => array(
				'id'			=> array('type' => 'auto', 'nullable' => false),
				'invoice_id'	=> array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'title'			=> array('type' => 'varchar','precision' => '45','nullable' => false),
				'area'			=> array('type' => 'float', 'precision' => 4,'nullable' => true),
				'count'			=> array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'agresso_id'	=> array('type' => 'varchar','precision' => '45','nullable' => true),
				'is_area'		=> array('type' => 'bool','nullable' => false,'default' => 'true'),
				'price'			=> array('type' => 'float', 'precision' => 4,'nullable' => true),
				'total_price'	=> array('type' => 'float', 'precision' => 4,'nullable' => true),
				'date_start'	=> array('type' => 'date'),
				'date_end'		=> array('type' => 'date')
			),
			'pk' => array('id'),
			'fk' => array(
				'rental_invoice' => array('invoice_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
		),
	);