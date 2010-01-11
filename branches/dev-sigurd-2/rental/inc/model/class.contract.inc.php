<?php

	include_class('rental', 'model', 'inc/model/');
	include_class('rental', 'contract_date', 'inc/model/');
	include_class('rental', 'invoice', 'inc/model/');

	class rental_contract extends rental_model
	{
		const SECURITY_TYPE_BANK_GUARANTEE = 1;
		const SECURITY_TYPE_DEPOSIT = 2;
		const SECURITY_TYPE_ADVANCE = 3;
		const SECURITY_TYPE_OTHER_GUARANTEE = 4;
		
		public static $so;
		public static $types;
		
		protected $id;
		protected $parties;
		protected $contract_date;
		protected $billing_start_date;
		protected $location_id;
		protected $term_id;
		protected $term_id_title;
		protected $security_type;
		protected $security_amount;
		protected $old_contract_id;
		protected $contract_type_title;
		protected $composites;
		protected $payer_id;
		protected $last_edited_by_current_user;
		protected $executive_officer_id;
		protected $comment;
		protected $last_updated;
		protected $bill_timestamps; // Keeps the bill timestamps for the contract - not a db property on the contract
		protected $service_id;
		protected $responsibility_id;
		protected $reference;
		protected $invoice_header;
		protected $account_in;
		protected $account_out;
		protected $project_id;
		protected $due_date;
		protected $contract_type_id;
		protected $total_price;
		protected $max_area;
		protected $notify_before;
		protected $notify_before_due_date;
		protected $notify_after_termination_date;
		protected $rented_area;
		
		/**
		 * Constructor.  Takes an optional ID.  If a contract is created from outside
		 * the database the ID should be empty so the database can add one according to its logic.
		 * 
		 * @param int $id the id of this composite
		 */
		public function __construct(int $id = null)
		{
			$this->id = (int)$id;
			$this->parties = array();
			$this->composites = array();
			$this->bill_timestamps = array(); // Consider to have all invoices here if other data than billing timetamps are needed 
		}
		
		public function set_id($id)
		{
			$this->id = $id;
		}
		
		public function get_id() { return $this->id; }
		
		public function set_payer($id)
		{
			$so = self::get_so();
			$so->set_payer($this->get_id(),$id);
		}
		
		public function set_payer_id($id){
			$this->payer_id = $id;
		}
		
		public function set_old_contract_id($id){
			$this->old_contract_id = $id;
		}
		
		public function get_old_contract_id(){
			return $this->old_contract_id;
		}
		
		public function get_payer_id() { return $this->payer_id; }
		
		public function set_parties($parties)
		{
			$this->parties = $parties;
		}
		
		public function set_contract_date($date)
		{
			$this->contract_date = $date;
		}
		
		public function get_contract_date() {
			return $this->contract_date;
		}
		
		public function set_billing_start_date($date)
		{
			$this->billing_start_date = $date;
		}
		
		public function get_executive_officer_id() {
			return $this->executive_officer_id;
		}
		
		public function set_executive_officer_id($id)
		{
			$this->executive_officer_id = $id;
		}
		
		/**
		 * Returns date of when the first invoice should be produced for the
		 * contract.
		 * @return string with UNIX time.
		 */
		public function get_billing_start_date() { return $this->billing_start_date; }
		
		public function set_location_id($location_id)
		{
			$this->field_of_responsibility_id = (int) $location_id;
			$this->location_id = (int) $location_id;
		}
		
		public function set_project_id($project_id)
		{
			$this->project_id = $project_id;
		}
	
		public function get_project_id(){ return $this->project_id; }
		
		public function get_location_id() { return $this->location_id; }
		
		public function set_service_id($service_id)
		{
			$this->service_id = $service_id;
		}
		
		public function get_service_id() { return $this->service_id; }
		
		public function set_responsibility_id($responsibility_id)
		{
			$this->responsibility_id = $responsibility_id;
		}
		
		public function get_responsibility_id() { return $this->responsibility_id; }
		
		public function set_term_id(int $term_id)
		{
			$this->term_id = (int)$term_id;
		}
		
		public function get_term_id() { return $this->term_id; }
		
		public function get_account_in() { return $this->account_in; }
		
		public function set_account_in($account_in)
		{
			$this->account_in = $account_in;
		}
		
		public function get_account_out() { return $this->account_out; }
		
		public function set_account_out($account_out)
		{
			$this->account_out = $account_out;
		}
		
		public function get_reference() { return $this->reference; }
		
		public function set_reference($reference)
		{
			$this->reference = $reference;
		}
		
		public function get_invoice_header() { return $this->invoice_header; }
		
		public function set_invoice_header($invoice_header)
		{
			$this->invoice_header = $invoice_header;
		}
		
		
		
		public function set_term_id_title($term_id_title)
		{
			$this->term_id_title = $term_id_title;
		}

		public function get_term_id_title(){ return $this->term_id_title; }
		
		public function set_security_type(int $security_type = null)
		{
			switch($security_type)
			{
				case rental_contract::SECURITY_TYPE_DEPOSIT:
					$this->security_type = rental_contract::SECURITY_TYPE_DEPOSIT;
					break;
				case rental_contract::SECURITY_TYPE_ADVANCE:
					$this->security_type = rental_contract::SECURITY_TYPE_ADVANCE;
					break;
				case rental_contract::SECURITY_TYPE_OTHER_GUARANTEE:
					$this->security_type = rental_contract::SECURITY_TYPE_OTHER_GUARANTEE;
					break;
				case rental_contract::SECURITY_TYPE_BANK_GUARANTEE:
					$this->security_type = rental_contract::SECURITY_TYPE_BANK_GUARANTEE;
					break;
				default:
					$this->security_type = -1;
					break;
			}
		}
		
		public function get_security_type() { return $this->security_type; }
		
		public function set_security_amount($security_amount)
		{
			$this->security_amount = $security_amount;
		}
		
		public function get_security_amount() { return $this->security_amount; }
		
		/**
		 * Get the name of the contract type @see get_location_id()
		 * 
		 * @return string
		 */
		public function get_contract_type_title()
		{
			return $this->contract_type_title;
		}
		
		public function set_contract_type_title($title)
		{
			$this->contract_type_title = $title;
		}
		
		public function get_party_name(){
			$names = '';
			foreach($this->parties as $party) {
				$names .= $party->get_name()."<br/>";
			}
			return $names;
		}
		
		public function get_party_name_as_list(){
			$names = '';
			$tot_parties = count($this->parties);
			$count_parties = 0;
			foreach($this->parties as $party) {
				$count_parties++;
				$names .= $party->get_name();
				if($count_parties < $tot_parties){
					$names .= ", ";
				}
			}
			return $names;
		}
		
		public function get_composite_name(){
			$names = '';
			foreach($this->composites as $composite) {
				$names .= $composite->get_name()."<br/>";
			}
			return $names;
		}
		
		public function get_composite_name_as_list(){
			$names = '';
			$tot_composites = count($this->composites);
			$count_composites = 0;
			foreach($this->composites as $composite) {
				$count_composites++;
				$names .= $composite->get_name();
				if($count_composites < $tot_composites){
					$names .= ", ";
				}
			}
			return $names;
		}
		
		public function set_composites($composites)
		{
			$this->composites = $composites;
		}
		
		public function set_last_edited_by_current_user($date)
		{
			$this->last_edited_by_current_user = $date;
		}
		
		public function get_last_edited_by_current_user() { return $this->last_edited_by_current_user;}
		
		/**
		 * Get a list of the composites associated with this contract.
		 * 
		 * @return array with rental_composite objects, empty array if none, never null.
		 */
		public function get_composites()
		{
			return $this->composites;
		}
		
		/**
		 * Get a list of the composites associated with this contract.  The composites are loaded
		 * lazily, so they will not be populated at object construction, but rather at first call
		 * of this function.
		 * 
		 * @return rental_composite[]
		 */
		public function get_available_composites()
		{
			$so = self::get_so();
			return $so->get_available_composites_for_contract($this->get_id());
		}
		
		/**
		 * Get a list of the parties associated with this contract.  The parties are loaded
		 * lazily, so they will not be populated at object construction, but rather at first call
		 * of this function.
		 * 
		 * @return rental_party[]
		 */
		public function get_parties()
		{
			return $this->parties;
		}
		

        public function set_comment($comment)
        {
            $this->comment = $comment;
        }
        
        /**
         * Get the timestamp for the last update on this contract
         * 
         * @return int timestamp for last update
         */
        public function get_last_updated()
        {
            return $this->last_updated;
        }
        
        /**
         * Set the timestamp for the last update on this contract
         * 
         * @param $timestamp last update
         */
		public function set_last_updated($timestamp)
        {
            $this->last_updated = $timestamp;
        }
        
        /**
         * Get comment associated with this contract.
         * 
         * @return string comment
         */
        public function get_comment()
        {
            return $this->comment;
        }
		
		/**
		 * Add a composite to this contract. This method does not check if
		 * object is already added and does not do any db handling.
		 * 
		 * @param $new_composite
		 */
		public function add_composite(rental_composite $new_composite)
		{
			$new_composite_id = $new_composite->get_id();
			if(!in_array($new_composite_id,$this->composites))
			{
				$this->composites[$new_composite_id] = $new_composite;
			}
		}
		
		/**
		 * Add a party to this contract. This method does not check if
		 * object is already added and does not do any db handling.
		 * 
		 * @param rental_party $new_party the new party
		 */
		public function add_party(rental_party $new_party)
		{
			$new_party_id = $new_party->get_id();
			
			if(!in_array($new_party_id,$this->parties))
			{
				$this->parties[$new_party_id] = $new_party;
			}
		}
		
		/**
		 * Add a price_item to this contract. This function does not check for duplicates
		 * before adding the price_item because multiple instances of the same price items are allowed.
		 * 
		 * @param $new_price_item
		 */
		public function add_price_item(rental_price_item $new_price_item)
		{
			$so = self::get_so();
			$so->add_price_item($this->get_id(), $new_price_item);
			$price_items = $this->get_price_items();
			$price_items[] = $new_price_item;
			$this->set_price_items($price_items);
		}
		
		public function remove_party(rental_party $party_to_remove)
		{
			unset($this->parties[$party_to_remove]);
			$so = self::get_so();
			$so->remove_party($this->get_id(),$party_to_remove->get_id());
		}
		
		
		public function remove_composite(rental_composite $composite_to_remove)
		{
			unset($this->composites[$composite_to_remove]);
			$so = self::get_so();
			$so->remove_composite($this->get_id(),$composite_to_remove->get_id());
		}
		
		public function remove_price_item(rental_contract_price_item $price_item_to_remove)
		{
			// TODO: Does this work?
			unset($this->price_items[$price_item_to_remove]);
			$so = self::get_so();
			$so->remove_price_item($this->get_id(),$price_item_to_remove);
		}
		
		
		public function add_bill_timestamp(int $timestamp)
		{
			if(!in_array($timestamp, $this->bill_timestamps)) // New timestamnp
			{
				$this->bill_timestamps[] = (int)$timestamp;
			}
		}
		
		/**
		 * Helper method to return the end date of the last invoice. The timestamp
		 * parameter is optional, but when used the date returned will be the
		 * end date of the last invoice before or at that time.
		 *  
		 * @param $timestamp int with UNIX timestamp.
		 * @return int with UNIX timestamp with the end date of the invoice, or
		 * null if no such invoice was found.
		 */
		public function get_last_invoice_timestamp(int $timestamp = null)
		{
			if(count($this->bill_timestamps) > 0) // The contract has been billed before
			{
				sort($this->bill_timestamps); // First we sort the timestamps..
				$this->bill_timestamps = array_reverse($this->bill_timestamps); // ..then we reverse them to make the last biling come first
				if($timestamp == null) // No timestamp specified
				{
					// We can just use the first invoice
					$keys = array_keys($this->bill_timestamps);
					return $this->bill_timestamps[$keys[0]]->get_timestamp_end();
				}
				foreach ($this->bill_timestamps as $bill_timestamp) // Runs through all invoices
				{
					if($bill_timestamp <= $timestamp)
					{
						return $bill_timestamp;
					}
				}
			}
			return null; // No matching invoices found
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
		
		public function get_last_edited_by()
		{
			$so = self::get_so();
			$contracts = $so->get_last_edited_by($this->get_id());
			return $GLOBALS['phpgw']->accounts->id2name($contracts);
		}
		
		/** 
		 * Returns the range of year there are contracts. That is, the array 
		 * returned contains reversed chronologically all the years from the earliest start 
		 * year of the contracts to next year.  
		 *  
		 * @return array of string values, never null. 
		 */ 
		public static function get_year_range()
		{
			return self::get_so()->get_year_range();
		}
		
		
		public function serialize()
		{
			$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			if(!isset($this->total_price))
			{
				$this->total_price =  rental_socontract_price_item::get_instance()->get_total_price($this->get_id());
			}
			return array(
				'id' => $this->get_id(),
				'date_start' => $this->get_contract_date() && $this->get_contract_date()->has_start_date() ? date($date_format, $this->get_contract_date()->get_start_date()): '',
				'date_end' => $this->get_contract_date() && $this->get_contract_date()->has_end_date() ? date($date_format, $this->get_contract_date()->get_end_date()): '',
				'type'	=> lang($this->get_contract_type_title()),
				'composite' => $this->get_composite_name(),
				'party' => $this->get_party_name(),
				'old_contract_id' => $this->get_old_contract_id(),
				//'last_edited_by_current_user' => $this->get_last_edited_by_current_user() ? date($date_format.' h:i:s A', $this->get_last_edited_by_current_user()): '',
				'last_edited_by_current_user' => $this->get_last_edited_by_current_user() ? date($date_format, $this->get_last_edited_by_current_user()): '',
				'payer_id' => $this->get_payer_id(),
				//'last_updated' => $this->get_last_updated() ? date($date_format.' h:i:s A', $this->get_last_updated()) : '',
				'last_updated' => $this->get_last_updated() ? date($date_format, $this->get_last_updated()) : '',
				'service_id' => $this->get_service_id(),
				'responsibility_id' => $this->get_responsibility_id(),
				'due_date' => $this->get_due_date() ? date($date_format, $this->get_due_date()): '',
				'contract_type_id' => $this->get_contract_type_id(),
				'total_price' => $this->total_price,
				//'max_area' => rental_socontract_price_item::get_instance()->get_max_area($this->get_id()),
				'max_area' =>	$this->rented_area,
				'contract_status' => $this->get_contract_status(),
				'contract_notification_status' => $this->get_contract_notification_status(),
				'rented_area' => $this->get_rented_area()
			);
		}
		
		public static function export(string $name, bool $return)
		{
			
		}
		
	
		public function set_due_date($due_date)
		{
			$this->due_date = $due_date;
		}
	
		public function get_due_date()
		{
			return $this->due_date;
		}
		
		public function set_total_price($total_price)
		{
			$this->total_price = $total_price;
		}
	
		public function get_total_price()
		{
			return $this->total_price;
		}
		
		public function set_max_area($max_area)
		{
			$this->max_area = $max_area;
		}
	
		public function get_max_area()
		{
			return $this->max_area;
		}
		
		public function set_contract_type_id($contract_type_id)
		{
			$this->contract_type_id = $contract_type_id;
		}
	
		public function get_contract_type_id()
		{
			return $this->contract_type_id;
		}
		
		public function set_notify_before($notify_before)
		{
			$this->notify_before = $notify_before;
		}
	
		public function get_notify_before()
		{
			return $this->notify_before;
		}
		
		public function set_notify_before_due_date($notify_before_due_date)
		{
			$this->notify_before_due_date = $notify_before_due_date;
		}
	
		public function get_notify_before_due_date()
		{
			return $this->notify_before_due_date;
		}

		public function set_notify_after_termination_date($notify_after_termination_date)
		{
			$this->notify_after_termination_date = $notify_after_termination_date;
		}
	
		public function get_notify_after_termination_date()
		{
			return $this->notify_after_termination_date;
		}
		
		public function get_contract_notification_status()
		{
			$ts = strtotime(date('Y-m-d')); // timestamp for today
			$ts_notify_before = $this->notify_before * 60 * 60 * 24;
			$ts_notify_before_due_date = $this->notify_before_due_date * 60 * 60 * 24;
			$ts_notify_after_termination_date = $this->notify_after_termination_date * 60 * 60 * 24;
			$date_start = $this->get_contract_date()->get_start_date();
			$date_end = $this->get_contract_date()->get_end_date();
			
			$status = array();
			
			if(isset($date_end) && ($date_end >= $ts) && ($ts >= ($date_end - $ts_notify_before)))	
			{
				// If contract has end date which is in the future and notification date is today or in the past
				$status[] = lang("under_dismissal");	// CONTRACT UNDER DISMISSAL
			}
			
			if(isset($date_end) && ($date_end < $ts) && ($ts < ($date_end + $ts_notify_after_termination_date))) 
			{
				// If the contract has end date shich is in the past and the end date is within a given time ago 
				$status[] =  lang("terminated_contract");	// CONTRACT UNDER TERMINATION
			}
			
			if(isset($this->due_date) && ($this->due_date >= $ts) && ($ts >= ($this->due_date - $ts_notify_before_due_date)))
			{
				// If the contract has a due date which is in the future and the due date is today or within a given time in the future
				$status[] = lang("closing_due_date");	// CLOSING DUE DATE
			}
			
			if(count($status) > 0)
			{
				return implode("<br/>",$status);
			}
			else
			{
				return '';
			}
		}
	
		public function get_contract_status()
		{
			$ts = strtotime(date('Y-m-d')); // timestamp for today
			
			$date_start = $this->get_contract_date()->get_start_date();
			$date_end = $this->get_contract_date()->get_end_date();
			
			if(isset($date_start) && ($ts < $date_start || $date_start == ''))								
			{
				// If contract has start date AND (today is before start date OR empty start date)
				return lang("under_planning");		// CONTRACT UNDER PLANNING
			}
			
			else if(isset($date_start) && $ts >= $date_start && (!isset($date_end) || $ts <= $date_end))								
			{	
				// else ... if contract has start date AND start date is today or in the past AND 
				// (contract has end date OR end date is in the future)	
				return lang("active_single");		// ACTIVE CONTRACT											
			}
			else if(isset($date_end) && $ts > $date_end)
			{
				//else ... if contract has end date AND end date is in the past
				return  lang("ended"); 				// ENDED CONTRACT
			}
			else
			{
				//else we do not know the contract status
				return lang("status_unknown");		// UNKNOWN STATUS
			}
		}
		
		public function set_rented_area($rented_area)
		{
			$this->rented_area = $rented_area;
		}
		
		public function get_rented_area() { return $this->rented_area; }

		/**
		 * (non-PHPdoc)
		 * @see rental/inc/model/rental_model#validates()
		 */
		public function validates(){
			
			// If the contract has number as identifier, it must be greater than 1
			/*$id = $this->get_id();
			if(is_numeric($id) && $id < 1){
				return false;
			}*/
			
			// The contract must be designated a responsibility area
			if($this->get_location_id() == null || $this->get_location_id() < 1){
				return false;
			}
			return true;
		}
		
		/**
		 * (non-PHPdoc)
		 * @see rental/inc/model/rental_model#validate_numeric()
		 */
		public function validate_numeric(){
			$valid_numeric = true;
			if($this->get_service_id() != null && !(strlen($this->get_service_id()) == 5)){
				$this->set_validation_error('service_id', lang('Service id must be 5 characters.'));
				$valid_numeric = false;
			}
			else if($this->get_service_id() != null && !is_numeric($this->get_service_id())){
				$this->set_validation_error('service_id', lang('service_id_not_numeric'));
				$valid_numeric = false;
			}
			if($this->get_responsibility_id() != null && !(strlen($this->get_responsibility_id()) == 6)){
				$this->set_validation_error('responsibility_id', lang('Responsibility id must be 6 characters.'));
				$valid_numeric = false;
			}
			else if($this->get_responsibility_id() != null && !is_numeric($this->get_responsibility_id())){
				$this->set_validation_error('responsibility_id', lang('responsibility_id_not_numeric'));
				$valid_numeric = false;
			}
			if($this->get_account_in() != null && !is_numeric($this->get_account_in())){
				$this->set_validation_error('account_in', lang('account_in_not_numeric'));
				$valid_numeric = false;
			}
			if($this->get_account_out() != null && !is_numeric($this->get_account_out())){
				$this->set_validation_error('account_out', lang('account_out_not_numeric'));
				$valid_numeric = false;
			}
			if($this->get_security_amount() != null && !is_numeric($this->get_security_amount())){
				$this->set_validation_error('security_amount', lang('security_amount_not_numeric'));
				$valid_numeric = false;
			}
			if(!is_numeric($this->get_rented_area())){
				$this->set_validation_error('rented_area', lang('rented_area_not_numeric'));
				$valid_numeric = false;
			}
			return $valid_numeric;
		}
		
		/**
		 * (non-PHPdoc)
		 * @see rental/inc/model/rental_model#check_consistency()
		 */
		public function check_consistency(){
			// Retrieve the start and end date
			$dates = $this->get_contract_date();
			if(isset($dates))
			{
				$start_date = $this->get_contract_date()->get_start_date();
				$end_date = $this->get_contract_date()->get_end_date();
			}
			
			// Give warning if the contract lacks a start date
			if(!isset($start_date))
			{
				$this->set_consistency_warning(lang('warning_lacking_start_date'));
			}
			else
			{
				
				// If set, the billing date must be between the contract's start date and end date
				$billing_start = $this->get_billing_start_date();
				if(isset($billing_start) && is_numeric($billing_start) && $billing_start > 0)
				{
					if($billing_start < $start_date || (isset($end_date) && $billing_start > $end_date)){
						$this->set_consistency_warning(lang('warning_billing_date_between'));
					} 
				}
				
				// If set, the due date must be between the contract's start date and end date
				$due_date = $this->get_due_date();
				if(isset($due_date) && is_numeric($due_date) && $due_date > 0)
				{
					if($due_date < $start_date || (isset($end_date) && $due_date > $end_date)){
						$this->set_consistency_warning(lang('warning_due_date_between'));
					} 
				}
			}
		}
		
	}

?>