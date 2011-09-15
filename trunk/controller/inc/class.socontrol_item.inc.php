<?php
phpgw::import_class('controller.socommon');

include_class('controller', 'control_item', 'inc/model/');

class controller_socontrol_item extends controller_socommon
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
			self::$so = CreateObject('controller.socontrol_item');
		}
		return self::$so;
	}

	/**
	 * Function for adding a new activity to the database. Updates the activity object.
	 *
	 * @param activitycalendar_activity $activity the party to be added
	 * @return bool true if successful, false otherwise
	 */
	function add(&$control_item)
	{
		
		$title = $control_item->get_title();
		
		$sql = "INSERT INTO controller_control_item (title) VALUES ('$title')";
		$result = $this->db->query($sql, __LINE__,__FILE__);

		if(isset($result)) {
			// Set the new party ID
			$control_item->set_id($this->db->get_last_insert_id('controller_control_item', 'id'));
			// Forward this request to the update method
			return $this->update($control_item);
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

	function update($control_item)
	{	
		$id = intval($control_item->get_id());
			
		$values = array(
			'$purpose = ' . $this->marshal($control_item->get_purpose(), 'string'),
			'responsibility = ' . $this->marshal($control_item->get_responsibility(), 'int'),
			'description = ' . $this->marshal($control_item->get_description(), 'int'),
			'reference = ' . $this->marshal($control_item->get_reference(), 'int'),
			'attachment = ' . $this->marshal($control_item->get_attachment(), 'int')
		);
		
		//var_dump('UPDATE activity_activity SET ' . join(',', $values) . " WHERE id=$id");
		$result = $this->db->query('UPDATE controller_control_item SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);
		
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
		
		$sql = "SELECT p.* FROM controller_control_item p WHERE p.id = " . $id;
		$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
		$this->db->next_record();
		
		$control_item = new controller_control_item($this->unmarshal($this->db->f('id', true), 'int'));
		$control_item->set_title($this->unmarshal($this->db->f('title', true), 'string'));
		$control_item->set_purpose($this->unmarshal($this->db->f('purpose', true), 'string'));
		$control_item->set_responsibility($this->unmarshal($this->db->f('responsibility', true), 'string'));
		$control_item->set_description($this->unmarshal($this->db->f('description', true), 'string'));
		$control_item->set_reference($this->unmarshal($this->db->f('reference', true), 'string'));
		$control_item->set_attachment($this->unmarshal($this->db->f('attachment', true), 'string'));
		
		return $control_item;
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
			$control_item->set_what_to_desc($this->unmarshal($this->db->f('what_to_desc', true), 'string'));
			$control_item->set_how_to_desc($this->unmarshal($this->db->f('how_to_desc', true), 'string'));
			$control_item->set_control_group_id($this->unmarshal($this->db->f('control_group_id', true), 'int'));
			$control_item->set_control_type_id($this->unmarshal($this->db->f('control_type_id', true), 'int'));
			
			$results[] = $control_item;
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
	
	function populate(int $control_item_id, &$control_item)
	{
		/*
		if($control_item == null) {
			$control_item = new activitycalendar_activity((int) $activity_id);

			$control_item->set_title($this->unmarshal($this->db->f('title'), 'string'));
			$control_item->set_organization_id($this->unmarshal($this->db->f('organization_id'), 'int'));
			$control_item->set_group_id($this->unmarshal($this->db->f('group_id'), 'int'));
			$control_item->set_district($this->unmarshal($this->db->f('district'), 'int'));
			$control_item->set_office($this->unmarshal($this->db->f('office'), 'int'));
			$control_item->set_category($this->unmarshal($this->db->f('category'), 'int'));
			$control_item->set_state($this->unmarshal($this->db->f('state'), 'int'));
			$control_item->set_target($this->unmarshal($this->db->f('target'), 'string'));
			$control_item->set_description($this->unmarshal($this->db->f('description'), 'string'));
			$control_item->set_arena($this->unmarshal($this->db->f('arena'), 'string'));
			$control_item->set_internal_arena($this->unmarshal($this->db->f('internal_arena'), 'string'));
			$control_item->set_time($this->unmarshal($this->db->f('time'), 'string'));
			$control_item->set_last_change_date($this->unmarshal($this->db->f('last_change_date'), 'int'));
			$control_item->set_special_adaptation($this->unmarshal($this->db->f('special_adaptation', 'bool')));
			$control_item->set_secret($this->unmarshal($this->db->f('secret'), 'string'));
			
			
		}
		*/
		return $control_item;
	}
	
}
