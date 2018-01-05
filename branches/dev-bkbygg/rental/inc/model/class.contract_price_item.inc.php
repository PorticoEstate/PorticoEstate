<?php
	include_class('rental', 'price_item', 'inc/model/');

	/**
	 * Class that represents a price item in the price list
	 *
	 */
	class rental_contract_price_item extends rental_price_item
	{

		public static $so;
		protected $price_item_id;
		protected $contract_id;
		protected $area;
		protected $count;
		protected $total_price;
		protected $date_start;
		protected $date_end;
		protected $is_one_time;
		protected $is_billed;
		protected $location_factor;
		protected $standard_factor;
		protected $custom_factor;
		protected $price_type_id;
		protected $billing_id;

		/**
		 * Constructor.  Takes an optional ID.  If a price item is created from outside
		 * the database the ID should be empty so the database can add one according to its logic.
		 * 
		 * @param int $id the id of this price item
		 */
		public function __construct( $id = 0 )
		{
			parent::__construct((int)$id);
			/*
			  if ($id) {
			  parent::__construct($price_item->get_id());
			  $this->set_title($price_item->get_title());
			  $this->set_agresso_id($price_item->get_agresso_id());
			  $this->set_is_area($price_item->is_area());
			  $this->set_price($price_item->get_price);
			  } else {
			  parent::__construct();
			  }
			 */
		}

		public function get_billing_id()
		{
			return $this->billing_id;
		}

		public function set_billing_id( $id )
		{
			$this->billing_id = $id;
		}
		public function get_price_item_id()
		{
			return $this->price_item_id;
		}

		public function set_price_item_id( $id )
		{
			$this->price_item_id = $id;
		}

		public function get_contract_id()
		{
			return $this->contract_id;
		}

		public function set_contract_id( $contract_id )
		{
			$this->contract_id = $contract_id;
		}

		public function get_area()
		{
			if (!$this->area)
				$this->area = 0;

			return $this->area;
		}

		public function set_area( $area )
		{
			$this->area = $area;
		}

		public function get_count()
		{
			if (!$this->count)
				$this->count = 0;

			return $this->count;
		}

		public function set_count( $count )
		{
			$this->count = $count;
		}

		public function get_total_price()
		{
			if (!$this->total_price)
				$this->total_price = 0;
			return $this->total_price;
		}

		public function set_total_price( $total_price )
		{
			$this->total_price = $total_price;
		}

		public function get_date_start()
		{
			return $this->date_start;
		}

		public function set_date_start( $date_start )
		{
			$this->date_start = $date_start;
		}

		public function get_date_end()
		{
			return $this->date_end;
		}

		public function set_date_end( $date_end )
		{
			$this->date_end = $date_end;
		}

		public function get_location_factor()
		{
			return $this->location_factor;
		}

		public function set_location_factor( $location_factor )
		{
			$this->location_factor = $location_factor;
		}
		public function get_standard_factor()
		{
			return $this->standard_factor;
		}

		public function set_standard_factor( $standard_factor )
		{
			$this->standard_factor = $standard_factor;
		}
		public function get_custom_factor()
		{
			return $this->custom_factor;
		}

		public function set_custom_factor( $custom_factor )
		{
			$this->custom_factor = $custom_factor;
		}
		/**
		 * Returns true if the price item is active at the given date, false otherwise
		 * 
		 * @param $date the date to check
		 * @return bool
		 */
		public function is_active_at( $date )
		{
			if ($date >= strtotime($this->get_date_start()))
			{
				if (!$this->get_date_end() || ($this->get_date_end() && $date <= strtotime($this->get_date_end())))
				{
					return true;
				}
			}

			return false;
		}

		/**
		 * Reset this contract price item to its original values from the price list
		 */
		public function reset()
		{
			$so = self::get_so();

			$original = $so->get_single($this->get_price_item_id());
			$this->set_agresso_id($original->get_agresso_id());
			$this->set_title($original->get_title());
			$this->set_price($original->get_price());

			$so->update_contract_price_item($this);
		}
		/*
		 * Overridden function.  @see rental_model::store()
		 * This function saves the contract price item rather than the price item, and
		 * doesn't handle add since we handle that through a contract object.
		 * 
		 */

		public function store()
		{
			if ($this->validates())
			{
				$so = $this->get_so();

				if ($this->id)
				{
					// We can assume this composite came from the database since it has an ID. Update the existing row
					return $so->update_contract_price_item($this);
				}
			}
			// The object did not validate 
			return false;
		}

		/**
		 * Convert this object to a hash representation
		 * 
		 * @see rental/inc/model/rental_model#serialize()
		 */
		public function serialize()
		{
			$currency_prefix = $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'];
			$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			//$date_format="Y/m/d";
			return array(
				'id' => $this->get_id(),
				'price_item_id' => $this->get_price_item_id(),
				'contract_id' => $this->get_contract_id(),
				'area' => $this->get_area(),
				'count' => $this->get_count(),
				'agresso_id' => $this->get_agresso_id(),
				'title' => $this->get_title(),
				'is_area' => $this->get_type_text(),
				//'price' => money_format($currency_prefix.' %.2n',$this->get_price()),
				'price' => $this->get_price(),
				//'total_price' => $currency_prefix.' '.$this->get_total_price(),
				'total_price' => $this->get_total_price(),
				'is_one_time' => $this->is_one_time(),
				'location_factor' => $this->get_location_factor(),
				'standard_factor' => $this->get_standard_factor(),
				'custom_factor' => $this->get_custom_factor(),
				// We set a format fitting for the DateCellEditor here because
				// this table has inline editing enabled.  The DateCellEditor is not
				// happy about empty values if a custom parser is set, so we use the
				// in "date" parser which requires a format like: 2009/07/30 to work. 
				// EHL: Removed 2009-10-27, due to change to int datatype. 
				'date_start' => $this->get_date_start() != NULL ? date($date_format, $this->get_date_start()) : '',
				'date_end' => $this->get_date_end() != NULL ? date($date_format, $this->get_date_end()) : '',
				'price_type_title' => lang($this->get_price_type_title()),
				'billing_id' => $this->get_billing_id(),
			);
		}

		public function set_is_billed( $is_billed )
		{
			$this->is_billed = (bool)$is_billed;
		}

		public function is_billed()
		{
			return $this->is_billed;
		}

		public function set_is_one_time( $is_one_time )
		{
			$this->is_one_time = (bool)$is_one_time;
		}

		public function is_one_time()
		{
			return $this->is_one_time;
		}

		public function get_is_one_time()
		{
			return $this->is_one_time;
		}
	}