<?php

	function parse_navbar($force = False)
	{
		$navbar = array();
//		if(!isset($GLOBALS['phpgw_info']['flags']['nonavbar']) || !$GLOBALS['phpgw_info']['flags']['nonavbar'])
		{
			$navbar = execMethod('phpgwapi.menu.get', 'navbar');
		}

		$user = $GLOBALS['phpgw']->accounts->get( $GLOBALS['phpgw_info']['user']['id'] );
		$extra_vars = array();
		foreach($_GET as $name => $value)
		{
			$extra_vars[$name] = phpgw::clean_value($value);
		}

		$var = array
		(
			'print_url'		=> "{$_SERVER['PHP_SELF']}?" . http_build_query(array_merge($extra_vars, array('phpgw_return_as' => 'noframes'))),
			'print_text'	=> lang('print'),
			'home_url'		=> $GLOBALS['phpgw']->link('/home.php'),
			'home_text'		=> lang('home'),
			'home_icon'		=> 'icon icon-home',
			'about_url'		=> $GLOBALS['phpgw']->link('/about.php', array('app' => $GLOBALS['phpgw_info']['flags']['currentapp']) ),
			'about_text'	=> lang('about'),
			'logout_url'	=> $GLOBALS['phpgw']->link('/logout.php'),
			'logout_text'	=> lang('logout'),
			'user_fullname' => $user->__toString(),
			'top_level_menu_url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'phpgwapi.menu.get_local_menu_ajax', 'node'=> 'top_level', 'phpgw_return_as'=>'json') ),
		);

		if ( $GLOBALS['phpgw']->acl->check('run', PHPGW_ACL_READ, 'preferences') )
		{
			$var['preferences_url'] = $GLOBALS['phpgw']->link('/preferences/index.php');
			$var['preferences_text'] = lang('preferences');
		}

		if ( isset($GLOBALS['phpgw_info']['user']['apps']['manual']) )
		{
			$var['help_url'] = "javascript:openwindow('"
			 . $GLOBALS['phpgw']->link('/index.php', array
			 (
			 	'menuaction'=> 'manual.uimanual.help',
			 	'app' => $GLOBALS['phpgw_info']['flags']['currentapp'],
			 	'section' => isset($GLOBALS['phpgw_info']['apps']['manual']['section']) ? $GLOBALS['phpgw_info']['apps']['manual']['section'] : '',
			 	'referer' => phpgw::get_var('menuaction')
			 )) . "','700','600')";

			$var['help_text'] = lang('help');
			$var['help_icon'] = 'icon icon-help';
		}


		if(isset($GLOBALS['phpgw_info']['server']['support_address']) && $GLOBALS['phpgw_info']['server']['support_address'])
		{
			$support_js = <<<JS

			support_request = function()
			{
				var oArgs = {menuaction:'manual.uisupport.send',app:'{$GLOBALS['phpgw_info']['flags']['currentapp']}'};
				var strURL = phpGWLink('index.php', oArgs);
				TINY.box.show({iframe:strURL, boxid:"frameless",width:700,height:400,fixed:false,maskid:"darkmask",maskopacity:40, mask:true, animate:true, close: true});
			}
JS;


			$var['support_request'] = $support_js;
			$var['support_url'] = "javascript:support_request();";
			$var['support_text'] = lang('support');
			$var['support_icon'] = 'icon icon-help';
		
		}

		if ( isset($GLOBALS['phpgw_info']['user']['apps']['admin']) )
		{
			$var['debug_url'] = "javascript:openwindow('"
			 . $GLOBALS['phpgw']->link('/index.php', array
			 (
			 	'menuaction'=> 'property.uidebug_json.index',
			 	'app'		=> $GLOBALS['phpgw_info']['flags']['currentapp']
			 )) . "','','')";

			$var['debug_text'] = lang('debug');
			$var['debug_icon'] = 'icon icon-debug';
		}

		$GLOBALS['phpgw']->template->set_root(PHPGW_TEMPLATE_DIR);
		$GLOBALS['phpgw']->template->set_file('navbar', 'navbar.tpl');

		$flags = &$GLOBALS['phpgw_info']['flags'];
		$var['current_app_title'] = isset($flags['app_header']) ? $flags['app_header'] : lang($GLOBALS['phpgw_info']['flags']['currentapp']);
		$flags['menu_selection'] = isset($flags['menu_selection']) ? $flags['menu_selection'] : '';
		// breadcrumbs
		$current_url = array
		(
			'id'	=> $flags['menu_selection'],
			'url'	=> 	"{$_SERVER['PHP_SELF']}?" . http_build_query($extra_vars),
			'name'	=> $var['current_app_title']
		);
		$breadcrumbs = phpgwapi_cache::session_get('phpgwapi','breadcrumbs');
		$breadcrumbs = $breadcrumbs ? $breadcrumbs : array(); // first one
		if(empty($breadcrumbs) ||( isset($breadcrumbs[0]['id']) && $breadcrumbs[0]['id'] != $breadcrumb_selection))
		{
			array_unshift($breadcrumbs, $current_url);
		}
		if(count($breadcrumbs) >= 5)
		{
			array_pop($breadcrumbs);
		}
		phpgwapi_cache::session_set('phpgwapi','breadcrumbs', $breadcrumbs);
		$breadcrumbs = array_reverse($breadcrumbs);
		
		phpgwapi_cache::session_set('navbar', 'menu_selection',$GLOBALS['phpgw_info']['flags']['menu_selection']);

//		$menu_organizer = createObject('phpgwapi.menu_jqtree');
//		$treemenu = $menu_organizer->get_menu();

//		$var['treemenu_data'] = json_encode($treemenu);
//		$var['current_node_id'] =  $menu_organizer->get_current_node_id();

		$GLOBALS['phpgw']->template->set_var($var);
		$GLOBALS['phpgw']->template->pfp('out','navbar');

		if( phpgw::get_var('phpgw_return_as') != 'json' && $global_message = phpgwapi_cache::system_get('phpgwapi', 'phpgw_global_message'))
		{
			echo "<div class='msg_good'>";
			echo nl2br($global_message);
			echo '</div>';
		}
		if(phpgw::get_var('phpgw_return_as') != 'json' && $breadcrumbs && isset($GLOBALS['phpgw_info']['user']['preferences']['common']['show_breadcrumbs']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['show_breadcrumbs'])
		{
			$history_url = array();
			foreach($breadcrumbs as $breadcrumb)
			{
				$history_url[] ="<a href='{$breadcrumb['url']}'>{$breadcrumb['name']}</a>";
			}
			$breadcrumbs = '<div class="breadcrumbs"><h4>' . implode(' >> ', $history_url) . '</h4></div>';
			echo $breadcrumbs;
		}


		if( phpgw::get_var('phpgw_return_as') != 'json' && $receipt = phpgwapi_cache::session_get('phpgwapi', 'phpgw_messages'))
		{
			phpgwapi_cache::session_clear('phpgwapi', 'phpgw_messages');
			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);
			$msgbox_data = $GLOBALS['phpgw']->common->msgbox($msgbox_data);
			foreach($msgbox_data as & $message)
			{
				echo "<div class='{$message['msgbox_class']}'>";
				echo $message['msgbox_text'];
				echo '</div>';
			}
		}

		$GLOBALS['phpgw']->hooks->process('after_navbar');
		register_shutdown_function('parse_footer_end');
	}

	function item_expanded($id)
	{
		static $navbar_state;
		if( !isset( $navbar_state ) )
		{
			$navbar_state = execMethod('phpgwapi.template_portico.retrieve_local', 'navbar_config');
		}
		return isset( $navbar_state[ $id ]);
	}

	function parse_footer_end()
	{
		// Stop the register_shutdown_function causing the footer to be included twice - skwashd dec07
		static $footer_included = false;
		if ( $footer_included )
		{
			return true;
		}

		$GLOBALS['phpgw']->template->set_root(PHPGW_TEMPLATE_DIR);
		$GLOBALS['phpgw']->template->set_file('footer', 'footer.tpl');
		
		$version = isset($GLOBALS['phpgw_info']['server']['versions']['system']) ? $GLOBALS['phpgw_info']['server']['versions']['system'] : $GLOBALS['phpgw_info']['server']['versions']['phpgwapi'];
		
		if(isset($GLOBALS['phpgw_info']['server']['system_name']))
		{
			 $powered_by = $GLOBALS['phpgw_info']['server']['system_name'] . ' ' . lang('version') . ' ' . $version;
		}
		else
		{
			$powered_by = lang('Powered by phpGroupWare version %1', $version);
		}
		
		$var = array
		(
			'powered_by'	=> $powered_by,
			'lang_login'	=> lang('login'),
			'javascript_end'=> $GLOBALS['phpgw']->common->get_javascript_end()
		);

		$GLOBALS['phpgw']->template->set_var($var);

		$GLOBALS['phpgw']->template->pfp('out', 'footer');

		$footer_included = true;
	}
