<?php
	phpgw::import_class('rental.uicommon');

	class rental_menu
	{
		function get_menu()
		{
			$incoming_app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$GLOBALS['phpgw_info']['flags']['currentapp'] = 'rental';
			
			$config = CreateObject('phpgwapi.config','rental');
			$config->read();
			$use_fellesdata = $config->config_data['use_fellesdata'];

			$menus = array();

			$menus['navbar'] = array
			(
				'rental' => array
				(
					'text'	=> lang('rental'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'rental.uifrontpage.index') ),
					'image'	=> array('rental', 'user-home'),
					'order'	=> 10,
					'group'	=> 'office'
				)
			);
			
			
			if(
				$GLOBALS['phpgw']->acl->check(rental_uicommon::LOCATION_IN,PHPGW_ACL_ADD,'rental') ||
				$GLOBALS['phpgw']->acl->check(rental_uicommon::LOCATION_OUT,PHPGW_ACL_ADD,'rental')	||
				$GLOBALS['phpgw']->acl->check(rental_uicommon::LOCATION_INTERNAL,PHPGW_ACL_ADD,'rental')
			)
			{
				$billing = array (
					'invoice' => array
					(
						'text'	=> lang('invoice_menu'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'rental.uibilling.index', 'appname' => 'rental') ),
						'image'	=> array('rental', 'x-office-document')
					),
					'price_item_list'	=> array
					(
						'text'	=> lang('price_list'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'rental.uiprice_item.index', 'appname' => 'rental') ),
						'image'	=> array('rental', 'x-office-spreadsheet'),
						'children'	=> array(
								'manual_adjustment' => array
								(
									'text'	=> lang('manual_adjustment'),
									'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'rental.uiprice_item.manual_adjustment', 'appname' => 'rental') ),
									'image'	=> array('rental', 'x-office-spreadsheet')
								)
							)
					),
					'adjustment'	=> array
					(
						'text'	=> lang('adjustment'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'rental.uiadjustment.index', 'appname' => 'rental') ),
						'image'	=> array('rental', 'x-office-spreadsheet')
					)
				);
				
				$sync_choices = array (
					'sync_org_unit' => array
					(
						'text'	=> lang('sync_org_unit'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'rental.uiparty.sync', 'sync' => 'org_unit', 'appname' => 'rental') ),
						'image'	=> array('rental', 'x-office-document')
					),
					'sync_resp_and_service' => array
					(
						'text'	=> lang('sync_resp_and_service'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'rental.uiparty.sync','sync' => 'resp_and_service', 'appname' => 'rental') ),
						'image'	=> array('rental', 'x-office-document')
					),
					'sync_res_units' => array
					(
						'text'	=> lang('sync_res_units'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'rental.uiparty.sync', 'sync' => 'res_unit_number', 'appname' => 'rental') ),
						'image'	=> array('rental', 'x-office-document')
					),
					'sync_identifier' => array
					(
						'text'	=> lang('sync_identifier'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'rental.uiparty.sync', 'sync' => 'identifier','appname' => 'rental') ),
						'image'	=> array('rental', 'x-office-document')
					)
				);

				$sub_parties = array(
					'sync' => array 
					(
						'text'	=> lang('sync_menu'),
						'url'	=> '',
						'image'	=> array('rental', 'x-office-document'),
						'children' => $sync_choices
					),
					'resultunit' => array
					(
						'text' => lang('delegates'),
						'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'rental.uiresultunit.index','appname' => 'rental') ),
						'image' => array('rental', 'system-users')
					)
				);
			}
			
			$menus['navigation'] =  array
			(
				'contracts' => array
				(
					'text'	=> lang('contracts'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'rental.uicontract.index') ),
					'image'	=> array('rental', 'text-x-generic'),
					'children'	=> $billing
				),
				'composites' => array
				(
					'text'	=> lang('rc'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'rental.uicomposite.index') ),
					'image'	=> array('rental', 'go-home')
				),
				'parties' => array
				(
					'text'	=> lang('parties'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'rental.uiparty.index') ),
					'image'	=> array('rental', 'x-office-address-book')
				)
			);
			
			if($use_fellesdata){
				$menus['navigation']['parties']['children'] = $sub_parties;
			}

			$menus['admin'] = array
			(
				'index'	=> array
				(
					'text'	=> lang('Configuration'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiconfig.index', 'appname' => 'rental') )
				),
				'acl'	=> array
				(
					'text'	=> lang('Configure Access Permissions'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uiadmin_acl.list_acl', 'acl_app' => 'rental') )
				),
				'import'	=> array
				(
					'text'	=> lang('facilit_import'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'rental.uiimport.index', 'appname' => 'rental') ),
					'image'	=> array('rental', 'document-save')
				),
				'import_adjustments'	=> array
				(
					'text'	=> lang('import_adjustments'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'rental.uiimport.import_regulations', 'appname' => 'rental') ),
					'image'	=> array('rental', 'document-save')
				)
			);
			
			$menus['folders'] = phpgwapi_menu::get_categories('bergen');
			
			$menus['preferences'] = array
			(
				array
				(
					'text'	=> lang('Preferences'),
					'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname' => 'rental', 'type'=> 'user') )
				),
				array
				(
				'text'	=> lang('Grant Access'),
				'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uiadmin_acl.list_acl', 'acl_app'=> 'rental'))
				)
			);
			$GLOBALS['phpgw_info']['flags']['currentapp'] = $incoming_app;
			return $menus;
		}
	}
?>
