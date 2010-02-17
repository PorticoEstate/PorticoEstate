<?php
	class rentalfrontend_menu
	{
		function get_menu()
		{
			$incoming_app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$GLOBALS['phpgw_info']['flags']['currentapp'] = 'rentalfrontend';

			$menus = array();

			if ( $GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin')
			|| $GLOBALS['phpgw']->acl->check('admin', phpgwapi_acl::ADD, 'rentalfrontend'))
			{
				$menus['admin'] = array
				(
					'index'	=> array
					(
						'text'	=> lang('Configuration'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiconfig.index', 'appname' => 'rentalfrontend') )
					),
				);
			}

			$menus['navbar'] = array
			(
				'rentalfrontend' => array
				(
					'text'	=> lang('rentalfrontend'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'rentalfrontend.uicontract.index') ),
					'image'	=> array('rentalfrontend', 'navbar'),
					'order'	=> 35,
					'group'	=> 'office'
				),
			);


			$menus['navigation'] = array
			(
				'show'	=> array
				(
					'text'	=> lang('show'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'rentalfrontend.uicontract.show'))
				)
			);

			$GLOBALS['phpgw_info']['flags']['currentapp'] = $incoming_app;
			return $menus;
		}
	}
