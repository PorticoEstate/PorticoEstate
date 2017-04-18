<?php
	/**
	 * phpGroupWare - helpdesk: a part of a Facilities Management System.
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
	 * @package helpdesk
	 * @subpackage email_out
	 * @version $Id: $
	 */

	phpgw::import_class('helpdesk.boemail_out');

	include_class('phpgwapi', 'model', 'inc/model/');

	class helpdesk_email_out extends phpgwapi_model
	{

		const STATUS_PENDING = 0;
		const STATUS_SENT = 1;
		const STATUS_ERROR = 2;
		const acl_location = '.email_out';

		protected
			$id,
			$name,
			$remark,
			$subject,
			$content,
			$user_id,
			$created,
			$modified;


		protected $field_of_responsibility_name = '.email_out';

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
			return new helpdesk_email_out();
		}

		public static function get_status_list()
		{
			return array(
				self::STATUS_PENDING	=> lang('planned'),
				self::STATUS_SENT => lang('sent'),
				self::STATUS_ERROR	=> lang('error')
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
				'name' => array(
					'action'=> PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'label' => 'name',
					'required' => true,
					'query' => true,
					),
				'subject' => array(
					'action'=> PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'label' => 'subject',
					'required' => true,
					'query' => true,
					),
				'content' => array(
					'action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'html',
					'label' => 'content',
					'required' => true,
					'query' => true,
					),
			 	'remark' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'label' => 'description',
					'sortable' => false,
//					'history'	=> true
					),
				'modified' => array('action'=> PHPGW_ACL_READ | PHPGW_ACL_EDIT,
					'type' => 'date',
					'label' => 'modified',
					'sortable' => true,
					)
			);

			if($debug)
			{
				foreach ($fields as $field => $field_info)
				{
					if(!property_exists('helpdesk_email_out', $field))
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
			$entity->modified = time();
		}


		public function serialize()
		{
			return self::toArray();
		}

		public function store()
		{
			return helpdesk_boemail_out::get_instance()->store($this);
		}

		public function read_single($id)
		{
			return helpdesk_boemail_out::get_instance()->read_single($id, true);
		}
	}
