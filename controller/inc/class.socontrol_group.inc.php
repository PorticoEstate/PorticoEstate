<?php
phpgw::import_class('controller.socommon');

include_class('controller', 'control_group', 'inc/model/');

class controller_socontrol_group extends controller_socommon
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
			self::$so = CreateObject('controller.socontrol_group');
		}
		return self::$so;
	}

	/**
	 * Function for adding a new activity to the database. Updates the activity object.
	 *
	 * @param activitycalendar_activity $activity the party to be added
	 * @return bool true if successful, false otherwise
	 */
	function add(&$control_group)
	{
		
		$control_group = $control_group->get_control_group();
		
		$sql = "INSERT INTO controller_control_group (group_name) VALUES ('$title')";
		$result = $this->db->query($sql, __LINE__,__FILE__);

		if(isset($result)) {
			// Set the new party ID
			$control_group->set_id($this->db->get_last_insert_id('controller_control_group', 'id'));
			// Forward this request to the update method
			return $this->update($control_group);
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

	function update($control_group)
	{	
		$id = intval($control_group->get_id());
			
		$values = array(
			'$group_name = ' . $this->marshal($control_group->get_group_name(), 'string')
		);
		
		//var_dump('UPDATE activity_activity SET ' . join(',', $values) . " WHERE id=$id");
		$result = $this->db->query('UPDATE controller_control_group SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);
		
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
		
		$sql = "SELECT p.* FROM controller_control_group p WHERE p.id = " . $id;
		$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
		$this->db->next_record();
		
		$control_group = new controller_control_group($this->unmarshal($this->db->f('id', true), 'int'));
		$control_group->set_group_name($this->unmarshal($this->db->f('group_name', true), 'string'));
		
		return $control_group;
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
	function get_control_group_array($start = 0, $results = 1000, $sort = null, $dir = '', $query = null, $search_option = null, $filters = array())
	{
		$results = array();
		
		//$condition = $this->get_conditions($query, $filters,$search_option);
		$order = $sort ? "ORDER BY $sort $dir ": '';
		
		//$sql = "SELECT * FROM controller_procedure WHERE $condition $order";
		$sql = "SELECT * FROM controller_control_group $order";
		$this->db->limit_query($sql, $start, __LINE__, __FILE__, $limit);
		
		while ($this->db->next_record()) {
			$control_group = new controller_control_group($this->unmarshal($this->db->f('id', true), 'int'));
			$control_group->set_group_name($this->unmarshal($this->db->f('group_name', true), 'string'));
			
			$results[] = $control_group;
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
	
	function populate(int $control_group_id, &$control_group)
	{
		/*
		if($control_group == null) {
			$control_group = new activitycalendar_activity((int) $activity_id);

			$control_group->set_title($this->unmarshal($this->db->f('title'), 'string'));
			$control_group->set_organization_id($this->unmarshal($this->db->f('organization_id'), 'int'));
			$control_group->set_group_id($this->unmarshal($this->db->f('group_id'), 'int'));
			$control_group->set_district($this->unmarshal($this->db->f('district'), 'int'));
			$control_group->set_office($this->unmarshal($this->db->f('office'), 'int'));
			$control_group->set_category($this->unmarshal($this->db->f('category'), 'int'));
			$control_group->set_state($this->unmarshal($this->db->f('state'), 'int'));
			$control_group->set_target($this->unmarshal($this->db->f('target'), 'string'));
			$control_group->set_description($this->unmarshal($this->db->f('description'), 'string'));
			$control_group->set_arena($this->unmarshal($this->db->f('arena'), 'string'));
			$control_group->set_internal_arena($this->unmarshal($this->db->f('internal_arena'), 'string'));
			$control_group->set_time($this->unmarshal($this->db->f('time'), 'string'));
			$control_group->set_last_change_date($this->unmarshal($this->db->f('last_change_date'), 'int'));
			$control_group->set_special_adaptation($this->unmarshal($this->db->f('special_adaptation', 'bool')));
			$control_group->set_secret($this->unmarshal($this->db->f('secret'), 'string'));
			
			
		}
		*/
		return $control_group;
	}
	
}
