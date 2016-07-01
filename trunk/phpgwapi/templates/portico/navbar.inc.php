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
			$var['support_url'] = "javascript:openwindow('"
			 . $GLOBALS['phpgw']->link('/index.php', array
			 (
			 	'menuaction'=> 'manual.uisupport.send',
			 	'app' => $GLOBALS['phpgw_info']['flags']['currentapp'],
			 )) . "','700','600')";

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
		if($breadcrumbs[0]['id'] != $flags['menu_selection'])
		{
			array_unshift($breadcrumbs, $current_url);
		}
		if(count($breadcrumbs) >= 5)
		{
			array_pop($breadcrumbs);
		}
		phpgwapi_cache::session_set('phpgwapi','breadcrumbs', $breadcrumbs);
		$breadcrumbs = array_reverse($breadcrumbs);
		
		$navigation = array();
		if( !isset($GLOBALS['phpgw_info']['user']['preferences']['property']['nonavbar']) || $GLOBALS['phpgw_info']['user']['preferences']['property']['nonavbar'] != 'yes' )
		{
			prepare_navbar($navbar);
		}
		else
		{
			foreach($navbar as & $app_tmp)
			{
				$app_tmp['text'] = ' ...';
			}
		}

//		if (isset($GLOBALS['phpgw_info']['user']['preferences']['common']['sidecontent']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['sidecontent'] == 'jsmenu'
//			&& !phpgwapi_cache::session_get('navbar', 'compiled') == true
//		)
		{
			$menu_organizer = new menu_organizer($navbar);
			$treemenu = $menu_organizer->get_menu();

			$var['treemenu_data'] = json_encode($treemenu);
			$var['current_node_id'] =  $menu_organizer->get_current_node_id();
			
			/**
			 * Check for HTML5
			 */
			if(!preg_match('/MSIE (6|7|8)/', $_SERVER['HTTP_USER_AGENT']))
			{
				phpgwapi_cache::session_set('navbar', 'compiled', true);
			}
		}

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

	/**
	* Callback for usort($navbar)
	*
	* @param array $item1 the first item to compare
	* @param array $item2 the second item to compare
	* @return int result of comparision
	*/
	function sort_navbar($item1, $item2)
	{
		$a =& $item1['order'];
		$b =& $item2['order'];

		if ($a == $b)
		{
			return strcmp($item1['text'], $item2['text']);
		}
		return ($a < $b) ? -1 : 1;
	}

	/**
	* Organise the navbar properly
	*
	* @param array $navbar the navbar items
	* @return array the organised navbar
	*/
	function prepare_navbar(&$navbar)
	{
		if ( isset($navbar['admin']) && is_array($navbar['admin']) )
		{
			$navbar['admin']['children'] = execMethod('phpgwapi.menu.get', 'admin');
		}
		uasort($navbar, 'sort_navbar');
	}

	class menu_organizer
	{
		private $current_node_id;
		private $navbar;

		function __construct($navbar = array())
		{
			$this->set_navbar($navbar);
			$this->set_current_node_id(0);
		}

		private function set_navbar($navbar)
		{
			$this->navbar = $navbar;
		}
		private function set_current_node_id($current_node_id)
		{
			$this->current_node_id = $current_node_id;
		}
		public function get_current_node_id()
		{
			return $this->current_node_id;
		}

		public function get_menu( )
		{
			$treemenu = array();
			$navbar = $this->navbar;
			$navigation = execMethod('phpgwapi.menu.get', 'navigation');

			foreach($navbar as $app => $app_data)
			{
				if(!in_array($app, array('logout', 'about', 'preferences')))
				{
					$submenu = isset($navigation[$app]) ? $this->render_submenu($app, $navigation[$app]) : '';
					$treemenu[] = $this->render_item($app_data, "navbar::{$app}", $submenu);
				}
			}
			return $treemenu;
		}

		private function render_item($item, $id='', $children=array())
		{
			static $node_id = 0;
			$node_id ++;

			$icon_style = $expand_class = $current_class = $link_class = $parent_class = '';
			static $blank_image;
			static $images = array(); // cache

			if ( $id == "navbar::{$GLOBALS['phpgw_info']['flags']['menu_selection']}" )
			{
				$current_class = 'current';
				$this->set_current_node_id($node_id);
			}

			$link_class = " class=\"{$current_class}{$parent_class}\"";
			$id=" id=\"{$id}\"";

			$link_class = '';//" class=\"{$current_class}{$parent_class}\"";
			$expand_class = '';
			$icon_style = '';
			$id			= '';

			$target = '';
			if(isset($item['target']))
			{
				$target = "target = '{$item['target']}'";
			}
			if(isset($item['local_files']) && $item['local_files'])
			{
				$item['url'] = 'file:///' . str_replace(':','|',$item['url']);
			}

			$ret = array(
				'name'	=> "<a href=\"{$item['url']}\" style=\"white-space: nowrap;\"{$target}>{$item['text']}</a>",
				'id'	=> $node_id
			);

			if($children)
			{
				$ret['children'] = $children;
			}

			return $ret;
		}

		private function render_submenu($parent, $menu)
		{
			$out = array();
			foreach ( $menu as $key => $item )
			{
				$children = isset($item['children']) ? $this->render_submenu(	"{$parent}::{$key}", $item['children']) : array();
				$out[]= $this->render_item($item, "navbar::{$parent}::{$key}", $children);
			}
			return $out;
		}
	}