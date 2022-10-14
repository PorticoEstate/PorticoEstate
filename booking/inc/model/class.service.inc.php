<?php
	/**
	 * phpGroupWare - booking: a part of a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2016 Free Software Foundation, Inc. http://www.fsf.org/
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
	 * @package booking
	 * @subpackage article
	 * @version $Id: $
	 */

	phpgw::import_class('booking.boservice');

	include_class('phpgwapi', 'model', 'inc/model/');

	class booking_service extends phpgwapi_model
	{
		const STATUS_ACTIVE = 1;
		const acl_location = '.article';

		protected
			$id,
			$name,
			$active,
			$description,
			$owner_id;
		protected $field_of_responsibility_name = '.article';

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
			return new booking_service();
		}

		public static function get_status_list()
		{
			return array(
				self::STATUS_ACTIVE	=> lang('active'),
			);
		}

		public static function get_fields($debug = true)
		{
			$currentapp = $GLOBALS['phpgw_info']['flags']['currentapp'];

			$fields = array(
				'id' => array(
					'action'=> PHPGW_ACL_READ,
					'type' => 'int',
					'label' => 'id',
					'sortable'=> true,
					'formatter' => 'JqueryPortico.formatLink',
					'public'	=> true
					),
				'name' => array(
					'action'=>  PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'required' => true,
					'label' => 'name',
					'query'	 => true,
					),
				'active' => array(
					'action'=>   PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int'
					),
				'description' => array(
					'action'=>  PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'html',
					'required' => true,
					'label' => 'description',
					'query'	 => true,
					),
				'owner_id' => array(
					'action'=> PHPGW_ACL_ADD,
					'type' => 'int',
					'required' => false
					),
			);

/*
			if($currentapp == 'booking')
			{
				$backend_fields = array(
					'active' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
						'type' => 'int',
						'history'	=> false
						)
					);

				foreach ($backend_fields as $key => $field_info)
				{
					$fields[$key] = $field_info;
				}
			}
*/
			if($debug)
			{
				foreach ($fields as $field => $field_info)
				{
					if(!property_exists('booking_service', $field))
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
			$entity->active = (int)$entity->active;

			if(!$entity->get_id())
			{
				$entity->owner_id = $GLOBALS['phpgw_info']['user']['account_id'];
			}
		}

		public function serialize()
		{
			return $this->toArray();
		}

		public function store()
		{
			return booking_boservice::get_instance()->store($this);
		}

		public function read_single($id)
		{
			return booking_boservice::get_instance()->read_single($id, true);
		}
	}
