<?php
	phpgw::import_class('booking.account_helper');

	abstract class booking_socommon
	{
		protected $db_null='NULL';
		
		protected $valid_field_types = array(
			'date' => true,
			'time' => true,
			'timestamp' => true,
			'string' => true,
			'int' => true,
			'decimal' => true,
			'intarray' => true,
		);
		
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
		
		public function get_db() {
			return $this->db;
		}
		
		public function get_table_name() {
			return $this->table_name;
		}
		
		/**
		 * Added because error reporting facilities in phpgw tries to serialize the PDO
		 * instance in $this->db which causes an error. This method removes $this->db from the 
		 * serialized values to avoid this problem.
		 */
		public function __sleep()
		{
			return array('table_name', 'fields');
		}
		
		protected function db_query($sql, $line, $file) {
			return $this->db->query($sql, $line, $file);
		}
		
		public function get_field_defs() {
			return $this->fields;
		}
		
		protected function _set_created_on($field, &$entity) {
			$params = current(self::$AUTO_CREATED_ON);
			$entity[$field] = 'now';
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
					if(isset($params['join']) && $params['join'])
					{
						continue;
					}
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
		
		protected function build_join_table_alias($field, array $params) {
			return "{$params['join']['table']}_{$params['join']['column']}_{$field}";
		}
		
		public function _get_cols_and_joins()
		{
			$cols = array();
			$joins = array();
			
			foreach($this->fields as $field => $params)
			{
				if(isset($params['manytomany']) && $params['manytomany'])
				{
					continue;
				}
				else if(isset($params['join']) && $params['join'])
				{
					$join_table_alias = $this->build_join_table_alias($field, $params);
					$cols[] = "{$join_table_alias}.{$params['join']['column']} AS {$field}";
					$joins[] = "LEFT JOIN {$params['join']['table']} AS {$join_table_alias} ON({$join_table_alias}.{$params['join']['key']}={$this->table_name}.{$params['join']['fkey']})";
				}
				else 
				{
					$value_expression = isset($params['expression']) ? 
						'('.strtr($params['expression'], array('%%table%%' => $this->table_name)).')' : "{$this->table_name}.{$field}";
					$cols[] = "{$value_expression} AS {$field}";
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

		public function valid_field_type($type) {
			return isset($this->valid_field_types[$type]);
		}
		
		function _marshal($value, $type)
		{
			$type = strtolower($type);
			if($value === null)
			{
				return $this->db_null;
			}
			else if($type == 'int' || $type == 'decimal')
			{
				if (is_string($value) && strlen(trim($value)) === 0) {
					return $this->db_null;
				} else if ($type == 'int') {
					return intval($value);
				} else if ($type == 'decimal') {
					return floatval($value);
				}
				//Don't know what could have gone wrong above for us to get here but returning NULL here as a safety
				return $this->db_null;
			}
			else if($type == 'intarray')
			{
				foreach($value as $v)
				{
					$values[] = $this->_marshal($v, 'int');
				}
				return '('.join(',', $values).')';
			}
					
			//Sanity check
			if (!$this->valid_field_type($type)) {
				throw new LogicException(sprintf('Invalid type "%s"', $type));
			}
			
			return "'" . $this->db->db_addslashes($value) . "'";
		}

		function _unmarshal($value, $type)
		{
			$type = strtolower($type);
			if( 
				 ($value === null)
				 || ($type != 'string' && (strlen(trim($value)) === 0)) /* phpgw always returns empty strings (i.e '') for null values */
			  ) 
			{				
				return null;
			}
			else if($type == 'int')
			{
				return intval($value);
			}
			else if ($type == 'decimal') {
				return floatval($value);
			}
			
			//Sanity check
			if (!$this->valid_field_type($type)) {
				throw new LogicException(sprintf('Invalid type "%s"', $type));
			}
			
			return $value;
		}
		
		protected function primary_key_conditions($id_params) {
			if (is_array($id_params)) {
				return $this->_get_conditions(null, $id_params);
			}
			
			if (isset($this->fields['id']) && isset($this->fields['id']['type'])) {
				$id_value = $this->_marshal($id_params, $this->fields['id']['type']);
			} else {
				$id_value = intval($id_params);
			}
			
			return $this->table_name.'.id='.$id_value;
		}

		function read_single($id)
		{
			$pk_params = $this->primary_key_conditions($id);
			$cols_joins = $this->_get_cols_and_joins();
			$cols = join(',', $cols_joins[0]);
			$joins = join(' ', $cols_joins[1]);
			$this->db->query("SELECT $cols FROM $this->table_name $joins WHERE $pk_params", __LINE__, __FILE__);
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
									
									$data[$col] = $this->_unmarshal($this->db->f($col, false), $type);
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
								$row[$field][] = $this->_unmarshal($this->db->f($column, false), $params['type']);
							}
						}
					}
					else
					{
						$row[$field] = $this->_unmarshal($this->db->f($field, false), $params['type']);
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
						$table = $params['join'] ? $this->build_join_table_alias($field, $params) : $this->table_name;
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
					$table = isset($this->fields[$key]['join']) && $this->fields[$key]['join'] ? $this->fields[$key]['table'].'_'.$params['join']['column'] : $this->table_name;
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
						$clauses[] = "{$table}.{$key} IS ".$this->db_null;
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
					$where_clauses = (array)$val;
					if (count($where_clauses) == 0) { continue; }
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
			$start = isset($params['start']) && $params['start'] ? (int)$params['start'] : 0;
			$results = isset($params['results']) && $params['results'] ? (int)$params['results'] : null;
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
			
			strtolower($results) === 'all' AND $results = $total_records; //TODO: Kept because of BC. Should be easy to remove this dependency?

			$order = $sort ? "ORDER BY $sort $dir ": '';
			
			$base_sql = "SELECT $cols FROM $this->table_name $joins WHERE $condition $order ";
			if ($results) 
			{
				$this->db->limit_query($base_sql, $start, __LINE__, __FILE__, $results);
			} else {
				$offset = ($start > 0) ?  'OFFSET ' . $start . ' ' : ' ';
				$this->db->query($base_sql.$offset, __LINE__, __FILE__);
			}
			
			$results = array();
			while ($this->db->next_record())
			{
				$row = array();
				foreach($this->fields as $field => $params)
				{
					$row[$field] = $this->_unmarshal($this->db->f($field, false), $params['type']);
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
	    					    $id = $this->_unmarshal($this->db->f($key, false), 'int');
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
										
									$data[$col] = $this->_unmarshal($this->db->f($col, false), $type);
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
	    					    $id = $this->_unmarshal($this->db->f($key, false), 'int');
	    						$results[$id_map[$id]][$field][] = $this->_unmarshal($this->db->f($column, false), $params['type']);
	    					}
						}
    				}
    			}
		    }
			return array(
				'total_records' => $total_records,
				'results'		=> $results,
				'start'			=> $start,
				'sort'			=> $sort,
				'dir'			=> $dir
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
							$colnames[(is_array($paramsOrCol) ? $intOrCol : $paramsOrCol)] = true;
						}
						unset($colnames['id']);
						
						$colnames = join(',', array_keys($colnames));
						
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
								
								if ($col == 'id') { continue; }
								
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
			return $value;
		}
		
		protected function entity_update_sql($entity_id, array $values) {
			array_walk($values, array($this, 'column_update_expression'));
			return sprintf(
				"UPDATE %s SET %s WHERE %s",
				$this->table_name,
				join(',', $values),
				$this->primary_key_conditions($entity_id)
			);
		}
		
		function update($entry)
		{
			if (!isset($entry['id'])) {
				throw new InvalidArgumentException(sprintf('Missing id in %s->%s', get_class($this), __FUNCTION__));
			}
			
			$id = intval($entry['id']);
			
			$values = $this->get_table_values($entry, __FUNCTION__);
			foreach($values as $key => $val)
			{
				$val = str_replace('&nbsp;', ' ', $val);
				$values[$key] = trim($val);
			}
			
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
							$colnames[(is_array($paramsOrCol) ? $intOrCol : $paramsOrCol)] = true;
						}
						unset($colnames['id']);
						
						$colnames = join(',', array_keys($colnames));

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
								
								if ($col == 'id') { continue; }
								
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
			$this->db->query(join($update_queries, ";\n"), __LINE__, __FILE__);
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
			
			if (!array_key_exists('results', $duplicates) || (is_array($duplicates['results']) && count($duplicates['results']) <= 0)) {
				return true; //Values are unique: found no other entity matching the values, so values must be valid
			}
			
			if (isset($entity['id']) && (is_array($duplicates['results']) && count($duplicates['results']) > 0) && $duplicates['results'][0]['id'] == $entity['id']) {
				return true; //Values are unique since the values uniquely identified this entity and no other entity
			}
			
			return false; //No, values are not unique
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
				
				if(isset($params['manytomany']) && isset($params['manytomany']['column']))
				{
					$sub_entity_count = 0;
					
					if (isset($entity[$field]) && is_array($entity[$field]))	{
						foreach($entity[$field] as $key => $sub_entity)
						{
							$this->_validate(
								(array)$sub_entity, 
								(array)$params['manytomany']['column'], 
								$errors, 
								sprintf('%s%s[%s]', $field_prefix, empty($field_prefix) ? $field : "[{$field}]", (is_string($key) ? $key : $sub_entity_count))
							);
							$sub_entity_count++;
						}
					}

					if($params['required'] && $sub_entity_count == 0)
					{
						$errors[$field] = lang("Field %1 is required", lang($field));
					}
					continue;
				}
				
				$error_key = empty($field_prefix) ? $field : "{$field_prefix}[{$field}]";
				if($params['required'] && (!isset($v) || ($v !== '0' && empty($v))))
				{
					$errors[$error_key] = lang("Field %1 is required", lang($error_key));
					$empty = true;
				}
				if($params['type'] == 'date' && !empty($entity[$field]))
				{
					$date = date_parse($entity[$field]);
					if(!$date || count($date['errors']) > 0) {
						$errors[$error_key] = lang("Field %1: Invalid format", lang($error_key));
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

			// replace several agegroups error messages with one
			// gives nicer output
			foreach($errors->getArrayCopy() as $key => $value)
			{
				// key starts with agegroups
				if (strncmp($key, 'agegroups', strlen('agegroups')) == 0)
				{
					unset($errors[$key]);
					$errors['agegroups'] = lang("Field %1 is required", lang('number of participants'));
				}
			}
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
