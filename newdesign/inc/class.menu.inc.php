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
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package newdesign
	 */
	class newdesign_menu
	{
		var $sub;

		var $public_functions = array
		(
			'links'	=> True,
		);

		function newdesign_menu($sub='')
		{
			$this->sub		= $sub;
			$this->currentapp	= 'newdesign';
		}

		public function get_menu()
		{
			$start_page = 'newdesign';
			if ( isset($GLOBALS['phpgw_info']['user']['preferences']['newdesign']['default_start_page'])
					&& $GLOBALS['phpgw_info']['user']['preferences']['newdesign']['default_start_page'] )
			{
					$start_page = $GLOBALS['phpgw_info']['user']['preferences']['newdesign']['default_start_page'];
			}

			$menus['navbar'] = array
			(
				'newdesign' => array
				(
					'text'	=> 'Newdesign',
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => "newdesign.ui{$start_page}.datatable") ),
					'image'	=> array('newdesign', 'navbar'),
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
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicategories.index', 'appname' => 'newdesign'))
					),
					'acl'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Configure Access Permissions', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uiadmin_acl.list_acl', 'acl_app' => 'newdesign'))
					),
					'list_atrribs'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('custom fields', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_attribute', 'appname' => 'newdesign'))
					),
					'list_functions'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('custom functions', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_custom_function', 'appname' =>  'newdesign'))
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
						'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname' => 'newdesign', 'type'=> 'user') )
					),
					array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Grant Access', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'preferences.uiadmin_acl.aclprefs', 'acl_app'=> 'newdesign') )
					)
				);

				$menus['toolbar'][] = array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Preferences', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname'	=> 'newdesign')),
					'image'	=> array('newdesign', 'preferences')
				);
			}
			$location_children = array
			(
				'location_loc_1' => array
				(
					'text' => 'Eiendom',
					'url'	=>  $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'newdesign.uinewdesign.location','output'=>'html', 'type_id' => 1 )),
				),
				'location_loc_2' => array
				(
					'text' => 'Bygg',
					'url'	=>  $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'newdesign.uinewdesign.location','output'=>'html', 'type_id' => 2 )),
				),
				'location_loc_3' => array
				(
					'text' => 'Inngang',
					'url'	=>  $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'newdesign.uinewdesign.location','output'=>'html', 'type_id' => 3 )),
				),
				'location_loc_4' => array
				(
					'text' => 'Leieobjekt',
					'url'	=>  $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'newdesign.uinewdesign.location','output'=>'html', 'type_id' => 4 )),
				),
				'location_loc_4_1' => array
				(
					'text' => 'Leieboer',
					'url'	=>  $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'newdesign.uinewdesign.location','output'=>'html', 'type_id' => 4, 'lookup_tenant' => 1 )),
				)

			);

			$menus['navigation'] = array
			(
				'datatable'	=> array
				(
					'text'	=> 'DataTable',
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'newdesign.uinewdesign.datatable','output'=>'html')),
					'image' => array('newdesign', 'table')
				),
				'form'	=> array
				(
					'text'	=> 'Property',
					'url'	=>  $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'newdesign.uinewdesign.index','output'=>'html')),
					'image' => array('newdesign', 'property')
				),
				'location'	=> array
				(
					'text'	=> 'Location',
					'url'	=>  $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'newdesign.uinewdesign.location','output'=>'html')),
					'image' => array('newdesign', 'property'),
					'children' => $location_children
				)
			);


			return $menus;
		}


		function links($page='',$page_2='')
		{

		}
	}
