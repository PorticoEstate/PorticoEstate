<?php
	/**
	 * helpdesk - Menus
	 *
	 * @author Dave Hall <skwashd@phpgroupware.org>
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2007,2008 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package helpdesk
	 * @version $Id: class.menu.inc.php 6711 2010-12-28 15:15:42Z sigurdne $
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
	 * Menus
	 *
	 * @package helpdesk
	 */
	class helpdesk_menu
	{
		/**
		 * Get the menus for the helpdesk
		 *
		 * @return array available menus for the current user
		 */
		public function get_menu($type='')
		{
			$incoming_app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$GLOBALS['phpgw_info']['flags']['currentapp'] = 'helpdesk';
			$acl = & $GLOBALS['phpgw']->acl;
			$menus = array();

			$config = CreateObject('phpgwapi.config', 'helpdesk')->read();
			if (!empty($config['app_name']))
			{
				$lang_app_name = $config['app_name'];
			}
			else
			{
				$lang_app_name = lang('helpdesk');
			}

			$menus['navbar'] = array
				(
					'helpdesk' => array
					(
						'text'	=> $lang_app_name,
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => "helpdesk.uitts.index") ),
						'image'	=> array('helpdesk', 'navbar'),
						'order'	=> 35,
						'group'	=> 'facilities management'
					),
				);

			$menus['toolbar'] = array();


			if ( $GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin')
				|| $GLOBALS['phpgw']->acl->check('admin', phpgwapi_acl::ADD, 'helpdesk'))
			{

				$menus['admin'] = array
					(
						'index'	=> array
						(
							'text'	=> lang('Configuration'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiconfig.index', 'appname' => 'helpdesk') )
						),
						'ticket_attribs' => array
							(
							'text' => lang('ticket Attributes'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_attribute',
								'appname' => 'helpdesk', 'location' => '.ticket', 'menu_selection' => 'admin::helpdesk::ticket_attribs'))
						),
						'ticket_functions' => array
							(
							'text' => lang('custom functions'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_custom_function',
								'appname' => 'helpdesk', 'location' => '.ticket', 'menu_selection' => 'admin::helpdesk::ticket_functions'))
						),
						'ticket_cats'	=> array
						(
							'text'	=> lang('Ticket Categories'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicategories.index', 'appname' => 'helpdesk', 'location' => '.ticket', 'global_cats' => 'true', 'menu_selection' => 'admin::helpdesk::ticket_cats') )
						),
						'ticket_status'	=> array
						(
							'text'	=> lang('Ticket status'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'helpdesk.uigeneric.index', 'type' => 'helpdesk_status') )
						),
						'acl'	=> array
						(
							'text'	=> lang('Configure Access Permissions'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uiadmin_acl.list_acl', 'acl_app' => 'helpdesk') )
						),
						'custom_menu_items' => array
							(
							'text' => lang('custom menu items'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'helpdesk.uigeneric.index',
								'type' => 'custom_menu_items'))
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
							'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname' => 'helpdesk', 'type'=> 'user') )
						),
						array
						(
							'text'	=> $GLOBALS['phpgw']->translation->translate('Grant Access', array(), true),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiadmin.aclprefs', 'acl_app'=> 'helpdesk'))
						)
					);

				$menus['toolbar'][] = array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Preferences', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname'	=> 'helpdesk')),
						'image'	=> array('helpdesk', 'preferences')
					);
			}

			$menus['navigation'] = array();


			if ( $acl->check('.ticket',PHPGW_ACL_READ, 'helpdesk') )
			{
				$categories	= CreateObject('phpgwapi.categories', -1, 'helpdesk', '.ticket');
				$categories->supress_info	= true;

				$_cats = $categories->return_sorted_array(0, false, '', '', '', false, false);

				$_categories = array();
				$subs = false;
				foreach ($_cats as $_cat)
				{
					if ($_cat['level'] == 0 && $_cat['active'] != 2)
					{
						$_categories[] = $_cat;
					}
					else if ($_cat['level'] > 0 && $_cat['active'] != 2)
					{
						$subs = true;
					}
				}

				if($subs)
				{
					$menus['navbar']['helpdesk']['url'] = $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'helpdesk.uitts.index', 'parent_cat_id' => -1));

					foreach ($_categories as $_category)
					{
						$menus['navigation']["helpdesk_{$_category['id']}"] = array
							(
								'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'helpdesk.uitts.index', 'parent_cat_id' => $_category['id'])),
								'text'	=> $_category['name'],
								'image'		=> array('helpdesk', 'helpdesk')
							);
					}
				}
				else
				{
					$menus['navigation']['helpdesk'] = array
						(
							'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'helpdesk.uitts.index')),
							'text'	=> lang('inbox'),
							'image'		=> array('helpdesk', 'helpdesk')
						);

				}
			}



			if ( $acl->check('.ticket.response_template',PHPGW_ACL_READ, 'helpdesk') ) //manage
			{
				$menus['navigation']['response_template'] = array
					(
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'helpdesk.uigeneric.index',
						'type' => 'response_template')),
					'text' => lang('response template'),
					'image' => array('helpdesk', 'helpdesk')
				);
			}

			if ( $acl->check('.email_out',PHPGW_ACL_READ, 'helpdesk') ) //manage
			{
				$menus['navigation']['email_out'] = array(
					'text' => lang('email out'),
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'helpdesk.uiemail_out.index')),
					'image' => array('helpdesk', 'helpdesk'),
					'children' => array(
						'template' => array(
							'text' => lang('email template'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'helpdesk.uigeneric.index',
								'type' => 'email_template', 'admin' => true))
						),
						'recipient_set' => array(
							'text' => lang('admin recipient set'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'helpdesk.uigeneric.index',
								'type' => 'email_recipient_set', 'admin' => true))
						),
						'recipient_list' => array(
							'text' => lang('recipient list'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'helpdesk.uigeneric.index',
								'type' => 'email_recipient_list', 'admin' => true))
						),
					)
				);

				$custom_menu_items = CreateObject('helpdesk.sogeneric','custom_menu_items')->read_tree(array('type' => 'custom_menu_items',
							'filter' => array('location' => '.ticket')));

				if ($custom_menu_items)
				{
					$menus['navigation']['report'] = array
						(
							'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'helpdesk.uicustom.index')),
							'text' => lang('reports'),
							'image' => array('helpdesk', 'helpdesk')
					);
					foreach ($custom_menu_items as $item)
					{
						if(empty($item['local_files']))
						{
							$item['url'] .= '&' . get_phpgw_session_url();							
						}
						$menus['navigation']['report']['children'][] = $item;
					}
				}
			}

			$GLOBALS['phpgw_info']['flags']['currentapp'] = $incoming_app;
			return $menus;
		}
	}
