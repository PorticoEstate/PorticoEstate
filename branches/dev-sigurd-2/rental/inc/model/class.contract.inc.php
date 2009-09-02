<?php

	include_class('rental', 'contract_date', 'inc/model/');
	include_class('rental', 'model', 'inc/model/');

	class rental_contract extends rental_model
	{
		const SECURITY_TYPE_BANK_GUARANTEE = 0;
		const SECURITY_TYPE_DEPOSIT = 1;
		const SECURITY_TYPE_ADVANCE = 2;
		const SECURITY_TYPE_OTHER_GUARANTEE = 3;
		
		public static $so;
		public static $types;
		
		protected $id;
		protected $parties;
		protected $contract_date;
		protected $billing_start_date;
		protected $type_id;
		protected $term_id;
		protected $term_id_title;
		protected $security_type;
		protected $security_amount;
		protected $billing_unit;
		protected $old_contract_id;
		protected $contract_type_title;
		protected $party_names = array();
		protected $composite_names = array();
		protected $composites;
		protected $payer_id;
		protected $price_items;	
		protected $last_edited_by_current_user;
		protected $executive_officer_id;
		
		/**
		 * Constructor.  Takes an optional ID.  If a contract is created from outside
		 * the database the ID should be empty so the database can add one according to its logic.
		 * 
		 * @param int $id the id of this composite
		 */
		public function __construct(int $id = null)
		{
			$this->id = (int)$id;
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
		
		public function set_type_id($type_id)
		{
			$this->type_id = (int) $type_id;
		}
		
		public function get_type_id() { return $this->type_id; }
		
		public function set_term_id(int $term_id)
		{
			$this->term_id = (int)$term_id;
		}
		
		public function get_term_id() { return $this->term_id; }
		
		public function set_term_id_title($term_id_title)
		{
			$this->term_id_title = $term_id_title;
		}

		public function get_term_id_title(){ return $this->term_id_title; }
		
		
		public function set_billing_unit($billing_unit)
		{
			$this->billing_unit = $billing_unit;
		}

		public function get_billing_unit() { return $this->billing_unit; }
		
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
		 * Get the name of the contract type @see get_type_id()
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
			foreach($this->party_names as $party) {
				$names .= $party."<br/>";
			}
			return $names;
		}
		
		public function set_party_name($name)
		{
			if(!in_array($name,$this->party_names)) {
				$this->party_names[] = $name;
			}
		}
		
		public function get_composite_name(){
			$names = '';
			foreach($this->composite_names as $composite) {
				$names .= $composite."<br/>\n";
			}
			return $names;
		}
		
		public function set_composite_name($name)
		{
			if(!in_array($name,$this->composite_names))
				$this->composite_names[] = $name;
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
		 * Get a list of the composites associated with this contract.  The composites are loaded
		 * lazily, so they will not be populated at object construction, but rather at first call
		 * of this function.
		 * 
		 * @return rental_composite[]
		 */
		public function get_composites()
		{
			if (!$this->composites) {
				// The list of composites are empty, so try to get them from the database
				$so = self::get_so();
				$this->composites = $so->get_composites_for_contract($this->get_id());
			}
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
			if(!$this->parties) {
				$so = self::get_so();
				$this->parties = $so->get_parties_for_contract($this->get_id());
			}
			
			return $this->parties;
		}
		
		public function set_price_items($price_items)
		{
			$this->price_items = $price_items;
		}
		
		/**
		 * Get a list of the price items associated with this contract.  The price items
		 * are loaded lazily, so they will not be populated at object construction, but rather
		 * at first call of this function.
		 * 
		 * @return rental_price_item[]
		 */
		public function get_price_items()
		{
			if(!$this->price_items) {
				$so = self::get_so();
				$this->price_items = $so->get_price_items_for_contract($this->get_id());
			}
			
			return $this->price_items;
		}
		
		
		/**
		 * Add a composite to this contract. This function checks for duplicates
		 * before adding the given composite.
		 * 
		 * @param $new_composite
		 */
		public function add_composite(rental_composite $new_composite)
		{
			$already_has_composite = false;
			
			foreach ($this->get_composites() as $composite) {
				if ($composite->get_id() == $new_composite->get_id()) {
					$already_has_composite = true;
				}
			}
			
			if (!$already_has_composite) {
				$so = self::get_so();
				$so->add_composite($this->get_id(),$new_composite->get_id());
				$composites = $this->get_composites();
				$composites[] = $new_composite;
				$this->set_composites($composites);
			}
		}
		
		/**
		 * Add a party to this contract. This function checks for duplicates
		 * before adding the party. 
		 * 
		 * @param rental_party $new_party the new party
		 */
		public function add_party(rental_party $new_party)
		{
			$already_has_party = false;
			
			foreach ($this->get_parties() as $party) {
				if ($party->get_id() == $new_party->get_id()) {
					$already_has_party = true;
				}
			}
			
			if (!$already_has_party) {
				$so = self::get_so();
				$so->add_party($this->get_id(),$new_party->get_id());
				$parties = $this->get_parties();
				$parties[] = $new_party;
				$this->set_parties($parties);
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
		
		/**
		 * Get the price of this contract at the given date.  If no date is provided, the current
		 * date is used.
		 * 
		 * @param $date the date to check the contract value at
		 * @return the price
		 */
		public function get_price($date = null)
		{
			if ($date == null) {
				$date = time();
			}
			
			$total = 0;
			
			foreach ($this->get_price_items() as $price_item) {
				if ($price_item->is_active_at($date)) {
					$total += $price_item->get_total_price();
				}
			}
			
			return $total;
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
		 * Get the contract stored in the database with the given id
		 * 
		 * @param $id id of the contract to get
		 * @return rental_contract
		 */
		public static function get($id)
		{
			$so = self::get_so();
			return $so->get_single($id);
		}
			
		/**
		 * Get a key/value array of titles of billing term types keyed by their id
		 * 
		 * @return array
		 */
		public static function get_billing_terms()
		{
			$so = self::get_so();
			return $so->get_billing_terms($id);
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
		
		public static function get_last_edited_by()
		{
			$so = self::get_so();
			$contracts = $so->get_last_edited_by();
			return $contracts;
		}
		
		/**
		 * Get a list of the available contract types.
		 * 
		 * @return array key/value array of id mapped to contract type title 
		 */
		public static function get_contract_types(){
			$so = self::get_so();
			$types = $so->get_contract_types();
			return $types;
		}
		
		public function serialize()
		{
			$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			return array(
				'id' => $this->get_id(),
				'date_start' => $this->get_contract_date() && $this->get_contract_date()->has_start_date() ? date($date_format, $this->get_contract_date()->get_start_date()): '',
				'date_end' => $this->get_contract_date() && $this->get_contract_date()->has_end_date() ? date($date_format, $this->get_contract_date()->get_end_date()): '',
				'type'	=> lang($this->get_contract_type_title()),
				'composite' => $this->get_composite_name(),
				'party' => $this->get_party_name(),
				'old_contract_id' => $this->get_old_contract_id(),
				'last_edited_by_current_user' => $this->get_last_edited_by_current_user() ? date($date_format, $this->get_last_edited_by_current_user()): ''
			);
		}
		
		public static function export(string $name, bool $return)
		{
			
		}
		
	}

?>