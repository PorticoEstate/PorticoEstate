<?php
phpgw::import_class('controller.socommon');

include_class('controller', 'control_type', 'inc/model/');

class controller_socontrol_type extends controller_socommon
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
			self::$so = CreateObject('controller.socontrol_type');
		}
		return self::$so;
	}

	/**
	 * Function for adding a new activity to the database. Updates the activity object.
	 *
	 * @param activitycalendar_activity $activity the party to be added
	 * @return bool true if successful, false otherwise
	 */
	function add(&$control_type)
	{
		
		$control_type = $control_type->get_control_type();
		
		$sql = "INSERT INTO controller_control_type (type_name) VALUES ('$title')";
		$result = $this->db->query($sql, __LINE__,__FILE__);

		if(isset($result)) {
			// Set the new party ID
			$control_type->set_id($this->db->get_last_insert_id('controller_control_type', 'id'));
			// Forward this request to the update method
			return $this->update($control_type);
		}
		else
		{
			return false;
		}
		
	}

	/**
	 * Update the database values for an existing activity object.
	 *
	 * @param $activity the activity to be updated
	 * @return boolean true if successful, false otherwise
	 */

	function update($control_type)
	{	
		$id = intval($control_type->get_id());
			
		$values = array(
			'$type_name = ' . $this->marshal($control_type->get_type_name(), 'string')
		);
		
		//var_dump('UPDATE activity_activity SET ' . join(',', $values) . " WHERE id=$id");
		$result = $this->db->query('UPDATE controller_control_type SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);
		
		return isset($result);
	}
	
	/**
	 * Get single procedure
	 * 
	 * @param	$id	id of the procedure to return
	 * @return a controller_procedure
	 */
	function get_single($id)
	{
		$id = (int)$id;
		
		$sql = "SELECT p.* FROM controller_control_type p WHERE p.id = " . $id;
		$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
		$this->db->next_record();
		
		$control_type = new controller_control_type($this->unmarshal($this->db->f('id', true), 'int'));
		$control_type->set_type_name($this->unmarshal($this->db->f('type_name', true), 'string'));
		
		return $control_type;
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
	function get_control_type_array($start = 0, $results = 1000, $sort = null, $dir = '', $query = null, $search_option = null, $filters = array())
	{
		$results = array();
		
		//$condition = $this->get_conditions($query, $filters,$search_option);
		$order = $sort ? "ORDER BY $sort $dir ": '';
		
		//$sql = "SELECT * FROM controller_procedure WHERE $condition $order";
		$sql = "SELECT * FROM controller_control_type $order";
		$this->db->limit_query($sql, $start, __LINE__, __FILE__, $limit);
		
		while ($this->db->next_record()) {
			$control_type = new controller_control_type($this->unmarshal($this->db->f('id', true), 'int'));
			$control_type->set_title($this->unmarshal($this->db->f('title', true), 'string'));
			
			$results[] = $control_type;
		}
		
		return $results;
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
				'table'			=> 'activity', // alias
				'field'			=> 'id',
				'translated'	=> 'id'
			);
		}
		*/
		return $ret;
	}

	protected function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count)
	{
		
		
	}
	
	function populate(int $control_type_id, &$control_type)
	{
		/*
		if($control_type == null) {
			$control_type = new activitycalendar_activity((int) $activity_id);

			$control_type->set_title($this->unmarshal($this->db->f('title'), 'string'));
			$control_type->set_organization_id($this->unmarshal($this->db->f('organization_id'), 'int'));
			$control_type->set_type_id($this->unmarshal($this->db->f('type_id'), 'int'));
			$control_type->set_district($this->unmarshal($this->db->f('district'), 'int'));
			$control_type->set_office($this->unmarshal($this->db->f('office'), 'int'));
			$control_type->set_category($this->unmarshal($this->db->f('category'), 'int'));
			$control_type->set_state($this->unmarshal($this->db->f('state'), 'int'));
			$control_type->set_target($this->unmarshal($this->db->f('target'), 'string'));
			$control_type->set_description($this->unmarshal($this->db->f('description'), 'string'));
			$control_type->set_arena($this->unmarshal($this->db->f('arena'), 'string'));
			$control_type->set_internal_arena($this->unmarshal($this->db->f('internal_arena'), 'string'));
			$control_type->set_time($this->unmarshal($this->db->f('time'), 'string'));
			$control_type->set_last_change_date($this->unmarshal($this->db->f('last_change_date'), 'int'));
			$control_type->set_special_adaptation($this->unmarshal($this->db->f('special_adaptation', 'bool')));
			$control_type->set_secret($this->unmarshal($this->db->f('secret'), 'string'));
			
			
		}
		*/
		return $control_type;
	}
	
}
