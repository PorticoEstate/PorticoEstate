<?php
	class socatalog_contact_comm_descr
	{
		function __construct()
		{
			$this->db = &$GLOBALS['phpgw']->db;
		}
		
	
		function select_catalog()
		{
			$comm_type = CreateObject('phpgwapi.contact_comm_descr');
			$comm_type->add_select('comm_descr_id');
			$comm_type->add_select('comm_type');
			$comm_type->add_select('comm_description');
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
			$comm_type = CreateObject('phpgwapi.contact_comm_descr');
			return $comm_type->insert($fields, PHPGW_SQL_RUN_SQL);
		}
		
		function delete($key)
		{
			$comm_type = CreateObject('phpgwapi.contact_comm_descr');
			return $comm_type->delete('comm_descr_id ='. $key, PHPGW_SQL_RUN_SQL);
		}
		
		function update($key, $fields)
		{
			$comm_type = CreateObject('phpgwapi.contact_comm_descr');
			return $comm_type->update($fields, 'comm_descr_id ='. $key, PHPGW_SQL_RUN_SQL);
		}

		function get_record($key)
		{
			$comm_type = CreateObject('phpgwapi.contact_comm_descr');
			$comm_type->add_select('comm_type');
			$comm_type->add_select('comm_description');
			$comm_type->set_criteria('comm_descr_id='. $key);
			$sql = $comm_type->select();
			$this->db->query($sql,__LINE__,__FILE__);
			while ($this->db->next_record())
			{
				$return_fields[] = $this->db->Record;
			}
			return $return_fields;
		}

		function select_catalog_types()
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
	}
