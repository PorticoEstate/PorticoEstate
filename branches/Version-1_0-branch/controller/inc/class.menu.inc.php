<?php
	class controller_menu
	{
		function get_menu()
		{
			$incoming_app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$GLOBALS['phpgw_info']['flags']['currentapp'] = 'controller';
			$menus = array();

			$menus['navbar'] = array
			(
				'controller' => array
				(
					'text'	=> lang('Controller'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'activitycalendar.uiactivities.index') ),
                    'image'	=> array('property', 'location'),
					'order'	=> 10,
					'group'	=> 'office'
				)
			);

			$menus['navigation'] =  array
			(
				'control' => array
				(
					'text'	=> lang('Control'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'controller.uicontrol.index') ),
		                  'image'	=> array('property', 'location_1'),
				),
				'organizationList' => array
				(
					'text'	=> lang('OrganizationList'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'activitycalendar.uiorganization.index') ),
		            'image'	=> array('property', 'location_tenant'),
					'children'	=> array(
								'changed_organizations' => array
								(
									'text'	=> lang('changed_org_group'),
									'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'activitycalendar.uiorganization.changed_organizations') ),
									'image'	=> array('property', 'location_tenant')
								)
							)
				)      
			);
			
			$menus['folders'] = phpgwapi_menu::get_categories('bergen');

			$GLOBALS['phpgw_info']['flags']['currentapp'] = $incoming_app;

			return $menus;
		}
	}
