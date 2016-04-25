<?php
	$menu_title = $GLOBALS['phpgw_info']['apps'][$appname]['title'] . ' ' . lang('Menu');
	$menu = $GLOBALS['phpgw']->session->appsession('menu_property', 'sidebox');
	if (isset($menu) && is_array($menu))
	{
		display_sidebox($appname, $menu_title, $menu['module'], $use_lang = false);
		if (isset($menu['menu_title_2']) && $menu['menu_title_2'])
		{
			display_sidebox($appname, $menu['menu_title_2'] . ' ' . lang('sub menu'), $menu['sub_menu'], $use_lang = false);
		}

		if (isset($menu['menu_title_3']) && $menu['menu_title_3'])
		{
			display_sidebox($appname, $menu['menu_title_3'] . ' ' . lang('sub menu'), $menu['sub_menu_2'], $use_lang = false);
		}
	}

