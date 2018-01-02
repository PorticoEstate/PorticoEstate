<?php

	function parse_navbar($force = False)
	{
		$nonavbar = false;
		if(isset($GLOBALS['phpgw_info']['flags']['nonavbar']) && $GLOBALS['phpgw_info']['flags']['nonavbar'])
		{
			$nonavbar	= true;
		}

		$navbar = array();
		if(!isset($GLOBALS['phpgw_info']['flags']['nonavbar']) || !$GLOBALS['phpgw_info']['flags']['nonavbar'])
		{
			$navbar = execMethod('phpgwapi.menu.get', 'navbar');
		}

		$user = $GLOBALS['phpgw']->accounts->get( $GLOBALS['phpgw_info']['user']['id'] );

		$var = array
		(
			'webserver_url'	=> $GLOBALS['phpgw_info']['server']['webserver_url']
		);

		$extra_vars = array();
		foreach($_GET as $name => $value)
		{
			$extra_vars[$name] = phpgw::clean_value($value);
		}

		$print_url = "{$_SERVER['PHP_SELF']}?" . http_build_query(array_merge($extra_vars, array('phpgw_return_as' => 'noframes')));
		$user_fullname	= $user->__toString();
		$print_text		= lang('print');
		$home_url		= $GLOBALS['phpgw']->link('/home.php');
		$home_text		= lang('home');
		$home_icon		= 'icon icon-home';
		$about_url	= $GLOBALS['phpgw']->link('/about.php', array('app' => $GLOBALS['phpgw_info']['flags']['currentapp']) );
		$about_text	= lang('about');
		$logout_url	= $GLOBALS['phpgw']->link('/logout.php');
		$logout_text	= lang('logout');
		$var['user_fullname'] = $user_fullname;
		$preferences_url = $GLOBALS['phpgw']->link('/preferences/index.php');
		$preferences_text = lang('preferences');


		$var['topmenu'] = <<<HTML
		<div class="pure-menu pure-menu-horizontal">
			<ul id="std-menu-items" class="pure-menu-list">
				<li class="pure-menu-item pure-menu-has-children pure-menu-allow-hover">
					<a class="pure-menu-link" href="#">{$user_fullname}</a>
					<ul class="pure-menu-children">
						<li class="pure-menu-item">
							<a href="{$preferences_url}" class="pure-menu-link">{$preferences_text}</a>
						</li>
						<li class="pure-menu-item">
							<a href="{$logout_url}" class="pure-menu-link">{$logout_text}</a>
						</li>
					</ul>
				</li>
				<li class="pure-menu-item">
					<a href="{$print_url}"  target="_blank" class="pure-menu-link">{$print_text}</a>
				</li>
				<li class="pure-menu-item">
					<a href="{$home_url}" class="pure-menu-link">{$home_text}</a>
				</li>
				<li class="pure-menu-item">
					<a href="{$about_url}" class="pure-menu-link">{$about_text}</a>
				</li>
HTML;

		if ( isset($GLOBALS['phpgw_info']['user']['apps']['manual']) )
		{
			$help_url= "javascript:openwindow('"
			 . $GLOBALS['phpgw']->link('/index.php', array
			 (
			 	'menuaction'=> 'manual.uimanual.help',
			 	'app' => $GLOBALS['phpgw_info']['flags']['currentapp'],
			 	'section' => isset($GLOBALS['phpgw_info']['apps']['manual']['section']) ? $GLOBALS['phpgw_info']['apps']['manual']['section'] : '',
			 	'referer' => phpgw::get_var('menuaction')
			 )) . "','700','600')";

			$help_text = lang('help');
			$var['topmenu'] .= <<<HTML
			<li class="pure-menu-item">
				<a href="{$help_url}" class="pure-menu-link">{$help_text}</a>
			</li>
HTML;
			}


		if(isset($GLOBALS['phpgw_info']['server']['support_address']) && $GLOBALS['phpgw_info']['server']['support_address'])
		{
			$support_url = "javascript:openwindow('"
			 . $GLOBALS['phpgw']->link('/index.php', array
			 (
			 	'menuaction'=> 'manual.uisupport.send',
			 	'app' => $GLOBALS['phpgw_info']['flags']['currentapp'],
			 )) . "','700','600')";

			$support_text = lang('support');

			$var['topmenu'] .= <<<HTML
			<li class="pure-menu-item">
				<a href="{$support_url}" class="pure-menu-link">{$support_text}</a>
			</li>
HTML;
		}

		if ( isset($GLOBALS['phpgw_info']['user']['apps']['admin']) )
		{
			$debug_url = "javascript:openwindow('"
			 . $GLOBALS['phpgw']->link('/index.php', array
			 (
			 	'menuaction'=> 'property.uidebug_json.index',
			 	'app'		=> $GLOBALS['phpgw_info']['flags']['currentapp']
			 )) . "','','')";

			$debug_text = lang('debug');
			$var['topmenu'] .= <<<HTML
			<li class="pure-menu-item">
				<a href="{$debug_url}" class="pure-menu-link">{$debug_text}</a>
			</li>
HTML;
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

		if (!$nonavbar)
		{
			$bookmarks = phpgwapi_cache::user_get('phpgwapi', "bookmark_menu", $GLOBALS['phpgw_info']['user']['id']);
			$lang_bookmarks = lang('bookmarks');

			$navigation = execMethod('phpgwapi.menu.get', 'navigation');
			$treemenu = '';
			foreach($navbar as $app => $app_data)
			{
				if(!in_array($app, array('logout', 'about', 'preferences')))
				{
					$submenu = isset($navigation[$app]) ? render_submenu($app, $navigation[$app], $bookmarks) : '';
					$treemenu .= render_item($app_data, "navbar::{$app}", $submenu, $bookmarks);
				}
			}
			$var['treemenu'] = <<<HTML

			<ul id="menutree">
HTML;
			if ( $GLOBALS['phpgw']->acl->check('run', PHPGW_ACL_READ, 'preferences') )
			{
				$var['treemenu'] .= <<<HTML

				<li>
					<a href="{$preferences_url}">{$preferences_text}</a>
				</li>
HTML;
			}
			$var['treemenu'] .= <<<HTML
			{$treemenu}
			</ul>
HTML;


			$collected_bm = set_get_bookmarks();
			if($collected_bm)
			{
				$var['topmenu'] .= <<<HTML

				<li class="pure-menu-item pure-menu-has-children pure-menu-allow-hover">
					<a href="#" class="pure-menu-link">{$lang_bookmarks}</a>
					<ul class="pure-menu-children">
HTML;
				
					foreach($collected_bm as $entry)
					{
						$seleced_bm = 'class="pure-menu-item"';
						if( isset($entry['selected']) && $entry['selected'])
						{
							$seleced_bm = 'class="pure-menu-item pure-menu-selected"' ;
							$entry['text'] = "<b>[ {$entry['text']} ]</b>";
						}

						$var['topmenu'] .= <<<HTML

						<li {$seleced_bm}>
							<a href="{$entry['url']}" class="pure-menu-link">{$entry['text']}</a>
						</li>

HTML;
					
					}
					$var['topmenu'] .= '</ul>';
			}
			else
			{
				$var['topmenu'] .= <<<HTML

				<li class="pure-menu-item pure-menu-disabled">
					<a href="#" class="pure-menu-link">{$lang_bookmarks}</a>
HTML;

			}
		$var['topmenu'] .= <<<HTML
				
			</li>
		</ul>
	</div>
HTML;



		}


		$GLOBALS['phpgw']->template->set_var($var);
		$GLOBALS['phpgw']->template->pfp('out','navbar');

		if( phpgw::get_var('phpgw_return_as') != 'json' && $global_message = phpgwapi_cache::system_get('phpgwapi', 'phpgw_global_message'))
		{
			echo "<div class='msg_good'>";
			echo nl2br($global_message);
			echo '</div>';
		}
		if(phpgw::get_var('phpgw_return_as') != 'json' && $breadcrumbs)// && isset($GLOBALS['phpgw_info']['user']['preferences']['common']['show_breadcrumbs']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['show_breadcrumbs'])
		{
			$history_url = array();
			foreach($breadcrumbs as $breadcrumb)
			{
				$history_url[] ="<a href='{$breadcrumb['url']}'>{$breadcrumb['name']}</a>";
			}
			$breadcrumbs = '<div class="breadcrumbs"><h4>' . implode(' >> ', $history_url) . '</h4></div>';
//			echo $breadcrumbs;
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

	function render_item($item, $id='', $children='', $bookmarks = array())
	{
		static $checkbox_id = 1;
		$current_class = '';

		if ( $id == "navbar::{$GLOBALS['phpgw_info']['flags']['menu_selection']}" )
		{
			$current_class = 'Selected';
			$item['selected'] = true;
		}

		$bookmark = '';
		if(preg_match("/(^navbar::)/i", $id)) // bookmarks
		{
			$_bookmark_checked = '';
			if(is_array($bookmarks) && isset($bookmarks[$id]))
			{
				$_bookmark_checked = "checked = 'checked'";
				set_get_bookmarks($item);
			}

			$bookmark = "<input type='checkbox' name='update_bookmark_menu' id='{$checkbox_id}' value='{$id}' {$_bookmark_checked}/>";
			$checkbox_id ++;
		}

		if(preg_match("/(^{$id})/i", "navbar::{$GLOBALS['phpgw_info']['flags']['menu_selection']}"))
		{
			$item['text'] = "<b>[ {$item['text']} ]</b>";
		}

		$link_class = $current_class ? "class=\"{$current_class}\"" : '';

		$out = <<<HTML
				<li {$link_class}>
HTML;
		$target = '';
		if(isset($item['target']))
		{
			$target = "target = '{$item['target']}'";
		}
		if(isset($item['local_files']) && $item['local_files'])
		{
			$item['url'] = 'file:///' . str_replace(':','|',$item['url']);
		}

		return <<<HTML
$out
					<a href="{$item['url']}" id="{$id}" {$target}>{$bookmark} {$item['text']}</a>
{$children}
				</li>

HTML;
	}

	function render_submenu($parent, $menu, $bookmarks = array())
	{
		$out = '';
		foreach ( $menu as $key => $item )
		{
			$children = isset($item['children']) ? render_submenu(	"{$parent}::{$key}", $item['children'], $bookmarks) : '';
			$out .= render_item($item, "navbar::{$parent}::{$key}", $children, $bookmarks);
			//$debug .= "{$parent}::{$key}<br>";
		}

		$out = <<<HTML
			<ul>
{$out}
			</ul>

HTML;
		return $out;
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
	//		'user_fullname'	=> $GLOBALS['phpgw']->accounts->get( $GLOBALS['phpgw_info']['user']['id'] )->__toString(),
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

	/**
	 * Cheat function to collect bookmarks
	 * @staticvar array $bookmarks
	 * @param array $item
	 * @return array bookmarks
	 */
	function set_get_bookmarks($item = array())
	{
		static $bookmarks = array();
		if($item)
		{
			$bookmarks[] = $item;
		}
		return $bookmarks;
	}