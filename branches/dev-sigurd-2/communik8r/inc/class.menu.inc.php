<?php

	class communik8r_menu
	{
		function get_menu()
		{
			$incoming_app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$GLOBALS['phpgw_info']['flags']['currentapp'] = 'communik8r';

			$menus = array();

			if ( $GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin')
			|| $GLOBALS['phpgw']->acl->check('admin', phpgwapi_acl::ADD, 'communik8r'))
			{
				$menus['admin'] = array
				(
					'index'	=> array
					(
						'text'	=> lang('Configuration'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiconfig.index', 'appname' => 'communik8r') )
					),
					'acl'	=> array
					(
						'text'	=> lang('Configure Access Permissions'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uiadmin_acl.list_acl', 'acl_app' => 'communik8r') )
					)
				);
			}

			$menus['navbar'] = array
			(
				'communik8r' => array
				(
					'text'	=> lang('communik8r'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'communik8r.uibase.index') ),
					'image'	=> array('communik8r', 'navbar'),
					'order'	=> 35,
					'group'	=> 'office'
				),
			);


			$menus['navigation'] = array();
			$menus['navigation'] = array
			(
				'start'	=> array
				(
					'text'	=> lang('start'),
					'url'	=> $GLOBALS['phpgw']->link('index.php', array('section'=> 'start'))
				),
				'job'	=> array
				(
					'text'	=> lang('compose'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'communik8r.uibase.compose')),
					'children' => $job_children
				)
			);
			
			$GLOBALS['phpgw_info']['flags']['currentapp'] = $incoming_app;
			return $menus;
		}
	}
