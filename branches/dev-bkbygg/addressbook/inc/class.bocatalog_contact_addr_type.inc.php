<?php
	class bocatalog_contact_addr_type
	{
		function __construct()
		{
			$this->so = CreateObject('addressbook.socatalog_contact_addr_type');
		}
		
		function select_catalog()
		{
			return $this->so->select_catalog();
		}
		
		function insert($fields)
		{
			return $this->so->insert($fields);
		}
		
		function delete($key)
		{
			return $this->so->delete($key);
		}
		
		function update($key, $fields)
		{
			$this->so->update($key, $fields);
		}

		function get_record($key)
		{
			return $this->so->get_record($key);
		}
	}