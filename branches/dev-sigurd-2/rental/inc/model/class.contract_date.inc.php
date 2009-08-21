<?php
/**
 * A date range in a contract.
 *
 */
class rental_contract_date
{
	protected $start_date;
	protected $end_date;
	
	/**
	 * Constructs a date range. Dates should be as long, seconds from 1.1.1970
	 * 
	 * @param $start_date string with start of contract.
	 * @param $end_date string with end of contract.
	 */
	public function __construct(string $start_date = null, string $end_date = null)
	{
		if($start_date != '')
		{
			$this->start_date = (string)$start_date;
		}
		else
		{
			$this->start_date = null;
		}
		if($end_date != '')
		{
			$this->end_date = (string)$end_date;
		}
		else
		{
			$this->end_date = null;
		}
	}
	
	public function get_start_date()
	{
		return $this->start_date;
	}
	
	public function has_start_date()
	{
		return $this->start_date != null && $this->start_date != ''; 
	}
	
	public function get_end_date()
	{
		return $this->end_date;
	}
	
	public function has_end_date()
	{
		return $this->end_date != null && $this->end_date != ''; 
	}
	
	public function serialize()
	{
		
	} 
	
}
?>