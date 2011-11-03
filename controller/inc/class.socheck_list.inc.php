<?php
phpgw::import_class('controller.socommon');

include_class('controller', 'control', 'inc/model/');

class controller_socheck_list extends controller_socommon
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
			self::$so = CreateObject('controller.socheck_list');
		}
		return self::$so;
	}
	
	public function get_check_list(){

		$current_time = time();
	
		$buffer_in_days = 3600*24*7*5;
		
		$buffer_time = $current_time - $buffer_in_days;
		
		$sql = "SELECT p.* FROM controller_control p WHERE $current_time >= p.start_date AND p.start_date > $buffer_time";
		$this->db->query($sql);
			
		while ($this->db->next_record()) {
			$start_date = date("d.m.Y",  $this->db->f('start_date'));
			$end_date = date("d.m.Y",  $this->db->f('end_date'));
			
			$control = new controller_control($this->unmarshal($this->db->f('id', true), 'int'));

			$control->set_title($this->unmarshal($this->db->f('title', true), 'string'));
			$control->set_description($this->unmarshal($this->db->f('description', true), 'boolean'));
			$control->set_start_date($start_date);
			$control->set_end_date($end_date);
			$control->set_procedure_id($this->unmarshal($this->db->f('procedure_id', true), 'int'));
			$control->set_procedure_name($this->unmarshal($this->db->f('procedure_name', true), 'string'));
			$control->set_requirement_id($this->unmarshal($this->db->f('requirement_id', true), 'int'));
			$control->set_costresponsibility_id($this->unmarshal($this->db->f('costresponsibility_id', true), 'int'));
			$control->set_responsibility_id($this->unmarshal($this->db->f('responsibility_id', true), 'int'));
			$control->set_control_area_id($this->unmarshal($this->db->f('control_area_id', true), 'int'));
			$control->set_control_area_name($this->unmarshal($this->db->f('control_area_name', true), 'string'));
			$control->set_equipment_type_id($this->unmarshal($this->db->f('equipment_type_id', true), 'int'));
			$control->set_equipment_id($this->unmarshal($this->db->f('equipment_id', true), 'int'));
			$control->set_location_code($this->unmarshal($this->db->f('location_code', true), 'int'));
			$control->set_repeat_type($this->unmarshal($this->db->f('repeat_type', true), 'int'));
			$control->set_repeat_interval($this->unmarshal($this->db->f('repeat_interval', true), 'int'));
				
			$results[] = $control->toArray(); 
		}
				
		return $results;
	}
	
	function get_check_lists_for_control($control_id){
		
		$sql = "SELECT ci.*, cil.order_nr FROM controller_control_item ci, controller_control_item_list cil WHERE cil.control_id = $control_id AND cil.control_item_id = ci.id ORDER BY cil.order_nr;";
		$this->db->query($sql, 0, __LINE__, __FILE__);
		$this->db->next_record();
		
		$control_item_list = new controller_control_item_list($this->unmarshal($this->db->f('id', true), 'int'));
		$control_item_list->set_control_id($this->unmarshal($this->db->f('control_id', true), 'int'));
		$control_item_list->set_control_item_id($this->unmarshal($this->db->f('control_item_id', true), 'int'));
		$control_item_list->set_order_nr($this->unmarshal($this->db->f('order_nr', true), 'int'));
		
		return $control_item_list;
		
	}
	
	function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count){}

	function populate(int $object_id, &$object){}
	
	function add(&$object){}
	
	function update($object){}
	
	function get_id_field_name(){}	
}
