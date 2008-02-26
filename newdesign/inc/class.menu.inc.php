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

			$menus['navigation'] = array
			(
				/*
				'form'	=> array
				(
					'text'	=> 'Form',
					'url'	=>  $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'newdesign.uinewdesign.index','output'=>'html'))
				),
				'grid'	=> array
				(
					'text'	=> 'Grid',
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'newdesign.uinewdesign.grid','output'=>'html'))
				),
				'project'	=> array
				(
					'text'	=> 'Project',
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'newdesign.uinewdesign.project','output'=>'html'))
				),
				*/
				'datatable'	=> array
				(
					'text'	=> 'DataTable',
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'newdesign.uinewdesign.datatable','output'=>'html')),
					'image' => array('newdesign', 'table')
				),
				'property'	=> array
				(
					'text'	=> 'Property',
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'newdesign.uinewdesign.property','output'=>'html')),
					'image' => array('newdesign', 'property')
				)
			);
			return $menus;
		}


		function links($page='',$page_2='')
		{

		}
	}
