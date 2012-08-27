<?php
	phpgw::import_class('phpgwapi.yui');
	phpgw::import_class('phpgwapi.template_portico');

	if ( !isset($GLOBALS['phpgw_info']['server']['site_title']) )
	{
		$GLOBALS['phpgw_info']['server']['site_title'] = lang('please set a site name in admin &gt; siteconfig');
	}

	$app = $GLOBALS['phpgw_info']['flags']['currentapp'];

	$config		= CreateObject('phpgwapi.config','bookingfrontend');
	$config->read();

	$tracker_id = isset($config->config_data['tracker_id']) && $config->config_data['tracker_id'] ? $config->config_data['tracker_id'] : '';
	unset($config);
	$tracker_code1 = <<<JS
		var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
		document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
JS;
	$tracker_code2 = <<<JS
		try 
		{
			var pageTracker = _gat._getTracker("{$tracker_id}");
			pageTracker._trackPageview();
		}
		catch(err)
		{
			alert(err);
		}
JS;

	if($tracker_id)
	{
		$GLOBALS['phpgw']->js->add_code('', $tracker_code1);
		$GLOBALS['phpgw']->js->add_code('', $tracker_code2);
	}

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
	$stylesheets[] = "/phpgwapi/templates/stavanger/css/frontend.css";

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

	// Construct navbar_config by taking into account the current selected menu
	// The only problem with this loop is that leafnodes will be included
	$navbar_config = execMethod('phpgwapi.template_portico.retrieve_local', 'navbar_config');

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

		phpgwapi_template_portico::store_local('navbar_config', $navbar_config);
	}

	$_navbar_config			= json_encode($navbar_config);
	//TODO Sigurd 8.july 2010: This one should be moved to frontend config
	$config	= CreateObject('phpgwapi.config','booking');
	$config->read();
	$logofile_frontend = isset($config->config_data['logopath_frontend']) && $config->config_data['logopath_frontend'] ? $config->config_data['logopath_frontend'] : "/phpgwapi/templates/stavanger/images/stavanger_logo.png";

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
	if ($manual !== null) 
	{
		$tpl_vars['manual_text'] = lang('manual');
		$tpl_vars['manual_url'] = $manual;
#		$tpl_vars['help_text'] = lang('help');
#		$tpl_vars['help_url'] = => '#';
	}
	$bouser = CreateObject('bookingfrontend.bouser');
	if($bouser->is_logged_in())
	{
		$tpl_vars['login_text'] = $bouser->orgnr . ' :: ' . lang('Logout');
		$tpl_vars['login_url'] = 'logout.php';
	}
	else
	{
		$tpl_vars['login_text'] = lang('Login');
		$tpl_vars['login_url'] = 'login.php?after='.urlencode($_SERVER['QUERY_STRING']);
		$config		= CreateObject('phpgwapi.config','bookingfrontend');
		$config->read();
		$login_parameter = isset($config->config_data['login_parameter']) && $config->config_data['login_parameter'] ? $config->config_data['login_parameter'] : '';
		$custom_login_url = isset($config->config_data['custom_login_url']) && $config->config_data['custom_login_url'] ? $config->config_data['custom_login_url'] : '';
		if($login_parameter)
		{
			$login_parameter = ltrim($login_parameter, '&');
			$tpl_vars['login_url'] .= "&{$login_parameter}";
		}
		if($custom_login_url)
		{
			$tpl_vars['login_url'] = $custom_login_url;
		}
	}

	$GLOBALS['phpgw']->template->set_var($tpl_vars);

	$GLOBALS['phpgw']->template->pfp('out', 'head');
	unset($tpl_vars);
