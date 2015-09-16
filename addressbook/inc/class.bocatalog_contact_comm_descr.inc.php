<?php
	class bocatalog_contact_comm_descr
	{
		function __construct()
		{
			$this->so = CreateObject('addressbook.socatalog_contact_comm_descr');
		}
		
		function select_catalog()
		{
			$comm_descr = $this->so->select_catalog();
			foreach($comm_descr as $key => $value)
			{
				$comm_descr_array[] = array('comm_type_id' => $value['comm_type'],
							    'comm_type'	=> $this->search_comm_type_id($value['comm_type']),
							    'comm_descr_id' => $value['comm_descr_id'],
							    'comm_description' => $value['comm_description']);
			}
			return $comm_descr_array;
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

		function select_catalog_types()
		{
			$this->comm_type = $this->so->select_catalog_types();
			return $this->comm_type;
		}

		/**
		* Search communication type id in communications catalog
		*
		* @param integer $id The communication id to find
		* @return string The description type of id
		*/
		function search_comm_type_id($id)
		{
			return $this->search_catalog('comm_type_id', $id, 'comm_type_description', 'comm_type');
		}
		
		/**
		* Search communication type in location catalog
		*
		* @param string $description The communication type to find
		* @return integer The id of description
		*/
		function search_comm_type($description)
		{
			return $this->search_catalog('comm_type_description', $description, 'comm_type_id', 'comm_type');
		}

		/**
		* Search a value into an array
		*
		* @param string $field_to_search Field into what you want to find
		* @param string $value_to_search Value what you want
		* @param string $field Field what you want return
		* @param string $catalog Catalog name into you want to find
		* @return string The value which you requiere in $field
		*/
		function search_catalog($field_to_search, $value_to_search, $field, $catalog)
		{
			reset($this->$catalog);
			foreach ($this->$catalog as $key => $value)
			{
				if ($value[$field_to_search] == $value_to_search)
				{
					return $value[$field];
				}
			}
		}
	}