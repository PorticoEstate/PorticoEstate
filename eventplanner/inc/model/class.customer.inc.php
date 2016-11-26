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
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/ and Nordlandssykehuset
	 * @package eventplanner
	 * @subpackage customer
	 * @version $Id: $
	 */

	phpgw::import_class('eventplanner.bocustomer');

	include_class('phpgwapi', 'model', 'inc/model/');

	class eventplanner_customer extends phpgwapi_model
	{

		const STATUS_REGISTERED = 1;
		const STATUS_PENDING = 2;
		const STATUS_REJECTED = 3;
		const STATUS_APPROVED = 4;
		const acl_location = '.customer';

		protected
			$id,
			$active,
			$category_id,
			$created,
			$modified,
			$secret,
			$name,
			$address_1,
			$address_2,
			$zip_code,
			$city,
			$customer_organization_number,
			$contact_name,
			$contact_email,
			$contact_phone,
			$account_number,
			$description,
			$remark,
	//		$customer_identifier_type,
	//		$customer_ssn,

			$comments,
			$comment;

		protected $field_of_responsibility_name = '.customer';

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
			return new eventplanner_customer();
		}

		public static function get_status_list()
		{
			return array(
				self::STATUS_REGISTERED => lang('registered'),
				self::STATUS_PENDING	=> lang('pending'),
				self::STATUS_REJECTED => lang('rejected'),
				self::STATUS_APPROVED	=> lang('approved')
			);
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
				'active' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int',
					'history'	=> true
					),
				'category_id' => array('action'=>  PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int'
					),
				'created' => array('action'=> PHPGW_ACL_READ,
					'type' => 'date',
					'label' => 'created',
					'sortable' => true,
					),
				'modified' => array('action'=> PHPGW_ACL_READ | PHPGW_ACL_EDIT,
					'type' => 'date',
					'label' => 'modified',
					'sortable' => true,
					),
				'secret' => array('action'=> PHPGW_ACL_ADD,
					'type' => 'string',
					'label' => 'secret',
					'sortable' => false,
					),
				'name' => array(
					'action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'label' => 'name',
					'required' => true,
					'query' => true,
					),
				'address_1' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'required' => true
					),
				'address_2' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'required' => false),
				'zip_code' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'required' => true),
				'city' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'required' => true),
				'account_number' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'required' => true),
			 	'description' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'label' => 'description',
					'sortable' => false,
					'required' => true
					),
			 	'remark' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'label' => 'description',
					'sortable' => false,
					),
				'contact_name' => array(
					'action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'required' => true,
					'query' => true,
					'label' => 'contact name',
					),
				'contact_email' => array(
					'action'=> PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'required' => true,
					'query' => true,
					'sf_validator' => createObject('booking.sfValidatorEmail', array(), array('invalid' => '%field% is invalid')),
					'label' => 'contact email',
					),
				'contact_phone' => array(
					'action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'required' => true,
					'query' => true,
					'label' => 'contact phone',
					),
/*				'customer_identifier_type' => array(
					'action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'required' => true,
					'label' => 'customer_identifier_type',
					),
				'customer_ssn' => array(
					'action'=> PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'required' => false,
					'query' => true,
					'sf_validator' => createObject('booking.sfValidatorNorwegianSSN', array('full_required' => false)),
					'label' => 'customer_ssn'
					),*/
				'customer_organization_number' => array(
					'action'=> PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'required' => true,
					'query' => true,
					'sf_validator' => createObject('booking.sfValidatorNorwegianOrganizationNumber', array(), array('invalid' => '%field% is invalid')),
					'label' => 'organization_number'
					),
				'comments' => array(
					'action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'manytomany' => array(
						'input_field' => 'comment_input',
						'table' => 'eventplanner_customer_comment',
						'key' => 'customer_id',
						'column' => array('time', 'author', 'comment', 'type'),
						'order' => array('sort' => 'time', 'dir' => 'ASC')
					)),
				'comment' => array(
					'action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'related' => true,
					)
			);

			if($debug)
			{
				foreach ($fields as $field => $field_info)
				{
					if(!property_exists('eventplanner_customer', $field))
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

			if($entity->get_id())
			{
			}
			else
			{
				$entity->status = eventplanner_customer::STATUS_REGISTERED;
				$entity->secret = self::generate_secret();
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
			return eventplanner_bocustomer::get_instance()->store($this);
		}

		public function read_single($id)
		{
			return eventplanner_bocustomer::get_instance()->read_single($id, true);
		}
	}
