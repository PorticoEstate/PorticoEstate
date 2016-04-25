<?php
	class socatalog_contact_comm_type
	{
		function __construct()
		{
			$this->db = &$GLOBALS['phpgw']->db;
		}
		
		function select_catalog()
		{
			$comm_type = CreateObject('phpgwapi.contact_comm_type');
			$comm_type->add_select('comm_type_id');
			$comm_type->add_select('comm_type_description');
			$sql = $comm_type->select();
			$this->db->query($sql,__LINE__,__FILE__);
			while ($this->db->next_record())
			{
				$return_fields[] = $this->db->Record;
			}
			return $return_fields;
		}
		
		function insert($fields)
		{
			$comm_type = CreateObject('phpgwapi.contact_comm_type');
			return $comm_type->insert($fields, PHPGW_SQL_RUN_SQL);
		}
		
		function delete($key)
		{
			$comm_type = CreateObject('phpgwapi.contact_comm_type');
			return $comm_type->delete('comm_type_id ='. $key, PHPGW_SQL_RUN_SQL);
		}
		
		function update($key, $fields)
		{
			$comm_type = CreateObject('phpgwapi.contact_comm_type');
			return $comm_type->update($fields, 'comm_type_id ='. $key, PHPGW_SQL_RUN_SQL);
		}

		function get_record($key)
		{
			$comm_type = CreateObject('phpgwapi.contact_comm_type');
			$comm_type->add_select('comm_type_id');
			$comm_type->add_select('comm_type_description');
			$comm_type->set_criteria('comm_type_id='. $key);
			$sql = $comm_type->select();
			$this->db->query($sql,__LINE__,__FILE__);
			while ($this->db->next_record())
			{
				$return_fields[] = $this->db->Record;
			}
			return $return_fields;
		}
	}
