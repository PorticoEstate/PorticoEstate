<?php
phpgw::import_class('controller.socommon');

include_class('controller', 'control', 'inc/model/');

class controller_socontrol extends controller_socommon
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
			self::$so = CreateObject('controller.socontrol');
		}
		return self::$so;
	}

	/**
	 * Function for adding a new activity to the database. Updates the activity object.
	 *
	 * @param activitycalendar_activity $activity the party to be added
	 * @return bool true if successful, false otherwise
	 */
	function add(&$control)
	{
		
		$title = $control->get_title();
		
		var_dump("i add");
		
		$sql = "INSERT INTO controller_control (title) VALUES ('$title')";
		$result = $this->db->query($sql, __LINE__,__FILE__);

		if(isset($result)) {
			// Set the new party ID
			$control->set_id($this->db->get_last_insert_id('controller_control', 'id'));
			// Forward this request to the update method
			return $this->update($control);
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
	
	
	function update($control)
	{	
		
		$id = intval($control->get_id());
			
		$values = array(
			'title = ' . $this->marshal($control->get_title(), 'string'),
			'description = ' . $this->marshal($control->get_description(), 'string'),
			'start_date = ' . $this->marshal($control->get_start_date(), 'int'),
			'end_date = ' . $this->marshal($control->get_end_date(), 'int'),
			'repeat_date = ' . $this->marshal($control->get_repeat_date(), 'int'),
			'repeat_day = ' . $this->marshal($control->get_repeat_day(), 'int'),
			'repeat_interval = ' . $this->marshal($control->get_repeat_interval(), 'int'),
		
		);
		
		//var_dump('UPDATE activity_activity SET ' . join(',', $values) . " WHERE id=$id");
		$result = $this->db->query('UPDATE controller_control SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);
		
		return isset($result);
	}
	
	
	function get_procedure_list(){
		
		
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
	
	function populate(int $control_id, &$control)
	{
		/*
		if($control == null) {
			$control = new activitycalendar_activity((int) $activity_id);

			$control->set_title($this->unmarshal($this->db->f('title'), 'string'));
			$control->set_organization_id($this->unmarshal($this->db->f('organization_id'), 'int'));
			$control->set_group_id($this->unmarshal($this->db->f('group_id'), 'int'));
			$control->set_district($this->unmarshal($this->db->f('district'), 'int'));
			$control->set_office($this->unmarshal($this->db->f('office'), 'int'));
			$control->set_category($this->unmarshal($this->db->f('category'), 'int'));
			$control->set_state($this->unmarshal($this->db->f('state'), 'int'));
			$control->set_target($this->unmarshal($this->db->f('target'), 'string'));
			$control->set_description($this->unmarshal($this->db->f('description'), 'string'));
			$control->set_arena($this->unmarshal($this->db->f('arena'), 'string'));
			$control->set_internal_arena($this->unmarshal($this->db->f('internal_arena'), 'string'));
			$control->set_time($this->unmarshal($this->db->f('time'), 'string'));
			$control->set_last_change_date($this->unmarshal($this->db->f('last_change_date'), 'int'));
			$control->set_special_adaptation($this->unmarshal($this->db->f('special_adaptation', 'bool')));
			$control->set_secret($this->unmarshal($this->db->f('secret'), 'string'));
			
			
		}
		*/
		return $control;
	}
	
}
