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
								'type' => 'varchar',
								'nullable' => false,
							),
							array
								(
								'name' => 'content',
								'descr' => lang('content'),
								'type' => 'text',
								'nullable' => false,
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
						'acl_app' => 'helpdesk',
						'system_location' => '.ticket.response_template',
						'acl_location' => '.ticket.response_template',
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
				case 'email_template':
					$info = array
						(
						'table' => 'phpgw_helpdesk_email_template',
						'id' => array('name' => 'id', 'type' => 'auto'),
						'fields' => array
							(
							array
								(
								'name' => 'name',
								'descr' => lang('name'),
								'type' => 'varchar',
								'nullable' => false
							),
							array
								(
								'name' => 'content',
								'descr' => lang('content'),
								'type' => 'html',
								'nullable' => false
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
						'acl_app' => 'helpdesk',
						'system_location' => '.email_out.email_template',
						'acl_location' => '.email_out',
						'menu_selection' => 'helpdesk::email_out::email_template',
						'default' => array
							(
							'user_id' => array('add' => '$this->account'),
							'created' => array('add' => 'time()'),
							'modified' => array('edit' => 'time()'),
						),
						'check_grant' => true
					);

					break;
				case 'email_recipient_set':
					$info = array
						(
						'table' => 'phpgw_helpdesk_email_out_recipient_set',
						'id' => array('name' => 'id', 'type' => 'auto'),
						'fields' => array
							(
							array
								(
								'name' => 'name',
								'descr' => lang('name'),
								'type' => 'varchar',
								'nullable' => false
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
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('admin recipient set'),
						'acl_app' => 'helpdesk',
						'system_location' => '.email_out.recipient_set',
						'acl_location' => '.email_out',
						'menu_selection' => 'helpdesk::email_out::recipient_set',
						'default' => array
							(
							'user_id' => array('add' => '$this->account'),
							'created' => array('add' => 'time()'),
							'modified' => array('edit' => 'time()'),
						),
	//					'check_grant' => true
					);

					break;
				case 'email_recipient_list':
					$info = array
						(
						'table' => 'phpgw_helpdesk_email_out_recipient_list',
						'id' => array('name' => 'id', 'type' => 'auto'),
						'fields' => array
							(
							array
								(
								'name' => 'name',
								'descr' => lang('name'),
								'type' => 'varchar',
								'nullable' => false
							),
							array
								(
								'name' => 'email',
								'descr' => lang('email'),
								'type' => 'varchar',
								'nullable' => false
							),
							array(
								'name' => 'set_id',
								'descr' => $GLOBALS['phpgw']->translation->translate('recipient set', array(), false, 'helpdesk'),
								'type' => 'select',
								'filter' => true,
								'nullable' => false,
								'values_def' => array(
									'valueset' => false,
									'method' => 'helpdesk.bogeneric.get_list',
									'get_single_value' => 'helpdesk.sogeneric.get_name',
									'method_input' => array('type' => 'email_recipient_set', 'selected' => '##set_id##')
								)
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
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('recipient list'),
						'acl_app' => 'helpdesk',
						'system_location' => '.email_out.recipient_list',
						'acl_location' => '.email_out',
						'menu_selection' => 'helpdesk::email_out::recipient_list',
						'default' => array
							(
							'user_id' => array('add' => '$this->account'),
							'created' => array('add' => 'time()'),
							'modified' => array('edit' => 'time()'),
						),
		//				'check_grant' => true
					);

					break;
				case 'custom_menu_items':
					$info = array
						(
						'table' => 'phpgw_helpdesk_custom_menu_items',
						'id' => array('name' => 'id', 'type' => 'auto'),
						'fields' => array
							(
							array
								(
								'name' => 'parent_id',
								'descr' => lang('parent'),
								'type' => 'select',
								'sortable' => true,
								'nullable' => true,
								'filter' => false,
								'role' => 'parent',
								'values_def' => array
									(
									'valueset' => false,
									'method' => 'helpdesk.bogeneric.get_list',
									'method_input' => array('type' => 'custom_menu_items', 'role' => 'parent',
										'selected' => '##parent_id##', 'mapping' => array('name' => 'text'))
								)
							),
							array
								(
								'name' => 'text',
								'descr' => lang('name'),
								'type' => 'varchar'
							),
							array
								(
								'name' => 'url',
								'descr' => lang('url'),
								'type' => 'text'
							),
							array
								(
								'name' => 'target',
								'descr' => lang('target'),
								'type' => 'select',
								'filter' => false,
								'values_def' => array
									(
									'valueset' => array(array('id' => '_blank', 'name' => '_blank'), array(
											'id' => '_parent', 'name' => '_parent')),
								)
							),
							array
								(
								'name' => 'location',
								'descr' => lang('location'),
								'type' => 'select',
								'filter' => true,
								'values_def' => array
									(
									'valueset' => false,
									'method' => 'preferences.boadmin_acl.get_locations',
									'method_input' => array('acl_app' => 'helpdesk', 'selected' => '##location##')
								)
							),
							array
								(
								'name' => 'local_files',
								'descr' => lang('local files'),
								'type' => 'checkbox',
								'default' => ''
							)
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('custom menu items'),
						'acl_app' => 'helpdesk',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::helpdesk::custom_menu_items',
						'default' => array
							(
							'user_id' => array('add' => '$this->account'),
							'entry_date' => array('add' => 'time()'),
							'modified_date' => array('edit' => 'time()'),
						),
						'check_grant' => false,
						'mapping' => array('name' => 'text')
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