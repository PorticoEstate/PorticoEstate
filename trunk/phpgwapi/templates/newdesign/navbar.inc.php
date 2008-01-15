<?php
	function parse_navbar($force = False)
	{
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

		if (isset($GLOBALS['phpgw_info']['flags']['app_header']))
		{
			$var['current_app_title'] = $GLOBALS['phpgw_info']['flags']['app_header'];
		}
		else
		{
			$var['current_app_title'] = lang($GLOBALS['phpgw_info']['flags']['currentapp']);
		}

		if ( !isset($GLOBALS['phpgw_info']['flags']['menu_selection']) )
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = '';
		}

		$treemenu = <<<HTML
		<ul id="navbar" class="expanded">

HTML;

		prepare_navbar($navbar);
		$navigation = execMethod('phpgwapi.menu.get', 'navigation');
		$selection = explode('::', $GLOBALS['phpgw_info']['flags']['menu_selection']);
		$selected_app = array_shift($selection);

		foreach($navbar as $app => $app_data)
		{
			switch( $app )
			{
				case in_array($app, array('logout', 'about', 'preferences')):
					continue;
					break;
				default:
					$class = '';
					if ( isset($navigation[$app]) && count($navigation[$app]) )
					{
						if ( $app == $selected_app )
						{
							$class = ' class="current expanded"';
						}
						else
						{
							$class = ' class="collapsed"';
						}
					}
					$img = ' style="background-image: url(' . $GLOBALS['phpgw']->common->image($app_data['image'][0], $app_data['image'][1]) . ');"';
					$treemenu .= <<<HTML
			<li{$class} id="navbar_{$app}">
				<a href="{$app_data['url']}"{$img}>{$app_data['text']}</a>

HTML;
					if ( isset($navigation[$app]) )
					{
						$treemenu .= render_submenu("navbar_{$app}", $navigation[$app], $app == $selected_app, $selection);
					}

					$treemenu .= <<<HTML
			</li>

HTML;
					break;
			}
		}

		$treemenu .= <<<HTML
		</ul>

HTML;

		$var['treemenu'] = $treemenu;

		$GLOBALS['phpgw']->template->set_var($var);
		$GLOBALS['phpgw']->template->pfp('out','navbar');

		register_shutdown_function('parse_footer_end');
	}

	function render_submenu($parent, $menu, $expanded, $selection)
	{
		$class = $expanded ? ' class="expanded"' : ' class="collapsed"';
		$submenu = <<<HTML
				<ul{$class}>

HTML;
		$level_selection = array_shift($selection);
		foreach ( $menu as $key => $item )
		{
			$expanded = false;
			$class = '';
			if ( $level_selection === $key )
			{
				$class = 'current ';
				$expanded = true;
			}

			//echo "level_selection: $level_selection, key: $key, class: $class<br>";
			if ( isset($item['children']) && count($item['children']) )
			{
				$class = $expanded ? " class=\"{$class}expanded\"" : " class=\"{$class}collapsed\"";
			}
			else if ( $class )
			{
				$class = " class=\"{$class}\"";
			}
			$style = isset($item['image']) ? ' style="background-image: url(' . $GLOBALS['phpgw']->common->image($item['image'][0], $item['image'][1]) . ');"' : '';
			$submenu .= <<<HTML
					<li{$class} id="{$parent}::{$key}">
						<a href="{$item['url']}"{$style}>{$item['text']}</a>

HTML;
			if ( isset($item['children']) && count($item['children']) )
			{
				$submenu .= render_submenu("{$parent}::{$key}", $item['children'], $expanded, $selection);
			}
			$submenu .= <<<HTML
					</li>

HTML;
		}
		$submenu .= <<<HTML
				</ul>

HTML;
		return $submenu;
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
