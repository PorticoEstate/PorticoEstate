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
		protected $decimals;
		protected $id;
		protected $invoice_id;
		protected $title;
		protected $agresso_id;
		protected $is_area;
		protected $price_per_year;
		protected $area;
		protected $count;
		protected $total_price;
		protected $timestamp_start; // Start date for the given invoice
		protected $timestamp_end; // End date for the given invoice
		
		public static $so;
		
		public function __construct(int $decimals, int $id, int $invoice_id, string $title, string $agresso_id, boolean $is_area, float $price_per_year, float $area, int $count, int $timestamp_start, int $timestamp_end)
		{
			$this->decimals = (int)$decimals;
			$this->id = (int)$id;
			$this->invoice_id = (int)$invoice_id;
			$this->title = $title;
			$this->agresso_id = $agresso_id;
			$this->is_area = (boolean)$is_area;
			$this->price_per_year = (float)$price_per_year;
			$this->area = (float)$area;
			$this->count = (int)$count;
			$this->timestamp_start = (int)$timestamp_start;
			$this->timestamp_end = (int)$timestamp_end;
			$this->total_price = null; // Needs to be re-calculated
		}
		
		public function set_id($id)
		{
			$this->id = $id;
		}
	
		public function get_id(){ return $this->id; }
		
		public function set_invoice_id(int $invoice_id)
		{
			$this->invoice_id = (int)$invoice_id;
		}
	
		public function set_title(string $title)
		{
			$this->title = $title;
		}
	
		public function get_title(){ return $this->title; }
			
		public function get_invoice_id(){ return $this->invoice_id; }
			
		public function set_agresso_id(string $agresso_id)
		{
			$this->agresso_id = $agresso_id;
		}
	
		public function get_agresso_id(){ return $this->agresso_id; }
			
		public function set_is_area(boolean $is_area)
		{
			$this->is_area = (boolean)$is_area;
			$this->total_price = null; // Needs to be re-calculated
		}
	
		public function is_area(){ return $this->is_area; }
		
		public function set_count(int $count)
		{
			$this->count = (int)$count;
			$this->total_price = null; // Needs to be re-calculated
		}

		public function set_price($price_per_year)
		{
			$this->price_per_year = (float)$price_per_year;
			$this->total_price = null; // Needs to be re-calculated
		}
	
		public function get_price(){ return $this->price_per_year; }
			
		public function set_area($area)
		{
			$this->area = (float)$area;
			$this->total_price = null; // Needs to be re-calculated
		}
	
		public function get_area(){ return $this->area; }
		
		public function get_count(){ return $this->count; }
		
		public function set_total_price(float $total_price)
		{
			$this->total_price = (float)$total_price;
		}
	
		public function get_total_price(){ 
			if($this->total_price == null) // Needs to be calculated
			{
				$num_of_complete_months = 0; // The calculation of the price for complete months (meaning the item applies for the whole month) ..
				$incomplete_months = array(); // ..is different than the calculation for incomplete months
				$date_start = array();
				$date_start['year'] = (int)date('Y', $this->get_timestamp_start());
				$date_start['month'] = (int)date('n', $this->get_timestamp_start());
				$date_start['day'] = (int)date('j', $this->get_timestamp_start());
				$date_end = array();
				$date_end['year'] = (int)date('Y', $this->get_timestamp_end());
				$date_end['month'] = (int)date('n', $this->get_timestamp_end());
				$date_end['day'] = (int)date('j', $this->get_timestamp_end());
				for($current_year = $date_end['year']; $current_year >= $date_start['year']; $current_year--) // Runs through all the years this price item goes for
				{
					// We need to find which months to cover the current year
					// First we set the defaults
					$current_start_month = 1; // January
					$current_end_month = 12; // December
					if($current_year == $date_start['year']) // We're at the start year
					{
						$current_start_month = $date_start['month'];
					}
					if($current_year == $date_end['year']) // We're at the end year
					{
						$current_end_month = $date_end['month'];
					}
					
					for($current_month = $current_end_month; $current_month >= $current_start_month; $current_month--) // Runs through all of the months the current year (we go backwards since we go backwards with the years)
					{
						$num_of_days_in_current_month = date('t', strtotime($current_year . '-' . $current_month . '-01'));
						$first_day = 1;
						$last_day = $num_of_days_in_current_month;
						if($current_year == $date_start['year'] && $current_month == $date_start['month']) // We're at the start month
						{
							$first_day = $date_start['day'];
						}
						if($current_year == $date_end['year'] && $current_month == $date_end['month']) // We're at the end month
						{
							$last_day = $date_end['day']; // The end date's day is the item's end day
						}
						if($first_day === 1 && $last_day == $num_of_days_in_current_month){ // This is a whole month
							$num_of_complete_months++;
						}
						else // Incomplete month
						{
							$num_of_days_in_current_year = (date('L', strtotime($current_year . '01-01')) == 0) ? 365 : 366; // YYY: There must be a better day to do this!?
							$num_of_days = $last_day - $first_day + 1;
							$incomplete_months[] = array($num_of_days_in_current_year, $num_of_days);
						}
					}
				}
				// We need to find what we're basing the price on
				$amount = $this->is_area() ? $this->get_area() : $this->get_count();
				$rounded_element_price_per_month = round($this->get_price() / 12.0, $this->decimals); // We have to first _round_ the element price per month
				$price_per_month = $rounded_element_price_per_month * $amount; // The price per month is the rounded element price multipied by the amount
				$this->total_price = $price_per_month * $num_of_complete_months; // The total price for the complete months are just the monthly price multiplied by the number of months
				$rounded_price_per_year = round($this->get_price() * $amount, $this->decimals);
				foreach($incomplete_months as $day_factors) // Runs through all the incomplete months
				{
					$this->total_price += ($rounded_price_per_year / $day_factors[0]) * $day_factors[1];
				}
				// We round the total price for each item
				$this->total_price = round($this->total_price, $this->decimals);
			}
			return $this->total_price;
		}
		
		public function set_timestamp_start(int $timestamp_start)
		{
			$this->timestamp_start = (int)$timestamp_start;
			$this->total_price = null; // Needs to be re-calculated
		}
		
		public function get_timestamp_start(){ return $this->timestamp_start; }
		
		public function set_timestamp_end(int $timestamp_end)
		{
			$this->timestamp_end = (int)$timestamp_end;
			$this->total_price = null; // Needs to be re-calculated
		}
	
		public function get_timestamp_end(){ return $this->timestamp_end; }
		
		public function serialize()
		{
			return array();
		}
		
	}
		
?>