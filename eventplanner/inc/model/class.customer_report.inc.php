<?php
	/**
	 * phpGroupWare - eventplanner: a part of a Facilities Management System.
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
	 * @package eventplanner
	 * @subpackage customer_report
	 * @version $Id: $
	 */
	phpgw::import_class('eventplanner.bocustomer_report');

	include_class('phpgwapi', 'model', 'inc/model/');

	class eventplanner_customer_report extends phpgwapi_model
	{

		const acl_location = '.customer_report';

		protected
			$id,
			$owner_id,
			$booking_id,
			$booking_location,
			$created,
			$json_representation;
		static $custom_fields = array();
		protected $field_of_responsibility_name = '.customer_report';

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
			return new eventplanner_customer_report();
		}

		public static function get_custom_fields()
		{
			static $custom_fields = array();
			if(!$custom_fields)
			{
				$custom_fields = $GLOBALS['phpgw']->custom_fields->find('eventplanner', self::acl_location, 0, '', 'ASC', 'attrib_sort', true, true);
			}
			return $custom_fields;
		}

		public function get_organized_fields()
		{
			if (!$this->custom_fields)
			{
				$this->custom_fields = createObject('booking.custom_fields', 'eventplanner')->get_organized_fields(self::acl_location);
			}
			return $this->custom_fields;
		}

		public static function get_fields( $debug = true )
		{
			$fields = array(
				'id' => array('action' => PHPGW_ACL_READ,
					'type' => 'int',
					'label' => 'id',
					'sortable' => true,
					'formatter' => 'JqueryPortico.formatLink',
				),
				'owner_id' => array('action'=> PHPGW_ACL_ADD,
					'type' => 'int',
					'required' => false
					),
				'booking_id' => array('action' => PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int',
					'label' => 'booking',
					'sortable' => true,
					'required' => true,
				),
				'booking_location' => array('action' => PHPGW_ACL_READ,
					'type' => 'string',
					'query' => true,
					'label' => 'location',
					'join' => array(
						'table' => 'eventplanner_booking',
						'fkey' => 'booking_id',
						'key' => 'id',
						'column' => 'location'
					)
				),
				'created' => array('action' => PHPGW_ACL_READ | PHPGW_ACL_ADD,
					'type' => 'date',
					'label' => 'created',
					'sortable' => true,
				),
				'json_representation' => array('action' => PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'json',
					'sortable' => false,
				),
			);

			if ($debug)
			{
				foreach ($fields as $field => $field_info)
				{
					if (!property_exists('eventplanner_customer_report', $field))
					{
						phpgwapi_cache::message_set('$' . "{$field},", 'error');
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
			if (!empty($entity->comment))
			{
				$entity->comment_input = array(
					'time' => time(),
					'author' => $GLOBALS['phpgw_info']['user']['fullname'],
					'comment' => $entity->comment,
					'type' => 'comment'
				);
			}
			$entity->modified = time();
			$entity->active = (int)$entity->active;

			if (!$entity->get_id())
			{
				$entity->created = time();
				$entity->secret = self::generate_secret();
				$entity->owner_id = $GLOBALS['phpgw_info']['user']['account_id'];
			}
		}

		protected function generate_secret( $length = 10 )
		{
			return substr(base64_encode(rand(1000000000, 9999999999)), 0, $length);
		}

		public function serialize()
		{
			return self::toArray();
		}

		public function store()
		{
			return eventplanner_bocustomer_report::get_instance()->store($this);
		}

		public function read_single( $id )
		{
			return eventplanner_bocustomer_report::get_instance()->read_single($id, true);
		}
	}