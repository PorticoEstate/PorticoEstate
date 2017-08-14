<?php
	/**
	 * phpGroupWare - property: a part of a Facilities Management System.
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
	 * @package rental
	 * @subpackage application
	 * @version $Id: $
	 */

	phpgw::import_class('rental.boapplication');

	include_class('phpgwapi', 'model', 'inc/model/');

	class rental_application extends phpgwapi_model
	{

		const STATUS_REGISTERED = 1;
		const STATUS_PENDING = 2;
		const STATUS_REJECTED = 3;
		const STATUS_APPROVED = 4;
		const acl_location = '.application';

		protected
			$id,
			$status,
			$ecodimb_id,
			$ecodimb_name,
			$composites,
			$district_id,
			$composite_type_id,
			$date_start,
			$date_end,
			$cleaning,
			$payment_method,
			$job_title,
			$description,
			$firstname,
			$lastname,
			$company_name,
			$department,
			$address1,
			$address2,
			$postal_code,
			$place,
			$account_number,
			$phone,
			$email,
			$unit_leader,
			$comment,
			$comments,
			$comment_input,
			$assign_date_start,
			$assign_date_end,
			$entry_date,
			$executive_officer,
			$identifier;


		public function __construct( int $id = null )
		{
			parent::__construct((int)$id);
		}

		/**
		 * Implementing classes must return an instance of itself.
		 *
		 * @return the class instance.
		 */
		public static function get_instance()
		{
			return new rental_application();
		}

		public static function get_composite_types()
		{
			return array(1 => 'Hybel', 2 => 'Leilighet');
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


		public static function get_payment_methods()
		{
			return array(1 => 'Faktura', 2 => 'Trekk i lønn',3 => 'intern faktura');
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
				'ecodimb_id' => array(
					'action'=>  PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int',
					'label' => 'dimb',
					),
				'ecodimb_name' => array(
					'action'=>  PHPGW_ACL_READ,
					'type' => 'string',
					'label' => 'dimb',
					'join' => array(
						'table' => 'fm_ecodimb',
						'fkey' => 'ecodimb_id',
						'key' => 'id',
						'column' => 'descr'
						)
					),
				'composites' => array('type' => 'string',
					'manytomany' => array(
						'table' => 'rental_application_composite',
						'key' => 'application_id',
						'column' => 'composite_id'
						)
					),
				'district_id' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int'),
				'composite_type_id' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int'),
				'date_start' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'date'),
				'date_end' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'date'),
				'cleaning' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'bool'),
				'payment_method' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int'),
				'firstname' => array(
					'action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'required' => true,
					'alternative' => array('company_name'),
					'query' => true,
					'label' => 'first name',
					),
				'lastname' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'required' => true,
					'query' => true,
					'alternative' => array('company_name'),
					'label' => 'last name',
					),
				'job_title' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string'),
				'company_name' => array(
					'action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'required' => true,
					'alternative' => array('firstname','lastname'),
					),
				'department' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string'),
				'address1' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string'),
				'address2' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string'),
				'postal_code' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string'),
				'place' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string'),
				'account_number' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string'),
				'phone' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string'),
				'email' => array(
					'action'=> PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'required' => true,
					'query' => true,
					'sf_validator' => createObject('booking.sfValidatorEmail', array(), array('invalid' => '%field% is invalid'))),
				'unit_leader' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string'),
				'comments' => array(
					'action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'manytomany' => array(
						'input_field' => 'comment_input',
						'table' => 'rental_application_comment',
						'key' => 'application_id',
						'column' => array('time', 'author', 'comment', 'type'),
						'order' => array('sort' => 'time', 'dir' => 'ASC')
					)),
				'comment' => array(
					'action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'related' => true,
					),
				'assign_date_start' => array('action'=> PHPGW_ACL_READ | PHPGW_ACL_EDIT,
					'type' => 'date',
					'label'=> 'assign_start',
					'history' => true
					),
				'assign_date_end' => array('action'=> PHPGW_ACL_READ | PHPGW_ACL_EDIT,
					'type' => 'date',
					'label'=> 'assign_end',
					'history' => true
					),
				'status' => array('action'=> PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int',
					'history' => true,
					),
				'entry_date' => array('action'=> PHPGW_ACL_READ | PHPGW_ACL_ADD,
					'type' => 'int',
					'label' => 'entry_date',
					'sortable' => true,
					),
				'executive_officer' => array('action'=> PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int',
					'label' => 'executive_officer',
					'sortable' => true,
					'history' => true,
					),
				'identifier' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string'),
			);

			if($debug)
			{
				foreach ($fields as $field => $field_info)
				{
					if(!property_exists('rental_application', $field))
					{
					   phpgwapi_cache::message_set('$'."{$field}", 'error');
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

			if(!$entity->get_id())
			{
				$entity->status = rental_application::STATUS_REGISTERED;
			}
		}

		public function serialize()
		{
			return self::toArray();
		}

		public function store()
		{
			return rental_boapplication::get_instance()->store($this);
		}

		public function read_single($id)
		{
			return rental_boapplication::get_instance()->read_single($id, true);
		}

	}