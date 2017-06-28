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

	include_class('eventplanner', 'application', 'inc/model/');

	class eventplanner_events extends eventplanner_application
	{

		const acl_location = '.events';

		protected $field_of_responsibility_name = '.events';

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
			return new eventplanner_events();
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
			 	'title' => array('action'=> PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'string',
					'label' => 'title',
					'sortable' => false,
					'query' => true,
					'public' => true,
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
				'number_of_units' => array('action'=> PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int',
					'label'	=> 'number of units',
					'required' => true,
					'history' => true
					)
			);

			return $fields;
		}

	}
