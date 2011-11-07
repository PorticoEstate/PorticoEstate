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
	
	
	function update($object){
		$values = array(
			'control_item_id = ' . $this->marshal($check_item->set_control_item_id(), 'int'),
			'status = ' . $this->marshal($check_item->set_status(), 'string'),
			'comment = ' . $this->marshal($check_item->set_comment(), 'string'),
			'check_list_id = ' . $this->marshal($check_item->set_check_list_id(), 'int')
		);
		
		$result = $this->db->query('UPDATE controller_check_item SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);
		
		if( isset($result) ){
			return $id;	
		}else{
			return 0;
		}
	}
	
	function get_id_field_name(){}	
}
