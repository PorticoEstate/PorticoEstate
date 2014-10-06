<?php

include_class('rental', 'model', 'inc/model/');

class rental_billing_info extends rental_model
{
	protected $id;
	protected $billing_id;
	protected $term_id;
	protected $location_id;
	protected $month;
	protected $year;
	protected $deleted;
	protected $term_label;
	
	public static $so;
	
	public function __construct(int $id = null, int $billing_id = null, $location_id = null, int $billing_term = null, int $year = null, int $month = null)
	{
		$this->id = $id;
		$this->billing_id = $billing_id;
		$this->location_id = $location_id;
		$this->term_id = $billing_term;
		$this->year = $year;
		$this->month = $month;
	}
	
	public function get_id()
	{
		return $this->id;
	}
	
	public function set_id(int $id)
	{
		$this->id = (int)$id;
	}
	
	public function get_billing_id()
	{
		return $this->billing_id;
	}
	
	public function set_billing_id(int $billing_id)
	{
		$this->billing_id = (int)$billing_id;
	}
	
	public function get_term_id()
	{
		return $this->term_id;
	}
	
	public function set_term_id(int $term_id)
	{
		$this->term_id = (int)$term_id;
	}
	
	public function get_location_id()
	{
		return $this->location_id;
	}
	
	public function set_location_id(int $location_id)
	{
		$this->location_id = (int)$location_id;
	}
	
	public function get_year()
	{
		return $this->year;
	}
	
	public function set_year($year)
	{
		$this->year = $year;
	}
	
	public function get_month()
	{
		return $this->month;
	}
	
	public function set_month($month)
	{
		$this->month = $month;
	}
	
	public function set_deleted(boolean $deleted)
	{
		$this->deleted = (boolean)$deleted;
	}

	public function is_deleted()
	{
		return $this->deleted;
	}
	
	public function serialize()
	{
		return array(
			'id'				=> $this->get_id(),
			'location_id'		=> $this->get_location_id(),
			'term_id'			=> $this->get_term_id(),
			'year'				=> $this->get_year(),
			'month'				=> $this->get_month()
		);
	}
	
	public function get_term_label()
	{
		return $this->term_label;
	}
	
	public function set_term_label($term_label)
	{
		$this->term_label = $term_label;
	}
}
?>