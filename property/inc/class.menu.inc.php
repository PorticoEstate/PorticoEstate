<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
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
	* @subpackage core
 	* @version $Id: class.menu.inc.php 18358 2007-11-27 04:43:37Z skwashd $
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_menu
	{
		var $sub;
		var $query;

		var $public_functions = array
		(
			'links'	=> True,
		);

		function property_menu($sub='')
		{
			$GLOBALS['phpgw_info']['flags']['current_location'] = 'property';
			$this->sub		= $sub;
		//	$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->query	= phpgw::get_var('query');

			if($GLOBALS['phpgw_info']['user']['preferences']['common']['template_set'] == 'newdesign')
			{
				$start_page = 'location';
				if ( isset($GLOBALS['phpgw_info']['user']['preferences']['property']['default_start_page'])
						&& $GLOBALS['phpgw_info']['user']['preferences']['property']['default_start_page'] )
				{
						$start_page = $GLOBALS['phpgw_info']['user']['preferences']['property']['default_start_page'];
				}

				$this->newmenus['navbar'] = array
				(
					'property' => array
					(
						'text'	=> lang('property'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => "property.ui{$start_page}.index") ),
						'image'	=> array('property', 'navbar'),
						'order'	=> 35,
						'group'	=> 'facilities management'
					),
				);

				$this->newmenus['toolbar'] = array();

				if ( isset($GLOBALS['phpgw_info']['user']['apps']['admin']) )
				{
					$this->newmenus['admin'] = array
					(
						array
						(
							'text'	=> lang('Configuration'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiconfig.index', 'appname' => 'property') )
						),
						array
						(
							'text'	=> lang('Street'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'street') )
						),
						array
						(
							'text'	=> lang('District'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'district') )
						),
						array
						(
							'text'	=> lang('Part of town'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uip_of_town.index') )
						),
						array
						(
							'text'	=> lang('Admin entity'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiadmin_entity.index') )
						),
						array
						(
							'text'	=> lang('Admin Location'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiadmin_location.index') )
						),
						array
						(
							'text'	=> lang('Update the not active category for locations'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uilocation.update_cat') )
						),
						array
						(
							'text'	=> lang('Request Categories'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'request') )
						),
						array
						(
							'text'	=> lang('Workorder Categories'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'wo') )
						),
						array
						(
							'text'	=> lang('Workorder Detail Categories'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'wo_hours') )
						),
						array
						(
							'text'	=> lang('Ticket Categories'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'ticket') )
						),
						array
						(
							'text'	=> lang('Tenant Claim Categories'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'tenant_claim') )
						),
						array
						(
							'text'	=> lang('Tenant Categories'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'tenant') )
						),
						array
						(
							'text'	=> lang('Tenant Global Categories'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicategories.index', 'appname' => 'fm_tenant', 'global_cats' => 'True') )
						),
						array
						(
							'text'	=> lang('Tenant Attributes'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_attribute', 'appname' => 'property', 'location' =>'.tenant'))
						),
						array
						(
							'text'	=> lang('Tenant'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiactor.index', 'role' => 'tenant') )
						),
						array
						(
							'text'	=> lang('Owner'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiactor.index', 'role' => 'owner') )
						),
						array
						(
							'text'	=> lang('Owner Categories'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'owner') )
						),
						array
						(
							'text'	=> lang('Owner Attributes'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_attribute', 'appname' => 'property', 'location' =>'.owner'))
						),
						array
						(
							'text'	=> lang('Vendor'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiactor.index', 'role' => 'vendor') )
						),
						array
						(
							'text'	=> lang('Vendor Categories'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'vendor') )
						),
						array
						(
							'text'	=> lang('Vendor Global Categories'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicategories.index', 'appname' => 'fm_vendor', 'global_cats' => 'True') )
						),
						array
						(
							'text'	=> lang('Vendor Attributes'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_attribute', 'appname' => 'property', 'location' => '.vendor'))
						),
						array
						(
							'text'	=> lang('Document Categories'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'document') )
						),
						array
						(
							'text'	=> lang('Building Part'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uistandard_2.index', 'type' => 'building_part') )
						),
						array
						(
							'text'	=> lang('Tender chapter'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'tender_chapter') )
						),
						array
						(
							'text'	=> lang('ID Control'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiadmin.edit_id') )
						),
						array
						(
							'text'	=> lang('Permissions'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiadmin.list_acl') )
						),
						array
						(
							'text'	=> lang('User contact info'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiadmin.contact_info') )
						),
						array
						(
							'text'	=> lang('Request status'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uistandard_2.index', 'type' => 'request_status') )
						),
						array
						(
							'text'	=> lang('Request condition_type'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'r_condition_type') )
						),
						array
						(
							'text'	=> lang('Workorders status'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uistandard_2.index', 'type' => 'workorder_status') )
						),
						array
						(
							'text'	=> lang('Agreement status'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uistandard_2.index', 'type' => 'agreement_status') )
						),
						array
						(
							'text'	=> lang('Agreement Attributes'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_attribute', 'appname' => 'property', 'location' =>'.agreement'))
						),
						array
						(
							'text'	=> lang('service agreement categories'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 's_agreement') )
						),
						array
						(
							'text'	=> lang('service agreement Attributes'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_attribute', 'appname' => 'property', 'location' =>'.s_agreement'))
						),
						array
						(
							'text'	=> lang('service agreement item Attributes'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_attribute', 'appname' => 'property', 'location' =>'.s_agreement.detail'))
						),
						array
						(
							'text'	=> lang('rental agreement categories'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'r_agreement') )
						),
						array
						(
							'text'	=> lang('rental agreement Attributes'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_attribute', 'appname' => 'property', 'location' =>'.r_agreement'))
						),
						array
						(
							'text'	=> lang('rental agreement item Attributes'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_attribute', 'appname' => 'property', 'location' =>'.r_agreement.detail'))
						),
						array
						(
							'text'	=> lang('Document Status'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uistandard_2.index', 'type' => 'document_status') )
						),
						array
						(
							'text'	=> lang('Unit'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uistandard_2.index', 'type' => 'unit') )
						),
						array
						(
							'text'	=> lang('Key location'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uistandard_3.index', 'type' => 'key_location') )
						),
						array
						(
							'text'	=> lang('Branch'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uistandard_3.index', 'type' => 'branch') )
						),
						array
						(
							'text'	=> lang('Accounting'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uib_account.index') )
						),
						array
						(
							'text'	=> lang('Accounting Categories'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'b_account') )
						),
						array
						(
							'text'	=> lang('Accounting dim b'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'dim_b') )
						),
						array
						(
							'text'	=> lang('Accounting dim d'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'dim_d') )
						),
						array
						(
							'text'	=> lang('Accounting tax'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'tax') )
						),
						array
						(
							'text'	=> lang('Accounting voucher category'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'voucher_cat') )
						),
						array
						(
							'text'	=> lang('Accounting voucher type'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicategory.index', 'type' => 'voucher_type') )
						),
						array
						(
							'text'	=> lang('Import'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiXport.import') )
						),
						array
						(
							'text'	=> lang('Export'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiXport.export') )
						),
						array
						(
							'text'	=> lang('Admin Async servises'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uialarm.index') )
						),
						array
						(
							'text'	=> lang('Async servises'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiasync.index') )
						),
						array
						(
							'text'	=> lang('Admin custom functions'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiadmin_custom.index') )
						),
					);
				}

				if ( isset($GLOBALS['phpgw_info']['user']['apps']['preferences']) )
				{
					$this->newmenus['preferences'] = array
					(
						array
						(
							'text'	=> lang('Preferences'),
							'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname' => 'property', 'type'=> 'user') )
						),
						array
						(
							'text'	=> lang('Grant Access'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiadmin.aclprefs', 'acl_app'=> 'property'))
						)
					);

					$this->newmenus['toolbar'][] = array
					(
						'text'	=> lang('Preferences'),
						'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname'	=> 'property')),
						'image'	=> array('property', 'preferences')
					);
				}
			}
		}

		public function get_menu()
		{
			$this->sub = 'location';
			$this->links();
			$menu_global = $GLOBALS['phpgw']->session->appsession('phpgwapi', 'menu');
			$menu['navbar']['property'] = $menu_global['navbar']['property'];
			$menu['admin'] = $menu_global['admin']['property'];
			$menu['preferences'] = $menu_global['preferences']['property'];
			$menu['navigation'] = $menu_global['navigation']['property'];
//_debug_array($menu)
			return $menu;
		}

		function links($page='',$page_2='')
		{

			$currentapp='property';
			$sub = $this->sub;
			
			if($sub)
			{
				$GLOBALS['phpgw_info']['flags']['current_location'] .= '::' . $sub;				
			}
			if($page)
			{
				$GLOBALS['phpgw_info']['flags']['current_location'] .= '::' . $page;				
			}
			if($page_2)
			{
				$GLOBALS['phpgw_info']['flags']['current_location'] .= '::' . $page_2;				
			}

			if(!$this->query)
			{
				$menu = $GLOBALS['phpgw']->session->appsession('menu',substr(md5($currentapp.$sub . '_' . $page . '_' . $page_2),-20));
			}

			if(!isset($menu) || !$menu)
			{
				$menu = array();  // set to '' as appsession dos'nt return empty array correctly 
				$this->acl 			= CreateObject('phpgwapi.acl');

				$i=0;
				if ($this->acl->check('.location',1))
				{
					if($sub=='location')
					{
						$menu['module'][$i]['this']=True;
					}
					$menu['module'][$i]['url'] 		= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uilocation.index', 'type_id'=>1));
					$menu['module'][$i]['text'] 		= lang('Location');
					$menu['module'][$i]['statustext'] 	= lang('Location');
					$menu['module'][$i]['image']		= array('property', 'location');
					$i++;
				}

				if ($this->acl->check('.ifc',1))
				{
					if($sub=='ifc')
					{
						$menu['module'][$i]['this']=True;
					}
					$menu['module'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiifc.import'));
					$menu['module'][$i]['text']			=	lang('IFC');
					$menu['module'][$i]['statustext']	=	lang('IFC');
					$menu['module'][$i]['image']		=	array('property', 'ifc');
					$i++;
				}

				if ($this->acl->check('.ticket',1))
				{
					if($sub=='ticket')
					{
						$menu['module'][$i]['this']=True;
					}
					$menu['module'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uitts.index'));
					$menu['module'][$i]['text']			=	lang('Helpdesk');
					$menu['module'][$i]['statustext']	=	lang('Helpdesk');
					$i++;
				}

				if ($this->acl->check('.project',1))
				{
					if($sub=='project')
					{
						$menu['module'][$i]['this']=True;
					}
					$menu['module'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiproject.index'));
					$menu['module'][$i]['text']			=	lang('Project');
					$menu['module'][$i]['statustext']	=	lang('Project');
					$i++;
				}

				if ($this->acl->check('.invoice',1))
				{
					if($sub=='invoice')
					{
						$menu['module'][$i]['this']=True;
					}
					$menu['module'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiinvoice.index'));
					$menu['module'][$i]['text']			=	lang('Invoice');
					$menu['module'][$i]['statustext']	=	lang('Invoice');
					$menu['module'][$i]['image']		=	array('property', 'invoice');
					$i++;
				}

				if ($this->acl->check('.budget',1))
				{
					if($sub=='budget')
					{
						$menu['module'][$i]['this']=True;
					}
					$menu['module'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uibudget.index'));
					$menu['module'][$i]['text']			=	lang('Budget');
					$menu['module'][$i]['statustext']	=	lang('Budget');
					$i++;
				}
//--------------------
				if ($this->acl->check('.agreement',1))
				{
					if($sub=='agreement')
					{
						$menu['module'][$i]['this']=True;
					}
					$menu['module'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiagreement.index'));
					$menu['module'][$i]['text']			=	lang('Agreement');
					$menu['module'][$i]['statustext']	=	lang('Agreement');
					$i++;
				}
//----------------------

				if ($this->acl->check('.document',1))
				{
					if($sub=='document')
					{
						$menu['module'][$i]['this']=True;
					}
					$menu['module'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uidocument.index'));
					$menu['module'][$i]['text']			=	lang('Documentation');
					$menu['module'][$i]['statustext']	=	lang('Documentation');
					$i++;
				}

				if ($this->acl->check('.custom',1))
				{
					if($sub=='custom')
					{
						$menu['module'][$i]['this']=True;
					}
					$menu['module'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uicustom.index'));
					$menu['module'][$i]['text']			=	lang('Custom');
					$menu['module'][$i]['statustext']	=	lang('Custom queries');
				}
	
				$entity			= CreateObject('property.soadmin_entity');
				$entity_list 	= $entity->read(array('allrows'=>True));

				if (isset($entity_list) AND is_array($entity_list))
				{
					foreach($entity_list as $entry)
					{
						if ($this->acl->check('.entity.' . $entry['id'],1))
						{
							$i++;
							if($sub=='entity_' . $entry['id'])
							{
								$menu['module'][$i]['this']=True;
							}
							$menu['module'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uientity.index', 'entity_id'=> $entry['id']));
							$menu['module'][$i]['text']			=	$entry['name'];
							$menu['module'][$i]['statustext']	=	$entry['descr'];
						}
					}
				}

				unset($entity);

				$i = 0;
				if ($this->acl->check('.location',1))
				{
					if ($sub == 'location')
					{
						$menu['menu_title_2']=lang('Location');

						$soadmin_location	= CreateObject('property.soadmin_location');
						$location	= $soadmin_location->select_location_type();
						$query_temp = explode('-',$this->query);
						$query_location = '';
						
						$location_count=count($location);
						for ($j=0; $j<$location_count; $j++)
						{
							if(isset($query_temp[$j]) && $query_temp[$j])
							{
								$query[] = $query_temp[$j];
								$query_location = implode('-',$query);
							}
							if($page=='location'.$location[$j]['id'].'_')
							{
								$menu['sub_menu'][$i]['this']=True;
							}
							$menu['sub_menu'][$i]['url'] = $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uilocation.index', 'type_id'=> $location[$j]['id'],'query'=>$query_location));
							$menu['sub_menu'][$i]['text'] = $location[$j]['name'];
							$menu['sub_menu'][$i]['statustext'] = $location[$j]['descr'];
							$i++;
						}

						$config_tenant = $soadmin_location->read_config_single('tenant_id');

						if(substr($page,-2)=='_1')
						{
							$menu['sub_menu'][$i]['this']=True;
						}
						$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uilocation.index', 'lookup_tenant'=>1, 'type_id'=> $config_tenant,'query'=>$query_location));
						$menu['sub_menu'][$i]['text']			=	lang('Tenant');
						$menu['sub_menu'][$i]['statustext']		=	lang('Tenant');
						$i++;
					
						if($page=='gab')
						{
							$menu['sub_menu'][$i]['this']=True;
						}
						$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uigab.index'));
						$menu['sub_menu'][$i]['text']			=	lang('gabnr');
						$menu['sub_menu'][$i]['statustext']		=	lang('gabnr');
						$i++;
					
						if($page=='summary')
						{
							$menu['sub_menu'][$i]['this']=True;
						}
						$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uilocation.summary'));
						$menu['sub_menu'][$i]['text']			=	lang('Summary');
						$menu['sub_menu'][$i]['statustext']		=	lang('Summary');
						$i++;
					}
				}

				if ($sub == 'invoice')
				{
					$menu['menu_title_2']=lang('Invoice');

					if($page=='invoice_')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiinvoice.index'));
					$menu['sub_menu'][$i]['text']			=	lang('Invoice');
					$menu['sub_menu'][$i]['statustext']		=	lang('Invoice');
					$i++;

					if($page=='invoice_1')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiinvoice.index', 'paid'=>true));
					$menu['sub_menu'][$i]['text']			=	lang('Paid');
					$menu['sub_menu'][$i]['statustext']		=	lang('Paid');
					$i++;

					if($page=='consume')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiinvoice.consume'));
					$menu['sub_menu'][$i]['text']			=	lang('consume');
					$menu['sub_menu'][$i]['statustext']		=	lang('consume');
					$i++;

					if($page=='b_account')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uib_account.index'));
					$menu['sub_menu'][$i]['text']			=	lang('Budget account');
					$menu['sub_menu'][$i]['statustext']		=	lang('Budget account');
					$i++;

					if($page=='vendor')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiactor.index', 'role'=> 'vendor'));
					$menu['sub_menu'][$i]['text']			=	lang('Vendor');
					$menu['sub_menu'][$i]['statustext']		=	lang('Vendor');
					$i++;

					if($page=='tenant')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiactor.index', 'role'=> 'tenant'));
					$menu['sub_menu'][$i]['text']			=	lang('Tenant');
					$menu['sub_menu'][$i]['statustext']		=	lang('Tenant');
					$i++;
					
					if ($this->acl->check('.invoice',16))
					{
						if($page=='investment')
						{
							$menu['sub_menu'][$i]['this']=True;
						}
						$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiinvestment.index'));
						$menu['sub_menu'][$i]['text']			=	lang('Investment value');
						$menu['sub_menu'][$i]['statustext']		=	lang('Investment value');
						$i++;

						if($page=='import_inv')
						{
							$menu['sub_menu'][$i]['this']=True;
						}
						$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiXport.import'));
						$menu['sub_menu'][$i]['text']			=	lang('Import invoice');
						$menu['sub_menu'][$i]['statustext']		=	lang('Import invoice');
						$i++;

						if($page=='export_inv')
						{
							$menu['sub_menu'][$i]['this']=True;
						}
						$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiXport.export'));
						$menu['sub_menu'][$i]['text']			=	lang('Export invoice');
						$menu['sub_menu'][$i]['statustext']		=	lang('Export invoice');
						$i++;
					}

					if ($this->acl->check('.invoice',2))
					{
						if($page=='add_inv')
						{
							$menu['sub_menu'][$i]['this']=True;
						}
						$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiinvoice.add'));
						$menu['sub_menu'][$i]['text']			=	lang('Add');
						$menu['sub_menu'][$i]['statustext']		=	lang('Add invoice');
						$i++;
					}
				}

				if ($sub == 'pricebook' && $this->acl->check('.agreement',16))
				{
					$menu['menu_title_2']=lang('pricebook');

					if($page=='agreement_group')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uipricebook.agreement_group'));
					$menu['sub_menu'][$i]['text']			=	lang('Agreement group');
					$menu['sub_menu'][$i]['statustext']		=	lang('Agreement group');
					$i++;

					if($page=='activity')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uipricebook.activity'));
					$menu['sub_menu'][$i]['text']			=	lang('Activities');
					$menu['sub_menu'][$i]['statustext']		=	lang('Activities');
					$i++;

					if($page=='agreement')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiagreement.index'));
					$menu['sub_menu'][$i]['text']			=	lang('Agreement');
					$menu['sub_menu'][$i]['statustext']		=	lang('Agreement');
					$i++;
				}
//--------------
				if ($sub == 'agreement')
				{
					$menu['menu_title_2']=lang('Agreement');
					if($page=='agreement')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiagreement.index'));
					$menu['sub_menu'][$i]['text']			=	lang('Pricebook');
					$menu['sub_menu'][$i]['statustext']		=	lang('Pricebook');
					$i++;

					if($page=='s_agreement')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uis_agreement.index'));
					$menu['sub_menu'][$i]['text']			=	lang('Service');
					$menu['sub_menu'][$i]['statustext']		=	lang('service agreement');
					$i++;

					if($page=='r_agreement')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uir_agreement.index'));
					$menu['sub_menu'][$i]['text']			=	lang('Rental');
					$menu['sub_menu'][$i]['statustext']		=	lang('Rental agreement');
					$i++;

					if($page=='alarm')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uialarm.list_alarm'));
					$menu['sub_menu'][$i]['text']			=	lang('alarm');
					$menu['sub_menu'][$i]['statustext']		=	lang('alarm');
					$i++;

					if($this->acl->check('.agreement',16) && $page=='agreement')
					{
						$menu['menu_title_3']=lang('pricebook');

						$j=0;
						if($page_2=='agreement_group')
						{
							$menu['sub_menu_2'][$j]['this']=True;
						}
						$menu['sub_menu_2'][$j]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uipricebook.agreement_group'));
						$menu['sub_menu_2'][$j]['text']			=	lang('Agreement group');
						$menu['sub_menu_2'][$j]['statustext']	=	lang('Agreement group');
						$j++;

						if($page_2=='activity')
						{
							$menu['sub_menu_2'][$j]['this']=True;
						}
						$menu['sub_menu_2'][$j]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uipricebook.activity'));
						$menu['sub_menu_2'][$j]['text']			=	lang('Activities');
						$menu['sub_menu_2'][$j]['statustext']	=	lang('Activities');
						$j++;

						if($page_2=='agreement')
						{
							$menu['sub_menu_2'][$j]['this']=True;
						}
						$menu['sub_menu_2'][$j]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiagreement.index'));
						$menu['sub_menu_2'][$j]['text']			=	lang('Agreement');
						$menu['sub_menu_2'][$j]['statustext']	=	lang('Agreement');
						$j++;
					}

				}

//---------------
				if ($sub == 'project')
				{
					$menu['menu_title_2']=lang('Project');

					if($page=='project')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiproject.index'));
					$menu['sub_menu'][$i]['text']			=	lang('Project');
					$menu['sub_menu'][$i]['statustext']		=	lang('Project');
					$i++;

					if($page=='workorder')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiworkorder.index'));
					$menu['sub_menu'][$i]['text']			=	lang('Workorder');
					$menu['sub_menu'][$i]['statustext']		=	lang('Workorder');
					$i++;
					
/*
					if($page=='s_agreement')
					{
						$menu['sub_menu'][$i]['this']=True;
						$menu['menu_title_2'][]=lang('Service');
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uis_agreement.index'));
					$menu['sub_menu'][$i]['text']			=	lang('Service');
					$menu['sub_menu'][$i]['statustext']		=	lang('service agreement');
					$i++;
*/
					if($page=='request')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uirequest.index'));
					$menu['sub_menu'][$i]['text']			=	lang('Request');
					$menu['sub_menu'][$i]['statustext']		=	lang('Request');
					$i++;

					if($page=='template')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uitemplate.index'));
					$menu['sub_menu'][$i]['text']			=	lang('template');
					$menu['sub_menu'][$i]['statustext']		=	lang('Workorder template');
					$i++;

					if($page=='tenant_claim')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uitenant_claim.index'));
					$menu['sub_menu'][$i]['text']			=	lang('Tenant claim');
					$menu['sub_menu'][$i]['statustext']		=	lang('Tenant claim');
					$i++;
				}

				if ($sub == 'adm_loc' && $this->acl->check('.location',16))
				{
					$menu['menu_title_2']=lang('Admin location');
					if($page=='loc_type')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiadmin_location.index'));
					$menu['sub_menu'][$i]['text']			=	lang('Location type');
					$menu['sub_menu'][$i]['statustext']		=	lang('Location type');
					$i++;

					if($page=='loc_config')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiadmin_location.config'));
					$menu['sub_menu'][$i]['text']			=	lang('Config');
					$menu['sub_menu'][$i]['statustext']		=	lang('Location Config');
					$i++;
				}

				if ($sub == 'document')
				{
					$menu['menu_title_2']=lang('documentation');
					if($page=='document_')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uidocument.index'));
					$menu['sub_menu'][$i]['text']			=	lang('location');
					$menu['sub_menu'][$i]['statustext']		=	lang('Documentation for locations');
					$i++;
					
					if (isset($entity_list) AND is_array($entity_list))
					{
						foreach($entity_list as $entry)
						{
							if($entry['documentation'])
							{
								if($page=='document_'.$entry['id'])
								{
									$menu['sub_menu'][$i]['this']=True;
								}
								$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uidocument.index', 'entity_id' => $entry['id']));
								$menu['sub_menu'][$i]['text']			=	$entry['name'];
								$menu['sub_menu'][$i]['statustext']		=	$entry['descr'];
								$i++;
							}
						}
					}

				}
				
				if ($sub == 'budget')
				{
					$menu['menu_title_2']=lang('budget');

					if($page=='budget.basis')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uibudget.basis'));
					$menu['sub_menu'][$i]['text']			=	lang('basis');
					$menu['sub_menu'][$i]['statustext']		=	lang('budget per group');
					$i++;

					if($page=='budget')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uibudget.index'));
					$menu['sub_menu'][$i]['text']			=	lang('budget');
					$menu['sub_menu'][$i]['statustext']		=	lang('budget');
					$i++;

					if($page=='budget.obligations')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uibudget.obligations'));
					$menu['sub_menu'][$i]['text']			=	lang('obligations');
					$menu['sub_menu'][$i]['statustext']		=	lang('contractual obligations');
					$i++;
				}

				if ($sub == 'ifc')
				{
					$menu['menu_title_2']=lang('ifc');

					if($page=='ifc.import')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiifc.import'));
					$menu['sub_menu'][$i]['text']			=	lang('import');
					$menu['sub_menu'][$i]['statustext']		=	lang('import ifc xml');
					$i++;

				}

				$GLOBALS['phpgw']->session->appsession('menu',substr(md5($currentapp.$sub . '_' . $page . '_' . $page_2),-20),$menu);

			}

			if($GLOBALS['phpgw_info']['user']['preferences']['common']['template_set'] == 'newdesign')
			{

				$newmenus['navigation'] = array();

				foreach ($menu['module'] as $module)
				{
					$newmenus['navigation']['property'][$module['text']] = array(
						'url'	=> $module['url'],
						'text'	=> $module['text'],
	                    'image'	=> isset($module['image']) ? $module['image'] : ''
						);

					if($module['this'] == 1 && isset($menu['sub_menu']) && is_array($menu['sub_menu']))
					{
						foreach ($menu['sub_menu'] as $sub_menu)
						{
							$newmenus['navigation']['property'][$module['text']]['children'][$sub_menu['text']] = array(
								'url'	=> $sub_menu['url'],
								'text'	=> $sub_menu['text']
								);

							if($sub_menu['this'] == 1 && isset($menu['sub_menu_2']) && is_array($menu['sub_menu_2']))
							{
								foreach ($menu['sub_menu_2'] as $sub_menu_2)
								{
									$newmenus['navigation']['property'][$module['text']]['children'][$sub_menu['text']]['children'][$sub_menu_2['text']] = array(
										'url'	=> $sub_menu_2['url'],
										'text'	=> $sub_menu_2['text']
										);
								}
							}
						}
					}
				}

				$menu_global = $GLOBALS['phpgw']->session->appsession('phpgwapi', 'menu');
				if ( !$menu_global )
				{
					$menu_global = array();
				}
				if(!isset($menu_global['admin']) || !is_array($menu_global['admin']))
				{
					$menu_global['admin'] = array();
				}
				if(!isset($menu_global['preferences']) || !is_array($menu_global['preferences']))
				{
					$menu_global['preferences'] = array();
				}
				if(!isset($menu_global['navigation']) || !is_array($menu_global['navigation']))
				{
					$menu_global['navigation'] = array();
				}
				

				foreach($this->newmenus['navbar'] as $name => $navbar)
				{
					$menu_global['navbar'][$name] = $navbar;
				}
				foreach($this->newmenus['admin'] as $name => $admin)
				{
					$menu_global['admin'][$name] = $admin;
				}
				foreach($this->newmenus['preferences'] as $name => $preferences)
				{
					$menu_global['preferences'][$name] = $preferences;
				}
				foreach($newmenus['navigation'] as $name => $navigation)
				{
					$menu_global['navigation'][$name] = $navigation;
				}
			
				$GLOBALS['phpgw']->session->appsession('phpgwapi', 'menu', $menu_global);
			}

			$GLOBALS['phpgw']->session->appsession('menu_property','sidebox',$menu);
			return $menu;
		}
	}
?>
