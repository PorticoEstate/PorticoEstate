<?php

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
					'text'	=> $GLOBALS['phpgw']->translation->translate('rental', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'rental.uifrontpage.index') ),
					'image'	=> array('rental', 'user-home'),
					'order'	=> 10,
					'group'	=> 'office'
				)
			);

			$menus['navigation'] =  array
			(
				'contracts' => array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('contracts', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'rental.uicontract.index') ),
					'image'	=> array('rental', 'text-x-generic')
				),
				'composites' => array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('rc', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'rental.uicomposite.index') ),
					'image'	=> array('rental', 'go-home'),
					'children' => array(
						'orphan_units' => array
						(
							'text'	=> $GLOBALS['phpgw']->translation->translate('orphan_units', array(), true),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'rental.uicomposite.orphan_units') ),
							'image'	=> array('rental', 'edit-clear'),
						)
					)
				),
				'parties' => array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('parties', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'rental.uiparty.index') ),
					'image'	=> array('rental', 'x-office-address-book')
				),
				'economy' => array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('economy', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'rental.uieconomy.index') ),
					'image'	=> array('rental', 'x-office-spreadsheet'),
				),
				'reports' => array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('reports', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'rental.uireports.index') ),
					'image'	=> array('rental', 'x-office-document'),
				)
			);

			$menus['admin'] = array
			(
				'index'	=> array
				(
					'text'	=> lang('Configuration'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiconfig.index', 'appname' => 'rental') )
				),
				'price_item_list'	=> array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('price_list', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'rental.uiprice_item.index', 'appname' => 'rental') ),
					'image'	=> array('rental', 'x-office-spreadsheet')
				),
				'acl'	=> array
				(
					'text'	=> lang('Configure Access Permissions'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uiadmin_acl.list_acl', 'acl_app' => 'rental') )
				),
				'billing'	=> array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('invoice', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'rental.uibilling.index', 'appname' => 'rental') ),
					'image'	=> array('rental', 'x-office-document')
				)
			);
			
			$menus['folders'] = phpgwapi_menu::get_categories('bergen');
			
			$menus['preferences'] = array
			(
				array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Preferences', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname' => 'rental', 'type'=> 'user') )
				),
				array
				(
				'text'	=> $GLOBALS['phpgw']->translation->translate('Grant Access', array(), true),
				'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uiadmin_acl.list_acl', 'acl_app'=> 'rental'))
				)
			);
			$GLOBALS['phpgw_info']['flags']['currentapp'] = $incoming_app;
			return $menus;
		}
	}
?>