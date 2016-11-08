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
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/ and Nordlandssykehuset
	 * @package helpdesk
	 * @subpackage generic
	 * @version $Id: $
	 */
	phpgw::import_class('property.sogeneric_');

	class helpdesk_sogeneric extends property_sogeneric_
	{
		var $appname = 'helpdesk';

		function __construct( $type = '', $type_id = 0 )
		{
			parent::__construct($type, $type_id);
		}


		public function get_location_info( $type, $type_id )
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
				case 'helpdesk_status':
					$info = array
						(
						'table' => 'phpgw_helpdesk_status',
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
								'name' => 'sorting',
								'descr' => lang('sorting'),
								'type' => 'integer',
								'sortable' => true
							),
							array
								(
								'name' => 'color',
								'descr' => lang('color'),
								'type' => 'varchar'
							),
							array
								(
								'name' => 'approved',
								'descr' => lang('approved'),
								'type' => 'checkbox'
							),
							array
								(
								'name' => 'in_progress',
								'descr' => lang('In progress'),
								'type' => 'checkbox'
							),
							array
								(
								'name' => 'delivered',
								'descr' => lang('delivered'),
								'type' => 'checkbox'
							),
							array
								(
								'name' => 'closed',
								'descr' => lang('closed'),
								'type' => 'checkbox'
							)
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('event action'),
						'acl_app' => 'helpdesk',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::helpdesk::ticket_status'
					);
					break;
				case 'response_template':
					$info = array
						(
						'table' => 'phpgw_helpdesk_response_template',
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
						'name' => lang('response template'),
						'acl_app' => 'property',
						'acl_location' => '.ticket',
						'menu_selection' => 'helpdesk::response_template',
						'default' => array
							(
							'user_id' => array('add' => '$this->account'),
							'entry_date' => array('add' => 'time()'),
							'modified_date' => array('edit' => 'time()'),
						),
						'check_grant' => true
					);

					break;
				default:
					$message = lang('ERROR: illegal type %1', $type);
					phpgwapi_cache::message_set($message, 'error');
//				throw new Exception(lang('ERROR: illegal type %1', $type));
			}

			$this->location_info = $info;
			return $info;
		}


	}