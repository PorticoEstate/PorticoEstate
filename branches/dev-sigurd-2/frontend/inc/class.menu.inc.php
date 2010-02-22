<?php
	class frontend_menu
	{
		function get_menu()
		{
			$incoming_app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$GLOBALS['phpgw_info']['flags']['currentapp'] = 'frontend';

			$menus = array();

			if ( $GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin')
			|| $GLOBALS['phpgw']->acl->check('admin', phpgwapi_acl::ADD, 'frontend'))
			{
				$menus['admin'] = array
				(
					'index'	=> array
					(
						'text'	=> lang('Configuration'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiconfig.index', 'appname' => 'frontend') )
					),
					'acl'	=> array
					(
						'text'	=> lang('Configure Access Permissions'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uiadmin_acl.list_acl', 'acl_app' => 'frontend') )
					)
				);
			}

			$menus['navbar'] = array
			(
				'frontend' => array
				(
					'text'	=> lang('frontend'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'frontend.uifrontend.index') ),
					'image'	=> array('frontend', 'navbar'),
					'order'	=> 35,
					'group'	=> 'office'
				),
			);


			$menus['navigation'] = array
			(
				'show'	=> array
				(
					'text'	=> lang('show'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'frontend.uicontract.show'))
				),
				'demo_tab'	=> array
				(
					'text'	=> 'Demo Tabs',
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'frontend.ui_demo_tabs.first'))
				),
				'demo_tab_noframework'	=> array
				(
					'text'	=> 'Demo Tabs noframework',
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'frontend.ui_demo_tabs.first', 'noframework' => true))
				)
			);


			$locations = $GLOBALS['phpgw']->locations->get_locations();

			unset($locations['.']);
			unset($locations['admin']);
//_debug_array($locations);die();
			$tabs = array();
			foreach ($locations as $location => $name)
			{
				if ( $GLOBALS['phpgw']->acl->check($location, PHPGW_ACL_READ, 'frontend') )
				{
					$location_id = $GLOBALS['phpgw']->locations->get_id('frontend', $location);
					$menus['navigation'][$location_id] = array(
						'text' => lang($name),
						'url'  => $GLOBALS['phpgw']->link('/',array('menuaction' => "frontend.uifrontend.{$name}", 'type'=>$location_id, 'noframework' => $noframework))
					);
				}			
			}
			


			$GLOBALS['phpgw_info']['flags']['currentapp'] = $incoming_app;
			return $menus;
		}
	}
