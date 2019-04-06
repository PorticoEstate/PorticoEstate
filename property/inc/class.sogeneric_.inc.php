<?php
	/**
	 * phpGroupWare - property: a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2003,2004,2005,2006,2007,2008,2009 Free Software Foundation, Inc. http://www.fsf.org/
	 * This file is part of phpGroupWare.
	 *
	 * phpGroupWare is free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation; either version 2 of the License, or
	 * (at your option) any later version.
	 *
	 * phpGroupWare is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with phpGroupWare; if not, write to the Free Software
	 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	 *
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package property
	 * @subpackage admin
	 * @version $Id$
	 */

	/**
	 * Description
	 * @package property
	 */
	abstract class property_sogeneric_
	{

		var $type;
		var $type_id;
		var $location_info	 = array();
		var $tree			 = array();
		protected $table;
		var $appname			 = 'property';

		function __construct( $type = '', $type_id = 0 )
		{
			$this->account	 = $GLOBALS['phpgw_info']['user']['account_id'];
			$this->custom	 = createObject('property.custom_fields');
			$this->_db		 = & $GLOBALS['phpgw']->db;
			$this->_db2		 = clone($this->_db);
			$this->_like	 = & $this->_db->like;
			$this->_join	 = & $this->_db->join;

			if ($type)
			{
				$this->get_location_info($type, $type_id);
			}
		}

		function read( $data )
		{
			$start				 = isset($data['start']) && $data['start'] ? $data['start'] : 0;
			$query				 = isset($data['query']) ? $data['query'] : '';
			$sort				 = isset($data['sort']) && $data['sort'] ? $data['sort'] : 'DESC';
			$order				 = isset($data['order']) ? $data['order'] : '';
			$disable_id_search	 = empty($data['disable_id_search']) ? false : true;
			$allrows			 = isset($data['allrows']) ? $data['allrows'] : '';
			$custom_criteria	 = isset($data['custom_criteria']) && $data['custom_criteria'] ? $data['custom_criteria'] : array();
			$custom_filter		 = isset($data['custom_filter']) && $data['custom_filter'] ? $data['custom_filter'] : array();
			$filter				 = isset($data['filter']) && $data['filter'] ? $data['filter'] : array();
			$results			 = isset($data['results']) ? (int)$data['results'] : 0;
			$dry_run			 = isset($data['dry_run']) ? $data['dry_run'] : '';

			$values	 = array();
			if (!isset($this->location_info['table']) || !$table	 = $this->location_info['table'])
			{
				return $values;
			}


			$_join_method	 = array();
			$_filter_array	 = array();
			if ($custom_criteria)
			{
				foreach ($custom_criteria as $_custom_criteria)
				{
					if (isset($this->location_info['custom_criteria'][$_custom_criteria]['join']) && is_array($this->location_info['custom_criteria'][$_custom_criteria]['join']))
					{
						$_join_method = array_merge($_join_method, $this->location_info['custom_criteria'][$_custom_criteria]['join']);
					}
					if (isset($this->location_info['custom_criteria'][$_custom_criteria]['filter']) && is_array($this->location_info['custom_criteria'][$_custom_criteria]['filter']))
					{
						$_filter_array = array_merge($_filter_array, $this->location_info['custom_criteria'][$_custom_criteria]['filter']);
					}
				}
			}

			foreach ($this->location_info['fields'] as $field)
			{
				if (isset($field['filter']) && $field['filter'])
				{
					if (isset($filter[$field['name']]) && $filter[$field['name']] && $field['type'] == 'multiple_select')
					{
						$_filter_array[] = "{$field['name']} {$this->_like} '%,{$filter[$field['name']]},%'";
					}
					else if (!empty($filter[$field['name']]) && (isset($field['values_def']['method_input']['role']) && $field['values_def']['method_input']['role'] == 'parent'))
					{

						$field_object	 = clone($this);
						$field_object->get_location_info($field['values_def']['method_input']['type'], 0);
						$this->table	 = $field_object->location_info['table'];
						$children		 = $this->get_children2(array(), $filter[$field['name']], 0, true);

						$_children = array($filter[$field['name']]);
						if ($children)
						{
							foreach ($children as $_child)
							{
								$_children[] = $_child['id'];
							}
						}
						$_filter_array[] = "{$field['name']} IN (" . implode(',', $_children) . ')';
					}
					else if (isset($filter[$field['name']]) && $filter[$field['name']])
					{
						$_filter_array[] = "{$field['name']} = '{$filter[$field['name']]}'";
					}
				}
			}

			$uicols					 = array();
			$uicols['input_type'][]	 = 'text';
			$uicols['name'][]		 = $this->location_info['id']['name'];
			$uicols['descr'][]		 = lang('id');
			$uicols['datatype'][]	 = $this->location_info['id']['type'] == 'varchar' ? 'V' : 'I';
			$uicols['sortable'][]	 = true;
			$uicols['formatter'][]	 = '';

			foreach ($this->location_info['fields'] as $field)
			{
				$uicols['input_type'][]	 = isset($field['hidden']) && $field['hidden'] ? 'hidden' : 'text';
				$uicols['name'][]		 = $field['name'];
				$uicols['descr'][]		 = $field['descr'];
				$uicols['datatype'][]	 = $field['type'];
				$uicols['sortable'][]	 = isset($field['sortable']) && $field['sortable'] ? true : false;
				$uicols['formatter'][]	 = $field['type'] == 'int' ? 'FormatterRight' : '';
			}

			$custom_fields = false;
			if ($GLOBALS['phpgw']->locations->get_attrib_table($this->location_info['acl_app'], $this->location_info['acl_location']))
			{
				$custom_fields		 = true;
				$choice_table		 = 'phpgw_cust_choice';
				$attribute_table	 = 'phpgw_cust_attribute';
				$location_id		 = $GLOBALS['phpgw']->locations->get_id($this->location_info['acl_app'], $this->location_info['acl_location']);
				$attribute_filter	 = " location_id = {$location_id}";

				$user_columns = isset($GLOBALS['phpgw_info']['user']['preferences'][$this->location_info['acl_app']]["generic_columns_{$this->type}_{$this->type_id}"]) ? $GLOBALS['phpgw_info']['user']['preferences'][$this->location_info['acl_app']]["generic_columns_{$this->type}_{$this->type_id}"] : '';

				$user_column_filter = '';
				if (isset($user_columns) AND is_array($user_columns) AND $user_columns[0])
				{
					$user_column_filter = " OR ($attribute_filter AND id IN (" . implode(',', $user_columns) . '))';
				}

				$this->_db->query("SELECT * FROM $attribute_table WHERE list=1 AND $attribute_filter $user_column_filter ORDER BY attrib_sort ASC");

				$i = count($uicols['name']);
				while ($this->_db->next_record())
				{
					$uicols['input_type'][]	 = 'text';
					$uicols['name'][]		 = $this->_db->f('column_name');
					$uicols['descr'][]		 = $this->_db->f('input_text');
					$uicols['statustext'][]	 = $this->_db->f('statustext');
					$uicols['datatype'][$i]	 = $this->_db->f('datatype');
					$uicols['attib_id'][$i]	 = $this->_db->f('id');
					$uicols['formatter'][$i] = $this->_db->f('datatype') == 'I' ? 'FormatterRight' : '';
					$cols_return_extra[]	 = array(
						'name'		 => $this->_db->f('column_name'),
						'datatype'	 => $this->_db->f('datatype'),
						'attrib_id'	 => $this->_db->f('id')
					);

					$i++;
				}
			}

			$where			 = 'WHERE';
			$filtermethod	 = '';
			if (isset($this->location_info['check_grant']) && $this->location_info['check_grant'])
			{
				$filtermethod	 = "{$where} (user_id = {$this->account} OR public = 1)";
				$where			 = 'AND';
			}

			if (isset($this->location_info['filter']) && $this->location_info['filter'] && is_array($this->location_info['filter']))
			{
				$_filtermethod = array();
				foreach ($this->location_info['filter'] as $_argument => $_argument_value)
				{
					if (preg_match('/^##/', $_argument_value))
					{
						$_argument_value_name	 = trim($_argument_value, '#');
						$_argument_value		 = $values[$_argument_value_name];
					}
					if (preg_match('/^\$this->/', $_argument_value))
					{
						$_argument_value_name	 = ltrim($_argument_value, '$this->');
						$_argument_value		 = $this->$_argument_value_name;
					}

					$_filtermethod[] = "{$_argument} = '{$_argument_value}'";
				}


				if ($_filtermethod)
				{
					$filtermethod	 = "{$where} " . implode(' AND ', $_filtermethod);
					$where			 = 'AND';
				}
			}

			if ($_filter_array)
			{
				$filtermethod	 .= " $where " . implode(' AND ', $_filter_array);
				$where			 = 'AND';
			}

			if ($custom_filter)
			{
				$filtermethod	 .= " $where " . implode(' AND ', $custom_filter);
				$where			 = 'AND';
			}

			$this->uicols = $uicols;

			if ($dry_run)
			{
				return array();
			}

			if ($order)
			{
				$ordermethod = " ORDER BY {$table}.{$order} {$sort}";
			}
			else
			{
				$ordermethod = " ORDER BY {$table}.{$this->location_info['id']['name']} ASC";
			}

			if ($query)
			{
				if ($this->location_info['id']['type'] == 'auto' || $this->location_info['id']['type'] == 'int')
				{
					$id_query = (int)$query;
				}
				else
				{
					$id_query = "'{$query}'";
				}

				$_query_start	 = '';
				$_query_end		 = '';

				if ($filtermethod)
				{
					$_query_start	 = '(';
					$_query_end		 = ')';
				}
				$query = $this->_db->db_addslashes($query);
				if ($disable_id_search)
				{
					$querymethod = " {$where } {$_query_start} (1=0";
				}
				else
				{
					$querymethod = " {$where } {$_query_start} ({$table}.{$this->location_info['id']['name']} = {$id_query}";
					if ($this->location_info['id']['type'] == 'varchar')
					{
						$querymethod .= " OR {$table}.{$this->location_info['id']['name']} $this->_like '%$query%'";
						$where		 = 'OR';
					}
					else
					{
						$querymethod .= " OR CAST ({$table}.{$this->location_info['id']['name']} AS TEXT) $this->_like '%$query%'";
					}
				}
				//_debug_array($filtermethod);
				//_debug_array($where);die();

				$where = 'OR';

				foreach ($this->location_info['fields'] as $field)
				{
					if ($field['type'] == 'varchar' || $field['type'] == 'text' || $field['type'] == 'location')
					{
						$querymethod .= " OR {$table}.{$field['name']} $this->_like '%$query%'";
					}
				}
				$querymethod .= ')';

				if ($custom_fields)
				{
					$_querymethod = array();

					$this->_db->query("SELECT * FROM $attribute_table WHERE $attribute_filter AND search='1'", __LINE__, __FILE__);

					while ($this->_db->next_record())
					{
						if ($this->_db->f('datatype') == 'V' || $this->_db->f('datatype') == 'email' || $this->_db->f('datatype') == 'CH')
						{
							$_querymethod[] = "$table." . $this->_db->f('column_name') . " {$this->_like} '%{$query}%'";
						}
						else if ($this->_db->f('datatype') == 'I')
						{
							if (ctype_digit($query))
							{
								//	$_querymethod[] = "$table." . $this->_db->f('column_name') . '=' . (int)$query;
								$_querymethod[] = "CAST ($table." . $this->_db->f('column_name') . " AS TEXT) {$this->_like} '%" . (int)$query . "%'";
							}
						}
						else
						{
							$_querymethod[] = "$table." . $this->_db->f('column_name') . " = '$query'";
						}
					}

					if (isset($_querymethod) && is_array($_querymethod) && $_querymethod)
					{
						$querymethod .= " $where (" . implode(' OR ', $_querymethod) . ')';
					}
				}

				$querymethod .= $_query_end;
			}

			$join_method = $_join_method ? implode(' ', $_join_method) : '';

			$sql = "SELECT DISTINCT {$table}.* FROM {$table} {$join_method} {$filtermethod} {$querymethod}";

			$this->_db->query('SELECT count(*) as cnt ' . substr($sql, strripos($sql, 'from')), __LINE__, __FILE__);
			$this->_db->next_record();
			$this->total_records = $this->_db->f('cnt');

			if (!$allrows)
			{
				$this->_db->limit_query($sql . $ordermethod, $start, __LINE__, __FILE__, $results);
			}
			else
			{
				$this->_db->query($sql . $ordermethod, __LINE__, __FILE__);
			}

			$cols_return = $uicols['name'];
			$j			 = 0;

			$dataset = array();
			while ($this->_db->next_record())
			{
				foreach ($cols_return as $key => $field)
				{
					$dataset[$j][$field] = array
						(
						'value'		 => $this->_db->f($field, true),
						'datatype'	 => $uicols['datatype'][$key],
						'attrib_id'	 => $uicols['attib_id'][$key]
					);
				}
				$j++;
			}

			$values = $this->custom->translate_value($dataset, $location_id);

			return $values;
		}

		abstract function get_location_info( $type, $type_id = 0 );

		public function get_name( $data )
		{
			$mapping = array();
			if (isset($data['mapping']) && $data['mapping'])
			{
				$mapping = $data['mapping'];
			}
			else
			{
				$mapping = array('name' => 'name');
			}

			if (isset($data['type']) && $data['type'])
			{
				$this->get_location_info($data['type']);
			}
			$values = $this->read_single($data);
			return isset($values[$mapping['name']]) ? $values[$mapping['name']] : $values['descr'];
		}

		function read_single( $data, $values = array() )
		{
			if (!isset($this->location_info['table']) || !$table = $this->location_info['table'])
			{
				return $values;
			}

			if ($this->location_info['id']['type'] == 'auto' || $this->location_info['id']['type'] == 'int')
			{
				$id = (int)$data['id'];
			}
			else
			{
				$id = "'" . $this->_db->db_addslashes($data['id']) . "'";
			}

			$sql = "SELECT * FROM $table WHERE {$this->location_info['id']['name']} = {$id}";

			$this->_db->query($sql, __LINE__, __FILE__);

			if ($this->_db->next_record())
			{
				$values['id'] = $this->_db->f($this->location_info['id']['name']);

				// FIXME - add field to $values['attributes']
				foreach ($this->location_info['fields'] as $field)
				{
					$values[$field['name']] = $this->_db->f($field['name'], true);
				}

				if (isset($values['attributes']) && is_array($values['attributes']))
				{
					foreach ($values['attributes'] as &$attr)
					{
						$attr['value'] = $this->_db->f($attr['column_name'], true);
					}
				}
			}
			return $values;
		}

		//deprecated
		function select_generic_list( $data )
		{
			return $this->get_entity_list($data);
		}

		function get_list( $data )
		{
			$values = array();

			$this->get_location_info($data['type'], $data['type_id']);

			if (!isset($this->location_info['table']) || !$table = $this->location_info['table'])
			{
				return $values;
			}

			$filtermthod = '';
			if (isset($data['filter']) && is_array($data['filter']))
			{
				$_filter = array();
				foreach ($data['filter'] as $_field => $_value)
				{
					if ($data['filter_method'] == 'like')
					{
						$_filter[] = "{$_field} {$this->_db->like} '%{$_value}%'";
					}
					else
					{
						$_filter[] = "{$_field} = '{$_value}'";
					}
				}
				if ($_filter)
				{
					$filtermthod = 'WHERE ' . implode(' AND ', $_filter);
				}
			}

			$order = isset($data['order']) && $data['order'] ? $data['order'] : '';

			if ($order)
			{
				$ordermethod = " ORDER BY {$table}.{$order} {$sort}";
			}
			else
			{
				$ordermethod = " ORDER BY {$table}.{$this->location_info['id']['name']} ASC";
			}

			foreach ($this->location_info['fields'] as $field)
			{
				$fields[] = $field['name'];
			}

			// Add extra info to name
			if (isset($data['id_in_name']) && $data['id_in_name'])
			{
				$id_in_name = 'id';
				if (in_array($data['id_in_name'], $fields))
				{
					$id_in_name = $data['id_in_name'];
				}
			}

			$fields = implode(',', $fields);

			$this->_db->query("SELECT {$this->location_info['id']['name']} as id, {$fields} FROM {$table} {$filtermthod} {$ordermethod}");

			$return_fields = isset($data['fields']) && $data['fields'] && is_array($data['fields']) ? $data['fields'] : array();


			$mapping = array();
			if (isset($data['mapping']) && $data['mapping'])
			{
				$mapping = $data['mapping'];
			}
			else
			{
				$mapping = array('name' => 'name');
			}

			$i = 0;
			while ($this->_db->next_record())
			{
				$_extra	 = $this->_db->f($id_in_name, true);
				$id		 = $this->_db->f('id');
				if (!$name	 = $this->_db->f($mapping['name'], true))
				{
					$name = $this->_db->f('descr', true);
				}

				if ($_extra)
				{
					$name = "{$_extra} - {$name}";
				}

				$values[$i] = array
					(
					'id'	 => $id,
					'name'	 => $name
				);

				foreach ($return_fields as $return_field)
				{
					$values[$i][$return_field] = $this->_db->f($return_field, true);
				}

				$i++;
			}
			return $values;
		}

		function add( $data, $values_attribute )
		{
			$receipt = array();

			if (!isset($this->location_info['table']) || !$table = $this->location_info['table'])
			{
				$receipt['error'][] = array('msg' => lang('not a valid type'));
				return $receipt;
			}

			if (isset($data['save']))
			{
				unset($data['save']);
			}
			if (isset($data['apply']))
			{
				unset($data['apply']);
			}

			// in case of backslash characters - as in path-references
			foreach ($data as $_key => &$_value)
			{
				$_value = str_replace('\\', '/', $_value);
			}
			unset($_key);
			unset($_value);


			foreach ($this->location_info['fields'] as $field)
			{
				if (isset($field['filter']) && $field['filter'])
				{
					if (isset($data[$field['name']]) && $data[$field['name']] && $field['type'] == 'multiple_select')
					{
						$data[$field['name']] = ',' . implode(',', $data[$field['name']]) . ',';
					}
				}
				if ($field['type'] == 'varchar')
				{
					$data[$field['name']] = $this->_db->db_addslashes(html_entity_decode($data[$field['name']]));
				}
			}

			$cols	 = array();
			$vals	 = array();

			if (isset($data['extra']))
			{
				foreach ($data['extra'] as $input_name => $value)
				{
					if (isset($value) && $value)
					{
						$cols[]	 = $input_name;
						$vals[]	 = $value;
					}
				}
			}
			unset($data['extra']);

			foreach ($data as $input_name => $value)
			{
				if (isset($value) && ($value || $value === '0'))
				{
					$cols[]	 = $input_name;
					$vals[]	 = $this->_db->db_addslashes(html_entity_decode($value));
				}
			}

			$data_attribute = $this->custom->prepare_for_db($table, $values_attribute);
			if (isset($data_attribute['value_set']))
			{
				foreach ($data_attribute['value_set'] as $input_name => $value)
				{
					if (isset($value) && $value)
					{
						$cols[]	 = $input_name;
						$vals[]	 = $this->_db->db_addslashes(html_entity_decode($value));
					}
				}
			}


			if (isset($this->location_info['default']) && is_array($this->location_info['default']))
			{
				foreach ($this->location_info['default'] as $field => $default)
				{
					if (isset($default['add']))
					{
						$cols[] = $field;
						eval('$vals[] = ' . $default['add'] . ';');
					}
				}
			}

			$this->_db->transaction_begin();

			if ($this->location_info['id']['type'] != 'auto')
			{
				$this->_db->query("SELECT {$this->location_info['id']['name']} AS id FROM {$table} WHERE {$this->location_info['id']['name']} = '{$data[$this->location_info['id']['name']]}'", __LINE__, __FILE__);
				if ($this->_db->next_record())
				{
					$receipt['error'][]	 = array('msg' => lang('duplicate key value'));
					$receipt['error'][]	 = array('msg' => lang('record has not been saved'));
					return $receipt;
				}
				$id = $data[$this->location_info['id']['name']];
			}
			else
			{
				$id		 = $this->_db->next_id($table);
				$cols[]	 = 'id';
				$vals[]	 = $id;
			}

			$cols	 = implode(",", $cols);
			$vals	 = $this->_db->validate_insert($vals);

			$this->_db->query("INSERT INTO {$table} ({$cols}) VALUES ({$vals})", __LINE__, __FILE__);

			/* 			if($this->location_info['id']['type']=='auto')
			  {
			  if(!$data['id'] = $this->_db->get_last_insert_id($table, 'id'))
			  {
			  $this->_db->transaction_abort();
			  $receipt['error'][]=array('msg'=>lang('record has not been saved'));
			  }
			  }
			 */
			$this->_db->transaction_commit();
			$receipt['id']			 = $id;
			$receipt['message'][]	 = array('msg' => lang('record has been saved'));
			return $receipt;
		}

		function edit( $data, $values_attribute )
		{

			$receipt = array();

			if (!isset($this->location_info['table']) || !$table = $this->location_info['table'])
			{
				$receipt['error'][] = array('msg' => lang('not a valid type'));
				return $receipt;
			}

			// in case of backslash characters - as in path-references
			foreach ($data as $_key => &$_value)
			{
				$_value = str_replace('\\', '/', $_value);
			}
			unset($_key);
			unset($_value);

			$value_set = array();

			if (isset($data['extra']))
			{
				foreach ($data['extra'] as $input_name => $value)
				{
					$value_set[$input_name] = $value;
				}
				unset($data['extra']);
			}

			$data_attribute = $this->custom->prepare_for_db($table, $values_attribute, $data['id']);

			if (isset($data_attribute['value_set']))
			{
				$value_set = array_merge($value_set, $data_attribute['value_set']);
			}

			$has_to_move = array();

			foreach ($this->location_info['fields'] as $field)
			{
				if (isset($field['filter']) && $field['filter'])
				{
					if (isset($data[$field['name']]) && $data[$field['name']] && $field['type'] == 'multiple_select')
					{
						$data[$field['name']] = ',' . implode(',', $data[$field['name']]) . ',';
					}
				}
				$value_set[$field['name']] = $this->_db->db_addslashes(html_entity_decode($data[$field['name']]));

				// keep hierarchy in order
				if (isset($field['role']) && $field['role'] == 'parent')
				{
					//FIXME				
					$this->_db->query("SELECT parent_id FROM $table WHERE {$this->location_info['id']['name']}='{$data['id']}'", __LINE__, __FILE__);
					$this->_db->next_record();
					$orig_parent_id = $this->_db->f('parent_id');

					if ($orig_parent_id && (int)$orig_parent_id != (int)$data['parent_id'])
					{

						$this->_db->query("SELECT {$this->location_info['id']['name']} as id FROM $table WHERE parent_id ='{$data['id']}'", __LINE__, __FILE__);

						while ($this->_db->next_record())
						{
							$has_to_move[] = $this->_db->f('id');
						}
					}
				}
			}

			if (isset($this->location_info['default']) && is_array($this->location_info['default']))
			{
				foreach ($this->location_info['default'] as $field => $default)
				{
					if (isset($default['edit']))
					{
						eval('$value_set[$field] = ' . $default['edit'] . ';');
					}
				}
			}

			$value_set = $this->_db->validate_update($value_set);
			$this->_db->transaction_begin();
			$this->_db->query("UPDATE $table SET {$value_set} WHERE {$this->location_info['id']['name']} = '{$data['id']}'", __LINE__, __FILE__);

			// keep hierarchy in order
			foreach ($has_to_move as $id)
			{
				$value_set = $this->_db->validate_update(array('parent_id' => $orig_parent_id));
				$this->_db->query("UPDATE $table SET {$value_set} WHERE {$this->location_info['id']['name']} = '{$id}'", __LINE__, __FILE__);
			}

			//FIXME
			if (isset($data_attribute['history_set']) && is_array($data_attribute['history_set']))
			{
				$historylog = CreateObject('property.historylog', $this->location_info['acl_app'], $this->location_info['acl_location']);
				foreach ($data_attribute['history_set'] as $attrib_id => $history)
				{
					$historylog->add('SO', $data['id'], $history['value'], isset($history['old_value']) ? $history['old_value'] : null, $attrib_id, $history['date']);
				}
			}

			$this->_db->transaction_commit();

			$receipt['id'] = $data['id'];

			$receipt['message'][] = array('msg' => lang('record has been edited'));
			return $receipt;
		}

		function delete( $id )
		{
			if (!isset($this->location_info['table']) || !$table = $this->location_info['table'])
			{
				return false;
			}

			$has_to_move = array();

			$this->_db->transaction_begin();

			foreach ($this->location_info['fields'] as $field)
			{
				// keep hierarchy in order
				if (isset($field['role']) && $field['role'] == 'parent')
				{
					$this->_db->query("SELECT parent_id FROM $table WHERE {$this->location_info['id']['name']}='{$id}'", __LINE__, __FILE__);
					$this->_db->next_record();
					$orig_parent_id = $this->_db->f('parent_id');

					$this->_db->query("SELECT {$this->location_info['id']['name']} as id FROM $table WHERE parent_id ='{$id}'", __LINE__, __FILE__);

					while ($this->_db->next_record())
					{
						$has_to_move[] = $this->_db->f('id');
					}
				}
			}

			$this->_db->query("DELETE FROM $table WHERE {$this->location_info['id']['name']}='{$id}'", __LINE__, __FILE__);

			// keep hierarchy in order
			foreach ($has_to_move as $id)
			{
				$value_set = $this->_db->validate_update(array('parent_id' => $orig_parent_id));
				$this->_db->query("UPDATE $table SET {$value_set} WHERE {$this->location_info['id']['name']} = '{$id}'", __LINE__, __FILE__);
			}

			$this->_db->transaction_commit();
		}

		public function get_tree2( $data )
		{
			$values = array();

			$this->get_location_info($data['type'], $data['type_id']);

			if (!isset($this->location_info['table']) || !$table = $this->location_info['table'])
			{
				return $values;
			}
			$this->table = $table;

			$filtermthod = 'WHERE (parent_id = 0 OR parent_id IS NULL)';

			if (isset($data['filter']) && is_array($data['filter']))
			{
				$_filter = array();
				foreach ($data['filter'] as $_field => $_value)
				{
					$_filter[] = "{$_field} = '{$_value}'";
				}
				if ($_filter)
				{
					$filtermthod .= implode(' AND ', $_filter);
				}
			}

			$order = isset($data['order']) && $data['order'] ? $data['order'] : '';

			if ($order)
			{
				$ordermethod = " ORDER BY {$table}.{$order} {$sort}";
			}
			else
			{
				$ordermethod = " ORDER BY {$table}.{$this->location_info['id']['name']} ASC";
			}

			foreach ($this->location_info['fields'] as $field)
			{
				$fields[] = $field['name'];
			}

			// Add extra info to name
			if (isset($data['id_in_name']) && $data['id_in_name'])
			{
				$id_in_name = 'id';
				if (in_array($data['id_in_name'], $fields))
				{
					$id_in_name = $data['id_in_name'];
				}
			}

			$fields = implode(',', $fields);

			$this->_db->query("SELECT id, {$fields} FROM {$table} {$filtermthod} {$ordermethod}", __LINE__, __FILE__);

			$return_fields	 = isset($data['fields']) && $data['fields'] && is_array($data['fields']) ? $data['fields'] : array();
//-----------
			$mapping		 = array();
			if (isset($data['mapping']) && $data['mapping'])
			{
				$mapping = $data['mapping'];
			}
			else
			{
				$mapping = array('name' => 'name');
			}

			$values	 = array();
			$i		 = 0;
			while ($this->_db->next_record())
			{
				$_extra	 = $this->_db->f($id_in_name);
				$id		 = $this->_db->f('id');
				$name	 = $this->_db->f($mapping['name'], true);

				if ($_extra)
				{
					$name = "{$_extra} - {$name}";
				}

				$values[$i] = array
					(
					'id'		 => $id,
					'name'		 => $name,
					'parent_id'	 => 0
				);

				foreach ($return_fields as $return_field)
				{
					$values[$i][$return_field] = $this->_db->f($return_field, true);
				}

				$i++;
			}


			$this->tree = array();

			foreach ($values as $value)
			{
				$this->tree[] = $value;
				$this->get_children2($data, $value['id'], 1);
			}
			return $this->tree;
		}

		public function get_children2( $data, $parent, $level, $reset = false )
		{
			$parent	 = (int)$parent;
			$mapping = array();
			if (isset($data['mapping']) && $data['mapping'])
			{
				$mapping = $data['mapping'];
			}
			else
			{
				$mapping = array('name' => 'name');
			}

			if ($reset)
			{
				$this->tree = array();
			}
			$db		 = clone($this->_db);
			if (!$table	 = $this->table)
			{
				return $this->tree;
			}
			$sql = "SELECT * FROM {$table} WHERE parent_id = {$parent}";

			$db->query($sql, __LINE__, __FILE__);

			while ($db->next_record())
			{
				$id				 = $db->f('id');
				$this->tree[]	 = array
					(
					'id'		 => $id,
					'name'		 => str_repeat('..', $level) . $db->f($mapping['name'], true),
					'parent_id'	 => $parent
				);
				$this->get_children2($data, $id, $level + 1);
			}
			return $this->tree;
		}

		/**
		 * used for retrive a child-node from a hierarchy
		 *
		 * @param integer $entity_id Entity id
		 * @param integer $parent is the parent of the children we want to see
		 * @param integer $level is increased when we go deeper into the tree,
		 * @return array $child Children
		 */
		protected function get_children( $data, $parent, $level )
		{
			$children = array();

			$this->get_location_info($data['type'], $data['type_id']);

			if (!isset($this->location_info['table']) || !$table = $this->location_info['table'])
			{
				return $children;
			}
			$this->table = $table;

			$filtermthod = 'WHERE parent_id = ' . (int)$parent;

			$sql = "SELECT * FROM {$table} {$filtermthod}";
			$this->_db2->query($sql, __LINE__, __FILE__);

			$fields = array(0 => 'id');
			foreach ($this->location_info['fields'] as $field)
			{
				$fields[] = $field['name'];
			}

			while ($this->_db2->next_record())
			{
				$id = $this->_db2->f('id');
				foreach ($fields as $field)
				{
					$children[$id][$field] = $this->_db2->f($field, true);
				}
			}

			foreach ($children as &$child)
			{
				$_children = $this->get_children($data, $child['id'], $level + 1);
				if ($_children)
				{
					$child['children'] = $_children;
				}
			}
			return $children;
		}

		/**
		 * Get tree from your node
		 * @param array $data - 'node_id' as parent and 'type'
		 * @return array tree
		 */
		public function read_tree( $data )
		{
			$parent_id	 = isset($data['node_id']) && $data['node_id'] ? (int)$data['node_id'] : 0;
			$tree		 = array();

			$this->get_location_info($data['type'], $data['type_id']);

			if (!isset($this->location_info['table']) || !$table = $this->location_info['table'])
			{
				return $tree;
			}
			$this->table = $table;

			if ($parent_id)
			{
				$filtermthod = "WHERE parent_id = {$parent_id}";
			}
			else
			{
				$filtermthod = 'WHERE (parent_id = 0 OR parent_id IS NULL)';
			}

			if (isset($data['filter']) && is_array($data['filter']))
			{
				$_filter = array();
				foreach ($data['filter'] as $_field => $_value)
				{
					$_filter[] = "{$_field} = '{$_value}'";
				}
				if ($_filter)
				{
					$filtermthod .= ' AND ' . implode(' AND ', $_filter);
				}
			}

			$sql = "SELECT * FROM {$table} {$filtermthod}";

			$this->_db2->query($sql, __LINE__, __FILE__);
			$this->total_records = $this->_db2->num_rows();

			$fields = array(0 => 'id');
			foreach ($this->location_info['fields'] as $field)
			{
				$fields[] = $field['name'];
			}
			$node	 = array();
			$i		 = 0;
			while ($this->_db2->next_record())
			{
				$id = $this->_db2->f('id');

				foreach ($fields as $field)
				{
					$tree[$i][$field] = $this->_db2->f($field, true);
				}
				$i++;
			}

			foreach ($tree as &$node)
			{
				$children = $this->get_children($data, $node['id'], 0);
				if ($children)
				{
					$node['children'] = $children;
				}
			}
			return $tree;
		}

		/**
		 * used for retrive the path for a particular node from a hierarchy
		 *
		 * @param integer $node is the id of the node we want the path of
		 * @return array $path Path
		 */
		public function get_path( $data )
		{

			$this->get_location_info($data['type'], $data['type_id']);

			if (!isset($this->location_info['table']) || !$table = $this->location_info['table'])
			{
				return array();
			}
			//		$this->table = $table;

			if(!empty($data['path_by_id']))
			{
				$mapping = array('name' => 'id');				
			}
			else if (isset($this->location_info['mapping']) && $this->location_info['mapping'])
			{
				$mapping = $this->location_info['mapping'];
			}
			else
			{
				$mapping = array('name' => 'name');
			}

			$sql = "SELECT {$mapping['name']} AS name, parent_id FROM {$table} WHERE id = '{$data['id']}'";

			$this->_db->query($sql, __LINE__, __FILE__);
			$this->_db->next_record();

			$parent_id = $this->_db->f('parent_id');

			$name = $this->_db->f('name', true);

			$path = array($name);

			if ($parent_id)
			{
				$data['id'] = $parent_id;
				$path = array_merge($this->get_path($data), $path);
			}
			return $path;
		}

		public function edit_field( $data = array() )
		{
			if (!isset($this->location_info['table']) || !$table = $this->location_info['table'])
			{
				return false;
			}

			$value_set = $this->_db->validate_update(array($data['field_name'] => $data['value']));
			return $this->_db->query("UPDATE $table SET {$value_set} WHERE {$this->location_info['id']['name']} = '{$data['id']}'", __LINE__, __FILE__);
		}
	}