<?php
	phpgw::import_class('rental.socomposite');
	include_class('rental', 'exportable', 'inc/model/');

	class rental_agresso_gl07 implements rental_exportable
	{

		protected $billing_job;
		protected $date_str;
		protected $lines;

		public function __construct( $billing_job )
		{
			$this->billing_job = $billing_job;
			$this->date_str = date('Ymd', $billing_job->get_timestamp_stop());
			$this->lines = null;
		}

		/**
		 * @see rental_exportable
		 */
		public function get_id()
		{
			return 'Agresso GL07';
		}

		/**
		 * Returns the file contents as a string.
		 * 
		 * @see rental_exportable
		 */
		public function get_contents()
		{
			$contents = '';
			if ($this->lines == null) // Data hasn't been created yet
			{
				$this->run();
			}
			foreach ($this->lines as $line)
			{
				$contents .= "{$line}\n";
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

			//FIXME: Might have to check for this one...
			/*
			  static $responsibility_arr = array();
			  static $responsibility_check = array();
			  if(!$responsibility_arr)
			  {
			  $responsibility_arr = execMethod('rental.bogeneric.get_list',array('type' => 'responsibility_unit'));
			  foreach ($responsibility_arr as $responsibility_entry)
			  {
			  $responsibility_check[$responsibility_entry['id']] = true;
			  }
			  }
			 */

			$missing_billing_info = array();

			$payer_id = $contract->get_payer_id();
			if ($payer_id == null || $payer_id = 0)
			{
				$missing_billing_info[] = 'Missing payer id.';
			}


			$contract_parties = $contract->get_parties();
			if ($contract_parties == null || count($contract_parties) < 1)
			{
				$missing_billing_info[] = 'Missing contract party.';
			}
			$account_in = $contract->get_account_in();
			if ($account_in == null || $account_in == '')
			{
				$missing_billing_info[] = 'Missing account in.';
			}
			$account_out = $contract->get_account_out();
			if ($account_out == null || $account_out == '')
			{
				$missing_billing_info[] = 'Missing account out.';
			}
			/* $responsibility_id_in = $GLOBALS['phpgw_info']['user']['preferences']['rental']['responsibility'];
			  if($responsibility_id_in == null || $responsibility_id_in == '')
			  {
			  $missing_billing_info[] = 'Missing system setting for responsibility id for the current user.';
			  }
			  else if(strlen($responsibility_id_in) != 6)
			  {
			  $missing_billing_info[] = 'System setting for responsibility id for the current user must be 6 characters.';
			  } */
			$responsibility_id_out = $contract->get_responsibility_id();
			if ($responsibility_id_out == null || $responsibility_id_out == '')
			{
				$missing_billing_info[] = 'Missing responsibility id.';
			}
			else if (strlen($responsibility_id_out) != 6)
			{
				$missing_billing_info[] = 'Responsibility id must be 6 characters.';
			}
			$service_id = $contract->get_service_id();
			if ($service_id == null || $service_id == '')
			{
				$missing_billing_info[] = 'Missing service id.';
			}
			else if (strlen($service_id) != 5)
			{
				$missing_billing_info[] = 'Service id must be 5 characters.';
			}
			// HACK to get the needed location code for the building
			$building_location_code = rental_socomposite::get_instance()->get_building_location_code($contract->get_id());
			if ($building_location_code == null || $building_location_code == '')
			{
				$missing_billing_info[] = 'Unable to get a location code for the building.';
			}
			else if (strlen($building_location_code) != 6)
			{
				$missing_billing_info[] = 'Invalid location code for the building.';
			}
			/* $project_id_in = $GLOBALS['phpgw_info']['user']['preferences']['rental']['project_id'];
			  if($project_id_in == null || $project_id_in == '')
			  {
			  $missing_billing_info[] = 'Missing system setting for project id.';
			  }
			  else if(strlen($project_id_in) > 6)
			  {
			  $missing_billing_info[] = 'System setting for project id can not be more than 6 characters.';
			  } */
			$project_id_out = $contract->get_project_id();
			if ($project_id_out == null || $project_id_out == '')
			{
				$missing_billing_info[] = 'Missing project id.';
			}
			else if (strlen($project_id_out) > 6)
			{
				$missing_billing_info[] = 'Project id can not be more than 6 characters.';
			}
			$price_items = rental_socontract_price_item::get_instance()->get(0, 0, '', false, '', '', array(
				'contract_id' => $contract->get_id()));
			foreach ($price_items as $price_item) // Runs through all items
			{
				$agresso_id = $price_item->get_agresso_id();
				if ($agresso_id == null || $agresso_id == '')
				{
					$missing_billing_info[] = 'One or more price items are missing Agresso ids.';
					break; // We only need one error message
				}
				else if (!preg_match("([A-Z]{1}[0-9]{3})", $agresso_id))
				{
					$missing_billing_info[] = 'One or more price items have an invalid Agresso id. Id must consist of one capital letter and three digits.';
					break; // We only need one error message
				}
			}
			return $missing_billing_info;
		}

		/**
		 * Does all the dirty work by building all the lines of Agresso contents
		 * from the billing job.
		 */
		protected function run()
		{
			$this->lines = array();
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
				// HACK to get the needed location code for the building
				$building_location_code = rental_socomposite::get_instance()->get_building_location_code($invoice->get_contract_id());
				$description = "{$invoice->get_old_contract_id()}, " . number_format($invoice->get_total_area(), 1, $decimal_separator, $thousands_separator) . " m2 - {$invoice->get_header()}";

				$responsibility_in = isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['responsibility']) ? $GLOBALS['phpgw_info']['user']['preferences']['rental']['responsibility'] : '028120';
				$project_id_in = isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['project_id']) ? $GLOBALS['phpgw_info']['user']['preferences']['rental']['project_id'] : '9';

				// The income side
				foreach ($price_items as $price_item) // Runs through all items
				{
					$this->lines[] = $this->get_line($invoice->get_account_in(), $responsibility_in, $invoice->get_service_id(), $building_location_code, $project_id_in, $price_item->get_agresso_id(), -1.0 * $price_item->get_total_price(), $description, $invoice->get_contract_id(), $this->billing_job->get_year(), $this->billing_job->get_month());
				}
				// The receiver's outlay side
				$this->lines[] = $this->get_line($invoice->get_account_out(), $invoice->get_responsibility_id(), $invoice->get_service_id(), $building_location_code, $invoice->get_project_id(), '', $invoice->get_total_sum(), $description, $invoice->get_contract_id(), $this->billing_job->get_year(), $this->billing_job->get_month());
			}
		}
		protected function run_excel_export( $excel_export_type )
		{
			switch ($excel_export_type)
			{
				case 'bk':
					$get_order_excel = 'get_order_excel_bk';
					break;
				case 'nlsh':
					$get_order_excel = 'get_order_excel_bk';//'get_order_excel_nlsh';
					break;

				default:
					$get_order_excel = 'get_order_excel_bk';
					break;
			}
			$this->lines = array();
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
				// HACK to get the needed location code for the building
				$building_location_code = rental_socomposite::get_instance()->get_building_location_code($invoice->get_contract_id());
				$description = "{$invoice->get_old_contract_id()}, " . number_format($invoice->get_total_area(), 1, $decimal_separator, $thousands_separator) . " m2 - {$invoice->get_header()}";

				$responsibility_in = isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['responsibility']) ? $GLOBALS['phpgw_info']['user']['preferences']['rental']['responsibility'] : '028120';
				$project_id_in = isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['project_id']) ? $GLOBALS['phpgw_info']['user']['preferences']['rental']['project_id'] : '9';

				// The income side
				foreach ($price_items as $price_item) // Runs through all items
				{
					$this->lines[] = $this->$get_order_excel($invoice->get_account_in(), $responsibility_in, $invoice->get_service_id(), $building_location_code, $project_id_in, $price_item->get_agresso_id(), -1.0 * $price_item->get_total_price(), $description, $invoice->get_contract_id(), $this->billing_job->get_year(), $this->billing_job->get_month());
				}
				// The receiver's outlay side
			//	$this->lines[] = $this->$get_order_excel($invoice->get_account_out(), $invoice->get_responsibility_id(), $invoice->get_service_id(), $building_location_code, $invoice->get_project_id(), '', $invoice->get_total_sum(), $description, $invoice->get_contract_id(), $this->billing_job->get_year(), $this->billing_job->get_month());
			}
			$this->orders = $this->lines;
		}

		/**
		 * Builds one single line of the Agresso file.
		 *
		 * @param $account
		 * @param $responsibility
		 * @param $service
		 * @param $building
		 * @param $project
		 * @param $part_no
		 * @param $amount
		 * @param $description
		 * @param $contract_id
		 * @param $bill_year
		 * @param $bill_month
		 * @return unknown_type
		 */
		protected function get_line( $account, $responsibility, $service, $building, $project, $part_no, $amount, $description, $contract_id, $bill_year, $bill_month )
		{
			// XXX: Which charsets do Agresso accept/expect? Do we need to something regarding padding and UTF-8?
			$line = sprintf("%-25.25s", "PE{$this->date_str}")  //  1	batch_id
				. sprintf("%-25s", 'BI') //  2	interface
				. sprintf("%-25s", 'HL') //  3	voucher_type
				. sprintf("%-2s", 'GL') //  4	trans_type
				. sprintf("%-25s", 'BY') //  5	client
				. sprintf("%-25.25s", strtoupper($account))  //  6	account
				. sprintf("%-25.25s", strtoupper($responsibility))   //  7	dim_1
				. sprintf("%-25.25s", strtoupper($service))  //  8	dim_2
				. sprintf("%-25.25s", strtoupper($building))  //  9	dim_3
				. sprintf("%-25s", '') // 10	dim_4
				. sprintf("%-25.25s", strtoupper($project))  // 11	dim_5
				. sprintf("%-25.25s", strtoupper($part_no))  // 12	dim_6
				. sprintf("%-25s", '') // 13	dim_7
				. sprintf("%-25s", '0') // 14	tax_code
				. sprintf("%-25s", '') // 15	tax_system
				. sprintf("%-25s", "NOK")   // 16	currency
				. sprintf("%02s", '') // 17	dc_flag
				. $this->get_formatted_amount($amount)   // 18	cur_amount
				. $this->get_formatted_amount($amount)   // 19	amount
				. sprintf("%011s", '') // 20	number_1
				. sprintf("%020s", '') // 21	value_1
				. sprintf("%020s", '') // 22	value_2
				. sprintf("%020s", '') // 23	value_3
				. sprintf("%-255.255s", iconv("UTF-8", "ISO-8859-1", $description))   // 24	description
				. sprintf("%-8s", '') // 25	trans_date
				. $this->date_str  // 26	voucher_date
				. sprintf("%015s", '') // 27	voucher_no
				. sprintf("%04.4s", $bill_year) . sprintf("%02.2s", $bill_month) // 28	period
				. sprintf("%-1s", '') // 29
				. sprintf("%-100s", '') // 30
				. sprintf("%-255s", '') // 31
				. sprintf("%-8s", '') // 32
				. sprintf("%-8s", '') // 33
				. sprintf("%-20s", '') // 34
				. sprintf("%-25s", '') // 35
				. sprintf("%-15.15s", $contract_id) // 36	order_id
				. sprintf("%-27s", '') // 37
				. sprintf("%-2s", '') // 38
				. sprintf("%-1s", '') // 39
				. sprintf("%-1s", '') // 40
				. sprintf("%-25s", '') // 41
				. sprintf("%01s", '') // 42
				. sprintf("%015s", '') // 43
				. sprintf("%09s", '') // 44
				. sprintf("%-25s", '') // 45
				. sprintf("%-25s", '') // 46
				. sprintf("%-25s", '') // 47
				. sprintf("%-255s", '') // 48
				. sprintf("%-160s", '') // 49
				. sprintf("%-40s", '') // 50
				. sprintf("%-40s", '') // 51
				. sprintf("%-35s", '') // 52
				. sprintf("%-2s", '') // 53
				. sprintf("%-25s", '') // 54
				. sprintf("%-15s", '') // 55
				. sprintf("%-3s", '') // 56
				. sprintf("%-25s", '') // 57
				. sprintf("%020s", '') // 58
				. sprintf("%020s", '') // 59
				. sprintf("%-4s", '') // 60
				. sprintf("%03s", '') // 61
				. sprintf("%02s", '') // 62
				. sprintf("%-13s", '') // 63
				. sprintf("%-11s", '') // 64
				. sprintf("%015s", '') // 65
				. sprintf("%-2s", '') // 66
			;
			return str_replace(array("\n", "\r"), '', $line);
		}
		/**
		 * Builds one single order of the excel file.
		 *
		 */
		protected function get_order_excel_bk( $account, $responsibility, $service, $building, $project, $part_no, $amount, $description, $contract_id, $bill_year, $bill_month )
		{

			//$order_id = $order_id + 39500000;
			// XXX: Which charsets do Agresso accept/expect? Do we need to something regarding padding and UTF-8?
			//$order = array();

			$item_counter = $counter;
			$order = array(
				'contract_id' => $contract_id,
				'account' => $account,
				'client_ref' => $client_ref,
				'header' => utf8_decode($header),
				'bill_year' => $bill_year,
				'bill_month' => $bill_month,
				'building' => $building,
				'name' => $party_name,
				'amount' => $this->get_formatted_amount_excel($amount),
				'article description' => utf8_decode($product_item['article_description']),
				'article_code' => $product_item['article_code'],
				'batch_id' => "BKBPE{$this->date_str}",
				'client' => 'BY',
				'responsibility' => $responsibility,
				'service' => $service,
				'project' => $project,
				'part_no' => $part_no,
				'counter' => ++$item_counter,
				'batch_id' => "BKBPE{$this->date_str}",
				'client' => 'BY',
				'item_counter' => $item_counter,
				'text' => utf8_decode($description)
			);

			return str_replace(array("\n", "\r"), '', $order);
		}

		protected function get_formatted_amount( $amount )
		{
			$amount = round($amount, 2) * 100;
			if ($amount <= 0) // Negative number , extra check for '-0' which proved to be a problem
			{
				return '-' . sprintf("%019.19s", abs($amount)); // We have to have the sign at the start of the string
			}
			return sprintf("%020.20s", $amount);
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