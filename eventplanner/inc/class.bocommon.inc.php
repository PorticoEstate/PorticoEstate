<?php
	/**
	 * phpGroupWare
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2016 Free Software Foundation http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License v2 or later
	 * @internal
	 * @package eventplanner
	 * @subpackage application
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


	abstract class eventplanner_bocommon
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

			$params = array(
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query' => $query ? $query : $search['value'],
				'sort' => $columns[$order[0]['column']]['data'],
				'dir' => $order[0]['dir'],
				'allrows' => phpgw::get_var('length', 'int') == -1,
			);

			foreach ($fields as $field => $_params)
			{
				if (!empty($_REQUEST["filter_$field"]))
				{
					$params['filters'][$field] = phpgw::get_var("filter_$field", $_params['type']);
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
					$object->set_field( $field, phpgw::get_var($field, $field_info['type'] ) );
				}
			}
			return $object;
		}

		public abstract function store( $object );


		/**
		 * Perform custom actions defined per location before storing object to database
		 * @param object $object
		 */
		public function store_pre_commit( &$object )
		{
			$criteria = array(
				'appname' => 'eventplanner',
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

				$file = PHPGW_SERVER_ROOT . "/eventplanner/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}/{$entry['file_name']}";
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
			$criteria = array(
				'appname' => 'eventplanner',
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

				$file = PHPGW_SERVER_ROOT . "/eventplanner/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}/{$entry['file_name']}";
				if ($entry['active'] && is_file($file) && !$entry['client_side'] && !$entry['pre_commit'])
				{
					require $file;
				}
			}
		}
	}