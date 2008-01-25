<?php
	/**
	 * property - Menus
	 *
	 * @author Dave Hall <skwashd@phpgroupware.org>
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2007 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package property
	 * @version $Id$
	 */

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 3 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */


	/**
	 * Menus
	 *
	 * @package property
	 */
	class property_menu
	{
		/**
		 * Get the menus for the property
		 *
		 * @return array available menus for the current user
		 */
		public function get_menu()
		{
			$acl = CreateObject('phpgwapi.acl');
			$menus = array();

			$entity			= CreateObject('property.soadmin_entity');
			$entity_list 	= $entity->read(array('allrows' => true));

			$start_page = 'location';
			if ( isset($GLOBALS['phpgw_info']['user']['preferences']['property']['default_start_page'])
					&& $GLOBALS['phpgw_info']['user']['preferences']['property']['default_start_page'] )
			{
					$start_page = $GLOBALS['phpgw_info']['user']['preferences']['property']['default_start_page'];
			}

			$menus['navbar'] = array
			(
				'property' => array
				(
					'text'	=> $GLOBALS['phpgw']->translations->translate('property', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => "property.ui{$start_page}.index") ),
					'image'	=> array('property', 'navbar'),
					'order'	=> 35,
					'group'	=> 'facilities management'
				),
			);

			$menus['toolbar'] = array();

			$soadmin_location	= CreateObject('property.soadmin_location');
			$locations	= $soadmin_location->select_location_type();

			if ( isset($GLOBALS['phpgw_info']['user']['apps']['admin']) )
			{
				if ( is_array($entity_list) && count($entity_list) )
				{
					foreach($entity_list as $entry)
					{
						$admin_children_entity["entity_{$entry['id']}"] = array
						(
							'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.category', 'entity_id'=> $entry['id'])),
							'text'	=> $entry['name']
						);
			
						$cat_list = $entity->read_category(array('allrows'=>True,'entity_id'=>$entry['id']));

						foreach($cat_list as $category)
						{
							$admin_children_entity["entity_{$entry['id']}"]['children']["entity_{$entry['id']}_{$category['id']}"]	= array
							(
								'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.list_attribute', 'entity_id'=> $entry['id'] , 'cat_id'=> $category['id'])),
								'text'	=> $category['name']
							);
						}
					}
				}

				$admin_children_tenant = array
				(
					'tenant_cats'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Tenant Categories', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'tenant') )
					),
					'tenant_global_cats'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Tenant Global Categories', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicategories.index', 'appname' => 'fm_tenant', 'global_cats' => 'True') )
					),
					'tenant_attribs'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Tenant Attributes', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_attribute', 'appname' => 'property', 'location' =>'.tenant'))
					),
					'claims_cats'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Tenant Claim Categories', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'tenant_claim') )
					)
				);

				$admin_children_vendor = array
				(
					'vendor'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Vendor', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiactor.index', 'role' => 'vendor') )
					),
					'vendor_cats'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Vendor Categories', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'vendor') )
					),
					'vendor_global_cats'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Vendor Global Categories', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicategories.index', 'appname' => 'fm_vendor', 'global_cats' => 'True') )
					),
					'vendor_attribs'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Vendor Attributes', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_attribute', 'appname' => 'property', 'location' => '.vendor'))
					)		
				);
				$admin_children_owner = array
				(
					'owner'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Owner', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiactor.index', 'role' => 'owner') )
					),
					'owner_cats'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Owner Categories', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'owner') )
					),
					'owner_attribs'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Owner Attributes', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_attribute', 'appname' => 'property', 'location' =>'.owner'))
					)
				);

				$admin_children_accounting = array
				(
					'accounting_cats'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Accounting Categories', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'b_account') )
					),
					'accounting_dim_b'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Accounting dim b', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'dim_b') )
					),
					'accounting_dim_d'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Accounting dim d', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'dim_d') )
					),
					'accounting_tax'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Accounting tax', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'tax') )
					),
					'voucher_cat'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Accounting voucher category', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'voucher_cat') )
					),
					'voucher_type'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Accounting voucher type', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'voucher_type') )
					),
					'import'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Import', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiXport.import') )
					),
					'export'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Export', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiXport.export') )
					)
				);

				$admin_children_agreement = array
				(
					'agreement_status'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Agreement status', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uistandard_2.index', 'type' => 'agreement_status') )
					),
					'agreement_attribs'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Agreement Attributes', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_attribute', 'appname' => 'property', 'location' =>'.agreement'))
					),
					'service_agree_cats'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('service agreement categories', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 's_agreement') )
					),
					'service_agree_attribs'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('service agreement Attributes', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_attribute', 'appname' => 'property', 'location' =>'.s_agreement'))
					),
					'service_agree_item_attribs'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('service agreement item Attributes', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_attribute', 'appname' => 'property', 'location' =>'.s_agreement.detail'))
					),
					'rental_agree_cats'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('rental agreement categories', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'r_agreement') )
					),
					'rental_agree_attribs'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('rental agreement Attributes', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_attribute', 'appname' => 'property', 'location' =>'.r_agreement'))
					),
					'rental_agree_item_attribs'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('rental agreement item Attributes', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_attribute', 'appname' => 'property', 'location' =>'.r_agreement.detail'))
					),

				);

				foreach ( $locations as $location )
				{
					$admin_children_location_children["attribute_loc_{$location['id']}"] = array
					(
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_location.list_attribute', 'type_id' => $location['id'])),
						'text'	=> $location['name'] . ' ' . $GLOBALS['phpgw']->translations->translate('attributes', array(), true),
					);
					$admin_children_location_children["category_loc_{$location['id']}"] = array
					(
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uicategory.index', 'type' => 'location', 'type_id' => $location['id'])),
						'text'	=> $location['name'] . ' ' . $GLOBALS['phpgw']->translations->translate('categories', array(), true),
					);	
				}

				$admin_children_location = array
				(
					'street'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Street', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'street') )
					),
					'district'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('District', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'district') )
					),
					'town'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Part of town', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uip_of_town.index') )
					),
					'location' => array
					(
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_location.index') ),
						'text'	=> $GLOBALS['phpgw']->translations->translate('Location type', array(), true),
						'children'	=> $admin_children_location_children
					),
					'config' => array
					(
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_location.config') ),
						'text'	=> lang('Config')
					)
				);


				$menus['admin'] = array
				(
					'index'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Configuration', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiconfig.index', 'appname' => 'property') )
					),
					'entity'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Admin entity', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiadmin_entity.index') ),
						'children' => $admin_children_entity
					),
					'location'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Admin Location', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiadmin_location.index') ),
						'children' => $admin_children_location
					),
					'inactive_cats'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Update the not active category for locations', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uilocation.update_cat') )
					),
					'request_cats'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Request Categories', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'request') )
					),
					'workorder_cats'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Workorder Categories', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'wo') )
					),
					'workorder_detail'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Workorder Detail Categories', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'wo_hours') )
					),
					'ticket_cats'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Ticket Categories', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'ticket') )
					),
					'tenant'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Tenant', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiactor.index', 'role' => 'tenant') ),
						'children'	=> $admin_children_tenant
					),
					'owner'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Owner', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiactor.index', 'role' => 'owner') ),
						'children'	=> $admin_children_owner
					),
					'vendor'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Vendor', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiactor.index', 'role' => 'vendor') ),
						'children'	=> $admin_children_vendor
					),
					'doc_cats'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Document Categories', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'document') )
					),
					'building_part'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Building Part', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uistandard_2.index', 'type' => 'building_part') )
					),
					'tender'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Tender chapter', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'tender_chapter') )
					),
					'id_control'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('ID Control', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiadmin.edit_id') )
					),
					'permissions'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Permissions', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiadmin.list_acl') )
					),
					'user_contact'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('User contact info', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiadmin.contact_info') )
					),
					'request_status'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Request status', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uistandard_2.index', 'type' => 'request_status') )
					),
					'request_condition'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Request condition_type', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'r_condition_type') )
					),
					'workorder_status'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Workorders status', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uistandard_2.index', 'type' => 'workorder_status') )
					),
					'agreement_status'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Agreement', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uistandard_2.index', 'type' => 'agreement_status') ),
						'children'	=> $admin_children_agreement
					),
					'document_status'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Document Status', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uistandard_2.index', 'type' => 'document_status') )
					),
					'unit'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Unit', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uistandard_2.index', 'type' => 'unit') )
					),
					'key_loc'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Key location', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uistandard_3.index', 'type' => 'key_location') )
					),
					'branch'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Branch', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uistandard_3.index', 'type' => 'branch') )
					),
					'accounting'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Accounting', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uib_account.index') ),
						'children'	=> $admin_children_accounting
					),
					'admin_async'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Admin Async services', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uialarm.index') )
					),
					'async'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Async services', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiasync.index') )
					),
					'cust_func'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Admin custom functions', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiadmin_custom.index') )
					),
				);
			}

			if ( isset($GLOBALS['phpgw_info']['user']['apps']['preferences']) )
			{
				$menus['preferences'] = array
				(
					array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Preferences', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname' => 'property', 'type'=> 'user') )
					),
					array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Grant Access', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiadmin.aclprefs', 'acl_app'=> 'property'))
					)
				);

				$menus['toolbar'][] = array
				(
					'text'	=> $GLOBALS['phpgw']->translations->translate('Preferences', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname'	=> 'property')),
					'image'	=> array('property', 'preferences')
				);
			}

			$menus['navigation'] = array();
			if ( $acl->check('.location', PHPGW_ACL_READ, 'property') )
			{
				$children = array();

		//		$soadmin_location	= CreateObject('property.soadmin_location');
		//		$locations	= $soadmin_location->select_location_type();
				foreach ( $locations as $location )
				{
					$children["loc_{$location['id']}"] = array
					(
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilocation.index', 'type_id' => $location['id'])),
						'text'	=> $location['name']
					);
				}

				$children['tenant'] = array
				(
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilocation.index', 'lookup_tenant' => 1, 'type_id' => $soadmin_location->read_config_single('tenant_id'))),
					'text'	=> lang('Tenant')
				);
				$children['gabnr'] = array
				(
					'url'	=>	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uigab.index')),
					'text'	=> lang('gabnr')
				);
				$children['summary'] = array
				(
					'url'	=>	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilocation.summary')),
					'text'	=>	lang('Summary')
				);

/*				if ( $acl->check('.location',16) )
				{
					$children['type'] = array
					(
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_location.index')),
						'text'	=> lang('Location type')
					);
					$children['config'] = array
					(
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_location.config')),
						'text'	=> lang('Config')
					);
				}
*/
				$menus['navigation']['location'] = array
				(
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilocation.index', 'type_id'=>1)),
					'text'	=> $GLOBALS['phpgw']->translations->translate('Location', array(), true),
					'image'	=> array('property', 'location'),
					'children'	=> $children
				);
			}

			if ( $acl->check('.ifc', PHPGW_ACL_READ, 'property') )
			{
				$menus['navigation']['ifc'] = array
				(
					'url'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiifc.import')),
					'text'		=> $GLOBALS['phpgw']->translations->translate('IFC', array(), true),
					'image'		=> array('property', 'ifc'),
					'children'	=> array
					(
						'import'	=> array
						(
							'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiifc.import')),
							'text'	=> lang('import')
						)
					)
				);
			}

			if ( $acl->check('.ticket',PHPGW_ACL_READ, 'property') )
			{
				$menus['navigation']['helpdesk'] = array
				(
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitts.index')),
					'text'	=> lang('Helpdesk')
				);
			}

			if ( $acl->check('.project', PHPGW_ACL_READ, 'property') )
			{
				$menus['navigation']['project'] = array
				(
					'url'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiproject.index')),
					'text'		=> $GLOBALS['phpgw']->translations->translate('Project', array(), true),
					'children'	=> array
					(
						'project'	=> array
						(
							'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiproject.index')),
							'text'	=> lang('Project')
						),
						'workorder'	=> array
						(
							'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.index')),
							'text'	=> lang('Workorder')
						),
						'request'	=> array
						(
							'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uirequest.index')),
							'text'	=> lang('Request')
						),
						'template'	=> array
						(
							'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitemplate.index')),
							'text'	=> lang('template')
						),
						'claim'		=> array
						(
							'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitenant_claim.index')),
							'text'	=> lang('Tenant claim')
						)
					)
				);
			}

			if ( $acl->check('.invoice', PHPGW_ACL_READ, 'property') )
			{
				$children = array();
				if ( $acl->check('.invoice', PHPGW_ACL_PRIVATE, 'property') )
				{
					$children['investment'] = array
					(
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiinvestment.index')),
						'text'	=>	lang('Investment value')
					);

					$children['import'] = array
					(
						'url'	=>	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiXport.import')),
						'text'	=> lang('Import invoice')
					);

					$children['export'] = array
					(
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiXport.export')),
						'text'	=>	lang('Export invoice')
					);
				}

				if ( $acl->check('.invoice', PHPGW_ACL_ADD, 'property') )
				{
					$children['add'] = array
					(
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiinvoice.add')),
						'text'	=>	lang('Add')
					);
				}

				$menus['navigation']['invoice'] = array
				(
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiinvoice.index')),
					'text'	=> $GLOBALS['phpgw']->translations->translate('Invoice', array(), true),
					'image'	=> array('property', 'invoice'),
					'children'	=> array_merge(array
					(
						'paid'		=> array
						(
							'url'	=>	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiinvoice.index', 'paid'=>true)),
							'text'	=> lang('Paid')
						),
						// Should this be process? skwashd jan08
						'consume'	=> array
						(
							'url'	=>	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiinvoice.consume')),
							'text'	=> lang('consume')
						),
						'budget'	=> array
						(
							'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uib_account.index')),
							'text'	=> lang('Budget account')
						),
						'vendor'	=> array
						(
							'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiactor.index', 'role'=> 'vendor')),
							'text'	=> lang('Vendor')
						),
						'tenant'	=> array
						(
							'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiactor.index', 'role'=> 'tenant')),
							'text'	=> lang('Tenant')
						)
					), $children)
				);
			}

			if ( $acl->check('.budget', PHPGW_ACL_READ, 'property') )
			{
				$menus['navigation']['budget'] = array
				(
					'url'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uibudget.index')),
					'text'		=> $GLOBALS['phpgw']->translations->translate('Budget', array(), true),
					'children'	=> array
					(
						'basis'		=> array
						(
							'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uibudget.basis')),
							'text'	=> lang('basis')
						),
						'budget'	=> array
						(
							'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uibudget.index')),
							'text'	=> lang('budget')
						),
						'obligations'	=> array
						(
							'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uibudget.obligations')),
							'text'	=> lang('obligations')
						)
					)
				);
			}

			if ( $acl->check('.agreement', PHPGW_ACL_READ, 'property') )
			{
				$admin_menu = array();
				if ( $acl->check('.agreement',16) )
				{
					$admin_menu = array
					(
						'group'		=> array
						(
							'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uipricebook.agreement_group')),
							'text'	=> lang('Agreement group')
						),
						'activities'	=> array
						(
							'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uipricebook.activity')),
							'text'	=> lang('Activities')
						),
						'agreement'		=> array
						(
							'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiagreement.index')),
							'text'	=> lang('Agreement')
						)
					);
				}

				$menus['navigation']['agreement'] = array
				(
					'url'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiagreement.index')),
					'text'		=> $GLOBALS['phpgw']->translations->translate('Agreement', array(), true),
					'children'	=> array
					(
						'pricebook'	=> array
						(
							'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiagreement.index')),
							'text'	=> $GLOBALS['phpgw']->translations->translate('Pricebook', array(), true),
							'children'	=> $admin_menu
						),
						'service'	=> array
						(
							'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uis_agreement.index')),
							'text'	=> lang('Service')
						),
						'rental'	=> array
						(
							'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uir_agreement.index')),
							'text'	=> lang('Rental')
						),
						'alarm'		=> array
						(
							'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uialarm.list_alarm')),
							'text'	=> lang('alarm')
						)
					)
				);
			}

			if ( $acl->check('.document', PHPGW_ACL_READ, 'property') )
			{
				$menus['navigation']['documentation'] = array
				(
					'url'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uidocument.index')),
					'text'		=> $GLOBALS['phpgw']->translations->translate('Documentation', array(), true),
					'children'	=> array
					(
						'location'	=> array
						(
							'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uidocument.index')),
							'text'	=> lang('location')
						)
					)
				);
				if (is_array($entity_list) && count($entity_list) )
				{
					foreach ( $entity_list as $entry )
					{
						if($entry['documentation'])
						{
							$menus['navigation']['documentation']['children']["entity_{$entry['id']}"] = array
							(
								'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'property.uidocument.index', 'entity_id' => $entry['id'])),
								'text'	=> $entry['name']
							);
						}
					}
				}
			}

			if ( $acl->check('.custom', PHPGW_ACL_READ, 'property') )
			{
				$menus['navigation']['custom'] = array
				(
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uicustom.index')),
					'text'	=> $GLOBALS['phpgw']->translations->translate('Custom', array(), true),
				);
			}

			if ( is_array($entity_list) && count($entity_list) )
			{
				foreach($entity_list as $entry)
				{
					if ( $acl->check(".entity.{$entry['id']}", PHPGW_ACL_READ, 'property') )
					{
						$menus['navigation']["entity_{$entry['id']}"] = array
						(
							'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uientity.index', 'entity_id'=> $entry['id'])),
							'text'	=> $entry['name']
						);
					}
					
					$cat_list = $entity->read_category(array('allrows'=>True,'entity_id'=>$entry['id']));

					foreach($cat_list as $category)
					{
						if ( $acl->check(".entity.{$entry['id']}.{$category['id']}", PHPGW_ACL_READ, 'property') )
						{
							$menus['navigation']["entity_{$entry['id']}"]['children']["entity_{$entry['id']}_{$category['id']}"]	= array
							(
								'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uientity.index', 'entity_id'=> $entry['id'] , 'cat_id'=> $category['id'])),
								'text'	=> $category['name']
							);
						}
					}
				}
			}
			unset($entity_list);
			unset($entity);
			return $menus;
		}
	}
