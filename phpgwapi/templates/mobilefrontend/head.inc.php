<?php
	phpgw::import_class('phpgwapi.template_portico');

	if ( !isset($GLOBALS['phpgw_info']['server']['site_title']) )
	{
		$GLOBALS['phpgw_info']['server']['site_title'] = lang('please set a site name in admin &gt; siteconfig');
	}

	$webserver_url = $GLOBALS['phpgw_info']['server']['webserver_url'];
	if($GLOBALS['phpgw_info']['server']['webserver_url'] == '/')
	{
		if (!empty($GLOBALS['phpgw_info']['server']['enforce_ssl']))
		{
			$webserver_url = "https://{$GLOBALS['phpgw_info']['server']['hostname']}";
		}
		else
		{
			$webserver_url = "http://{$GLOBALS['phpgw_info']['server']['hostname']}";
		}
	}

	$app = $GLOBALS['phpgw_info']['flags']['currentapp'];

	$GLOBALS['phpgw']->template->set_root(PHPGW_TEMPLATE_DIR);
	$GLOBALS['phpgw']->template->set_unknowns('remove');
	$GLOBALS['phpgw']->template->set_file('head', 'head.tpl');
	$GLOBALS['phpgw']->template->set_block('head', 'stylesheet', 'stylesheets');
	$GLOBALS['phpgw']->template->set_block('head', 'javascript', 'javascripts');

	$javascripts = array();

	$stylesheets = array();
	$stylesheets[] = "/phpgwapi/templates/pure/css/global.css";
	$stylesheets[] = "/phpgwapi/templates/pure/css/pure-min.css";
	$stylesheets[] = "/phpgwapi/templates/pure/css/pure-extension.css";
	$stylesheets[] = "/phpgwapi/templates/pure/css/grids-responsive-min.css";
    $stylesheets[] = "/phpgwapi/js/DataTables/extensions/Responsive/css/responsive.dataTables.min.css";

	$stylesheets[] = "/{$app}/templates/base/css/base.css";
	$stylesheets[] = "/{$app}/templates/mobilefrontend/css/base.css";
	$stylesheets[] = "/{$app}/templates/mobilefrontend/css/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css";
	$stylesheets[] = "/phpgwapi/templates/bookingfrontend/css/frontend.css";
    $stylesheets[] = "/bookingfrontend/css/bookingfrontend.css";

//	$stylesheets[] = "/phpgwapi/templates/mobilefrontend/css/frontend.css";

	foreach ( $stylesheets as $stylesheet )
	{
		if( file_exists( PHPGW_SERVER_ROOT . $stylesheet ) )
		{
			$GLOBALS['phpgw']->template->set_var( 'stylesheet_uri', $webserver_url . $stylesheet );
			$GLOBALS['phpgw']->template->parse('stylesheets', 'stylesheet', true);
		}
	}

	foreach ( $javascripts as $javascript )
	{
		if( file_exists( PHPGW_SERVER_ROOT . $javascript ) )
		{
			$GLOBALS['phpgw']->template->set_var( 'javascript_uri', $webserver_url . $javascript );
			$GLOBALS['phpgw']->template->parse('javascripts', 'javascript', true);
		}
	}


	$app = lang($app);
	$tpl_vars = array
	(
		'css'			=> $GLOBALS['phpgw']->common->get_css(),
		'javascript'	=> $GLOBALS['phpgw']->common->get_javascript(),
		'img_icon'      => $GLOBALS['phpgw']->common->find_image('phpgwapi', 'favicon.ico'),
		'site_title'	=> "{$GLOBALS['phpgw_info']['server']['site_title']}",
		'site_url'		=> $GLOBALS['phpgw']->link('/home.php', array()),
		'str_base_url'	=> $GLOBALS['phpgw']->link('/', array(), true),
		'webserver_url'	=> $webserver_url,
		'win_on_events'	=> $GLOBALS['phpgw']->common->get_on_events(),
		'current_app_header' => isset($GLOBALS['phpgw_info']['flags']['app_header']) && $GLOBALS['phpgw_info']['flags']['app_header'] ? $GLOBALS['phpgw_info']['flags']['app_header'] : '',
		'current_user'	=> $GLOBALS['phpgw']->accounts->get( $GLOBALS['phpgw_info']['user']['id'] )->__toString()
	);

	$tpl_vars['manual_text'] = lang('manual');
	$tpl_vars['manual_url'] = $GLOBALS['phpgw']->link('/index.php', array( 'menuaction' => 'manual.uidocuments.view' ));
	$tpl_vars['home_text'] = lang('home');
	$tpl_vars['home_url'] = $GLOBALS['phpgw']->link('/home.php');
	$tpl_vars['logout_text'] = lang('logout');
	$tpl_vars['logout_url'] = $GLOBALS['phpgw']->link('/logout.php');

	$GLOBALS['phpgw']->template->set_var($tpl_vars);

	$GLOBALS['phpgw']->template->pfp('out', 'head');
	unset($tpl_vars);
