<?php
	/**
	 * phpGroupWare - property: a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2003,2004,2005,2006,2007,2008,2009 Free Software Foundation, Inc. http://www.fsf.org/
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
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package property
	 * @subpackage admin
	 * @version $Id: class.sogeneric.inc.php 15493 2016-08-19 11:45:55Z sigurdne $
	 */

	phpgw::import_class('property.sogeneric_');

	/**
	 * Description
	 * @package property
	 */
	class property_sogeneric extends property_sogeneric_
	{

		var $appname = 'property';

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
				//-------- ID type integer
				case 'part_of_town':
					$info = array
						(
						'table' => 'fm_part_of_town',
						'id' => array('name' => 'id', 'type' => 'int', 'descr' => lang('id')),
						'fields' => array(
							array(
								'name' => 'name',
								'descr' => lang('name'),
								'type' => 'varchar',
								'nullable' => false,
								'size' => 20
							),
							array(
								'name' => 'delivery_address',
								'descr' => lang('delivery address'),
								'type' => 'text'
							),
							array(
								'name' => 'district_id',
								'descr' => lang('district'),
								'type' => 'select',
								'nullable' => false,
								'filter' => true,
								'values_def' => array
									(
									'valueset' => false,
									'method' => 'property.bogeneric.get_list',
									'get_single_value' => 'property.sogeneric.get_name',
									'method_input' => array('type' => 'district', 'selected' => '##district_id##')
								)
							),
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('part of town'),
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::location::town',
						/*
						  'default'			=> array
						  (
						  'user_id' 		=> array('add'	=> '$this->account'),
						  'entry_date'	=> array('add'	=> 'time()'),
						  'modified_date'	=> array('edit'	=> 'time()'),
						  ),
						 */
						'check_grant' => false
					);

					break;

				case 'dimb':
					$info = array
						(
						'table' => 'fm_ecodimb',
						'id' => array('name' => 'id', 'type' => 'int'),
						'fields' => array
							(
							array
								(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							),
							array
								(
								'name' => 'org_unit_id',
								'descr' => lang('department'),
								'type' => 'select',
								'nullable' => false,
								'filter' => true,
								'sortable' => true,
								'values_def' => array
									(
									'valueset' => false,
									'method' => 'property.bogeneric.get_list',
									'get_single_value' => 'property.sogeneric.get_name',
									'method_input' => array('type' => 'org_unit', 'selected' => '##org_unit_id##')
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
						'custom_criteria' => array
							(
							'dimb_role_user' => array
								(
								'join' => array("{$this->_db->join} fm_ecodimb_role_user ON fm_ecodimb.id = fm_ecodimb_role_user.ecodimb"),
								'filter' => array('fm_ecodimb_role_user.user_id = ' . (int)$this->account)
							)
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('dimb'),
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::accounting::accounting_dimb'
					);
					break;
				case 'dimd':
					$info = array
						(
						'table' => 'fm_ecodimd',
						'id' => array('name' => 'id', 'type' => 'varhcar'),
						'fields' => array
							(
							array
								(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('dimd'),
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::accounting::accounting_dimd'
					);
					break;
				case 'periodization':
					$info = array
						(
						'table' => 'fm_eco_periodization',
						'id' => array('name' => 'id', 'type' => 'int'),
						'fields' => array
							(
							array
								(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							),
							array
								(
								'name' => 'active',
								'descr' => lang('active'),
								'type' => 'checkbox',
								'default' => 'checked'
							)
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('periodization'),
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::accounting::periodization'
					);
					break;
				case 'tax':
					$info = array
						(
						'table' => 'fm_ecomva',
						'id' => array('name' => 'id', 'type' => 'int'),
						'fields' => array
							(
							array
								(
								'name' => 'percent',
								'descr' => lang('percent'),
								'type' => 'int'
							),
							array
								(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('tax code'),
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::accounting::accounting_tax'
					);
					break;
				case 'voucher_cat':
					$info = array
						(
						'table' => 'fm_ecobilag_category',
						'id' => array('name' => 'id', 'type' => 'int'),
						'fields' => array
							(
							array
								(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => '',
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::accounting::voucher_cats'
					);
					break;
				case 'voucher_type':
					$info = array
						(
						'table' => 'fm_ecoart',
						'id' => array('name' => 'id', 'type' => 'int'),
						'fields' => array
							(
							array
								(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => '',
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::accounting::voucher_type'
					);
					break;
				case 'tender_chapter':
					$info = array
						(
						'table' => 'fm_chapter',
						'id' => array('name' => 'id', 'type' => 'int'),
						'fields' => array
							(
							array
								(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => '',
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::tender'
					);
					break;
				case 'location':

					$this->_db->query("SELECT id FROM fm_location_type WHERE id ={$type_id}", __LINE__, __FILE__);

					if ($this->_db->next_record())
					{
						$info = array
							(
							'table' => "fm_location{$type_id}_category",
							'id' => array('name' => 'id', 'type' => 'varchar'),
							'fields' => array
								(
								array
									(
									'name' => 'descr',
									'descr' => lang('descr'),
									'type' => 'varchar'
								)
							),
							'edit_msg' => lang('edit'),
							'add_msg' => lang('add'),
							'name' => lang('category'),
							'acl_app' => 'property',
							'acl_location' => '.admin',
							'menu_selection' => "admin::property::location::location::category_{$type_id}"
						);
					}
					else
					{
						throw new Exception(lang('ERROR: illegal type %1', $type_id));
					}
					break;
				case 'owner_cats':
					$info = array
						(
						'table' => 'fm_owner_category',
						'id' => array('name' => 'id', 'type' => 'int'),
						'fields' => array
							(
							array
								(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => '',
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::owner::owner_cats'
					);
					break;
				case 'tenant_cats':
					$info = array
						(
						'table' => 'fm_tenant_category',
						'id' => array('name' => 'id', 'type' => 'int'),
						'fields' => array
							(
							array
								(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('tenant category'),
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::tenant::tenant_cats'
					);
					break;
				case 'vendor_cats':
					$info = array
						(
						'table' => 'fm_vendor_category',
						'id' => array('name' => 'id', 'type' => 'int'),
						'fields' => array
							(
							array
								(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('vendor category'),
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::vendor::vendor_cats'
					);
					break;
				case 'vendor':
					$info = array
						(
						'table' => 'fm_vendor',
						'id' => array('name' => 'id', 'type' => 'int'),
						'fields' => array(
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
							),
							array(
								'name' => 'contact_phone',
								'descr' => lang('contact phone'),
								'type' => 'varchar'
							),
							array(
								'name' => 'category',
								'descr' => lang('category'),
								'type' => 'select',
								'nullable' => false,
								'filter' => true,
								'sortable' => true,
								'values_def' => array
									(
									'valueset' => false,
									'method' => 'property.bogeneric.get_list',
									'get_single_value' => 'property.sogeneric.get_name',
									'method_input' => array('type' => 'vendor_cats', 'selected' => '##category##')
								)
							),
							array
								(
								//FIXME
								'name' => 'member_of',
								'descr' => lang('member'),
								'type' => 'multiple_select',
								'nullable' => true,
								'filter' => true,
								'sortable' => false,
								'hidden' => false,
								'values_def' => array
									(
									'valueset' => false,
									'method' => 'property.bocommon.get_categories',
									'method_input' => array('app' => 'property', 'acl_location' => '.vendor',
										'selected' => '##member_of##')
								)
							),
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('vendor'),
						'acl_app' => 'property',
						'system_location' => '.vendor',
						'acl_location' => '.vendor',
						'menu_selection' => 'property::economy::vendor',
						'default' => array
							(
							'owner_id' => array('add' => '$this->account'),
							'entry_date' => array('add' => 'time()'),
						//			'modified_date'	=> array('edit'	=> 'time()'),
						)
					);
					break;
				case 'owner':
					$info = array
						(
						'table' => 'fm_owner',
						'id' => array('name' => 'id', 'type' => 'int'),
						'fields' => array
							(
							array
								(
								'name' => 'remark',
								'descr' => lang('remark'),
								'type' => 'text'
							),
							array
								(
								'name' => 'category',
								'descr' => lang('category'),
								'type' => 'select',
								'nullable' => false,
								'filter' => true,
								'sortable' => true,
								'values_def' => array
									(
									'valueset' => false,
									'method' => 'property.bogeneric.get_list',
									'get_single_value' => 'property.sogeneric.get_name',
									'method_input' => array('type' => 'owner_cats', 'selected' => '##category##')
								)
							),
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('owner'),
						'acl_app' => 'property',
						'acl_location' => '.owner',
						'menu_selection' => 'admin::property::owner',
						'default' => array
							(
							'owner_id' => array('add' => '$this->account'),
							'entry_date' => array('add' => 'time()'),
						//			'modified_date'	=> array('edit'	=> 'time()'),
						)
					);
					break;
				case 'tenant':
					$info = array
						(
						'table' => 'fm_tenant',
						'id' => array('name' => 'id', 'type' => 'int'),
						'fields' => array
							(
							array
								(
								'name' => 'contact_email',
								'descr' => lang('contact email'),
								'type' => 'varchar',
								'sortable' => true,
							),
							array
								(
								'name' => 'category',
								'descr' => lang('category'),
								'type' => 'select',
								'nullable' => false,
								'filter' => true,
								'sortable' => true,
								'values_def' => array
									(
									'valueset' => false,
									'method' => 'property.bogeneric.get_list',
									'get_single_value' => 'property.sogeneric.get_name',
									'method_input' => array('type' => 'tenant_cats', 'selected' => '##category##')
								)
							)
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('tenant'),
						'acl_app' => 'property',
						'acl_location' => '.tenant',
						'menu_selection' => 'admin::property::tenant',
						'default' => array
							(
							'owner_id' => array('add' => '$this->account'),
							'entry_date' => array('add' => 'time()'),
						//			'modified_date'	=> array('edit'	=> 'time()'),
						)
					);
					break;
				case 'district':
					$info = array
						(
						'table' => 'fm_district',
						'id' => array('name' => 'id', 'type' => 'int'),
						'fields' => array(
							array(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							),
							array(
								'name' => 'delivery_address',
								'descr' => lang('delivery address'),
								'type' => 'text'
							)
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('district'),
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::location::district'
					);
					break;
				case 'street':
					$info = array
						(
						'table' => 'fm_streetaddress',
						'id' => array('name' => 'id', 'type' => 'int'),
						'fields' => array
							(
							array
								(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('streetaddress'),
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::location::street'
					);
					break;
				case 's_agreement':
					$info = array
						(
						'table' => 'fm_s_agreement_category',
						'id' => array('name' => 'id', 'type' => 'int'),
						'fields' => array
							(
							array
								(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => '',
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::agreement::service_agree_cats'
					);
					break;
				case 'tenant_claim':
					$info = array
						(
						'table' => 'fm_tenant_claim_category',
						'id' => array('name' => 'id', 'type' => 'int'),
						'fields' => array
							(
							array
								(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => '',
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::tenant::claims_cats'
					);
					break;
				case 'wo_hours':
					$info = array
						(
						'table' => 'fm_wo_hours_category',
						'id' => array('name' => 'id', 'type' => 'int'),
						'fields' => array
							(
							array
								(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => '',
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::workorder_detail'
					);
					break;
				case 'r_condition_type':
					$info = array
						(
						'table' => 'fm_request_condition_type',
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
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => 'condition type',
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::request_condition'
					);
					break;
				case 'authorities_demands':
					$info = array
						(
						'table' => 'fm_authorities_demands',
						'id' => array('name' => 'id', 'type' => 'int'),
						'fields' => array
							(
							array
								(
								'name' => 'name',
								'descr' => lang('name'),
								'type' => 'varchar'
							)
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('authorities demands'),
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::authorities_demands',
						'default' => array
							(
							'user_id' => array('add' => '$this->account'),
							'entry_date' => array('add' => 'time()'),
							'modified_date' => array('edit' => 'time()'),
						),
						'check_grant' => false
					);
					break;
				case 'b_account':
				case 'b_account_category':
					$info = array
						(
						'table' => 'fm_b_account_category',
						'id' => array('name' => 'id', 'type' => 'int'),
						'fields' => array
							(
							array
								(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							),
							array(
								'name' => 'active',
								'descr' => lang('active'),
								'type' => 'checkbox',
								'default' => 'checked',
								'filter' => true,
								'sortable' => true,
								'values_def' => array
									(
									'valueset' => array(array('id' => 1, 'name' => lang('active'))),
								)
							),
							array
								(
								'name' => 'external_project',
								'descr' => lang('mandatory project group'),
								'type' => 'checkbox'
							)
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('budget account group'),
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::accounting::accounting_cats'
					);
					break;

				case 'dimb_role':
					$info = array
						(
						'table' => 'fm_ecodimb_role',
						'id' => array('name' => 'id', 'type' => 'int'),
						'fields' => array
							(
							array
								(
								'name' => 'name',
								'descr' => lang('name'),
								'type' => 'varchar'
							)
						),
						'edit_msg' => lang('edit role'),
						'add_msg' => lang('add role'),
						'name' => lang('dimb role'),
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::accounting::dimb_role'
					);
					break;
				case 'condition_survey_status':
					$info = array
						(
						'table' => 'fm_condition_survey_status',
						'id' => array('name' => 'id', 'type' => 'int'),
						'fields' => array
							(
							array
								(
								'name' => 'descr',
								'descr' => lang('descr'),
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
						'edit_msg' => lang('edit status'),
						'add_msg' => lang('add status'),
						'name' => lang('request status'),
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::condition_survey_status'
					);
					break;

				case 'request_responsible_unit':
					$_lang_responsible_unit = lang('responsible unit');
					$info = array
						(
						'table' => 'fm_request_responsible_unit',
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
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg' => lang('edit') . ' ' . $_lang_responsible_unit,
						'add_msg' => lang('add') . ' ' . $_lang_responsible_unit,
						'name' => $_lang_responsible_unit,
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::request_responsible_unit'
					);
					break;

				case 'ticket_priority':
					$_lang_priority = lang('priority');
					$info = array
						(
						'table' => 'fm_tts_priority',
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
						'edit_msg' => lang('edit') . ' ' . $_lang_priority,
						'add_msg' => lang('add') . ' ' . $_lang_priority,
						'name' => $_lang_priority,
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::ticket::ticket_priority'
					);
					break;

				case 'external_com_type':
					$_lang_external_com_type = lang('external communication type');
					$info = array
						(
						'table' => 'fm_tts_external_communication_type',
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
						'edit_msg' => lang('edit') . ' ' . $_lang_external_com_type,
						'add_msg' => lang('add') . ' ' . $_lang_external_com_type,
						'name' => $_lang_external_com_type,
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::ticket::external_com_type'
					);
					break;

				//-------- ID type varchar
				case 'external_project':
					$info = array
						(
						'table' => 'fm_external_project',
						'id' => array('name' => 'id', 'type' => 'varchar'),
						'fields' => array(
							array(
								'name' => 'name',
								'descr' => lang('name'),
								'type' => 'varchar'
							),
							array(
								'name' => 'budget',
								'descr' => lang('budget'),
								'type' => 'int'
							),
							array(
								'name' => 'active',
								'descr' => lang('active'),
								'type' => 'checkbox',
								'default' => 'checked',
								'filter' => true,
								'sortable' => true,
								'values_def' => array
									(
									'valueset' => array(array('id' => 1, 'name' => lang('active'))),
								)
							)
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('external project'),
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::external_project'
					);
					break;
				case 'unspsc_code':
					$info = array
						(
						'table' => 'fm_unspsc_code',
						'id' => array('name' => 'id', 'type' => 'varchar'),
						'fields' => array(
							array
								(
								'name' => 'name',
								'descr' => lang('name'),
								'type' => 'varchar'
							)
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('unspsc code'),
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::unspsc_code'
					);
					break;
				case 'project_status':
					$info = array
						(
						'table' => 'fm_project_status',
						'id' => array('name' => 'id', 'type' => 'varchar'),
						'fields' => array
							(
							array
								(
								'name' => 'descr',
								'descr' => lang('descr'),
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
								'name' => 'closed',
								'descr' => lang('closed'),
								'type' => 'checkbox'
							)
						),
						'edit_msg' => lang('edit status'),
						'add_msg' => lang('add status'),
						'name' => lang('project status'),
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::project_status'
					);
					break;
				case 'workorder_status':
					$info = array
						(
						'table' => 'fm_workorder_status',
						'id' => array('name' => 'id', 'type' => 'varchar'),
						'fields' => array
							(
							array
								(
								'name' => 'descr',
								'descr' => lang('descr'),
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
							),
							array
								(
								'name' => 'canceled',
								'descr' => lang('canceled'),
								'type' => 'checkbox'
							)
						),
						'edit_msg' => lang('edit status'),
						'add_msg' => lang('add status'),
						'name' => lang('workorder status'),
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::workorder_status'
					);
					break;
				case 'request_status':
					$info = array
						(
						'table' => 'fm_request_status',
						'id' => array('name' => 'id', 'type' => 'varchar'),
						'fields' => array
							(
							array
								(
								'name' => 'descr',
								'descr' => lang('descr'),
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
						'edit_msg' => lang('edit status'),
						'add_msg' => lang('add status'),
						'name' => lang('request status'),
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::request_status'
					);
					break;
				case 'agreement_status':
					$info = array
						(
						'table' => 'fm_agreement_status',
						'id' => array('name' => 'id', 'type' => 'varchar'),
						'fields' => array
							(
							array
								(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg' => lang('edit status'),
						'add_msg' => lang('add status'),
						'name' => lang('agreement status'),
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::agreement::agreement_status'
					);
					break;
				case 'building_part':

					$config = CreateObject('phpgwapi.config', 'property');
					$config->read();

					$filter_buildingpart = isset($config->config_data['filter_buildingpart']) ? $config->config_data['filter_buildingpart'] : array();

					$info = array
						(
						'table' => 'fm_building_part',
						'id' => array('name' => 'id', 'type' => 'varchar'),
						'fields' => array
							(
							array
								(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							),
							array
								(
								'name' => 'filter_1',
								'descr' => isset($filter_buildingpart[1]) && $filter_buildingpart[1] ? $filter_buildingpart[1] : 'Filter 1',
								'type' => 'checkbox',
								'filter' => true,
								'sortable' => true,
								'values_def' => array
									(
									'valueset' => array(array('id' => 1, 'name' => lang('active'))),
								)
							),
							array
								(
								'name' => 'filter_2',
								'descr' => isset($filter_buildingpart[2]) && $filter_buildingpart[2] ? $filter_buildingpart[2] : 'Filter 2',
								'type' => 'checkbox',
								'filter' => true,
								'sortable' => true,
								'values_def' => array
									(
									'valueset' => array(array('id' => 1, 'name' => lang('active'))),
								)
							),
							array
								(
								'name' => 'filter_3',
								'descr' => isset($filter_buildingpart[3]) && $filter_buildingpart[3] ? $filter_buildingpart[3] : 'Filter 3',
								'type' => 'checkbox',
								'filter' => true,
								'sortable' => true,
								'values_def' => array
									(
									'valueset' => array(array('id' => 1, 'name' => lang('active'))),
								)
							),
							array
								(
								'name' => 'filter_4',
								'descr' => isset($filter_buildingpart[4]) && $filter_buildingpart[4] ? $filter_buildingpart[4] : 'Filter 4',
								'type' => 'checkbox',
								'filter' => true,
								'sortable' => true,
								'values_def' => array
									(
									'valueset' => array(array('id' => 1, 'name' => lang('active'))),
								)
							),
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('building part'),
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::building_part'
					);
					break;
				case 'document_status':
					$info = array
						(
						'table' => 'fm_document_status',
						'id' => array('name' => 'id', 'type' => 'varchar'),
						'fields' => array
							(
							array
								(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg' => lang('edit status'),
						'add_msg' => lang('add status'),
						'name' => lang('document status'),
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::document_status'
					);
					break;
				case 'ns3420':
					$info = array
						(
						'table' => 'fm_ns3420',
						'id' => array('name' => 'id', 'type' => 'varchar'),
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
									'method' => 'property.bogeneric.get_list',
									'get_single_value' => 'property.sogeneric.get_name',
									'method_input' => array('type' => 'ns3420', 'role' => 'parent', 'selected' => '##parent_id##',
										'id_in_name' => 'num', 'mapping' => array('name' => 'tekst1')
									)
								)
							),
							array
								(
								'name' => 'num',
								'descr' => lang('num'),
								'type' => 'varchar',
								'nullable' => false,
								'sortable' => true
							),
							array
								(
								'name' => 'tekst1',
								'descr' => 'tekst1',
								'type' => 'varchar'
							),
							array
								(
								'name' => 'tekst2',
								'descr' => 'tekst2',
								'type' => 'varchar'
							),
							array
								(
								'name' => 'tekst3',
								'descr' => 'tekst3',
								'type' => 'varchar'
							),
							array
								(
								'name' => 'tekst4',
								'descr' => 'tekst4',
								'type' => 'varchar'
							),
							array
								(
								'name' => 'tekst5',
								'descr' => 'tekst5',
								'type' => 'varchar'
							),
							array
								(
								'name' => 'tekst6',
								'descr' => 'tekst6',
								'type' => 'varchar'
							)
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('ns3420'),
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::ns3420',
						'check_grant' => false
					);
					break;
				case 'unit':
					$info = array
						(
						'table' => 'fm_standard_unit',
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
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg' => lang('edit unit'),
						'add_msg' => lang('add unit'),
						'name' => lang('unit'),
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::unit'
					);
					break;
				case 'budget_account':
					$info = array
						(
						'table' => 'fm_b_account',
						'id' => array('name' => 'id', 'type' => 'varchar'),
						'fields' => array
							(
							array
								(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar',
								'nullable' => false,
								'size' => 60,
								'sortable' => true
							),
							array
								(
								'name' => 'category',
								'descr' => lang('category'),
								'type' => 'select',
								'nullable' => false,
								'filter' => true,
								'sortable' => true,
								'values_def' => array
									(
									'valueset' => false,
									'method' => 'property.bogeneric.get_list',
									'get_single_value' => 'property.sogeneric.get_name',
									'method_input' => array('type' => 'b_account', 'selected' => '##category##')//b_account_category
								)
							),
							array
								(
								'name' => 'mva',
								'descr' => lang('tax code'),
								'type' => 'int',
								'nullable' => true,
								'size' => 4,
								'sortable' => true
							),
							array
								(
								'name' => 'responsible',
								'descr' => lang('responsible'),
								'type' => 'select',
								'filter' => true,
								'values_def' => array
									(
									'valueset' => false,
									'get_single_value' => 'get_user',
									'method' => 'property.bocommon.get_user_list_right2',
									'method_input' => array('selected' => '##responsible##', 'right' => 128,
										'acl_location' => '.invoice')
								)
							),
							array
								(
								'name' => 'active',
								'descr' => lang('active'),
								'type' => 'checkbox',
								'default' => 'checked',
								'filter' => true,
								'sortable' => true,
								'values_def' => array(
									'valueset' => array(array('id' => 1, 'name' => lang('active'))),
								)
							),
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('budget account'),
						'acl_app' => 'property',
						'acl_location' => '.b_account',
						'menu_selection' => 'property::economy::budget_account',
						'default' => array
							(
							'user_id' => array('add' => '$this->account'),
							'entry_date' => array('add' => 'time()'),
							'modified_date' => array('edit' => 'time()'),
						),
						'check_grant' => false
					);

					break;
				case 'voucher_process_code':
					$info = array
						(
						'table' => 'fm_ecobilag_process_code',
						'id' => array('name' => 'id', 'type' => 'varchar'),
						'fields' => array
							(
							array
								(
								'name' => 'name',
								'descr' => lang('name'),
								'type' => 'varchar'
							),
						),
						'edit_msg' => lang('edit process code'),
						'add_msg' => lang('add process code'),
						'name' => lang('process code'),
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::accounting::process_code',
						'default' => array
							(
							'user_id' => array('add' => '$this->account'),
							'entry_date' => array('add' => 'time()'),
							'modified_date' => array('edit' => 'time()'),
						)
					);
					break;

				case 'org_unit':

					$info = array
						(
						'table' => 'fm_org_unit',
						'id' => array('name' => 'id', 'type' => 'int'),
						'fields' => array(
							array(
								'name' => 'name',
								'descr' => lang('name'),
								'type' => 'varchar',
								'nullable' => false,
								'size' => 60,
								'sortable' => true
							),
							array(
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
									'method' => 'property.bogeneric.get_list',
									'method_input' => array('type' => 'org_unit', 'role' => 'parent', 'selected' => '##parent_id##')
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
						'name' => lang('department'),
						'acl_app' => 'property',
						'acl_location' => '.org_unit',
						'menu_selection' => 'admin::property::accounting::org_unit',
						'default' => array
							(
							'created_by' => array('add' => '$this->account'),
							'created_on' => array('add' => 'time()'),
							'modified_by' => array('edit' => '$this->account'),
							'modified_on' => array('edit' => 'time()'),
						),
						'check_grant' => false
					);
					break;

				case 'eco_service':
					$info = array(
						'table' => 'fm_eco_service',
						'id' => array('name' => 'id', 'type' => 'int'),
						'fields' => array(
							array(
								'name' => 'name',
								'descr' => lang('name'),
								'type' => 'varchar',
								'nullable' => false,
								'size' => 50,
								'sortable' => true
							),
							array(
								'name' => 'active',
								'descr' => lang('active'),
								'type' => 'checkbox',
								'default' => 'checked'
							),
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('service'),
						'acl_app' => 'property',
						'acl_location' => '.b_account',
						'menu_selection' => 'property::economy::eco_service',
						'check_grant' => false
					);
					break;
				//-------- ID type auto

				case 'dimb_role_user':

					$info = array
						(
						'table' => 'fm_ecodimb_role_user',
						'id' => array('name' => 'id', 'type' => 'auto'),
						'fields' => array
							(
							array
								(
								'name' => 'ecodimb',
								'descr' => lang('dim b'),
								'type' => 'select',
								'nullable' => false,
								'filter' => true,
								'sortable' => true,
								'values_def' => array
									(
									'valueset' => false,
									'method' => 'property.bogeneric.get_list',
									'method_input' => array('type' => 'dimb', 'selected' => '##ecodimb##')//b_account_category
								)
							),
							array
								(
								'name' => 'role_id',
								'descr' => lang('role type'),
								'type' => 'select',
								'filter' => true,
								'sortable' => true,
								'values_def' => array
									(
									'valueset' => false,
									'method' => 'property.bogeneric.get_list',
									'get_single_value' => 'property.sogeneric.get_name',
									'method_input' => array('type' => 'dimb_role', 'selected' => '##role_id##')
								)
							),
							array
								(
								'name' => 'user_id',
								'descr' => lang('user'),
								'type' => 'select',
								'filter' => true,
								'values_def' => array
									(
									'valueset' => false,
									'get_single_value' => 'get_user',
									'method' => 'property.bocommon.get_user_list_right2',
									'method_input' => array('selected' => '##user_id##', 'right' => 1, 'acl_location' => '.invoice')
								)
							),
							array
								(
								'name' => 'default_user',
								'descr' => lang('default'),
								'type' => 'checkbox',
								'default' => 'checked'
							),
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('dimb role'),
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::accounting::dimb_role_user',
						'default' => array
							(
							'created_by' => array('add' => '$this->account'),
							'created_on' => array('add' => 'time()'),
						),
						'check_grant' => false
					);
					break;

				case 'order_dim1':
					$info = array
						(
						'table' => 'fm_order_dim1',
						'id' => array('name' => 'id', 'type' => 'auto'),
						'fields' => array
							(
							array
								(
								'name' => 'num',
								'descr' => lang('name'),
								'type' => 'varchar'
							),
							array
								(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar',
								'nullable' => false
							),
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('order_dim1'),
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::order_dim1'
					);
					break;
				case 'branch':
					$info = array
						(
						'table' => 'fm_branch',
						'id' => array('name' => 'id', 'type' => 'auto'),
						'fields' => array
							(
							array
								(
								'name' => 'num',
								'descr' => lang('name'),
								'type' => 'varchar'
							),
							array
								(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('branch'),
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::branch'
					);

					break;
				case 'key_location':
					$info = array
						(
						'table' => 'fm_key_loc',
						'id' => array('name' => 'id', 'type' => 'auto'),
						'fields' => array
							(
							array
								(
								'name' => 'num',
								'descr' => lang('name'),
								'type' => 'varchar'
							),
							array
								(
								'name' => 'descr',
								'descr' => lang('key location'),
								'type' => 'text'
							)
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('branch'),
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::key_location'
					);

					break;

				case 'async':
					$info = array
						(
						'table' => 'fm_async_method',
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
								'name' => 'data',
								'descr' => lang('data'),
								'type' => 'text'
							),
							array
								(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'text'
							)
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('Async services'),
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::async'
					);
					break;

				case 'event_action':
					$info = array
						(
						'table' => 'fm_event_action',
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
								'name' => 'action',
								'descr' => lang('action'),
								'type' => 'varchar'
							),
							array
								(
								'name' => 'data',
								'descr' => lang('data'),
								'type' => 'text'
							),
							array
								(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'text'
							)
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('event action'),
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::event_action',
						'default' => array
							(
							'user_id' => array('add' => '$this->account'),
							'entry_date' => array('add' => 'time()'),
							'modified_date' => array('edit' => 'time()'),
						)
					);

					break;

				case 'ticket_status':

					$info = array
						(
						'table' => 'fm_tts_status',
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
							),
							array
								(
								'name' => 'actual_cost',
								'descr' => lang('mandatory actual cost'),
								'type' => 'checkbox'
							)
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('ticket status'),
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::ticket::ticket_status'
					);
					break;


				case 'regulations':
					$info = array
						(
						'table' => 'fm_regulations',
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
									'method' => 'property.bogeneric.get_list',
									'method_input' => array('type' => 'regulations', 'role' => 'parent', 'selected' => '##parent_id##')
								)
							),
							array
								(
								'name' => 'name',
								'descr' => lang('name'),
								'type' => 'varchar',
								'sortable' => true,
							),
							array
								(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'text'
							),
							array
								(
								'name' => 'external_ref',
								'descr' => lang('external ref'),
								'type' => 'link'
							)
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('regulations'),
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::regulations',
						'default' => array
							(
							'user_id' => array('add' => '$this->account'),
							'entry_date' => array('add' => 'time()'),
							'modified_date' => array('edit' => 'time()'),
						)
					);
					break;

				case 'pending_action_type':
					$info = array
						(
						'table' => 'fm_action_pending_category',
						'id' => array('name' => 'num', 'type' => 'varchar'),
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
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'text'
							)
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('Pending action type'),
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::action_type'
					);

					break;

				case 'order_template':

					$info = array
						(
						'table' => 'fm_order_template',
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
						'name' => lang('order template'),
						'acl_app' => 'property',
						'acl_location' => '.ticket.order',
						'menu_selection' => 'property::helpdesk::order_template',
						'default' => array
							(
							'user_id' => array('add' => '$this->account'),
							'entry_date' => array('add' => 'time()'),
							'modified_date' => array('edit' => 'time()'),
						),
						'check_grant' => true
					);

					break;
				case 'response_template':

					$info = array
						(
						'table' => 'fm_response_template',
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
						'system_location' => '.ticket.response_template',
						'menu_selection' => 'property::helpdesk::response_template',
						'default' => array
							(
							'user_id' => array('add' => '$this->account'),
							'entry_date' => array('add' => 'time()'),
							'modified_date' => array('edit' => 'time()'),
						),
						'check_grant' => true
					);

					break;

				case 'responsibility_role':

					$info = array
						(
						'table' => 'fm_responsibility_role',
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
								'name' => 'remark',
								'descr' => lang('remark'),
								'type' => 'text'
							),
							array(
								'name' => 'location_level',
								'descr' => lang('location level'),
								'type' => 'varchar'
							),
							array
								(
								'name' => 'responsibility_id',
								'descr' => lang('responsibility'),
								'type' => 'select',
								'values_def' => array
									(
									'valueset' => false,
									'get_single_value' => 'property.soresponsible.get_responsibility_name',
									'method' => 'property.boresponsible.get_responsibilities',
									'method_input' => array('appname' => '$this->appname', 'selected' => '##responsibility_id##')
								)
							)
						),
						'edit_action' => 'property.uiresponsible.edit_role',
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('responsibility role'),
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::responsibility_role',
						'default' => array
							(
							'appname' => array('add' => '$this->appname'),
							'user_id' => array('add' => '$this->account'),
							'entry_date' => array('add' => 'time()'),
							'modified_date' => array('edit' => 'time()'),
						),
						'check_grant' => false,
						'filter' => array('appname' => '$this->appname')
					);

					break;

				case 'custom_menu_items':
					$info = array
						(
						'table' => 'fm_custom_menu_items',
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
									'method' => 'property.bogeneric.get_list',
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
									'method_input' => array('acl_app' => 'property', 'selected' => '##location##')
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
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::custom_menu_items',
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
				case 'location_contact':
					$info = array
						(
						'table' => 'fm_location_contact',
						'id' => array('name' => 'id', 'type' => 'auto'),
						'fields' => array
							(
							array
								(
								'name' => 'contact_id',
								'descr' => lang('contact'),
								'type' => 'int', //contact
								'nullable' => false,
							),
							array
								(
								'name' => 'location_code',
								'descr' => lang('location_code'),
								'type' => 'varchar', //location
								'nullable' => false,
							)
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('location contact'),
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::location::location_contact',
						'default' => array
							(
							'user_id' => array('add' => '$this->account'),
							'entry_date' => array('add' => 'time()'),
							'modified_date' => array('edit' => 'time()'),
						)
					);

					break;

				case 'periodization_outline':
					$valueset_month = array();

					for ($i = 1; $i < 13; $i++)
					{
						$valueset_month[] = array
							(
							'id' => $i,
							'name' => $i
						);
					}

					$info = array
						(
						'table' => 'fm_eco_periodization_outline',
						'id' => array('name' => 'id', 'type' => 'auto'),
						'fields' => array
							(
							array
								(
								'name' => 'periodization_id',
								'descr' => lang('periodization'),
								'type' => 'select',
								'nullable' => false,
								'filter' => true,
								'sortable' => true,
								'values_def' => array
									(
									'valueset' => false,
									'method' => 'property.bogeneric.get_list',
									'get_single_value' => 'property.sogeneric.get_name',
									'method_input' => array('type' => 'periodization', 'selected' => '##periodization_id##')
								)
							),
							array
								(
								'name' => 'month',
								'descr' => lang('month'),
								'type' => 'select',
								'nullable' => false,
								'filter' => true,
								'sortable' => true,
								'values_def' => array
									(
									'valueset' => $valueset_month,
								)
							),
							array
								(
								'name' => 'value',
								'descr' => lang('value'),
								'type' => 'numeric',
								'nullable' => true,
								'size' => 4,
								'sortable' => true
							),
							array
								(
								'name' => 'dividend',
								'descr' => lang('fraction::dividend'),
								'type' => 'integer',
								'nullable' => true,
								'size' => 4,
								'sortable' => true
							),
							array
								(
								'name' => 'divisor',
								'descr' => lang('fraction::divisor'),
								'type' => 'integer',
								'nullable' => true,
								'size' => 4,
								'sortable' => true
							),
							array
								(
								'name' => 'remark',
								'descr' => lang('remark'),
								'type' => 'varchar',
								'nullable' => false,
								'size' => 60,
								'sortable' => true
							)
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('periodization'),
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::accounting::periodization_outline'
					);

					break;

				case 'period_transition':
					$valueset_month = array();
					$valueset_day = array();
					$valueset_hour = array();

					$lang_default = lang('default');
					for ($i = 1; $i < 14; $i++)
					{
						$valueset_month[] = array
							(
							'id' => $i,
							'name' => $i == 13 ? "{$i} ({$lang_default})" : $i
						);
					}

					for ($i = 1; $i < 32; $i++)
					{
						$valueset_day[] = array
							(
							'id' => $i,
							'name' => $i
						);
					}

					for ($i = 1; $i < 25; $i++)
					{
						$valueset_hour[] = array
							(
							'id' => $i,
							'name' => $i
						);
					}

					$info = array
						(
						'table' => 'fm_eco_period_transition',
						'id' => array('name' => 'id', 'type' => 'auto'),
						'fields' => array
							(
							array
								(
								'name' => 'month',
								'descr' => lang('month'),
								'type' => 'select',
								'nullable' => false,
								'filter' => true,
								'sortable' => true,
								'values_def' => array
									(
									'valueset' => $valueset_month,
								)
							),
							array
								(
								'name' => 'day',
								'descr' => lang('day'),
								'type' => 'select',
								'nullable' => false,
								'size' => 4,
								'sortable' => true,
								'values_def' => array
									(
									'valueset' => $valueset_day,
								)
							),
							array
								(
								'name' => 'hour',
								'descr' => lang('hour'),
								'type' => 'select',
								'nullable' => true,
								'size' => 4,
								'sortable' => true,
								'values_def' => array
									(
									'valueset' => $valueset_hour,
								)
							),
							array
								(
								'name' => 'remark',
								'descr' => lang('remark'),
								'type' => 'varchar',
								'nullable' => true,
								'size' => 60,
								'sortable' => true
							)
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('period transition'),
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::accounting::period_transition',
						'default' => array
							(
							'user_id' => array('add' => '$this->account'),
							'entry_date' => array('add' => 'time()'),
							'modified_date' => array('edit' => 'time()'),
						)
					);

					break;

				case 'entity_group':
					$info = array
						(
						'table' => 'fm_entity_group',
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
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							),
							array
								(
								'name' => 'active',
								'descr' => lang('active'),
								'type' => 'checkbox',
								'default' => 'checked'
							)
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('entity group'),
						'acl_app' => 'property',
						'acl_location' => '.admin.entity',
						'menu_selection' => 'admin::property::entity::entity_group',
						'default' => array
							(
							'user_id' => array('add' => '$this->account'),
							'entry_date' => array('add' => 'time()'),
							'modified_date' => array('add' => 'time()', 'edit' => 'time()'),
						),
						'check_grant' => false
					);

					break;

				case 'location_exception_severity':
					$info = array
						(
						'table' => 'fm_location_exception_severity',
						'id' => array('name' => 'id', 'type' => 'int', 'descr' => lang('id')),
						'fields' => array
							(
							array
							(
								'name' => 'name',
								'descr' => lang('name'),
								'type' => 'varchar',
								'nullable' => false
							)
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('severity'),
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::location::location_exception::severity'
					);
					break;
				case 'location_exception_category':
					$info = array
						(
						'table' => 'fm_location_exception_category',
						'id' => array('name' => 'id', 'type' => 'auto'),
						'fields' => array
						(
							array(
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
									'method' => 'property.bogeneric.get_list',
									'method_input' => array('type' => 'location_exception_category', 'role' => 'parent', 'selected' => '##parent_id##')
								)
							),
							array
							(
								'name' => 'name',
								'descr' => lang('name'),
								'type' => 'varchar',
								'nullable' => false
							)
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('severity category'),
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::location::location_exception::category'
					);
					break;

				case 'location_exception_category_text':
					$info = array
						(
						'table' => 'fm_location_exception_category_text',
						'id' => array('name' => 'id', 'type' => 'auto'),
						'fields' => array
						(
							array
							(
								'name' => 'content',
								'descr' => lang('content'),
								'type' => 'varchar',
								'nullable' => false
							),
							array
							(
								'name' => 'category_id',
								'descr' => lang('category'),
								'type' => 'select',
								'nullable' => false,
								'filter' => true,
								'sortable' => true,
								'values_def' => array
								(
									'valueset' => false,
									'method' => 'property.bogeneric.get_list',
									'get_single_value' => 'property.sogeneric.get_name',
									'method_input' => array('type' => 'location_exception_category', 'selected' => '##category_id##', 'role' => 'parent')
								)
							)
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('severity category text'),
						'acl_app' => 'property',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::location::location_exception::category::category_text'
					);
					break;

				case 'location_exception':
					$info = array
						(
						'table' => 'fm_location_exception',
						'id' => array('name' => 'id', 'type' => 'auto'),
						'fields' => array
						(
							array
							(
								'name' => 'location_code',
								'descr' => lang('location'),
								'type' => 'location',
								'nullable' => false
							),
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'text',
								'nullable' => true
							),
							array
							(
								'name' => 'start_date',
								'descr' => lang('start date'),
								'type' => 'date',
								'nullable' => false
							),
							array
							(
								'name' => 'end_date',
								'descr' => lang('end date'),
								'type' => 'date',
								'nullable' => true
							),
							array
							(
								'name' => 'reference',
								'descr' => lang('reference'),
								'type' => 'text',
								'nullable' => true
							),
							array
							(
								'name' => 'severity_id',
								'descr' => lang('severity'),
								'type' => 'select',
								'nullable' => false,
								'filter' => true,
								'sortable' => true,
								'values_def' => array
								(
									'valueset' => false,
									'method' => 'property.bogeneric.get_list',
									'get_single_value' => 'property.sogeneric.get_name',
									'method_input' => array('type' => 'location_exception_severity', 'selected' => '##severity_id##')
								)
							),
							array
							(
								'name' => 'category_id',
								'descr' => lang('category'),
								'type' => 'select',
								'nullable' => false,
								'filter' => true,
								'sortable' => true,
								'values_def' => array
								(
									'valueset' => false,
									'method' => 'property.bogeneric.get_list',
									'get_single_value' => 'property.sogeneric.get_name',
									'method_input' => array('role' => 'parent', 'type' => 'location_exception_category', 'selected' => '##category_id##')
								)
							),
							array
							(
								'name' => 'category_text_id',
								'descr' => lang('category content'),
								'type' => 'select',
								'nullable' => true,
								'filter' => false,
								'sortable' => true,
								'js_file'	=> 'location_exception_category_text.edit.js',
								'values_def' => array
								(
									'valueset' => false,
									'method' => 'property.bogeneric.get_list',
									'get_single_value' => 'property.sogeneric.get_name',
									'method_input' => array(
										'type' => 'location_exception_category_text',
										'selected' => '##category_text_id##',
										'mapping' => array('name' => 'content'),
										'filter'	=> array('category_id' => '##category_id##'),
										)
								)
							),
							array
								(
								'name' => 'alert_vendor',
								'descr' => lang('alert vendor'),
								'type' => 'checkbox'
							),
						),
						'edit_msg' => lang('edit'),
						'add_msg' => lang('add'),
						'name' => lang('severity category text'),
						'acl_app' => 'property',
						'acl_location' => '.location',
						'menu_selection' => 'admin::property::location::location_exception::category::category_text',
						'default' => array
						(
							'user_id' => array('add' => '$this->account'),
							'entry_date' => array('add' => 'time()'),
							'modified_date' => array('add' => 'time()', 'edit' => 'time()'),
						),
					);
					break;

// START CONTROLLER TABLES
				case 'controller_check_item_status':
					$info = array
						(
						'table' => 'controller_check_item_status',
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
								'name' => 'open',
								'descr' => lang('open'),
								'type' => 'checkbox'
							),
							array
								(
								'name' => 'pending',
								'descr' => lang('pending'),
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
						'name' => lang('status'),
						'acl_app' => 'controller',
						'acl_location' => 'admin',
						'menu_selection' => 'admin::controller::check_item_status'
					);
					break;

// END CONTROLLER TABLES

				default:
					$message = lang('ERROR: illegal type %1', $type);
					phpgwapi_cache::message_set($message, 'error');
//				throw new Exception(lang('ERROR: illegal type %1', $type));
			}

			$this->location_info = $info;
			return $info;
		}

	}