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

		public function get_cols_and_joins()
		{
			$cols = array();
			$joins = array();
			foreach($this->fields as $field => $params)
			{
				if($params['join'])
				{
					$cols[] = "{$params['join']['table']}.{$params['join']['column']} AS {$field}";
					$joins[] = "LEFT JOIN {$params['join']['table']} ON({$params['join']['table']}.{$params['join']['key']}={$this->table_name}.{$params['join']['fkey']})";
				}
				else 
				{
					$cols[] = "{$this->table_name}.{$field} AS {$field}";
				}
			}
			return array($cols, $joins);
		}

		function read_single($id)
		{
			$cols_joins = $this->get_cols_and_joins();
			$cols = join(',', $cols_joins[0]);
			$joins = join(' ', $cols_joins[1]);
			$this->db->query("SELECT $cols FROM $this->table_name $joins WHERE {$this->table_name}.id=". intval($id), __LINE__, __FILE__);
			if ($this->db->next_record())
			{
				foreach($this->fields as $field => $params)
				{
					$row[$field] = $this->db->f($field, true);
					if($params['type'] == 'int')
					{
						$row[$field] = (int)$row[$field];
					}
				}
				return $row;
			}
		}

		function get_condition($query, $filters)
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
						$table = $params['join'] ? $params['join']['table'] : $this->table_name;
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
					$table = $this->fields[$key]['join'] ? $this->fields[$key]['table'] : $this->table_name;
					$clauses[] = "{$table}.{$key}='" . $this->db->db_addslashes($val) . "'";
				}
			}
			return join(' AND ', $clauses);
		}

		function read($params)
		{
			$start = isset($params['start']) && $params['start'] ? $params['start'] : 0;
			$results = isset($params['results']) && $params['results'] ? $data['results'] : 1000;
			$sort = isset($params['sort']) && $params['sort'] ? $params['sort'] : null;
			$dir = isset($params['dir']) && $params['dir'] ? $params['dir'] : 'asc';
			$query = isset($params['query']) && $params['query'] ? $params['query'] : null;
			$filters = isset($params['filters']) && $params['filters'] ? $params['filters'] : array();

			$cols_joins = $this->get_cols_and_joins();
			$cols = join(',', $cols_joins[0]);
			$joins = join(' ', $cols_joins[1]);
			$condition = $this->get_condition($query, $filters);

			// Calculate total number of records
			$this->db->query("SELECT count(1) AS count FROM $this->table_name $joins WHERE $condition", __LINE__, __FILE__);
			$this->db->next_record();
			$total_records = (int)$this->db->f('count');

			$order = '';
			if($sort && in_array($sort, array_keys($this->fields)))
			{
				$order = ' ORDER BY ' . $sort . ' ' . ($dir == 'desc' ? 'DESC' : 'ASC');
			}

			$this->db->limit_query("SELECT $cols FROM $this->table_name $joins WHERE $condition $order", $start, __LINE__, __FILE__, $limit);
			$results = array();
			while ($this->db->next_record())
			{
				$row = array();
				foreach($this->fields as $field => $fparams)
				{
					$row[$field] = $this->db->f($field, true);
					if($fparams['type'] == 'int')
					{
						$row[$field] = (int)$row[$field];
					}
				}
				$results[] = $row;
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
				if($field == 'id' || $params['join'])
				{
					continue;
				}
				$cols[] = $field;
				$values[] = "'" . $this->db->db_addslashes($entry[$field]) . "'";
			}
			$this->db->query('INSERT INTO ' . $this->table_name . ' (' . join(',', $cols) . ') VALUES(' . join(',', $values) . ')', __LINE__,__FILE__);
			$receipt['id']= $this->db->get_last_insert_id($this->table_name, 'id');
			$receipt['message'][] = array('msg'=>lang('Entity %1 has been saved', $receipt['id']));
			return $receipt;
		}

		function update($entry)
		{
			$cols = array();
			$values = array();
			foreach($this->fields as $field => $params)
			{
				if($field == 'id' || $params['join'])
				{
					continue;
				}
				$values[] = $field . "='" . $this->db->db_addslashes($entry[$field]) . "'";
			}
			$cols = join(',', $cols);
			$this->db->query('UPDATE ' . $this->table_name . ' SET ' . join(',', $values), __LINE__,__FILE__);
			$receipt['id'] = $entry['id'];
			$receipt['message'][] = array('msg'=>lang('Entity %1 has been updated', $entry['id']));
			return $receipt;
		}

		function delete($id)
		{
			$this->db->query("DELETE FROM {$this->table_name} WHERE id=" . intval($id), __LINE__, __FILE__);
		}
	}
