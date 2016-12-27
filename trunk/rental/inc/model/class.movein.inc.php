<?php
	/**
	 * phpGroupWare - rental: a part of a Facilities Management System.
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
	 * @subpackage movein
	 * @version $Id: $
	 */

	phpgw::import_class('rental.bomovein');

	include_class('phpgwapi', 'model', 'inc/model/');

	class rental_movein extends phpgwapi_model
	{

		const acl_location = '.movein';

		protected
			$id,
			$contract_id,
			$old_contract_id,
			$created,
			$modified,
			$account_id,
			$comments,
			$comment,
			$attributes,// custom fields
			$values_attribute;// custom fields

		protected $field_of_responsibility_name = '.movein';

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
			return new rental_movein();
		}

		public static function get_custom_fields()
		{
			static $custom_fields = array();
			if(!$custom_fields)
			{
				$custom_fields = $GLOBALS['phpgw']->custom_fields->find('rental', self::acl_location, 0, '', 'ASC', 'attrib_sort', true, true);
			}
			return $custom_fields;
		}

		public function get_organized_fields()
		{
			if (!$this->custom_fields)
			{
				$this->custom_fields = createObject('booking.custom_fields', 'rental')->get_organized_fields(self::acl_location);
			}
			return $this->custom_fields;
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
				'created' => array('action'=> PHPGW_ACL_READ,
					'type' => 'date',
					'label' => 'created',
					'sortable' => true,
					),
				'modified' => array('action'=> PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'date',
					'label' => 'modified',
					'sortable' => true,
					),
				'account_id' => array('action'=> PHPGW_ACL_ADD,
					'type' => 'int',
					'label' => 'contract_id',
					'sortable' => true,
					),
				'contract_id' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int',
					'label' => 'contract_id',
					'sortable' => true,
					),
				'old_contract_id' => array('action'=>  PHPGW_ACL_READ,
					'type' => 'string',
					'query' => true,
					'label' => 'contract',
					'join' => array(
						'table' => 'rental_contract',
						'fkey' => 'contract_id',
						'key' => 'id',
						'column' => 'old_contract_id'
						)
					),
				'comments' => array(
					'action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'manytomany' => array(
						'input_field' => 'comment_input',
						'table' => 'rental_movein_comment',
						'key' => 'movein_id',
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
					if(!property_exists('rental_movein', $field))
					{
					   phpgwapi_cache::message_set('$'."{$field},", 'error');
					}

				}
			}

			$custom_fields = self::get_custom_fields();

			foreach ($custom_fields as $attrib_id => $attrtib)
			{
				$fields[$attrtib['name']] = array(
						'action'=> $attrtib['list'],
						'type' => $attrtib['datatype'] == 'D' || $attrtib['datatype'] == 'DT' ? 'datestring' : 'string',
						'label' => $attrtib['input_text'],
						'translated_label' => $attrtib['input_text'],
						'sortable' => !!$attrtib['attrib_sort'],
						'query' => !!$attrtib['search']
				);
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
			if(!$entity->get_id())
			{
				$entity->account_id = (int)$GLOBALS['phpgw_info']['user']['account_id'];
			}

		}

		protected function doValidate( $entity, &$errors )
		{
			$values =  rental_somovein::get_instance()->read(array('filters' => array('contract_id' => $entity->contract_id)));

			//Duplicate
			if(!$entity->get_id() &&!empty($values['results']))
			{
				$errors['contract_id'] = lang("report is already recorded for %1", $entity->contract_id);
			}
		}

		public function serialize()
		{
			return self::toArray();
		}

		public function store()
		{
			return rental_bomovein::get_instance()->store($this);
		}

		public function read_single($id)
		{
			return rental_bomovein::get_instance()->read_single($id, true);
		}
	}
