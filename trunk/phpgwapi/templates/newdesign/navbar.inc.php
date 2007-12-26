<?php
	global $menu_tmp;

	function parse_navbar($force = False)
	{
		global $menu_tmp;
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

		$treemenu = <<<HTML
		<ul id="navbar" class="expanded">

HTML;

		$navbar = execMethod('phpgwapi.menu.get', 'navbar');
		prepare_navbar($navbar);
		$navigation = execMethod('phpgwapi.menu.get', 'navigation');

		foreach($navbar as $app => $app_data)
		{
			switch( $app )
			{
				case in_array($app, array('logout', 'about', 'preferences')):
					continue;
					break;
				default:
					$class = $app == $GLOBALS['phpgw_info']['flags']['currentapp'] ? ' class="current expanded"' : ' class="expanded"';
					$img = ' style="background-image: url(' . $GLOBALS['phpgw']->common->image($app_data['image'][0], $app_data['image'][1]) . ');"';
					$treemenu .= <<<HTML
			<li{$class}>
				<a href="{$app_data['url']}"{$img}>{$app_data['text']}</a>

HTML;
					if ( isset($navigation[$app]) )
					{
						$treemenu .= render_submenu($navigation[$app], true);
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

	function render_submenu($menu, $expanded)
	{
		$class = $expanded ? ' class="expanded"' : '';
		$submenu = <<<HTML
				<ul{$class}>

HTML;
		foreach ( $menu as $item )
		{
			$class = isset($item['children']) && count($item['children']) ? ' class="expanded"' : '';
			$style = isset($item['image']) ? ' style="background-image: url(' . $GLOBALS['phpgw']->common->image($item['image'][0], $item['image'][1]) . ');"' : '';
			$submenu .= <<<HTML
					<li{$class}>
						<a href="{$item['url']}"{$style}>{$item['text']}</a>

HTML;
			if ( isset($item['children']) && count($item['children']) )
			{
				$submenu .= render_submenu($item['children'], true); //isset($menu['children']) && count($menu['children']) );
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

		$GLOBALS['phpgw']->template->pfp('out','footer');
		$footer_included = true;
	}

	function display_sidebox($appname, $menu_title, $file, $use_lang = true)
	{
		global $menu_tmp;
		$i = count($menu_tmp);
		$menu_tmp[$i] = $file;

		//echo "<ul>";
		//echo "<li>{$appname} - {$menu_title}</li>";
		//foreach ( $file as $item )
		//{
		//	if( $item['this'])
		//		echo "<li><a href=\"{$item['url']}\">* {$item['text']}</a></li>";
		//	else
		//		echo "<li><a href=\"{$item['url']}\">{$item['text']}</a></li>";
			//echo "<pre>";
			//var_dump($item);
		//}
		//echo "</ul>";
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
		uasort($navbar, 'sort_navbar');
	}

