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
                    'image'	=> array('property', 'location'),
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
                    'image'	=> array('property', 'location'),
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
                    'image'	=> array('property', 'location'),
					'children' => array(
						'orphan_units' => array
						(
							'text'	=> $GLOBALS['phpgw']->translation->translate('rental_menu_orphan_units', array(), true),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'rental.uicomposite.orphan_units') ),
							'image'	=> array('property', 'location'),
						)
					)
				),
				'parties' => array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('rental_menu_parties', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'rental.uiparty.index') ),
                    'image'	=> array('property', 'location')
				),
				'economy' => array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('rental_menu_economy', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'rental.uieconomy.index') ),
                    'image'	=> array('property', 'location'),
				),
				'reports' => array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('rental_menu_reports', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'rental.uireports.index') ),
                    'image'	=> array('property', 'location'),
				)
			);
			$menus['admin'] = array
			(
				'admin'	=> array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Admin', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'rental.uiadmin.index', 'appname' => 'rental') )
				)
			);
			$menus['folders'] = phpgwapi_menu::get_categories('bergen');

			return $menus;
		}
	}
?>