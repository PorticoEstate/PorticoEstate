<?php
	class import_conversion
	{
		protected $db;
		public $messages = array();
		public $warnings = array();
		public $errors = array();
		public $debug = true;
		public $fields = array();
		public $table;
		public $metadata = array();

		public function __construct()
		{
			set_time_limit(10000); //Set the time limit for this request
			$this->account		= (int)$GLOBALS['phpgw_info']['user']['account_id'];
			$this->db           = & $GLOBALS['phpgw']->db;
		}

		public function add($data)
		{
			$error = false;
			$table = $this->table;
			$fields =  $this->fields;

			if(!$table)
			{
				throw new Exception("Tabell er ikke angitt");
			}

			if(!$fields)
			{
				throw new Exception("Felter er ikke definert");
			}

			$primary_key = array();
			$remove_keys = array();
			foreach($this->metadata as $key => $info)
			{
				if(isset($info->primary_key) && $info->primary_key)
				{
					if(!$_value = $data[array_search($key, $fields)])
					{
						throw new Exception("Fant ikke verdi for feltet 'primary key'");
					}

					$primary_key[] = "$key='{$_value}'";
					$remove_keys[] = $key;
				}
			}
			unset($key);
			unset($info);
			unset($_value);

			$filtermethod = implode(' AND ', $primary_key);

			$value_set = array();
			foreach ($fields as $key => $field)
			{
				$value_set[$field] 	= $this->validate_value($data[$key], $field);
			}

			$this->db->query("SELECT count(*) as cnt FROM {$table} WHERE {$filtermethod}",__LINE__,__FILE__);
			$this->db->next_record();
			if($this->db->f('cnt'))
			{
				foreach ($remove_keys as $remove_key)
				{
					unset($value_set[$remove_key]);			
				}

				$this->warnings[] = "ID finnes fra før: {$filtermethod}, oppdaterer";
				$value_set	= $this->db->validate_update($value_set);
				$sql = "UPDATE {$table} SET {$value_set} WHERE {$filtermethod}";
			}
			else
			{
				$this->warnings[] = "ID fantes ikke fra før: {$filtermethod}";

				$cols = implode(',', array_keys($value_set));
				$values	= $this->db->validate_insert(array_values($value_set));
				$sql = "INSERT INTO {$table} ({$cols}) VALUES ({$values})";
			}

			if($this->debug)
			{
				_debug_array($sql);
			}
			else
			{
				$request_ok = $this->db->query($sql,__LINE__,__FILE__);
			}

			if(!$error)
			{
				$this->messages[] = "Successfully updated entry: id ($filtermethod)";
				$ok = true;
			}
			else
			{
				$this->errors[] = "Error updating location: id ({$filtermethod})";
				$ok = false;
			}
			return $ok;
		}


		/**
		 * Test a value for null according to several formats that can exist in the export.
		 * Returns true if the value is null according to these rules, false otherwise.
		 * 
		 * @param string $value The value to test
		 * @return bool
		 */
		protected function is_null($value)
		{
			return ((trim($value) == "") || ($data == "<NULL>") || ($data == "''"));
		}

		protected function validate_value($value,$field)
		{
			$datatype = $this->metadata[$field]->type;
			switch ($datatype)
			{
				case 'char':
				case 'varchar':
				case 'text':
					$ret = $this->db->db_addslashes($value);
					break;
				case 'bool':
					$ret = $value ? 'True' : 'False';
					break;
				default:
					$ret = $value;
			}

			return $ret;
		}

	}
