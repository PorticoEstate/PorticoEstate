<?php
phpgw::import_class('controller.socommon');

include_class('controller', 'check_item', 'inc/model/');

class controller_socheck_item extends controller_socommon
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
			self::$so = CreateObject('controller.socheck_item');
		}
		return self::$so;
	}
	
	function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count){}

	function populate(int $object_id, &$object){}
	
	function add(&$check_item)
	{
		$cols = array(
				'control_item_id',
				'status',
				'comment',
				'check_list_id'
		);
		
		$values = array(
			$this->marshal($check_item->get_control_item_id(), 'int'),
			$this->marshal($check_item->get_status(), 'bool'),
			$this->marshal($check_item->get_comment(), 'string'),
			$this->marshal($check_item->get_check_list_id(), 'int')
		);
		
		$result = $this->db->query('INSERT INTO controller_check_item (' . join(',', $cols) . ') VALUES (' . join(',', $values) . ')', __LINE__,__FILE__);

		return isset($result) ? $this->db->get_last_insert_id('controller_check_item', 'id') : 0;
	}
	
	function update($check_item){
		
		$id = $check_item->get_id();

		$values = array(
			'control_item_id = ' . $this->marshal($check_item->get_control_item_id(), 'int'),
			'status = ' . $this->marshal($check_item->get_status(), 'bool'),
			'comment = ' . $this->marshal($check_item->get_comment(), 'string'),
			'check_list_id = ' . $this->marshal($check_item->get_check_list_id(), 'int')
		);
		
		$result = $this->db->query('UPDATE controller_check_item SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);
		
		if( isset($result) ){
			return $id;	
		}else{
			return 0;
		}
	}
	
	public function get_single($check_item_id){
		$sql = "SELECT ci.*, coi.id as coi_id, coi.* ";
		$sql .= "FROM controller_check_item ci, controller_control_item coi "; 
		$sql .= "WHERE ci.id = $check_item_id ";
		$sql .= "AND ci.control_item_id=coi.id";
		
		$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
		
		if($this->db->next_record()) {
			$check_item = new controller_check_item($this->unmarshal($this->db->f('id', true), 'int'));
			$check_item->set_control_item_id($this->unmarshal($this->db->f('control_item_id', true), 'int'));
			$check_item->set_status($this->unmarshal($this->db->f('status', true), 'bool'));
			$check_item->set_comment($this->unmarshal($this->db->f('comment', true), 'string'));
			$check_item->set_check_list_id($this->unmarshal($this->db->f('check_list_id', true), 'int'));
			
			$control_item = new controller_control_item($this->unmarshal($this->db->f('coi_id', true), 'int'));
			$control_item->set_title($this->db->f('title', true), 'string');
			$control_item->set_required($this->db->f('required', true), 'string');
			$control_item->set_what_to_do($this->db->f('what_to_do', true), 'string');
			$control_item->set_how_to_do($this->db->f('how_to_do', true), 'string');
			$control_item->set_control_group_id($this->db->f('control_group_id', true), 'string');
			
			$check_item->set_control_item($control_item->toArray());
			
			return $check_item;
		}else{
			return null;
		}
	}
	
	function get_id_field_name(){}	
}
