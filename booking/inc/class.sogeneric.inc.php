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
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/ and Nordlandssykehuset
	 * @package booking
	 * @subpackage generic
	 * @version $Id: $
	 */
	phpgw::import_class('property.sogeneric_');

	class booking_sogeneric extends property_sogeneric_
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
				case 'bb_office':
					$info = array(
						'table'				 => 'bb_office',
						'id'				 => array('name' => 'id', 'type' => 'auto'),
						'fields'			 => array(
							array(
								'name'	 => 'name',
								'descr'	 => lang('name'),
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
						'acl_app'			 => 'booking',
						'acl_location'		 => '.office',
						'system_location'	 => '.office',
						'menu_selection'	 => 'booking::settings::office::office',
						'default'			 => array(
							'user_id'		 => array('add' => '$this->account'),
							'entry_date'	 => array('add' => 'time()'),
							'modified_date'	 => array('edit' => 'time()'),
						),
						'check_grant'		 => false
					);

					break;
				case 'bb_office_user':
					$info = array(
						'table'				 => 'bb_office_user',
						'id'				 => array('name' => 'id', 'type' => 'auto'),
						'fields'			 => array(
							array(
								'name'		 => 'office',
								'descr'		 => $GLOBALS['phpgw']->translation->translate('office', array(), false, 'booking'),
								'type'		 => 'select',
								'filter'	 => true,
								'values_def' => array(
									'valueset'			 => false,
									'method'			 => 'booking.bogeneric.get_list',
									'get_single_value'	 => 'booking.sogeneric.get_name',
									'method_input'		 => array('type' => 'bb_office', 'selected' => '##office##')
								)
							)
						),
						'edit_msg'			 => lang('edit'),
						'add_msg'			 => lang('add'),
						'name'				 => $GLOBALS['phpgw']->translation->translate('office user', array(), false, 'booking'),
						'acl_app'			 => 'booking',
						'acl_location'		 => '.office.user',
						'system_location'	 => '.office.user',
						'menu_selection'	 => 'booking::settings::office::office_user',
						'default'			 => array(
							'user_id'		 => array('add' => '$this->account'),
							'entry_date'	 => array('add' => 'time()'),
							'modified_date'	 => array('edit' => 'time()'),
						),
						'check_grant'		 => false
					);

					break;
				case 'article_category':
					$info = array(
						'table'				 => 'bb_article_category',
						'id'				 => array('name' => 'id', 'type' => 'auto'),
						'fields'			 => array(
							array(
								'name'	 => 'name',
								'descr'	 => lang('name'),
								'type'	 => 'varchar'
							),
						),
						'edit_msg'			 => lang('edit'),
						'add_msg'			 => lang('add'),
						'name'				 => $GLOBALS['phpgw']->translation->translate('article category', array(), false, 'booking'),
						'acl_app'			 => 'booking',
						'acl_location'		 => '.admin',
						'system_location'	 => '.article',
						'menu_selection'	 => 'booking::settings::article',
						'default'			 => array(
							'owner_id' => array('add' => '$this->account'),
						),
						'check_grant'		 => false
					);

					break;
				case 'article_service':
					$info	 = array(
						'table'				 => 'bb_service',
						'id'				 => array('name' => 'id', 'type' => 'auto'),
						'fields'			 => array(
							array(
								'name'	 => 'name',
								'descr'	 => lang('name'),
								'type'	 => 'varchar'
							),
							array(
								'name'	 => 'description',
								'descr'	 => lang('description'),
								'type'	 => 'text'
							),
							array(
								'name'		 => 'active',
								'descr'		 => lang('active'),
								'type'		 => 'checkbox',
								'default'	 => 'checked',
								'filter'	 => true,
								'sortable'	 => true,
								'values_def' => array(
									'valueset' => array(array('id' => 1, 'name' => lang('active'))),
								)
							)
						),
						'edit_msg'			 => lang('edit'),
						'add_msg'			 => lang('add'),
						'name'				 => $GLOBALS['phpgw']->translation->translate('article service', array(), false, 'booking'),
						'acl_app'			 => 'booking',
						'acl_location'		 => '.admin',
						'system_location'	 => '.article',
						'menu_selection'	 => 'booking::commerce::service',
						'check_grant'		 => false
					);
					break;
				case 'tax':
					$info	 = array(
						'table'			 => 'fm_ecomva',
						'id'			 => array('name' => 'id', 'type' => 'int'),
						'fields'		 => array(
							array(
								'name'	 => 'percent_',
								'descr'	 => lang('percent'),
								'type'	 => 'int'
							),
							array(
								'name'	 => 'descr',
								'descr'	 => lang('descr'),
								'type'	 => 'varchar'
							)
						),
						'edit_msg'		 => lang('edit'),
						'add_msg'		 => lang('add'),
						'name'			 => lang('tax code'),
						'acl_app'		 => 'booking',
						'acl_location'	 => '.admin',
						'menu_selection' => 'booking::commerce::accounting_tax'
					);
					break;

				case 'e_lock_system':
					$info	 = array(
						'table'			 => 'bb_e_lock_system',
						'id'			 => array('name' => 'id', 'type' => 'int'),
						'fields'		 => array(
							array(
								'name'	 => 'name',
								'descr'	 => lang('name'),
								'type'	 => 'varchar'
							),
							array(
								'name'	 => 'webservicehost',
								'descr'	 => 'WebServiceHost',
								'type'	 => 'varchar'
							),
							array(
								'name'	 => 'instruction',
								'descr'	 => lang('receipt'),
								'type'	 => 'html'
							),
							array(
								'name'		 => 'sms_alert',
								'descr'		 => lang('sms alert'),
								'type'		 => 'checkbox',
								'default'	 => 'checked',
								'sortable'	 => true,
							)
						),
						'edit_msg'		 => lang('edit'),
						'add_msg'		 => lang('add'),
						'name'			 => $GLOBALS['phpgw']->translation->translate('e_lock_system', array(), false, 'booking'),
						'acl_app'		 => 'booking',
						'acl_location'	 => '.admin',
						'menu_selection' => 'booking::settings::e_lock_system',
						'default'			 => array(
							'user_id'		 => array('add' => '$this->account'),
							'entry_date'	 => array('add' => 'time()'),
							'modified_date'	 => array('edit' => 'time()'),
						),
						'check_grant'		 => false
					);
					break;
				case 'article_group':
					$info	 = array(
						'table'			 => 'bb_article_group',
						'id'			 => array('name' => 'id', 'type' => 'int'),
						'fields'		 => array(
							array(
								'name'	 => 'name',
								'descr'	 => lang('name'),
								'type'	 => 'varchar'
							),
							array(
								'name'	 => 'remark',
								'descr'	 => lang('remark'),
								'type'	 => 'text'
							)
						),
						'edit_msg'		 => lang('edit'),
						'add_msg'		 => lang('add'),
						'name'			 => lang('article group'),
						'acl_app'		 => 'booking',
						'acl_location'	 => '.admin',
						'menu_selection' => 'booking::commerce::article_group'
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