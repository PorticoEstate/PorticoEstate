<?php
phpgw::import_class('controller.socommon');

include_class('controller', 'procedure', 'inc/model/');

class controller_soprocedure extends controller_socommon
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
			self::$so = CreateObject('controller.soprocedure');
		}
		return self::$so;
	}

	/**
	 * Function for adding a new activity to the database. Updates the activity object.
	 *
	 * @param activitycalendar_activity $activity the party to be added
	 * @return bool true if successful, false otherwise
	 */
	function add(&$procedure)
	{
		
		$title = $procedure->get_title();
		
		$sql = "INSERT INTO controller_procedure (title) VALUES ('$title')";
		$result = $this->db->query($sql, __LINE__,__FILE__);

		if(isset($result)) {
			// Set the new party ID
			$procedure->set_id($this->db->get_last_insert_id('controller_procedure', 'id'));
			// Forward this request to the update method
			return $this->update($procedure);
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

	function update($procedure)
	{	
		$id = intval($procedure->get_id());
			
		$values = array(
			'$purpose = ' . $this->marshal($procedure->get_purpose(), 'string'),
			'responsibility = ' . $this->marshal($procedure->get_responsibility(), 'int'),
			'description = ' . $this->marshal($procedure->get_description(), 'int'),
			'reference = ' . $this->marshal($procedure->get_reference(), 'int'),
			'attachment = ' . $this->marshal($procedure->get_attachment(), 'int')
		);
		
		//var_dump('UPDATE activity_activity SET ' . join(',', $values) . " WHERE id=$id");
		$result = $this->db->query('UPDATE controller_procedure SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);
		
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
		
		$sql = "SELECT p.* FROM controller_procedure p WHERE p.id = " . $id;
		$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
		$this->db->next_record();
		
		$procedure = new controller_procedure($this->unmarshal($this->db->f('id', true), 'int'));
		$procedure->set_title($this->unmarshal($this->db->f('title', true), 'string'));
		$procedure->set_purpose($this->unmarshal($this->db->f('purpose', true), 'string'));
		$procedure->set_responsibility($this->unmarshal($this->db->f('responsibility', true), 'string'));
		$procedure->set_description($this->unmarshal($this->db->f('description', true), 'string'));
		$procedure->set_reference($this->unmarshal($this->db->f('reference', true), 'string'));
		$procedure->set_attachment($this->unmarshal($this->db->f('attachment', true), 'string'));
		
		return $procedure;
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
	function get_procedure_array($start = 0, $results = 1000, $sort = null, $dir = '', $query = null, $search_option = null, $filters = array())
	{
		$results = array();
		
		//$condition = $this->get_conditions($query, $filters,$search_option);
		$order = $sort ? "ORDER BY $sort $dir ": '';
		
		//$sql = "SELECT * FROM controller_procedure WHERE $condition $order";
		$sql = "SELECT * FROM controller_procedure $order";
		$this->db->limit_query($sql, $start, __LINE__, __FILE__, $limit);
		
		while ($this->db->next_record()) {
			$procedure = new controller_procedure($this->unmarshal($this->db->f('id', true), 'int'));
			$procedure->set_title($this->unmarshal($this->db->f('title', true), 'string'));
			$procedure->set_purpose($this->unmarshal($this->db->f('purpose', true), 'string'));
			$procedure->set_responsibility($this->unmarshal($this->db->f('responsibility', true), 'string'));
			$procedure->set_description($this->unmarshal($this->db->f('description', true), 'string'));
			$procedure->set_reference($this->unmarshal($this->db->f('reference', true), 'string'));
			$procedure->set_attachment($this->unmarshal($this->db->f('attachment', true), 'string'));
			
			$results[] = $procedure;
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
	
	function populate(int $procedure_id, &$procedure)
	{
		/*
		if($procedure == null) {
			$procedure = new activitycalendar_activity((int) $activity_id);

			$procedure->set_title($this->unmarshal($this->db->f('title'), 'string'));
			$procedure->set_organization_id($this->unmarshal($this->db->f('organization_id'), 'int'));
			$procedure->set_group_id($this->unmarshal($this->db->f('group_id'), 'int'));
			$procedure->set_district($this->unmarshal($this->db->f('district'), 'int'));
			$procedure->set_office($this->unmarshal($this->db->f('office'), 'int'));
			$procedure->set_category($this->unmarshal($this->db->f('category'), 'int'));
			$procedure->set_state($this->unmarshal($this->db->f('state'), 'int'));
			$procedure->set_target($this->unmarshal($this->db->f('target'), 'string'));
			$procedure->set_description($this->unmarshal($this->db->f('description'), 'string'));
			$procedure->set_arena($this->unmarshal($this->db->f('arena'), 'string'));
			$procedure->set_internal_arena($this->unmarshal($this->db->f('internal_arena'), 'string'));
			$procedure->set_time($this->unmarshal($this->db->f('time'), 'string'));
			$procedure->set_last_change_date($this->unmarshal($this->db->f('last_change_date'), 'int'));
			$procedure->set_special_adaptation($this->unmarshal($this->db->f('special_adaptation', 'bool')));
			$procedure->set_secret($this->unmarshal($this->db->f('secret'), 'string'));
			
			
		}
		*/
		return $procedure;
	}
	
}
