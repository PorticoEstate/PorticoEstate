<?php
	class booking_menu
	{
		function get_menu()
		{
			$bodoc = CreateObject('booking.bodocumentation');
			$manual  =  $bodoc->so->getBackendDoc();	

			$incoming_app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$GLOBALS['phpgw_info']['flags']['currentapp'] = 'booking';
			$menus = array();

			$menus['navbar'] = array
			(
				'booking' => array
				(
					'text'	=> lang('Booking'),
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
					'text'	=> lang('Dashboard'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uidashboard.index') ),
					'image'	=> array('property', 'location'),
				),
				'messages' => array
				(
					'text'	=> lang('Messages'),
				 	'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uisystem_message.index') ),
				                        'image'	=> array('property', 'location'),
				),
				// 'applications' => array
				// (
				// 	'text'	=> lang('Applications'),
				// 	'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uibooking.applications') ),
				//                     'image'	=> array('property', 'location'),
				// ),
				'applications' => array
				(
					'text'	=> lang('Applications'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uiapplication.index') ),
                    'image'	=> array('property', 'project_request'),
					'children' => array(
						'allocations' => array
						(
							'text'	=> lang('Allocations'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uiallocation.index') ),
							'image'	=> array('property', 'project_request'),
						),
						'bookings' => array
						(
							'text'	=> lang('Bookings'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uibooking.index') ),
							'image'	=> array('property', 'project_request'),
						),
						'events' => array
						(
							'text'	=> lang('Events'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uievent.index') ),
							'image'	=> array('property', 'project_request'),
						),
						'massbookings' => array
						(
							'text'	=> lang('Bookings and allocations'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uimassbooking.index') ),
							'image'	=> array('property', 'project_request'),
						),
					)
				),
				'buildings' => array
				(
					'text'	=> lang('Buildings'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uibuilding.index') ),
                    'image'	=> array('property', 'location_1'),
					'children' => array
					(
						'documents' => array
						(
							'text'	=> lang('Documents'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uidocument_building.index') ),
		                    'image'	=> array('property', 'documentation'),
						),
						'permissions' => array
						(
							'text'	=> lang('Permissions'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uipermission_building.index') ),
		                    'image'	=> array('property', 'agreement'),
						),
						'resources' => array
						(
							'text'	=> lang('Resources'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uiresource.index') ),
		                    'image'	=> array('property', 'location'),
							'children' => array
							(
								'documents' => array
								(
									'text'	=> lang('Documents'),
									'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uidocument_resource.index') ),
				                    'image'	=> array('property', 'documentation'),
								),
								'permissions' => array
								(
									'text'	=> lang('Permissions'),
									'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uipermission_resource.index') ),
									'image'	=> array('property', 'agreement'),
								),
							)
						),
						'seasons' => array
						(
							'text'	=> lang('Seasons'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uiseason.index') ),
		                    'image'	=> array('property', 'location_gabnr'),
							'children' => array
							(
								'permissions' => array
								(
									'text'	=> lang('Permissions'),
									'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uipermission_season.index') ),
									'image'	=> array('property', 'agreement'),
								),
							),
						),
					)
				),
				// 'events' => array
				// (
				// 	'text'	=> lang('Events'),
				// 	'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uievent.index') ),
				//                     'image'	=> array('property', 'location'),
				// ),
				'organizations' => array
				(
					'text'	=> lang('Organizations'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uiorganization.index') ),
                    'image'	=> array('property', 'location_tenant'),
					'children' => array
					(
						'groups' => array
						(
							'text'	=> lang('Groups'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uigroup.index') ),
		                    'image'	=> array('property', 'location_tenant'),
						)
					)
				),
				// 'costs' => array
				// (
				// 	'text'	=> lang('Costs'),
				// 	'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uicost.index') ),
				//                     'image'	=> array('property', 'location'),
				// )
				
				'invoice_center' => array
				(
					'text'	=> lang('Invoice Data Exports'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uicompleted_reservation.index')),
					'image'	=> array('property', 'invoice'),
					'children' => array
					(
						'completed_reservations' => array
						(
							'text'	=> lang('Completed'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uicompleted_reservation.index') ),
							'image'	=> array('property', 'invoice'),
						),
						'exported_files' => array
						(
							'text'	=> lang('Exported Files'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uicompleted_reservation_export.index')),
							'image'	=> array('property', 'invoice'),
						),
						'generated_files' => array
						(
							'text'	=> lang('Generated Files'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uicompleted_reservation_export_file.index')),
							'image'	=> array('property', 'invoice'),
						)
					)
				),
				
				'mailing' => array
				(
					'text'	=> lang('Send e-mail'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uisend_email.index')),
					'image'	=> array('property', 'helpdesk'),
				),

                'reportcenter' => array
                (   
                    'text'  => lang('Reports'),
                    'url'   => $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uireports.index') ),
                    'image' => array('property', 'report'),
                    'children' => array
                    (   
                        'participants' => array
                        (
                            'text'  => lang('Participants'),
                            'url'   => $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uireports.participants') ),
							'image' => array('property', 'report'),
                        ),
                        'free_time' => array
                        (
                            'text'  => lang('Free time'),
                            'url'   => $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uireports.freetime') ),
							'image' => array('property', 'report'),
                        ),
#                        'free_time2' => array
#                        (
#                            'text'  => lang('Free time2'),
#                            'url'   => $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uireports.freetime2') ),
#							'image' => array('property', 'report'),
#                        )
                    )       
                ),      
			);
			if ( isset($GLOBALS['phpgw_info']['user']['apps']['admin']) )
			{
				$menus['navigation']['settings'] = array
					(
						'text'	=> lang('Settings'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uipermission_root.index', 'appname' => 'booking')),
						'image' => array('admin', 'navbar'),
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
								'text'	=> lang('Age group'),
								'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiagegroup.index', 'appname' => 'booking') )
							),
							'account_code_sets'	=> array
							(
								'text'	=> lang('Account Codes'),
								'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiaccount_code_set.index', 'appname' => 'booking') )
							),
							'account_code_dimensions'	=> array
							(
								'text'	=> lang('Account Code Dimension'),
								'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiaccount_code_dimension.index', 'appname' => 'booking') )
							),
							'async_settings'	=> array
							(
								'text'	=> lang('Asynchronous Tasks'),
								'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiasync_settings.index', 'appname' => 'booking') )
							),
							'documentation'	=> array
							(
								'text'	=> lang('Documentation'),
								'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uidocumentation.index', 'appname' => 'booking') )
							),
							'mail_settings'	=> array
							(
								'text'	=> lang('Mail Settings'),
								'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uimail_settings.index', 'appname' => 'booking') )
							),
							'event_mail_settings'	=> array
							(
								'text'	=> lang('Event Mail Settings'),
								'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uievent_mail_settings.index', 'appname' => 'booking') )
							),
							'application_settings'	=> array
							(
								'text'	=> lang('Application Settings'),
								'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiapplication_settings.index', 'appname' => 'booking') )
							),
							'office'	=> array
							(
								'text'	=> lang('office'),
								'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uigeneric.index', 'type' => 'bb_office') ),
								'children' => array
								(
									'office_user'	=> array
									(
										'text'	=> lang('office user'),
										'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uigeneric.index', 'type' => 'bb_office_user') )
									)
								)
							),
						)
					);
					if ($manual !== null)
					{
						$menus['navigation']['documentation'] = array
						(
							'text'	=> lang('Documentation'),
							'url'	=> $manual,
							'image' => array('property', 'documentation'),
						);
					}
			}

			if ( $GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin')
			|| $GLOBALS['phpgw']->acl->check('admin', phpgwapi_acl::ADD, 'booking'))
			{
				$menus['admin'] = array
				(
					'index'	=> array
					(
						'text'	=> lang('Configuration'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiconfig.index', 'appname' => 'booking') )
					),
					'permissions'	=> array
					(
						'text'	=> lang('Root Permissions'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uipermission_root.index', 'appname' => 'booking') )
					),
					'acl'	=> array
					(
						'text'	=> lang('Configure Access Permissions'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uiadmin_acl.list_acl', 'acl_app' => 'booking') )
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
					),
					'async_settings'	=> array
					(
						'text'	=> lang('Asynchronous Tasks'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiasync_settings.index', 'appname' => 'booking') )
					),
					'settings'	=> array
					(
						'text'	=> lang('Settings'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uisettings.index', 'appname' => 'booking') )
					),
				);
			}

			
			$menus['folders'] = phpgwapi_menu::get_categories('bergen');

			$GLOBALS['phpgw_info']['flags']['currentapp'] = $incoming_app;

			return $menus;
		}
	}
