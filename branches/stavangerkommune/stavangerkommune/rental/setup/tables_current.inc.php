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
					'name' => 				array('type' => 'varchar','precision' => '255','nullable' => false),
					'description' => 		array('type' => 'text'),
					'is_active' => 			array('type' => 'bool','nullable' => false,'default' => 'true'),
					'address_1' =>			array('type' => 'varchar','precision' => '255'),
					'address_2' =>			array('type' => 'varchar','precision' => '255'),
					'house_number' =>		array('type' => 'varchar','precision' => '255'),
					'postcode' =>			array('type' => 'varchar','precision' => '255'),
					'place' =>				array('type' => 'varchar','precision' => '255'),
					'has_custom_address' =>	array('type' => 'bool','nullable' => false,'default' => 'false'),
                    'object_type_id' =>     array('type' => 'int', 'precision' => 2, 'nullable' => true, 'default' => null),
                    'area' =>               array('type' => 'decimal', 'precision' => '20','scale' => '2', 'nullable' => true),
					'furnish_type_id' => 	array('type' => 'int', 'precision' => 4, 'nullable' => true)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
		),
		'rental_unit' => array(
				'fd' => array(
					'id'			=> array('type' => 'auto', 'nullable' => false),
					'composite_id'	=> array('type' => 'int', 'precision' => 4, 'nullable' => false),
					'location_code' => array('type' => 'varchar', 'precision' => 50, 'nullable' => false)
				),
				'pk' => array('id'),
				'fk' => array(
					'rental_composite' => array( 'composite_id' => 'id'),
				),
				'ix' => array(),
				'uc' => array(
					array('composite_id','location_code')
				)
		),
		'rental_contract_responsibility' => array(
			'fd' => array(
				'id'            => array('type' => 'auto', 'nullable' => false),
				'location_id'	=> array('type' => 'int','precision' => '4', 'nullable' => false),
				'title'			=> array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
				'notify_before' => array('type' => 'int','precision' => '4','nullable' => false),
				'notify_before_due_date' => array('type' => 'int','precision' => '4','nullable' => false),
				'notify_after_termination_date' => array('type' => 'int','precision' => '4','nullable' => false),
				'account_in'	=> array('type' => 'varchar', 'precision' => '255', 'nullable' => true),
				'account_out'	=> array('type' => 'varchar', 'precision' => '255', 'nullable' => true),
				'project_number' => array('type' => 'varchar', 'precision' => '255', 'nullable' => true),
				'agresso_export_format' => array('type' => 'varchar', 'precision' => '255', 'nullable' => true)
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
				'id'        => array('type' => 'auto', 'nullable' => false),
				'title'     => array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
				'months'    => array('type' => 'int', 'precision' => '4', 'nullable' => false)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'rental_contract' => array(
			'fd' => array(
				'id' => 				array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'date_start' => 		array('type' => 'int', 'precision' => '8', 'nullable' => true),
				'date_end' => 			array('type' => 'int', 'precision' => '8', 'nullable' => true),
				'billing_start' => 		array('type' => 'int', 'precision' => '8', 'nullable' => true),						
				'location_id' =>	 	array('type' => 'int', 'precision' => '4', 'nullable' => false), // Contract type
				'term_id' =>			array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'security_type' =>		array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'security_amount' =>	array('type' => 'varchar', 'precision' => '255', 'nullable' => true),
				'old_contract_id' => 	array('type' => 'varchar', 'precision' => '255', 'nullable' => true),
				'executive_officer' => 	array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'created' =>			array('type' => 'int', 'precision' => '8', 'nullable' => true), // timestamp
				'created_by' =>			array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'comment' =>            array('type' => 'text'),
				'last_updated' =>		array('type' => 'int', 'precision' => '8', 'nullable' => true), // timestamp
				'service_id'	=>		array('type' => 'varchar', 'precision' => '255', 'nullable' => true), //Tjeneste
				'responsibility_id' =>	array('type' => 'varchar', 'precision' => '255', 'nullable' => true),	//Ansvar
				'reference' =>			array('type' => 'varchar', 'precision' => '255', 'nullable' => true),
				'invoice_header' =>		array('type' => 'varchar', 'precision' => '255', 'nullable' => true),
				'account_in' =>			array('type' => 'varchar', 'precision' => '255', 'nullable' => true),
				'account_out' =>		array('type' => 'varchar', 'precision' => '255', 'nullable' => true),
				'project_id' =>			array('type' => 'varchar', 'precision' => '255', 'nullable' => true),
				'due_date' =>			array('type' => 'int', 'precision' => '8', 'nullable' => true), // opsjonsfrist
				'contract_type_id' =>	array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'rented_area' =>		array('type' => 'decimal', 'precision' => '20','scale' => '2', 'nullable' => true), //Utleid areal
				'adjustment_interval' =>	array('type' => 'int', 'precision' => '4', 'nullable' => true),	//Reguleringsintervall
				'adjustment_share' =>	array('type' => 'int', 'precision' => '4', 'nullable' => true, 'default' => 100),	//Reguleringsandel
				'adjustment_year' =>	array('type' => 'int', 'precision' => '4', 'nullable' => true),	//Sist regulert
				'adjustable' =>			array('type' => 'bool','nullable' => true, 'default' => 'false'), //Regulerbar
				'publish_comment' =>	array('type' => 'bool','nullable' => true,'default' => 'false')	//skal kommentar vises i frontend
			),
			'pk' => array('id'),
			'fk' => array(
					'phpgw_locations' =>    array('location_id' => 'location_id'),
					'rental_billing_term' =>array('term_id' => 'id'),
					'phpgw_accounts' =>     array('executive_officer' => 'account_id'),
					'phpgw_accounts' =>     array('created_by' => 'account_id')
			),
			'ix' => array(),
			'uc' => array()
		),
		'rental_contract_last_edited' => array(
			'fd' => array(
				'contract_id' =>	array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'account_id' =>		array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'edited_on' =>		array('type' => 'int', 'precision' => '8', 'nullable' => false)	//timestamp
			),
			'pk' => array('contract_id','account_id'),
			'fk' => array(
				'rental_contract' => array('contract_id' => 'id'),
				'phpgw_accounts' =>  array('account_id' => 'account_id')
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
					'rental_contract' =>  array('contract_id' => 'id'),
					'rental_composite' => array('composite_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
		),
		// A tenant
		'rental_party' => array(
				'fd' => array(
					'id' =>             array('type' => 'auto', 'nullable' => false),
					'identifier' =>     array('type' => 'varchar','precision' => '255','nullable' => true),
					'first_name' =>     array('type' => 'varchar','precision' => '255','nullable' => true),
					'last_name' =>      array('type' => 'varchar','precision' => '255','nullable' => true),
					'comment' =>        array('type' => 'text'),
					'is_inactive' =>    array('type' => 'bool', 'nullable' => false),
					'title' =>          array('type' => 'varchar','precision' => '255','nullable' => true),
					'company_name' =>	array('type' => 'varchar','precision' => '255','nullable' => true),
					'department' =>     array('type' => 'varchar','precision' => '255','nullable' => true),
					'address_1' =>      array('type' => 'varchar','precision' => '255','nullable' => true),
					'address_2' =>      array('type' => 'varchar','precision' => '255','nullable' => true),
					'postal_code' =>	array('type' => 'varchar','precision' => '255','nullable' => true),
					'place' =>          array('type' => 'varchar','precision' => '255','nullable' => true),
					'phone' =>          array('type' => 'varchar','precision' => '255','nullable' => true),
					'mobile_phone' =>	array('type' => 'varchar','precision' => '255','nullable' => true),
					'fax' =>            array('type' => 'varchar','precision' => '255','nullable' => true),
					'email' =>          array('type' => 'varchar','precision' => '255','nullable' => true),
					'url' =>            array('type' => 'varchar','precision' => '255','nullable' => true),
					'account_number' =>	array('type' => 'varchar','precision' => '255','nullable' => true),
					'reskontro' =>      array('type' => 'varchar','precision' => '255','nullable' => true),
					'location_id' =>    	array('type' => 'int', 'precision' => '4', 'nullable' => true),
					'result_unit_number' => array('type' => 'varchar','precision' => '255','nullable' => true),
					'org_enhet_id' =>		array ('type' => 'int','precision' => 8, 'nullable' => true),
					'unit_leader' =>	array ('type' => 'varchar','precision' => '255', 'nullable' => true)
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
				'party_id' =>       array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'is_payer'	=>      array('type' => 'bool','nullable' => false, 'default' => 'false')
			),
			'pk' => array('contract_id','party_id'),
			'fk' => array(
					'rental_contract' =>    array('contract_id' => 'id'),
					'rental_party' =>       array('party_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
		),
		// Price list
		'rental_price_item' => array(
				'fd' => array(
					'id' =>         array('type' => 'auto', 'nullable' => false),
					'title' =>      array('type' => 'varchar','precision' => '255','nullable' => false),
					'agresso_id' =>	array('type' => 'varchar','precision' => '255','nullable' => true),
					'is_area' =>	array('type' => 'bool','nullable' => false,'default' => 'true'),
					'is_inactive' =>	array('type' => 'bool','nullable' => true,'default' => 'false'),
					'is_adjustable' =>	array('type' => 'bool','nullable' => true,'default' => 'true'),
					'standard' =>	array('type' => 'bool','nullable' => true,'default' => 'false'),
					'price' =>      array('type' => 'decimal', 'precision' => '20','scale' => '2', 'nullable' => true),
					'responsibility_id' => array('type' => 'int','precision' => '4', 'nullable' => false)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array('agresso_id')
		),
		// Price item related to contract
		'rental_contract_price_item' => array(
			'fd' => array(
				'id' =>             array('type' => 'auto', 'nullable' => false),
				'price_item_id' =>	array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'contract_id' =>	array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'title' =>          array('type' => 'varchar','precision' => '255','nullable' => false),
				'area' =>           array('type' => 'decimal', 'precision' => '20','scale' => '2', 'nullable' => true),
				'count' =>          array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'agresso_id' => 	array('type' => 'varchar','precision' => '255','nullable' => true),
				'is_area' =>        array('type' => 'bool','nullable' => false,'default' => 'true'),
				'price' =>          array('type' => 'decimal', 'precision' => '20','scale' => '2', 'nullable' => true),
				'total_price' => 	array('type' => 'decimal', 'precision' => '20','scale' => '2', 'nullable' => true),
				'date_start' => 	array('type' => 'int', 'precision' => '8', 'nullable' => true),
				'date_end' => 		array('type' => 'int', 'precision' => '8', 'nullable' => true),
				'is_billed' =>        array('type' => 'bool','nullable' => false,'default' => 'false'),
				'is_one_time' =>	array('type' => 'bool','nullable' => true,'default' => 'false')
			),
			'pk' => array('id'),
			'fk' => array(
				'rental_price_item' =>  array('price_item_id' => 'id'),
				'rental_contract' =>    array('contract_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
		),
		'rental_notification' => array(
			'fd' => array(
				'id'			=>	array('type' => 'auto', 'nullable' => false),
				'location_id'   =>	array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'account_id'	=>	array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'contract_id'	=>	array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'message'		=> 	array('type' => 'text'),
				'date'			=>	array('type' => 'int', 'precision' => '8', 'nullable' => false),	// timestamp, from
				'last_notified' =>	array('type' => 'int', 'precision' => '8', 'nullable' => true),
				'recurrence'	=>	array('type' => 'int', 'precision' => '4', 'nullable' => false, 'default' => 0),
				'deleted'		=>	array('type' => 'bool', 'default' => 'false')
			),
			'pk' => array('id'),
			'fk' => array(
				'rental_contract' => array('contract_id' => 'id'),
				'phpgw_accounts'  => array('account_id' => 'account_id'),
				'phpgw_locations' => array('location_id' => 'location_id')
			),
			'ix' => array(),
			'uc' => array()
		),
		'rental_notification_workbench' => array(
			'fd' => array(
				'id'                => array('type' => 'auto', 'nullable' => false),
				'account_id'        => array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'date'              => array('type' => 'int', 'precision' => '8', 'nullable' => false), 	// timestamp, deadline
				'notification_id'   => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'workbench_message' => array('type' => 'text'),
				'dismissed'         => array('type' => 'bool', 'default' => false)
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
				'id'				=> array('type' => 'auto', 'nullable' => false),
				'total_sum'			=> array('type' => 'decimal', 'precision' => '20','scale' => '2'),
				'success'			=> array('type' => 'bool','nullable' => false,'default' => 'false'),
				'created_by'		=> array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'timestamp_start'	=> array('type' => 'int', 'precision' => '8', 'nullable' => true),
				'timestamp_stop'	=> array('type' => 'int', 'precision' => '8', 'nullable' => true),
				'timestamp_commit'	=> array('type' => 'int', 'precision' => '8', 'nullable' => true),
				'location_id'		=> array('type' => 'int', 'precision' => '4', 'nullable' => false), // Contract type
				'title'				=> array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
				'deleted'			=> array('type' => 'bool', 'default' => 'false'),
				'export_format'		=> array('type' => 'varchar', 'precision' => '255', 'nullable' => true),
				'export_data'		=> array('type' => 'blob', 'nullable' => true),
				'serial_start'		=> array('type' => 'int', 'precision' => '8', 'nullable' => true),
				'serial_end'		=> array('type' => 'int', 'precision' => '8', 'nullable' => true)
			),
			'pk' => array('id'),
			'fk' => array(
				'phpgw_locations'		=> array('location_id' => 'location_id'),
				'phpgw_accounts'		=> array('created_by' => 'account_id')
			),
			'ix' => array(),
			'uc' => array()
		),
		'rental_billing_info' => array(
			'fd' => array(
				'id'				=> array('type' => 'auto', 'nullable' => false),
				'billing_id'		=> array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'location_id'		=> array('type' => 'int', 'precision' => '4', 'nullable' => false), // Contract type
				'term_id'			=> array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'year'				=> array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'month'				=> array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'deleted'			=> array('type' => 'bool', 'default' => 'false')
			),
			'pk' => array('id'),
			'fk' => array(
				'rental_billing'	=> array('billing_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
		),
		'rental_invoice' => array(
			'fd' => array(
				'id'				=> array('type' => 'auto', 'nullable' => false),
				'contract_id'		=> array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'billing_id'		=> array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'party_id'			=> array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'timestamp_created'	=> array('type' => 'int', 'precision' => '8', 'nullable' => false),
				'timestamp_start'	=> array('type' => 'int', 'precision' => '8', 'nullable' => false),
				'timestamp_end'		=> array('type' => 'int', 'precision' => '8', 'nullable' => false),
				'total_sum'			=> array('type' => 'decimal', 'precision' => '20','scale' => '2'),
				'total_area'		=> array('type' => 'decimal', 'precision' => '20','scale' => '2'),
				'header'			=> array('type' => 'varchar', 'precision' => '255','nullable' => true),
				'account_in'		=> array('type' => 'varchar', 'precision' => '255','nullable' => true),
				'account_out'		=> array('type' => 'varchar', 'precision' => '255','nullable' => true),
				'service_id'		=> array('type' => 'varchar', 'precision' => '255', 'nullable' => true), // Tjeneste
				'responsibility_id' => array('type' => 'varchar', 'precision' => '255', 'nullable' => true), // Ansvar
				'project_id'		=> array('type' => 'varchar', 'precision' => '255', 'nullable' => true),
				'serial_number'		=> array('type' => 'int', 'precision' => '8', 'nullable' => true)		//Sekvensnummer (Agresso)
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
				'title'			=> array('type' => 'varchar','precision' => '255','nullable' => false),
				'area'			=> array('type' => 'decimal', 'precision' => '20','scale' => '2', 'nullable' => true),
				'count'			=> array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'agresso_id'	=> array('type' => 'varchar','precision' => '255','nullable' => true),
				'is_area'		=> array('type' => 'bool','nullable' => false,'default' => 'true'),
				'price'			=> array('type' => 'decimal', 'precision' => '20','scale' => '2', 'nullable' => true),
				'total_price'	=> array('type' => 'decimal', 'precision' => '20','scale' => '2', 'nullable' => true),
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
		'rental_document_types' => array(
			'fd' => array(
				'id' => array('type' => 'auto', 'nullable' => false),
				'title' => array('type' => 'varchar', 'precision' => '255', 'nullable' => false)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'rental_document' => array(
			'fd' => array(
				'id'            => array('type' => 'auto', 'nullable' => false),
				'name'          => array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
				'contract_id'   => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'party_id'      => array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'title'         => array('type' => 'varchar', 'precision' => '255', 'nullable' => true),
				'description'   => array('type' => 'text', 'nullable' => true),
				'type_id'       => array('type' => 'int', 'precision' => '4', 'nullable' => false)
			),
			'pk' => array('id'),
			'fk' => array(
				'rental_contract'   => array('contract_id' => 'id'),
				'rental_party'      => array('party_id' => 'id'),
				'rental_document_types' => array('type_id' => 'id')
			),
			'ix' => array(),
			'uc' => array()
		),
		
		'rental_contract_types' => array(
				'fd' => array(
					'id' =>                 array('type' => 'auto', 'nullable' => false),
					'label'	=>              array('type' => 'varchar', 'precision' => '255', 'nullable' => false),
					'responsibility_id' =>  array('type' => 'int', 'precision' => 4, 'nullable' => false),
					'account' =>			array('type' => 'varchar', 'precision' => '255', 'nullable' => true)
				),
				'pk' => array('id'),
				'fk' => array(
					'rental_contract_responsibility' => array( 'responsibility_id' => 'id'),
				),
				'ix' => array(),
				'uc' => array()
		),
		
		'rental_adjustment' => array(
			'fd' => array(
				'id' =>                 	array('type' => 'auto', 'nullable' => false),
				'price_item_id'	=>			array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'responsibility_id' =>  	array('type' => 'int', 'precision' => '4', 'nullable' => false),
				'adjustment_date' =>  		array('type' => 'int', 'precision' => '8', 'nullable' => true),
				'adjustment_type' =>		array('type' => 'varchar','precision' => '255','nullable' => true),
				'new_price' =>				array('type' => 'decimal', 'precision' => '20','scale' => '2', 'nullable' => true),
				'percent' =>				array('type' => 'decimal', 'precision' => '20','scale' => '2', 'nullable' => true),
				'adjustment_interval' =>	array('type' => 'int', 'precision' => '4', 'nullable' => true),
				'is_manual' =>				array('type' => 'bool','nullable' => false,'default' => 'false'),
				'is_executed' =>			array('type' => 'bool','nullable' => false,'default' => 'false')	
			),
			'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
		)
	);