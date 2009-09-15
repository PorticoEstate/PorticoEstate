<?php
phpgw::import_class('rental.socommon');

class rental_sobilling extends rental_socommon
{
	public function __construct()
	{
		parent::__construct('rental_billing',
			array
			(
				'id'				=> array('type' => 'int'),
				'total_sum'			=> array('type' => 'int'),
				'success'			=> array('type' => 'int'),
				'timestamp_start'	=> array('type' => 'int'),
				'timestamp_stop'	=> array('type' => 'int'),
				'location_id'		=> array('type' => 'int'), // Contract type
				'term_id'			=> array('type' => 'int'),
				'year'				=> array('type' => 'int'),
				'month'				=> array('type' => 'int')
			));
	}
	
	public function add(rental_billing &$billing)
	{
		$values = array
		(
			$this->marshal($billing->get_total_sum(), 'int'),
			$billing->get_success() ? 'true' : 'false',
			$this->marshal($billing->get_timestamp_start(), 'int'),
			$this->marshal($billing->get_timestamp_stop(), 'int'),
			$this->marshal($billing->get_location_id(), 'int'),
			$this->marshal($billing->get_billing_term(), 'int'),
			$this->marshal($billing->get_year(), 'int'),
			$this->marshal($billing->get_month(), 'int'),
		);
		$query ="INSERT INTO ".$this->table_name." (" . join(',', array_keys(array_slice($this->fields, 1))) . ") VALUES (" . join(',', $values) . ")";
		$receipt = null;
		if($this->db->query($query))
		{
			$receipt = array();
			$receipt['id'] = $this->db->get_last_insert_id($this->table_name, 'id');
			$billing->set_id($receipt['id']);
		}
		return $receipt;
	}
	
	public function update(rental_billing &$billing)
	{
		$values = array(
			'total_sum = ' . $this->marshal($billing->get_total_sum(), 'int'),
			"success = '" . ($billing->get_success() ? 'true' : 'false') . "'",
			'timestamp_start = ' . $this->marshal($billing->get_timestamp_start(), 'int'),
			'timestamp_stop = ' . $this->marshal($billing->get_timestamp_stop(), 'int'),
			'location_id = ' . $this->marshal($billing->get_location_id(), 'int'),
			'term_id = ' . $this->marshal($billing->get_billing_term(), 'int'),
			'year = ' . $this->marshal($billing->get_year(), 'int'),
			'month = ' . $this->marshal($billing->get_month(), 'int')
		);
		$result = $this->db->query('UPDATE ' . $this->table_name . ' SET ' . join(',', $values) . " WHERE id=" . $billing->get_id(), __LINE__,__FILE__);
	}
	
}
?>