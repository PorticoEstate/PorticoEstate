<?php
	/**
	 * bim - Menus
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2007,2008 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package bim
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
	 * Menus
	 *
	 * @package bim
	 */
	class bim_menu
	{

		/**
		 * Get the menus for the bim
		 *
		 * @return array available menus for the current user
		 */
		public function get_menu()
		{
			$incoming_app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$GLOBALS['phpgw_info']['flags']['currentapp'] = 'bim';
			$acl = & $GLOBALS['phpgw']->acl;
			$menus = array();

			$menus['navbar'] = array
			(
				'bim' => array
				(
					'text'	=> lang('bim'),
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => "bim.uibim.showModels")),
					'image'	=> array('bim', 'navbar'),
					'order'	=> 35,
					'group'	=> 'facilities management'
				),
			);

			$menus['toolbar'] = array();

			if($GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin')
			|| $GLOBALS['phpgw']->acl->check('admin', phpgwapi_acl::ADD, 'bim'))
			{


				$menus['admin'] = array
				(
					'index'	=> array
					(
						'text'	=> lang('Configuration'),
						'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiconfig.index',
							'appname' => 'bim'))
					),
					'acl'	=> array
					(
						'text'	=> lang('Configure Access Permissions'),
						'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uiadmin_acl.list_acl',
							'acl_app' => 'bim'))
					)
				);
			}

			if(isset($GLOBALS['phpgw_info']['user']['apps']['preferences']))
			{
				$menus['preferences'] = array
				(
					array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Preferences', array(), true),
						'url' => $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname' => 'bim',
							'type' => 'user'))
					),
					array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Grant Access', array(), true),
						'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bim.uiadmin.aclprefs',
							'acl_app' => 'bim'))
					)
				);

				$menus['toolbar'][] = array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Preferences', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname'	=> 'bim')),
					'image'	=> array('bim', 'preferences')
				);
			}

			$menus['navigation'] = array();


			if($acl->check('.ifc', PHPGW_ACL_READ, 'bim'))
			{
				$menus['navigation']['ifc'] = array
				(
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bim.uiifc.import')),
					'text'		=> lang('IFC'),
					'image'		=> array('bim', 'ifc'),
					'children'	=> array
					(
						'import'	=> array
						(
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bim.uiifc.import')),
							'text'	=> lang('import'),
							'image'		=> array('bim', 'ifc_import'),
						)
					)
				);
			}

			$menus['navigation']['viewer'] = array
			(
				'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bim.uiviewer.index')),
				'text'		=> lang('viewer'),
				'image'		=> array('bim', 'ifc'),
			);

			$menus['navigation']['item'] = array
            (
				'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bim.uiitem.index')),
                'text'	=> lang('BIM Items'),
                'image'	=> array('bim', 'custom'),
                'children'	=> array_merge(array
                (
                    'index'		=> array
                    (
						'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bim.uiitem.index')),
                        'text'	=> lang('Register')
                    ),
                    'foo'       => array
                    (
						'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bim.uiitem.foo')),
                        'text'	=> lang('Foo')
                    ),
                    'showModels'       => array
                    (
						'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bim.uibim.showModels')),
                        'text'	=> lang('Show Models')
                    ),
                    'ifc'       => array
                    (
						'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bim.uiifc.import')),
                        'text'	=> lang('Ifc')
                    ),
                    'upload'		=> array
                    (
						'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bim.uibim.upload')),
                        'text'	=> lang('Upload Model'),
                        'image'	=> array('bim', 'project_tenant_claim')
                    )
                ))
            );

			$GLOBALS['phpgw_info']['flags']['currentapp'] = $incoming_app;
			return $menus;
		}
	}
