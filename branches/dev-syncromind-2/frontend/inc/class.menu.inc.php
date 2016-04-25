<?php
	phpgw::import_class('frontend.bofrontend');

	class frontend_menu
	{

		function get_menu()
		{
			$incoming_app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$GLOBALS['phpgw_info']['flags']['currentapp'] = 'frontend';

			$menus = array();

			if ($GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin') || $GLOBALS['phpgw']->acl->check('admin', phpgwapi_acl::ADD, 'frontend'))
			{
				$menus['admin'] = array
					(
					'index' => array
						(
						'text' => lang('Configuration'),
						'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiconfig.index',
							'appname' => 'frontend'))
					),
					'acl' => array
						(
						'text' => lang('Configure Access Permissions'),
						'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uiadmin_acl.list_acl',
							'acl_app' => 'frontend'))
					),
					'documents' => array
						(
						'text' => lang('upload_userdoc'),
						'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'frontend.uidocumentupload.index',
							'appname' => 'frontend'))
					)
				);
			}

			$menus['navbar'] = array
				(
				'frontend' => array
					(
					'text' => lang('frontend'),
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'frontend.uifrontend.index')),
					'image' => array('frontend', 'navbar'),
					'order' => 35,
					'group' => 'office'
				),
			);


			$menus['navigation'] = array();


			$locations = frontend_bofrontend::get_sections();

			$tabs = array();
			foreach ($locations as $key => $entry)
			{
				$name = $entry['name'];
				$location = $entry['location'];

				if ($GLOBALS['phpgw']->acl->check($location, PHPGW_ACL_READ, 'frontend'))
				{
					$location_id = $GLOBALS['phpgw']->locations->get_id('frontend', $location);
					$menus['navigation'][$location_id] = array(
						'text' => lang($name),
						'url' => $GLOBALS['phpgw']->link('/', array('menuaction' => "frontend.ui{$name}.index",
							'type' => $location_id, 'noframework' => $noframework))
					);
				}
			}

			$GLOBALS['phpgw_info']['flags']['currentapp'] = $incoming_app;
			return $menus;
		}
	}