<?php
	phpgw::import_class('rental.uicommon');

	class rental_menu
	{
		function get_menu()
		{
			$incoming_app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$GLOBALS['phpgw_info']['flags']['currentapp'] = 'rental';

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
