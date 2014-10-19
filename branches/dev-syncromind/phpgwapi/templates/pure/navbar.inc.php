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

		$user_fullname	= $user->__toString();
		$print_url		= strpos($_SERVER['REQUEST_URI'], '?') ? "{$_SERVER['REQUEST_URI']}&phpgw_return_as=noframes" : "{$_SERVER['REQUEST_URI']}?phpgw_return_as=noframes";
		$print_text		= lang('print');
		$home_url		= $GLOBALS['phpgw']->link('/home.php');
		$home_text		= lang('home');
		$home_icon		= 'icon icon-home';
		$about_url	= $GLOBALS['phpgw']->link('/about.php', array('app' => $GLOBALS['phpgw_info']['flags']['currentapp']) );
		$about_text	= lang('about');
		$logout_url	= $GLOBALS['phpgw']->link('/logout.php');
		$logout_text	= lang('logout');


		$var['topmenu'] = <<<HTML
			<a class="pure-menu-heading" href="#">{$user_fullname}</a>
			 <ul>
				<li>
					<a href="{$print_url}"  target="_blank">{$print_text}</a>
				</li>
				<li>
					<a href="{$home_url}">{$home_text}</a>
				</li>
				<li>
					<a href="{$about_url}">{$about_text}</a>
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
			<li>
				<a href="{$help_url}">{$help_text}</a>
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
			<li>
				<a href="{$support_url}">{$support_text}</a>
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
			<li>
				<a href="{$debug_url}">{$debug_text}</a>
			</li>
HTML;
		}

		$var['topmenu'] .= <<<HTML
		<li>
			<a href="{$logout_url}">{$logout_text}</a>
		</li>
	 </ul>
HTML;

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

		if (!$nonavbar && isset($GLOBALS['phpgw_info']['user']['preferences']['common']['sidecontent']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['sidecontent'] == 'ajax_menu')
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
		else if (!$nonavbar)
		{
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
			<ul id="menutree">
HTML;
			if ( $GLOBALS['phpgw']->acl->check('run', PHPGW_ACL_READ, 'preferences') )
			{
				$preferences_url = $GLOBALS['phpgw']->link('/preferences/index.php');
				$preferences_text = lang('preferences');
				$var['treemenu'] .= <<<HTML
				<li>
					<a href="{$preferences_url}">{$preferences_text}</a>
				</li>
HTML;
			}
			$var['treemenu'] .= <<<HTML
			{$treemenu}
			</ul>
			<script type="text/javascript">
			$(document).ready(function(){
				   $('#menutree').slicknav({
					allowParentLinks: true,
					easingOpen: "swing",
					label: "",
					prependTo:'#MenutreeDiv1'
				});
				var height = $(window).height();
				$('.slicknav_menu').css({'max-height': height, 'overflow-y':'scroll'});
			});

			</script>

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

	function render_item($item, $id='', $children='')
	{
		$current_class = '';
/*
		if ( $id == "navbar::{$GLOBALS['phpgw_info']['flags']['menu_selection']}" )
		{
			$current_class = 'pure-menu-selected';
		}
*/
		if(preg_match("/(^{$id})/i", "navbar::{$GLOBALS['phpgw_info']['flags']['menu_selection']}")) // need it for MySQL and Oracle
		{
			$current_class = 'pure-menu-selected';
			$item['text'] = "<b>[ {$item['text']} ]</b>";
		}

		$link_class =" class=\"{$current_class}\"";

		$out = <<<HTML
				<li>
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
					<a href="{$item['url']}"{$link_class} id="{$id}" {$target}>{$item['text']}</a>
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
