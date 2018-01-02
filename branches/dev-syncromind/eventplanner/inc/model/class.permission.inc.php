<?php
	/**
	 * phpGroupWare - eventplanner: a part of a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2017 Free Software Foundation, Inc. http://www.fsf.org/
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
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/
	 * @package eventplanner
	 * @subpackage permission
	 * @version $Id: $
	 */

	phpgw::import_class('eventplanner.bopermission');

	include_class('phpgwapi', 'model', 'inc/model/');

	class eventplanner_permission extends phpgwapi_model
	{

		const acl_location = '.admin';

		protected
			$id,
			$subject_id,
			$object_id,
			$object_type,
			$permission,
			$subject_name;

		protected $field_of_responsibility_name = '.admin';

		public function __construct( int $id = null )
		{
			parent::__construct((int)$id);
			$this->field_of_responsibility_name = self::acl_location;
		}

		/**
		 * Implementing classes must return an instance of itself.
		 *
		 * @return the class instance.
		 */
		public static function get_instance()
		{
			return new eventplanner_permission();
		}

		public static function get_status_list()
		{
		}

		public static function get_fields($debug = true)
		{

			$fields = array(
				'id' => array('action'=> PHPGW_ACL_READ,
					'type' => 'int',
					'label' => 'id',
					'sortable'=> true,
					'formatter' => 'JqueryPortico.formatLink',
				),
				'subject_id' => array('action'=> PHPGW_ACL_ADD,
					'type' => 'int',
					'required' => true
				),
				'object_id' => array('action'=>  PHPGW_ACL_READ | PHPGW_ACL_ADD,
					'type' => 'int',
					'label' => 'object id',
					'required' => true
				),
				'object_type' => array('action'=>  PHPGW_ACL_READ | PHPGW_ACL_ADD,
					'type' => 'string',
					'label' => 'object type',
					'required' => true,
					'query' => true,
				),
				'permission' => array('action'=>  PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int',
					'label' => 'permission',
					'required' => true,
					'query' => true,
				),
				'subject_name' => array('action'=>  PHPGW_ACL_READ,
					'type' => 'string',
					'query' => true,
					'label' => 'user',
					'join' => array(
						'table' => 'phpgw_accounts',
						'fkey' => 'subject_id',
						'key' => 'account_id',
						'column' => 'account_lid'
					)
				)
			);


			if($debug)
			{
				foreach ($fields as $field => $field_info)
				{
					if(!property_exists('eventplanner_permission', $field))
					{
					   phpgwapi_cache::message_set('$'."{$field},", 'error');
					}

				}
			}
			return $fields;
		}

		/**
		 * Implement in subclasses to perform actions on entity before validation
		 */
		protected function preValidate( &$entity )
		{
			$permission = (array) phpgw::get_var('permission', 'int');

			$entity->permission = 0;//reset
			foreach ($permission as $key => $value)
			{
				$entity->permission += $value;
			}
		}


		public function serialize()
		{
			return self::toArray();
		}

		public function store()
		{
			return eventplanner_bopermission::get_instance()->store($this);
		}

		public function read_single($id)
		{
			return eventplanner_bopermission::get_instance()->read_single($id, true);
		}
	}
