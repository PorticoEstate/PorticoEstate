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
	phpgw::import_class('property.sogeneric_');

	class eventplanner_sogeneric extends property_sogeneric_
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
				case 'application_type':
					$info = array
						(
						'table' => 'eventplanner_application_type',
						'id' => array('name' => 'id', 'type' => 'auto'),
						'fields' => array(
							array('name' => 'name',
								'descr' => lang('name'),
								'type' => 'varchar'
							),
							array(
								'name' => 'active',
								'descr' => lang('active'),
								'type' => 'checkbox',
								'default' => 'checked',
								'filter' => true,
								'sortable' => true,
								'values_def' => array(
									'valueset' => array(array('id' => 1, 'name' => lang('active'))),
								)
							)
						),
						'edit_msg' => lang('edit type'),
						'add_msg' => lang('add type'),
						'name' => lang('type'),
						'acl_app' => 'eventplanner',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::eventplanner::application_type'
					);
					break;
				case 'resource_category':
					$info = array
						(
						'table' => 'eventplanner_resource_category',
						'id' => array('name' => 'id', 'type' => 'auto'),
						'fields' => array(
							array('name' => 'name',
								'descr' => lang('name'),
								'type' => 'varchar'
							)
						),
						'edit_msg' => lang('edit type'),
						'add_msg' => lang('add type'),
						'name' => lang('type'),
						'acl_app' => 'eventplanner',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::eventplanner::resource_category'
					);
					break;
				case 'vendor_category':
					$info = array
						(
						'table' => 'eventplanner_vendor_category',
						'id' => array('name' => 'id', 'type' => 'auto'),
						'fields' => array(
							array('name' => 'name',
								'descr' => lang('name'),
								'type' => 'varchar'
							)
						),
						'edit_msg' => lang('edit type'),
						'add_msg' => lang('add type'),
						'name' => lang('type'),
						'acl_app' => 'eventplanner',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::eventplanner::vendor_category'
					);
					break;
				case 'customer_category':
					$info = array
						(
						'table' => 'eventplanner_customer_category',
						'id' => array('name' => 'id', 'type' => 'auto'),
						'fields' => array(
							array('name' => 'name',
								'descr' => lang('name'),
								'type' => 'varchar'
							)
						),
						'edit_msg' => lang('edit type'),
						'add_msg' => lang('add type'),
						'name' => lang('type'),
						'acl_app' => 'eventplanner',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::eventplanner::customer_category'
					);
					break;

				default:
					$message = lang('ERROR: illegal type %1', $type);
					phpgwapi_cache::message_set($message, 'error');
			}

			$this->location_info = $info;
			return $info;
		}
	}