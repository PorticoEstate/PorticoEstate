<?php
phpgw::import_class('controller.socommon');

//include_class('controller', 'control_group_list', 'inc/model/');

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
			
			$result[] = array( 
							'title' => $this->db->f('title'), 
							'start_date' => $start_date
						); 
		}
	
		return $result;
	}
	
	function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count){}

	function populate(int $object_id, &$object){}
	
	function add(&$object){}
	
	function update($object){}
	
	function get_id_field_name(){}	
}
