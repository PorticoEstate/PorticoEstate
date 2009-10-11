<?php
phpgw::import_class('rental.socomposite'); 
include_class('rental', 'exportable', 'inc/model/');

class rental_agresso_lg04 implements rental_exportable
{
	protected $billing_job;
	protected $date_str;
	protected $orders;
	
	public function __construct($billing_job)
	{
		$this->billing_job = $billing_job;
		$this->date_str = date('ymd', $billing_job->get_timestamp_stop());
		$this->orders = null;
	}
	
	/**
	 * @see rental_exportable
	 */
	public function get_id()
	{
		return 'Agresso LG04';
	}
	
	/**
	 * Returns the file contents as a string.
	 * 
	 * @see rental_exportable
	 */
	public function get_contents()
	{
		$contents = '';
		if($this->orders == null) // Data hasn't been created yet
		{
			$this->run();
		}
		foreach($this->orders as $order)
		{
			foreach($order as $line)
			{
				$contents .= "{$line}\n";
			}
		}
		return $contents;
	}
	
	public function get_missing_billing_info($contract)
	{
		$missing_billing_info = array();
		return $missing_billing_info;
	}
	
	/**
	 * Does all the dirty work by building all the lines of Agresso contents
	 * from the billing job.
	 */
	protected function run()
	{
		$this->orders = array();
		$decimal_separator = isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['decimal_separator']) ? $GLOBALS['phpgw_info']['user']['preferences']['rental']['decimal_separator'] : ',';
		$thousands_separator = isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['thousands_separator']) ? $GLOBALS['phpgw_info']['user']['preferences']['rental']['thousands_separator'] : '.'; 
		// We need all invoices for this billing
		$invoices = rental_soinvoice::get_instance()->get(null, null, 'id', true, null, null, array('billing_id' => $this->billing_job->get_id()));
		foreach($invoices as $invoice) // Runs through all invoices
		{
			// We need all price items in the invoice
			$price_items = rental_soinvoice_price_item::get_instance()->get(null, null, null, null, null, null, array('invoice_id' => $invoice->get_id()));
			$composite_name = '';
			// We need to get the composites to get a composite name for the Agresso export
			$composites = rental_socomposite::get_instance()->get(null, null, null, null, null, null, array('contract_id' => $invoice->get_contract_id()));
			if($composites != null && count($composites) > 0)
			{
				$keys = array_keys($composites);
				$composite_name = $composites[$keys[0]]->get_name();
			}
			// HACK to get the needed location code for the building
			$building_location_code = rental_socomposite::get_instance()->get_building_location_code($invoice->get_contract_id());
			$price_item_data = array();
			foreach($price_items as $price_item) // Runs through all items
			{
				$data = array();
				$data['amount'] = $price_item->get_total_price();
				$data['article_description'] = $price_item->get_title();
				$data['article_code'] = $price_item->get_agresso_id();
				$price_item_data[] = $data;
			}
			$this->orders[] = $this->get_order($invoice->get_header(), $invoice->get_party()->get_identifier(), $invoice->get_id(), $this->billing_job->get_year(), $this->billing_job->get_month(), $invoice->get_account_out(), $price_item_data, $invoice->get_responsibility_id(), $invoice->get_service_id(), $building_location_code, $invoice->get_project_id(), $composite_name);
		}
	}
	
	/**
	 * Builds one single order of the Agresso file.
	 * 
	 */
	protected function get_order($header, $party_id, $order_id, $bill_year, $bill_month, $account, $product_items, $responsibility, $service, $building, $project, $text)
	{
		$order = array();
		$order[] =  // Header line
			 '1'														//  1	accept_flag
			.sprintf("%8s", '')											//		just white space..
			.sprintf("%20s", '')										//  3	accountable
			.sprintf("%160s", '')										//  4	address
			.sprintf("%20s", '')										//		just white space..
			.sprintf("%08s", '')										//  8	apar_id
			.sprintf("%30s", '')										//  9	apar_name
			.sprintf("%50s", '')										//		just white space..
			.sprintf("%2s", '')											// 12	att_1_id
			.sprintf("%2s", '')											// 13	att_2_id
			.sprintf("%2s", '')											// 14	att_3_id
			.sprintf("%2s", '')											// 15	att_4_id
			.sprintf("%2s", '')											// 16	att_5_id
			.sprintf("%2s", '')											// 17	att_6_id
			.sprintf("%2s", '')											// 18	att_7_id
			.sprintf("%35s", '')										// 19	bank_account
			.sprintf("%-12s", "PE{$this->date_str}")					// 20	batch_id
			.'BY'														// 21	client
			.sprintf("%2s", '')											// 22	client_ref
			.sprintf("%17s", "{$this->date_str}")						// 23	confirm_date
			.sprintf("%1s", '')											// 24	control
			.sprintf("%17s", '')										//		just white space..
			.'NOK'														// 26	currency
			.sprintf("%60s", '')										// 27	del_met_descr
			.sprintf("%60s", '')										// 28	del_term_descr
			.sprintf("%255s", '')										// 29	deliv_addr
			.sprintf("%50s", '')										// 30	deliv_attention
			.sprintf("%3s", '')											// 31	deliv_countr
			.sprintf("%17s", "{$this->date_str}")						// 32	deliv_date
			.sprintf("%8s", '')											// 33	deliv_method
			.sprintf("%8s", '')											// 34	deliv_terms
			.sprintf("%52s", '')										//		just white space..
			.sprintf("%-12.12s", $account)								// 42	dim_value_1
			.sprintf("%12s", '')										// 43	dim_value_2
			.sprintf("%12s", '')										// 44	dim_value_3
			.sprintf("%12s", '')										// 45	dim_value_4
			.sprintf("%12s", '')										// 46	dim_value_5
			.sprintf("%12s", '')										// 47	dim_value_6
			.sprintf("%12s", '')										// 48	dim_value_7
			.sprintf("%17s", '')										//		just white space..
			.sprintf("%017s", '')										// 51	exch_rate
			.sprintf("%-15.15s", $party_id)								// 52	ext_ord_ref
			.sprintf("%6s", '')											// 53	intrule_id
			.sprintf("%8s", '')											//	just white space..
			.sprintf("%-120.120s", $header)								// 56	long_info1
			.sprintf("%120s", '')										// 57	long_info2
			.sprintf("%10s", '')										//		just white space..
			.sprintf("%08s", '')										// 59	main_apar_id
			.sprintf("%50s", '')										// 60?	mark_attention
			.sprintf("%3s", '')											// 61	mark_ctry_cd
			.sprintf("%120s", '')										// 62	markings
			.sprintf("%-17s", '')										// 63	obs_date
			.sprintf("%-17s", '')										// 64	order_date
			.sprintf("%09.9s", $order_id)								// 65	order_id
			.'FS'														// 66	order_type
			.'IP'														// 67	pay_method
			.sprintf("%02s", '').sprintf("%04.4s", $bill_year).sprintf("%02.2s", $bill_month)	// 69?	period
			.sprintf("%30s", '')										// 70	place
			.sprintf("%40s", '')										// 71	province
			.sprintf("%12s", '')										//		just white space..
			.sprintf("%-8s", 'PE')										// 73	responsible
			.sprintf("%-8s", 'PE')										// 74	responsible2
			.sprintf("%8s", '')											//		just white space..
			.sprintf("%-08s", '')										// 76	sequence_ref
			.sprintf("%80s", '')										//		just white space..
			.'N'														// 79	status
			.sprintf("%4s", '')											//		just white space..
			.sprintf("%08s", '')										// 83	template_id
			.sprintf("%2s", '')											// 84	terms_id
			.sprintf("%12s", '')										// 85	tekx1
			.sprintf("%12s", '')										// 86	tekst2
			.sprintf("%12s", '')										// 87	tekst3
			.sprintf("%12s", '')										// 88	text4
			.'42'														// 89	trans_type
			.sprintf("%70s", '')										//		just white space..
			.sprintf("%09s", '')										// 93	voucher_ref
			.'XX'														// 94	voucher_type
			.sprintf("%4s", '')											//		just white space..
			.sprintf("%15s", '')										// 96	zip_code
		;
		$item_counter = 0;
		foreach($product_items as $item) // All products (=price items)
		{
			$order[] = // Product line
				 sprintf("%1s", '')										//		just white space..
				.sprintf("%8s", '')										//  2	account
				.sprintf("%180s", '')									//		just white space..
				.sprintf("%02s", '')									//  5	allocation_key
				.$this->get_formatted_amount($item['amount'])			//  6	amount
				.'1'													//  7	amount_set
				.sprintf("%38s", '')									//		just white space..
				.sprintf("%-35.35s", $item['article_description'])		// 10	art_descr
				.sprintf("%-15.15s", $item['article_code'])				// 11	article
				.sprintf("%49s", '')									//		just white space..
				.sprintf("%-12s", "PE{$this->date_str}")				// 20	batch_id
				.'BY'													// 21	client
				.sprintf("%20s", '')									//		just white space..
				.sprintf("%017s", '')									// 25	cur_amount
				.sprintf("%464s", '')									//		just white space..
				.sprintf("%-8.8s", $responsibility)						// 35	dim_1
				.sprintf("%-8.8s", $service)							// 36	dim_2
				.sprintf("%-8.8s", $building)							// 37	dim_3
				.sprintf("%8s", '')										// 38	dim_4
				.sprintf("%-12.12s", $project)							// 39	dim_5
				.sprintf("%4s", '')										// 40	dim_6
				.sprintf("%4s", '')										// 41	dim_7
				.sprintf("%84s", '')									//		just white space..
				.sprintf("%017s", '')									// 49	disc_percent
				.sprintf("%017s", '')									// 51?	exch_rate
				.sprintf("%21s", '')									//		just white space..
				.sprintf("%04.4s", ++$item_counter)						// 54	line_no
				.sprintf("%4s", '')										// 55	location
				.sprintf("%240s", '')									//		just white space..
				.sprintf("%10s", '')									// 58	lot
				.sprintf("%215s", '')									//		just white space..
				.sprintf("%09.9s", $order_id)							// 65	order_id
				.sprintf("%4s", '')										//		just white space..
				.sprintf("%02s", '').sprintf("%04.4s", $bill_year).sprintf("%02.2s", $bill_month)	// 69?	period
				.sprintf("%70s", '')									//		just white space..
				.sprintf("%12s", '')									// 72	rel_value
				.sprintf("%16s", '')									//		just white space..
				.sprintf("%08s", '')									// 75	sequence_no
				.sprintf("%8s", '')										//		just white space..
				.sprintf("%20s", '')									// 77	serial_no
				.sprintf("%60s", '')									//		just white space..
				.'N'													// 79	status
				.sprintf("%2s", '')										// 81	tax_code
				.sprintf("%2s", '')										// 82	tax_system
				.sprintf("%-08s", '')									// 83	template_id
				.sprintf("%50s", '')									//		just white space..
				.'42'													// 89	trans_type
				.sprintf("%3s", '')										// 90	unit_code
				.sprintf("%50s", '')									// 91	unit_descr
				.sprintf("%017s", 1*100)								// 92	value_1
				.sprintf("%9s", '')										//		just white space..
				.'XX'													// 94	voucher_type
				.sprintf("%4s", '')										// 95	warehouse
				.sprintf("%15s", '')									//		just white space..
			;
			$order[] = // Text line
				 sprintf("%346s", '')									//		just white space..
				.sprintf("%-12s", "PE{$this->date_str}")				// 20	batch_id
				.'BY'													// 21	client
				.sprintf("%692s", '')									//		just white space..
				.sprintf("%04.4s", $item_counter)						// 54	line_no
				.sprintf("%469s", '')									//		just white space..
				.sprintf("%09.9s", $order_id)							// 65	order_id
				.sprintf("%110s", '')									//		just white space..
				.sprintf("%08s", 1)										// 75	sequence_no
				.sprintf("%28s", '')									//		just white space..
				.sprintf("%-60.60s", $text)								// 78	shot_info
				.sprintf("%63s", '')									//		just white space..
				.'42'													// 89	trans_type
				.sprintf("%79s", '')									//		just white space..
				.'XX'													// 94	voucher_type
				.sprintf("%19s", '')									//		just white space..
			;
		}
		return $order;
	}
	
	protected function get_formatted_amount($amount)
	{
		$amount = round($amount, 2) * 100;
		if($amount < 0) // Negative number
		{
			return '-' . sprintf("%016.16s", abs($amount)); // We have to have the sign at the start of the string
		}
		return sprintf("%017.17s", $amount);
	} 
	
} 

?>