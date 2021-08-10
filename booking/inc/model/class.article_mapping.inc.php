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

	phpgw::import_class('booking.boarticle_mapping');

	include_class('phpgwapi', 'model', 'inc/model/');

	class booking_article extends phpgwapi_model
	{

		const STATUS_REGISTERED = 1;
		const STATUS_PENDING = 2;
		const STATUS_REJECTED = 3;
		const STATUS_APPROVED = 4;
		const acl_location = '.article';

		protected
			$id,
			$article_id,
			$building_id,
			$building_name,
			$article_name,
			$article_cat_id,
			$article_cat_name,
//			$created,
//			$modified,
//			,
//			$name,
//			$address_2,
//			$zip_code,
//			$city,
//			$organization_number,
//			$contact_name,
//			$contact_email,
//			$contact_phone,
//			$contact2_name,
//			$contact2_email,
//			$contact2_phone,
//			$number_of_users,
//			$max_events,
//			$account_number,
//			$description,
//			$grant_non_public,
//			$remark,
//			$comments,
//			$comment;
			$article_code,
			$owner_id,
			$unit;
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
			return new booking_article();
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
				'id' => array(
					'action'=> PHPGW_ACL_READ,
					'type' => 'int',
					'label' => 'id',
					'sortable'=> true,
					'formatter' => 'JqueryPortico.formatLink',
					'public'	=> true
					),
				'owner_id' => array(
					'action'=> PHPGW_ACL_ADD,
					'type' => 'int',
					'required' => false
					),
				'article_cat_id' => array(
					'action'=>  PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int'
					),
				'building_id' => array(
					'action'=>  PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int'
					),
				'building_name' => array(
					'action'=>  PHPGW_ACL_READ,
						'type' => 'string',
						'query' => true,
						'label' => 'building',
						'multiple_join' => array(
							'statement' => ' LEFT JOIN bb_building ON bb_building.id = bb_article_mapping.building_id',
							'column' => 'bb_building.name'
						),
					),
				'article_cat_name' => array(
					'action'=>  PHPGW_ACL_READ,
						'type' => 'string',
						'query' => true,
						'label' => 'category',
						'join' => array(
							'table' => 'bb_article_category',
							'fkey' => 'article_cat_id',
							'key' => 'id',
							'column' => 'name'
							)
						),
				'article_id' => array(
					'action'=>  PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int'
					),
				'article_name' => array(
					'action'=>  PHPGW_ACL_READ,
						'type' => 'string',
						'query' => true,
						'label' => 'name',
						'multiple_join' => array(
							'statement' => ' JOIN bb_article_view ON bb_article_view.id = bb_article_mapping.article_id'
							. ' AND bb_article_view.article_cat_id = bb_article_mapping.article_cat_id',
							'column' => 'bb_article_view.name'
						),
					),
				'article_code' => array(
					'action'=>  PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'required' => true,
					'label' => 'article code'
					),
				'unit' => array(
					'action'=>  PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'required' => true,
					'label' => 'unit'
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
					if(!property_exists('booking_article', $field))
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

			if ($entity->article_cat_id == 1)
			{
				$entity->article_id = phpgw::get_var('resource_id', 'int', 'POST');
			}
			else if ($entity->article_cat_id == 2)
			{
				$entity->article_id = phpgw::get_var('service_id', 'int', 'POST');
			}
			if(!$entity->get_id())
			{
				$entity->owner_id = $GLOBALS['phpgw_info']['user']['account_id'];
			}
		}

		public function serialize()
		{
			return self::toArray();
		}

		public function store()
		{
			return booking_boarticle_mapping::get_instance()->store($this);
		}

		public function read_single($id)
		{
			return booking_boarticle_mapping::get_instance()->read_single($id, true);
		}
	}
