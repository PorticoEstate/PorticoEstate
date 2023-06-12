<?php
	/**
	 * phpGroupWare - addressbook: a part of a Facilities Management System.
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
	 * @package addressbook
	 * @subpackage addressbook
	 * @version $Id: $
	 */

	phpgw::import_class('addressbook.boaddressbook');

	include_class('phpgwapi', 'model', 'inc/model/');

	class addressbook_addressbook extends phpgwapi_model
	{
		const acl_location = '.addressbook';

		protected
			$id;


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
			return new addressbook_addressbook();
		}

		public static function get_status_list()
		{
			return array();
		}

		public static function get_fields($debug = true)
		{
			$currentapp = $GLOBALS['phpgw_info']['flags']['currentapp'];
				
			$fields = array(
				'contact_id' => array('action'=> PHPGW_ACL_READ,
					'type' => 'int',
					'label' => 'id',
					'sortable'=> true,
					'public' => false,
					),
			 	'per_first_name' => array('action'=> PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'label' => 'first name',
					'sortable' => false,
					'query' => true,
					'public' => true,
					),
			 	'per_last_name' => array('action'=> PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'label' => 'last name',
					'sortable' => false,
					'query' => true,
					'public' => true,
					),
			 	'per_department' => array('action'=> PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'label' => 'department',
					'sortable' => false,
					'query' => true,
					'public' => true,
					),
			 	'per_title' => array('action'=> PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'label' => 'title',
					'sortable' => false,
					'query' => true,
					'public' => true,
					),
			 	'addr_add1' => array('action'=> PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'label' => 'address1',
					'sortable' => false,
					'query' => true,
					'public' => true,
					),
			 	'addr_city' => array('action'=> PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'label' => 'city',
					'sortable' => false,
					'query' => true,
					'public' => true,
					),
			 	'org_name' => array('action'=> PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'label' => 'org_name',
					'sortable' => false,
					'query' => true,
					'public' => true,
					),
			 	'owner' => array('action'=> PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'label' => 'owner',
					'sortable' => false,
					'query' => true,
					'public' => true,
					)
			);

			/*if($currentapp == 'eventplanner')
			{
				$backend_fields = array(
				 'agreement_1' => array('action'=> 0,
					'type' => 'int',
					'required' => false,
					),
				 'agreement_2' => array('action'=> 0,
					'type' => 'int',
					'required' => false,
					),
					'num_granted_events' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
						'type' => 'int',
						'label' => 'number of granted events',
						'history' => true
					),
					'case_officer_id' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
						'type' => 'int',
						'required' => true,
						'label' => 'case officer',
						'sortable' => true,
						'history' => true,
						),
					'case_officer_name' => array('action'=>  PHPGW_ACL_READ,
						'type' => 'string',
						'query' => true,
						'label' => 'case officer',
						'join' => array(
							'table' => 'phpgw_accounts',
							'fkey' => 'case_officer_id',
							'key' => 'account_id',
							'column' => 'account_lid'
							)
						),
					'active' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
						'type' => 'int',
						'label' => 'active',
						'history' => true,
						),
					'status' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
						'type' => 'int',
						'label' => 'status',
						'history' => true
						),
					'summary' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
						'type' => 'html',
						'label' => 'summary',
						'sortable' => false,
					),
					'comments' => array(
						'action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
						'type' => 'string',
						'manytomany' => array(
							'input_field' => 'comment_input',
							'table' => 'eventplanner_application_comment',
							'key' => 'application_id',
							'column' => array('time', 'author', 'comment', 'type'),
							'order' => array('sort' => 'time', 'dir' => 'ASC')
						)),
					'comment' => array(
						'action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
						'type' => 'string',
						'related' => true,
						),
					);

				foreach ($backend_fields as $key => $field_info)
				{
					$fields[$key] = $field_info;
				}
			}
			else
			{
				$fields['status'] = true;
			}


			if($debug)
			{
				foreach ($fields as $field => $field_info)
				{
					if(!property_exists('eventplanner_application', $field))
					{
					   phpgwapi_cache::message_set('$'."{$field},", 'error');
					}

				}
			}*/
			return $fields;
		}

		/**
		 * Implement in subclasses to perform actions on entity before validation
		 */
		protected function preValidate( &$entity )
		{

			if($entity->date_start && $entity->date_start >  $entity->date_end)
			{
				$entity->date_end = $entity->date_start;
				phpgwapi_cache::message_set(lang('End date cannot be before start date'), 'error');
			}
		}

		public function serialize()
		{
			return self::toArray();
		}

		public function store()
		{
			return boaddressbook::get_instance()->store($this);
		}

		public function read_single($id)
		{
			return boaddressbook::get_instance()->read_single($id, true);
		}
	}
