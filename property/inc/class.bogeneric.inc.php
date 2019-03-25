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
	class property_bogeneric
	{

		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;
		var $location_info	 = array();
		var $appname;
		var $allrows;
		var $public_functions = array
			(
			'get_autocomplete'	 => true,
			'get_single_name'	 => true
		);

		function __construct( $session = false, $call_appname = '' )
		{
			if (!$call_appname) // call from mobilefrontend
			{
				$called_class		 = get_called_class();
				$called_class_arr	 = explode('_', $called_class);
				$call_appname		 = !empty($called_class_arr[0]) && !empty($GLOBALS['phpgw_info']['apps'][$called_class_arr[0]]) ? $called_class_arr[0] : 'property';
			}
			$this->so = CreateObject("{$call_appname}.sogeneric");

			$this->custom	 = & $this->so->custom;
			$this->bocommon	 = CreateObject('property.bocommon');

			$start	 = phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query	 = phpgw::get_var('query');
			$sort	 = phpgw::get_var('sort', 'string', 'REQUEST', 'DESC');
			$order	 = phpgw::get_var('order');
			$filter	 = phpgw::get_var('filter', 'int');
			$cat_id	 = phpgw::get_var('cat_id', 'int');
			$allrows = phpgw::get_var('allrows', 'bool');
			$appname = phpgw::get_var('appname', 'string');

			if ($appname)
			{
				$this->appname		 = $appname;
				$this->so->appname	 = $appname;
			}

			$type			 = phpgw::get_var('type');
			$type_id		 = phpgw::get_var('type_id', 'int', 'REQUEST', 0);
			$this->type		 = $type;
			$this->type_id	 = $type_id;

			if ($session)
			{
				$this->read_sessiondata($type);
				$this->use_session = true;
			}

			$this->start	 = $start ? $start : 0;
			$this->query	 = isset($_REQUEST['query']) ? $query : $this->query;
			$this->sort		 = isset($_REQUEST['sort']) ? $sort : $this->sort;
			$this->order	 = isset($_REQUEST['order']) && $_REQUEST['order'] ? $order : $this->order;
			$this->filter	 = isset($_REQUEST['filter']) ? $filter : $this->filter;
			$this->cat_id	 = isset($_REQUEST['cat_id']) ? $cat_id : $this->cat_id;
			$this->allrows	 = isset($allrows) ? $allrows : false;

//			$this->location_info = $this->so->get_location_info($type, $type_id);
		}

		public function save_sessiondata( $data )
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data', "generic_{$data['type']}", $data);
			}
		}

		function read_sessiondata( $type )
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data', "generic_{$type}");

			//		_debug_array($data);

			$this->start	 = $data['start'];
			$this->query	 = $data['query'];
			$this->filter	 = $data['filter'];
			$this->sort		 = $data['sort'];
			$this->order	 = $data['order'];
			$this->cat_id	 = $data['cat_id'];
			$this->allrows	 = $data['allrows'];
		}

		public function get_location_info( $type = '', $type_id = 0 )
		{
			$type				 = $type ? $type : $this->type;
			$type_id			 = $type_id ? $type_id : $this->type_id;
			return $this->location_info = $this->so->get_location_info($type, $type_id);
		}

		function column_list( $selected = '', $allrows = '' )
		{
			if (!$selected)
			{
				$selected = $GLOBALS['phpgw_info']['user']['preferences'][$this->location_info['acl_app']]["generic_columns_{$this->type}_{$this->type_id}"];
			}

			$filter			 = array('list' => ''); // translates to "list IS NULL"
			$system_location = $this->location_info['system_location'] ? $this->location_info['system_location'] : $this->location_info['acl_location'];

			$columns	 = $this->custom->find($this->location_info['acl_app'], $system_location, 0, '', '', '', true, false, $filter);
			$column_list = $this->bocommon->select_multi_list($selected, $columns);

			return $column_list;
		}

		public function read( $data = array() )
		{
			if (isset($data['location_info']) && $data['location_info']['type'])
			{
				$this->get_location_info($data['location_info']['type'], (int)$data['location_info']['type_id']);
				unset($data['location_info']);
			}
			$values = $this->so->read($data);

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			foreach ($values as &$entry)
			{
				foreach ($this->location_info['fields'] as $field)
				{
					if (isset($entry[$field['name']]) && $entry[$field['name']])
					{
						if ($field['type'] == 'date')
						{
							$entry[$field['name']] = $GLOBALS['phpgw']->common->show_date($entry[$field['name']], $dateformat);
						}
						else if (isset($field['values_def']['get_single_value']) && $field['values_def']['get_single_value'] == 'get_user')
						{
							$entry[$field['name']] = $GLOBALS['phpgw']->accounts->get($entry[$field['name']])->__toString();
						}
						else if ((isset($field['role']) && $field['role'] == 'parent' || isset($field['values_def']['method_input']['role']) && $field['values_def']['method_input']['role'] == 'parent') && !empty($field['values_def']['method_input']['type']))
						{
							$path					 = $this->so->get_path(array('type'	 => $field['values_def']['method_input']['type'],
								'id'	 => $entry[$field['name']]));
							$entry[$field['name']]	 = implode(' > ', $path);
						}
						else if (isset($field['values_def']['get_single_value']) && $field['values_def']['get_single_value'])
						{
							$entry[$field['name']] = execMethod($field['values_def']['get_single_value'], array_merge(array(
								'id' => $entry[$field['name']]), $field['values_def']['method_input']));
						}
					}
				}
			}

			$this->total_records = $this->so->total_records;
			$this->uicols		 = $this->so->uicols;

			return $values;
		}

		public function read_single( $data = array() )
		{
			$values = array();
			if (isset($data['location_info']) && $data['location_info']['type'])
			{
				$this->get_location_info($data['location_info']['type'], (int)$data['location_info']['type_id']);
				unset($data['location_info']);
			}
			$custom_fields	 = false;
			$system_location = !empty($this->location_info['system_location']) ? $this->location_info['system_location'] : $this->location_info['acl_location'];
			if ($GLOBALS['phpgw']->locations->get_attrib_table($this->location_info['acl_app'], $this->location_info['acl_location']))
			{
				$custom_fields			 = true;
				$values['attributes']	 = $this->custom->find($this->location_info['acl_app'], $system_location, 0, '', 'ASC', 'attrib_sort', true, true);
			}

			if (isset($data['id']) && $data['id'] || (string)$data['id'] === '0')
			{
				$values = $this->so->read_single($data, $values);
			}
			if ($custom_fields)
			{
				$values = $this->custom->prepare($values, $this->location_info['acl_app'], $system_location, $data['view']);
			}
			return $values;
		}

		public function save( $data, $action = '', $values_attribute = array() )
		{
			if (is_array($values_attribute))
			{
				$values_attribute = $this->custom->convert_attribute_save($values_attribute);
			}

			if ($action == 'edit')
			{
				if ($data['id'] !== '')
				{

					$receipt = $this->so->edit($data, $values_attribute);
				}
			}
			else
			{
				$receipt = $this->so->add($data, $values_attribute);
			}

			return $receipt;
		}

		/**
		 * Get a list from and tag the selected item
		 *
		 * @param array $data
		 *
		 * @return array with information to include in forms
		 */
		public function get_list( $data )
		{

			if (isset($data['role']) && $data['role'] == 'parent')
			{
				$values = $this->so->get_tree2($data);
			}
			else
			{
				$values = $this->so->get_list($data);
			}

			if (isset($data['add_empty']) && $data['add_empty'])
			{
				array_unshift($values, array('id' => '', 'name' => lang('select')));
			}

			if (isset($data['selected']) && is_array($data['selected']))
			{
				foreach ($values as &$entry)
				{
					$entry['selected'] = in_array($entry['id'], $data['selected']);
				}
			}
			else
			{
				foreach ($values as &$entry)
				{
					$entry['selected'] = isset($data['selected']) && trim($data['selected']) === trim($entry['id']) ? 1 : 0;
				}
			}
			return $values;
		}

		public function delete( $id )
		{
			$this->so->delete($id);
		}

		public function get_children2( $id, $level, $reset = false )
		{
			return $this->so->get_children2($id, $level, $reset);
		}

		public function read_attrib_history( $data )
		{
			$system_location = $data['system_location'] ? $data['system_location'] : $data['acl_location'];
			$attrib_data	 = $this->custom->get($data['appname'], $system_location, $data['attrib_id'], $inc_choices	 = true);
			$historylog		 = CreateObject('property.historylog', $data['appname'], $data['acl_location']);
			$history_values	 = $historylog->return_array(array(), array('SO'), 'history_timestamp', 'DESC', $data['id'], $data['attrib_id']);

			if ($attrib_data['column_info']['type'] == 'LB')
			{
				foreach ($history_values as &$value_set)
				{
					foreach ($attrib_data['choice'] as $choice)
					{
						if ($choice['id'] == $value_set['new_value'])
						{
							$value_set['new_value'] = $choice['value'];
						}
					}
				}
			}


			if ($attrib_data['column_info']['type'] == 'D')
			{
				foreach ($history_values as &$value_set)
				{
					$value_set['new_value'] = date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], strtotime($value_set['new_value']));
				}
			}

			reset($history_values);
			$this->total_records = count($history_values);
			return $history_values;
		}

		public function delete_history_item( $data )
		{
			$historylog = CreateObject('property.historylog', $data['appname'], $data['acl_location']);
			$historylog->delete_single_record((int)$data['history_id']);
		}

		function get_history_type_for_location( $acl_location )
		{
			switch ($acl_location)
			{
				case '.vendor':
					$history_type	 = 'vendor';
					break;
				default:
					$history_type	 = str_replace('.', '_', substr($acl_location, -strlen($acl_location) + 1));
			}
			if (!$history_type)
			{
				throw new Exception(lang('Unknown history type for acl_location: %1', $acl_location));
			}
			return $history_type;
		}

		/**
		 * Fetch data to populate autocomplete from forms
		 * @return array result to be handled by javascript
		 */
		function get_autocomplete()
		{
			$this->get_location_info();

			$query = phpgw::get_var('query');

			$params = array(
				'results'	 => phpgw::get_var('length', 'int', 'REQUEST', 10),
				'query'		 => $query,
			);

			foreach ($this->location_info['fields'] as $field)
			{
				if (isset($field['filter']) && $field['filter'])
				{
					$params['filter'][$field['name']] = phpgw::get_var($field['name']);
				}
			}


			$system_location = $this->location_info['system_location'] ? $this->location_info['system_location'] : $this->location_info['acl_location'];

			$attributes = $this->custom->find($this->location_info['acl_app'], $system_location, 0, '', 'ASC', 'attrib_sort', true, true);

			$custom_filter = array();
			foreach ($attributes as $attribute_id => $attribute)
			{
				switch (phpgw::get_var($attribute['name']))
				{
					case 'ISNOTNULL':
						$custom_filter[] = "{$attribute['name']} IS NOT NULL";
						break;
					case 'ISNULL':
						$custom_filter[] = "{$attribute['name']} IS NULL";
						break;
					default:
				}
			}
			$params['custom_filter']	 = $custom_filter;
			$params['disable_id_search'] = phpgw::get_var('disable_id_search', 'bool');
			$values						 = $this->read($params);
			$include					 = phpgw::get_var('include');

			if ($include)
			{
				foreach ($values as &$entry)
				{
					if ($entry[$include])
					{
						$entry['name'] = "{$entry[$include]} {$entry['name']}";
					}
				}
			}

			return array('ResultSet' => array('Result' => $values));
		}

		public function get_single_name( $data = array() )
		{
			$include	 = !empty($data['include']) ? $data['include'] : false;
			$attributes	 = array();
			if ($include)
			{
				$attributes[] = array('column_name' => $include);
			}

			$this->get_location_info($data['type']);
			$values = $this->so->read_single(array('id' => $data['id']), array('attributes' => $attributes));
			if ($include)
			{
				if (!empty($values['attributes']))
				{
					foreach ($values['attributes'] as $entry)
					{
						if ($entry['column_name'] == $include && $entry['value'])
						{
							$values['name'] = "{$entry['value']} {$values['name']}";
							break;
						}
					}
				}
			}

//			$values['path'] = $values['name'];
//			if ($values['parent_id'])
//			{
//				$path = $this->so->get_path(array('type' => $data['type'], 'id' => $values['parent_id']));
//				if ($path)
//				{
//					$values['path'] .= '::' . implode(' > ', $path);
//				}
//			}
			return $values['name'];
		}

		public function edit_field( $data = array() )
		{
			return $this->so->edit_field($data);
		}

		public function get_single_attrib_value( array $data )
		{
			$ret		 = $id			 = $data['id'];
			$type		 = $data['type'];
			$attrib_name = $data['attrib_name'];
			$ret		 = $id;
			if ($id			 = (int)$id)
			{
				$sogeneric		 = CreateObject('property.sogeneric', $type);
				$sogeneric_data	 = $sogeneric->read_single(array('id' => $id));
				$ret			 = $sogeneric_data[$attrib_name];
			}
			return $ret;
		}
	}