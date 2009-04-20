<?php
	class booking_socommon
	{
		public function __construct($table_name, $fields)
		{
			$this->table_name = $table_name;
			$this->fields = $fields;
			$this->db           = & $GLOBALS['phpgw']->db;
			$this->join			= & $this->db->join;
			$this->like			= & $this->db->like;
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

		function _marshal($value, $type)
		{
			if($value === null)
			{
				return 'NULL';
			}
			else if($type == 'int')
			{
				return intval($value);
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
					$row[$field] = $this->_unmarshal($this->db->f($field, true), $params['type']);
				}
				foreach($this->fields as $field => $params)
				{
					if($params['manytomany'])
					{
						$column = $params['manytomany']['column'];
						$table = $params['manytomany']['table'];
						$key = $params['manytomany']['key'];
						$this->db->query("SELECT $column FROM $table WHERE $key=$id", __LINE__, __FILE__);
						$row[$field] = array();
						while ($this->db->next_record())
						{
							$row[$field][] = $this->_unmarshal($this->db->f($column, true), $params['type']);
						}
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
						$like_clauses[] = "{$table}.{$column} $this->like $like_pattern";
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
			$results = isset($params['results']) && $params['results'] ? $data['results'] : 1000;
			$sort = isset($params['sort']) && $params['sort'] ? $params['sort'] : null;
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

			$order = $sort ? 'ORDER BY ' . $sort : '';

			$this->db->limit_query("SELECT $cols FROM $this->table_name $joins WHERE $condition $order", $start, __LINE__, __FILE__, $limit);
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
    					$column = $params['manytomany']['column'];
    					$table = $params['manytomany']['table'];
    					$key = $params['manytomany']['key'];
    					$ids = join(',', array_keys($id_map));
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
			return array(
				'total_records' => $total_records,
				'results'		=> $results
			);
		}

		function add($entry)
		{
			$cols = array();
			$values = array();
			foreach($this->fields as $field => $params)
			{
				if($field == 'id' || $params['join'] || $params['manytomany'])
				{
					continue;
				}
				$cols[] = $field;
				$values[] = $this->_marshal($entry[$field], $params['type']);
			}
			$this->db->query('INSERT INTO ' . $this->table_name . ' (' . join(',', $cols) . ') VALUES(' . join(',', $values) . ')', __LINE__,__FILE__);
			$id = $this->db->get_last_insert_id($this->table_name, 'id');
			foreach($this->fields as $field => $params)
			{
				if($params['manytomany'] && is_array($entry[$field]))
				{
					$column = $params['manytomany']['column'];
					$table = $params['manytomany']['table'];
					$key = $params['manytomany']['key'];
					foreach($entry[$field] as $v)
					{
						$v = $this->_marshal($v, $params['type']);
						$this->db->query("INSERT INTO $table ($column, $key) VALUES($v, $id)", __LINE__, __FILE__);
					}
				}
			}
			$receipt['id'] = $id;
			$receipt['message'][] = array('msg'=>lang('Entity %1 has been saved', $receipt['id']));
			return $receipt;
		}

		function update($entry)
		{
			$id = intval($entry['id']);
			$cols = array();
			$values = array();
			foreach($this->fields as $field => $params)
			{
				if($field == 'id' || $params['join'] || $params['manytomany'])
				{
					continue;
				}
				$values[] = $field . "=" . $this->_marshal($entry[$field], $params['type']);
			}
			$cols = join(',', $cols);
			$this->db->query('UPDATE ' . $this->table_name . ' SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);
			foreach($this->fields as $field => $params)
			{
				if($params['manytomany'] && is_array($entry[$field]))
				{
					$column = $params['manytomany']['column'];
					$table = $params['manytomany']['table'];
					$key = $params['manytomany']['key'];
					$this->db->query("DELETE FROM $table WHERE $key=$id", __LINE__, __FILE__);
					foreach($entry[$field] as $v)
					{
						$v = $this->_marshal($v, $params['type']);
						$this->db->query("INSERT INTO $table ($column, $key) VALUES($v, $id)", __LINE__, __FILE__);
					}
				}
			}
			$receipt['id'] = $id;
			$receipt['message'][] = array('msg'=>lang('Entity %1 has been updated', $entry['id']));
			return $receipt;
		}

		function validate($entity)
		{
			$errors = array();
			foreach($this->fields as $field => $params)
			{
				$v = trim($entity[$field]);
				if($params['required'] && (!isset($v) || empty($v)))
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

		function delete($id)
		{
			$this->db->query("DELETE FROM {$this->table_name} WHERE id=" . intval($id), __LINE__, __FILE__);
		}
		
		function set_active($id, $active)
		{
			$this->db->query("UPDATE {$this->table_name} SET active=".$active." WHERE id=".$id, __LINE__, __FILE__);
		}
		
		
		
	}
