<?php
	class booking_menu
	{
		function get_menu()
		{
			$menus = array();

			$menus['navbar'] = array
			(
				'booking' => array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Booking', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uidashboard.index') ),
                    'image'	=> array('property', 'location'),
					'order'	=> 10,
					'group'	=> 'office'
				)
			);

			$menus['navigation'] =  array
			(
				'dashboard' => array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Dashboard', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uidashboard.index') ),
				                    'image'	=> array('property', 'location'),
				),
				// 'applications' => array
				// (
				// 	'text'	=> $GLOBALS['phpgw']->translation->translate('Applications', array(), true),
				// 	'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uibooking.applications') ),
				//                     'image'	=> array('property', 'location'),
				// ),
				'applications' => array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Applications', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uiapplication.index') ),
                    'image'	=> array('property', 'location'),
					'children' => array(
						'allocations' => array
						(
							'text'	=> $GLOBALS['phpgw']->translation->translate('Allocations', array(), true),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uiallocation.index') ),
						                    'image'	=> array('property', 'location'),
						),
						'bookings' => array
						(
							'text'	=> $GLOBALS['phpgw']->translation->translate('Bookings', array(), true),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uibooking.index') ),
						                    'image'	=> array('property', 'location'),
						),
					)
				),
				'buildings' => array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Buildings', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uibuilding.index') ),
                    'image'	=> array('property', 'location'),
					'children' => array
					(
						'documents' => array
						(
							'text'	=> $GLOBALS['phpgw']->translation->translate('Documents', array(), true),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uidocument_building.index') ),
		                    'image'	=> array('property', 'documentation'),
						),
						'permissions' => array
						(
							'text'	=> $GLOBALS['phpgw']->translation->translate('Permissions', array(), true),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uipermission_building.index') ),
						),
						'resources' => array
						(
							'text'	=> $GLOBALS['phpgw']->translation->translate('Resources', array(), true),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uiresource.index') ),
		                    'image'	=> array('property', 'location'),
							'children' => array
							(
								'equipment' => array
								(
									'text'	=> $GLOBALS['phpgw']->translation->translate('Equipment', array(), true),
									'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uiequipment.index') ),
									'image'	=> array('property', 'location'),
								),
								'documents' => array
								(
									'text'	=> $GLOBALS['phpgw']->translation->translate('Documents', array(), true),
									'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uidocument_resource.index') ),
				                    'image'	=> array('property', 'documentation'),
								),
								'permissions' => array
								(
									'text'	=> $GLOBALS['phpgw']->translation->translate('Permissions', array(), true),
									'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uipermission_resource.index') ),
								),
							)
						),
						'seasons' => array
						(
							'text'	=> $GLOBALS['phpgw']->translation->translate('Seasons', array(), true),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uiseason.index') ),
		                    'image'	=> array('property', 'location'),
							'children' => array
							(
								'permissions' => array
								(
									'text'	=> $GLOBALS['phpgw']->translation->translate('Permissions', array(), true),
									'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uipermission_season.index') ),
								),
							),
						),
					)
				),
				// 'events' => array
				// (
				// 	'text'	=> $GLOBALS['phpgw']->translation->translate('Events', array(), true),
				// 	'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uievent.index') ),
				//                     'image'	=> array('property', 'location'),
				// ),
				'organizations' => array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Organizations', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uiorganization.index') ),
                    'image'	=> array('property', 'location'),
					'children' => array
					(
						'groups' => array
						(
							'text'	=> $GLOBALS['phpgw']->translation->translate('Groups', array(), true),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uigroup.index') ),
		                    'image'	=> array('property', 'location'),
						)
					)
				),
				// 'costs' => array
				// (
				// 	'text'	=> $GLOBALS['phpgw']->translation->translate('Costs', array(), true),
				// 	'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uicost.index') ),
				//                     'image'	=> array('property', 'location'),
				// )
				'contacts' => array
				(
				 	'text'  => $GLOBALS['phpgw']->translation->translate('Contacts', array(), true),
					'url'   => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uicontactperson.index') ),
					'image' => array('property', 'location'),
				),
				'settings' => array
				(
					'text'	=> lang('Settings'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uipermission_root.index', 'appname' => 'booking')),
					'children' => array
					(
						'permissions'	=> array
						(
							'text'	=> lang('Root Permissions'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uipermission_root.index', 'appname' => 'booking') )
						),
						'activity'	=> array
						(
							'text'	=> lang('Activity'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiactivity.index', 'appname' => 'booking') )
						),
						'audience'	=> array
						(
							'text'	=> lang('Audience'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiaudience.index', 'appname' => 'booking') )
						),
						'agegroup'	=> array
						(
							'text'	=> lang('Agegroup'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiagegroup.index', 'appname' => 'booking') )
						)
					)
				),
			);
				// $menus['admin'] = array
				// (
				// 	'permissions'	=> array
				// 	(
				// 		'text'	=> lang('Root Permissions'),
				// 		'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uipermission_root.index', 'appname' => 'booking') )
				// 	),
				// 	'activity'	=> array
				// 	(
				// 		'text'	=> lang('Activity'),
				// 		'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiactivity.index', 'appname' => 'booking') )
				// 	),
				// 	'audience'	=> array
				// 	(
				// 		'text'	=> lang('Audience'),
				// 		'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiaudience.index', 'appname' => 'booking') )
				// 	),
				// 	'agegroup'	=> array
				// 	(
				// 		'text'	=> lang('Agegroup'),
				// 		'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiagegroup.index', 'appname' => 'booking') )
				// 	)
				// );
			$menus['folders'] = phpgwapi_menu::get_categories('bergen');

			return $menus;
		}
	}
