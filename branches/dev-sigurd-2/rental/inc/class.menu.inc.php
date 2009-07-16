<?php

	class rental_menu
	{
		function get_menu()
		{
			$menus = array();

			$menus['navbar'] = array
			(
				'rental' => array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('rental_menu_rental', array(), true),
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
					'text'	=> $GLOBALS['phpgw']->translation->translate('rental_menu_contracts', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'rental.uicontract.index') ),
                    'image'	=> array('rental', 'text-x-generic'),
					/*'children' => array(
						'party' => array
						(
							'text'	=> $GLOBALS['phpgw']->translation->translate('party', array(), true),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'rental.uicontractcenant.index') ),
							'image'	=> array('property', 'location'),
						)
					)*/
				),
				'rentalcomposites' => array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('rental_menu_rc', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'rental.uicomposite.index') ),
                    'image'	=> array('rental', 'go-home'),
					'children' => array(
						'orphan_units' => array
						(
							'text'	=> $GLOBALS['phpgw']->translation->translate('rental_menu_orphan_units', array(), true),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'rental.uicomposite.orphan_units') ),
							'image'	=> array('rental', 'edit-clear'),
						)
					)
				),
				'parties' => array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('rental_menu_parties', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'rental.uiparty.index') ),
                    'image'	=> array('rental', 'x-office-address-book')
				),
				'economy' => array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('rental_menu_economy', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'rental.uieconomy.index') ),
                    'image'	=> array('rental', 'x-office-spreadsheet'),
				),
				'reports' => array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('rental_menu_reports', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'rental.uireports.index') ),
                    'image'	=> array('rental', 'x-office-document'),
				)
			);
			
			$menus['admin'] = array
			(
				'contract_type_list'	=> array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('rental_menu_contract_types', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'rental.uiadmin.contract_type_list', 'appname' => 'rental') )
				),
				'price_item_list'	=> array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('rental_menu_price_list', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'rental.uiadmin.price_item_list', 'appname' => 'rental') )
				)
			);
			
			$menus['folders'] = phpgwapi_menu::get_categories('bergen');

			return $menus;
		}
	}
?>