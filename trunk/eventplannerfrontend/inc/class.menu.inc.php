<?php

	class eventplannerfrontend_menu
	{

		function get_menu()
		{
			$incoming_app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$GLOBALS['phpgw_info']['flags']['currentapp'] = 'eventplannerfrontend';

			$menus = array();

			if ($GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin') || $GLOBALS['phpgw']->acl->check('admin', phpgwapi_acl::ADD, 'eventplannerfrontend'))
			{
				$menus['admin'] = array
					(
					'index' => array
						(
						'text' => lang('Configuration'),
						'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiconfig.index',
							'appname' => 'eventplannerfrontend'))
					),
					'acl' => array(
						'text' => $GLOBALS['phpgw']->translation->translate('Configure Access Permissions', array(), true),
						'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uiadmin_acl.list_acl',
							'acl_app' => 'eventplannerfrontend'))
					),
					'metasettings' => array
						(
						'text' => lang('Metadata'),
						'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uimetasettings.index',
							'appname' => 'eventplanner'))
					),
				);
			}

			$GLOBALS['phpgw_info']['flags']['currentapp'] = $incoming_app;
			return $menus;
		}
	}