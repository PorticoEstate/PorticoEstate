<?php
	include_class('rental', 'contract', 'inc/model/');
	include_class('rental', 'invoice', 'inc/model/');
	include_class('rental', 'model', 'inc/model/');

	/**
	 * Class that represents the actual billing job.
	 *
	 */
	class rental_billing extends rental_model
	{

		protected $id;
		protected $location_id; // Contract type
		protected $billing_term;
		protected $year;
		protected $month;
		protected $title;
		protected $success;
		protected $total_sum;
		protected $timestamp_start;
		protected $timestamp_stop;
		protected $timestamp_commit;
		protected $deleted;
		protected $created_by;
		protected $has_generated_export;
		protected $contract_type_title;
		protected $billing_info;
		protected $responsibility_title;
		public static $so;

		public function __construct( int $id, int $location_id, $title, int $created_by )
		{
			$this->id = (int)$id;
			$this->location_id = (int)$location_id;
			$this->title = $title;
			$this->success = false;
			$this->created_by = (int)$created_by;
			$this->has_generated_export = false;
			$this->deleted = false;
			$this->billing_info = array();
		}

		public function get_id()
		{
			return $this->id;
		}

		public function set_id( int $id )
		{
			$this->id = (int)$id;
		}

		public function get_contract_type_title()
		{
			return $this->contract_type_title;
		}

		public function set_contract_type_title( $contract_type_title )
		{
			$this->contract_type_title = $contract_type_title;
		}

		public function get_billing_term()
		{
			return $this->billing_term;
		}

		public function set_total_sum( float $total_sum )
		{
			$this->total_sum = (float)$total_sum;
		}

		public function get_location_id()
		{
			return $this->location_id;
		}

		public function get_year()
		{
			return $this->year;
		}

		public function set_year( $year )
		{
			$this->year = $year;
		}

		public function get_month()
		{
			return $this->month;
		}

		public function set_month( $month )
		{
			$this->month = $month;
		}

		public function get_total_sum()
		{
			return $this->total_sum;
		}

		public function set_timestamp_start( int $timestamp_start )
		{
			$this->timestamp_start = (int)$timestamp_start;
		}

		public function get_timestamp_start()
		{
			return $this->timestamp_start;
		}

		public function set_timestamp_stop( int $timestamp_stop )
		{
			$this->timestamp_stop = (int)$timestamp_stop;
		}

		public function get_timestamp_stop()
		{
			return $this->timestamp_stop;
		}

		public function set_success( $success )
		{
			$this->success = (bool)$success;
		}

		public function set_timestamp_commit( $timestamp_commit )
		{
			$this->timestamp_commit = $timestamp_commit;
		}

		public function get_timestamp_commit()
		{
			return $this->timestamp_commit;
		}

		/**
		 * Convenience method for checking if a billing job has been commited or
		 * not. Checks if the timestamp for commit has been set.
		 *
		 * @return bool true if job has been commited, false if not.
		 */
		public function is_commited()
		{
			return $this->timestamp_commit != null && $this->timestamp_commit != '';
		}

		public function is_success()
		{
			return $this->success;
		}

		public function set_created_by( int $created_by )
		{
			$this->created_by = (int)$created_by;
		}

		public function set_deleted( bool $deleted )
		{
			$this->deleted = (bool)$deleted;
		}

		public function is_deleted()
		{
			return $this->deleted;
		}

		public function get_created_by()
		{
			return $this->created_by;
		}

		public function has_generated_export()
		{
			return $this->has_generated_export;
		}

		public function set_generated_export( bool $has_generated_export )
		{
			$this->has_generated_export = (bool)$has_generated_export;
		}

		public function set_export_format( $export_format )
		{
			$this->export_format = $export_format;
		}

		public function get_export_format()
		{
			return $this->export_format;
		}

		public function get_responsibility_title()
		{
			return $this->responsibility_title;
		}

		public function set_responsibility_title( $responsibility_title )
		{
			$this->responsibility_title = $responsibility_title;
		}

		public function serialize()
		{
			$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$location_id = $this->get_location_id();
			$account = $GLOBALS['phpgw']->accounts->get($this->get_created_by());
			$timestamp_commit = '';
			if ($this->get_timestamp_commit() != null && $this->get_timestamp_commit())
			{
				$timestamp_commit = $GLOBALS['phpgw']->common->show_date($this->get_timestamp_commit(), $date_format . ' H:i:s');
				//$timestamp_commit = date($date_format . ' H:i:s', $this->get_timestamp_commit());
			}
			$billing_info_content = array();
			foreach ($this->get_billing_info() as $bi)
			{
				$term = $bi->get_term_id();
				$term_label = "";
				$month = $bi->get_month();
				$year = $bi->get_year();
				if ($term == 1)
				{
					$term_label = lang('month ' . $bi->get_month() . ' capitalized');
				}
				else
				{
					$term_label = $bi->get_term_label();
				}
				$billing_info_content[] = $term_label . " " . $year;
			}
			$billing_info_labels = join('<br/>', $billing_info_content);
			return array(
				'id' => $this->get_id(),
				'description' => $this->get_title(),
				'responsibility_title' => $this->get_responsibility_title(),
				'billing_info' => $billing_info_labels,
				'total_sum' => $this->get_total_sum(),
				//'timestamp_stop'	=> date($date_format . ' H:i:s', $this->get_timestamp_stop()),
				'timestamp_stop' => $GLOBALS['phpgw']->common->show_date($this->get_timestamp_stop(), $date_format . ' H:i:s'),
				'timestamp_commit' => $timestamp_commit,
				'created_by' => "{$account->firstname} {$account->lastname}",
				'contract_type_title' => $this->get_contract_type_title()
			);
		}

		public function get_title()
		{
			return $this->title;
		}

		public function set_title( $title )
		{
			$this->title = $title;
		}

		public function get_billing_info()
		{
			return $this->billing_info;
		}

		public function add_billing_info( rental_billing_info $new_billing_info )
		{
			$new_billing_info_id = $new_billing_info->get_id();

			if (!in_array($new_billing_info_id, $this->billing_info))
			{
				$this->billing_info[$new_billing_info_id] = $new_billing_info;
			}
		}

		public function set_billing_info( $billing_info )
		{
			$this->billing_info = $billing_info;
		}
	}