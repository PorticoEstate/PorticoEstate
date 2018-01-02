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
	 * @subpackage application
	 * @version $Id: $
	 */
	phpgw::import_class('property.sogeneric_');

	class rental_sogeneric extends property_sogeneric_
	{
		function __construct( $type = '', $type_id = 0 )
		{
			parent::__construct($type, $type_id);
		}

		public function get_location_info( $type, $type_id = 0 )
		{

			$type_id = (int)$type_id;
			$this->type = $type;
			$this->type_id = $type_id;
			$info = array();

			if (!$type)
			{
				return $info;
			}

			switch ($type)
			{
// START RENTAL TABLES
				case 'location_factor':
					$info = array
						(
						'table' => 'rental_location_factor',
						'id' => array('name' => 'id', 'type' => 'auto'),
						'fields' => array(
							array(
								'name' => 'part_of_town_id',
								'descr' => lang('location'),
								'type' => 'select',
								'nullable' => false,
								'filter' => true,
								'values_def' => array(
									'valueset' => false,
									'get_single_value' => 'property.sogeneric.get_name',
									'method' => 'property.bogeneric.get_list',
									'method_input' => array('type' => 'part_of_town', 'selected' => '##part_of_town_id##')
								)
							),
							array(
								'name' => 'factor',
								'descr' => lang('factor'),
								'type' => 'numeric',
								'nullable' => false,
								'size' => 4,
								'sortable' => true
							),
							array(
								'name' => 'remark',
								'descr' => lang('remark'),
								'type' => 'text'
							)
						),
						'edit_msg' => lang('edit unit'),
						'add_msg' => lang('add unit'),
						'name' => lang('unit'),
						'acl_app' => 'rental',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::rental::location_factor',
						'default' => array(
							'user_id' => array('add' => '$this->account'),
							'entry_date' => array('add' => 'time()'),
							'modified_date' => array('edit' => 'time()'),
						)
					);
					break;
				case 'composite_standard':
					$info = array
						(
						'table' => 'rental_composite_standard',
						'id' => array('name' => 'id', 'type' => 'int'),
						'fields' => array
							(
							array
								(
								'name' => 'name',
								'descr' => lang('name'),
								'type' => 'varchar'
							),
							array
								(
								'name' => 'factor',
								'descr' => lang('factor'),
								'type' => 'numeric',
								'nullable' => false,
								'size' => 4,
								'sortable' => true
							)
						),
						'edit_msg' => lang('edit unit'),
						'add_msg' => lang('add unit'),
						'name' => lang('unit'),
						'acl_app' => 'rental',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::rental::composite_standard'
					);
					break;
				case 'responsibility_unit':
					$info = array
						(
						'table' => 'rental_contract_responsibility_unit',
						'id' => array('name' => 'id', 'type' => 'int'),
						'fields' => array
							(
							array
								(
								'name' => 'name',
								'descr' => lang('name'),
								'type' => 'varchar'
							),
						),
						'edit_msg' => lang('edit unit'),
						'add_msg' => lang('add unit'),
						'name' => lang('unit'),
						'acl_app' => 'rental',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::rental::responsibility_unit'
					);
					break;
				case 'composite_type':
					$info = array
						(
						'table' => 'rental_composite_type',
						'id' => array('name' => 'id', 'type' => 'int'),
						'fields' => array(
							array(
								'name' => 'name',
								'descr' => lang('name'),
								'type' => 'varchar'
							)
						),
						'edit_msg' => lang('edit type'),
						'add_msg' => lang('add type'),
						'name' => lang('type'),
						'acl_app' => 'rental',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::rental::composite_type'
					);
					break;
				case 'email_template':
					$info = array
						(
						'table' => 'rental_email_template',
						'id' => array('name' => 'id', 'type' => 'auto'),
						'fields' => array
							(
							array
								(
								'name' => 'name',
								'descr' => lang('name'),
								'type' => 'varchar'
							),
							array
								(
								'name' => 'content',
								'descr' => lang('content'),
								'type' => 'text'
							),
							array
								(
								'name' => 'public',
								'descr' => lang('public'),
								'type' => 'checkbox'
							)
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('email template'),
						'acl_app' => 'rental',
						'system_location' => '.email_out.email_template',
						'acl_location' => '.email_out',
						'menu_selection' => 'rental::email_out::email_template',
						'default' => array
							(
							'user_id' => array('add' => '$this->account'),
							'created' => array('add' => 'time()'),
							'modified' => array('edit' => 'time()'),
						),
						'check_grant' => true
					);

					break;

// END RENTAL TABLES

				default:
					$message = lang('ERROR: illegal type %1', $type);
					phpgwapi_cache::message_set($message, 'error');
					throw new Exception;
			}

			$this->location_info = $info;
			return $info;
		}
	}