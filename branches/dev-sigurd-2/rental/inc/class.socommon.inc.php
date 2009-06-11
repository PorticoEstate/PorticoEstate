<?php
	abstract class rental_socommon
	{
		public function __construct($table_name, $fields)
		{
			$this->table_name = $table_name;
			$this->fields = $fields;
			$this->db           = & $GLOBALS['phpgw']->db;
			$this->join			= & $this->db->join;
			$this->like			= & $this->db->like;
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

		public function get_cols_and_joins()
		{
			$cols = array();
			$joins = array();
			foreach($this->fields as $field => $params)
			{
//				print 'field:'.$field.'<br/>';
//				print 'parms:'.$params.'<br/>';
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
//			var_dump($cols);
//			var_dump($joins);
			return array($cols, $joins);
		}

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
			else if($type == 'intarray')
			{
				foreach($value as $v)
				{
					$values[] = $this->marshal($v, 'int');
				}
				return '('.join(',', $values).')';
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
		 * Read a single rental composite based on id

		 */
		function read_single($id)
		{
			$id = intval($id);
			$cols_joins = $this->get_cols_and_joins();
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
							$column = join(',', $params['manytomany']['column']);
							$this->db->query("SELECT $column FROM $table WHERE $key=$id", __LINE__, __FILE__);
							$row[$field] = array();
							while ($this->db->next_record())
							{
								$data = array();
								foreach($params['manytomany']['column'] as $col)
								{
									$data[$col] = $this->unmarshal($this->db->f($col, true), $params['type']);
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
								$row[$field][] = $this->unmarshal($this->db->f($column, true), $params['type']);
							}
						}
					}
					else
					{
						$row[$field] = $this->unmarshal($this->db->f($field, true), $params['type']);
					}
				}
				return $row;
			}
		}

		protected function get_conditions($query, $filters)
		{
			$clauses = array('1=1');
			if($query)
			{
				$like_pattern = "'%" . $this->db->db_addslashes($query) . "%'";
				$like_clauses = array();
				foreach($this->fields as $field => $params)
				{
					if($params['query'] && $params['type'] !== 'int')
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
							$vals[] = $this->marshal($v, $this->fields[$key]['type']);
						}
						$clauses[] = "({$table}.{$key} IN (" . join(',', $vals) . '))';
					}
					else if($val == null)
					{
						$clauses[] = "{$table}.{$key} IS NULL";
					}
					else
					{
						$clauses[] = "{$table}.{$key}=" . $this->marshal($val, $this->fields[$key]['type']);
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
			$dir = isset($params['dir']) && $params['dir'] ? $params['dir'] : '';
			$query = isset($params['query']) && $params['query'] ? $params['query'] : null;
			$filters = isset($params['filters']) && $params['filters'] ? $params['filters'] : array();

			$cols_joins = $this->get_cols_and_joins();
			$cols = join(',', $cols_joins[0]);
			$joins = join(' ', $cols_joins[1]);
			$condition = $this->get_conditions($query, $filters);

			// Calculate total number of records
			$this->db->query("SELECT count(1) AS count FROM $this->table_name $joins WHERE $condition", __LINE__, __FILE__);
			$this->db->next_record();
			$total_records = (int)$this->db->f('count');

			$order = $sort ? "ORDER BY $sort $dir ": '';

			$this->db->limit_query("SELECT $cols FROM $this->table_name $joins WHERE $condition $order", $start, __LINE__, __FILE__, $limit);
			$results = array();
			while ($this->db->next_record())
			{
				$row = array();
				foreach($this->fields as $field => $fparams)
				{
                    $row[$field] = $this->unmarshal($this->db->f($field, true), $params['type']);
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
							$colnames = join(',', $params['manytomany']['column']);
	    					$this->db->query("SELECT $colnames, $key FROM $table WHERE $key IN($ids)", __LINE__, __FILE__);
	    					$row[$field] = array();
	    					while ($this->db->next_record())
	    					{
	    					    $id = $this->unmarshal($this->db->f($key, true), 'int');
								$data = array();
								foreach($params['manytomany']['column'] as $col)
								{
									$data[$col] = $this->unmarshal($this->db->f($col, true), $params['type']);
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
	    					    $id = $this->unmarshal($this->db->f($key, true), 'int');
	    						$results[$id_map[$id]][$field][] = $this->unmarshal($this->db->f($column, true), $params['type']);
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
				$values[] = $this->marshal($entry[$field], $params['type']);
			}
			$this->db->query('INSERT INTO ' . $this->table_name . ' (' . join(',', $cols) . ') VALUES(' . join(',', $values) . ')', __LINE__,__FILE__);
			$id = $this->db->get_last_insert_id($this->table_name, 'id');
			foreach($this->fields as $field => $params)
			{
				if($params['manytomany'] && is_array($entry[$field]))
				{
					$table = $params['manytomany']['table'];
					$key = $params['manytomany']['key'];
					if(is_array($params['manytomany']['column']))
					{
						$colnames = join(',', $params['manytomany']['column']);
						foreach($entry[$field] as $v)
						{
							$data = array();
							foreach($params['manytomany']['column'] as $col)
							{
								$data[] = $this->marshal($v[$col], $params['type']);
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
							$v = $this->marshal($v, $params['type']);
							$this->db->query("INSERT INTO $table ($key, $colname) VALUES($id, $v)", __LINE__, __FILE__);
						}
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
				$values[] = $field . "=" . $this->marshal($entry[$field], $params['type']);
			}
			$cols = join(',', $cols);
			$this->db->query('UPDATE ' . $this->table_name . ' SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);
			foreach($this->fields as $field => $params)
			{
				if($params['manytomany'] && is_array($entry[$field]))
				{
					$table = $params['manytomany']['table'];
					$key = $params['manytomany']['key'];
					$this->db->query("DELETE FROM $table WHERE $key=$id", __LINE__, __FILE__);
					if(is_array($params['manytomany']['column']))
					{
						$colnames = join(',', $params['manytomany']['column']);
						foreach($entry[$field] as $v)
						{
							$data = array();
							foreach($params['manytomany']['column'] as $col)
							{
								$data[] = $this->marshal($v[$col], $params['type']);
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
							$v = $this->marshal($v, $params['type']);
							$this->db->query("INSERT INTO $table ($key, $colname) VALUES($id, $v)", __LINE__, __FILE__);
						}
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

		function delete($id)
		{
			$this->db->query("DELETE FROM {$this->table_name} WHERE id=" . intval($id), __LINE__, __FILE__);
		}
		
		function set_active($id, $active)
		{
			$this->db->query("UPDATE {$this->table_name} SET active=".$active." WHERE id=".$id, __LINE__, __FILE__);
		}
		
	}
?>