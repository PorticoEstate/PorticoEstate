<?php
	/**
	* phpGroupWare - newdesign: a demo application.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package newdesign
	* @subpackage core
 	* @version $Id: class.menu.inc.php 1433 2008-07-16 12:02:46Z janaage@hikt.no $
	*/

	/**
	 * Description
	 * @package newdesign
	 */
	class equipo_menu
	{
		var $sub;

		var $public_functions = array
		(
			'links'	=> True,
		);

		function equipo_menu($sub='')
		{
			$this->sub		= $sub;
			$this->currentapp	= 'equipo';
		}

		public function get_menu()
		{
			$start_page = 'equipo';
			if ( isset($GLOBALS['phpgw_info']['user']['preferences']['equipo']['default_start_page'])
					&& $GLOBALS['phpgw_info']['user']['preferences']['equipo']['default_start_page'] )
			{
					$start_page = $GLOBALS['phpgw_info']['user']['preferences']['equipo']['default_start_page'];
			}

			$menus['navbar'] = array
			(
				'equipo' => array
				(
					'text'	=> 'equipo',
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => "equipo.ui{$start_page}.datatable") ),
					'image'	=> array('equipo', 'navbar'),
					'order'	=> 35,
					'group'	=> 'office'
				),
			);

			$menus['toolbar'] = array();
			if ( isset($GLOBALS['phpgw_info']['user']['apps']['admin']) )
			{
				$menus['admin'] = array
				(
					'categories'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Global Categories', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicategories.index', 'appname' => 'equipo'))
					),
					'acl'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Configure Access Permissions', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uiadmin_acl.list_acl', 'acl_app' => 'equipo'))
					),
					'list_atrribs'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('custom fields', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_attribute', 'appname' => 'equipo'))
					),
					'list_functions'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('custom functions', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_custom_function', 'appname' =>  'equipo'))
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
						'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname' => 'equipo', 'type'=> 'user') )
					),
					array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Grant Access', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'preferences.uiadmin_acl.aclprefs', 'acl_app'=> 'equipo') )
					)
				);

				$menus['toolbar'][] = array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Preferences', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname'	=> 'equipo')),
					'image'	=> array('equipo', 'preferences')
				);
			}


			$menus['navigation'] = array
			(
				'datatable'	=> array
				(
					'text'	=> 'DataTable',
					'url'	=>  $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'equipo.uiequipo.gab','output'=>'html', 'type_id' => 1 )),
					'image' => array('equipo', 'property')
				)

		);


			return $menus;
		}


		function links($page='',$page_2='')
		{

		}
	}
