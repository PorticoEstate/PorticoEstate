<?php
	/**
	 * phpGroupWare - controller: a part of a Facilities Management System.
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
	 * @package booking
	 * @subpackage generic
	 * @version $Id: $
	 */
	phpgw::import_class('property.sogeneric_');

	class controller_sogeneric extends property_sogeneric_
	{

		var $appname = 'booking';

		function __construct( $type = '', $type_id = 0 )
		{
			parent::__construct($type, $type_id);
		}

		public function get_location_info( $type, $type_id = 0 )
		{

			$type_id		 = (int)$type_id;
			$this->type		 = $type;
			$this->type_id	 = $type_id;
			$info			 = array();

			if (!$type)
			{
				return $info;
			}

			switch ($type)
			{
// START BOOKING TABLES
				case 'control':
					$info = array(
						'table'				 => 'controller_control',
						'id'				 => array('name' => 'id', 'type' => 'auto'),
						'fields'			 => array(
							array(
								'name'	 => 'title',
								'descr'	 => lang('title'),
								'type'	 => 'varchar'
							),
							array(
								'name'	 => 'description',
								'descr'	 => lang('description'),
								'type'	 => 'text'
							)
						),
						'edit_msg'			 => lang('edit'),
						'add_msg'			 => lang('add'),
						'name'				 => $GLOBALS['phpgw']->translation->translate('office', array(), false, 'booking'),
						'acl_app'			 => 'controller',
						'acl_location'		 => 'admin',
						'system_location'	 => 'admin',
						'menu_selection'	 => 'controller::settings::control',
						'check_grant'		 => true
					);

					break;
				case 'control_category':
					$info = array(
						'table'				 => 'controller_control_category',
						'id'				 => array('name' => 'id', 'type' => 'auto'),
						'fields'			 => array(
							array(
								'name'		 => 'control_id',
								'descr'		 => $GLOBALS['phpgw']->translation->translate('control', array(), false, 'controller'),
								'type'		 => 'select',
								'filter'	 => true,
								'values_def' => array(
									'valueset'			 => false,
									'method'			 => 'controller.bogeneric.get_list',
									'get_single_value'	 => 'controller.sogeneric.get_name',
									'method_input'		 => array('type' => 'control', 'selected' => '##control_id##')
								)
							),
							array(
								'name'	 => 'name',
								'descr'	 => lang('name'),
								'type'	 => 'varchar'
							),
						),
						'edit_msg'			 => lang('edit'),
						'add_msg'			 => lang('add'),
						'name'				 => $GLOBALS['phpgw']->translation->translate('category', array(), false, 'controller'),
						'acl_app'			 => 'controller',
						'acl_location'		 => 'admin',
						'system_location'	 => 'admin',
						'menu_selection'	 => 'admin::controller::control_category',
						'check_grant'		 => false
					);

					break;
// END BOOKING TABLES
				default:
					$message = lang('ERROR: illegal type %1', $type);
					phpgwapi_cache::message_set($message, 'error');
//				throw new Exception(lang('ERROR: illegal type %1', $type));
			}

			$info['type']		 = $type;
			$info['type_id']	 = $type_id;
			$this->location_info = $info;
			return $info;
		}
	}