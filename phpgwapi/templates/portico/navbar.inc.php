<?php

	function parse_navbar($force = False)
	{
		$navbar = array();
//		if(!isset($GLOBALS['phpgw_info']['flags']['nonavbar']) || !$GLOBALS['phpgw_info']['flags']['nonavbar'])
		{
			$navbar = execMethod('phpgwapi.menu.get', 'navbar');
		}

		$user = $GLOBALS['phpgw']->accounts->get( $GLOBALS['phpgw_info']['user']['id'] );

		$var = array
		(
			'home_url'		=> $GLOBALS['phpgw']->link('/home.php'),
			'home_text'		=> lang('home'),
			'home_icon'		=> 'icon icon-home',
			'about_url'		=> $GLOBALS['phpgw']->link('/about.php', array('app' => $GLOBALS['phpgw_info']['flags']['currentapp']) ),
			'about_text'	=> lang('about'),
			'logout_url'	=> $GLOBALS['phpgw']->link('/logout.php'),
			'logout_text'	=> lang('logout'),
			'user_fullname' => $user->__toString()
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
			'url'	=> phpgw::get_var('REQUEST_URI', 'string', 'SERVER'),
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

		if (isset($GLOBALS['phpgw_info']['user']['preferences']['common']['sidecontent']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['sidecontent'] == 'ajax_menu')
		{
			$exclude = array('logout', 'about', 'preferences');
			$i = 1;
			foreach ( $navbar as $app => $app_data )
			{
				if ( in_array($app, $exclude) )
				{
					continue;
				}

				$applications[] = array
				(
					'value'=> array
					(
						'id'	=> $i,
						'app'	=> $app,
						'label' => $app_data['text'],
						'href'	=> str_replace('&amp;','&', $app_data['url']),
					),
					'children'	=> array()
				);

				$mapping[$i] = array
				(
					'id'		=> $i,
					'name'		=> $app,
					'expanded'	=> false,
					'highlight'	=> $app == $currentapp ? true : false,
					'is_leaf'	=> false
				);
				
				$i ++;
			}
			$applications = json_encode($applications);
			$mapping = json_encode($mapping);
			$_menu_selection = str_replace('::', '|', $GLOBALS['phpgw_info']['flags']['menu_selection']);

			$var['treemenu'] = <<<HTML
				<div id="MenutreeDiv1"></div>
				<script type="text/javascript">
		 			var apps = {$applications};
					var mapping = {$mapping};
					var proxy_data = ['first_element_is_dummy'];
					var menu_selection = '{$_menu_selection}';
				</script>
HTML;
		}
		else
		{		
//			prepare_navbar($navbar);
			$navigation = execMethod('phpgwapi.menu.get', 'navigation');
			$treemenu = '';
			foreach($navbar as $app => $app_data)
			{
				if(!in_array($app, array('logout', 'about', 'preferences')))
				{
					$submenu = isset($navigation[$app]) ? render_submenu($app, $navigation[$app]) : '';
					$treemenu .= render_item($app_data, "navbar::{$app}", $submenu);
				}
			}
			$var['treemenu'] = <<<HTML
			<ul id="navbar">
{$treemenu}
			</ul>

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

	function render_item($item, $id='', $children='')
	{
		$icon_style = $expand_class = $current_class = $link_class = $parent_class = '';
		static $blank_image;
		static $images = array(); // cache

		if ( !isset($blank_image) )
		{
			$blank_image = $GLOBALS['phpgw']->common->find_image('phpgwapi', 'blank.png');
		}
		if ( isset($item['image']) )
		{
			if(!isset($images[$item['image'][0]][$item['image'][1]]))
			{
				$icon = $GLOBALS['phpgw']->common->image($item['image'][0], $item['image'][1]);
				if(!$icon)
				{
					$icon = $GLOBALS['phpgw']->common->image('phpgwapi', 'folder');
				}
				$icon_style = ' style="background-image: url(' . $icon . ')"';
				$images[$item['image'][0]][$item['image'][1]] = $icon_style;
			}
			else
			{
				$icon_style = $images[$item['image'][0]][$item['image'][1]];
			}
		}
		else
		{
			$icon = $GLOBALS['phpgw']->common->image('phpgwapi', 'folder');
			$icon_style = ' style="background-image: url(' . $icon . ')"';
		//	$images[$item['image'][0]][$item['image'][1]] = $icon_style;
		}
		if ( $children )
		{
			$expand_class = item_expanded($id) ? ' class="expanded"' : ' class="collapsed"';
			$parent_class = ' parent';
		}
		if ( $id == "navbar::{$GLOBALS['phpgw_info']['flags']['menu_selection']}" )
		{
			$current_class = 'current';
		}

		$link_class =" class=\"{$current_class}{$parent_class}\"";

		$out = <<<HTML
				<li{$expand_class}>

HTML;
		if( $expand_class )
		{
		$out .= <<<HTML
							<img src="{$blank_image}"{$expand_class}width="16" height="16" alt="+/-" />

HTML;
		}
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
					<a href="{$item['url']}"{$link_class}{$icon_style} id="{$id}" {$target}>
						<span>{$item['text']}</span>
					</a>
{$children}
				</li>

HTML;
	}

	function render_submenu($parent, $menu)
	{
		$out = '';
		foreach ( $menu as $key => $item )
		{
			$children = isset($item['children']) ? render_submenu(	"{$parent}::{$key}", $item['children']) : '';
			$out .= render_item($item, "navbar::{$parent}::{$key}", $children);
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
			'powered_by'	=> $powered_by,
			'lang_login'	=> lang('login')
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
