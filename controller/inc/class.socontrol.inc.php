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
			'control_area_id = ' . $this->marshal($control->get_control_area_id()),
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
		if(!$extended_info)
		{
			$ret = 'id';
		}
		else
		{
			$ret = array
			(
				'table'			=> 'control', // alias
				'field'			=> 'id',
				'translated'	=> 'id'
			);
		}
		
		return $ret;
	}

	protected function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count)
	{
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
					$like_clauses[] = "controller_control.title $this->like $like_pattern";
					$like_clauses[] = "controller_control.description $this->like $like_pattern";
					break;
			}
			
			if(count($like_clauses))
			{
				$clauses[] = '(' . join(' OR ', $like_clauses) . ')';
			}
		}
		//var_dump($filters);
		if(isset($filters[$this->get_id_field_name()]))
		{
			$filter_clauses[] = "controller_control.id = {$this->marshal($filters[$this->get_id_field_name()],'int')}";
		}
		if(isset($filters['control_areas']))
		{
			$filter_clauses[] = "controller_control.control_area_id = {$this->marshal($filters['control_areas'],'int')}";
		}
		
		if(count($filter_clauses))
		{
			$clauses[] = join(' AND ', $filter_clauses);
		}
		
		
		$condition =  join(' AND ', $clauses);

		$tables = "controller_control";
		//$joins = " {$this->left_join} rental_document_types ON (rental_document.type_id = rental_document_types.id)";
		$joins = " {$this->left_join} controller_control_area ON (controller_control.control_area_id = controller_control_area.id)";
		$joins .= " {$this->left_join} controller_procedure ON (controller_control.procedure_id = controller_procedure.id)";
		
		if($return_count)
		{
			$cols = 'COUNT(DISTINCT(controller_control.id)) AS count';
		}
		else
		{
			$cols = 'controller_control.id, controller_control.title, controller_control.description, controller_control.start_date, controller_control.end_date, procedure_id, control_area_id, requirement_id, costresponsibility_id, responsibility_id, equipment_type_id, equipment_id, location_code, repeat_type, repeat_interval, enabled, controller_control_area.title AS control_area_name, controller_procedure.title AS procedure_name ';
		}
		
		$dir = $ascending ? 'ASC' : 'DESC';
		if($sort_field == 'title')
		{
			$sort_field = 'controller_control.title';
		}
		else if($sort_field == 'id')
		{
			$sort_field = 'controller_control.id';
		}
		$order = $sort_field ? "ORDER BY {$this->marshal($sort_field, 'field')} $dir ": '';
		
		return "SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}";
		
	}
	
	function populate(int $control_id, &$control)
	{
		if($control == null) {
			$control = new controller_control((int) $control_id);

			$control->set_title($this->unmarshal($this->db->f('title', true), 'string'));
			$control->set_description($this->unmarshal($this->db->f('description', true), 'boolean'));
			$control->set_start_date($this->unmarshal($this->db->f('start_date', true), 'int'));
			$control->set_end_date($this->unmarshal($this->db->f('end_date', true), 'int'));
			$control->set_procedure_id($this->unmarshal($this->db->f('procedure_id', true), 'int'));
			$control->set_procedure_name($this->unmarshal($this->db->f('procedure_name', true), 'string'));
			$control->set_requirement_id($this->unmarshal($this->db->f('requirement_id', true), 'int'));
			$control->set_costresponsibility_id($this->unmarshal($this->db->f('costresponsibility_id', true), 'int'));
			$control->set_responsibility_id($this->unmarshal($this->db->f('responsibility_id', true), 'int'));
			$control->set_control_area_id($this->unmarshal($this->db->f('control_area_id', true), 'int'));
			$control->set_control_area_name($this->unmarshal($this->db->f('control_area_name', true), 'string'));
//			$control->set_control_group_id($this->unmarshal($this->db->f('control_group_id', true), 'int'));
			$control->set_equipment_type_id($this->unmarshal($this->db->f('equipment_type_id', true), 'int'));
			$control->set_equipment_id($this->unmarshal($this->db->f('equipment_id', true), 'int'));
			$control->set_location_code($this->unmarshal($this->db->f('location_code', true), 'int'));
			$control->set_repeat_type($this->unmarshal($this->db->f('repeat_type', true), 'int'));
			$control->set_repeat_interval($this->unmarshal($this->db->f('repeat_interval', true), 'int'));
		}
		
		return $control;
	}
	
	/**
	 * Get single control
	 * 
	 * @param	$id	id of the control to return
	 * @return a controller_control
	 */
	function get_single($id)
	{
		$id = (int)$id;
		
		$joins = " {$this->left_join} controller_control_area ON (c.control_area_id = controller_control_area.id)";
		$joins .= " {$this->left_join} controller_procedure ON (c.procedure_id = controller_procedure.id)";
		
		$sql = "SELECT c.*, controller_control_area.title AS control_area_name, controller_procedure.title AS procedure_name FROM controller_control c {$joins} WHERE c.id = " . $id;
		$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
		$this->db->next_record();
		
		$control = new controller_control((int) $id);

		$control->set_title($this->unmarshal($this->db->f('title', true), 'string'));
		$control->set_description($this->unmarshal($this->db->f('description', true), 'boolean'));
		$control->set_start_date($this->unmarshal($this->db->f('start_date', true), 'int'));
		$control->set_end_date($this->unmarshal($this->db->f('end_date', true), 'int'));
		$control->set_procedure_id($this->unmarshal($this->db->f('procedure_id', true), 'int'));
		$control->set_procedure_name($this->unmarshal($this->db->f('procedure_name', true), 'string'));
		$control->set_requirement_id($this->unmarshal($this->db->f('requirement_id', true), 'int'));
		$control->set_costresponsibility_id($this->unmarshal($this->db->f('costresponsibility_id', true), 'int'));
		$control->set_responsibility_id($this->unmarshal($this->db->f('responsibility_id', true), 'int'));
		$control->set_control_area_id($this->unmarshal($this->db->f('control_area_id', true), 'int'));
		$control->set_control_area_name($this->unmarshal($this->db->f('control_area_name', true), 'string'));
//			$control->set_control_group_id($this->unmarshal($this->db->f('control_group_id', true), 'int'));
		$control->set_equipment_type_id($this->unmarshal($this->db->f('equipment_type_id', true), 'int'));
		$control->set_equipment_id($this->unmarshal($this->db->f('equipment_id', true), 'int'));
		$control->set_location_code($this->unmarshal($this->db->f('location_code', true), 'int'));
		$control->set_repeat_type($this->unmarshal($this->db->f('repeat_type', true), 'int'));
		$control->set_repeat_interval($this->unmarshal($this->db->f('repeat_interval', true), 'int'));
		
		return $control;
	}
	
/*		public function populate($control){
						
			$control->set_title(phpgw::get_var('title', 'string'));
			$control->set_description(phpgw::get_var('description', 'string'));
			$control->set_start_date( strtotime( phpgw::get_var('start_date_hidden', 'int')));
			$control->set_end_date( strtotime( phpgw::get_var('end_date_hidden', 'int')));
			$control->set_repeat_type( phpgw::get_var('repeat_type', 'string'));
			$control->set_repeat_interval( phpgw::get_var('repeat_interval', 'string'));
			$control->set_procedure_id( phpgw::get_var('procedure_id', 'int'));
			$control->set_enabled( true );
			
			return $control;
			
		}*/
	
	
}
