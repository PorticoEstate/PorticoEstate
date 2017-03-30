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
	 * @subpackage calendar
	 * @version $Id: $
	 */
	phpgw::import_class('eventplanner.bocalendar');

	include_class('phpgwapi', 'model', 'inc/model/');

	class eventplanner_calendar extends phpgwapi_model
	{

		const STATUS_REGISTERED = 1;
		const STATUS_PENDING = 2;
		const STATUS_REJECTED = 3;
		const STATUS_APPROVED = 4;
		const acl_location = '.calendar';

		protected
			$id,
			$owner_id,
			$active,
			$completed,
			$cost,
			$from_,
			$to_,
			$application_id,
			$application_name,
			$vendor_name,
			$customer_id,
			$customer_name,
			$customer_contact_name,
			$customer_contact_email,
			$customer_contact_phone,
			$location,
			$comments,
			$comment,
			$created,
			$secret,
			$process_update;
		protected $field_of_responsibility_name = '.calendar';

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
			return new eventplanner_calendar();
		}

		public static function get_status_list()
		{
			return array(
				self::STATUS_REGISTERED => lang('registered'),
				self::STATUS_PENDING => lang('pending'),
				self::STATUS_REJECTED => lang('rejected'),
				self::STATUS_APPROVED => lang('approved')
			);
		}

		public static function get_fields( $debug = true )
		{
			$fields = array(
				'id' => array('action' => PHPGW_ACL_READ,
					'type' => 'int',
					'label' => 'id',
					'sortable' => true,
					'formatter' => 'JqueryPortico.formatLink',
					'public' => true
				),
				'owner_id' => array('action' => PHPGW_ACL_ADD,
					'type' => 'int',
					'required' => false
				),
				'active' => array('action' => PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int',
					'public' => true,
					'history' => true
				),
				'completed' => array('action' => PHPGW_ACL_EDIT,
					'type' => 'int',
					'history' => true
				),
				'cost' => array('action' => PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'decimal'
				),
				'from_' => array('action' => PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'date',
					'label' => 'from',
					'history' => true,
					'required' => true,
					'public' => true
				),
				'to_' => array('action' => PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'date',
					'label' => 'to',
					'history' => true,
					'required' => true,
					'public' => true
				),
				'application_id' => array('action' => PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int',
					'label' => 'application',
					'sortable' => true,
					'required' => true,
					'public' => true
				),
				'application_name' => array('action' => PHPGW_ACL_READ,
					'type' => 'string',
					'query' => true,
					'label' => 'application',
					'public' => true,
					'join' => array(
						'table' => 'eventplanner_application',
						'fkey' => 'application_id',
						'key' => 'id',
						'column' => 'title'
					)
				),
				'location' => array('action' => PHPGW_ACL_READ,
					'type' => 'string',
					'query' => true,
					'label' => 'location',
					'public' => true,
					'join' => array(
						'table' => 'eventplanner_booking',
						'fkey' => 'id',
						'key' => 'calendar_id',
						'column' => 'location'
					)
				),
				'vendor_name' => array('action' => PHPGW_ACL_READ,
					'type' => 'string',
					'query' => true,
					'label' => 'vendor',
					'public' => true,
					'multiple_join' => array(
						'statement' => 'LEFT JOIN eventplanner_application ON eventplanner_application.id = eventplanner_calendar.application_id'
						. ' LEFT JOIN eventplanner_vendor ON eventplanner_application.vendor_id = eventplanner_vendor.id',
						'column' => 'eventplanner_vendor.name'
					)
				),
				'customer_name' => array('action' => PHPGW_ACL_READ,
					'type' => 'string',
					'query' => true,
					'label' => 'customer',
					'public' => true,
					'multiple_join' => array(
						'statement' => 'LEFT JOIN eventplanner_booking ON eventplanner_booking.calendar_id = eventplanner_calendar.id'
						. ' LEFT JOIN eventplanner_customer ON eventplanner_booking.customer_id = eventplanner_customer.id',
						'column' => 'eventplanner_customer.name'
					)
				),
				'created' => array('action' => PHPGW_ACL_READ,
					'type' => 'date',
					'label' => 'created',
					'sortable' => true,
				)
			);

			if ($debug)
			{
				foreach ($fields as $field => $field_info)
				{
					if (!property_exists('eventplanner_calendar', $field))
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

			if (!$entity->get_id())
			{
				$entity->status = eventplanner_calendar::STATUS_REGISTERED;
				$entity->secret = self::generate_secret();
				$entity->owner_id = $GLOBALS['phpgw_info']['user']['account_id'];
			}

			if (empty($entity->completed))
			{
				$entity->completed = 0;
			}

			if (!empty($entity->application_id))
			{
				$application = createObject('eventplanner.boapplication')->read_single($entity->application_id, true, $relaxe_acl = true);
				$entity->to_ = $entity->from_ + ((int)$application->timespan * 60);
			}

			$entity->modified = time();
			$entity->active = (int)$entity->active;
		}

		protected function doValidate( $entity, &$errors )
		{
			$application = createObject('eventplanner.boapplication')->read_single($entity->application_id);
			$params = array();
			$params['filters']['active'] = 1;
			$params['filters']['application_id'] = $entity->application_id;

			$calendars = eventplanner_socalendar::get_instance()->read($params);

			foreach ($calendars['results'] as $calendar)
			{
				if ($calendar['id'] == $entity->get_id())
				{
					continue;
				}

				if (($entity->from_ > $calendar['from_'] && $entity->from_ < $calendar['to_']) || ($entity->to_ > $calendar['from_'] && $entity->to_ < $calendar['to_']))
				{
					$errors['from_'] = lang('Time is already booked');
				}
			}


			if ($entity->get_id()) // update
			{
				$test_total_tecords = (int)$calendars['total_records'];
			}
			else // new entry
			{
				$test_total_tecords = (int)$calendars['total_records'] + 1;
			}

			if ($test_total_tecords > (int)$application->num_granted_events)
			{
				$errors['num_granted_events'] = lang('maximum of granted events are reached');
			}

			$date_start = date('Ymd', $application->date_start);
			$date_end = date('Ymd', $application->date_end);
			$from_ = date('Ymd', $entity->from_);

			if ($from_ < $date_start || $from_ > $date_end)
			{
				$errors['from_'] = lang('date is outside the scope');
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
			return eventplanner_bocalendar::get_instance()->store($this);
		}

		public function read_single( $id )
		{
			return eventplanner_bocalendar::get_instance()->read_single($id, true);
		}
	}