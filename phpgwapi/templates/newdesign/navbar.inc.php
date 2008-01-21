<?php
	function parse_navbar($force = False)
	{
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

		if ( !isset($flags['menu_selection']) )
		{
			$flags['menu_selection'] = '';
		}

		prepare_navbar($navbar);
		$navigation = execMethod('phpgwapi.menu.get', 'navigation');
		$selection = explode('::', $flags['menu_selection']);
		$selected_app = array_shift($selection);
		$blank_image = $GLOBALS['phpgw']->common->find_image('phpgwapi', 'blank.png');

		$treemenu = '<ul id="navbar" class="expanded">';

		foreach($navbar as $app => $app_data)
		{
			switch( $app )
			{
				case in_array($app, array('logout', 'about', 'preferences')):
					continue;
					break;
				case isset($navigation[$app]) && count($navigation[$app]):
					$class = ($app == $selected_app) ? 'expanded' : 'collapsed';
					$submenu = render_submenu($app, $navigation[$app], $selection);
				default:
					$class = isset( $class ) ? $class : '';
					$icon  = $GLOBALS['phpgw']->common->image($app_data['image'][0], $app_data['image'][1]);

					$treemenu .= "<li class=\"{$class}\">";
					$treemenu .= "	<a href=\"{$app_data['url']}\">";
					$treemenu .= "		<img src=\"{$blank_image}\" class=\"{$class}\" />";
					$treemenu .= "		<img src=\"{$icon}\" /> ";
					$treemenu .= "		{$app_data['text']}";
					$treemenu .= "	</a>";
					$treemenu .= isset($submenu) ? $submenu : '';
					$treemenu .= "</li>";
					break;
			}
		}

		$treemenu .= '</ul>';

		$var['treemenu'] = $treemenu;

		$GLOBALS['phpgw']->template->set_var($var);
		$GLOBALS['phpgw']->template->pfp('out','navbar');

		register_shutdown_function('parse_footer_end');
	}

	function render_submenu($parent, $menu, $selection)
	{
		$blank_image = $GLOBALS['phpgw']->common->find_image('phpgwapi', 'blank.png');
		$level_selection = array_shift($selection);

		$submenu = '<ul>';
		foreach ( $menu as $key => $item )
		{
			$expanded = '';
			$current = '';
			if( $GLOBALS['phpgw_info']['flags']['menu_selection'] == "{$parent}::{$key}" )
			{
				$current = "current";
			}

			if( isset($item['children']) && count($item['children']) )
			{
				if ( $level_selection === $key && preg_match("/^{$parent}::{$key}/", $GLOBALS['phpgw_info']['flags']['menu_selection']) )
				{

					$expanded = 'expanded';
				}
				else
				{
					$expanded = 'collapsed';
				}

				$children = render_submenu("{$parent}::{$key}", $item['children'], $selection);
			}

			$icon = isset($item['image']) ? $GLOBALS['phpgw']->common->image($item['image'][0], $item['image'][1]) : $blank_image;

			$submenu .= "<li class=\"{$expanded}\" id=\"navbar_{$parent}::{$key}\">";
			$submenu .= "	<a href=\"{$item['url']}\" class=\"{$current}\">";
			$submenu .= "		<img src=\"{$blank_image}\" class=\"{$expanded}\" />";
			$submenu .= "		<img src=\"{$icon}\" />";
			$submenu .= "		<span>{$item['text']}</span>";
			$submenu .= "	</a>";
			$submenu .= isset($children) ? $children : '';
			$submenu .= '</li>';
		}
		$submenu .= '</ul>';

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
