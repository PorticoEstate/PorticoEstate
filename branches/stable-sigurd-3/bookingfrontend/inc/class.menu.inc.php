<?php
	class bookingfrontend_menu
	{
		function get_menu()
		{
			$incoming_app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$GLOBALS['phpgw_info']['flags']['currentapp'] = 'bookingfrontend';

			$menus = array();

			if ( isset($GLOBALS['phpgw_info']['user']['apps']['admin']) )
			{
				$menus['admin'] = array
				(
					'index'	=> array
					(
						'text'	=> lang('Configuration'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiconfig.index', 'appname' => 'bookingfrontend') )
					),
				);
			}

			$GLOBALS['phpgw_info']['flags']['currentapp'] = $incoming_app;
			return $menus;
		}
	}
