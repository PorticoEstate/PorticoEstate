<?php
	/**
	 * Class that represents a price item in the price list
	 *
	 */
	
	phpgw::import_class('rental.bocommon');
	include_class('rental', 'model', 'inc/model/');
	
	class rental_price_item extends rental_model
	{
		public static $so;
		
		protected $id;
		protected $title;
		protected $agresso_id;
		protected $is_area;
		protected $price;
		
		/**
		 * Constructor.  Takes an optional ID.  If a price item is created from outside
		 * the database the ID should be empty so the database can add one according to its logic.
		 * 
		 * @param int $id the id of this price item
		 */
		public function __construct($id = 0)
		{
			$this->id = $id;
		}
		
		public function get_id()
		{
			return $this->id;
		}
		
		public function set_id($id)
		{
			$this->id = $id;
		}
		
		public function get_title()
		{
			return $this->title;
		}
		
		public function set_title($title)
		{
			$this->title = $title;
		}
		
		public function get_agresso_id()
		{
			return $this->agresso_id;
		}
		
		public function set_agresso_id($agresso_id)
		{
			$this->agresso_id = $agresso_id;
		}
		
		public function is_area()
		{
			return $this->is_area;
		}
		
		public function get_type_text()
		{
			if ($this->is_area()) {
				return lang('rental_price_item_type_area');
			} else {
				return lang('rental_price_item_type_apiece');
			}
		}
		
		public function set_is_area($is_area)
		{
			$this->is_area = $is_area;
		}
		
		public function get_price()
		{
			return $this->price;
		}
		
		public function set_price($price)
		{
			$this->price = $price;
		}
		
		/**
		 * Convert this object to a hash representation
		 * 
		 * @see rental/inc/model/rental_model#serialize()
		 */
		public function serialize()
		{
			return array(
				'id' => $this->get_id(),
				'title' => $this->get_title(),
				'agresso_id' => $this->get_agresso_id(),
				'is_area' => $this->get_type_text(),
				'price' => $this->get_price()
			);
		}
		
		/**
		 * Return a single rental_price_item object based on the provided id
		 * 
		 * @param $id rental price item id
		 * @return rental_price_item
		 */
		public static function get($id)
		{
			$so = self::get_so();
			
			return $so->get_single($id);
		}
		
		/**
		 * Return a list all of rental_price_item objects that fits the provided arguments
		 * 
		 * @param $start		which index to start the list at
		 * @param $results	how many results to return
		 * @param $sort			sort column
		 * @param $dir			sort direction
		 * @param $query
		 * @param $search_option
		 * @param $filters
		 * @return a list of rental_price_item objects
		 */
		public static function get_all($start = 0, $results = 1000, $sort = null, $dir = '', $query = null, $search_option = null, $filters = array())
		{
			$so = self::get_so();
			return $so->get_price_item_array($start, $results, $sort, $dir,
																			 $query, $search_option, $filters);
		}
		
		/**
		 * Get a static reference to the storage object associated with this model object
		 * 
		 * @return the storage object
		 */
		public static function get_so()
		{
			if (self::$so == null) {
				self::$so = CreateObject('rental.soprice_item');
			}
			
			return self::$so;
		}
	}