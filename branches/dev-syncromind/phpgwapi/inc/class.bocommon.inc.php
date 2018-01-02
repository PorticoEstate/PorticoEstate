<?php
	/**
	 * phpGroupWare
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2016 Free Software Foundation http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License v2 or later
	 * @internal
	 * @package phpgwapi
	 * @subpackage utilities
	 * @version $Id:$
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


	abstract class phpgwapi_bocommon
	{

		protected static
			$fields,
			$acl_location;


		public function __construct()
		{
		}


		/*
		 * Get the filters and search parametres for table-listings
		 */
		public function build_default_read_params()
		{
			$fields = $this->fields;

			$search = phpgw::get_var('search');
			$query =  phpgw::get_var('query');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');
			$sort = $columns[$order[0]['column']]['data'];

			$params = array(
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query' => $query ? $query : $search['value'],
				'sort' => false,
				'dir' => $order[0]['dir'],
				'allrows' => phpgw::get_var('length', 'int') == -1,
			);

			foreach ($fields as $field => $_params)
			{
				if (!empty($_REQUEST["filter_$field"]))
				{
					$params['filters'][$field] = phpgw::get_var("filter_$field", $_params['type']);
				}

				if($field == $sort && $_params['sortable'])
				{
					$params['sort'] = $field;
				}
			}

			return $params;
		}

		/**
		 * Insert values prosted from form
		 * @param object $object
		 * @return object
		 */
		public function populate($object)
		{
			$fields = $this->fields;

			foreach ($fields as $field	=> $field_info)
			{
				if(($field_info['action'] & PHPGW_ACL_ADD) ||  ($field_info['action'] & PHPGW_ACL_EDIT))
				{
					if($field_info['type'] == 'json')
					{
						$values = array();
						$custom_fields = $object->get_custom_fields();
						$values_attribute = phpgw::get_var('values_attribute');

						foreach ($custom_fields as $key => $custom_field)
						{
							$values[$custom_field['name']] = $values_attribute[$key]['value'];
						}
						$object->set_field( $field, $values);
					}
					else
					{
						$object->set_field( $field, phpgw::get_var($field, $field_info['type'] ) );
					}
				}
			}
			$values_attribute = phpgw::get_var('values_attribute');
			$object->set_field( 'values_attribute', $values_attribute);

			return $object;
		}

		public abstract function store( $object );


		/**
		 * Perform custom actions defined per location before storing object to database
		 * @param object $object
		 */
		public function store_pre_commit( &$object )
		{
			$appname = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$criteria = array(
				'appname' => $appname,
				'location' => $this->acl_location,
				'pre_commit' => true,
				'allrows' => true
			);

			$custom_functions = $GLOBALS['phpgw']->custom_functions->find($criteria);

			foreach ($custom_functions as $entry)
			{
				// prevent path traversal
				if (preg_match('/\.\./', $entry['file_name']))
				{
					continue;
				}

				$file = PHPGW_SERVER_ROOT . "/{$appname}/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}/{$entry['file_name']}";
				if ($entry['active'] && is_file($file) && !$entry['client_side'] && $entry['pre_commit'])
				{
					require $file;
				}
			}
		}

		/**
		 * Perform custom actions defined per location after storing object to database
		 * @param object $object
		 */
		public function store_post_commit( &$object )
		{
			$appname = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$criteria = array(
				'appname' => $appname,
				'location' => $this->acl_location,
				'allrows' => true
			);

			$custom_functions = $GLOBALS['phpgw']->custom_functions->find($criteria);


			foreach ($custom_functions as $entry)
			{
				// prevent path traversal
				if (preg_match('/\.\./', $entry['file_name']))
				{
					continue;
				}

				$file = PHPGW_SERVER_ROOT . "/{$appname}/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}/{$entry['file_name']}";
				if ($entry['active'] && is_file($file) && !$entry['client_side'] && !$entry['pre_commit'])
				{
					require $file;
				}
			}
		}

		function select_multi_list( $selected = array(), $list )
		{
			if (isset($list) && is_array($list) && isset($selected) && is_array($selected))
			{
				foreach ($list as & $entry)
				{
					$entry['selected'] = in_array( $entry['id'], $selected) ? 1 : 0;
				}
			}
			return $list;
		}

		function msgbox_data( $receipt )
		{
			$msgbox_data_error = array();
			$msgbox_data_message = array();
			if (isset($receipt['error']) && is_array($receipt['error']))
			{
				foreach ($receipt['error'] as $dummy => $error)
				{
					$msgbox_data_error[$error['msg']] = false;
				}
			}

			if (isset($receipt['message']) && is_array($receipt['message']))
			{
				foreach ($receipt['message'] as $dummy => $message)
				{
					$msgbox_data_message[$message['msg']] = true;
				}
			}

			$msgbox_data = array_merge($msgbox_data_error, $msgbox_data_message);

			return $msgbox_data;
		}

	}