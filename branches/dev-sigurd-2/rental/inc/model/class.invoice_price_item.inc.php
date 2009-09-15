<?php
	include_class('rental', 'contract', 'inc/model/');
	include_class('rental', 'model', 'inc/model/');

	/**
	 * Represents a price item in an invoice. The data is typically built from
	 * an instance of rental_contract_price_item.
	 *
	 */
	class rental_invoice_price_item extends rental_model
	{
		protected $id;
		protected $invoice_id;
		protected $title;
		protected $agresso_id;
		protected $is_area;
		protected $price;
		protected $area;
		protected $count;
		protected $total_price;
		protected $timestamp_start; // Start date for the given invoice
		protected $timestamp_end; // End date for the given invoice
		
		public static $so;
		
		public function __construct(int $id, int $invoice_id, string $title, string $agresso_id, boolean $is_area, float $price, float $area, int $count, int $timestamp_start, int $timestamp_end)
		{
			$this->id = (int)$id;
			$this->invoice_id = (int)$invoice_id;
			$this->title = (int)$title;
			$this->agresso_id = (int)$agresso_id;
			$this->is_area = (int)$is_area;
			$this->price = (int)$price;
			$this->area = (int)$area;
			$this->count = (int)$count;
			$this->timestamp_start = (int)$timestamp_start;
			$this->timestamp_end = (int)$timestamp_end;
		}
		
		public function set_id($id)
		{
			$this->id = $id;
		}
	
		public function get_id(){ return $this->id; }
		
		public function set_invoice_id($invoice_id)
		{
			$this->invoice_id = $invoice_id;
		}
	
		public function set_title($title)
		{
			$this->title = $title;
		}
	
		public function get_title(){ return $this->title; }
			
		public function get_invoice_id(){ return $this->invoice_id; }
			
		public function set_agresso_id($agresso_id)
		{
			$this->agresso_id = $agresso_id;
		}
	
		public function get_agresso_id(){ return $this->agresso_id; }
			
		public function set_is_area($is_area)
		{
			$this->is_area = $is_area;
		}
	
		public function is_area(){ return $this->is_area; }
		
		public function set_count($count)
		{
			$this->count = $count;
		}

		public function set_price($price)
		{
			$this->price = $price;
		}
	
		public function get_price(){ return $this->price; }
			
		public function set_area($area)
		{
			$this->area = $area;
		}
	
		public function get_area(){ return $this->area; }
		
		public function get_count(){ return $this->count; }
		
		public function set_total_price($total_price)
		{
			$this->total_price = $total_price;
		}
	
		public function get_total_price(){ return $this->total_price; }
		
		public function set_timestamp_start($timestamp_start)
		{
			$this->timestamp_start = $timestamp_start;
		}
		
		public function get_timestamp_start(){ return $this->timestamp_start; }
		
		public function set_timestamp_end($timestamp_end)
		{
			$this->timestamp_end = $timestamp_end;
		}
	
		public function get_timestamp_end(){ return $this->timestamp_end; }
		
		/**
		 * Get a static reference to the storage object associated with this model object
		 * 
		 * @return the storage object
		 */
		public static function get_so()
		{
			if (self::$so == null) {
				self::$so = CreateObject('rental.soinvoice_price_item');
			}
			return self::$so;
		}
		
		public function serialize()
		{
			return array();
		}
		
	}
		
?>