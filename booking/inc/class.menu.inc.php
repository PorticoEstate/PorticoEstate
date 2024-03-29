<?php

	class booking_menu
	{

		function get_menu()
		{
			$bodoc = CreateObject('booking.bodocumentation');
			$manual = $bodoc->so->getBackendDoc();

			$incoming_app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$GLOBALS['phpgw_info']['flags']['currentapp'] = 'booking';
			$menus = array();

			$menus['navbar'] = array
				(
				'booking' => array
					(
					'text' => lang('Booking'),
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uidashboard.index')),
					'image' => array('property', 'location'),
					'order' => 10,
					'group' => 'office'
				)
			);

			$menus['preferences'] = array
			(
				array
				(
					'text' => $GLOBALS['phpgw']->translation->translate('Preferences', array(), true),
					'url' => $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname' => 'booking',
						'type' => 'user'))
				)
			);
			$menus['navigation'] = array
				(
				'dashboard' => array
					(
					'text' => lang('Dashboard'),
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uidashboard.index')),
					'image' => array('property', 'location'),
				),
				'messages' => array
					(
					'text' => lang('Messages'),
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uisystem_message.index')),
					'image' => array('property', 'location'),
				),
				// 'applications' => array
				// (
				// 	'text'	=> lang('Applications'),
				// 	'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uibooking.applications') ),
				//                     'image'	=> array('property', 'location'),
				// ),
				'applications' => array
					(
					'text' => lang('Applications'),
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiapplication.index')),
					'image' => array('property', 'project_request'),
					'children' => array(
						'applications' => array
							(
							'text' => lang('Applications'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiapplication.index')),
							'image' => array('property', 'project_request'),
							'icon' => 'fas fa-2x fa-file-alt'
						),
						'allocations' => array
							(
							'text' => lang('Allocations'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiallocation.index')),
							'image' => array('property', 'project_request'),
						),
						'bookings' => array
							(
							'text' => lang('Bookings'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uibooking.index')),
							'image' => array('property', 'project_request'),
						),
						'events' => array
							(
							'text' => lang('Events'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uievent.index')),
							'image' => array('property', 'project_request'),
						),
						'massbookings' => array
							(
							'text' => lang('Bookings and allocations'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uimassbooking.index')),
							'image' => array('property', 'project_request'),
						),
					)
				),
				'buildings' => array
					(
					'text' => lang('Buildings'),
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uibuilding.index')),
					'image' => array('property', 'location_1'),
					'icon' => 'fa fa-2x fa-building',
					'children' => array
						(
						'buildings' => array
							(
							'text' => lang('Buildings'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uibuilding.index')),
							'image' => array('property', 'location_1'),
							'icon' => 'fa fa-2x fa-building'
						),
						'documents' => array
							(
							'text' => lang('Documents'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uidocument_building.index')),
							'image' => array('property', 'documentation'),
						),
						'permissions' => array
							(
							'text' => lang('Permissions'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uipermission_building.index')),
							'image' => array('property', 'agreement'),
						),
						'resources' => array
							(
							'text' => lang('Resources'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiresource.index')),
							'image' => array('property', 'location'),
							'children' => array
								(
								'resources' => array
									(
									'text' => lang('Resources'),
									'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiresource.index')),
									'image' => array('property', 'location'),
								),
								'documents' => array
									(
									'text' => lang('Documents'),
									'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uidocument_resource.index')),
									'image' => array('property', 'documentation'),
								),
								'permissions' => array
									(
									'text' => lang('Permissions'),
									'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uipermission_resource.index')),
									'image' => array('property', 'agreement'),
								),
							)
						),
						'seasons' => array
							(
							'text' => lang('Seasons'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiseason.index')),
							'image' => array('property', 'location_gabnr'),
							'children' => array
								(
								'seasons' => array
									(
									'text' => lang('Seasons'),
									'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiseason.index')),
									'image' => array('property', 'location_gabnr'),
								),
								'permissions' => array
									(
									'text' => lang('Permissions'),
									'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uipermission_season.index')),
									'image' => array('property', 'agreement'),
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
					'text' => lang('Organizations'),
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiorganization.index')),
					'image' => array('property', 'location_tenant'),
					'children' => array
						(
						'organizations' => array
							(
							'text' => lang('Organizations'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiorganization.index')),
							'image' => array('property', 'location_tenant'),
							'icon' => 'fa fa-2x fa-sitemap'
						),
						'documents' => array
							(
							'text' => lang('Documents'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uidocument_organization.index')),
							'image' => array('property', 'documentation'),
						),
						'groups' => array
							(
							'text' => lang('Groups'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uigroup.index')),
							'image' => array('property', 'location_tenant'),
						),
						'delegates' => array
							(
							'text' => lang('delegates'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uidelegate.index')),
							'image' => array('property', 'location_tenant'),
						)
					)
				),
				'users' => array
					(
					'text' => lang('users'),
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiuser.index')),
					'image' => array('property', 'location_tenant'),
					'icon' => 'fas fa-2x fa-address-card',
					'children' => array
						(
							'collect_users' => array
							(
							'text' => lang('collect users'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiuser.collect_users')),
							'image' => array('property', 'location_tenant'),
						),
						'update_user_address' => array
							(
							'text' => lang('update user address'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiuser.update_user_address')),
							'image' => array('property', 'location_tenant'),
						),
						'export_customer' => array
							(
							'text' => lang('export customer'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiuser.export_customer')),
							'image' => array('property', 'location_tenant'),
						),
					)
				),
				'commerce' => array
				(
					'text'	=> lang('commerce'),
				 	'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uiarticle_mapping.index') ),
				                     'image'	=> array('property', 'article'),
					'children'	=> array(
						'article' => array
						(
							'text'	=> lang('article'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'booking.uiarticle_mapping.index') ),
											 'image'	=> array('property', 'article'),
						),
						'service' => array
						(
							'text'	=> lang('service'),
									'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiservice.index')),
									'image'	=> array('property', 'service'),
						),
						'article_group'		 => array
							(
							'text'	 => lang('article group'),
							'url'	 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uigeneric.index',
								'type'		 => 'article_group'))
						),
						'accounting_tax'		 => array
							(
							'text'	 => lang('tax code'),
							'url'	 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uigeneric.index',
								'type'		 => 'tax'))
						),

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
					'text' => lang('Invoice Data Exports'),
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uicompleted_reservation.index')),
					'image' => array('property', 'invoice'),
					'children' => array
						(
						'completed_reservations' => array
							(
							'text' => lang('completed reservations'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uicompleted_reservation.index')),
							'image' => array('property', 'invoice'),
						),
						'exported_files' => array
							(
							'text' => lang('Exported Files'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uicompleted_reservation_export.index')),
							'image' => array('property', 'invoice'),
						),
						'generated_files' => array
							(
							'text' => lang('Generated Files'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uicompleted_reservation_export_file.index')),
							'image' => array('property', 'invoice'),
						)
					)
				),
				'mailing' => array
					(
					'text' => lang('Send e-mail'),
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uisend_email.index')),
					'image' => array('property', 'helpdesk'),
				),
				'reportcenter' => array
					(
					'text' => lang('Reports'),
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uireports.index')),
					'image' => array('property', 'report'),
					'children' => array
						(
						'reports' => array
							(
							'text' => lang('Reports'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uireports.index')),
							'image' => array('property', 'report'),
						),
						'participants' => array
							(
							'text' => lang('Participants'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uireports.participants')),
							'image' => array('property', 'report'),
						),
						'free_time' => array
							(
							'text' => lang('Free time'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uireports.freetime')),
							'image' => array('property', 'report'),
						),
						'add_generic' => array
							(
							'text' => 'TESTING::add_generic',
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uireports.add')),
							'image' => array('property', 'report'),
						)
					)
				),
			);
			if (isset($GLOBALS['phpgw_info']['user']['apps']['admin']))
			{
				$menus['navigation']['settings'] = array
					(
					'text' => lang('Settings'),
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uipermission_root.index',
						'appname' => 'booking')),
					'image' => array('admin', 'navbar'),
					'children' => array
					(
						'permissions' => array
							(
							'text' => lang('Root Permissions'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uipermission_root.index',
								'appname' => 'booking'))
						),
						'rescategory' => array
							(
							'text' => lang('Resource categories'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uirescategory.index',
								'appname' => 'booking'))
						),
						'activity' => array
							(
							'text' => lang('Activity'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiactivity.index',
								'appname' => 'booking'))
						),
						'facility' => array
							(
							'text' => lang('Facilities'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uifacility.index',
								'appname' => 'booking'))
						),
						'custom_fields_example' => array
							(
							'text' => 'TEMPORARY:custom fields example',
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiapplication.custom_fields_example'))
						),
						'custom_field_groups' => array
							(
							'text' => lang('custom field groups'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_attribute_group',
								'appname' => 'booking', 'menu_selection' => 'booking::settings::custom_field_groups'))
						),
						'custom_fields' => array
							(
							'text' => lang('custom fields'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_attribute',
								'appname' => 'booking', 'menu_selection' => 'booking::settings::custom_fields'))
						),
						'audience' => array
							(
							'text' => lang('Audience'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiaudience.index',
								'appname' => 'booking'))
						),
						'agegroup' => array
							(
							'text' => lang('Age group'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiagegroup.index',
								'appname' => 'booking'))
						),
						'account_code_sets' => array
							(
							'text' => lang('Account Codes'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiaccount_code_set.index',
								'appname' => 'booking'))
						),
						'account_code_dimensions' => array
							(
							'text' => lang('Account Code Dimension'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiaccount_code_dimension.index',
								'appname' => 'booking'))
						),
						'async_settings' => array
							(
							'text' => lang('Asynchronous Tasks'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiasync_settings.index',
								'appname' => 'booking'))
						),
						'documentation' => array
							(
							'text' => lang('Documentation'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uidocumentation.index',
								'appname' => 'booking'))
						),
						'mail_settings' => array
							(
							'text' => lang('Mail Settings'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uimail_settings.index',
								'appname' => 'booking'))
						),
						'event_mail_settings' => array
							(
							'text' => lang('Event Mail Settings'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uievent_mail_settings.index',
								'appname' => 'booking'))
						),
						'application_settings' => array
							(
							'text' => lang('Application Settings'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiapplication_settings.index',
								'appname' => 'booking'))
						),
						'e_lock_system' => array(
							'text' => lang('e_lock_system'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uigeneric.index',
								'type' => 'e_lock_system')),
						),
						'office' => array
							(
							'text' => lang('office'),
							'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uigeneric.index',
								'type' => 'bb_office')),
							'children' => array
								(
								'office' => array
									(
									'text' => lang('office'),
									'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uigeneric.index',
										'type' => 'bb_office')),
								),
								'office_user' => array
									(
									'text' => lang('office user'),
									'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uigeneric.index',
										'type' => 'bb_office_user'))
								)
							)
						),
					)
				);
				if ($manual !== null)
				{
					$menus['navigation']['documentation'] = array
						(
						'text' => lang('Documentation'),
						'url' => $manual,
						'image' => array('property', 'documentation'),
					);
				}
			}

			if ($GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin') || $GLOBALS['phpgw']->acl->check('admin', phpgwapi_acl::ADD, 'booking'))
			{
				$menus['admin'] = array
					(
					'index' => array
						(
						'text' => lang('Configuration'),
						'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiconfig.index',
							'appname' => 'booking'))
					),
					'customconfig' => array
					(
						'text'			 => lang('custom config'),
						'nav_location'	 => 'navbar#' . $GLOBALS['phpgw']->locations->get_id('booking', 'run'),
						'url'			 => $GLOBALS['phpgw']->link('/index.php', array('menuaction'	 => 'admin.uiconfig2.index',
							'location_id'	 => $GLOBALS['phpgw']->locations->get_id('booking', 'run')))
					),
					'permissions' => array
						(
						'text' => lang('Root Permissions'),
						'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uipermission_root.index',
							'appname' => 'booking'))
					),
					'acl' => array
						(
						'text' => lang('Configure Access Permissions'),
						'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uiadmin_acl.list_acl',
							'acl_app' => 'booking'))
					),
					'rescategory' => array
						(
						'text' => lang('Resource categories'),
						'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uirescategory.index',
							'appname' => 'booking'))
					),
					'activity' => array
						(
						'text' => lang('Activity'),
						'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiactivity.index',
							'appname' => 'booking'))
					),
					'facility' => array
						(
						'text' => lang('Facilities'),
						'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uifacility.index',
							'appname' => 'booking'))
					),
					'audience' => array
						(
						'text' => lang('Audience'),
						'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiaudience.index',
							'appname' => 'booking'))
					),
					'agegroup' => array
						(
						'text' => lang('Agegroup'),
						'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiagegroup.index',
							'appname' => 'booking'))
					),
					'async_settings' => array
						(
						'text' => lang('Asynchronous Tasks'),
						'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uiasync_settings.index',
							'appname' => 'booking'))
					),
					'settings' => array
						(
						'text' => lang('Settings'),
						'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'booking.uisettings.index',
							'appname' => 'booking'))
					),
				);
			}


			$menus['folders'] = phpgwapi_menu::get_categories('bergen');

			$GLOBALS['phpgw_info']['flags']['currentapp'] = $incoming_app;

			return $menus;
		}
	}