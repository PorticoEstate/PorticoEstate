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
		$this->date_str = date('Ymd', $billing_job->get_timestamp_stop());
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
			$contents .= "{$order[0]}\n{$order[1]}\n{$order[2]}\n";
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
			// HACK to get the needed location code for the building
			$building_location_code = rental_socomposite::get_instance()->get_building_location_code($invoice->get_contract_id());
			$description = "{$invoice->get_contract_id()}, " . number_format($invoice->get_total_area(), 1, $decimal_separator, $thousands_separator) . " m2 - {$invoice->get_header()}"; 
			// The income side
			foreach($price_items as $price_item) // Runs through all items
			{
				$this->orders[] = $this->get_order($invoice->get_account_in(), $GLOBALS['phpgw_info']['user']['preferences']['rental']['responsibility'], $invoice->get_service_id(), $building_location_code, $GLOBALS['phpgw_info']['user']['preferences']['rental']['project_id'], $price_item->get_agresso_id(), -1.0 * $price_item->get_total_price(), $description, $invoice->get_contract_id(), $this->billing_job->get_year(), $this->billing_job->get_month());
			}
			// The receiver's outlay side
			$this->orders[] = $this->get_order($invoice->get_account_out(), $invoice->get_responsibility_id(), $invoice->get_service_id(), $building_location_code, $invoice->get_project_id(), '', $invoice->get_total_sum(), $description, $invoice->get_contract_id(), $this->billing_job->get_year(), $this->billing_job->get_month());
			// org. no, invoice id, bill year, bill month
		}
	}
	
	/**
	 * Builds one single order of the Agresso file.
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
	protected function get_order($party_id, $order_id, $bill_year, $bill_month, $account, $responsibility, $service, $building, $project, $part_no, $amount, $description, $contract_id )
	{
		$header = 
			 '1'														//  1	accept_flag
			.sprintf("%9s", '')											//		just white space??
			.sprintf("%20s", '')										//  3	accountable
			.sprintf("%160s", '')										//  4	address
			.sprintf("%20s", '')										//		just white space??
			.sprintf("%08s", '')										//  8	apar_id
			.sprintf("%30s", '')										//  9	apar_name
			.sprintf("%50s", '')										//		just white space??
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
			.sprintf("%-17s", "PE{$this->date_str}")					// 23	confirm_date
			.sprintf("%1s", '')											// 24	control
			.sprintf("%17s", '')										//		just white space??
			.'NOK'														// 26	currency
			.sprintf("%60s", '')										// 27	del_met_descr
			.sprintf("%60s", '')										// 28	del_term_descr
			.sprintf("%255s", '')										// 29	deliv_addr
			.sprintf("%50s", '')										// 30	deliv_attention
			.sprintf("%3s", '')											// 31	deliv_countr
			.sprintf("%17s", "PE{$this->date_str}")						// 32	deliv_date
			.sprintf("%8s", '')											// 33	deliv_method
			.sprintf("%8s", '')											// 34	deliv_terms
			.sprintf("%52s", '')										//		just white space??
			.sprintf("%12s", '')										// 42	dim_value_1
			.sprintf("%12s", '')										// 43	dim_value_2
			.sprintf("%12s", '')										// 44	dim_value_3
			.sprintf("%12s", '')										// 45	dim_value_4
			.sprintf("%12s", '')										// 46	dim_value_5
			.sprintf("%12s", '')										// 47	dim_value_6
			.sprintf("%12s", '')										// 48	dim_value_7
			.sprintf("%17s", '')										//		just white space??
			.sprintf("%017s", '')										// 51	exch_rate
			.sprintf("%15s", $party_id)									// 52	ext_ord_ref
			.sprintf("%6s", '')											// 53	intrule_id
			.sprintf("%8s", '')											//		just white space??
			.sprintf("%120s", '')										// 56	long_info1
			.sprintf("%120s", '')										// 57	long_info2
			.sprintf("%10s", '')										//		just white space??
			.sprintf("%8s", '')											// 59	main_apar_id
			.sprintf("%50s", '')										// 60?	mark_attention
			.sprintf("%3s", '')											// 61	mark_ctry_cd
			.sprintf("%120s", '')										// 62	markings
			.sprintf("%-17s", '')										// 63	obs_date
			.sprintf("%-17s", '')										// 64	order_date
			.sprintf("%-9s", $order_id)									// 65	order_id
			.'FS'														// 66	order_type
			.'IP'														// 67	pay_method
			.sprintf("%02s", '').sprintf("%04s", $bill_year).sprintf("%02s", $bill_month)	// 69?	period
			.sprintf("%30s", '')										// 70	place
			.sprintf("%40s", '')										// 71	province
			.sprintf("%12s", '')										//		just white space??
			.sprintf("%8s", 'PE')										// 73	responsible
			.sprintf("%8s", 'PE')										// 74	responsible2
			.sprintf("%8s", '')											//		just white space??
			.sprintf("%-08s", '')										// 76	sequence_ref
			.sprintf("%80s", '')										//		just white space??
			.'N'														// 79	status
			.sprintf("%4s", '')											//		just white space??
			.sprintf("%-08s", '')										// 83	template_id
			.sprintf("%2s", '')											// 84	terms_id
			.sprintf("%12s", '')										// 85	tekx1
			.sprintf("%12s", '')										// 86	tekst2
			.sprintf("%12s", '')										// 87	tekst3
			.sprintf("%12s", '')										// 88	text4
			.'42'														// 89	trans_type
			.sprintf("%80s", '')										//		just white space??
			.sprintf("%-09s", '')										// 93	voucher_ref
			.'XX'														// 94	voucher_type
			.sprintf("%4s", '')											//		just white space??
			.sprintf("%15s", '')										// 96	zip_code
		;
		$item = '';
		$text = '';
		return array($header, $item, $text);
	}
	
	protected function get_formatted_amount($amount)
	{
		$amount = round($amount, 2) * 100;
		if($amount < 0) // Negative number
		{
			return '-' . sprintf("%019s", abs($amount)); // We have to have the sign at the start of the string
		}
		return sprintf("%020s", $amount);
	} 
	
} 

?>