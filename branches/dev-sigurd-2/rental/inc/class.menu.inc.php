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
					'text'	=> $GLOBALS['phpgw']->translation->translate('Rental', array(), true),
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
					'text'	=> $GLOBALS['phpgw']->translation->translate('Contracts', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'rental.uicontract.index') ),
                    'image'	=> array('property', 'location'),
					/*'children' => array(
						'tenant' => array
						(
							'text'	=> $GLOBALS['phpgw']->translation->translate('Tenant', array(), true),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'rental.uicontractcenant.index') ),
							'image'	=> array('property', 'location'),
						)
					)*/
				),
				'rentalcomposites' => array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Rental composites', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'rental.uicomposite.index') ),
                    'image'	=> array('property', 'location')
				),
				'tenants' => array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Tenants', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'rental.uitenants.index') ),
                    'image'	=> array('property', 'location')
				),
				'economy' => array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Economy', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'rental.uieconomy.index') ),
                    'image'	=> array('property', 'location'),
				),
				'reports' => array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Reports', array(), true),
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