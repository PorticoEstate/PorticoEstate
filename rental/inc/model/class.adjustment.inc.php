<?php
include_class('rental', 'model', 'inc/model/');

class rental_adjustment extends rental_model
{
	protected $id;
	protected $price_item_id;
	protected $responsibility_id;
	protected $new_price;
	protected $percent;
	protected $adjustment_date;
	protected $year;
	protected $is_manual;
	protected $interval;
	protected $adjustment_type;
	protected $is_executed;

	public function __construct(int $id = null)
	{
		$adjustment_id = intval($id);
		parent::__construct($adjustment_id);
	}
	
	public function get_id()
	{
		return $this->id;
	}
	
	public function set_id(int $id)
	{
		$this->id = (int)$id;
	}
	
	public function get_price_item_id()
	{
		return $this->price_item_id;
	}
	
	public function set_price_item_id(int $price_item_id)
	{
		$this->price_item_id = (int)$price_item_id;
	}
	
	public function get_responsibility_id()
	{
		return $this->responsibility_id;
	}
	
	public function set_responsibility_id(int $responsibility_id)
	{
		$this->responsibility_id = (int)$responsibility_id;
	}
	
	public function get_new_price()
	{
		return $this->new_price;
	}
	
	public function set_new_price($new_price)
	{
		$this->new_price = $new_price;
	}
	
	public function get_percent()
	{
		return $this->percent;
	}
	
	public function set_percent($percent)
	{
		$this->percent = $percent;
	}
	
	public function get_adjustment_date()
	{
		return $this->adjustment_date;
	}
	
	public function set_year($year)
	{
		$this->year = $year;
	}
	
	public function get_year()
	{
		return $this->year;
	}
	
	public function set_adjustment_date($adjustment_date)
	{
		$this->adjustment_date = $adjustment_date;
	}
	
	public function get_is_manual()
	{
		return $this->is_manual;
	}
	
	public function is_manual()
	{
		return $this->is_manual;
	}
	
	public function set_is_manual($is_manual)
	{
		$this->is_manual = (boolean)$is_manual;
	}
	
	public function get_is_executed()
	{
		return $this->is_executed;
	}
	
	public function is_executed()
	{
		return $this->is_executed;
	}
	
	public function set_is_executed($is_executed)
	{
		$this->is_executed = (boolean)$is_executed;
	}
	
	public function get_interval()
	{
		return $this->interval;
	}
	
	public function set_interval(int $interval)
	{
		$this->interval = (int)$interval;
	}
	
	public function get_adjustment_type()
	{
		return $this->adjustment_type;
	}
	
	public function set_adjustment_type($adjustment_type)
	{
		$this->adjustment_type = $adjustment_type;
	}
	
	public function serialize()
	{
		$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
		return array(
			'id' => $this->get_id(),
			'price_item_id' => $this->get_price_item_id(),
			'responsibility_title' => lang(rental_socontract::get_instance()->get_responsibility_title($this->get_responsibility_id())),
			'new_price' => $this->get_new_price(),
			'percent' => $this->get_percent(),
			'interval' => $this->get_interval(),
			'adjustment_type' => lang(($this->get_adjustment_type())?$this->get_adjustment_type():'none'),
			'adjustment_date' => date($date_format, $this->get_adjustment_date()),
			'is_executed' => lang(($this->is_executed())?'yes':'no'),
			'year' => $this->get_year()
		);
	}
}
?>