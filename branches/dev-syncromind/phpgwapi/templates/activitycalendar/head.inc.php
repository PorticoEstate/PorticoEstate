<?php
	phpgw::import_class('phpgwapi.yui');
	phpgw::import_class('phpgwapi.template_portico');

	if ( !isset($GLOBALS['phpgw_info']['server']['site_title']) )
	{
		$GLOBALS['phpgw_info']['server']['site_title'] = lang('please set a site name in admin &gt; siteconfig');
	}

	$app = $GLOBALS['phpgw_info']['flags']['currentapp'];

	$GLOBALS['phpgw']->template->set_root(PHPGW_TEMPLATE_DIR);
	$GLOBALS['phpgw']->template->set_unknowns('remove');
	$GLOBALS['phpgw']->template->set_file('head', 'head.tpl');
	$GLOBALS['phpgw']->template->set_block('head', 'stylesheet', 'stylesheets');
	$GLOBALS['phpgw']->template->set_block('head', 'javascript', 'javascripts');

	$javascripts = array();

	phpgwapi_yui::load_widget('dragdrop');
	phpgwapi_yui::load_widget('element');
	phpgwapi_yui::load_widget('container');
	phpgwapi_yui::load_widget('connection');
	phpgwapi_yui::load_widget('resize');
	phpgwapi_yui::load_widget('layout');

	phpgwapi_yui::load_widget('button');
	$stylesheets = array
		(
			"/phpgwapi/js/yahoo/reset-fonts-grids/reset-fonts-grids.css",
			"/phpgwapi/js/yahoo/tabview/assets/skins/sam/tabview.css",
			"/phpgwapi/js/yahoo/resize/assets/skins/sam/resize.css",
			"/phpgwapi/js/yahoo/layout/assets/skins/sam/layout.css",
		);
	$stylesheets[] = "/phpgwapi/js/yahoo/menu/assets/skins/sam/menu.css";
	$stylesheets[] = "/phpgwapi/js/yahoo/button/assets/skins/sam/button.css";
	$stylesheets[] = "/phpgwapi/templates/portico/css/base.css";
	$stylesheets[] = "/phpgwapi/templates/portico/css/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css";
	$stylesheets[] = "/{$app}/templates/base/css/base.css";
	$stylesheets[] = "/{$app}/templates/portico/css/base.css";
	$stylesheets[] = "/{$app}/templates/portico/css/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css";
	$stylesheets[] = "/phpgwapi/templates/activitycalendar/css/frontend.css";

	foreach ( $stylesheets as $stylesheet )
	{
		if( file_exists( PHPGW_SERVER_ROOT . $stylesheet ) )
		{
			$GLOBALS['phpgw']->template->set_var( 'stylesheet_uri', $GLOBALS['phpgw_info']['server']['webserver_url'] . $stylesheet );
			$GLOBALS['phpgw']->template->parse('stylesheets', 'stylesheet', true);
		}
	}

	foreach ( $javascripts as $javascript )
	{
		if( file_exists( PHPGW_SERVER_ROOT . $javascript ) )
		{
			$GLOBALS['phpgw']->template->set_var( 'javascript_uri', $GLOBALS['phpgw_info']['server']['webserver_url'] . $javascript );
			$GLOBALS['phpgw']->template->parse('javascripts', 'javascript', true);
		}
	}

	$logofile_frontend = "/phpgwapi/templates/bkbooking/images/bergen_logo.png";

	$bodoc = CreateObject('booking.bodocumentation');
	
	$manual  =  $bodoc->so->getFrontendDoc();	

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
		'navbar_config' => $_navbar_config,
		'lbl_search'   	=> lang('Search'),
		'logofile'		=> $logofile_frontend,
		'header_search_class'	=> 'hidden'//(isset($_GET['menuaction']) && $_GET['menuaction'] == 'bookingfrontend.uisearch.index' ? 'hidden' : '')
	);

	$GLOBALS['phpgw']->template->set_var($tpl_vars);

	$GLOBALS['phpgw']->template->pfp('out', 'head');
	unset($tpl_vars);
