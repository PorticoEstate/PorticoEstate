<?php
	/**
	* phpGroupWare - logistic: a part of a Facilities Management System.
	*
	* @author Erik Holm-Larsen <erik.holm-larsen@bouvet.no>
	* @copyright Copyright (C) 2011,2012 Free Software Foundation, Inc. http://www.fsf.org/
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
	* @package property
	* @subpackage logistic
 	* @version $Id: class.menu.inc.php 10314 2012-10-23 12:41:15Z sigurdne $
	*/

	class logistic_menu
	{
		function get_menu()
		{
			$incoming_app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$GLOBALS['phpgw_info']['flags']['currentapp'] = 'logistic';
			$menus = array();

			$menus['navbar'] = array
			(
				'logistic' => array
				(
					'text'	=> lang('logistic'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'logistic.uiproject.index') ),
					'image'	=> array('property', 'location'),
					'order'	=> 10,
					'group'	=> 'office'
				)
			);


			$favorites_children = array();
			if(isset($GLOBALS['phpgw_info']['user']['preferences']['logistic']['menu_favorites']) && $GLOBALS['phpgw_info']['user']['preferences']['logistic']['menu_favorites'])
			{
				$menu_favorites = $GLOBALS['phpgw_info']['user']['preferences']['logistic']['menu_favorites'];
				foreach ($menu_favorites as $type => $targets)
				{
					foreach ($targets as $target => $target_name)
					{
						$favorites_children["{$type}{$target}"] = array
						(
							'text'	=> $target_name,
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => "logistic.ui{$type}.index", 'filter' =>$target ) ),
							'image'	=> array('property', 'location_tenant')
						);
					}
				}
			}

			$menus['navigation'] =  array
			(
				'project' => array
				(
					'text'	=> lang('project'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'logistic.uiproject.index') ),
					'image'	=> array('property', 'location_tenant'),
					'children'	=> array(
							'activity' => array
								(
										'text'	=> lang('activity'),
										'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'logistic.uiactivity.index') ),
										'image'	=> array('property', 'location_tenant')
								),
/*								'requirement' => array
								(
										'text'	=> lang('requirement'),
										'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'logistic.uirequirement.index') ),
										'image'	=> array('property', 'location_tenant'),
								),
								'allocation' => array
								(
										'text'	=> lang('allocation'),
										'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'logistic.uiallocation.index') ),
										'image'	=> array('property', 'location_tenant'),
								),*/
						)
				),
				'favorites' => array
				(
					'text'		=> lang('favorites'),
					'url'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'logistic.uiactivity.index') ),
					'image'		=> array('property', 'location_tenant'),
					'children'	=> $favorites_children
				)
			);

			if ( $GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin')
				|| $GLOBALS['phpgw']->acl->check('admin', phpgwapi_acl::ADD, 'logistic'))
			{
				$menus['admin'] = array
				(
					'index'	=> array
					(
						'text'	=> lang('Configuration'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiconfig.index', 'appname' => 'logistic') )
					),
					'acl'	=> array
					(
						'text'	=> lang('Configure Access Permissions'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uiadmin_acl.list_acl', 'acl_app' => 'logistic') )
					),
					'project_types'	=> array
					(
						'text'	=> lang('Project types'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'logistic.uiproject.project_types') )
					),
					'resource_type_requirement' => array(
						'text' => lang('resource_type_requirement'),
						'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'logistic.uiresource_type_requirement.index') )
					)
/*					'control_cats'	=> array
					(
						'text'	=> lang('Control area'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicategories.index', 'appname' => 'controller', 'location' => '.control', 'global_cats' => 'true', 'menu_selection' => 'admin::controller::control_cats') )
					),
					'role_at_location'	=> array
					(
						'url'	=>	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilocation.responsiblility_role', 'menu_selection' => 'admin::controller::role_at_location') ),
						'text'	=>	lang('role at location'),
						'image'	=> array('property', 'responsibility_role')
					),
					'controller_document_types'	=> array
					(
						'url'	=>	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'controller.uidocument.document_types', 'menu_selection' => 'admin::controller::controller_document_types') ),
						'text'	=>	lang('Document types')
					)*/
				);
			}

			$menus['folders'] = phpgwapi_menu::get_categories('bergen');

			$GLOBALS['phpgw_info']['flags']['currentapp'] = $incoming_app;

			return $menus;
		}
	}
