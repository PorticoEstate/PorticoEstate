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
	 * Function for adding a new control to the database. Updates the control object.
	 *
	 * @param activitycalendar_activity $activity the party to be added
	 * @return bool true if successful, false otherwise
	 */
	function add(&$control)
	{
		
		$title = $control->get_title();
		
		$sql = "INSERT INTO controller_control (title) VALUES ('$title')";
		$result = $this->db->query($sql, __LINE__,__FILE__);

		if(isset($result)) {
			
			// Set the new control ID
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
			'repeat_type = ' . $this->marshal($control->get_repeat_type(), 'string'),
			'repeat_interval = ' . $this->marshal($control->get_repeat_interval(), 'string'),
			'procedure_id = ' . $this->marshal($control->get_procedure_id(), 'int')
		);
		
		$result = $this->db->query('UPDATE controller_control SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);
		
		if( isset($result) ){
			return $id;	
		}else{
			return 0;
		}
				
		// Kommenterte denne ut midlertidig. 
		//Trenger id-en som ble lagret når controllen blir lagret. 
		//return isset($result);
	}
	
	
	
	
	
	
	
	function get_procedure_list(){
		
		
	}
	
	
	function get_id_field_name($extended_info = false)
	{
		
	}

	protected function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count)
	{
		
		
	}
	
	function populate(int $control_id, &$control)
	{
	
	}
	
	
}
