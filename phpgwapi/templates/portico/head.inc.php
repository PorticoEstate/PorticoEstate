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

	$GLOBALS['phpgw_info']['server']['no_jscombine']=true;

	$javascripts = array();

	$stylesheets = array();

	phpgw::import_class('phpgwapi.jquery');
	phpgwapi_jquery::load_widget('core');

	if( !isset($GLOBALS['phpgw_info']['flags']['noframework']) )
	{
		$javascripts[] = "/phpgwapi/templates/portico/js/base.js";
	}

	if( !$GLOBALS['phpgw_info']['flags']['noframework'] && !$GLOBALS['phpgw_info']['flags']['nonavbar'] )
	{
		phpgwapi_jquery::load_widget('layout');
		phpgwapi_jquery::load_widget('jqtree');

		$GLOBALS['phpgw_info']['user']['preferences']['common']['sidecontent'] = 'ajax_menu';//ajax_menu|jsmenu
		if (isset($GLOBALS['phpgw_info']['user']['preferences']['common']['sidecontent']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['sidecontent'] == 'ajax_menu')
		{
			$javascripts[] = "/phpgwapi/templates/portico/js/jqtree_jsmenu.js";
		}

	}


	$stylesheets = array();
//	$stylesheets[] = "/phpgwapi/js/materialize/css/materialize.min.css";
	$stylesheets[] = "/phpgwapi/templates/pure/css/global.css";
//	$stylesheets[] = "/phpgwapi/templates/pure/css/demo_mmenu.css";
	$stylesheets[] = "/phpgwapi/templates/pure/css/pure-min.css";
	$stylesheets[] = "/phpgwapi/templates/pure/css/pure-extension.css";
	$stylesheets[] = "/phpgwapi/templates/pure/css/grids-responsive-min.css";
	$stylesheets[] = "/phpgwapi/js/DataTables/DataTables/css/jquery.dataTables.min.css";
	$stylesheets[] = "/phpgwapi/js/DataTables/DataTables/css/dataTables.jqueryui.min.css";
	$stylesheets[] = "/phpgwapi/js/DataTables/Responsive/css/responsive.dataTables.min.css";
	$stylesheets[] = "/phpgwapi/templates/base/css/base.css";
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
			$GLOBALS['phpgw']->template->set_var( 'stylesheet_uri', $webserver_url . $stylesheet . $cache_refresh_token);
			$GLOBALS['phpgw']->template->parse('stylesheets', 'stylesheet', true);
		}
	}

	foreach ( $javascripts as $javascript )
	{
		if( file_exists( PHPGW_SERVER_ROOT . $javascript ) )
		{
			$GLOBALS['phpgw']->template->set_var( 'javascript_uri', $webserver_url . $javascript . $cache_refresh_token );
			$GLOBALS['phpgw']->template->parse('javascripts', 'javascript', true);
		}
	}


	switch($GLOBALS['phpgw_info']['user']['preferences']['common']['template_set'])
	{
		case 'portico':
			$selecte_portico = ' selected = "selected"';
			$selecte_pure = '';
			break;
		case 'pure':
			$selecte_portico = '';
			$selecte_pure = ' selected = "selected"';
			break;
	}

	$template_selector = <<<HTML

   <select id = "template_selector">
	<option value="pure"{$selecte_pure}>Mobil</option>
	<option value="portico"{$selecte_portico}>Desktop</option>
   </select>
HTML;
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
		'css'			=> $GLOBALS['phpgw']->common->get_css($cache_refresh_token),
		'javascript'	=> $GLOBALS['phpgw']->common->get_javascript($cache_refresh_token),
		'img_icon'  => $GLOBALS['phpgw']->common->find_image('phpgwapi', 'favicon.ico'),
		'site_title'	=> "{$GLOBALS['phpgw_info']['server']['site_title']}",
		'str_base_url'	=> $GLOBALS['phpgw']->link('/', array(), true),
		'webserver_url'	=> $webserver_url,
		'win_on_events'	=> $GLOBALS['phpgw']->common->get_on_events(),
		'border_layout_config' => $_border_layout_config,
		'navbar_config' => $_navbar_config,
		'lang_collapse_all'	=> lang('collapse all'),
		'lang_expand_all'	=> lang('expand all'),
		'template_selector'	=> $template_selector
	);

	$GLOBALS['phpgw']->template->set_var($tpl_vars);

	$GLOBALS['phpgw']->template->pfp('out', 'head');
	unset($tpl_vars);

	flush();


	if( isset($GLOBALS['phpgw_info']['flags']['noframework']) )
	{
		echo '<body>';
		register_shutdown_function('parse_footer_end_noframe');
	}

	function parse_footer_end_noframe()
	{
		$javascript_end = $GLOBALS['phpgw']->common->get_javascript_end();

		$footer = <<<HTML
		</body>
		{$javascript_end}
	</html>
HTML;
		echo $footer;
	}
