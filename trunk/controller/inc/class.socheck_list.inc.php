<?php
phpgw::import_class('controller.socommon');

include_class('controller', 'check_list', 'inc/model/');

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
	
	function get_check_list_for_control($control_id){
		$sql = "SELECT cl.id as check_list_id, cl.*, ci.id as check_item_id, ci.* FROM controller_check_list cl, controller_check_item ci WHERE cl.control_id = $control_id AND cl.id = ci.check_list_id ORDER BY cl.id;";
		$this->db->query($sql);
		
		$check_list_id = 0;
		while ($this->db->next_record()) {
			
			print "ID: " . $this->db->f('id', true) . "<br>";
			
			$check_item = new controller_check_item($this->unmarshal($this->db->f('check_item_id', true), 'int'));
			$check_item->set_control_item_id($this->unmarshal($this->db->f('control_item_id', true), 'int'));
			$check_item->set_status($this->unmarshal($this->db->f('status', true), 'bool'));
			$check_item->set_comment($this->unmarshal($this->db->f('comment', true), 'string'));
			$check_item->set_check_list_id($this->unmarshal($this->db->f('check_list_id', true), 'int'));
			
			if( $this->db->f('id', true) != $check_list_id){
				$check_list = new controller_check_list($this->unmarshal($this->db->f('check_list_id', true), 'int'));	
			}else{
				$check_list_array[] = $check_list;	
			}
			
			$check_list->set_control_id($this->unmarshal($this->db->f('control_id', true), 'int'));
			$check_list->set_status($this->unmarshal($this->db->f('status', true), 'bool'));
			$check_list->set_comment($this->unmarshal($this->db->f('comment', true), 'string'));
			$check_list->set_deadline($this->unmarshal($this->db->f('deadline', true), 'int'));			
			
			$check_list_id =  $this->unmarshal($this->db->f('id', true), 'int');
			$check_items_array = array();
			
		
			$check_items_array[] = $check_item;
			
			$check_list->set_check_item_array($check_items_array);
			
		}
		
		foreach($check_list_array as $check_list){
			echo "Skriver ut check_list!"."<br>";
			
			echo "check list id: " . $check_list->get_id()."<br>";
			echo "status: " . $check_list->get_status()."<br>";
			echo "comment: " . $check_list->get_comment()."<br>";
			echo "deadline: " . $check_list->get_deadline()."<br>";
			echo "Check_item_array: " . "<br>";
			
			foreach($check_list->get_check_item_array() as $check_item){
				echo "check item id:  " . $check_item->get_id()."<br>";
				echo "status:  " . $check_item->get_status()."<br>";	
				echo "control_item_id:  " . $check_item->get_control_item_id()."<br>";
				echo "comment:  " . $check_item->get_comment()."<br>";
				echo "check_list_id:  " . $check_item->get_check_list_id()."<br>";
			}	
			echo "<br>";
		}
		
		//$check_list->set_check_item_array($check_items_array);
				
		//return $check_list;
	}
	
	function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count){}

	function populate(int $object_id, &$object){}
	
	function add(&$check_list)
	{
		$cols = array(
				'control_id',
				'status',
				'comment',
				'deadline'
		);
		
		$values = array(
			$this->marshal($check_list->get_control_id(), 'int'),
			$this->marshal($check_list->get_status(), 'bool'),
			$this->marshal($check_list->get_comment(), 'string'),
			$this->marshal($check_list->get_deadline(), 'int')
		);
		
		$result = $this->db->query('INSERT INTO controller_check_list (' . join(',', $cols) . ') VALUES (' . join(',', $values) . ')', __LINE__,__FILE__);

		return isset($result) ? $this->db->get_last_insert_id('controller_check_list', 'id') : 0;
	}
	
	function update($object){}
	
	function get_id_field_name(){}	
}
