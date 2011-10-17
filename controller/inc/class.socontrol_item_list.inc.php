<?php
phpgw::import_class('controller.socommon');

include_class('controller', 'control_item_list', 'inc/model/');

class controller_socontrol_item_list extends controller_socommon
{
	protected static $so;

	/**
	 * Get a static reference to the storage object associated with this model object
	 *
	 * @return controller_soparty the storage object
	 */
	public static function get_instance()
	{
		if (self::$so == null) {
			self::$so = CreateObject('controller.socontrol_item_list');
		}
		return self::$so;
	}

	/**
	 * Function for adding a new activity to the database. Updates the activity object.
	 *
	 * @param activitycalendar_activity $activity the party to be added
	 * @return bool true if successful, false otherwise
	 */
	function add(&$control_item_list)
	{
		$cols = array(
				'control_id',
				'control_item_id',
		);
		
		$values = array(
			$this->marshal($control_item_list->get_control_id(), 'int'),
			$this->marshal($control_item_list->get_control_item_id(), 'int')
		);
		
		$result = $this->db->query( 'INSERT INTO controller_control_item_list (' . join(',', $cols) . ') VALUES (' . join(',', $values) . ')', __LINE__,__FILE__);
		$result = $this->db->query($sql, __LINE__,__FILE__);

		if(isset($result)) {
			// return the new control item ID
			return $this->db->get_last_insert_id('controller_control_item_list', 'id');
			// Forward this request to the update method
			//return $this->update($control_item);
		}
		else
		{
			return 0;
		}
	}

	/**
	 * Update the database values for an existing activity object.
	 *
	 * @param $activity the activity to be updated
	 * @return boolean true if successful, false otherwise
	 */

/**
	 * Update the database values for an existing activity object.
	 *
	 * @param $activity the activity to be updated
	 * @return boolean true if successful, false otherwise
	 */

	function update($control_item_list)
	{	
		$id = intval($control_item_list->get_id());
			
		$values = array(
			'control_id = ' . $this->marshal($control_item_list->get_control_id(), 'int'),
			'control_item_id = '. $this->marshal($control_item_list->get_control_item_id(), 'int'),
			'order_nr = ' . $this->marshal($control_item_list->get_order_nr(), 'int')
		);
		
		$result = $this->db->query('UPDATE controller_control_item_list SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);
				
		return isset($result);
	}
	
	/**
	 * Get single control_item_list
	 * 
	 * @param	$id	id of the control_item_list to return
	 * @return a controller_control_item_list
	 */
	function get_single($id)
	{
		$id = (int)$id;
		
		$sql = "SELECT p.* FROM controller_control_item_list p WHERE p.id = " . $id;
		$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
		$this->db->next_record();
		
		$control_item_list = new controller_control_item_list($this->unmarshal($this->db->f('id', true), 'int'));
		$control_item_list->set_control_id($this->unmarshal($this->db->f('control_id', true), 'int'));
		$control_item_list->set_control_item_id($this->unmarshal($this->db->f('control_item_id', true), 'int'));
		$control_item_list->set_order_nr($this->unmarshal($this->db->f('order_nr', true), 'int'));
		
		return $control_item_list;
	}
	
	function get_single_2($control_id, $control_item_id)
	{		
		$sql = "SELECT p.* FROM controller_control_item_list p WHERE p.control_id = " . $control_id . " AND p.control_item_id = " . $control_item_id;
		$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
		$this->db->next_record();
		
		$control_item_list = new controller_control_item_list($this->unmarshal($this->db->f('id', true), 'int'));
		$control_item_list->set_control_id($this->unmarshal($this->db->f('control_id', true), 'int'));
		$control_item_list->set_control_item_id($this->unmarshal($this->db->f('control_item_id', true), 'int'));
		$control_item_list->set_order_nr($this->unmarshal($this->db->f('order_nr', true), 'int'));
		
		return $control_item_list;
	}
	
	function delete($control_id, $control_item_id)
	{		
		var_dump("DELETE FROM controller_control_item_list WHERE control_id = $control_id AND control_item_id = $control_item_id");
		
		$result = $this->db->query("DELETE FROM controller_control_item_list WHERE control_id = $control_id AND control_item_id = $control_item_id", __LINE__,__FILE__);
				
		return isset($result);
	}
	
	/**
	 * Get a list of procedure objects matching the specific filters
	 * 
	 * @param $start search result offset
	 * @param $results number of results to return
	 * @param $sort field to sort by
	 * @param $query LIKE-based query string
	 * @param $filters array of custom filters
	 * @return list of rental_composite objects
	 */
	function get_control_item_array($start = 0, $results = 1000, $sort = null, $dir = '', $query = null, $search_option = null, $filters = array())
	{
		/*
		
		$results = array();
		
		//$condition = $this->get_conditions($query, $filters,$search_option);
		$order = $sort ? "ORDER BY $sort $dir ": '';
		
		//$sql = "SELECT * FROM controller_procedure WHERE $condition $order";
		$sql = "SELECT * FROM controller_control_item $order";
		$this->db->limit_query($sql, $start, __LINE__, __FILE__, $limit);
		
		while ($this->db->next_record()) {
			$control_item = new controller_control_item($this->unmarshal($this->db->f('id', true), 'int'));
			$control_item->set_title($this->unmarshal($this->db->f('title', true), 'string'));
			$control_item->set_required($this->unmarshal($this->db->f('required', true), 'boolean'));
			$control_item->set_what_to_do($this->unmarshal($this->db->f('what_to_do', true), 'string'));
			$control_item->set_how_to_do($this->unmarshal($this->db->f('how_to_do', true), 'string'));
			$control_item->set_control_group_id($this->unmarshal($this->db->f('control_group_id', true), 'int'));
			$control_item->set_control_area_id($this->unmarshal($this->db->f('control_area_id', true), 'int'));
			
			$results[] = $control_item;
		}
		
		return $results;
		
		*/
	}	
	
	function get_id_field_name($extended_info = false)
	{
		/*
		if(!$extended_info)
		{
			$ret = 'id';
		}
		else
		{
			$ret = array
			(
				'table'			=> 'controller', // alias
				'field'			=> 'id',
				'translated'	=> 'id'
			);
		}
		
		return $ret;
		*/
	}

	protected function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count)
	{
		/*
		$clauses = array('1=1');
		
		$filter_clauses = array();
		
		// Search for based on search type
		if($search_for)
		{
			$search_for = $this->marshal($search_for,'field');
			$like_pattern = "'%".$search_for."%'";
			$like_clauses = array();
			switch($search_type){
				default:
					$like_clauses[] = "controller_control_item.title $this->like $like_pattern";
					$like_clauses[] = "controller_control_item.what_to_do $this->like $like_pattern";
					$like_clauses[] = "controller_control_item.how_to_do $this->like $like_pattern";
					break;
			}
			
			if(count($like_clauses))
			{
				$clauses[] = '(' . join(' OR ', $like_clauses) . ')';
			}
		}
		
		if(isset($filters[$this->get_id_field_name()]))
		{
			$filter_clauses[] = "controller_control_item.id = {$this->marshal($filters[$this->get_id_field_name()],'int')}";
		}
		
		if(count($filter_clauses))
		{
			$clauses[] = join(' AND ', $filter_clauses);
		}
		
		
		$condition =  join(' AND ', $clauses);

		$tables = "controller_control_item";
		//$joins = " {$this->left_join} rental_document_types ON (rental_document.type_id = rental_document_types.id)";
		
		if($return_count)
		{
			$cols = 'COUNT(DISTINCT(controller_control_item.id)) AS count';
		}
		else
		{
			$cols = 'id, title, required, what_to_do, how_to_do, control_group_id, control_area_id';
		}
		
		$dir = $ascending ? 'ASC' : 'DESC';
		if($sort_field == 'title')
		{
			$sort_field = 'controller_control_item.title';
		}
		$order = $sort_field ? "ORDER BY {$this->marshal($sort_field, 'field')} $dir ": '';
		
		//return "SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}";
		
		return "SELECT {$cols} FROM {$tables} WHERE {$condition} {$order}";
		
		*/
	}
	
	function get_control_items($control_group_id)
	{
		/*
		$results = array();
		
		$sql = "SELECT * FROM controller_control_item WHERE control_group_id=$control_group_id";
		$this->db->limit_query($sql, $start, __LINE__, __FILE__, $limit);
		
		while ($this->db->next_record()) {
			$control_item = new controller_control_item($this->unmarshal($this->db->f('id', true), 'int'));
			$control_item->set_title($this->unmarshal($this->db->f('title', true), 'string'));
			$control_item->set_required($this->unmarshal($this->db->f('required', true), 'boolean'));
			$control_item->set_what_to_do($this->unmarshal($this->db->f('what_to_do', true), 'string'));
			$control_item->set_how_to_do($this->unmarshal($this->db->f('how_to_do', true), 'string'));
			$control_item->set_control_group_id($this->unmarshal($this->db->f('control_group_id', true), 'int'));
			$control_item->set_control_area_id($this->unmarshal($this->db->f('control_area_id', true), 'int'));
			
			$results[] = $control_item;
		}
		
		return $results;
		
		*/
	}
	
	function populate(int $control_item_id, &$control_item)
	{
		/*
		if($control_item == null) {
			$control_item = new controller_control_item((int) $control_item_id);

			$control_item->set_title($this->unmarshal($this->db->f('title', true), 'string'));
			$control_item->set_required($this->unmarshal($this->db->f('required', true), 'boolean'));
			$control_item->set_what_to_do($this->unmarshal($this->db->f('what_to_do', true), 'string'));
			$control_item->set_how_to_do($this->unmarshal($this->db->f('how_to_do', true), 'string'));
			$control_item->set_control_group_id($this->unmarshal($this->db->f('control_group_id', true), 'int'));
			$control_item->set_control_area_id($this->unmarshal($this->db->f('control_area_id', true), 'int'));
		}
		
		return $control_item;
		*/
	}
	
}
