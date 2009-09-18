<?php
	phpgw::import_class('booking.account_helper');

	abstract class booking_socommon
	{
		public static $AUTO_CREATED_ON = array('created_on' => array('type' => 'timestamp', 'auto' => true, 'add_callback' => '_set_created_on'));
		public static $AUTO_CREATED_BY = array('created_by' => array('type' => 'int', 'auto' => true, 'add_callback' => '_set_created_by'));
		public static $REL_CREATED_BY_NAME = array(
			'type' => 'string',
			'query' => true,
			'join' => array(
				'table' => 'phpgw_accounts',
				'fkey' => 'created_by',
				'key' => 'account_id',
				'column' => 'account_lid'
			)
		);
		
		protected
			$cols,
			$auto_fields;
			
		protected static $AUTO_FIELD_ACTIONS = array('add' => true, 'update' => true);
		
		public function __construct($table_name, $fields)
		{
			$this->table_name = $table_name;
			$this->fields = $fields;
			$this->db         = $GLOBALS['phpgw']->db;
			$this->join			= & $this->db->join;
			$this->like			= & $this->db->like;
		}
		
		public function get_field_defs() {
			return $this->fields;
		}
		
		protected function _set_created_on($field, &$entity) {
			$params = current(self::$AUTO_CREATED_ON);
			$entity[$field] = $this->_marshal(date('Y-m-d H:i:s'), $params['type']);
		}
		
		protected function _set_created_by($field, &$entity) {
			$params = current(self::$AUTO_CREATED_BY);
			$entity[$field] = $this->_marshal(booking_account_helper::current_account_id(), $params['type']);
		}
		
		public function get_columns()
		{
			if (!isset($this->cols))
			{
				$this->cols = array();
				
				foreach($this->fields as $field => $params)
				{
					if($params['join']) { continue; }
					$this->cols[] = $field;
				}
			}
			
			return $this->cols;
		}
		
		public function get_table_values(&$entry, $action) {
			//First, set automatic fields registered for this action
			$this->process_auto_fields($entry, $action);
			
			//Id is maintained by database, so unset it
			unset($entry['id']);
			
			//Here we return only those fields in entry that exist in the table
			return array_intersect_key(
				$entry, 
				array_filter($this->fields, array($this, 'is_table_field_def'))+$this->get_auto_field_defs($action)
			);
		}
		
		public function get_table_field_defs() {
			return array_filter($this->fields, array($this, 'is_table_field_def'));
		}
		
		protected function is_table_field_def(&$field_def) {
			return !($field_def['join'] || $field_def['manytomany']);
		}

		public function _get_cols_and_joins()
		{
			$cols = array();
			$joins = array();
			foreach($this->fields as $field => $params)
			{
				if($params['manytomany'])
				{
					continue;
				}
				else if($params['join'])
				{
					$cols[] = "{$params['join']['table']}_{$params['join']['column']}.{$params['join']['column']} AS {$field}";
					$joins[] = "LEFT JOIN {$params['join']['table']} AS {$params['join']['table']}_{$params['join']['column']} ON({$params['join']['table']}_{$params['join']['column']}.{$params['join']['key']}={$this->table_name}.{$params['join']['fkey']})";
				}
				else 
				{
					$cols[] = "{$this->table_name}.{$field} AS {$field}";
				}
			}
			return array($cols, $joins);
		}
		
		public function marshal_field_value($field, $value) {
			if (!is_array($field_def = $this->fields[$field])) 
				throw new InvalidArgumentException(sprintf('Field "%s" does not exists in "%s"', $field, get_class($this)));
			if (!isset($field_def['type']))
				throw new InvalidArgumentException(sprintf('Field "%s" in "%s" is missing a type definition', $field, get_class($this)));
				
			return $this->_marshal($value, $field_def['type']);
		}
		
		private function _marshal_field_value_by_ref(&$value, $field) {
			$value = $this->_marshal($value, $this->fields[$field]['type']);
		}
		
		function marshal_field_values(&$entry) {
			array_walk($entry, array($this, '_marshal_field_value_by_ref'));
			return $entry;
		}

		function _marshal($value, $type)
		{
			if($value === null)
			{
				return 'NULL';
			}
			else if($type == 'int')
			{
				return (is_string($value) && strlen($value) === 0) ? 'NULL' : intval($value);
			}
			else if($type == 'intarray')
			{
				foreach($value as $v)
				{
					$values[] = $this->_marshal($v, 'int');
				}
				return '('.join(',', $values).')';
			}
			return "'" . $this->db->db_addslashes($value) . "'";
		}

		function _unmarshal($value, $type)
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

		function read_single($id)
		{
			$id = intval($id);
			$cols_joins = $this->_get_cols_and_joins();
			$cols = join(',', $cols_joins[0]);
			$joins = join(' ', $cols_joins[1]);
			$this->db->query("SELECT $cols FROM $this->table_name $joins WHERE {$this->table_name}.id=$id", __LINE__, __FILE__);
			if ($this->db->next_record())
			{
				foreach($this->fields as $field => $params)
				{
					if($params['manytomany'])
					{
						$table = $params['manytomany']['table'];
						$key = $params['manytomany']['key'];
						
						if(is_array($params['manytomany']['column']))
						{	
							$column = array();
							foreach($params['manytomany']['column'] as $fieldOrInt => $paramsOrFieldName) {
								$column[] = is_array($paramsOrFieldName) ? $fieldOrInt : $paramsOrFieldName;
							}
							$column = join(',', $column);
							
							$this->db->query("SELECT $column FROM $table WHERE $key=$id", __LINE__, __FILE__);
							$row[$field] = array();
							while ($this->db->next_record())
							{
								$data = array();
								foreach($params['manytomany']['column'] as $intOrCol => $paramsOrCol)
								{
									if (is_array($paramsOrCol)) {
										$col = $intOrCol;
										$type = isset($paramsOrCol['type']) ? $paramsOrCol['type'] : $params['type'];
									} else {
										$col = $paramsOrCol;
										$type = $params['type'];
									}
									
									$data[$col] = $this->_unmarshal($this->db->f($col, true), $params['type']);
								}
								$row[$field][] = $data;
							}
						}
						else
						{
							$column = $params['manytomany']['column'];
							$this->db->query("SELECT $column FROM $table WHERE $key=$id", __LINE__, __FILE__);
							$row[$field] = array();
							while ($this->db->next_record())
							{
								$row[$field][] = $this->_unmarshal($this->db->f($column, true), $params['type']);
							}
						}
					}
					else
					{
						$row[$field] = $this->_unmarshal($this->db->f($field, true), $params['type']);
					}
				}
				return $row;
			}
		}

		function _get_conditions($query, $filters)
		{
			$clauses = array('1=1');
			if($query)
			{
				$like_pattern = "'%" . $this->db->db_addslashes($query) . "%'";
				$like_clauses = array();
				foreach($this->fields as $field => $params)
				{
					if($params['query'])
					{
						$table = $params['join'] ? $params['join']['table'].'_'.$params['join']['column'] : $this->table_name;
						$column = $params['join'] ? $params['join']['column'] : $field;
						if($params['type'] == 'int')
						{
							$like_clauses[] = "{$table}.{$column} = ". $this->db->db_addslashes($query);
						}
						else
						{
							$like_clauses[] = "{$table}.{$column} $this->like $like_pattern";
						}
					}
				}
				if(count($like_clauses))
				{
					$clauses[] = '(' . join(' OR ', $like_clauses) . ')';
				}
			}
			foreach($filters as $key => $val)
			{
				if($this->fields[$key])
				{
					$table = $this->fields[$key]['join'] ? $this->fields[$key]['table'].'_'.$params['join']['column'] : $this->table_name;
					if(is_array($val) && count($val) == 0)
					{
						$clauses[] = '1=0';
					}
					else if(is_array($val))
					{
						$vals = array();
						foreach($val as $v) {
							$vals[] = $this->_marshal($v, $this->fields[$key]['type']);
						}
						$clauses[] = "({$table}.{$key} IN (" . join(',', $vals) . '))';
					}
					else if($val == null)
					{
						$clauses[] = "{$table}.{$key} IS NULL";
					}
					else
					{
						$clauses[] = "{$table}.{$key}=" . $this->_marshal($val, $this->fields[$key]['type']);
					}
				} 	
				else if($key == 'where') 
				{
					//Includes a custom where-clause as a filter. Also replaces %%table%% 
					//tokens with actual table_name in the clause.
					$clauses[] = strtr(join((array)$val, ' AND '), array('%%table%%' => $this->table_name));
				}
			}
			return join(' AND ', $clauses);
		}

		/**
		 * Return all entries matching $params. Valid parameters:
		 *
		 * - $params['start']: Search result offset
		 * - $params['results']: Number of results to return
		 * - $params['sort']: Field to sort by
		 * - $params['query']: LIKE-based query string
		 * - $params['filters']: Array of custom filters
		 *
		 * @return array('total_records'=>X, 'results'=array(...))
		 */
		function read($params)
		{
			$start = isset($params['start']) && $params['start'] ? $params['start'] : 0;
			$results = isset($params['results']) && $params['results'] ? $params['results'] : null; //Passing null causes the system default to be used later on
			$sort = isset($params['sort']) && $params['sort'] ? $params['sort'] : null;
			$dir = isset($params['dir']) && $params['dir'] ? $params['dir'] : 'asc';
			$query = isset($params['query']) && $params['query'] ? $params['query'] : null;
			$filters = isset($params['filters']) && $params['filters'] ? $params['filters'] : array();

			$cols_joins = $this->_get_cols_and_joins();
			$cols = join(',', $cols_joins[0]);
			$joins = join(' ', $cols_joins[1]);
			$condition = $this->_get_conditions($query, $filters);

			// Calculate total number of records
			$this->db->query("SELECT count(1) AS count FROM $this->table_name $joins WHERE $condition", __LINE__, __FILE__);
			$this->db->next_record();
			$total_records = (int)$this->db->f('count');
			
			strtolower($results) === 'all' AND $results = $total_records;

			$order = $sort ? "ORDER BY $sort $dir ": '';

			$this->db->limit_query("SELECT $cols FROM $this->table_name $joins WHERE $condition $order", $start, __LINE__, __FILE__, $results);
			$results = array();
			while ($this->db->next_record())
			{
				$row = array();
				foreach($this->fields as $field => $fparams)
				{
                    $row[$field] = $this->_unmarshal($this->db->f($field, true), $params['type']);
				}
				$results[] = $row;
			}
			if(count($results) > 0)
			{
    			foreach($results as $id => $result)
    			{
    			    $id_map[$result['id']] = $id;
    			}
    			foreach($this->fields as $field => $params)
    			{
    				if($params['manytomany'])
    				{
    					$table = $params['manytomany']['table'];
    					$key = $params['manytomany']['key'];
    					$ids = join(',', array_keys($id_map));
						if(is_array($params['manytomany']['column']))
						{
							$colnames = array();
							foreach($params['manytomany']['column'] as $intOrCol => $paramsOrCol) {
								$colnames[] = is_array($paramsOrCol) ? $intOrCol : $paramsOrCol;
							}
							$colnames = join(',', $colnames);
							
	    					$this->db->query("SELECT $colnames, $key FROM $table WHERE $key IN($ids)", __LINE__, __FILE__);
	    					$row[$field] = array();
	    					while ($this->db->next_record())
	    					{
	    					    $id = $this->_unmarshal($this->db->f($key, true), 'int');
								$data = array();
								foreach($params['manytomany']['column'] as $intOrCol => $paramsOrCol)
								{	
									if (is_array($paramsOrCol)) {
										$col = $intOrCol;
										$type = isset($paramsOrCol['type']) ? $paramsOrCol['type'] : $params['type'];
									} else {
										$col = $paramsOrCol;
										$type = $params['type'];
									}
										
									$data[$col] = $this->_unmarshal($this->db->f($col, true), $type);
								}
								$row[$field][] = $data;
	    						$results[$id_map[$id]][$field][] = $data;
	    					}
						}
						else
						{
	    					$column = $params['manytomany']['column'];
	    					$this->db->query("SELECT $column, $key FROM $table WHERE $key IN($ids)", __LINE__, __FILE__);
	    					$row[$field] = array();
	    					while ($this->db->next_record())
	    					{
	    					    $id = $this->_unmarshal($this->db->f($key, true), 'int');
	    						$results[$id_map[$id]][$field][] = $this->_unmarshal($this->db->f($column, true), $params['type']);
	    					}
						}
    				}
    			}
		    }
			return array(
				'total_records' => $total_records,
				'results'		=> $results
			);
		}
		
		protected function is_auto_field_def(array &$field_def, $action) {
			return isset($field_def['auto']) && $field_def['auto'] != false && $this->get_auto_field_def_callback($field_def, $action);
		}
		
		protected function get_auto_field_def_callback(array &$field_def, $action) {
			return isset($field_def[$action.'_callback']) ? $field_def[$action.'_callback'] : null;
		}
		
		protected function get_auto_field_def($name, $action) {
			$auto_field_defs = $this->get_auto_field_defs($action);
			return (isset($auto_field_defs[$name]) ? $auto_field_defs[$name] : null);
		}
		
		protected function get_auto_field_defs($action = null) {
			if (is_string($action) && !array_key_exists($action, self::$AUTO_FIELD_ACTIONS)) { 
				throw new InvalidArgumentException("Unsupported mode \"$action\"");
			}
			
			if (!isset($this->auto_fields)) {
				foreach(array_keys(self::$AUTO_FIELD_ACTIONS) as $supported_action) {
					if (!is_array($this->auto_fields[$supported_action])) {
						$action_auto_fields = array();
						foreach($this->fields as $field => $params) {
							if (!$this->is_auto_field_def($params, $supported_action)) continue;
							$params['action_callback'] = $this->get_auto_field_def_callback($params, $supported_action);
							$params['action'] = $supported_action;
							$action_auto_fields[$field] = $params;
						}
						$this->auto_fields[$supported_action] = $action_auto_fields;
					}
				}
			}
			
			return is_string($action) ? $this->auto_fields[$action] : $this->auto_fields;
		}
		
		protected function process_auto_fields(&$entity, $action) {
			foreach($this->get_auto_field_defs($action) as $field => $params) { 
				$callback = $params['action_callback'];
				$this->$callback($field, $entity);
			}
			return $entity;
		}

		function add($entry)
		{			
			$values = $this->marshal_field_values($this->get_table_values($entry, __FUNCTION__));
			
			$this->db->query('INSERT INTO ' . $this->table_name . ' (' . join(',', array_keys($values)) . ') VALUES(' . join(',', $values) . ')', __LINE__,__FILE__);
			$id = $this->db->get_last_insert_id($this->table_name, 'id');
			foreach($this->fields as $field => $params)
			{
				if($params['manytomany'] && is_array($entry[$field]))
				{
					$table = $params['manytomany']['table'];
					$key = $params['manytomany']['key'];
					
					if(is_array($params['manytomany']['column']))
					{
						$colnames = array();
						foreach($params['manytomany']['column'] as $intOrCol => $paramsOrCol) {
							$colnames[] = is_array($paramsOrCol) ? $intOrCol : $paramsOrCol;
						}
						$colnames = join(',', $colnames);
						
						foreach($entry[$field] as $v)
						{
							$data = array();
							foreach($params['manytomany']['column'] as $intOrCol => $paramsOrCol)
							{
								if (is_array($paramsOrCol)) {
									$col = $intOrCol;
									$type = isset($paramsOrCol['type']) ? $paramsOrCol['type'] : $params['type'];
								} else {
									$col = $paramsOrCol;
									$type = $params['type'];
								}
								
								$data[] = $this->_marshal($v[$col], $type);
							}
							$v = join(',', $data);
							$this->db->query("INSERT INTO $table ($key, $colnames) VALUES($id, $v)", __LINE__, __FILE__);
						}
					}
					else
					{
						$colname = $params['manytomany']['column'];
						foreach($entry[$field] as $v)
						{
							$v = $this->_marshal($v, $params['type']);
							$this->db->query("INSERT INTO $table ($key, $colname) VALUES($id, $v)", __LINE__, __FILE__);
						}
					}
				}
			}
			$receipt['id'] = $id;
			$receipt['message'][] = array('msg'=>lang('Entity %1 has been saved', $receipt['id']));
			return $receipt;
		}
		
		function column_update_expression(&$value, $field) { 
			$value = sprintf($field.'=%s', $this->marshal_field_value($field, $value));
		}
		
		function update($entry)
		{
			if (!isset($entry['id'])) {
				throw new InvalidArgumentException(sprintf('Missing id in %s->%s', get_class($this), __FUNCTION__));
			}
			
			$id = intval($entry['id']);
			
			$values = $this->get_table_values($entry, __FUNCTION__);
			
			array_walk($values, array($this, 'column_update_expression'));
			
			$update_queries = array(
				'UPDATE ' . $this->table_name . ' SET ' . join(',', $values) . " WHERE id=$id"
			);
			
			foreach($this->fields as $field => $params)
			{
				if($params['manytomany'] && is_array($entry[$field]))
				{
					$table = $params['manytomany']['table'];
					$key = $params['manytomany']['key'];
					$update_queries[] = "DELETE FROM $table WHERE $key=$id";

					if(is_array($params['manytomany']['column']))
					{
						$colnames = array();
						foreach($params['manytomany']['column'] as $intOrCol => $paramsOrCol) {
							$colnames[] = is_array($paramsOrCol) ? $intOrCol : $paramsOrCol;
						}
						$colnames = join(',', $colnames);

						foreach($entry[$field] as $v)
						{
							$data = array();
							foreach($params['manytomany']['column'] as $intOrCol => $paramsOrCol)
							{
								if (is_array($paramsOrCol)) {
									$col = $intOrCol;
									$type = isset($paramsOrCol['type']) ? $paramsOrCol['type'] : $params['type'];
								} else {
									$col = $paramsOrCol;
									$type = $params['type'];
								}

								$data[] = $this->_marshal($v[$col], $type);
							}
							$v = join(',', $data);
							$update_queries[] = "INSERT INTO $table ($key, $colnames) VALUES($id, $v)";
						}
					}
					else
					{
						$colname = $params['manytomany']['column'];
						foreach($entry[$field] as $v)
						{
							$v = $this->_marshal($v, $params['type']);
							$update_queries[] = "INSERT INTO $table ($key, $colname) VALUES($id, $v)";
						}
					}
				}
			}
			
			$this->db->query(join($update_queries, ';'), __LINE__, __FILE__);
			
			$receipt['id'] = $id;
			$receipt['message'][] = array('msg'=>lang('Entity %1 has been updated', $entry['id']));
			return $receipt;
		}
		
		/**
		 * Validate uniqueness of record
		 *
		 * @param array $entity
		 * @param $unique_fields*
		 * @return boolean
		 */
		public function validate_uniqueness()
		{
			$args = func_get_args();
			$entity = array_shift($args);
			$filters = array();
			foreach($args as $unique_field) {
				if (isset($entity[$unique_field])) $filters[$unique_field] = $entity[$unique_field];
			}
			
			$duplicates = $this->read(array('filters' => $filters, 'results' => 1));
			
			if ($duplicates['total_records'] == 0) return true;
			
			if (isset($entity['id']) && $duplicates['total_records'] == 1 && $duplicates['results'][0]['id'] == $entity['id']) {
				return true;
			}
			
			return false;
		}
		
		public function create_error_stack($errors = array())
		{
			return CreateObject('booking.errorstack', $errors);
		}
		
		private function _validate($entity, array $fields, booking_errorstack $errors, $field_prefix = '')
		{	
			foreach($fields as $field => $params)
			{
				if (!is_array($params)) { continue; }
				
				$v = trim($entity[$field]);
				$empty = false;
				
				if(isset($params['manytomany']) && isset($params['manytomany']['column']) && isset($entity[$field]))
				{
					$sub_entity_count = 0; 
					foreach($entity[$field] as $sub_entity)
					{
						$this->_validate(
							(array)$sub_entity, 
							(array)$params['manytomany']['column'], 
							$errors, 
							sprintf('%s%s[%s]', $field_prefix, empty($field_prefix) ? $field : "[{$field}]", $sub_entity_count++)
						);
					}
					if($params['required'] && $sub_entity_count == 0)
					{
						$errors[$field] = "Field $field is required";
					}
					continue;
				}
				
				$error_key = empty($field_prefix) ? $field : "{$field_prefix}[{$field}]";
				if($params['required'] && (!isset($v) || ($v !== '0' && empty($v))))
				{
					$errors[$error_key] = "Field $error_key is required";
					$empty = true;
				}
				if($params['type'] == 'date' && !empty($entity[$field]))
				{
					$date = date_parse($entity[$field]);
					if(!$date || count($date['errors']) > 0) {
						$errors[$error_key] = "Field {$error_key}: Invalid format";
					}
				}
				
				if (!$empty && $params['sf_validator'])
				{
					try {
						$params['sf_validator']->setOption('required', false);
						$params['sf_validator']->clean($entity[$field]);
					} catch (sfValidatorError $e) {
						$errors[$error_key] = lang(strtr($e->getMessage(), array('%field%' => $error_key)));
					}
				}
			}
		}

		function validate($entity)
		{
			$errors = $this->create_error_stack();
			$this->preValidate($entity);
			$this->_validate($entity, $this->fields, $errors);
			$this->doValidate($entity, $errors);
			return $errors->getArrayCopy();
		}
		
		/**
		 * Implement in subclasses to perform actions on entity before validation
		 */
		protected function preValidate(&$entity)
		{
		}
		
		/**
		 * Implement in subclasses to perform custom validation.
		 */
		protected function doValidate($entity, booking_errorstack $errors)
		{
		}

		function delete($id)
		{
			$this->db->query("DELETE FROM {$this->table_name} WHERE id=" . intval($id), __LINE__, __FILE__);
		}
		
		function set_active($id, $active)
		{
			$this->db->query("UPDATE {$this->table_name} SET active=".$active." WHERE id=".$id, __LINE__, __FILE__);
		}
		
		
		public static function select_id(&$entity) {
			return $entity['id'];
		}
	}
