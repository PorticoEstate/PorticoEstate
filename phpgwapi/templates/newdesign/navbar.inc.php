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

		//$GLOBALS['phpgw']->hooks->single('sidebox_menu',$GLOBALS['phpgw_info']['flags']['currentapp']);

		$treemenu = "";

		foreach($GLOBALS['phpgw_info']['navbar'] as $app => $app_data)
		{
			switch( $app )
			{

				case in_array($app, array('logout', 'about', 'preferences')):
					$var["{$app}_name"] = lang($app_data['title']);
					$var["{$app}_url"] = $app_data['url'];
					//$var["{$app}_icon_class"] = $app;
					break;
				case $app == $GLOBALS['phpgw_info']['flags']['currentapp']:
					$treemenu .= '<ul><li>';
					$treemenu .= "<a class=\"current\" href=\"{$app_data['url']}\">" . lang($app) . "</a>";

					$GLOBALS['phpgw']->hooks->single('sidebox_menu',$app);
					$treemenu .= render_sub_menu(0);

					$treemenu .= '</li></ul>';
					break;
				default:
					$treemenu .= '<ul><li>';
					$treemenu .= "<a href=\"{$app_data['url']}\">" . lang($app) . "</a>";
					$treemenu .= '</li></ul>';
			}
		}

		$var['treemenu'] = $treemenu;

		$GLOBALS['phpgw']->template->set_var($var);
		$GLOBALS['phpgw']->template->pfp('out','navbar');
	}

	function render_sub_menu($level)
	{
		global $menu_tmp;
		$output = "";

		if ( isset($menu_tmp[$level]) && $menu_tmp[$level] )
		{
			$output .= "<ul>\n";
			foreach($menu_tmp[$level] as $item)
			{
				if($item['text'] != '_NewLine_')
				{
					$output .= "<li>";
					if( isset($item['this']) && $item['this'] )
					{
						$output .= "<a class=\"current\" href=\"{$item['url']}\">{$item['text']}</a>";
						$output .= render_sub_menu(++$level);
					}
					else
					{
						$output .= "<a href=\"{$item['url']}\">{$item['text']}</a>";
					}

					$output .=  "</li>\n";
				}
			}
			$output .=  "</ul>\n";
		}
		return $output;
	}

	function parse_navbar_end()
	{
		$GLOBALS['phpgw']->template->set_file('footer', 'footer.tpl');
		$GLOBALS['phpgw']->template->set_root(PHPGW_TEMPLATE_DIR);

		$var = array
		(
			'powered_by'		=> lang('Powered by phpGroupWare version %1', $GLOBALS['phpgw_info']['server']['versions']['phpgwapi']),
		);
		$GLOBALS['phpgw']->template->set_var($var);
		
		$GLOBALS['phpgw']->template->pfp('out','footer');
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
?>
