<?php

	function parse_navbar($force = False)
	{
		global $debug;

		$flags = &$GLOBALS['phpgw_info']['flags'];
		$navbar = execMethod('phpgwapi.menu.get', 'navbar');

		$var = array
		(
			'about_url'		=> $GLOBALS['phpgw']->link('/about.php', array('appname' => $GLOBALS['phpgw_info']['flags']['currentapp']) ),
			'about_text'	=> lang('about'),
			'logout_url'	=> $navbar['logout']['url'],
			'logout_text'	=> $navbar['logout']['text'],
			'user_fullname' => $GLOBALS['phpgw']->common->display_fullname()
		);

		if ( isset($navbar['preferences']) )
		{
			$var['preferences_url'] = $navbar['preferences']['url'];
			$var['preferences_text'] = $navbar['preferences']['text'];
		}

		$GLOBALS['phpgw']->template->set_root(PHPGW_TEMPLATE_DIR);
		$GLOBALS['phpgw']->template->set_file('navbar', 'navbar.tpl');

		$var['current_app_title'] = isset($flags['app_header']) ? $flags['app_header'] : lang($GLOBALS['phpgw_info']['flags']['currentapp']);
		$flags['menu_selection'] = isset($flags['menu_selection']) ? $flags['menu_selection'] : '';

		prepare_navbar($navbar);
		$navigation = execMethod('phpgwapi.menu.get', 'navigation');
		$selection = explode('::', $flags['menu_selection']);
		$selected_app = array_shift($selection);

		$debug .= "<b>" . $GLOBALS['phpgw_info']['flags']['menu_selection'] . "</b><br>";

		$treemenu = "";
		foreach($navbar as $app => $app_data)
		{
			switch( $app )
			{
				case in_array($app, array('logout', 'about', 'preferences')):
					break;
				default:
					if( isset($navigation[$app]) && count($navigation[$app]) )
					{
						$expanded = ($selected_app == $app) ? 'expanded' : 'collapsed';
						$submenu = render_submenu($app, $navigation[$app]);
					}
					else
					{
						$expanded = $submenu = "";
					}
					$treemenu .= render_item($app_data, $expanded, "", $submenu);
					break;
			}
		}

		$var['treemenu'] = <<<HTML
			<ul id="navbar">
				{$treemenu}
			</ul>
HTML;

		$var['debug'] = $debug;
		$GLOBALS['phpgw']->template->set_var($var);
		$GLOBALS['phpgw']->template->pfp('out','navbar');

		register_shutdown_function('parse_footer_end');
	}

	function render_item($item, $expanded = "", $current = "", $children="")
	{
		$blank_image = $GLOBALS['phpgw']->common->find_image('phpgwapi', 'blank.png');
		$icon_image = isset($item['image']) ? $GLOBALS['phpgw']->common->image($item['image'][0], $item['image'][1]) : $blank_image;

		return <<<HTML
			<li class="{$expanded}">
				<a href="{$item['url']}" class="{$current}">
					<img src="{$blank_image}" class="{$expanded}" width="16" height="16" />
					<img src="{$icon_image}" width="16" height="16" />
					<span>
						{$item['text']}
					</span>
				</a>
				{$children}
			</li>
HTML;
	}

	function render_submenu($parent, $menu)
	{
		global $debug;
		$menu_selection = $GLOBALS['phpgw_info']['flags']['menu_selection'];
		$out = "";

		foreach ( $menu as $key => $item )
		{
			$expanded = $children = "";

			if( isset($item['children']) )
			{
				$children = render_submenu(	"{$parent}::{$key}", $item['children']);
				$expanded = preg_match("/^{$parent}::{$key}/", $menu_selection) ? 'expanded' : 'collapsed';
			}
			$current = "{$parent}::{$key}" == $menu_selection ? 'current' : '';

			$out .= render_item($item, $expanded, $current, $children);

			$debug .= "{$parent}::{$key}<br>";
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

		$var = array
		(
			'powered_by'	=> lang('Powered by phpGroupWare version %1', $GLOBALS['phpgw_info']['server']['versions']['phpgwapi']),
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
		if ( isset($navbar['admin']) )
		{
			$navbar['admin']['children'] = execMethod('phpgwapi.menu.get', 'admin');
		}
		uasort($navbar, 'sort_navbar');
	}