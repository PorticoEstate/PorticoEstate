<?php
	global $menu_tmp;

	function parse_navbar($force = False)
	{
		global $menu_tmp;
		//$var = array();
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

		foreach($GLOBALS['phpgw_info']['navbar'] as $app => $app_data)
		{
			echo "<ul><li>";

			if( $app == $GLOBALS['phpgw_info']['flags']['currentapp'] )
			{
				echo "<a href=\"{$app_data['url']}\">* {$app_data['title']}</a>";
				$GLOBALS['phpgw']->hooks->single('sidebox_menu',$app);
				render_sub_menu(0);
			}
			else
			{
				echo "<a href=\"{$app_data['url']}\">{$app_data['title']}</a>";
			}
			//$menu_tmp=array();
			echo "</li></ul>";
		}



		$GLOBALS['phpgw']->template->set_var($var);
		$GLOBALS['phpgw']->template->pfp('out','navbar');
		//echo "<pre>";

		//var_dump($GLOBALS['phpgw']->session->appsession('menu_newdesign','sidebox'));

	}
	function render_sub_menu($level)
	{
		global $menu_tmp;
		if($menu_tmp[$level])
		{
			echo "<ul>";
			foreach($menu_tmp[$level] as $item)
			{
				echo "<li>";
				if( $item['this'])
				{
					echo "<a href=\"{$item['url']}\">* {$item['text']}</a>";
					render_sub_menu(++$level);
				}
				else
				{
					echo "<a href=\"{$item['url']}\">{$item['text']}</a>";
				}

				echo "</li>";
			}
			echo "</ul>";
		}
	}

	function parse_navbar_end()
	{
		$GLOBALS['phpgw']->template->set_root(PHPGW_TEMPLATE_DIR);
		$GLOBALS['phpgw']->template->set_file('footer', 'footer.tpl');
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