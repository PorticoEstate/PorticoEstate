<?php
phpgw::import_class('rental.socommon');
phpgw::import_class('rental.socontract');
phpgw::import_class('rental.socontract_price_item');
phpgw::import_class('rental.soprice_item');
phpgw::import_class('rental.soworkbench_notification');
//phpgw::import_class('rental.uicommon');

include_class('rental', 'adjustment', 'inc/model/');
include_class('rental', 'contract_price_item', 'inc/model/');
include_class('rental', 'notification', 'inc/model/');
include_class('rental', 'price_item', 'inc/model/');

class rental_soadjustment extends rental_socommon
{
	protected static $so;
	
	/**
	 * Get a static reference to the storage object associated with this model object
	 * 
	 * @return the storage object
	 */
	public static function get_instance()
	{
		if (self::$so == null) {
			self::$so = CreateObject('rental.soadjustment');
		}
		return self::$so;
	}

	protected function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count)
	{		
		$clauses = array('1=1');
		
		$filter_clauses = array();
		
		if(isset($filters[$this->get_id_field_name()])){
			$id = $this->marshal($filters[$this->get_id_field_name()],'int');
			$filter_clauses[] = "{$this->get_id_field_name()} = {$id}";
		}
		
		if(isset($filters['manual_adjustment']))
		{
			$clauses[] = "is_manual";
		}
		else
		{
			$clauses[] = "NOT is_manual";
		}
		
		if(count($filter_clauses))
		{
			$clauses[] = join(' AND ', $filter_clauses);
		}

		$condition =  join(' AND ', $clauses);

		$tables = "rental_adjustment";
		$joins = "";
		$dir = $ascending ? 'ASC' : 'DESC';
		if($return_count) // We should only return a count
		{
			$cols = 'COUNT(DISTINCT(id)) AS count';
			$order = "";
		}
		else
		{
			$cols = 'id, price_item_id, responsibility_id, new_price, percent, adjustment_interval, adjustment_date, adjustment_type, is_executed, year';
			$order = $sort_field ? "ORDER BY {$this->marshal($sort_field, 'field')} $dir ": ' ORDER BY adjustment_date DESC';
		}
		
		return "SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}";
	}
	
	function populate(int $adjustment_id, &$adjustment)
	{ 
		if($adjustment == null ) // new object
		{
			$adjustment = new rental_adjustment($adjustment_id);
			$adjustment->set_price_item_id($this->unmarshal($this->db->f('price_item_id', true), 'int'));
			$adjustment->set_responsibility_id($this->unmarshal($this->db->f('responsibility_id', true), 'int'));
			$adjustment->set_new_price($this->unmarshal($this->db->f('new_price', true), 'float'));
			$adjustment->set_percent($this->unmarshal($this->db->f('percent', true), 'float'));
			$adjustment->set_interval($this->unmarshal($this->db->f('adjustment_interval', true), 'int'));
			$adjustment->set_adjustment_date($this->unmarshal($this->db->f('adjustment_date', true), 'int'));
			$adjustment->set_adjustment_type($this->unmarshal($this->db->f('adjustment_type'), 'string'));
			$adjustment->set_is_manual($this->unmarshal($this->db->f('is_manual'),'bool'));
			$adjustment->set_is_executed($this->unmarshal($this->db->f('is_executed'),'bool'));
			$adjustment->set_year($this->unmarshal($this->db->f('year'), 'int'));
		}
		
		return $adjustment;
	}
	
	public function get_id_field_name(){
		return 'id';
	}

	/**
	 * Update the database values for an existing composite object. Also updates associated rental units.
	 *
	 * @param $composite the composite to be updated
	 * @return result receipt from the db operation
	 */
	public function update($adjustment)
	{
		$id = intval($adjustment->get_id());

		$values = array(
			'price_item_id = ' . $adjustment->get_price_item_id() ,
			'responsibility_id = ' . $adjustment->get_responsibility_id(),
			'new_price= ' . $adjustment->get_new_price(),
            'percent = '.$adjustment->get_percent(),
			'adjustment_interval = '.$adjustment->get_interval(),
            'adjustment_date = ' . $adjustment->get_adjustment_date(),
			'adjustment_type = \'' . $adjustment->get_adjustment_type() . '\'',
			'is_manual = ' . ($adjustment->is_manual() ? "true" : "false"),
			'is_executed = ' . ($adjustment->is_executed() ? "true" : "false"),
			'year = ' . $adjustment->get_year()
		);

		$result = $this->db->query('UPDATE rental_adjustment SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);

		return $result != null;
	}
	
	/**
	 * Add a new adjustment to the database.  Adds the new insert id to the object reference.
	 *
	 * @param $adjustment the adjustment to be added
	 * @return int with id of the adjustment
	 */
	public function add(&$adjustment)
	{
		// Build a db-friendly array of the adjustment object
		$cols = array('price_item_id', 'responsibility_id', 'new_price', 'percent', 'adjustment_interval', 'adjustment_date', 'adjustment_type', 'is_manual', 'is_executed', 'year');
		$values = array(
			$adjustment->get_price_item_id(),
			$adjustment->get_responsibility_id(),
			$adjustment->get_new_price(),
			$adjustment->get_percent(),
			$adjustment->get_interval(),
			$adjustment->get_adjustment_date(),
			'\''.$adjustment->get_adjustment_type().'\'',
			($adjustment->is_manual() ? "true" : "false"),
			($adjustment->is_executed() ? "true" : "false"),
			$adjustment->get_year()
		);

		$query ="INSERT INTO rental_adjustment (" . join(',', $cols) . ") VALUES (" . join(',', $values) . ")";
		$result = $this->db->query($query);

		$adjustment_id = $this->db->get_last_insert_id('rental_adjustment', 'id');
		$adjustment->set_id($adjustment_id);
		return $adjustment_id;
	}
	
	public function adjustment_exist($adjustment)
	{
		$query = "SELECT * FROM rental_adjustment WHERE " .
				 "responsibility_id = {$adjustment->get_responsibility_id()} " .
				 "AND adjustment_date = {$adjustment->get_adjustment_date()} " .
				 "AND year = {$adjustment->year()} " . 
				 "AND adjustment_interval = {$adjustment->get_interval()} " .
				 "AND percent = {$adjustment->get_percent()}";
		$result = $this->db->query($query);
		if($this->db->next_record())
		{
			return true;
		}
		return false;
	}
	
	public function delete($id)
	{
		//TODO: Should be updated when run status is included in database design
		if(isset($id) && $id > 0)
		{
			$query = "DELETE FROM rental_adjustment WHERE id = {$id} AND NOT is_executed";
			$result = $this->db->query($query);
			if($result && ($this->db->affected_rows() > 0))
			{
				return true;
			}
		}
		return false;
	}
	
	public function run_adjustments()
	{
		/* check if there are incomplete adjustments (for today)
		 * gather all adjustable contracts with 
		 * 		interval = adjustment interval and this year = last adjusted + interval
		 * 		or
		 * 		last adjusted is null / 0 (use contract start year)
		 * adjust each contract's price items according to adjustment info (percent, adjustment_percent)
		 * Run as transaction
		 * update adjustment -> set is_executed to true
		 * update price book elements according to type if interval=1
		 */
		
		$prev_day = mktime(0,0,0,date('m'),date('d'),date('Y'));
		$next_day = mktime(0,0,0,date('m'),date('d')+1,date('Y'));

		//get incomplete adjustments for today
		$adjustments_query = "SELECT * FROM rental_adjustment WHERE NOT is_executed AND (adjustment_date < {$next_day} AND adjustment_date >= {$prev_day})";
		//var_dump($adjustments_query);
		$result = $this->db->query($adjustments_query);
		//var_dump("etter spr");
		//there are un-executed adjustments
		$adjustments = array();
		while($this->db->next_record())
		{
			$adjustment_id = $this->unmarshal($this->db->f('id', true), 'int');
			$adjustment = new rental_adjustment($adjustment_id);
			$adjustment->set_price_item_id($this->unmarshal($this->db->f('price_item_id', true), 'int'));
			$adjustment->set_responsibility_id($this->unmarshal($this->db->f('responsibility_id', true), 'int'));
			$adjustment->set_new_price($this->unmarshal($this->db->f('new_price', true), 'float'));
			$adjustment->set_percent($this->unmarshal($this->db->f('percent', true), 'float'));
			$adjustment->set_interval($this->unmarshal($this->db->f('adjustment_interval', true), 'int'));
			$adjustment->set_adjustment_date($this->unmarshal($this->db->f('adjustment_date', true), 'int'));
			$adjustment->set_adjustment_type($this->unmarshal($this->db->f('adjustment_type'), 'string'));
			$adjustment->set_is_manual($this->unmarshal($this->db->f('is_manual'),'bool'));
			$adjustment->set_is_executed($this->unmarshal($this->db->f('is_executed'),'bool'));
			$adjustment->set_year($this->unmarshal($this->db->f('year'), 'int'));
			$adjustments[] = $adjustment;
		}
		
		if(count($adjustments) > 0)
		{
			$this->db->transaction_begin();
			$success = $this->adjust_contracts($adjustments);
			if($success)
			{
				$this->db->transaction_commit();
			}
			else
			{
				$this->db->transaction_abort();
			}
			return $success;
		}
		return false;
	}
	
	public function adjust_contracts($adjustments)
	{
		/*
		 * gather all adjustable contracts with 
		 * 		interval = adjustment interval and this year = last adjusted + interval
		 * 		or
		 * 		last adjusted is null / 0 (use contract start year)
		 * adjust each contract's price items according to adjustment info (percent, adjustment_percent)
		 * Run as transaction
		 * update adjustment -> set is_executed to true
		 * update price book elements according to type if interval=1
		 */
		$current_year = (int)date('Y');
		
		//var_dump("innicontr");
		foreach ($adjustments as $adjustment)
		{
			//gather all adjustable contracts
			$adjustable_contracts = "SELECT id, adjustment_share, date_start, adjustment_year FROM rental_contract ";
			$adjustable_contracts .= "WHERE location_id = '{$adjustment->get_responsibility_id()}' AND adjustable ";
			$adjustable_contracts .= "AND adjustment_interval = {$adjustment->get_interval()} ";
			$adjustable_contracts .= "AND (((adjustment_year + {$adjustment->get_interval()}) <= {$adjustment->get_year()})";
			$adjustable_contracts .= " OR ";
			$adjustable_contracts .= "(adjustment_year IS NULL OR adjustment_year = 0)";
			$adjustable_contracts .= ")";
			//var_dump($adjustable_contracts);
			$result = $this->db->query($adjustable_contracts);
			while($this->db->next_record())
			{
				$contract_id = $this->unmarshal($this->db->f('id', true), 'int');
				$adjustment_share = $this->unmarshal($this->db->f('adjustment_share', true), 'int');
				$date_start = $this->unmarshal($this->db->f('date_start', true), 'int');
				$adj_year = $this->unmarshal($this->db->f('adjustment_year', true), 'int');
				$start_year = date('Y', $date_start);

				$contract = rental_socontract::get_instance()->get_single($contract_id);

				$firstJanAdjYear = mktime(0,0,0,1,1,$adjustment->get_year());
				if($contract->is_active($firstJanAdjYear) && (($adj_year != null && $adj_year > 0) || (($adj_year == null || $adj_year == 0) && ($start_year + $adjustment->get_interval() <= $adjustment->get_year()))))
				{
					//update adjustment_year on contract
					rental_socontract::get_instance()->update_adjustment_year($contract_id, $adjustment->get_year());
					//gather price items to be adjusted
					$contract_price_items = rental_socontract_price_item::get_instance()->get(null, null, null, null, null, null, array('contract_id' => $contract_id));
					foreach($contract_price_items as $cpi)
					{
						//update price according to adjustment info
						$cpi_old_price = $cpi->get_price();
						$cpi_adjustment = ($cpi_old_price*($adjustment->get_percent()/100))*($adjustment_share/100);
						$cpi_new_price = $cpi_old_price + $cpi_adjustment;
						$cpi->set_price($cpi_new_price);
						rental_socontract_price_item::get_instance()->store($cpi);
					}
				}
			}
			
			//TODO: update price book
			if($adjustment->get_interval() == 1){
				$adjustable_price_items = "SELECT * FROM rental_price_item WHERE responsibility_id = {$adjustment->get_responsibility_id()} AND is_adjustable";
				$result = $this->db->query($adjustable_price_items);
				$price_items = array();
				while($this->db->next_record())
				{
					$price_item = new rental_price_item($this->unmarshal($this->db->f('id'),'int'));
					$price_item->set_title($this->unmarshal($this->db->f('title'),'string'));
					$price_item->set_agresso_id($this->unmarshal($this->db->f('agresso_id'),'string'));
					$price_item->set_is_area($this->unmarshal($this->db->f('is_area'),'bool'));
					$price_item->set_is_inactive($this->unmarshal($this->db->f('is_inactive'),'bool'));
					$price_item->set_is_adjustable($this->unmarshal($this->db->f('is_adjustable'),'bool'));
					$price_item->set_price($this->unmarshal($this->db->f('price'),'float'));
					$price_item->set_responsibility_id($this->unmarshal($this->db->f('responsibility_id', true), 'int'));
					$price_item->set_responsibility_title($this->unmarshal($this->db->f('resp_title', true), 'string'));
					
					$price_items[] = $price_item;
				}
				
				foreach($price_items as $pi)
				{
					$pi_old_price = $pi->get_price();
					$pi_adjustment = $pi_old_price*($adjustment->get_percent()/100);
					$pi_new_price = $pi_old_price + $pi_adjustment;
					$pi->set_price($pi_new_price);
					rental_soprice_item::get_instance()->store($pi);
				}
			}
			
			$adjustment->set_is_executed(true);
			$this->update($adjustment);
			
			//notify all users with write access on the field of responsibility
			$location_id = $adjustment->get_responsibility_id();
			if($location_id)
			{
				$location_names = $GLOBALS['phpgw']->locations->get_name($location_id);
				if($location_names['appname'] == $GLOBALS['phpgw_info']['flags']['currentapp'])
				{
					$responsible_accounts = $GLOBALS['phpgw']->acl->get_user_list_right(PHPGW_ACL_EDIT,$location_names['location']);
					foreach($responsible_accounts as $ra)
					{
						$account_ids[] = $ra['account_id'];
					}
				}
				
			}

			$location_label = rental_socontract::get_instance()->get_responsibility_title($location_id);
			$adj_interval = $adjustment->get_interval();
			$day = date("Y-m-d",strtotime('now'));
			$ts_today = strtotime($day);
			//notify each unique account
			foreach($account_ids as $account_id) {
				if($account_id && $account_id > 0)
				{
					
					$notification = new rental_notification
					(
						0,					// No notification identifier
						$account_id,
						0,					// No location identifier
						null,				// No contract id
						$ts_today,
						$location_label.'_'.$adj_interval,
						null,
						null,
						null,
						null
					);
					rental_soworkbench_notification::get_instance()->store($notification);
				}		
			}
		}
		return true;
	}
	
	public function newer_executed_regulation_exists($adjustment){
		$columns = "id";
		$table = "rental_adjustment";
		$conditions = "is_executed='true'".
					"AND adjustment_date > {$adjustment->get_adjustment_date()}".
					"AND adjustment_interval={$adjustment->get_interval()}".
					"AND responsibility_id={$adjustment->get_responsibility_id()}";
		$sql = "Select $columns from $table where $conditions"; 
		
		$result = $this->db->query($sql);
		
		if($this->db->num_rows() > 0){
			return true;
		}
		return false;
	}
}
?>
