<?php
phpgw::import_class('rental.socomposite'); 
include_class('rental', 'exportable', 'inc/model/');

class rental_agresso_gl07 implements rental_exportable
{
	protected $billing_job;
	protected $date_str;
	protected $lines;
	
	public function __construct($billing_job)
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
		if($this->lines == null) // Data hasn't been created yet
		{
			$this->run();
		}
		foreach($this->lines as $line)
		{
			$contents .= "{$line}\n";
		}
		return $contents;
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
		$invoices = rental_soinvoice::get_instance()->get(null, null, 'id', true, null, null, array('billing_id' => $this->billing_job->get_id()));
		foreach($invoices as $invoice) // Runs through all invoices
		{
			// We need all price items in the invoice
			$price_items = rental_soinvoice_price_item::get_instance()->get(null, null, null, null, null, null, array('invoice_id' => $invoice->get_id()));
			// HACK to get the needed location code for the building
			$buildine_location_code = rental_socomposite::get_instance()->get_building_location_code($invoice->get_contract_id());
			$description = "{$invoice->get_contract_id()}, " . number_format($invoice->get_total_area(), 1, $decimal_separator, $thousands_separator) . " m2, {$invoice->get_header()}"; 
			// The income side
			foreach($price_items as $price_item) // Runs through all items
			{
				$this->lines[] = $this->get_line($invoice->get_account_in(), $GLOBALS['phpgw_info']['user']['preferences']['rental']['responsibilty'], $invoice->get_service_id(), $building_location_code, $GLOBALS['phpgw_info']['user']['preferences']['rental']['project_id'], $price_item->get_agresso_id(), $price_item->get_total_price(), '', $description, $invoice->get_contract_id(), $this->billing_job->get_year(), $this->billing_job->get_month());
			}
			// The receiver's outlay side
			$this->lines[] = $this->get_line($invoice->get_account_out(), $invoice->get_responsibility_id(), $invoice->get_service_id(), $buildine_location_code, $invoice->get_project_id(), '', $invoice->get_total_sum(), $description, $invoice->get_contract_id(), $this->billing_job->get_year(), $this->billing_job->get_month());
		}
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
	protected function get_line($account, $responsibility, $service, $building, $project, $part_no, $amount, $description, $contract_id, $bill_year, $bill_month)
	{
		return
			 sprintf("%-25s", "PE{$this->date_str}")					//  1	batch_id
			.sprintf("%-25s", 'BI')										//  2	interface
			.sprintf("%-25s", 'HL')										//  3	voucher_type
			.sprintf("%-2s", 'GL')										//  4	trans_type
			.sprintf("%-25s", 'BY')										//  5	client
			.sprintf("%-25s", strtoupper($account))						//  6	account
			.sprintf("%-25s", strtoupper($responsibility))				//  7	dim_1
			.sprintf("%-25s", strtoupper($service))						//  8	dim_2
			.sprintf("%-25s", strtoupper($building))					//  9	dim_3
			.sprintf("%-25s", '')										// 10	dim_4
			.sprintf("%-25s", strtoupper($project))						// 11	dim_5
			.sprintf("%-25s", strtoupper($part_no))						// 12	dim_6
			.sprintf("%-25s", '')										// 13	dim_7
			.sprintf("%-25s", '0')										// 14	tax_code
			.sprintf("%-25s", '')										// 15	tax_system
			.sprintf("%-25s", "NOK")									// 16	currency
			.sprintf("%0-2s", '')										// 17	dc_flag
			.sprintf("%020s", number_format($amount, 2) * 100)			// 18	cur_amount
			.sprintf("%020s", number_format($amount, 2) * 100)			// 19	amount
			.sprintf("%011s", '')										// 20	number_1
			.sprintf("%0-20s", '')										// 21	value_1
			.sprintf("%0-20s", '')										// 22	value_2
			.sprintf("%0-20s", '')										// 23	value_3
			.sprintf("%-255s", $description)							// 24	description
			.sprintf("%-8s", '')										// 25	trans_date
			.$this->date_str											// 26	voucher_date
			.sprintf("%0-15s", '')										// 27	voucher_no
			.sprintf("%04s", $bill_year).sprintf("%02s", $bill_month)	// 28	period
			.sprintf("%-8s", '')										// 29
			.sprintf("%-8s", '')										// 30
			.sprintf("%-8s", '')										// 31
			.sprintf("%-8s", '')										// 32
			.sprintf("%-8s", '')										// 33
			.sprintf("%-8s", '')										// 34
			.sprintf("%-8s", '')										// 35
			.sprintf("%-15s", $contract_id)								// 36	order_id
			.sprintf("%-27s", '')										// 37
			.sprintf("%-2s", '')										// 38
			.sprintf("%-1s", '')										// 39
			.sprintf("%-1s", '')										// 40
			.sprintf("%-25s", '')										// 41
			.sprintf("%01s", '')										// 42
			.sprintf("%015s", '')										// 43
			.sprintf("%09s", '')										// 44
			.sprintf("%-25s", '')										// 45
			.sprintf("%-25s", '')										// 46
			.sprintf("%-25s", '')										// 47
			.sprintf("%-255s", '')										// 48
			.sprintf("%-160s", '')										// 49
			.sprintf("%-40s", '')										// 50
			.sprintf("%-40s", '')										// 51
			.sprintf("%-35s", '')										// 52
			.sprintf("%-2s", '')										// 53
			.sprintf("%-25s", '')										// 54
			.sprintf("%-15s", '')										// 55
			.sprintf("%-3s", '')										// 56
			.sprintf("%-25s", '')										// 57
			.sprintf("%020s", '')										// 58
			.sprintf("%020s", '')										// 59
			.sprintf("%-4s", '')										// 60
			.sprintf("%03s", '')										// 61
			.sprintf("%02s", '')										// 62
			.sprintf("%-13s", '')										// 63
			.sprintf("%-11s", '')										// 64
			.sprintf("%015s", '')										// 65
			.sprintf("%-2s", '')										// 66
			;
	}
	
} 

?>