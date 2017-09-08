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

	abstract class phpgwapi_socommon
	{

		protected $db;
		protected $db2;
		protected $db_null = 'NULL';
		protected $like;
		protected $join;
		protected $left_join;
		protected $sort_field;
		protected $skip_limit_query;
		protected $fields = array();
		protected static $so;
		protected $table_name;
		protected $use_acl = false;
		protected $currentapp;
		protected $acl;
		protected $relaxe_acl;
		protected $account;

		public function __construct( $table_name, $fields )
		{
			$this->db = & $GLOBALS['phpgw']->db;
			$this->db2 = clone($GLOBALS['phpgw']->db);
			$this->like = & $this->db->like;
			$this->join = & $this->db->join;
			$this->left_join = & $this->db->left_join;
			$this->sort_field = null;
			$this->skip_limit_query = null;
			$this->fields = $fields;
			$this->table_name = $table_name;
			$this->dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$this->currentapp = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->acl = & $GLOBALS['phpgw']->acl;
			$this->account = (int)$GLOBALS['phpgw_info']['user']['account_id'];
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
		 * @return bool True if successful, False if fails
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
			else if ($type == 'jsonb')
			{
				return "'" . json_encode($value) . "'";
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
			else if ($type == 'datestring')
			{
				return date($this->dateformat, strtotime($value));
			}
			return $this->db->stripslashes($value);
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
		 * Method for retrieving the db-object (security "forgotten")
		 */
		public function get_db()
		{
			return $this->db;
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
			$relaxe_acl = isset($params['relaxe_acl']) && $params['relaxe_acl'] ? $params['relaxe_acl'] : false;
			$this->relaxe_acl = $relaxe_acl;
			$cols_joins = $this->_get_cols_and_joins($query, $filters);
			$cols = join(',', $cols_joins[0]);
			$joins = join(' ', $cols_joins[1]);
			$condition = $this->_get_conditions($query, $filters);
			$this->relaxe_acl = false;

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

			$base_sql = "SELECT DISTINCT $cols FROM $this->table_name $joins WHERE $condition $order ";
			if ($results > -1)
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
					if($relaxe_acl && !$params['public'])
					{
						continue;
					}

					$row[$field] = $this->unmarshal($this->db->f($field, false), $params['type']);
				}
				$results[] = $row;
			}
			$id_map = array();
			if (count($results) > 0)
			{
				foreach ($results as $id => $result)
				{
					$id_map[$result['id']] = $id;
				}
				foreach ($this->fields as $field => $params)
				{
					if($relaxe_acl && !$params['public'])
					{
						continue;
					}
					if ($params['manytomany'])
					{
						$row[$field] = array();
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

							while ($this->db->next_record())
							{
								$id = $this->unmarshal($this->db->f($key, false), 'int');
								if(empty($results[$id_map[$id]][$field]))
								{
									$results[$id_map[$id]][$field] = array();
								}
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
							while ($this->db->next_record())
							{
								$id = $this->unmarshal($this->db->f($key, false), 'int');
								if(empty($results[$id_map[$id]][$field]))
								{
									$results[$id_map[$id]][$field] = array();
								}
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

			$conditions = "{$this->table_name}.id={$id_value}";

			return $conditions;
		}

		function read_single( $id, $return_object = false , $relaxe_acl = false)
		{
			if (!$id && !$return_object)
			{
				return array();
			}

			if($relaxe_acl)
			{
				$this->relaxe_acl = $relaxe_acl;
			}

			$row = array();
			$pk_params = $this->primary_key_conditions($id);

			$acl_condition = $this->get_acl_condition();

			$cols_joins = $this->_get_cols_and_joins();
			$cols = join(',', $cols_joins[0]);
			$joins = join(' ', $cols_joins[1]);

			/**
			 * test
			 */
			$acl_test = false;
			if($acl_condition)
			{
				$this->db->query("SELECT DISTINCT {$cols} FROM {$this->table_name} {$joins} WHERE {$pk_params} AND {$acl_condition}", __LINE__, __FILE__); //DISTINCT: There might be LEFT JOINs..
				if ($this->db->next_record())
				{
					$acl_test = true;
				}
			}

			$this->db->query("SELECT DISTINCT $cols FROM $this->table_name $joins WHERE $pk_params", __LINE__, __FILE__); //DISTINCT: There might be LEFT JOINs..
			if ($this->db->next_record())
			{
				if($acl_condition && !$acl_test)
				{
					$message = lang('do you represent the owner of this entry?');
					phpgw::no_access(false, $message);
				}

				foreach ($this->fields as $field => $params)
				{
					if (!empty($params['manytomany']))
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

							$this->db2->query("SELECT {$column} FROM {$table} WHERE {$key}={$id} {$order_method}", __LINE__, __FILE__);
							$row[$field] = array();
							while ($this->db2->next_record())
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

									$data[$col] = $this->unmarshal($this->db2->f($col, false), $type);
								}
								$row[$field][] = $data;
							}
						}
						else
						{
							$column = $params['manytomany']['column'];
							$this->db2->query("SELECT $column FROM $table WHERE $key=$id", __LINE__, __FILE__);
							$row[$field] = array();
							while ($this->db2->next_record())
							{
								$row[$field][] = $this->unmarshal($this->db2->f($column, false), $params['type']);
							}
						}
					}
					else
					{
						$row[$field] = $this->unmarshal($this->db->f($field, true), $params['type']);
					}
				}
			}
			if ($return_object)
			{
				return $this->populate($row);
			}
			else
			{
				return $row;
			}
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

						if (isset($params['multiple_join']) && $params['multiple_join'])
						{
							$table_column = $params['multiple_join']['column'];
						}
						else
						{
							$column = $params['join'] ? $params['join']['column'] : $field;
							$table_column = "{$table}.{$column}";
						}
						if ($params['type'] == 'int')
						{
							if (!(int)$query)
							{
								continue;
							}
							$like_clauses[] = "{$table_column} = " . (int)$query;//$this->db->db_addslashes($query);
						}
						else
						{
							$like_clauses[] = "{$table_column} $this->like $like_pattern";
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

			$clause = $this->get_acl_condition();

			if($clause)
			{
				$clauses[] = $clause;
			}

			return join(' AND ', $clauses);
		}

		function get_acl_condition( )
		{
			$clause = '';

			if(!$this->relaxe_acl && ($this->use_acl && $this->currentapp && $this->acl_location))
			{
				$paranthesis = false;

				$grants = $this->acl->get_grants2($this->currentapp, $this->acl_location);
				$public_user_list = array();
				if (is_array($grants['accounts']) && $grants['accounts'])
				{
					foreach($grants['accounts'] as $user => $_right)
					{
						$public_user_list[] = $user;
					}
					unset($user);
					reset($public_user_list);
					$clause .= "({$this->table_name}.owner_id IN(" . implode(',', $public_user_list) . ")";
					$paranthesis = true;
				}

				$public_group_list = array();
				if (is_array($grants['groups']) && $grants['groups'])
				{
					foreach($grants['groups'] as $user => $_right)
					{
						$public_group_list[] = $user;
					}
					unset($user);
					reset($public_group_list);
					$where = $public_user_list ? 'OR' : 'AND';
					if(!$paranthesis)
					{
						$clause .='(';
					}
					$clause .= " $where phpgw_group_map.group_id IN(" . implode(',', $public_group_list) . ")";

					$paranthesis = true;
				}

				if($paranthesis)
				{
					$clause .=')';
				}
			}

			return $clause;
		}

		protected function build_join_table_alias( $field, array $params )
		{
			return "{$params['join']['table']}_{$params['join']['column']}_{$field}";
		}

		public function _get_cols_and_joins( $query = '', $filters = array() )
		{
			$cols = array();
			$joins = array();

			foreach ($this->fields as $field => $params)
			{
				if (isset($params['manytomany']) && $params['manytomany'])
				{
					continue;
				}
				if (isset($params['related']) && $params['related'])
				{
					continue;
				}
				else if (isset($params['join']) && $params['join'])
				{
					if ($params['join_type'] == 'manytomany' && ( empty($filters[$field]) && empty($query) ) )
					{
						continue;
					}

					$join_table_alias = $this->build_join_table_alias($field, $params);
					$cols[] = "{$join_table_alias}.{$params['join']['column']} AS {$field}";
					$joins[] = " LEFT JOIN {$params['join']['table']} AS {$join_table_alias} ON({$join_table_alias}.{$params['join']['key']}={$this->table_name}.{$params['join']['fkey']})";
				}
				else if (isset($params['multiple_join']) && $params['multiple_join'])
				{
					$joins[] = " {$params['multiple_join']['statement']}";
					$cols[] = "{$params['multiple_join']['column']} AS {$field}";
				}
				else
				{
					$value_expression = isset($params['expression']) ?
						'(' . strtr($params['expression'], array('%%table%%' => $this->table_name)) . ')' : "{$this->table_name}.{$field}";
					$cols[] = "{$value_expression} AS {$field}";
				}
			}

			if(!$this->relaxe_acl && ($this->use_acl && $this->currentapp && $this->acl_location))
			{
				$joins[] = " JOIN phpgw_accounts ON ({$this->table_name}.owner_id = phpgw_accounts.account_id)";
				$joins[] = " JOIN phpgw_group_map ON (phpgw_accounts.account_id = phpgw_group_map.account_id)";
			}
			return array($cols, $joins);
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
			$object->entry_date = time();
			$value_set = array();

			$fields = $object::get_fields();

			foreach ($fields as $field => $field_info)
			{
				if (($field_info['action'] & PHPGW_ACL_ADD) && empty($field_info['join']) && empty($field_info['related']) && empty($field_info['manytomany']))
				{
					if ($field_info['type'] == 'json')
					{
						$value_set[$field] = json_encode($object->$field);
					}
					else
					{
						$value_set[$field] = $object->$field;
					}
				}
			}

			$class_info = explode('_', get_class($object), 2);
			$appname = $class_info[0];

			$attrib_table = $GLOBALS['phpgw']->locations->get_attrib_table($appname, $object::acl_location);
			$values_attribute = array();
			if($attrib_table)
			{
				$values_attribute = createObject('phpgwapi.custom_fields')->convert_attribute_save($object->values_attribute);
			}

			if ( $values_attribute)
			{
				foreach ($values_attribute as $attrib_id => $entry)
				{
					if ($entry['value'])
					{
						if ($entry['datatype'] == 'C' || $entry['datatype'] == 'T' || $entry['datatype'] == 'V' || $entry['datatype'] == 'link')
						{
							$entry['value'] = $this->db->db_addslashes($entry['value']);
						}
						$value_set[$entry['name']] = $entry['value'];
					}
				}
			}

			$sql = "INSERT INTO {$this->table_name} (" . implode(',', array_keys($value_set))
				. ') VALUES ('
				. $this->db->validate_insert(array_values($value_set))
				. ')';

			if ($this->db->get_transaction())
			{
				$this->global_lock = true;
			}
			else
			{
				$this->db->transaction_begin();
			}

			$this->db->query($sql, __LINE__, __FILE__);

			$id = $this->db->get_last_insert_id($this->table_name, 'id');
			$object->set_id($id);
			$this->add_manytomany($object);
			if (!$this->global_lock)
			{
				$this->db->transaction_commit();
			}
			return $object;
		}

		protected function update( $object )
		{
			if ($this->db->get_transaction())
			{
				$this->global_lock = true;
			}
			else
			{
				$this->db->transaction_begin();
			}

			$id = $object->get_id();
			$value_set = array();

			foreach ($this->fields as $field => $field_info)
			{
				if (($field_info['action'] & PHPGW_ACL_EDIT) && empty($field_info['join']) && empty($field_info['related']) && empty($field_info['manytomany']))
				{
					if ($field_info['type'] == 'json')
					{
						$value_set[$field] = json_encode($object->$field);
					}
					else
					{
						$value_set[$field] = $object->$field;
					}
				}
			}

			$class_info = explode('_', get_class($object), 2);
			$appname = $class_info[0];

			$attrib_table = $GLOBALS['phpgw']->locations->get_attrib_table($appname, $object::acl_location);
			$values_attribute = array();
			if($attrib_table)
			{
				$values_attribute = createObject('phpgwapi.custom_fields')->convert_attribute_save($object->values_attribute);
			}

			if ( $values_attribute)
			{
				foreach ($values_attribute as $attrib_id => $entry)
				{
					if ($entry['value'])
					{
						if ($entry['datatype'] == 'C' || $entry['datatype'] == 'T' || $entry['datatype'] == 'V' || $entry['datatype'] == 'link')
						{
							$entry['value'] = $this->db->db_addslashes($entry['value']);
						}
						$value_set[$entry['name']] = $entry['value'];
					}
				}
				$object->values_attribute = $values_attribute; // update with converted
			}

			$sql = "UPDATE {$this->table_name} SET "
				. $this->db->validate_update($value_set)
				. " WHERE id = {$id}";

			$ret1 = $this->db->query($sql, __LINE__, __FILE__);

			$ret2 = $this->add_manytomany($object);

			if (!$this->global_lock)
			{
				$this->db->transaction_commit();
			}
			return ($ret1 && $ret2);
		}

		protected function add_manytomany( $object )
		{
			$id = $object->get_id();

			foreach ($this->fields as $field => $params)
			{
				$value = $object->get_field($field);
				if (!empty($params['manytomany']) && !empty($params['manytomany']['input_field']) && $object->get_field($params['manytomany']['input_field']))
				{
					$table = $params['manytomany']['table'];
					$key = $params['manytomany']['key'];

					if (is_array($params['manytomany']['column']))
					{
						$colnames = array();
						foreach ($params['manytomany']['column'] as $intOrCol => $paramsOrCol)
						{
							$colnames[(is_array($paramsOrCol) ? $intOrCol : $paramsOrCol)] = true;
						}
						unset($colnames['id']);

						$colnames = join(',', array_keys($colnames));

						$v = $object->get_field($params['manytomany']['input_field']);

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

							if ($col == 'id')
							{
								continue;
							}

							$data[] = $this->marshal($v[$col], $type);
						}
						$v = join(',', $data);
						$update_query = "INSERT INTO $table ($key, $colnames) VALUES($id, $v)";
						return $this->db->query($update_query, __LINE__, __FILE__);
					}
					else
					{
						$colname = $params['manytomany']['column'];
						$v = $this->marshal($object->get_field($params['manytomany']['input_field']), $params['type']);
						$update_query = "INSERT INTO $table ($key, $colname) VALUES($id, $v)";
						return $this->db->query($update_query, __LINE__, __FILE__);
					}
				}
				else if (!empty($params['manytomany']) && is_array($value))
				{
					$update_queries = array();
					$table = $params['manytomany']['table'];
					$key = $params['manytomany']['key'];
					$update_queries[] = "DELETE FROM $table WHERE $key=$id";

					if (is_array($params['manytomany']['column']))
					{
						$colnames = array();
						foreach ($params['manytomany']['column'] as $intOrCol => $paramsOrCol)
						{
							$colnames[(is_array($paramsOrCol) ? $intOrCol : $paramsOrCol)] = true;
						}
						unset($colnames['id']);

						$colnames = join(',', array_keys($colnames));

						foreach ($value as $v)
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

								if ($col == 'id')
								{
									continue;
								}

								$data[] = $this->marshal($v, $type);
							}
							$v = join(',', $data);
							$update_queries[] = "INSERT INTO $table ($key, $colnames) VALUES($id, $v)";
						}
					}
					else
					{
						$colname = $params['manytomany']['column'];
						foreach ($value as $v)
						{
							$v = $this->marshal($v, $params['type']);
							$update_queries[] = "INSERT INTO $table ($key, $colname) VALUES($id, $v)";
						}
					}
					foreach ($update_queries as $update_query)
					{
						$this->db->query($update_query, __LINE__, __FILE__);
					}
				}
			}
			return true;
		}

		/**
		 * Store the object in the database.  If the object has no ID it is assumed to be new and
		 * inserted for the first time.  The object is then updated with the new insert id.
		 */
		public function store( &$object )
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
	}