<?php

	$menu_title = $GLOBALS['phpgw_info']['apps'][$appname]['title'] . ' '. lang('Menu');
	$menu = $GLOBALS['phpgw']->session->appsession('menu_hrm','sidebox');	
	if(isset($menu) && is_array($menu))
	{
		display_sidebox($appname,$menu_title,$menu['module'],$use_lang = false);
		if(isset($menu['sub_menu']) && $menu['sub_menu'])
		{
			display_sidebox($appname,$menu['sub_menu'] . ' ' . lang ('sub menu'),$menu['sub_menu'],$use_lang = false);	
		}
	}	
?>
