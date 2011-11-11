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
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'controller.uicontrol.view_control_details') ),
                    'image'	=> array('property', 'location'),
					'order'	=> 10,
					'group'	=> 'office'
				)
			);
			
			if($GLOBALS['phpgw']->acl->check('.usertype.superuser',PHPGW_ACL_ADD,'controller'))
			{
				$menus['navigation'] =  array
				(
					'control' => array
					(
						'text'	=> lang('Control'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'controller.uicontrol.control_list') ),
			            'image'	=> array('property', 'location_1')
					),
					'control_item' => array
					(
						'text'	=> lang('Control_item'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'controller.uicontrol_item.index') ),
			            'image'	=> array('property', 'location_1')
			        ),
			        'control_group' => array
					(
						'text'	=> lang('Control_group'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'controller.uicontrol_group.index') ),
			            'image'	=> array('property', 'location_1')
			        ),
			        'procedure' => array
					(
						'text'	=> lang('Procedure'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'controller.uiprocedure.index') ),
			            'image'	=> array('property', 'location_1'),
					),    
					'check_list' => array
					(
						'text'	=> lang('Check_list'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'controller.uicheck_list.index') ),
			            'image'	=> array('property', 'location_1'),
					),
				);
			}
			else
			{
				$menus['navigation'] =  array
				(    
					'check_list' => array
					(
						'text'	=> lang('Check_list'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'controller.uicheck_list.index') ),
			            'image'	=> array('property', 'location_1'),
					),
				);
			}
			
			if ( $GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin')
				|| $GLOBALS['phpgw']->acl->check('admin', phpgwapi_acl::ADD, 'controller'))
			{
				$menus['admin'] = array
				(
					'acl'	=> array
					(
						'text'	=> lang('Configure Access Permissions'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uiadmin_acl.list_acl', 'acl_app' => 'controller') )
					)
				);
			}
			
			$menus['folders'] = phpgwapi_menu::get_categories('bergen');

			$GLOBALS['phpgw_info']['flags']['currentapp'] = $incoming_app;

			return $menus;
		}
	}
