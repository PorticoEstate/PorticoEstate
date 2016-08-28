<?php
	/**
	 * phpGroupWare
	 *
	 * @author Sigurd Nes <sigurdne@online.no> and others
	 * @copyright Copyright (C) 2016 Free Software Foundation http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License v2 or later
	 * @internal 
	 * @package phpgroupware
	 * @subpackage phpgwapi
	 * @version $Id: class.custom_fields.inc.php 15409 2016-08-03 11:52:23Z sigurdne $
	 */

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU Lesser General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	abstract class  phpgwapi_socommon
	{

		protected $db;
		protected $db_null = 'NULL';
		protected $like;
		protected $join;
		protected $left_join;
		protected $sort_field;
		protected $skip_limit_query;
		protected $fields = array();
		protected static $so;
		protected $table_name;

		public function __construct($table_name, $fields)
		{
			$this->db = & $GLOBALS['phpgw']->db;
			$this->like = & $this->db->like;
			$this->join = & $this->db->join;
			$this->left_join = & $this->db->left_join;
			$this->sort_field = null;
			$this->skip_limit_query = null;
			$this->fields = $fields;
			$this->table_name = $table_name;

		}

		/**
		 * Begin transaction
		 *
		 * @return integer|bool current transaction id
		 */
		public function transaction_begin()
		{
			return $this->db->transaction_begin();
		}

		/**
		 * Complete the transaction
		 *
		 * @return bool True if sucessful, False if fails
		 */
		public function transaction_commit()
		{
			return $this->db->transaction_commit();
		}

		/**
		 * Rollback the current transaction
		 *
		 * @return bool True if sucessful, False if fails
		 */
		public function transaction_abort()
		{
			return $this->db->transaction_abort();
		}

		/**
		 * Marshal values according to type
		 * @param $value the value
		 * @param $type the type of value
		 * @return database value
		 */
		protected function marshal( $value, $type )
		{
			if ($value === null)
			{
				return 'NULL';
			}
			else if ($type == 'int')
			{
				if ($value == '')
				{
					return 'NULL';
				}
				return intval($value);
			}
			else if ($type == 'float')
			{
				return str_replace(',', '.', $value);
			}
			else if ($type == 'field')
			{
				return $this->db->db_addslashes($value);
			}
			return "'" . $this->db->db_addslashes($value) . "'";
		}

		/**
		 * Unmarchal database values according to type
		 * @param $value the field value
		 * @param $type	a string dictating value type
		 * @return the php value
		 */
		protected function unmarshal( $value, $type )
		{
			if ($type == 'bool')
			{
				return (bool)$value;
			}
			elseif ($type == 'int')
			{
				return (int)$value;
			}
			elseif ($value === null || $value == 'NULL' || ($type != 'string' && (strlen(trim($value)) === 0)))
			{
				return null;
			}
			elseif ($type == 'float')
			{
				return floatval($value);
			}
			else if ($type == 'json')
			{
				return json_decode($value, true);
			}
			return $value;
		}

		/**
		 * Get the count of the specified query. Query must return a signel column
		 * called count.
		 *
		 * @param $sql the sql query
		 * @return the count value
		 */
		protected function get_query_count( $sql )
		{
			$result = $this->db->query($sql);
			if ($result && $this->db->next_record())
			{
				return $this->unmarshal($this->db->f('count', true), 'int');
			}
		}

		/**
		 * Implementing classes must return an instance of itself.
		 *
		 * @return the class instance.
		 */
		public abstract static function get_instance();

		/**
		 * Convenience method for getting one single object. Calls get() with the
		 * specified id as a filter.
		 *
		 * @param $id int with id of object to return.
		 * @return object with the specified id, null if not found.
		 */
		public function get_single( int $id )
		{
			$objects = $this->get(0, 0, '', false, '', '', array($this->get_id_field_name() => $id));
			if (count($objects) > 0)
			{
				$keys = array_keys($objects);
				return $objects[$keys[0]];
			}
			return null;
		}

		/**
		 * Method for retrieving the db-object (security "forgotten")
		 */
		public function get_db()
		{
			return $this->db;
		}

		/**
		 * Method for retreiving objects.
		 *
		 * @param $start_index int with index of first object.
		 * @param $num_of_objects int with max number of objects to return.
		 * @param $sort_field string representing the object field to sort on.
		 * @param $ascending bool true for ascending sort on sort field, false
		 * for descending.
		 * @param $search_for string with free text search query.
		 * @param $search_type string with the query type.
		 * @param $filters array with key => value of filters.
		 * @return array of objects. May return an empty
		 * array, never null. The array keys are the respective index numbers.
		 */
		public function get( int $start_index, int $num_of_objects, string $sort_field, bool $ascending, string $search_for, string $search_type, array $filters )
		{
			// Only allow positive start index
			if ($start_index < 0)
			{
				$start_index = 0;
			}


			$sql = $this->get_query($sort_field, $ascending, $search_for, $search_type, $filters, false);
//			$sql_parts = explode('1=1', $sql); // Split the query to insert extra condition on test for break

			if($num_of_objects)
			{
				$this->db->limit_query($sql, $start_index, __LINE__, __FILE__, (int)$num_of_objects);
			}
			else
			{
				$this->db->query($sql,__LINE__,__FILE__);
			}

			$results = array();

			while ($this->db->next_record())
			{
				$results[] = $this->populate((int)$this->db->f('id'));
			}

			return $results;
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
		function read( $params )
		{
			$start = isset($params['start']) && $params['start'] ? (int)$params['start'] : 0;
			$results = isset($params['results']) && $params['results'] ? (int)$params['results'] : null;
			$sort = isset($params['sort']) && $params['sort'] ? $params['sort'] : null;
			$dir = isset($params['dir']) && $params['dir'] ? $params['dir'] : 'asc';
			$query = isset($params['query']) && $params['query'] ? $params['query'] : null;
			$filters = isset($params['filters']) && $params['filters'] ? $params['filters'] : array();
			$cols_joins = $this->_get_cols_and_joins($filters);
			$cols = join(',', $cols_joins[0]);
			$joins = join(' ', $cols_joins[1]);
			$condition = $this->_get_conditions($query, $filters);

			// Calculate total number of records
			$this->db->query("SELECT count(1) AS count FROM $this->table_name $joins WHERE $condition", __LINE__, __FILE__);
			$this->db->next_record();
			$total_records = (int)$this->db->f('count');

			strtolower($results) === 'all' AND $results = $total_records; //TODO: Kept because of BC. Should be easy to remove this dependency?

			/*
			 * Due to problem on order with offset - we need to set an additional parameter in some cases
			 * http://stackoverflow.com/questions/13580826/postgresql-repeating-rows-from-limit-offset
			 */

			if ($sort)
			{
				if (is_array($sort))
				{
					$order = "ORDER BY {$sort[0]} {$dir}, {$sort[1]}";
				}
				else
				{
					$order = "ORDER BY {$sort} {$dir}";
				}
			}

			$base_sql = "SELECT $cols FROM $this->table_name $joins WHERE $condition $order ";
			if ($results)
			{
				$this->db->limit_query($base_sql, $start, __LINE__, __FILE__, $results);
			}
			else
			{
				$offset = ($start > 0) ? 'OFFSET ' . $start . ' ' : ' ';
				$this->db->query($base_sql . $offset, __LINE__, __FILE__);
			}

			$results = array();
			while ($this->db->next_record())
			{
				$row = array();
				foreach ($this->fields as $field => $params)
				{
					$row[$field] = $this->unmarshal($this->db->f($field, false), $params['type']);
				}
				$results[] = $row;
			}
			if (count($results) > 0)
			{
				foreach ($results as $id => $result)
				{
					$id_map[$result['id']] = $id;
				}
				foreach ($this->fields as $field => $params)
				{
					if ($params['manytomany'])
					{
						$table = $params['manytomany']['table'];
						$key = $params['manytomany']['key'];
						$ids = join(',', array_keys($id_map));
						if (is_array($params['manytomany']['column']))
						{
							$colnames = array();
							foreach ($params['manytomany']['column'] as $intOrCol => $paramsOrCol)
							{
								$colnames[] = is_array($paramsOrCol) ? $intOrCol : $paramsOrCol;
							}
							$colnames = join(',', $colnames);

							$this->db->query("SELECT $colnames, $key FROM $table WHERE $key IN($ids)", __LINE__, __FILE__);
							$row[$field] = array();
							while ($this->db->next_record())
							{
								$id = $this->unmarshal($this->db->f($key, false), 'int');
								$data = array();
								foreach ($params['manytomany']['column'] as $intOrCol => $paramsOrCol)
								{
									if (is_array($paramsOrCol))
									{
										$col = $intOrCol;
										$type = isset($paramsOrCol['type']) ? $paramsOrCol['type'] : $params['type'];
									}
									else
									{
										$col = $paramsOrCol;
										$type = $params['type'];
									}

									$data[$col] = $this->unmarshal($this->db->f($col, false), $type);
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
								$id = $this->unmarshal($this->db->f($key, false), 'int');
								$results[$id_map[$id]][$field][] = $this->unmarshal($this->db->f($column, false), $params['type']);
							}
						}
					}
				}
			}
			return array(
				'total_records' => $total_records,
				'results' => $results,
				'start' => $start,
				'sort' => is_array($sort) ? $sort[0] : $sort,
				'dir' => $dir
			);
		}
		protected function primary_key_conditions( $id_params )
		{
			if (is_array($id_params))
			{
				return $this->_get_conditions(null, $id_params);
			}

			if (isset($this->fields['id']) && isset($this->fields['id']['type']))
			{
				$id_value = $this->marshal($id_params, $this->fields['id']['type']);
			}
			else
			{
				$id_value = intval($id_params);
			}

			return $this->table_name . '.id=' . $id_value;
		}

		function read_single( $id, $return_object = false)
		{
			if (!$id && !$return_object)
			{
				return array();
			}
			$row = array();
			$pk_params = $this->primary_key_conditions($id);
			$cols_joins = $this->_get_cols_and_joins();
			$cols = join(',', $cols_joins[0]);
			$joins = join(' ', $cols_joins[1]);
			$this->db->query("SELECT $cols FROM $this->table_name $joins WHERE $pk_params", __LINE__, __FILE__);
			if ($this->db->next_record())
			{
				foreach ($this->fields as $field => $params)
				{
					if ($params['manytomany'])
					{
						$table = $params['manytomany']['table'];
						$key = $params['manytomany']['key'];

						if (is_array($params['manytomany']['column']))
						{
							$column = array();
							foreach ($params['manytomany']['column'] as $fieldOrInt => $paramsOrFieldName)
							{
								$column[] = is_array($paramsOrFieldName) ? $fieldOrInt : $paramsOrFieldName;
							}
							$column = join(',', $column);
							$order_method = '';

							if (is_array($params['manytomany']['order']))
							{
								$order_method = "ORDER BY {$params['manytomany']['order']['sort']} {$params['manytomany']['order']['dir']}";
							}

							$this->db->query("SELECT {$column} FROM {$table} WHERE {$key}={$id} {$order_method}", __LINE__, __FILE__);
							$row[$field] = array();
							while ($this->db->next_record())
							{
								$data = array();
								foreach ($params['manytomany']['column'] as $intOrCol => $paramsOrCol)
								{
									if (is_array($paramsOrCol))
									{
										$col = $intOrCol;
										$type = isset($paramsOrCol['type']) ? $paramsOrCol['type'] : $params['type'];
									}
									else
									{
										$col = $paramsOrCol;
										$type = $params['type'];
									}

									$data[$col] = $this->unmarshal($this->db->f($col, false), $type);
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
								$row[$field][] = $this->unmarshal($this->db->f($column, false), $params['type']);
							}
						}
					}
					else
					{
						$row[$field] = $this->unmarshal($this->db->f($field, false), $params['type']);
					}
				}
			}
			if($return_object)
			{
				return $this->populate($row);
			}
			else
			{
				return $row;
			}
		}

		/**
		 * Returns SQL for retrieving matching objects or object count.
		 *
		 * @param $start_index int with index of first object.
		 * @param $num_of_objects int with max number of objects to return.
		 * @param $sort_field string representing the object field to sort on.
		 * @param $ascending bool true for ascending sort on sort field, false
		 * for descending.
		 * @param $search_for string with free text search query.
		 * @param $search_type string with the query type.
		 * @param $filters array with key => value of filters.
		 * @param $return_count bool telling to return only the count of the
		 * matching objects, or the objects themself.
		 * @return string with SQL.
		 */
		protected function get_query( string $sort_field, bool $ascending, string $search_for, string $search_type, array $filters, bool $return_count )
		{

			//Add columns to this array to include them in the query
			$columns = array();

			if ($sort_field != null)
			{
				$dir = $ascending ? 'ASC' : 'DESC';
				if ($sort_field == 'name')
				{
					$order = "ORDER BY {$this->table_name}.last_name {$dir}, {$this->table_name}.first_name {$dir}";
					$this->sort_field = array("{$this->table_name}.last_name", "{$this->table_name}.first_name");
				}
				else
				{
					if ($sort_field == 'address')
					{
						$sort_field = "{$this->table_name}.address1";
						$this->sort_field = array("{$this->table_name}.address1");
					}
					$order = "ORDER BY {$this->marshal($sort_field, 'field')} $dir";
				}
			}

			$condition = $this->_get_conditions( $search_for, $filters );
			if ($return_count) // We should only return a count
			{
				$cols = "COUNT(DISTINCT({$this->table_name}.id)) AS count";
			}
			else
			{
				$columns[] = "{$this->table_name}.*";
				$cols = implode(',', $columns);
			}

			$joins = '';

			return $sql = "SELECT {$cols} FROM {$this->table_name} {$joins} WHERE {$condition} {$order}";
		}

		function _get_conditions( $query, $filters )
		{
			$clauses = array('1=1');
			if ($query)
			{
				$like_pattern = "'%" . $this->db->db_addslashes($query) . "%'";
				$like_clauses = array();
				foreach ($this->fields as $field => $params)
				{
					if ($params['query'])
					{
						$table = $params['join'] ? $this->build_join_table_alias($field, $params) : $this->table_name;
						$column = $params['join'] ? $params['join']['column'] : $field;
						if ($params['type'] == 'int')
						{
							if (!(int)$query)
							{
								continue;
							}
							$like_clauses[] = "{$table}.{$column} = " . (int)$query;//$this->db->db_addslashes($query);
						}
						else
						{
							$like_clauses[] = "{$table}.{$column} $this->like $like_pattern";
						}
					}
				}
				if (count($like_clauses))
				{
					$clauses[] = '(' . join(' OR ', $like_clauses) . ')';
				}
			}
			foreach ($filters as $key => $val)
			{
				if ($this->fields[$key])
				{
					$table = $this->fields[$key]['join'] ? $this->build_join_table_alias($key, $this->fields[$key]) : $this->table_name;

					if (is_array($val) && count($val) == 0)
					{
						$clauses[] = '1=0';
					}
					else if (is_array($val))
					{
						$vals = array();
						foreach ($val as $v)
						{
							$vals[] = $this->marshal($v, $this->fields[$key]['type']);
						}
						$clauses[] = "({$table}.{$key} IN (" . join(',', $vals) . '))';
					}
					else if ($val == null)
					{
						$clauses[] = "{$table}.{$key} IS " . $this->db_null;
					}
					else
					{
						$_column = $this->fields[$key]['join'] ? $this->fields[$key]['join']['column'] : $key;					
						$clauses[] = "{$table}.{$_column}=" . $this->marshal($val, $this->fields[$key]['type']);
					}
				}
				else if ($key == 'where')
				{
					//Includes a custom where-clause as a filter. Also replaces %%table%%
					//tokens with actual table_name in the clause.
					$where_clauses = (array)$val;
					if (count($where_clauses) == 0)
					{
						continue;
					}
					$clauses[] = strtr(join((array)$val, ' AND '), array('%%table%%' => $this->table_name));
				}
			}
			return join(' AND ', $clauses);
		}

		protected function build_join_table_alias( $field, array $params )
		{
			return "{$params['join']['table']}_{$params['join']['column']}_{$field}";
		}

		public function _get_cols_and_joins( $filters = array() )
		{
			$cols = array();
			$joins = array();

			foreach ($this->fields as $field => $params)
			{
				if (isset($params['manytomany']) && $params['manytomany'])
				{
					continue;
				}
				else if (isset($params['join']) && $params['join'])
				{
					if ($params['join_type'] == 'manytomany' && !isset($filters[$field]) && !$filters[$field])
					{
						continue;
					}

					$join_table_alias = $this->build_join_table_alias($field, $params);
					$cols[] = "{$join_table_alias}.{$params['join']['column']} AS {$field}";
					$joins[] = "LEFT JOIN {$params['join']['table']} AS {$join_table_alias} ON({$join_table_alias}.{$params['join']['key']}={$this->table_name}.{$params['join']['fkey']})";
				}
				else
				{
					$value_expression = isset($params['expression']) ?
						'(' . strtr($params['expression'], array('%%table%%' => $this->table_name)) . ')' : "{$this->table_name}.{$field}";
					$cols[] = "{$value_expression} AS {$field}";
				}
			}
			return array($cols, $joins);
		}

		/**
		 * Returns count of matching objects.
		 *
		 * @param $search_for string with free text search query.
		 * @param $search_type string with the query type.
		 * @param $filters array with key => value of filters.
		 * @return int with object count.
		 */
		public function get_count( string $search_for, string $search_type, array $filters )
		{
			return $this->get_query_count($this->get_query('', false, $search_for, $search_type, $filters, true));
		}

		/**
		 * Implementing classes must return the name of the field used in the query
		 * returned from get_query().
		 * 
		 * @return string with name of id field.
		 */
		protected function get_id_field_name()
		{
			
		}

		protected abstract function populate( array $data );


		protected function add( &$object )
		{
			$object->set_entry_date(time());
			$value_set = array();

			$fields = $object::get_fields();

			foreach ($fields as $field	=> $field_info)
			{
				if(($field_info['action'] & PHPGW_ACL_ADD) && empty($field_info['join']))
				{
					$value_set[$field] = $object->$field;
				}
			}
			
			$sql = "INSERT INTO {$this->table_name} (". implode(',',$cols)
				. ') VALUES ('
				. $this->db->validate_insert(array_values($value_set))
				. ')';
			$this->db->query($sql,__LINE__,__FILE__);

			$id = $this->db->get_last_insert_id($this->table_name, 'id');
			$object->set_id($id);
			return $object;
		}

		protected function update( $object )
		{
			$id = $object->get_id();
			$value_set = array();

			foreach ($this->fields as $field => $field_info)
			{
				if(($field_info['action'] & PHPGW_ACL_EDIT) && empty($field_info['join']))
				{
					$value_set[$field] = $object->$field;
				}
			}

			$sql = "UPDATE {$this->table_name} SET "
				. $this->db->validate_update($value_set)
				. " WHERE id = {$id}";

			return $this->db->query($sql,__LINE__,__FILE__);

		}

		/**
		 * Store the object in the database.  If the object has no ID it is assumed to be new and
		 * inserted for the first time.  The object is then updated with the new insert id.
		 */
		public function store( &$object )
		{
			if ($object->validates())
			{
				if ($object->get_id() > 0)
				{
					// We can assume this composite came from the database since it has an ID. Update the existing row
					return $this->update($object);
				}
				else
				{
					// This object does not have an ID, so will be saved as a new DB row
					return $this->add($object);
				}
			}

			// The object did not validate
			return false;
		}
	}
