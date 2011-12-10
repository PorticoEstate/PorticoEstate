<?php
phpgw::import_class('controller.socommon');

include_class('controller', 'control_group_list', 'inc/model/');
include_class('controller', 'control_group', 'inc/model/');

class controller_socontrol_group_list extends controller_socommon
{
	protected static $so;

	/**
	 * Get a static reference to the storage object associated with this model object
	 *
	 * @return controller_socontrol_group the storage object
	 */
	public static function get_instance()
	{
		if (self::$so == null) {
			self::$so = CreateObject('controller.socontrol_group_list');
		}
		return self::$so;
	}

	/**
	 * Function for adding a new control group to the database.
	 *
	 * @param controller_control_group $control_group the control group to be added
	 * @return int id of the new control group object
	 */
	function add(&$control_group_list)
	{
		$cols = array(
				'control_id',
				'control_group_id',
				'order_nr'
		);
			
		$values = array(
			$this->marshal($control_group_list->get_control_id(), 'int'),
			$this->marshal($control_group_list->get_control_group_id(), 'int'),
			$this->marshal($control_group_list->get_order_nr(), 'int')
		);
		
		$result = $this->db->query('INSERT INTO controller_control_group_list (' . join(',', $cols) . ') VALUES (' . join(',', $values) . ')', __LINE__,__FILE__);
		
		if(isset($result)) {
			// Get the new control group ID and return it
			return $this->db->get_last_insert_id('controller_control_group_list', 'id');
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

	function update($control_group_list)
	{	
		$id = intval($control_group_list->get_id());
			
		$values = array(
			'control_id = ' . $this->marshal($control_group_list->get_control_id(), 'string'),
			'control_group_id = '. $this->marshal($control_group_list->get_control_group_id(), 'int'),
			'order_nr = ' . $this->marshal($control_group_list->get_order_nr(), 'int')
		);
		
		//var_dump('UPDATE activity_activity SET ' . join(',', $values) . " WHERE id=$id");
		$result = $this->db->query('UPDATE controller_control_group_list SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);
		
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
		
		$sql = "SELECT p.* FROM controller_control_group_list p WHERE p.id = " . $id;
		$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);

		if($this->db->next_record()){
			$control_group_list = new controller_control_group_list($this->unmarshal($this->db->f('id', true), 'int'));
			$control_group_list->set_control_id($this->unmarshal($this->db->f('control_id', true), 'int'));
			$control_group_list->set_control_group_id($this->unmarshal($this->db->f('control_group_id'), 'int'));
			$control_group_list->set_order_nr($this->unmarshal($this->db->f('order_nr'), 'int'));

			return $control_group_list; 
		}
		else
		{
			return null;
		}
	}
	
	function get_single_2($control_id, $control_group_id)
	{
		$sql = "SELECT p.* FROM controller_control_group_list p WHERE p.control_id=" . $control_id . " AND p.control_group_id=" . $control_group_id;
		$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
		
		if($this->db->next_record()){
			$control_group_list = new controller_control_group_list($this->unmarshal($this->db->f('id', true), 'int'));
			$control_group_list->set_control_id($this->unmarshal($this->db->f('control_id', true), 'int'));
			$control_group_list->set_control_group_id($this->unmarshal($this->db->f('control_group_id'), 'int'));
			$control_group_list->set_order_nr($this->unmarshal($this->db->f('order_nr'), 'int'));

			return $control_group_list; 
		}
		else
		{
			return null;
		}
	}
	
	function delete($control_id, $control_group_id)
	{		
		$result = $this->db->query("DELETE FROM controller_control_group_list WHERE control_id = $control_id AND control_group_id = $control_group_id");
				
		return isset($result);
	}
	
	function delete_control_groups($control_id)
	{		
		$result = $this->db->query("DELETE FROM controller_control_group_list WHERE control_id = $control_id");
				
		return isset($result);
	}
	
	function get_control_groups_by_control_id($control_id)
	{
		$this->db->query("SELECT cg.*, cgl.order_nr FROM controller_control_group_list cgl, controller_control_group cg WHERE cgl.control_id=$control_id AND cgl.control_group_id=cg.id ORDER BY cgl.order_nr", __LINE__, __FILE__);

		$control_group_list = array();
		
		while($this->db->next_record())
		{	
			$control_group = new controller_control_group($this->unmarshal($this->db->f('id', true), 'int'));
			$control_group->set_group_name($this->unmarshal($this->db->f('group_name', true), 'string'));
			$control_group->set_procedure_id($this->unmarshal($this->db->f('procedure_id'), 'int'));
			$control_group->set_control_area_id($this->unmarshal($this->db->f('control_area_id'), 'int'));
			$control_group->set_building_part_id($this->unmarshal($this->db->f('building_part_id'), 'int'));
				
			$control_group_list[] = $control_group;
		}
		
		return $control_group_list;
	}
	
	
	protected function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count){}
	
	function get_id_field_name($extended_info = false){}
	
	function populate(int $control_group_id, &$control_group)
	{
		/*
		if($control_group == null) {
			$control_group = new controller_control_group((int) $control_group_id);

			$control_group->set_group_name($this->unmarshal($this->db->f('group_name'), 'string'));
			$control_group->set_procedure_id($this->unmarshal($this->db->f('procedure_id'), 'int'));
			$control_group->set_procedure_name($this->unmarshal($this->db->f('procedure_title'), 'string'));
			$control_group->set_control_area_id($this->unmarshal($this->db->f('control_area_id'), 'int'));
			$control_group->set_control_area_name($this->unmarshal($this->db->f('control_area_name'), 'string'));
			$control_group->set_building_part_id($this->unmarshal($this->db->f('building_part_id'), 'int'));
			$control_group->set_building_part_descr($this->unmarshal($this->db->f('building_part_descr'), 'string'));
		}
		//var_dump($control_group);
		return $control_group;
		*/
	}
	
}
