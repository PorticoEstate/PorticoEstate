<?php
	phpgw::import_class('rental.socomposite');
	phpgw::import_class('rental.socontract');
	include_class('rental', 'exportable', 'inc/model/');

	class rental_agresso_lg04 implements rental_exportable
	{

		protected static $bo;
		protected $billing_job;
		protected $date_str;
		protected $orders;
		protected $prizebook;
		protected $batch_id;
		protected $check_customer_id;
		protected $dateformat;

		public function __construct( $billing_job = null)
		{
			if($billing_job)
			{
				$this->billing_job = $billing_job;
				$this->date_str = date('ymd', $billing_job->get_timestamp_stop());
			}
			$this->orders = null;

			$config = CreateObject('phpgwapi.config', 'rental')->read();
			$organization = empty($config['organization']) ? 'bergen' : $config['organization'];

			if($organization == 'bergen')
			{
				$this->check_customer_id = false;
			}
			else//nlsh
			{
				$this->check_customer_id = true;
			}
			$this->dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

		}

		public static function get_instance()
		{
			if (self::$bo == null)
			{
				self::$bo = new rental_agresso_lg04();
			}
			return self::$bo;
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
			if ($this->orders == null) // Data hasn't been created yet
			{
				$this->run();
			}
			foreach ($this->orders as $order)
			{
				foreach ($order as $line)
				{
					$contents .= "{$line}\n";
				}
			}
			return $contents;
		}

		public function get_contents_excel( $excel_export_type )
		{
			if ($this->orders == null) // Data hasn't been created yet
			{
				$this->run_excel_export($excel_export_type);
			}
			return $this->orders;
		}

		public function get_missing_billing_info( $contract )
		{
			$missing_billing_info = array();
			$contract_parties = $contract->get_parties();
			if ($contract_parties == null || count($contract_parties) < 1)
			{
				$missing_billing_info[] = 'Missing contract party.';
			}

			$payer_id = $contract->get_payer_id();

			if ($payer_id == null || $payer_id == 0)
			{
				$missing_billing_info[] = 'Missing payer id.';
			}
			else
			{
				$customer_id = $contract_parties[$payer_id]->get_customer_id();
				if($this->check_customer_id && empty($customer_id))
				{
					$missing_billing_info[] = 'Missing customer id.';
				}
			}

			if(!$contract->get_billing_start_date())
			{
				$missing_billing_info[] = 'Missing start_date.';
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
			$invoices = rental_soinvoice::get_instance()->get(0, 0, 'id', true, '', '', array(
				'billing_id' => $this->billing_job->get_id()));


			$config = CreateObject('phpgwapi.config', 'rental');
			$config->read();
			$serial_config_start = $config->config_data['serial_start'];
			$serial_config_stop = $config->config_data['serial_stop'];
			$organization = empty($config->config_data['organization']) ? 'bergen' : $config->config_data['organization'];


			if (isset($serial_config_start) && is_numeric($serial_config_start) &&
				isset($serial_config_stop) && is_numeric($serial_config_stop))
			{
				$max_serial_number_used = rental_soinvoice::get_instance()->get_max_serial_number_used($serial_config_start, $serial_config_stop);

				if (isset($max_serial_number_used) && is_numeric($max_serial_number_used) && $max_serial_number_used > 0)
				{
					$serial_number = $max_serial_number_used + 1;
				}
				else
				{
					$serial_number = $serial_config_start;
				}

				$number_left_in_sequence = $serial_config_stop - $serial_number;

				if ($number_left_in_sequence < count($invoices))
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

			foreach ($invoices as $invoice) // Runs through all invoices
			{
				// We need all price items in the invoice
				$price_items = rental_soinvoice_price_item::get_instance()->get(0, 0, '', false, '', '', array(
					'invoice_id' => $invoice->get_id()));
				$composite_name = '';
				// We need to get the composites to get a composite name for the Agresso export
				$composites = rental_socomposite::get_instance()->get(0, 0, '', false, '', '', array(
					'contract_id' => $invoice->get_contract_id()));
				if ($composites != null && count($composites) > 0)
				{
					$keys = array_keys($composites);
					$composite_name = $composites[$keys[0]]->get_name();
				}
				// HACK to get the needed location code for the building
				$building_location_code = rental_socomposite::get_instance()->get_building_location_code($invoice->get_contract_id());

				$price_item_data = array();
				foreach ($price_items as $price_item) // Runs through all items
				{
					$data = array();
					$data['amount'] = $price_item->get_total_price();
					$description = $price_item->get_title();
					$start = $price_item->get_timestamp_start();
					$stop = $price_item->get_timestamp_end();
					if (!$price_item->get_is_one_time() && isset($start) && isset($stop))
					{
						$description .= ' ' . date('j/n', $start) . '-' . date('j/n', $stop);
					}
					$data['article_description'] = $description;
					$data['article_code'] = $price_item->get_agresso_id();
					$price_item_data[] = $data;
				}
				$this->orders[] = $this->get_order($invoice, $this->billing_job->get_year(), $this->billing_job->get_month(), $price_item_data, $building_location_code, $composite_name, $serial_number, $organization);
				$invoice->set_serial_number($serial_number);
				$serial_number++;
			}

			$so_invoice = rental_soinvoice::get_instance();

			$so_invoice->transaction_begin();
			//Store invoices with serial numbers
			foreach ($invoices as $invoice) // Runs through all invoices
			{
				$so_invoice->store($invoice);
			}
			return $so_invoice->transaction_commit();
		}


		function get_prizebook()
		{
			$this->prizebook = array();
			$db = $GLOBALS['phpgw']->db;
			$sql = "SELECT id, agresso_id, price, type FROM rental_price_item";
			$db->query($sql);
			while($db->next_record())
			{
				$agresso_id = $db->f('agresso_id');

				$entry = array(
					'price'	=> $db->f('price'),
					'type'	=> $db->f('type'),
				);
				$this->prizebook[$agresso_id] = $entry;
			}
		}

		protected function run_excel_export( $excel_export_type )
		{

			$this->get_prizebook();

			switch ($excel_export_type)
			{
				case 'bk':
					$get_order_excel = 'get_order_excel_bk';
					break;
				case 'nlsh':
					$get_order_excel = 'get_order_excel_nlsh';
					break;

				default:
					$get_order_excel = 'get_order_excel_bk';
					break;
			}
			$this->orders = array();
			$decimal_separator = isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['decimal_separator']) ? $GLOBALS['phpgw_info']['user']['preferences']['rental']['decimal_separator'] : ',';
			$thousands_separator = isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['thousands_separator']) ? $GLOBALS['phpgw_info']['user']['preferences']['rental']['thousands_separator'] : '.';
			// We need all invoices for this billing
			$invoices = rental_soinvoice::get_instance()->get(0, 0, 'id', true, '', '', array(
				'billing_id' => $this->billing_job->get_id()));

			foreach ($invoices as $invoice) // Runs through all invoices
			{
				// We need all price items in the invoice
				$price_items = rental_soinvoice_price_item::get_instance()->get(0, 0, '', false, '', '', array(
					'invoice_id' => $invoice->get_id()));
				$composite_name = '';
				// We need to get the composites to get a composite name for the Agresso export
				$composites = rental_socomposite::get_instance()->get(0, 0, '', false, '', '', array(
					'contract_id' => $invoice->get_contract_id()));
				if ($composites != null && count($composites) > 0)
				{
					$keys = array_keys($composites);
					$composite_name = $composites[$keys[0]]->get_name();
				}
				// HACK to get the needed location code for the building
				$building_location_code = rental_socomposite::get_instance()->get_building_location_code($invoice->get_contract_id());

				/*				 * Sigurd:Start contract type* */
				$contract = rental_socontract::get_instance()->get_single($invoice->get_contract_id());
				$current_contract_type_id = $contract->get_contract_type_id();
				$contract_type_label = lang(rental_socontract::get_instance()->get_contract_type_label($current_contract_type_id));
				$contract_id = $contract->get_old_contract_id();
				$party_names = explode('<br/>', rtrim($contract->get_party_name(), '<br/>'));
				$start_date = $GLOBALS['phpgw']->common->show_date($contract->get_contract_date()->get_start_date(), $this->dateformat);
				$end_date = $GLOBALS['phpgw']->common->show_date($contract->get_contract_date()->get_end_date(), $this->dateformat);
				$billing_start_date = $GLOBALS['phpgw']->common->show_date($contract->get_billing_start_date(), $this->dateformat);
				$billing_end_date = $GLOBALS['phpgw']->common->show_date($contract->get_billing_end_date(), $this->dateformat);

				/*				 * End contract type* */

				$price_item_data = array();
				$price_item_counter = 0;
				foreach ($price_items as $price_item) // Runs through all items
				{
					$data = array();
					$data['amount'] = $price_item->get_total_price();
					$description = $price_item->get_title();
					$start = $price_item->get_timestamp_start();
					$stop = $price_item->get_timestamp_end();
					if (isset($start) && isset($stop))
					{
						$description .= ' ' . date('j/n', $start) . '-' . date('j/n', $stop);
					}
					$data['article_description'] = $description;
					$data['article_code'] = $price_item->get_agresso_id();
					$price_item_data[] = $data;

					$serialized_party = $invoice->get_party()->serialize();
					$party_name = $serialized_party['name'];
					$_party_names = array();

					if (count($party_names) > 1)
					{
						foreach ($party_names as $value)
						{
							if ($party_name == $value)
							{
								continue;
							}
							$_party_names[] = $value;
						}
					}
					else
					{
						$_party_names = $party_names;
					}

					$party_full_name = implode(', ', $_party_names);

					$this->orders[] = $this->$get_order_excel(
						$start_date, $end_date, $billing_start_date, $billing_end_date, $invoice->get_header(), $invoice->get_party()->get_identifier(), $party_name, $serialized_party['address'], $party_full_name, $invoice->get_id(), $this->billing_job->get_year(), $this->billing_job->get_month(), $invoice->get_account_out(), $data, $invoice->get_responsibility_id(), $invoice->get_service_id(), $building_location_code, $invoice->get_project_id(), $composite_name, $invoice->get_reference(), $price_item_counter, $invoice->get_account_in(), //ny
						$invoice->get_responsibility_id(), //ny
						$contract_type_label, //ny
						$contract_id, //ny
						$invoice->get_customer_order_id(),
						$serialized_party['customer_id']
					);
					$price_item_counter++;
				}
			}
		}

		/**
		 * Builds one single order of the Agresso file.
		 *
		 */
		protected function get_order( $invoice, $bill_year, $bill_month, $product_items,  $building, $text, $serial_number, $organization )
		{
			$header = $invoice->get_header();
			$party = $invoice->get_party();
			$party_id = $party->get_identifier();
			$order_id = $invoice->get_id();
			$account = $invoice->get_account_out();
			$responsibility = $invoice->get_responsibility_id();
			$service = $invoice->get_service_id();
			$project = $invoice->get_project_id();
			$client_ref = $invoice->get_reference();
			$customer_order_id = $invoice->get_customer_order_id();

			$contract = rental_socontract::get_instance()->get_single($invoice->get_contract_id());

			$start_date = $GLOBALS['phpgw']->common->show_date($contract->get_contract_date()->get_start_date(), $this->dateformat);
			$end_date = $GLOBALS['phpgw']->common->show_date($contract->get_contract_date()->get_end_date(), $this->dateformat);

			//$order_id = $order_id + 39500000;
			// XXX: Which charsets do Agresso accept/expect? Do we need to something regarding padding and UTF-8?
			$order = array();
			$extra_text = "";
			$extra_text2 = "";

			if(!$organization || $organization == 'bergen') //Bergen kommune
			{
				$att_1_id = '';
				$dim_value_1 = $account;
				$dim_1 = $responsibility;
				$batch_id = "BKBPE{$this->date_str}";
				$client = 'BY'; // For Bergen Kommune
				$confirm_date = $this->date_str;
				$pay_method = 'IP';
				$responsible ='BKBPE';
				$responsible2 ='BKBPE';
				$terms_id = '';
				$voucher_type = 'FU';
				$apar_id = '';
				$order_type = 'FS';
			}
			else if($organization == 'nlsh')
			{

				$parties = $contract->get_parties();
				$payer_id = $contract->get_payer_id();

				$_party_names = array();

				foreach ($parties as $_party_key => $_party)
				{
					if($_party_key == $payer_id)
					{
						continue;
					}
					$_party_name = $_party->get_last_name() . ', ' . $_party->get_first_name();
					if($_party->get_department())
					{
						$_party_name .= ' (' . $_party->get_department() . ')';
					}

					$_party_names[] = $_party_name;

				}

				if($_party_names)
				{
					$extra_text = implode('; ', $_party_names); // leieboer
				}

				$extra_text_sequence_no = 1;

				$contract_id = $contract->get_old_contract_id();
				$extra_text2 = "Kontrakt: $contract_id [{$start_date} - {$end_date}]"; // Kontrakt, fra dato – til dato.

				$att_1_id = 'DR';
				$dim_value_1 = ''; //endre til k.sted?
				$dim_1 = $responsibility; //k.sted
//				$batch_id =  'PU' . sprintf("%010s",$this->billing_job->get_id());
				$batch_id =  'PU' . sprintf("%08s",$this->billing_job->get_id()) . '  ';
				$client = '14';
				$confirm_date = '';
				$pay_method = '';
				$responsible ='PORT';
				$responsible2 ='PORT';
				$terms_id = '14';
				$voucher_type = 'SO';
				$apar_id = $party->get_customer_id();//kundenr fra agresso
				$order_type = 'PO';
				$service = '';
			}

			$order[] = // Header line
				'1'  //  1		accept_flag
				. sprintf("%8s", '')  //	2		just white space..
				. sprintf("%20s", '') //  3		accountable
				. sprintf("%160s", '') //  4		address
				. sprintf("%20s", '') //	5-7		just white space..
				. sprintf("%08s", $apar_id) //  8		apar_id
				. sprintf("%30s", '') //  9		apar_name
				. sprintf("%50s", '') //	10-11	just white space..
				. sprintf("%2s", $att_1_id)  // 	12		att_1_id
				. sprintf("%2s", '')  // 	13		att_2_id
				. sprintf("%2s", '')  // 	14		att_3_id
				. sprintf("%2s", '')  // 	15		att_4_id
				. sprintf("%2s", '')  // 	16		att_5_id
				. sprintf("%2s", '')  // 	17		att_6_id
				. sprintf("%2s", '')  // 	18		att_7_id
				. sprintf("%35s", '') // 	19		bank_account
				. sprintf("%-12s", $batch_id)  // 	20		batch_id				DATA
				. sprintf("%2s", $client)  // 	21		client					DATA
				. sprintf("%2s", '')  // 	22		client_ref
				. sprintf("%-17s", "{$confirm_date}")   // 	23		confirm_date			DATA
				. sprintf("%1s", '')  // 	24		control
				. sprintf("%17s", '') //	25		just white space..
				. 'NOK'  // 	26		currency				DATA
				. sprintf("%60s", '') // 	27		del_met_descr
				. sprintf("%60s", '') // 	28		del_term_descr
				. sprintf("%255s", '') // 	29		deliv_addr
				. sprintf("%50s", '') // 	30		deliv_attention
				. sprintf("%3s", '')  // 	31		deliv_countr
				. sprintf("%-17s", "{$this->date_str}")   // 	32		deliv_date				DATA
				. sprintf("%8s", '')  // 	33		deliv_method
				. sprintf("%8s", '')  // 	34		deliv_terms
				. sprintf("%52s", '') //	35-41	just white space..
				. sprintf("%-12.12s", $dim_value_1)  // 	42		dim_value_1				DATA
				. sprintf("%12s", '') // 	43		dim_value_2
				. sprintf("%12s", '') // 	44		dim_value_3
				. sprintf("%12s", '') // 	45		dim_value_4
				. sprintf("%12s", '') // 	46		dim_value_5
				. sprintf("%12s", '') // 	47		dim_value_6
				. sprintf("%12s", '') // 	48		dim_value_7
				. sprintf("%17s", '') //	49-50	just white space..
				. sprintf("%017s", '') // 	51		exch_rate
				. sprintf("%-15.15s", $client_ref) // 	52		ext_ord_ref
				. sprintf("%6s", '')  // 	53		intrule_id
				. sprintf("%8s", '')  //	54-55	just white space..
				. sprintf("%-120.120s", utf8_decode($header)) // 	56		long_info1				DATA
				. sprintf("%120s", '') //	57		long_info2
				. sprintf("%10s", '') //	58		just white space..
				. sprintf("%08s", '') // 	59		main_apar_id
				. sprintf("%50s", '') // 	60		mark_attention
				. sprintf("%3s", '')  // 	61		mark_ctry_cd
				. sprintf("%120s", '') // 	62		markings
				. sprintf("%-17s", '') // 	63		obs_date
				. sprintf("%-17s", '') // 	64		order_date
				. sprintf("%09.9s", $serial_number) // 	65		order_id				DATA
				. $order_type //'FS'  // 	66		order_type				DATA
				. sprintf("%2s", $pay_method)  // 	67		pay_method				DATA
				//	(68)
				. sprintf("%02s", '')
				. sprintf("%04.4s", $bill_year)
				. sprintf("%02.2s", $bill_month)  // 	69		period					DATA
				. sprintf("%30s", '') // 	70		place
				. sprintf("%40s", '') // 	71		province
				. sprintf("%12s", '') //	72		just white space..
				. sprintf("%-8s", $responsible)   // 	73		responsible				DATA
				. sprintf("%-8s", $responsible2)   // 	74		responsible2			DATA
				. sprintf("%8s", '')  //	75		just white space..
				. sprintf("%-08s", '') // 	76		sequence_ref
				. sprintf("%80s", '') //	77-78	just white space..
				. 'N'  // 	79		status					DATA
				. sprintf("%4s", '')  //	80-82	just white space..
				. sprintf("%08s", '') // 	83		template_id
				. sprintf("%2s", $terms_id)  // 	84		terms_id
				. sprintf("%12s", '') // 	85		tekx1
				. sprintf("%-12.12s", $party_id)  // 	86		tekst2					DATA
				. sprintf("%12s", $customer_order_id ? $customer_order_id : '') // 	87		tekst3
				. sprintf("%12s", '') // 	88		text4
				. '42'  // 	89		trans_type				DATA
				. sprintf("%70s", '') //	90-92	just white space..
				. sprintf("%09s", '') // 	93		voucher_ref
				. $voucher_type  // 	94		voucher_type			DATA
				. sprintf("%4s", '')  //	95		just white space..
				. sprintf("%15s", '') // 	96		zip_code
			;
			$item_counter = 0;
			foreach ($product_items as $item) // All products (=price items)
			{
				$order[] = // Product line
					'0'  //	1		0 for påfølgende linjer etter ordrehde
					. sprintf("%8s", '') //  2		account
					. sprintf("%180s", '')   //	3-4		just white space..
					. sprintf("%2s", '') //  5		allocation_key
					. $this->get_formatted_amount($item['amount'])   //  6		amount					DATA
					. '1' //  7		amount_set
					. sprintf("%38s", '')   //	8-9		just white space..
					. sprintf("%-35.35s", utf8_decode($item['article_description']))  // 	10	art_descr		DATA
					. sprintf("%-15.15s", $item['article_code']) // 	11		article					DATA
					. sprintf("%49s", '')   //	12-19	just white space..
					. sprintf("%-12s", $batch_id) // 	20		batch_id				DATA
					. sprintf("%2s", $client) // 	21		client					DATA
					. sprintf("%20s", '')   //	22-24	just white space..
					. sprintf("%017s", '')   // 	25		cur_amount
					. sprintf("%464s", '')   //	26-34	just white space..
					. sprintf("%-8.8s", $dim_1)   // 	35		dim_1					DATA
					. sprintf("%-8.8s", $service) // 	36		dim_2					DATA
					. sprintf("%8s", '') // 	37		dim_3
					. sprintf("%8s", '') // 	38		dim_4
					. sprintf("%-12.12s", $project) // 	39		dim_5					DATA
					. sprintf("%4s", '') // 	40		dim_6
					. sprintf("%4s", '') // 	41		dim_7
					. sprintf("%84s", '')   //	42-48	just white space..
					. sprintf("%017s", '')   // 	49		disc_percent
					//	(50)
					. sprintf("%017s", '')   // 	51		exch_rate
					. sprintf("%21s", '')   //	52-53	just white space..
					. sprintf("%04.4s", ++$item_counter)   // 	54		line_no					DATA
					. sprintf("%4s", '') // 	55		location
					. sprintf("%240s", '')   //	56-57	just white space..
					. sprintf("%10s", '')   // 	58		lot
					. sprintf("%215s", '')   //	59-64	just white space..
					. sprintf("%09.9s", $serial_number)   // 	65	order_id					DATA
					. sprintf("%4s", '') //	66-67	just white space..
					//	(68)
					. sprintf("%02s", '')
					. sprintf("%04.4s", $bill_year)
					. sprintf("%02.2s", $bill_month) //	69		period					DATA
					. sprintf("%70s", '')   //	70-71	just white space..
					. sprintf("%12s", '')   // 	72		rel_value
					. sprintf("%16s", '')   //	73-74	just white space..
					. sprintf("%08s", '')   // 	75		sequence_no
					. sprintf("%8s", '') //	76		just white space..
					. sprintf("%20s", '')   // 	77		serial_no
					. sprintf("%60s", '')   //	78		just white space..
					. 'N' // 	79		status					DATA
					//	(80)
					. sprintf("%2s", '') // 	81		tax_code
					. sprintf("%2s", '') // 	82		tax_system
					. sprintf("%-08s", '')   // 	83		template_id
					. sprintf("%50s", '')   //	84-88	just white space..
					. '42' // 	89		trans_type
					. sprintf("%3s", '') // 	90		unit_code
					. sprintf("%50s", '')   // 	91		unit_descr
					. sprintf("%017s", 1 * 100)  // 	92		value_1					DATA
					. sprintf("%9s", '') //	93		just white space..
					. $voucher_type // 	94		voucher_type			DATA
					. sprintf("%4s", '') // 	95		warehouse
					. sprintf("%15s", '')   //	96		just white space..
				;
				$order[] = // Text line
					'0' . //	1
					sprintf("%345s", '')   //	2-19	just white space..		DATA
					. sprintf("%-12s", $batch_id) // 	20		batch_id				DATA
					. sprintf("%2s", $client) // 	21		client					DATA
					. sprintf("%692s", '')   //	22-53	just white space..
					. sprintf("%04.4s", $item_counter)   // 	54		line_no					DATA
					. sprintf("%469s", '')   //	55-64	just white space..
					. sprintf("%09.9s", $serial_number)   // 	65		order_id				DATA
					. sprintf("%110s", '')   //	66-74	just white space..
					. sprintf("%08s", 1) // 	75		sequence_no				DATA
					. sprintf("%28s", '')   //	76-77	just white space..
					. sprintf("%-60.60s", utf8_decode($text)) // 	78		shot_info				DATA
					. sprintf("%63s", '')   //	79-88	just white space..
					. '42' // 	89		trans_type				DATA
					. sprintf("%79s", '')   //	90-93	just white space..
					. $voucher_type // 	94		voucher_type			DATA
					. sprintf("%19s", '')   //	95-96	just white space..
				;

				if($extra_text)
				{
					$extra_text_sequence_no++;

					$order[] = // Text line
						'0' . //	1
						sprintf("%345s", '')   //	2-19	just white space..		DATA
						. sprintf("%-12s", $batch_id) // 	20		batch_id				DATA
						. sprintf("%2s", $client) // 	21		client					DATA
						. sprintf("%692s", '')   //	22-53	just white space..
						. sprintf("%04.4s", $item_counter)   // 	54		line_no					DATA
						. sprintf("%469s", '')   //	55-64	just white space..
						. sprintf("%09.9s", $serial_number)   // 	65		order_id				DATA
						. sprintf("%110s", '')   //	66-74	just white space..
						. sprintf("%08s", $extra_text_sequence_no) // 	75		sequence_no				DATA
						. sprintf("%28s", '')   //	76-77	just white space..
						. sprintf("%-60.60s", utf8_decode($extra_text)) // 	78		shot_info				DATA
						. sprintf("%63s", '')   //	79-88	just white space..
						. '42' // 	89		trans_type				DATA
						. sprintf("%79s", '')   //	90-93	just white space..
						. $voucher_type // 	94		voucher_type			DATA
						. sprintf("%19s", '')   //	95-96	just white space..
					;
				}

				if($extra_text2)
				{
					$extra_text_sequence_no++;

					$order[] = // Text line
						'0' . //	1
						sprintf("%345s", '')   //	2-19	just white space..		DATA
						. sprintf("%-12s", $batch_id) // 	20		batch_id				DATA
						. sprintf("%2s", $client) // 	21		client					DATA
						. sprintf("%692s", '')   //	22-53	just white space..
						. sprintf("%04.4s", $item_counter)   // 	54		line_no					DATA
						. sprintf("%469s", '')   //	55-64	just white space..
						. sprintf("%09.9s", $serial_number)   // 	65		order_id				DATA
						. sprintf("%110s", '')   //	66-74	just white space..
						. sprintf("%08s", $extra_text_sequence_no) // 	75		sequence_no				DATA
						. sprintf("%28s", '')   //	76-77	just white space..
						. sprintf("%-60.60s", utf8_decode($extra_text2)) // 	78		shot_info				DATA
						. sprintf("%63s", '')   //	79-88	just white space..
						. '42' // 	89		trans_type				DATA
						. sprintf("%79s", '')   //	90-93	just white space..
						. $voucher_type // 	94		voucher_type			DATA
						. sprintf("%19s", '')   //	95-96	just white space..
					;
				}
			}
			return str_replace(array("\n", "\r"), '', $order);
		}

		/**
		 * Builds one single order of the excel file.
		 *
		 */
		protected function get_order_excel_bk(
		$start_date, $end_date, $billing_start_date, $billing_end_date, $header, $party_id, $party_name, $party_address, $party_full_name, $order_id, $bill_year, $bill_month, $account, $product_item, $responsibility, $service, $building, $project, $text, $client_ref, $counter, $account_in, $responsibility_id, $contract_type_label, $contract_id, $customer_order_id, $customer_id )
		{

			//$order_id = $order_id + 39500000;
			// XXX: Which charsets do Agresso accept/expect? Do we need to something regarding padding and UTF-8?
			//$order = array();

			$item_counter = $counter;
			$order = array(
				'contract_id' => $contract_id,
				'account' => $account,
				'customer id' => $customer_id,
				'client_ref' => $client_ref,
				'customer order id' => $customer_order_id,
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

		protected function get_order_excel_nlsh(
		$start_date, $end_date, $billing_start_date, $billing_end_date, $header, $party_id, $party_name, $party_address, $party_full_name, $order_id, $bill_year, $bill_month, $account_out, $product_item, $responsibility, $service, $building, $project, $text, $client_ref, $counter, $account_in, $responsibility_id, $contract_type_label, $contract_id, $customer_order_id, $customer_id )
		{

			$article_price = $this->prizebook[$product_item['article_code']];

			switch ($article_price['type'])
			{
				case '1'://year
					$monthly_rent = $article_price['price'] / 12;
					break;
				case '2'://Month
					$monthly_rent = $article_price['price'];
					break;
				case '3'://day
					$monthly_rent = $article_price['price'] * 30;
					break;
				default:
					$monthly_rent = '';
					break;
			}

//			_debug_array($product_item);
//_debug_array(func_get_args());
			$item_counter = $counter;
			$order = array
				(
				'contract_id' => $contract_id,
				'date_start' => $start_date,
				'date_end' => $end_date,
				'billing_start' => $billing_start_date,
				'billing_end' => $billing_end_date,
				'Kontraktstype' => $contract_type_label, //FIXME
				'Art/konto inntektsside' => $account_in,
				'Art/konto utgiftsside' => $account_out, //FIXME
				'client_ref' => $client_ref,
				'header' => $header,
				'bill_year' => $bill_year,
				'bill_month' => $bill_month,
				'Ansvar' => $responsibility_id, //FIXME
//				'Ansvar2'				 => 'BKBPE',//FIXME
				'Party' => $party_id,
				'customer id' => $customer_id,
				'name' => $party_name,
				'address' => $party_address,
				'Leieboer' => $party_full_name,
				'amount' => $this->get_formatted_amount_excel($product_item['amount']),
				'Mnd leie' => $this->get_formatted_amount_excel($monthly_rent),
				'article description' => $product_item['article_description'],
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
				'text' => $text,
				'Kommentar' => 'Kommentar', //FIXME
			);

			return str_replace(array("\n", "\r"), '', $order);
		}

		protected function get_formatted_amount( $amount )
		{
			$amount = round($amount, 2) * 100;
			if ($amount < 0) // Negative number
			{
				return '-' . sprintf("%016.16s", abs($amount)); // We have to have the sign at the start of the string
			}
			return sprintf("%017.17s", $amount);
		}

		protected function get_formatted_amount_excel( $amount )
		{
//            var_dump($amount);
//            var_dump($belop);
			$amount = round($amount, 2) * 100;
			$belop = substr($amount, 0, strlen($amount) - 2) . '.' . substr($amount, -2);
			if ($amount < 0) // Negative number
			{
				return '-' . sprintf("%016.16s", abs($belop)); // We have to have the sign at the start of the string
			}
			return sprintf("%017.17s", $belop);
		}
	}