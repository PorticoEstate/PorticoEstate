<?php
	include_class('rental', 'model', 'inc/model/');

	/**
	 * Class that represents a price item in the price list
	 *
	 */
	class rental_price_item extends rental_model
	{

		const PRICE_TYPE_YEAR = 1;
		const PRICE_TYPE_MONTH = 2;
		const PRICE_TYPE_DAY = 3;
		const PRICE_TYPE_HOUR = 4;

		public static $so;
		protected $id;
		protected $title;
		protected $agresso_id;
		protected $is_area;
		protected $price;
		protected $is_inactive;
		protected $is_adjustable;
		protected $standard;
		protected $responsibility_id;
		protected $responsibility_title;
		protected $price_type_id;
		protected $price_type_title;
		protected $price_types = array(
			1 => 'year',
			2 => 'month',
			3 => 'day',
//			4 => 'hour',
		);

		//protected $is_one_time;

		/**
		 * Constructor.  Takes an optional ID.  If a price item is created from outside
		 * the database the ID should be empty so the database can add one according to its logic.
		 *
		 * @param int $id the id of this price item
		 */
		public function __construct( $id = 0 )
		{
			$this->id = $id;
			$this->is_area = true;
		}

		public function get_id()
		{
			return $this->id;
		}

		public function set_id( $id )
		{
			$this->id = $id;
		}

		public function get_title()
		{
			return $this->title;
		}

		public function set_title( $title )
		{
			$this->title = $title;
		}

		public function get_agresso_id()
		{
			return $this->agresso_id;
		}

		public function set_agresso_id( $agresso_id )
		{
			$this->agresso_id = $agresso_id;
		}

		public function is_area()
		{
			return $this->is_area;
		}

		public function get_type_text()
		{
			if ($this->is_area())
			{
				return lang('price_item_type_area');
			}
			else
			{
				return lang('price_item_type_apiece');
			}
		}

		public function set_is_area( $is_area )
		{
			$this->is_area = (bool)$is_area;
		}

		public function is_inactive()
		{
			return $this->is_inactive;
		}

		public function get_status_text()
		{
			if ($this->is_inactive())
			{
				return lang('price_item_inactive');
			}
			else
			{
				return lang('price_item_active');
			}
		}

		public function set_is_inactive( $is_inactive )
		{
			$this->is_inactive = (bool)$is_inactive;
		}

		public function get_price()
		{
			if (!$this->price)
				$this->price = 0;

			return $this->price;
		}

		public function set_price( $price )
		{
			$this->price = (float)$price;
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
				'is_inactive' => $this->get_status_text(),
				'is_adjustable' => $this->get_adjustable_text(),
				'standard' => $this->get_standard_text(),
				'price' => $this->get_price(),
				'responsibility_id' => $this->get_responsibility_id(),
				'responsibility_title' => lang($this->get_responsibility_title()),
				'price_type_title' => lang($this->get_price_type_title()),
				'price_type_id' => $this->get_price_type_id(),
				//'is_one_time' => $this->is_one_time()
			);
		}

		/**
		 * Return a single rental_price_item object based on the provided id
		 *
		 * @param $id rental price item id
		 * @return rental_price_item
		 */
		public static function get( $id )
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
		public static function get_all( $start = 0, $results = 1000, $sort = null, $dir = '', $query = null, $search_option = null, $filters = array() )
		{
			$so = self::get_so();
			return $so->get_price_item_array($start, $results, $sort, $dir, $query, $search_option, $filters);
		}

		/**
		 * Get a static reference to the storage object associated with this model object
		 *
		 * @return the storage object
		 */
		public static function get_so()
		{
			if (self::$so == null)
			{
				self::$so = CreateObject('rental.soprice_item');
			}

			return self::$so;
		}

		/**
		 * @see rental/inc/model/rental_model#validates()
		 */
		public function validates()
		{
			$valid = parent::validates();

			// Check that we have a correct agresso id
			/*
			  if ($this->get_agresso_id() && !rental_validator::valid_agresso_id($this->get_agresso_id(), $this->validation_errors['agresso_id'])) {
			  $valid = false;
			  }
			 */

			return $valid;
		}

		public function get_price_types()
		{
			return $this->price_types;
		}

		public function get_price_type_id()
		{
			return $this->price_type_id;
		}

		public function set_price_type_id( $price_type_id )
		{
			$this->price_type_id = $price_type_id;
		}

		public function get_price_type_title()
		{
			return $this->price_type_title;
		}

		public function set_price_type_title( $price_type_id )
		{
			$this->price_type_title = $this->price_types[$price_type_id];
		}

		public function get_responsibility_id()
		{
			return $this->responsibility_id;
		}

		public function set_responsibility_id( $responsibility_id )
		{
			$this->responsibility_id = $responsibility_id;
		}

		public function get_responsibility_title()
		{
			return $this->responsibility_title;
		}

		public function set_responsibility_title( $responsibility_title )
		{
			$this->responsibility_title = $responsibility_title;
		}

		public function is_adjustable()
		{
			return $this->is_adjustable;
		}

		public function get_adjustable_text()
		{
			if ($this->is_adjustable())
			{
				return lang('price_item_adjustable');
			}
			else
			{
				return lang('price_item_not_adjustable');
			}
		}

		public function set_is_adjustable( $is_adjustable )
		{
			$this->is_adjustable = (bool)$is_adjustable;
		}

		public function is_standard()
		{
			return $this->standard;
		}

		public function get_standard_text()
		{
			if ($this->is_standard())
			{
				return lang('yes');
			}
			else
			{
				return lang('no');
			}
		}

		public function set_standard( $standard )
		{
			$this->standard = (bool)$standard;
		}
		/* 		public function is_one_time(){
		  return $this->is_one_time;
		  }

		  public function set_is_one_time($is_one_time){
		  $this->is_one_time = (bool)$is_one_time;
		  }
		 */
	}