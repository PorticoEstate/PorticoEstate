<?php

	include_class('rental', 'contract_date', 'inc/model/');

	class rental_contract
	{
		public static $so;
		
		protected $id;
		protected $tenant;
		protected $contract_date;
		protected $billing_start_date;
		protected $type_id;
		protected $term_id;
		protected $account;
		protected $contract_type_title;
		
		public function __construct(int $id)
		{
			$this->id = $id;
		}
		
		public function set_id($id)
		{
			$this->id = $id;
		}
		
		public function get_id() { return $this->id; }
		
		public function set_tenants($tenants)
		{
			$this->tenant = $tenants;
		}
		
		public function get_tenants() { return $this->tenants; }
		
		public function set_contract_date($date)
		{
			$this->contract_date = $date;
		}
		
		public function get_contract_date() { return $this->contract_date; }
		
		public function set_billing_start_date($date)
		{
			$this->billing_start_date = $date;
		}
		
		public function get_billing_start_date() { return $this->billing_start_date; }
		
		public function set_type_id($type_id)
		{
			$this->type_id = $type_id;
		}
		
		public function get_type_id() { return $this->type_id; }
		
		public function set_term_id($term_id)
		{
			$this->term_id = $term_id;
		}
		
		public function get_term_id() { return $this->term_id; }
		
		public function set_account($account)
		{
			$this->account = $account;
		}
		
		public function get_account() { return $this->account; }
		
		public function get_contract_type_title(){
			return $this->contract_type_title;
		}
		
		public function set_contract_type_title($title)
		{
			$this->contract_type_title = $title;
		}
		
		/**
		 * Get a static reference to the storage object associated with this model object
		 * 
		 * @return the storage object
		 */
		public static function get_so()
		{
			if (self::$so == null) {
				self::$so = CreateObject('rental.socontract');
			}
			
			return self::$so;
		}
		
		/**
		 * Return a list of all contracts registered on the given rental_composite
		 * 
		 * @param $composite_id	which composite to return contracts for
		 * @param $start		which index to start the list at
		 * @param $results	how many results to return
		 * @param $sort			sort column
		 * @param $dir			sort direction
		 * @param $query
		 * @param $search_option
		 * @param $filters
		 * @return a list of rental_contract objects
		 */
		public static function get_contracts_for_composite($composite_id, $sort = null, $dir = '', $start = 0, $results = 1000, $status = null, $date = null)
		{
			$so = self::get_so();
			return $so->get_contracts($composite_id, $sort = null, $dir = '', $start = 0, $results = 1000, $status = null, $date = null);
		}
		
		
		public static function get_all($start = 0, $results = 1000, $sort = null, $dir = '', $query = null, $search_option = null, $filters = array())
		{
			$so = self::get_so();
			$contracts = $so->get_contract_array($start, $results, $sort, $dir, $query, $search_option, $filters);
			return $contracts;
		}
		
		public static function get_contract_types(){
			$so = self::get_so();
			$contract_types = $so->get_contract_types();
			return $contract_types;
		}
		
		
	}

?>