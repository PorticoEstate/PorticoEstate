<?php
	phpgw::import_class('booking.socommon');

	class booking_sosystem_message extends booking_socommon
	{

		function __construct()
		{
			parent::__construct('bb_system_message', array(
				'id' => array('type' => 'int'),
				'created' => array('type' => 'timestamp','read_callback' => 'modify_by_timezone'),
				'title' => array('type' => 'string', 'query' => true, 'required' => true),
				'display_in_dashboard' => array('type' => 'int', 'nullable' => False, 'precision' => '4',
					'default' => 1),
				'building_id' => array('type' => 'int', 'precision' => '4'),
				'building_name' => array('type' => 'string', 'nullable' => False, 'query' => true,),
				'name' => array('type' => 'string', 'nullable' => False, 'query' => true,),
				'phone' => array('type' => 'string', 'nullable' => False, 'default' => ''),
				'email' => array('type' => 'string', 'nullable' => False, 'default' => ''),
				'message' => array('type' => 'string', 'required' => true),
				'type' => array('type' => 'string', 'default' => 'message', 'query' => true),
				'status' => array('type' => 'string', 'default' => 'NEW', 'query' => true)
				)
			);
		}

		function get_building( $id )
		{
			$this->db->limit_query("SELECT name FROM bb_building where id=" . intval($id), 0, __LINE__, __FILE__, 1);
			if (!$this->db->next_record())
			{
				return False;
			}
			return $this->db->f('name', false);
		}
	}

	class booking_sosystem_message_association extends booking_socommon
	{

		function __construct()
		{
			parent::__construct('bb_system_message_association', array(
				'id' => array('type' => 'int'),
				'building_id' => array('type' => 'int'),
				'type' => array('type' => 'string', 'required' => true),
				'status' => array('type' => 'string', 'required' => true),
			));
		}
	}