<?php
	class controller_menu
	{
		function get_menu()
		{
			$incoming_app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$GLOBALS['phpgw_info']['flags']['currentapp'] = 'controller';
			$menus = array();

			$menus['navbar'] = array
			(
				'controller' => array
				(
					'text'	=> lang('Controller'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'controller.uicontrol.index') ),
                    'image'	=> array('property', 'location'),
					'order'	=> 10,
					'group'	=> 'office'
				)
			);

			$menus['navigation'] =  array
			(
				'control' => array
				(
					'text'	=> lang('Control'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'controller.uicontrol.index') ),
		            'image'	=> array('property', 'location_1'),
				),
				'control_item' => array
				(
					'text'	=> lang('Control_item'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'controller.uicontrol_item.index') ),
		            'image'	=> array('property', 'location_1'),
					'children'	=> array(
								'control_item_list' => array
								(
									'text'	=> lang('control_item_list'),
									'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol_item.display_control_items', 'appname' => 'controller') ),
									'image'	=> array('rental', 'x-office-spreadsheet')
								)
							)
				),
				'control_item2' => array
				(
					'text'	=> lang('Control_item') . 2,
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'controller.uicontrol_item2.index') ),
		            'image'	=> array('property', 'location_1'),
					'children'	=> array(
								'control_item_list2' => array

								(
									'text'	=> lang('control_item_list') . 2,
									'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol_item2.display_control_items', 'appname' => 'controller') ),
									'image'	=> array('rental', 'x-office-spreadsheet')
								),
								'separate_tabs' => array

								(
									'text'	=> 'example::separate_tabs',
									'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol_item2.separate_tabs') ),
									'image'	=> array('rental', 'x-office-spreadsheet')
								),
								
							)
		        ),
				'procedure' => array
				(
					'text'	=> lang('Procedure'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'controller.uiprocedure.index') ),
		            'image'	=> array('property', 'location_1'),
				)     
			);
			
			$menus['folders'] = phpgwapi_menu::get_categories('bergen');

			$GLOBALS['phpgw_info']['flags']['currentapp'] = $incoming_app;

			return $menus;
		}
	}
