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
	
	$GLOBALS['phpgw_info']['server']['no_jscombine']=true;

	$javascripts = array();

	$stylesheets = array();

	phpgw::import_class('phpgwapi.jquery');
	phpgwapi_jquery::load_widget('core');
	phpgwapi_jquery::load_widget('layout');

	$javascripts = array
	(
		"/phpgwapi/js/json/json.js"
	);

	if( !isset($GLOBALS['phpgw_info']['flags']['noframework']) )
	{
		phpgwapi_yui::load_widget('layout');
		$javascripts[] = "/phpgwapi/templates/portico/js/base.js";
	}

	if( !$GLOBALS['phpgw_info']['flags']['noframework'] && !$GLOBALS['phpgw_info']['flags']['nonavbar'] )
	{
		phpgwapi_jquery::load_widget('treeview');

		$GLOBALS['phpgw_info']['user']['preferences']['common']['sidecontent'] = 'jsmenu';//ajax_menu|jsmenu
		if (isset($GLOBALS['phpgw_info']['user']['preferences']['common']['sidecontent']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['sidecontent'] == 'ajax_menu')
		{
			$javascripts[] = "/phpgwapi/templates/portico/js/ajax_jsmenu.js";
		}

	}


	$stylesheets = array();
	$stylesheets[] = "/phpgwapi/templates/pure/css/global.css";
//	$stylesheets[] = "/phpgwapi/templates/pure/css/demo_mmenu.css";
	$stylesheets[] = "/phpgwapi/templates/pure/css/pure-min.css";
	$stylesheets[] = "/phpgwapi/templates/pure/css/pure-extension.css";
	$stylesheets[] = "/phpgwapi/templates/pure/css/grids-responsive-min.css";
	$stylesheets[] = "/phpgwapi/js/DataTables/extensions/Responsive/css/responsive.dataTables.min.css";
	$stylesheets[] = "/phpgwapi/templates/portico/css/base.css";
	if(isset($GLOBALS['phpgw_info']['user']['preferences']['common']['theme']))
	{
		$stylesheets[] = "/phpgwapi/templates/portico/css/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css";
	}
	$stylesheets[] = "/{$app}/templates/base/css/base.css";
	$stylesheets[] = "/{$app}/templates/portico/css/base.css";
	if(isset($GLOBALS['phpgw_info']['user']['preferences']['common']['theme']))
	{
		$stylesheets[] = "/{$app}/templates/portico/css/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css";
	}

//	if(isset($GLOBALS['phpgw_info']['user']['preferences']['common']['yui_table_nowrap']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['yui_table_nowrap'])
//	{
//		$stylesheets[] = "/phpgwapi/templates/base/css/yui_table_nowrap.css";
//	}

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

	$_border_layout_config	= execMethod('phpgwapi.template_portico.retrieve_local', 'border_layout_config');

	if(isset($GLOBALS['phpgw_info']['flags']['nonavbar']) && $GLOBALS['phpgw_info']['flags']['nonavbar'])
	{
		//FIXME This one removes the sidepanels - but the previous settings are forgotten
		$_border_layout_config = true;
	}

	$_border_layout_config = json_encode($_border_layout_config);

	$_navbar_config			= json_encode($navbar_config);

	$app = lang($app);
	$tpl_vars = array
	(
		'noheader'		=> isset($GLOBALS['phpgw_info']['flags']['noheader_xsl']) && $GLOBALS['phpgw_info']['flags']['noheader_xsl'] ? 'true' : 'false',
		'nofooter'		=> isset($GLOBALS['phpgw_info']['flags']['nofooter']) && $GLOBALS['phpgw_info']['flags']['nofooter'] ? 'true' : 'false',
		'css'			=> $GLOBALS['phpgw']->common->get_css(),
		'javascript'	=> $GLOBALS['phpgw']->common->get_javascript(),
		'img_icon'      => $GLOBALS['phpgw']->common->find_image('phpgwapi', 'favicon.ico'),
		'site_title'	=> "{$GLOBALS['phpgw_info']['server']['site_title']}",
		'str_base_url'	=> $GLOBALS['phpgw']->link('/', array(), true),
		'webserver_url'	=> $GLOBALS['phpgw_info']['server']['webserver_url'],
		'win_on_events'	=> $GLOBALS['phpgw']->common->get_on_events(),
		'border_layout_config' => $_border_layout_config,
		'navbar_config' => $_navbar_config,
		'lang_collapse_all'	=> lang('collapse all'),
		'lang_expand_all'	=> lang('expand all')
	);

	$GLOBALS['phpgw']->template->set_var($tpl_vars);

	$GLOBALS['phpgw']->template->pfp('out', 'head');
	unset($tpl_vars);

	flush();

	echo '<body>';

	if( isset($GLOBALS['phpgw_info']['flags']['noframework']) )
	{
		register_shutdown_function('parse_footer_end_noframe');
	}
	
	function parse_footer_end_noframe()
	{
		if( isset($GLOBALS['phpgw_info']['flags']['noframework']) )
		{
		}

		$footer = <<<HTML
		</body>
	</html>
HTML;
		echo $footer;
	}
