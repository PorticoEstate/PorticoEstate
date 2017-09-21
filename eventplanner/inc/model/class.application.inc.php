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
	 * @subpackage application
	 * @version $Id: $
	 */

	phpgw::import_class('eventplanner.boapplication');

	include_class('phpgwapi', 'model', 'inc/model/');

	class eventplanner_application extends phpgwapi_model
	{

		const STATUS_REGISTERED = 1;
		const STATUS_PENDING = 2;
		const STATUS_REJECTED = 3;
		const STATUS_APPROVED = 4;
		const acl_location = '.application';

		protected
			$id,
			$agreement_1,
			$agreement_2,
			$active,
			$display_in_dashboard,
			$category_id,
			$vendor_id,
			$vendor_name,
			$status,
			$num_granted_events,
			$created,
			$modified,
			$secret,
			$frontend_modified,
			$owner_id,
			$other_participants,
			$stage_width,
			$stage_depth,
			$stage_requirement,
			$wardrobe,
			$audience_limit,
			$title,
			$description,
			$non_public,
			$public_type,
			$summary,
			$remark,
			$contact_name,
			$contact_email,
			$contact_phone,
			$case_officer_id,
			$case_officer_name,
			$types,
			$comments,
			$comment,
			$date_start,
			$date_end,
			$timespan,
			$charge_per_unit,
			$number_of_units,
			$rig_up_min_before,
			$rig_up_num_person,
			$during_num_person,
			$rig_down_num_person,
			$rig_down_min_after,
			$power,
			$sound,
			$light,
			$piano,
			$power_remark,
			$sound_remark,
			$light_remark,
			$piano_remark,
			$equipment_remark,
			$raider;

		protected $field_of_responsibility_name = '.application';

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
			return new eventplanner_application();
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
			$currentapp = $GLOBALS['phpgw_info']['flags']['currentapp'];

			$fields = array(
				'id' => array('action'=> PHPGW_ACL_READ,
					'type' => 'int',
					'label' => 'id',
					'sortable'=> true,
					'formatter' => 'JqueryPortico.formatLink',
					'public' => true,
					),
				'display_in_dashboard' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'bool'),
				'category_id' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int',
					'label' => 'category',
					'history' => true),
				'num_granted_events' => array('action'=> 0,
					'type' => 'int',
					'label' => 'number of granted events',
					'history' => true
					),
				'created' => array('action'=> PHPGW_ACL_READ | PHPGW_ACL_ADD,
					'type' => 'date',
					'label' => 'created',
					'sortable' => true,
					),
				'modified' => array('action'=> PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'date',
					'label' => 'modified',
					'sortable' => true,
					),
				'secret' => array('action'=> PHPGW_ACL_ADD,
					'type' => 'string',
					'label' => 'secret',
					'sortable' => false,
					),
				'owner_id' => array('action'=> PHPGW_ACL_ADD,
					'type' => 'int',
					'required' => false
					),
			 	'other_participants' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'label' => 'other participants',
					'required' => false
					),
			 	'title' => array('action'=> PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'label' => 'title',
					'sortable' => false,
					'query' => true,
					'public' => true,
					),
			 	'description' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'label' => 'application description',
					'sortable' => false,
					'required' => true,
					),
			 	'non_public' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int',
					'label' => 'application public type',
					'sortable' => false,
					'required' => false,
					),
			 	'summary' => array('action'=> 0,
					'type' => 'html',
					'label' => 'summary',
					'sortable' => false,
					'query' => true,
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
					'history' => true,
					),
				'contact_email' => array(
					'action'=> PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'required' => true,
					'query' => true,
					'sf_validator' => createObject('booking.sfValidatorEmail', array(), array('invalid' => '%field% is invalid')),
					'label' => 'contact email',
					'history' => true,
					),
				'contact_phone' => array(
					'action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'required' => true,
					'query' => true,
					'label' => 'contact phone',
					'history' => true,
					),
				'vendor_id' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int',
					'label' => 'vendor',
					'sortable' => true,
					'history' => true,
					),
				'vendor_name' => array('action'=>  PHPGW_ACL_READ,
					'type' => 'string',
					'query' => true,
					'label' => 'vendor',
					'join' => array(
						'table' => 'eventplanner_vendor',
						'fkey' => 'vendor_id',
						'key' => 'id',
						'column' => 'name'
						)
					),
				'types' => array(
					'action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int',
					'label' => 'program type',
					'required' => true,
					'manytomany' => array(
						'table' => 'eventplanner_application_type_relation',
						'key' => 'application_id',
						'column' => array(
							'type_id' => array('type' => 'int', 'required' => true)),
					)),
				'date_start' => array('action'=> PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'date',
					'label'	=> 'date start',
					'sortable' => true,
					'history' => true
					),
				'date_end' => array('action'=> PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'date',
					'label'	=> 'date end',
					'history' => true
					),
				'charge_per_unit' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int',
					'label'	=> 'charge per unit',
					'required' => true,
					'history' => true
					),
				'number_of_units' => array('action'=> PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int',
					'label'	=> 'number of units',
					'required' => true,
					'history' => true
					),
				'timespan' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int',
					'label'	=> 'event timespan',
					'required' => true,
					'history' => true,
					),
				 'stage_width' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int',
					'required' => false,
					),
				 'stage_depth' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int',
					'required' => false,
					),
				 'stage_requirement' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'required' => false,
					),
				 'wardrobe' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int',
					'required' => false,
					),
				 'agreement_1' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int',
					'required' => true,
					),
				 'agreement_2' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int',
					'required' => true,
					),
				 'audience_limit' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int',
					'required' => false,
					),
				 'rig_up_min_before' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int',
					'required' => true,
					),
				'rig_up_num_person' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int',
					'required' => false,
					),
				'during_num_person' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int',
					'required' => false,
					),
				'rig_down_num_person' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int',
					'required' => false,
					),
				'rig_down_min_after' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int',
					'required' => true,
					),
				'power' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int',
					'required' => false,
					),
				'sound' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int',
					'required' => false,
					),
				'light' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int',
					'required' => false,
					),
				'piano' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int',
					'required' => false,
					),
				'power_remark' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'required' => false,
					),
				'sound_remark' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'required' => false,
					),
				'light_remark' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'required' => false,
					),
				'piano_remark' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'required' => false,
					),
				'equipment_remark' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'required' => false,
					),
				'raider' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'required' => false,
					)
			);

			if($currentapp == 'eventplanner')
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
			}
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

			if(!$entity->get_id())
			{
				$entity->created = time();
				$entity->owner_id = $GLOBALS['phpgw_info']['user']['account_id'];
				$entity->status = eventplanner_application::STATUS_REGISTERED;
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
			return eventplanner_boapplication::get_instance()->store($this);
		}

		public function read_single($id)
		{
			return eventplanner_boapplication::get_instance()->read_single($id, true);
		}
	}
