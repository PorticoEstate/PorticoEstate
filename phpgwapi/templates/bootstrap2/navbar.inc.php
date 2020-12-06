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

		$print_url				 = "{$_SERVER['PHP_SELF']}?" . http_build_query(array_merge($extra_vars, array('phpgw_return_as' => 'noframes')));
		$user_fullname			 = $user->__toString();
		$print_text				 = lang('print');
		$home_url				 = $GLOBALS['phpgw']->link('/home.php');
		$home_text				 = lang('home');
		$about_url				 = $GLOBALS['phpgw']->link('/about.php', array('app' => $GLOBALS['phpgw_info']['flags']['currentapp']));
		$about_text				 = lang('about');
		$logout_url				 = $GLOBALS['phpgw']->link('/logout.php');
		$logout_text			 = lang('logout');
		$var['home_url']		 = $home_url;
		$var['user_fullname']	 = $user_fullname;
		$var['site_title']		 = $GLOBALS['phpgw_info']['server']['site_title'];


		$var['img_site_cover'] = $GLOBALS['phpgw']->common->find_image('phpgwapi', 'cover-transparent-white.png');
		$var['img_site_logo'] = $GLOBALS['phpgw']->common->find_image('phpgwapi', 'logo-transparent-white.png');
		$var['img_undraw_rocket'] = $GLOBALS['phpgw']->common->find_image('phpgwapi', 'undraw_rocket.svg');
		$undraw_profile = $GLOBALS['phpgw']->common->find_image('phpgwapi', 'undraw_profile.svg');
		$undraw_profile_1 = $GLOBALS['phpgw']->common->find_image('phpgwapi', 'undraw_profile_1.svg');
		$undraw_profile_2 = $GLOBALS['phpgw']->common->find_image('phpgwapi', 'undraw_profile_2.svg');
		$undraw_profile_3 = $GLOBALS['phpgw']->common->find_image('phpgwapi', 'undraw_profile_3.svg');


		$preferences_url = $GLOBALS['phpgw']->link('/preferences/index.php');
		$preferences_text = lang('preferences');

		switch($GLOBALS['phpgw_info']['user']['preferences']['common']['template_set'])
		{
			case 'portico':
				$selecte_portico = ' selected = "selected"';
				$selecte_bootstrap = '';
				$selecte_bootstrap2 = '';
				break;
			case 'bootstrap':
				$selecte_portico = '';
				$selecte_bootstrap = ' selected = "selected"';
				$selecte_bootstrap2 = '';
				break;
			case 'bootstrap2':
				$selecte_portico = '';
				$selecte_bootstrap = '';
				$selecte_bootstrap2 = ' selected = "selected"';
				break;
		}
		$template_selector = <<<HTML


		<select id = "template_selector" class="btn btn-sm dropdown-toggle" style="padding-top: .315rem;-webkit-appearance: none;-moz-appearance: none;">
			<option class="nav-link" value="bootstrap"{$selecte_bootstrap}>Bootstrap</option>
			<option value="bootstrap2"{$selecte_bootstrap2}>bootstrap2</option>
			<option value="portico"{$selecte_portico}>Portico</option>
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



		if($breadcrumbs[0]['id'] != $breadcrumb_selection)
		{
			array_unshift($breadcrumbs, $current_url);
		}
		if(count($breadcrumbs) >= 6)
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

			$navbar_state = execMethod('phpgwapi.template_portico.retrieve_local', 'menu_state');

			$var['menu_state'] = $navbar_state['menu_state'];

			$bookmarks = phpgwapi_cache::user_get('phpgwapi', "bookmark_menu", $GLOBALS['phpgw_info']['user']['id']);
//			_debug_array($bookmarks);
			$lang_bookmarks = lang('bookmarks');

			$navigation = execMethod('phpgwapi.menu.get', 'navigation');
			$treemenu = '';
			foreach($navbar as $app => $app_data)
			{
				if(!in_array($app, array('logout', 'about', 'preferences')))
				{
					$submenu = isset($navigation[$app]) ? render_submenu($app, $navigation[$app], $bookmarks, $app_data['text']) : '';
					$node = render_item($app_data, "navbar::{$app}", $submenu, $bookmarks);
					$treemenu .= $node['node'];
				}
			}
			$var['treemenu'] = $treemenu;
		}


		$breadcrumb_html = "";

		if(phpgw::get_var('phpgw_return_as') != 'json' && $breadcrumbs && is_array($breadcrumbs))// && isset($GLOBALS['phpgw_info']['user']['preferences']['common']['show_breadcrumbs']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['show_breadcrumbs'])
		{
			$breadcrumb_html = <<<HTML
				<nav aria-label="breadcrumb">
				  <ol class="breadcrumb">
HTML;
			$history_url = array();
			for($i=0;$i< (count($breadcrumbs) -1); $i++)
			{
				$breadcrumb_html .= <<<HTML
					<li class="breadcrumb-item"><a href="{$breadcrumbs[$i]['url']}">{$breadcrumbs[$i]['name']}</a></li>
HTML;
			}

			$breadcrumb_html .= <<<HTML
				    <li class="breadcrumb-item active" aria-current="page">{$breadcrumbs[$i]['name']}</li>
HTML;

			$breadcrumb_html .= <<<HTML
				</ol>
			  </nav>

HTML;

		}

		$var['breadcrumb'] = $breadcrumb_html;

		$manual_option = '';
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
			$manual_option .= <<<HTML
			<li class="nav-item">
				<a href="{$help_url}" class="nav-link">{$help_text}</a>
			</li>
HTML;
			}


		$support_option = '';
		if(isset($GLOBALS['phpgw_info']['server']['support_address']) && $GLOBALS['phpgw_info']['server']['support_address'])
		{
			$support_url = "javascript:openwindow('"
			 . $GLOBALS['phpgw']->link('/index.php', array
			 (
			 	'menuaction'=> 'manual.uisupport.send',
			 	'app' => $GLOBALS['phpgw_info']['flags']['currentapp'],
			 )) . "','700','600')";

			$support_text = lang('support');

			$support_option = <<<HTML
			<li class="nav-item">
				<a href="{$support_url}" class="nav-link">{$support_text}</a>
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
			<li class="nav-item">
				<a href="{$debug_url}" class="nav-link">{$debug_text}</a>
			</li>
HTML;
		}


		$bookmark_option = '';
			$collected_bm = set_get_bookmarks();
			if($collected_bm)
			{
				$bookmark_option .= <<<HTML

				<li class="nav-item dropdown no-arrow">
					 <a class="nav-link dropdown-toggle" href="#" id="bookmarkDropdown" role="button"
						 data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						 <span class="mr-2 d-none d-lg-inline text-gray-600 small">{$lang_bookmarks}</span>
					  </a>
					 <!-- Dropdown - bookmarks -->
					 <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
						aria-labelledby="bookmarkDropdown">


HTML;

					foreach($collected_bm as $entry)
					{
						$seleced_bm = 'dropdown-item';
						if( isset($entry['selected']) && $entry['selected'])
						{
							$seleced_bm .= ' active';
						}

						$bookmark_option .= <<<HTML

						 <a class="$seleced_bm" href="{$entry['url']}" id="bookmark_{$entry['bookmark_id']}>
							 <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
							 {$entry['text']}
						 </a>
HTML;

					}
					$bookmark_option .= '</div></li>';
			}
			else
			{
				$bookmark_option .= <<<HTML

				<li class="nav-item disabled">
					<a href="#" class="nav-link">{$lang_bookmarks}</a>
				</li>
HTML;

			}

		if ( isset($GLOBALS['phpgw_info']['user']['apps']['messenger']) )
		{
			$bomessenger	 = CreateObject('messenger.bomessenger');
			$total_messages	 = $bomessenger->total_messages(" AND message_status = 'N'");
			if ($total_messages > 0)
			{
				$new_messages		 = $total_messages;
				$new_messages_alert	 = "<span class='badge badge-danger badge-counter'>{$new_messages}</span>";
			}
			else
			{
				$new_messages		 = 0;
				$new_messages_alert	 = '';
			}
			$total_messages = $total_messages;
		}
		$var['topbar'] = <<<HTML

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
						{$template_selector}
 						{$manual_option}
						{$debug_option}
						{$support_option}
						{$bookmark_option}
                        <!-- Nav Item - Alerts -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <!-- Counter - Alerts -->
                                <span class="badge badge-danger badge-counter">3+</span>
                            </a>
                            <!-- Dropdown - Alerts -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header">
                                    Alerts Center
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-primary">
                                            <i class="fas fa-file-alt text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 12, 2019</div>
                                        <span class="font-weight-bold">A new monthly report is ready to download!</span>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-success">
                                            <i class="fas fa-donate text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 7, 2019</div>
                                        $290.29 has been deposited into your account!
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-warning">
                                            <i class="fas fa-exclamation-triangle text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 2, 2019</div>
                                        Spending Alert: We've noticed unusually high spending for your account.
                                    </div>
                                </a>
                                <a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>
                            </div>
                        </li>

                        <!-- Nav Item - Messages -->
                        <li class="nav-item dropdown no-arrow mx-1" onClick="get_messages();">
                            <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-envelope fa-fw"></i>
                                <!-- Counter - Messages -->
								{$new_messages_alert}
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="messagesDropdown">
                                <h6 class="dropdown-header">
                                    Message Center
                                </h6>
								<div id="messages"></div>
                                <!--a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="{$undraw_profile_1}"
                                            alt="">
                                        <div class="status-indicator bg-success"></div>
                                    </div>
                                    <div class="font-weight-bold">
                                        <div class="text-truncate">Hi there! I am wondering if you can help me with a
                                            problem I've been having.</div>
                                        <div class="small text-gray-500">Emily Fowler · 58m</div>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="{$undraw_profile_2}"
                                            alt="">
                                        <div class="status-indicator"></div>
                                    </div>
                                    <div>
                                        <div class="text-truncate">I have the photos that you ordered last month, how
                                            would you like them sent to you?</div>
                                        <div class="small text-gray-500">Jae Chun · 1d</div>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="{$undraw_profile_3}"
                                            alt="">
                                        <div class="status-indicator bg-warning"></div>
                                    </div>
                                    <div>
                                        <div class="text-truncate">Last month's report looks great, I am very happy with
                                            the progress so far, keep up the good work!</div>
                                        <div class="small text-gray-500">Morgan Alvarez · 2d</div>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="https://source.unsplash.com/Mv9hjnEUHR4/60x60"
                                            alt="">
                                        <div class="status-indicator bg-success"></div>
                                    </div>
                                    <div>
                                        <div class="text-truncate">Am I a good boy? The reason I ask is because someone
                                            told me that people say this to all dogs, even if they aren't good...</div>
                                        <div class="small text-gray-500">Chicken the Dog · 2w</div>
                                    </div>
                                </a-->
                                <a class="dropdown-item text-center small text-gray-500" href="#">Read More Messages</a>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">$user_fullname</span>
                                <img class="img-profile rounded-circle"
                                    src="{$undraw_profile}">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <a class="dropdown-item" href="{$preferences_url}">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
									{$preferences_text}
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Activity Log
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    {$logout_text}
                                </a>
                            </div>
                        </li>

                    </ul>


HTML;

		$GLOBALS['phpgw']->template->set_var($var);
		$GLOBALS['phpgw']->template->pfp('out','navbar');

		if( phpgw::get_var('phpgw_return_as') != 'json' && $global_message = phpgwapi_cache::system_get('phpgwapi', 'phpgw_global_message'))
		{
			echo "<div class='msg_good'>";
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
		$selected_node = false;
		$current_class = 'nav-item';

		if ( $id == "navbar::{$GLOBALS['phpgw_info']['flags']['menu_selection']}" 
		|| ( !empty($item['location_id']) && $item['location_id'] == $GLOBALS['phpgw_info']['flags']['menu_selection'] ))
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

		if ($children)
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
			<a href="{$item['url']}" class="nav-link context-menu-nav" id="{$id}" {$target}>{$bookmark}{$item['text']}</a>
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
								'image'	=> $item['image']
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
	          <a href="#_$id" data-toggle="collapse" aria-expanded="{$aria_expanded}" class="nav-link collapsed" data-target="#_$id">{$parent_name}</a>
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

		$webserver_url = $GLOBALS['phpgw_info']['server']['webserver_url'];

      $javascript_end = <<<HTML
		<script src="{$webserver_url}/phpgwapi/templates/bootstrap2/js/jquery-easing/jquery.easing.min.js"></script>

		    <!-- Custom scripts for all pages-->
		<script src="{$webserver_url}/phpgwapi/templates/bootstrap2/js/sb-admin-2.min.js"></script>
HTML;
		$javascript_end .= $GLOBALS['phpgw']->common->get_javascript_end($cache_refresh_token);

		$logout_url	= $GLOBALS['phpgw']->link('/logout.php');
		$logout_text	= lang('logout');

		$var = array
		(
	//		'user_fullname'	=> $GLOBALS['phpgw']->accounts->get( $GLOBALS['phpgw_info']['user']['id'] )->__toString(),
			'powered_by'	=> $powered_by,
			'lang_login'	=> lang('login'),
			'logout_url'	=> $logout_url,
			'logout_text'	=> $logout_text,
			'javascript_end'=> $javascript_end
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