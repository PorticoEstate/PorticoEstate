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
        
        public function get_contents_excel()
	{
		if($this->orders == null) // Data hasn't been created yet
		{
			$this->run_excel_export();
		}
		return $this->orders;
	}
	
	public function get_missing_billing_info($contract)
	{
		$missing_billing_info = array();
		$contract_parties = $contract->get_parties();
		if($contract_parties == null || count($contract_parties) < 1)
		{
			$missing_billing_info[] = 'Missing contract party.';
		}
		
		$payer_id = $contract->get_payer_id();
		if($payer_id == null || $payer_id = 0)
		{
			$missing_billing_info[] = 'Missing payer id.';
		}
		
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
		
		
		$config	= CreateObject('phpgwapi.config','rental');
		$config->read();
		$serial_config_start = $config->config_data['serial_start'];
		$serial_config_stop =  $config->config_data['serial_stop'];
		
		
		if(	isset($serial_config_start) && is_numeric($serial_config_start) &&
			isset($serial_config_stop) && is_numeric($serial_config_stop))
		{
			$max_serial_number_used = rental_soinvoice::get_instance()->get_max_serial_number_used($serial_config_start, $serial_config_stop);
			
			if(isset($max_serial_number_used) && is_numeric($max_serial_number_used) && $max_serial_number_used > 0 )
			{
				$serial_number = $max_serial_number_used + 1;
			}
			else
			{
				$serial_number = $serial_config_start;
			}
			
			$number_left_in_sequence = $serial_config_stop - $serial_number;
			
			if($number_left_in_sequence < count($invoices))
			{
				//var_dump("Out of sequence numbers");
				//Give error message (out of sequence numbers) and return
			}
		}
		else
		{
			//var_dump("Not configured properly");
			//Give error message (not configured properly) and return
		}
		
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
				$description = $price_item->get_title();
				$start = $price_item->get_timestamp_start();
				$stop = $price_item->get_timestamp_end();
				if(isset($start) && isset($stop))
				{
					$description .= ' '.date('j/n',$start).'-'.date('j/n',$stop);
				}
				$data['article_description'] = $description;
				$data['article_code'] = $price_item->get_agresso_id();
				$price_item_data[] = $data;
			}
			$this->orders[] = $this->get_order(
				$invoice->get_header(), 
				$invoice->get_party()->get_identifier(), 
				$invoice->get_id(), 
				$this->billing_job->get_year(), 
				$this->billing_job->get_month(), 
				$invoice->get_account_out(), 
				$price_item_data, 
				$invoice->get_responsibility_id(), 
				$invoice->get_service_id(), 
				$building_location_code, 
				$invoice->get_project_id(), 
				$composite_name,
				$serial_number,
				$invoice->get_reference()
			);
			$invoice->set_serial_number($serial_number);
			$serial_number++;
		}
		
		$so_invoice = rental_soinvoice::get_instance();
		
		//Store invoices with serial numbers
		foreach($invoices as $invoice) // Runs through all invoices
		{
			$so_invoice->store($invoice);
		}
	}
        
        protected function run_excel_export()
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
            $price_item_counter = 0;
			foreach($price_items as $price_item) // Runs through all items
			{
				$data = array();
				$data['amount'] = $price_item->get_total_price();
				$description = $price_item->get_title();
				$start = $price_item->get_timestamp_start();
				$stop = $price_item->get_timestamp_end();
				if(isset($start) && isset($stop))
				{
					$description .= ' '.date('j/n',$start).'-'.date('j/n',$stop);
				}
				$data['article_description'] = $description;
				$data['article_code'] = $price_item->get_agresso_id();
				$price_item_data[] = $data;
                
                $serialized_party = $invoice->get_party()->serialize();
                $party_name = $serialized_party['name'];
                        
                $this->orders[] = $this->get_order_excel(
                    $invoice->get_header(), 
                    $invoice->get_party()->get_identifier(),
                    $party_name,
                    $invoice->get_id(), 
                    $this->billing_job->get_year(), 
                    $this->billing_job->get_month(), 
                    $invoice->get_account_out(), 
                    $data, 
                    $invoice->get_responsibility_id(), 
                    $invoice->get_service_id(), 
                    $building_location_code, 
                    $invoice->get_project_id(), 
                    $composite_name,
                    $invoice->get_reference(),
                    $price_item_counter
                );
                $price_item_counter++;
			}
		}
	}
	
	/**
	 * Builds one single order of the Agresso file.
	 * 
	 */
	protected function get_order($header, $party_id, $order_id, $bill_year, $bill_month, $account, $product_items, $responsibility, $service, $building, $project, $text, $serial_number, $client_ref)
	{
		
		//$order_id = $order_id + 39500000;
		// XXX: Which charsets do Agresso accept/expect? Do we need to something regarding padding and UTF-8?
		$order = array();
		
		
		$order[] =  // Header line
			 '1'														//  1		accept_flag
			.sprintf("%8s", '')											//	2		just white space..
			.sprintf("%20s", '')										//  3		accountable
			.sprintf("%160s", '')										//  4		address
			.sprintf("%20s", '')										//	5-7		just white space..
			.sprintf("%08s", '')										//  8		apar_id
			.sprintf("%30s", '')										//  9		apar_name
			.sprintf("%50s", '')										//	10-11	just white space..
			.sprintf("%2s", '')											// 	12		att_1_id
			.sprintf("%2s", '')											// 	13		att_2_id
			.sprintf("%2s", '')											// 	14		att_3_id
			.sprintf("%2s", '')											// 	15		att_4_id
			.sprintf("%2s", '')											// 	16		att_5_id
			.sprintf("%2s", '')											// 	17		att_6_id
			.sprintf("%2s", '')											// 	18		att_7_id
			.sprintf("%35s", '')										// 	19		bank_account
			.sprintf("%-12s", "BKBPE{$this->date_str}")					// 	20		batch_id				DATA
			.'BY'														// 	21		client					DATA
			.sprintf("%2s", '')											// 	22		client_ref
			.sprintf("%-17s", "{$this->date_str}")						// 	23		confirm_date			DATA
			.sprintf("%1s", '')											// 	24		control
			.sprintf("%17s", '')										//	25		just white space..
			.'NOK'														// 	26		currency				DATA
			.sprintf("%60s", '')										// 	27		del_met_descr
			.sprintf("%60s", '')										// 	28		del_term_descr
			.sprintf("%255s", '')										// 	29		deliv_addr
			.sprintf("%50s", '')										// 	30		deliv_attention
			.sprintf("%3s", '')											// 	31		deliv_countr
			.sprintf("%-17s", "{$this->date_str}")						// 	32		deliv_date				DATA
			.sprintf("%8s", '')											// 	33		deliv_method
			.sprintf("%8s", '')											// 	34		deliv_terms
			.sprintf("%52s", '')										//	35-41	just white space..
			.sprintf("%-12.12s", $account)								// 	42		dim_value_1				DATA
			.sprintf("%12s", '')										// 	43		dim_value_2
			.sprintf("%12s", '')										// 	44		dim_value_3
			.sprintf("%12s", '')										// 	45		dim_value_4
			.sprintf("%12s", '')										// 	46		dim_value_5
			.sprintf("%12s", '')										// 	47		dim_value_6	
			.sprintf("%12s", '')										// 	48		dim_value_7
			.sprintf("%17s", '')										//	49-50	just white space..
			.sprintf("%017s", '')										// 	51		exch_rate
			.sprintf("%-15.15s", $client_ref)							// 	52		ext_ord_ref
			.sprintf("%6s", '')											// 	53		intrule_id
			.sprintf("%8s", '')											//	54-55	just white space..
			.sprintf("%-120.120s", utf8_decode($header))				// 	56		long_info1				DATA
			.sprintf("%120s", '')										//	57		long_info2
			.sprintf("%10s", '')										//	58		just white space..
			.sprintf("%08s", '')										// 	59		main_apar_id
			.sprintf("%50s", '')										// 	60		mark_attention
			.sprintf("%3s", '')											// 	61		mark_ctry_cd
			.sprintf("%120s", '')										// 	62		markings
			.sprintf("%-17s", '')										// 	63		obs_date
			.sprintf("%-17s", '')										// 	64		order_date
			.sprintf("%09.9s", $serial_number)							// 	65		order_id				DATA
			.'FS'														// 	66		order_type				DATA
			.'IP'														// 	67		pay_method				DATA
																		//	(68)
			.sprintf("%02s", '')
			.sprintf("%04.4s", $bill_year)
			.sprintf("%02.2s", $bill_month)								// 	69		period					DATA
			.sprintf("%30s", '')										// 	70		place
			.sprintf("%40s", '')										// 	71		province
			.sprintf("%12s", '')										//	72		just white space..
			.sprintf("%-8s", 'BKBPE')									// 	73		responsible				DATA
			.sprintf("%-8s", 'BKBPE')									// 	74		responsible2			DATA
			.sprintf("%8s", '')											//	75		just white space..
			.sprintf("%-08s", '')										// 	76		sequence_ref
			.sprintf("%80s", '')										//	77-78	just white space..
			.'N'														// 	79		status					DATA
			.sprintf("%4s", '')											//	80-82	just white space..
			.sprintf("%08s", '')										// 	83		template_id
			.sprintf("%2s", '')											// 	84		terms_id
			.sprintf("%12s", '')										// 	85		tekx1
			.sprintf("%-12s", $party_id)								// 	86		tekst2					DATA
			.sprintf("%12s", '')										// 	87		tekst3
			.sprintf("%12s", '')										// 	88		text4
			.'42'														// 	89		trans_type				DATA
			.sprintf("%70s", '')										//	90-92	just white space..
			.sprintf("%09s", '')										// 	93		voucher_ref
			.'FU'														// 	94		voucher_type			DATA
			.sprintf("%4s", '')											//	95		just white space..
			.sprintf("%15s", '')										// 	96		zip_code
		;
		$item_counter = 0;
		foreach($product_items as $item) // All products (=price items)
		{
			$order[] = // Product line
				'0'														//	1		0 for påfølgende linjer etter ordrehde
				.sprintf("%8s", '')										//  2		account
				.sprintf("%180s", '')									//	3-4		just white space..
				.sprintf("%2s", '')										//  5		allocation_key
				.$this->get_formatted_amount($item['amount'])			//  6		amount					DATA
				.'1'													//  7		amount_set
				.sprintf("%38s", '')									//	8-9		just white space..
				.sprintf("%-35.35s", utf8_decode($item['article_description']))		// 	10	art_descr		DATA
				.sprintf("%-15.15s", $item['article_code'])				// 	11		article					DATA
				.sprintf("%49s", '')									//	12-19	just white space..
				.sprintf("%-12s", "BKBPE{$this->date_str}")				// 	20		batch_id				DATA
				.'BY'													// 	21		client					DATA
				.sprintf("%20s", '')									//	22-24	just white space..
				.sprintf("%017s", '')									// 	25		cur_amount
				.sprintf("%464s", '')									//	26-34	just white space..
				.sprintf("%-8.8s", $responsibility)						// 	35		dim_1					DATA
				.sprintf("%-8.8s", $service)							// 	36		dim_2					DATA
				.sprintf("%8s", '')										// 	37		dim_3
				.sprintf("%8s", '')										// 	38		dim_4
				.sprintf("%-12.12s", $project)							// 	39		dim_5					DATA
				.sprintf("%4s", '')										// 	40		dim_6
				.sprintf("%4s", '')										// 	41		dim_7
				.sprintf("%84s", '')									//	42-48	just white space..
				.sprintf("%017s", '')									// 	49		disc_percent
																		//	(50)
				.sprintf("%017s", '')									// 	51		exch_rate
				.sprintf("%21s", '')									//	52-53	just white space..
				.sprintf("%04.4s", ++$item_counter)						// 	54		line_no					DATA
				.sprintf("%4s", '')										// 	55		location
				.sprintf("%240s", '')									//	56-57	just white space..
				.sprintf("%10s", '')									// 	58		lot
				.sprintf("%215s", '')									//	59-64	just white space..
				.sprintf("%09.9s", $serial_number)						// 	65	order_id					DATA
				.sprintf("%4s", '')										//	66-67	just white space..
																		//	(68)
				.sprintf("%02s", '')
				.sprintf("%04.4s", $bill_year)
				.sprintf("%02.2s", $bill_month)							//	69		period					DATA
				.sprintf("%70s", '')									//	70-71	just white space..
				.sprintf("%12s", '')									// 	72		rel_value
				.sprintf("%16s", '')									//	73-74	just white space..
				.sprintf("%08s", '')									// 	75		sequence_no
				.sprintf("%8s", '')										//	76		just white space..
				.sprintf("%20s", '')									// 	77		serial_no
				.sprintf("%60s", '')									//	78		just white space..
				.'N'													// 	79		status					DATA
																		//	(80)
				.sprintf("%2s", '')										// 	81		tax_code
				.sprintf("%2s", '')										// 	82		tax_system
				.sprintf("%-08s", '')									// 	83		template_id
				.sprintf("%50s", '')									//	84-88	just white space..
				.'42'													// 	89		trans_type
				.sprintf("%3s", '')										// 	90		unit_code
				.sprintf("%50s", '')									// 	91		unit_descr
				.sprintf("%017s", 1*100)								// 	92		value_1					DATA
				.sprintf("%9s", '')										//	93		just white space..
				.'FU'													// 	94		voucher_type			DATA
				.sprintf("%4s", '')										// 	95		warehouse
				.sprintf("%15s", '')									//	96		just white space..
			;
			$order[] = // Text line
				'0'.													//	1
				 sprintf("%345s", '')									//	2-19	just white space..		DATA
				.sprintf("%-12s", "BKBPE{$this->date_str}")				// 	20		batch_id				DATA
				.'BY'													// 	21		client					DATA
				.sprintf("%692s", '')									//	22-53	just white space..
				.sprintf("%04.4s", $item_counter)						// 	54		line_no					DATA
				.sprintf("%469s", '')									//	55-64	just white space..
				.sprintf("%09.9s", $serial_number)						// 	65		order_id				DATA
				.sprintf("%110s", '')									//	66-74	just white space..
				.sprintf("%08s", 1)										// 	75		sequence_no				DATA
				.sprintf("%28s", '')									//	76-77	just white space..
				.sprintf("%-60.60s", utf8_decode($text))				// 	78		shot_info				DATA
				.sprintf("%63s", '')									//	79-88	just white space..
				.'42'													// 	89		trans_type				DATA
				.sprintf("%79s", '')									//	90-93	just white space..
				.'FU'													// 	94		voucher_type			DATA
				.sprintf("%19s", '')									//	95-96	just white space..
			;
		}
		return str_replace(array("\n", "\r"), '', $order);
	}
        
        /**
	 * Builds one single order of the excel file.
	 * 
	 */
	protected function get_order_excel($header, $party_id, $party_name, $order_id, $bill_year, $bill_month, $account, $product_item, $responsibility, $service, $building, $project, $text, $client_ref, $counter)
	{
		
		//$order_id = $order_id + 39500000;
		// XXX: Which charsets do Agresso accept/expect? Do we need to something regarding padding and UTF-8?
		//$order = array();
		
		$item_counter = $counter;
        $order = array(
            'account' => $account,
            'client_ref' => $client_ref,
            'header' => utf8_decode($header),
            'bill_year' => $bill_year,
            'bill_month' => $bill_month,
            'Ansvar' => 'BKBPE',
            'Ansvar2' => 'BKBPE',
            'Party' => $party_id,
            'name' => $party_name,
            'amount' => $this->get_formatted_amount_excel($product_item['amount']),
//                        'amount' => $this->get_formatted_amount($product_items[0]['amount']),
            'article description' => utf8_decode($product_item['article_description']),
            'article_code' => $product_item['article_code'],
            'batch_id' => "BKBPE{$this->date_str}",
            'client' => 'BY',
            'responsibility' => $responsibility,
            'service' => $service,
            'project' => $project,
            'counter' => ++$item_counter,
            'bill_year' => $bill_year,
            'bill_month' => $bill_month,
            'batch_id' => "BKBPE{$this->date_str}",
            'client' => 'BY',
            'item_counter' => $item_counter,
            'text' => utf8_decode($text)
            );
               
		return str_replace(array("\n", "\r"), '', $order);
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
        
        protected function get_formatted_amount_excel($amount)
	{
//            var_dump($amount);
            
//            var_dump($belop);
		$amount = round($amount, 2) * 100;
                $belop = substr($amount, 0, strlen($amount)-2) . '.' . substr($amount, -2);
		if($amount < 0) // Negative number
		{
			return '-' . sprintf("%016.16s", abs($belop)); // We have to have the sign at the start of the string
		}
		return sprintf("%017.17s", $belop);
	} 
	
} 

?>