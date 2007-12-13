<?php
	phpgw::import_class('phpgwapi.yui');

	if ( !isset($GLOBALS['phpgw_info']['server']['site_title']) )
	{
		$GLOBALS['phpgw_info']['server']['site_title'] = lang('please set a site name in admin &gt; siteconfig');
	}

	$app = $GLOBALS['phpgw_info']['flags']['currentapp'];

	$stylesheets = array
	(
		"/phpgwapi/js/yahoo/reset-fonts-grids/reset-fonts-grids.css",
		"/phpgwapi/js/yahoo/build/button/assets/skins/sam/button.css",
		"/phpgwapi/templates/newdesign/css/base.css",
		"/phpgwapi/templates/newdesign/css/icons.css",
		"/phpgwapi/templates/newdesign/css/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css",
		"/{$app}/templates/base/css/base.css",
		"/{$app}/templates/newdesign/css/base.css",
		"/{$app}/templates/newdesign/css/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css"
	);

	$GLOBALS['phpgw']->template->set_root(PHPGW_TEMPLATE_DIR);
	$GLOBALS['phpgw']->template->set_unknowns('remove');
	$GLOBALS['phpgw']->template->set_file('head', 'head.tpl');
	$GLOBALS['phpgw']->template->set_block('head', 'stylesheet', 'stylesheets');

	phpgwapi_yui::load_widget( 'dragdrop' );
	phpgwapi_yui::load_widget( 'element' );
	phpgwapi_yui::load_widget( 'button' );

	foreach ( $stylesheets as $stylesheet )
	{
		if( file_exists( PHPGW_SERVER_ROOT . $stylesheet ) )
		{
			$GLOBALS['phpgw']->template->set_var( 'stylesheet_uri', $GLOBALS['phpgw_info']['server']['webserver_url'] . $stylesheet );
			$GLOBALS['phpgw']->template->parse('stylesheets', 'stylesheet', true);
		}
	}

	$GLOBALS['phpgw']->template->set_var(array
	(
		'javascript'	=> $GLOBALS['phpgw']->common->get_javascript(),
		'site_title'	=> "{$GLOBALS['phpgw_info']['server']['site_title']}",
		'url_root'		=> $GLOBALS['phpgw']->link('/', array(), true),
		'user_fullname' => $GLOBALS['phpgw']->common->display_fullname()
	));

	$GLOBALS['phpgw']->template->pfp('out','head');
?>

