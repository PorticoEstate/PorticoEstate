<?php
	class socatalog_contact_addr_type
	{
		function __construct()
		{
			$this->db = &$GLOBALS['phpgw']->db;
		}
		
		function select_catalog()
		{
			$addr_type = CreateObject('phpgwapi.contact_addr_type');
			$addr_type->add_select('addr_type_id');
			$addr_type->add_select('addr_description');
			$sql = $addr_type->select();
			$this->db->query($sql,__LINE__,__FILE__);
			while ($this->db->next_record())
			{
				$return_fields[] = $this->db->Record;
			}
			return $return_fields;
		}
		
		function insert($fields)
		{
			$comm_type = CreateObject('phpgwapi.contact_addr_type');
			return $comm_type->insert($fields, PHPGW_SQL_RUN_SQL);
		}
		
		function delete($key)
		{
			$comm_type = CreateObject('phpgwapi.contact_addr_type');
			return $comm_type->delete('addr_type_id ='. $key, PHPGW_SQL_RUN_SQL);
		}
		
		function update($key, $fields)
		{
			$comm_type = CreateObject('phpgwapi.contact_addr_type');
			return $comm_type->update($fields, 'addr_type_id ='. $key, PHPGW_SQL_RUN_SQL);
		}

		function get_record($key)
		{
			$comm_type = CreateObject('phpgwapi.contact_addr_type');
			$comm_type->add_select('addr_type_id');
			$comm_type->add_select('addr_description');
			$comm_type->set_criteria('addr_type_id='. $key);
			$sql = $comm_type->select();
			$this->db->query($sql,__LINE__,__FILE__);
			while ($this->db->next_record())
			{
				$return_fields[] = $this->db->Record;
			}
			return $return_fields;
		}
	}
