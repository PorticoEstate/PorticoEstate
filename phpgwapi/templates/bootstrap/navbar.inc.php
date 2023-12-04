<?php

	function parse_navbar($force = False)
	{
		$nonavbar = false;
		if(isset($GLOBALS['phpgw_info']['flags']['nonavbar']) && $GLOBALS['phpgw_info']['flags']['nonavbar'])
		{
			$nonavbar	= true;
		}

		$navbar = array();
		if(!$nonavbar)
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
//		$var['logout_url']	= $GLOBALS['phpgw']->link('/logout.php');
		$var['logout_text']	= lang('logout');
		$var['user_fullname'] = $user_fullname;
		$preferences_url = $GLOBALS['phpgw']->link('/preferences/index.php');
		$preferences_text = lang('preferences');
		$undraw_profile = $GLOBALS['phpgw']->common->find_image('phpgwapi', 'undraw_profile.svg');

		switch($GLOBALS['phpgw_info']['user']['preferences']['common']['template_set'])
		{
			case 'portico':
				$selecte_portico = ' selected = "selected"';
				$selecte_bootstrap = '';
				break;
			case 'bootstrap':
				$selecte_portico = '';
				$selecte_bootstrap = ' selected = "selected"';
				break;
		}

		$template_selector = <<<HTML

	   <select id = "template_selector" class="btn btn-link btn-sm nav-item dropdown no-arrow nav-link text-white dropdown-toggle" style="height:2rem;margin-top:5px">
		<option class="nav-link text-white" value="bootstrap"{$selecte_bootstrap}>Bootstrap</option>
		<option class="nav-link text-white" value="portico"{$selecte_portico}>Portico</option>
	   </select>
HTML;

		$GLOBALS['phpgw']->template->set_root(PHPGW_TEMPLATE_DIR);
		$GLOBALS['phpgw']->template->set_file('navbar', 'navbar.tpl');

		$flags = &$GLOBALS['phpgw_info']['flags'];
		$var['current_app_title'] = isset($flags['app_header']) ? $flags['app_header'] : lang($GLOBALS['phpgw_info']['flags']['currentapp']);
		$flags['menu_selection'] = isset($flags['menu_selection']) ? $flags['menu_selection'] : '';
		$breadcrumb_selection = !empty($flags['breadcrumb_selection']) ? $flags['breadcrumb_selection'] : $flags['menu_selection'];
		// breadcrumbs
		$current_url = array
		(
			'id'	=> $breadcrumb_selection,
			'url'	=> 	"{$_SERVER['PHP_SELF']}?" . http_build_query($extra_vars),
			'name'	=> $var['current_app_title']
		);
		$breadcrumbs = phpgwapi_cache::session_get('phpgwapi','breadcrumbs');
		$breadcrumbs = $breadcrumbs ? $breadcrumbs : array(); // first one


		if(empty($breadcrumbs) ||( isset($breadcrumbs[0]['id']) && $breadcrumbs[0]['id'] != $breadcrumb_selection))
		{
			array_unshift($breadcrumbs, $current_url);
		}
		if(count($breadcrumbs) >= 6)
		{
			array_pop($breadcrumbs);
		}
		phpgwapi_cache::session_set('phpgwapi','breadcrumbs', $breadcrumbs);
		$breadcrumbs = array_reverse($breadcrumbs);
		phpgwapi_cache::session_set('navbar', 'menu_selection',$GLOBALS['phpgw_info']['flags']['menu_selection']);

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
//			_debug_array($bookmarks);
			$lang_bookmarks = lang('bookmarks');

			$_treemenu = '';

			if($GLOBALS['phpgw_info']['user']['preferences']['common']['sidecontent'] !== 'ajax_menu')
			{
				$navigation = execMethod('phpgwapi.menu.get', 'navigation');
				foreach($navbar as $app => $app_data)
				{
					if(!in_array($app, array('logout', 'about', 'preferences')))
					{
						$submenu = isset($navigation[$app]) ? render_submenu($app, $navigation[$app], $bookmarks, $app_data['text']) : '';
						$node = render_item($app_data, "navbar::{$app}", $submenu, $bookmarks);
						$_treemenu .= $node['node'];
					}
				}
			}
			$treemenu = <<<HTML

			<ul id="menutree" class="list-unstyled components">
HTML;
			$preferences_option = '';
			if ( $GLOBALS['phpgw']->acl->check('run', PHPGW_ACL_READ, 'preferences') )
			{
				$preferences_option .= <<<HTML
				<a class="dropdown-item" href="{$preferences_url}">
					<i class="fas fa-cogs fa-sm fa-fw me-2"></i>
					{$preferences_text}
				</a>
HTML;
			}
			$treemenu .= <<<HTML
			{$_treemenu}
			</ul>
HTML;




		}
		$breadcrumb_html = "";

		if((phpgw::get_var('phpgw_return_as') != 'json'  && $breadcrumbs && is_array($breadcrumbs)) && !$nonavbar)// && isset($GLOBALS['phpgw_info']['user']['preferences']['common']['show_breadcrumbs']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['show_breadcrumbs'])
		{
			$breadcrumb_html = <<<HTML
			<div class="clearfix">
			<nav aria-label="breadcrumb">
				  <ol class="breadcrumb shadow ps-2 pt-2 pb-3">
HTML;
			$history_url = array();
			for($i=0;$i< (count($breadcrumbs) -1); $i++)
			{
				$breadcrumb_html .= <<<HTML
					<li class="breadcrumb-item"><a href="{$breadcrumbs[$i]['url']}">{$breadcrumbs[$i]['name']}</a></li>
HTML;
			}

			$breadcrumb_html .= <<<HTML
				    <li class="breadcrumb-item" aria-current="page">{$breadcrumbs[$i]['name']}</li>
HTML;

			$breadcrumb_html .= <<<HTML
				</ol>
			  </nav>
		</div>
HTML;

		}

		$var['breadcrumb'] = $breadcrumb_html;

		$manual_option = '';

		if ( isset($GLOBALS['phpgw_info']['user']['apps']['manual']) )
		{
			$help_file = execMethod('manual.uimanual.help_file_exist');
			if($help_file['file_exist'])
			{
				$help_url= "javascript:openwindow('"
				. $GLOBALS['phpgw']->link('/index.php', array
				(
					'menuaction'=> 'manual.uimanual.help',
					'app' => $help_file['app'],
					'section' => $help_file['section'],
					'referer' => $help_file['referer'],
				)) . "','700','600')";

				$help_text = lang('help');
				$manual_option .= <<<HTML
				<li class="nav-item mt-1">
					<a href="{$help_url}" class="nav-link text-white">{$help_text}</a>
				</li>
HTML;
			}
		}

		$support_option = '';
		if(isset($GLOBALS['phpgw_info']['server']['support_address']) && $GLOBALS['phpgw_info']['server']['support_address'])
		{
			$support_text = lang('support');
			$support_link = $GLOBALS['phpgw']->link('/index.php', array
				(
					'menuaction'=> 'manual.uisupport.send',
					'app' => $GLOBALS['phpgw_info']['flags']['currentapp'],
					'form_type' => 'stacked',
					'width' => 700,
					'height' => 540
				));
			$support_option = <<<HTML
			<li class="nav-item mt-1">
				<a href="$support_link" class="nav-link text-white" data-bs-toggle="modal" data-bs-target="#popupModal">{$support_text}</a>
			</li>
HTML;
		}

		$debug_option = '';
		if ( isset($GLOBALS['phpgw_info']['user']['apps']['admin']) )
		{
			$debug_url = "javascript:openwindow('"
			 . $GLOBALS['phpgw']->link('/index.php', array
			 (
			 	'menuaction'=> 'property.uidebug_json.index',
			 	'app'		=> $GLOBALS['phpgw_info']['flags']['currentapp']
			 )) . "','','')";

			$debug_text = lang('debug');
			$debug_option = <<<HTML
			<li class="nav-item mt-1">
				<a href="{$debug_url}" class="nav-link text-white">{$debug_text}</a>
			</li>
HTML;
		}
		/**
		 * Modal-version
		 */
//		$debug_option = '';
//		if(isset($GLOBALS['phpgw_info']['server']['support_address']) && $GLOBALS['phpgw_info']['server']['support_address'])
//		{
//			$debug_text = lang('debug');
//			$debug_link = $GLOBALS['phpgw']->link('/index.php', array
//				(
//					'menuaction'=> 'property.uidebug_json.index',
//					'app' => $GLOBALS['phpgw_info']['flags']['currentapp'],
//					'width' => 700,
//					'height' => 800
//				));
//			$debug_option = <<<HTML
//			<li class="nav-item">
//				<a href="$debug_link" class="nav-link" data-bs-toggle="modal" data-bs-target="#popupModal">{$debug_text}</a>
//			</li>
//HTML;
//		}


		$bookmark_option = '';
		$collected_bm = phpgwapi_cache::user_get('phpgwapi', "bookmark_menu", $GLOBALS['phpgw_info']['user']['id']);

		if($collected_bm)
		{
			$bookmark_option .= <<<HTML

			<li class="nav-item dropdown no-arrow mt-1">
				<a class="nav-link dropdown-toggle text-white" href="#" id="bookmarkDropdown" role="button"
					data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<span class="me-2 d-none d-lg-inline">{$lang_bookmarks}</span>
				</a>
				<!-- Dropdown - bookmarks -->
				<ul id="_bookmark" class="dropdown-menu"
				aria-labelledby="bookmarkDropdown">
HTML;

				foreach($collected_bm as $bookmark_id => $entry)
				{
					if(empty($entry['text']))
					{
						continue;
					}
					$seleced_bm = 'dropdown-item';
					$icon = !empty($entry['icon']) ? "<i class='{$entry['icon']} me-2'></i>": '<i class="fas fa-cogs fa-sm fa-fw me-2"></i>';


					if ( $bookmark_id == "navbar::{$GLOBALS['phpgw_info']['flags']['menu_selection']}"
					|| ( !empty($entry['nav_location']) && $entry['nav_location'] == $GLOBALS['phpgw_info']['flags']['menu_selection'] ))
					{
						$seleced_bm .= ' text-secondary';
					}

					$bookmark_option .= <<<HTML
					<li>
						<a class="{$seleced_bm}" href="{$entry['href']}" id="bookmark_{$bookmark_id}">
							{$icon}
							{$entry['text']}
						</a>
					</li>
HTML;

				}
				$bookmark_option .= '</ul></li>';
		}
		else
		{
			$bookmark_option .= <<<HTML

			<li class="nav-item disabled mt-1">
				<a href="#" class="nav-link text-white">{$lang_bookmarks}</a>
			</li>
HTML;

		}

		$messenger_option = '';
		if ( isset($GLOBALS['phpgw_info']['user']['apps']['messenger']) )
		{
			$bomessenger	 = CreateObject('messenger.bomessenger');
			$total_messages	 = $bomessenger->total_messages(" AND message_status = 'N'");
			if ($total_messages > 0)
			{
				$new_messages		 = $total_messages;
				$new_messages_alert	 = "<span class='badge bg-danger rounded-pill'>{$new_messages}</span>";
			}
			else
			{
				$new_messages		 = 0;
				$new_messages_alert	 = '';
			}

			$link_messages = $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'messenger.uimessenger.index' ));

			$lang_messenger = $GLOBALS['phpgw']->translation->translate('messenger', array(), true);
			$lang_read_messages = $GLOBALS['phpgw']->translation->translate('read messages', array(), false, 'messenger');

			$messenger_option = <<<HTML
                        <li class="nav-item dropdown no-arrow mt-1" onClick="get_messages();">
                            <a class="nav-link dropdown-toggle text-white" href="#" id="messagesDropdown" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-envelope fa-fw"></i>
                                <!-- Counter - Messages -->
								{$new_messages_alert}
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="messagesDropdown">
                                <h6 class="dropdown-header">
									{$lang_messenger}
                                </h6>
								<div id="messages"></div>
                                <a class="dropdown-item small" href="{$link_messages}">{$lang_read_messages}</a>
                            </div>
                        </li>
HTML;
		}
		$topmenu = <<<HTML

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ms-auto">
					<li class="nav-item  mt-1">
						<a href="{$home_url}" class="nav-link text-white">{$home_text}</a>
					</li>
						{$template_selector}
 						{$manual_option}
						{$debug_option}
						{$support_option}
						{$bookmark_option}
                        <!-- Nav Item - Alerts -->
                         <!-- Nav Item - Messages -->
						{$messenger_option}

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle text-white" href="#" id="userDropdown" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="me-2 d-none d-lg-inline">$user_fullname</span>
                                <img class="img-profile rounded-circle" style="height:2rem; width: 2rem;"
                                    src="{$undraw_profile}">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <!--a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw me-2"></i>
                                    Profile
                                </a-->
								{$preferences_option}
                                <!--a class="dropdown-item" href="#">
                                    <i class="fas fa-list fa-sm fa-fw me-2"></i>
                                    Activity Log
                                </a-->
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw me-2"></i>
                                    {$var['logout_text']}
                                </a>
                            </div>
                        </li>

                    </ul>


HTML;

		if($nonavbar)
		{
			$var['sidebar'] = '';
			$var['top_panel'] = '';
		}
		else 
		{
			$navbar_state = execMethod('phpgwapi.template_portico.retrieve_local', 'menu_state');
			$var['menu_state'] = !empty($navbar_state['menu_state']) ? 'sb-sidenav-toggled' : '';

			if($GLOBALS['phpgw_info']['user']['preferences']['common']['sidecontent'] == 'ajax_menu')
			{
				$lang_collapse_all	= lang('collapse all');
				$var['sidebar'] = <<<HTML
               <nav class="sb-sidenav accordion sb-sidenav-light" id="sidebar">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
						</div>
						<div class="sidebar-header">
							<h1>{$user_fullname}</h1>
						</div>
						<div class="input-group">
							<input class="form-control border-end-0 border" type="search" value="" id="navbar_search">
							<span class="input-group-append">
				                <button class="btn btn-outline-secondary bg-white border-start-0 border ms-n3">
                				    <i class="fa fa-search"></i>
               					</button>
        					</span>
						</div>

						<div id="navtreecontrol" class="ms-4">
							<a id="collapseNavbar" title="Collapse the entire tree below" href="#" style="white-space:nowrap; color:inherit; font-size: 1rem">
								{$lang_collapse_all}
							</a>
						</div>
						<div id="navbar" style="overflow: auto" class="ms-4"></div>

                    </div>
                    <!--div class="sb-sidenav-footer">
                        <div class="small">Logged in as:</div>
                        {$user_fullname}
                    </div-->
                </nav>
HTML;

			}
			else
			{
				$var['sidebar'] = <<<HTML
				<nav id="sidebar" class="{$menu_state}">
					<div class="sidebar-header">
						<h1>{$user_fullname}</h1>
					</div>
					<div class="sidebar-sticky">
						{$treemenu}
					</div>
				</nav>
HTML;
			}

			$var['top_panel'] = <<<HTML
	        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
	            <!-- Sidebar Toggle-->
		        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" ><i class="fas fa-bars"></i></button>
		        <!--  Brand-->
				<a class="navbar-brand ps-3" href="#">{$GLOBALS['phpgw_info']['server']['site_title']}</a>
		        <!-- Navbar-->
				{$topmenu}
			</nav>
HTML;
		}

		$GLOBALS['phpgw']->template->set_var($var);
		$GLOBALS['phpgw']->template->pfp('out','navbar');

		if( phpgw::get_var('phpgw_return_as') != 'json' && $global_message = phpgwapi_cache::system_get('phpgwapi', 'phpgw_global_message'))
		{
			echo "<div class='text-center alert alert-success' role='alert'>";
			echo nl2br($global_message);
			echo '</div>';
		}


		if( phpgw::get_var('phpgw_return_as') != 'json' && $receipt = phpgwapi_cache::session_get('phpgwapi', 'phpgw_messages'))
		{
			phpgwapi_cache::session_clear('phpgwapi', 'phpgw_messages');
			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);
			$msgbox_data = $GLOBALS['phpgw']->common->msgbox($msgbox_data);
			foreach($msgbox_data as & $message)
			{
				echo "<div class='text-center {$message['msgbox_class']}' role='alert'>";
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
		$selected_node = false;
		$current_class = 'nav-item';

		if ( $id == "navbar::{$GLOBALS['phpgw_info']['flags']['menu_selection']}"
		|| ( !empty($item['nav_location']) && $item['nav_location'] == $GLOBALS['phpgw_info']['flags']['menu_selection'] ))
		{
			$current_class .= ' active';
			$item['selected'] = true;
			$selected_node = true;
		}

		$bookmark = '';
		if(!$children && preg_match("/(^navbar::)/i", $id)) // bookmarks
		{
			if(is_array($bookmarks) && isset($bookmarks[$id]))
			{
				$current_class .= ' bookmark_checked';
				$item['bookmark_id'] =$id;
				set_get_bookmarks($item);
			}
		}

		$out = <<<HTML
				<li class="{$current_class}">
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

		if($children)
		{
			$ret = <<<HTML
			$out
			{$children}
			</li>
HTML;

		}
		else
		{
			$ret = <<<HTML
			$out
			<a href="{$item['url']}" class="nav-link text-white context-menu-nav" id="{$id}" {$target}>{$bookmark}{$item['text']}</a>
			</li>
HTML;
		}

		return array('selected' =>  $selected_node, 'node' => $ret);
	}

	function render_submenu($parent, $menu, $bookmarks = array(), $parent_name = '')
	{
		static $id = 0;
		$out = '';

		foreach ( $menu as $key => &$item )
		{
			if(!empty($item['children']))
			{
				$found = false;
				foreach ($item['children'] as $child_key => $child)
				{
					if($child['url'] == $item['url'])
					{
						$found = true;
						break;
					}

					if("navbar::{$parent}::{$key}" == "navbar::{$GLOBALS['phpgw_info']['flags']['menu_selection']}")
					{
						$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::{$key}";
					}
				}

				if(!$found)
				{
					$item['children'] = array
						(
						$key => array
							(
								'text'	=> $item['text'],
								'url'	=> $item['url'],
								'image'	=> isset($item['image']) ? $item['image'] : null,
								'icon'	=> isset($item['icon']) ? $item['icon'] : null
							)
						)	+ $item['children'];
				}

			}
		}

		unset($item);
		unset($key);

		foreach ( $menu as $key => $item )
		{
//if(preg_match("/addressbook.uifields.index/", $item['url']))
//{
//	_debug_array($item);
//}
			$children = isset($item['children']) ? render_submenu(	"{$parent}::{$key}", $item['children'], $bookmarks, $item['text']) : '';
			$node = render_item($item, "navbar::{$parent}::{$key}", $children, $bookmarks);
			$out .= $node['node'];
		}

		if(!preg_match("/(nav-item active)/", $out))
		{
			$ul_class = '';
			$aria_expanded = 'false';
		}
		else
		{
			$ul_class = 'show ';
			$aria_expanded = 'true';
		}

		if($out)
		{
			$id ++;
			$out = <<<HTML
	          <a href="#_$id" data-bs-toggle="collapse text-white" aria-expanded="{$aria_expanded}" class="dropdown-toggle">{$parent_name}</a>
				<ul class="{$ul_class}list-unstyled collapse" id = "_$id">
					{$out}
				</ul>

HTML;
}
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

		$cache_refresh_token = '';
		if(!empty($GLOBALS['phpgw_info']['server']['cache_refresh_token']))
		{
			$cache_refresh_token = "?n={$GLOBALS['phpgw_info']['server']['cache_refresh_token']}";
		}

		$var = array
		(
	//		'user_fullname'	=> $GLOBALS['phpgw']->accounts->get( $GLOBALS['phpgw_info']['user']['id'] )->__toString(),
			'lang_logout_header' => lang('Choose "Log out" if you want to end the session'),
			'logout_url'	=> $GLOBALS['phpgw']->link('/logout.php'),
			'logout_text'	=> lang('logout'),
			'powered_by'	=> $powered_by,
			'lang_login'	=> lang('login'),
			'javascript_end'=> $GLOBALS['phpgw']->common->get_javascript_end($cache_refresh_token)
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