<?php
	/**
	* phpGroupWare - Catch: a aplication for import from external sources.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2007 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package catch
	* @subpackage core
 	* @version $Id$
	*/

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	 /**
	 * Description
	 * @package catch
	 */

	class catch_menu
	{
		/**
		 * Get the menus for the catch
		 *
		 * @return array available menus for the current user
		 */
		public function get_menu()
		{
			$incoming_app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$GLOBALS['phpgw_info']['flags']['currentapp'] = 'catch';
			$acl = & $GLOBALS['phpgw']->acl;
			$start_page = 'catch';
			if ( isset($GLOBALS['phpgw_info']['user']['preferences']['catch']['default_start_page'])
					&& $GLOBALS['phpgw_info']['user']['preferences']['catch']['default_start_page'] )
			{
					$start_page = $GLOBALS['phpgw_info']['user']['preferences']['catch']['default_start_page'];
			}

			$menus = array();

			$menus['navbar'] = array
			(
				'catch' => array
				(
					'text'	=> lang('catch'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => "catch.ui{$start_page}.index") ),
					'image'	=> array('catch', 'navbar'),
					'order'	=> 35,
					'group'	=> 'office'
				),
			);

			$entity			= CreateObject('property.soadmin_entity');
			$entity_list 	= $entity->read(array('allrows' => true, 'type' => 'catch'));

			$menus['toolbar'] = array();
			if ( isset($GLOBALS['phpgw_info']['user']['apps']['admin']) )
			{
				if ( is_array($entity_list) && count($entity_list) )
				{
					foreach($entity_list as $entry)
					{
						$admin_children_entity["entity_{$entry['id']}"] = array
						(
							'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.category', 'entity_id'=> $entry['id'], 'type' => 'catch')),
							'text'	=> $entry['name'],
							'image'		=> array( 'catch', 'entity_' . $entry['id'] )
						);

						$cat_list = $entity->read_category(array('allrows'=>true,'entity_id'=>$entry['id'], 'type' => 'catch'));

						foreach($cat_list as $category)
						{
							$admin_children_entity["entity_{$entry['id']}"]['children']["entity_{$entry['id']}_{$category['id']}"]	= array
							(
								'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.list_attribute', 'entity_id'=> $entry['id'] , 'cat_id'=> $category['id'], 'type' => 'catch')),
								'text'	=> $category['name']
							);
						}
					}
				}


				$menus['admin'] = array
				(
					'entity'	=> array
					(
						'text'	=> lang('Admin entity'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiadmin_entity.index', 'type' => 'catch') ),
						'children' => $admin_children_entity
					),
					'config'	=> array
					(
						'text'	=> lang('config'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'catch.uiconfig.index'))
					),
					'categories'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Global Categories', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicategories.index', 'appname' => 'catch'))
					),
					'acl'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Configure Access Permissions', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uiadmin_acl.list_acl', 'acl_app' => 'catch'))
					),
					'list_atrribs'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('custom fields', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_attribute', 'appname' => 'catch'))
					),
					'list_functions'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('custom functions', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_custom_function', 'appname' =>  'catch'))
					)
				);
			}

			if ( isset($GLOBALS['phpgw_info']['user']['apps']['preferences']) )
			{
				$menus['preferences'] = array
				(
					array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Preferences', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname' => 'catch', 'type'=> 'user') )
					),
					array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Grant Access', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'preferences.uiadmin_acl.aclprefs', 'acl_app'=> 'catch') )
					)
				);

				$menus['toolbar'][] = array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Preferences', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname'	=> 'catch')),
					'image'	=> array('catch', 'preferences')
				);
			}

			$menus['navigation'] = array();
			if ( is_array($entity_list) && count($entity_list) )
			{
				foreach($entity_list as $entry)
				{
					if ( $acl->check(".catch.{$entry['id']}", PHPGW_ACL_READ, 'catch') )
					{
						$menus['navigation']["entity_{$entry['id']}"] = array
						(
							'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uientity.index', 'entity_id'=> $entry['id'], 'type' => 'catch')),
							'text'	=> $entry['name'],
							'image'		=> array( 'catch', 'entity_' . $entry['id'] )
						);
					}

					$cat_list = $entity->read_category(array('allrows'=>true,'entity_id'=>$entry['id'], 'type' => 'catch'));

					foreach($cat_list as $category)
					{
						if ( $acl->check(".catch.{$entry['id']}.{$category['id']}", PHPGW_ACL_READ, 'catch') )
						{
							$menus['navigation']["entity_{$entry['id']}"]['children']["entity_{$entry['id']}_{$category['id']}"]	= array
							(
								'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uientity.index', 'entity_id'=> $entry['id'] , 'cat_id'=> $category['id'], 'type' => 'catch')),
								'text'	=> $category['name']
							);
						}
					}
				}
			}
			unset($entity_list);
			unset($entity);
			$GLOBALS['phpgw_info']['flags']['currentapp'] = $incoming_app;
			return $menus;
		}
	}
