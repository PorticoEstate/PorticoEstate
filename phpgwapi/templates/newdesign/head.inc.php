<?php
	phpgw::import_class('phpgwapi.yui');
	phpgw::import_class('phpgwapi.template_newdesign');

	if ( !isset($GLOBALS['phpgw_info']['server']['site_title']) )
	{
		$GLOBALS['phpgw_info']['server']['site_title'] = lang('please set a site name in admin &gt; siteconfig');
	}

	$app = $GLOBALS['phpgw_info']['flags']['currentapp'];

	$stylesheets = array
	(
		"/phpgwapi/js/yahoo/reset-fonts-grids/reset-fonts-grids.css",
		"/phpgwapi/js/yahoo/build/menu/assets/skins/sam/menu.css",
		"/phpgwapi/js/yahoo/build/button/assets/skins/sam/button.css",
		"/phpgwapi/js/yahoo/build/tabview/assets/skins/sam/tabview.css",
		"/phpgwapi/templates/newdesign/css/base.css",
		"/phpgwapi/templates/newdesign/css/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css",
		"/{$app}/templates/base/css/base.css",
		"/{$app}/templates/newdesign/css/base.css",
		"/{$app}/templates/newdesign/css/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css"
	);

	$GLOBALS['phpgw']->template->set_root(PHPGW_TEMPLATE_DIR);
	$GLOBALS['phpgw']->template->set_unknowns('remove');
	$GLOBALS['phpgw']->template->set_file('head', 'head.tpl');
	$GLOBALS['phpgw']->template->set_block('head', 'stylesheet', 'stylesheets');

	phpgwapi_yui::load_widget('dragdrop');
	phpgwapi_yui::load_widget('element');
	phpgwapi_yui::load_widget('container');
	phpgwapi_yui::load_widget('menu');
	phpgwapi_yui::load_widget('button');
	phpgwapi_yui::load_widget('connection');

	foreach ( $stylesheets as $stylesheet )
	{
		if( file_exists( PHPGW_SERVER_ROOT . $stylesheet ) )
		{
			$GLOBALS['phpgw']->template->set_var( 'stylesheet_uri', $GLOBALS['phpgw_info']['server']['webserver_url'] . $stylesheet );
			$GLOBALS['phpgw']->template->parse('stylesheets', 'stylesheet', true);
		}
	}

	// Construct navbar_config by taking into account the current selected menu
	// The only problem with this loop is that nodes without children allso will
	// get set at expanded even though that isn't needed.

	$navbar_config = execMethod('phpgwapi.template_newdesign.retrieve_local', 'navbar_config');

	if( isset($GLOBALS['phpgw_info']['flags']['menu_selection']) )
	{
		if(!isset($navbar_config))
		{
			$navbar_config = array();
		}

		$current_selection = $GLOBALS['phpgw_info']['flags']['menu_selection'];

		while($current_selection)
		{
			$navbar_config["navbar::$current_selection"] = true;
			$current_selection = implode("::", explode("::", $current_selection, -1));
		}

		phpgwapi_template_newdesign::store_local('navbar_config', $navbar_config);
	}

	//var_dump($navbar_config);

	$app = lang($app);
	$tpl_vars = array
	(
		'css'			=> $GLOBALS['phpgw']->common->get_css(),
		'javascript'	=> $GLOBALS['phpgw']->common->get_javascript(),
		'img_icon'      => $GLOBALS['phpgw']->common->find_image('phpgwapi', 'favicon.ico'),
		'site_title'	=> "{$GLOBALS['phpgw_info']['server']['site_title']}",
		'str_base_url'	=> $GLOBALS['phpgw']->link('/', array(), true),
		'webserver_url'	=> $GLOBALS['phpgw_info']['server']['webserver_url'],
		'win_on_events'	=> $GLOBALS['phpgw']->common->get_on_events(),
		'border_layout_config' => json_encode(execMethod('phpgwapi.template_newdesign.retrieve_local', 'border_layout_config')),
		'navbar_config' => json_encode($navbar_config)
	);

	$GLOBALS['phpgw']->template->set_var($tpl_vars);

	$GLOBALS['phpgw']->template->pfp('out', 'head');
	unset($tpl_vars);
