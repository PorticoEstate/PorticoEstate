<?php
	class activitycalendar_menu
	{
		function get_menu()
		{
			$incoming_app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$GLOBALS['phpgw_info']['flags']['currentapp'] = 'activitycalendar';
			$menus = array();

			$menus['navbar'] = array
			(
				'activitycalendar' => array
				(
					'text'	=> lang('Activitycalendar'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'activitycalendar.uidashboard.index') ),
                    'image'	=> array('property', 'location'),
					'order'	=> 10,
					'group'	=> 'office'
				)
			);

			$menus['navigation'] =  array
			(
				'dashboard' => array
				(
					'text'	=> lang('dashboard'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'activitycalendar.uidashboard.index') ),
                    'image'	=> array('property', 'location_tenant'),
				),
				'activities' => array
				(
					'text'	=> lang('Activities'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'activitycalendar.uiactivities.index') ),
                    'image'	=> array('property', 'location_tenant'),
				),
				'arena' => array
				(
					'text'	=> lang('Arena'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'activitycalendar.uiarena.index') ),
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
