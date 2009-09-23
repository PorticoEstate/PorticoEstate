<?php
	abstract class rental_socommon
	{
		public $table_name;
		
		public function __construct($table_name, $fields)
		{
			$this->table_name = $table_name;
			$this->fields = $fields;
			$this->db           = clone $GLOBALS['phpgw']->db;
			$this->like			= & $this->db->like;
			$this->join			= & $this->db->join;
			$this->left_join	= & $this->db->left_join;
		}
		
		/*
		 * Shortcut function to get a value from the current db row and unmarshal it according
		 * to its type.
		 */
		protected function get_field_value($field)
		{
			return $this->unmarshal($this->db->f($field, true), $this->fields[$field]['type']);
		}

		/**
		 * Marshal values according to type
		 * @param $value the value
		 * @param $type the type of value
		 * @return database value
		 */
		protected function marshal($value, $type)
		{
			if($value === null)
			{
				return 'NULL';
			}
			else if($type == 'int')
			{
				return intval($value);
			}
			return "'" . $this->db->db_addslashes($value) . "'";
		}

		/**
		 * Unmarchal database values according to type
		 * @param $value the field value
		 * @param $type	a string dictating value type
		 * @return the php value
		 */
		protected function unmarshal($value, $type)
		{
			if($value === null || $value == 'NULL')
			{
				return null;
			}
			else if($type == 'int')
			{
				return intval($value);
			}
			return $value;
		}

		/**
		 * Validate entity according to specification
		 * @param $entity the entity to validate
		 * @return an array of errors
		 */
		function validate($entity)
		{
			$errors = array();
			foreach($this->fields as $field => $params)
			{
				$v = trim($entity[$field]);
				if($params['required'] && (!isset($v) || ($v !== '0' && empty($v))))
				{
					$errors[$field] = "Field $field is required";
				}
				if($params['type'] == 'date' && !empty($entity[$field]))
				{
					$date = date_parse($entity[$field]);
					if(!$date || count($date['errors']) > 0) {
						$errors[$field] = "Field {$field}: Invalid format";
					}
				}
			}
			return $errors;
		}
		
		/**
		 * Get the count of the last query 
		 * 
		 * @param $sql the sql query
		 * @return the count value
		 */
		function get_count($sql)
		{
			$result = $this->db->query($sql);
			if($result && $this->db->next_record())
			{
				return $this->unmarshal($this->db->f('count', true), 'int');
			} 
		}
	}
?>