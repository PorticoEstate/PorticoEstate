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
				 'frontpage' => array
				 (
				 	'text'	=> $GLOBALS['phpgw']->translation->translate('Frontpage', array(), true),
				 	'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'rental.uifrontpage.index') ),
					'image'	=> array('property', 'location'),
				 ),
				'contract' => array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Contract', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'rental.uicontract.index') ),
                    'image'	=> array('property', 'location'),
					'children' => array(
						'tenant' => array
						(
							'text'	=> $GLOBALS['phpgw']->translation->translate('Tenant', array(), true),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'rental.uicontractcenant.index') ),
							'image'	=> array('property', 'location'),
						)
					)
				),
				'rentalobject' => array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Rental object', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'rental.uirentalobject.index') ),
                    'image'	=> array('property', 'location')
				),
				'tenant' => array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Tenant', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'rental.uitenant.index') ),
                    'image'	=> array('property', 'location')
				),
				'economy' => array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Economy', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'rental.uieconomy.index') ),
                    'image'	=> array('property', 'location'),
				),
				'report' => array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Report', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'rental.uireport.index') ),
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