<?php
	phpgw::import_class('rental.socomposite');
	include_class('rental', 'exportable', 'inc/model/');

	class rental_default_export implements rental_exportable
	{

		protected $billing_job;
		protected $date_str;
		protected $lines;

		//protected $exports;

		public function __construct( $billing_job )
		{
			$this->billing_job = $billing_job;
			$this->date_str = date('Ymd', $billing_job->get_timestamp_stop());
			$this->lines = null;
			/*
			  $dh = @opendir(PHPGW_SERVER_ROOT . "/rental/inc/export/{$GLOBALS['phpgw_info']['user']['domain']}");
			  $myfilearray = array();

			  // For each entry in directory...
			  while($file = readdir($dh))
			  {
			  // ...ignore files beginning with "."
			  if(substr($file, 0, 1) != '.' && is_file(PHPGW_SERVER_ROOT . "/rental/inc/export/{$GLOBALS['phpgw_info']['user']['domain']}/{$file}"))
			  {
			  $myfilearray[] = $file;
			  }
			  }
			  closedir($dh);
			  sort($myfilearray);

			  for($i = 0; $i < count($myfilearray); $i++)
			  {
			  $fname = preg_replace('/_/', ' ', $myfilearray[$i]);
			  $sel_file = '';
			  if($myfilearray[$i] == $selected)
			  {
			  $sel_file = 'selected';
			  }

			  $conv_list[] = array(
			  'id' => $myfilearray[$i],
			  'name' => $fname,
			  'selected' => $sel_file
			  );
			  }

			  for($i = 0; $i < count($conv_list); $i++)
			  {
			  if($conv_list[$i]['selected'] != 'selected')
			  {
			  unset($conv_list[$i]['selected']);
			  }
			  }

			  $this->exports = $conv_list; */
		}

		/**
		 * @see rental_exportable
		 */
		public function get_id()
		{
			return null;
		}

		/**
		 * Returns the file contents as a string.
		 *
		 * @see rental_exportable
		 */
		public function get_contents()
		{

		}

		public function get_missing_billing_info( $contract )
		{
			
		}

		/**
		 * Does all the dirty work by building all the lines of Agresso contents
		 * from the billing job.
		 */
		protected function run()
		{

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

		}

		protected function get_formatted_amount( $amount )
		{

		}
	}