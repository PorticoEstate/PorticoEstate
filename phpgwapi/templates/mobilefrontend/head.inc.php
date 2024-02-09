<?php
	phpgw::import_class('phpgwapi.template_portico');

	if ( !isset($GLOBALS['phpgw_info']['server']['site_title']) )
	{
		$GLOBALS['phpgw_info']['server']['site_title'] = lang('please set a site name in admin &gt; siteconfig');
	}

	$webserver_url = $GLOBALS['phpgw_info']['server']['webserver_url'];

	$app = $GLOBALS['phpgw_info']['flags']['currentapp'];

	$cache_refresh_token = '';
	if(!empty($GLOBALS['phpgw_info']['server']['cache_refresh_token']))
	{
		$cache_refresh_token = "?n={$GLOBALS['phpgw_info']['server']['cache_refresh_token']}";
	}

	$GLOBALS['phpgw']->template->set_root(PHPGW_TEMPLATE_DIR);
	$GLOBALS['phpgw']->template->set_unknowns('remove');
	$GLOBALS['phpgw']->template->set_file('head', 'head.tpl');
	$GLOBALS['phpgw']->template->set_block('head', 'stylesheet', 'stylesheets');
	$GLOBALS['phpgw']->template->set_block('head', 'javascript', 'javascripts');

	$GLOBALS['phpgw_info']['server']['no_jscombine']=false;

	$javascripts = array();
	$stylesheets = array();

	phpgw::import_class('phpgwapi.jquery');
	phpgwapi_jquery::load_widget('core');
	phpgwapi_jquery::load_widget('ui');

	$javascripts[]	 = "/phpgwapi/js/popper/popper2.min.js";
	$javascripts[]	 = "/phpgwapi/js/bootstrap5/vendor/twbs/bootstrap/dist/js/bootstrap.min.js";

	$javascripts[] = "/phpgwapi/templates/mobilefrontend/js/keep_alive.js";

	$stylesheets[] = "/phpgwapi/templates/pure/css/global.css";
	$stylesheets[] = "/phpgwapi/templates/pure/css/version_3/pure-min.css";
	$stylesheets[] = "/phpgwapi/templates/pure/css/pure-extension.css";
	$stylesheets[] = "/phpgwapi/templates/pure/css/version_3/grids-responsive-min.css";
	$stylesheets[] = "/phpgwapi/js/DataTables/DataTables/css/jquery.dataTables.min.css";
	$stylesheets[] = "/phpgwapi/js/DataTables/DataTables/css/dataTables.jqueryui.min.css";
	$stylesheets[] = "/phpgwapi/js/DataTables/Responsive/css/responsive.dataTables.min.css";


	$stylesheets[]	 = "/phpgwapi/js/bootstrap5/vendor/twbs/bootstrap/dist/css/bootstrap.min.css";

//	$stylesheets[] = "/{$app}/templates/base/css/base.css";
	$stylesheets[] = "/{$app}/templates/mobilefrontend/css/base.css";
//	$stylesheets[] = "/{$app}/templates/mobilefrontend/css/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css";
	$stylesheets[] = "/phpgwapi/templates/mobilefrontend/css/base.css";
//	$stylesheets[] = "/phpgwapi/templates/bookingfrontend/css/fontawesome.all.css";
	$stylesheets[] = "/phpgwapi/templates/base/css/fontawesome/css/all.min.css";

	foreach ( $stylesheets as $stylesheet )
	{
		if( file_exists( PHPGW_SERVER_ROOT . $stylesheet ) )
		{
			$GLOBALS['phpgw']->template->set_var( 'stylesheet_uri', $webserver_url . $stylesheet . $cache_refresh_token );
			$GLOBALS['phpgw']->template->parse('stylesheets', 'stylesheet', true);
		}
	}

	if (!$GLOBALS['phpgw_info']['server']['no_jscombine'])
	{
		$_jsfiles = array();
		foreach ($javascripts as $javascript)
		{
			if (file_exists(PHPGW_SERVER_ROOT . $javascript))
			{
				// Add file path to array and replace path separator with "--" for URL-friendlyness
				$_jsfiles[] = str_replace('/', '--', ltrim($javascript, '/'));
			}
		}

		$cachedir	 = urlencode("{$GLOBALS['phpgw_info']['server']['temp_dir']}/combine_cache");
		$jsfiles	 = implode(',', $_jsfiles);
		$GLOBALS['phpgw']->template->set_var('javascript_uri', "{$webserver_url}/phpgwapi/inc/combine.php?cachedir={$cachedir}&type=javascript&files={$jsfiles}");
		$GLOBALS['phpgw']->template->parse('javascripts', 'javascript', true);
		unset($jsfiles);
		unset($_jsfiles);
	}
	else
	{
		foreach ( $javascripts as $javascript )
		{
			if( file_exists( PHPGW_SERVER_ROOT . $javascript ) )
			{
				$GLOBALS['phpgw']->template->set_var( 'javascript_uri', $webserver_url . $javascript . $cache_refresh_token );
				$GLOBALS['phpgw']->template->parse('javascripts', 'javascript', true);
			}
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

	$app = lang($app);
	$tpl_vars = array
	(
		'noheader'		=> isset($GLOBALS['phpgw_info']['flags']['noheader_xsl']) && $GLOBALS['phpgw_info']['flags']['noheader_xsl'] ? 'true' : 'false',
		'nofooter'		=> isset($GLOBALS['phpgw_info']['flags']['nofooter']) && $GLOBALS['phpgw_info']['flags']['nofooter'] ? 'true' : 'false',
		'css'			=> $GLOBALS['phpgw']->common->get_css($cache_refresh_token),
		'javascript'	=> $GLOBALS['phpgw']->common->get_javascript($cache_refresh_token),
		'img_icon'      => $GLOBALS['phpgw']->common->find_image('phpgwapi', 'favicon.ico'),
		'site_title'	=> "{$GLOBALS['phpgw_info']['server']['site_title']}",
		'site_url'		=> $GLOBALS['phpgw']->link('/home.php', array()),
		'str_base_url'	=> $GLOBALS['phpgw']->link('/', array(), true),
		'userlang'		=> $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'],
		'webserver_url'	=> $webserver_url,
		'win_on_events'	=> $GLOBALS['phpgw']->common->get_on_events(),
		'navbar_config' => $_navbar_config,
	);

	$GLOBALS['phpgw']->template->set_var($tpl_vars);

	$GLOBALS['phpgw']->template->pfp('out', 'head');
	unset($tpl_vars);
