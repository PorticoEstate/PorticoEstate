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
				);
			}

			$menus['navbar'] = array
			(
				'frontend' => array
				(
					'text'	=> lang('frontend'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'frontend.uicontract.index') ),
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

			$GLOBALS['phpgw_info']['flags']['currentapp'] = $incoming_app;
			return $menus;
		}
	}
