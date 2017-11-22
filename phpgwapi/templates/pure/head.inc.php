<?php
	$javascripts = array();
	$stylesheets = array();

	$webserver_url = $GLOBALS['phpgw_info']['server']['webserver_url'];

	phpgw::import_class('phpgwapi.jquery');
	phpgwapi_jquery::load_widget('core');

	$GLOBALS['phpgw_info']['server']['no_jscombine']=true;
	if( !$GLOBALS['phpgw_info']['flags']['noframework'] && !$GLOBALS['phpgw_info']['flags']['nonavbar'] )
	{
		$javascripts[] = "/phpgwapi/js/jquery/mmenu/core/js/jquery.mmenu.min.all.js";
		$javascripts[] = "/phpgwapi/templates/pure/js/mmenu.js";

		$stylesheets[] = "/phpgwapi/js/jquery/mmenu/core/css/jquery.mmenu.all.css";

		$menu_stylesheet_widescreen = '';

/*
		$menu_stylesheet_widescreen = <<<HTML

		<link href="{$webserver_url}/phpgwapi/js/jquery/mmenu/extensions/css/jquery.mmenu.widescreen.css" type="text/css" rel="stylesheet" media="all and (min-width: 1430px)" />
HTML;
*/
	}
	else
	{
		$menu_stylesheet_widescreen = '';
	}

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

	$stylesheets[] = "/phpgwapi/templates/pure/css/global.css";
	$stylesheets[] = "/phpgwapi/templates/pure/css/demo_mmenu.css";
	$stylesheets[] = "/phpgwapi/templates/pure/css/pure-min.css";
	$stylesheets[] = "/phpgwapi/templates/pure/css/pure-extension.css";
	$stylesheets[] = "/phpgwapi/templates/pure/css/grids-responsive-min.css";
	$stylesheets[] = "/phpgwapi/js/DataTables/extensions/Responsive/css/responsive.dataTables.min.css";
//	$stylesheets[] = "/phpgwapi/templates/base/css/base.css";

//	$stylesheets[] = "/phpgwapi/templates/pure/css/side-menu.css";
//	$stylesheets[] = "/phpgwapi/templates/pure/css/baby-blue.css";


	if(isset($GLOBALS['phpgw_info']['user']['preferences']['common']['theme']))
	{
		$stylesheets[] = "/phpgwapi/templates/pure/themes/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css";
	}
	$stylesheets[] = "/{$app}/templates/base/css/base.css";
	//$stylesheets[] = "/{$app}/templates/portico/css/base.css";
	if(isset($GLOBALS['phpgw_info']['user']['preferences']['common']['theme']))
	{
		$stylesheets[] = "/{$app}/templates/pure/themes/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css";
	}

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

	$tpl_vars = array
	(
		'noheader'		=> isset($GLOBALS['phpgw_info']['flags']['noheader_xsl']) && $GLOBALS['phpgw_info']['flags']['noheader_xsl'] ? 'true' : 'false',
		'nofooter'		=> isset($GLOBALS['phpgw_info']['flags']['nofooter']) && $GLOBALS['phpgw_info']['flags']['nofooter'] ? 'true' : 'false',
		'css'			=> $GLOBALS['phpgw']->common->get_css(),
		'javascript'	=> $GLOBALS['phpgw']->common->get_javascript(),
		'img_icon'      => $GLOBALS['phpgw']->common->find_image('phpgwapi', 'favicon.ico'),
		'site_title'	=> "{$GLOBALS['phpgw_info']['server']['site_title']}",
		'str_base_url'	=> $GLOBALS['phpgw']->link('/', array(), true),
		'webserver_url'	=> $webserver_url,
		'win_on_events'	=> $GLOBALS['phpgw']->common->get_on_events(),
		'menu_stylesheet_widescreen'=> $menu_stylesheet_widescreen,
		'template_selector'			=> $template_selector
	);

	$GLOBALS['phpgw']->template->set_var($tpl_vars);

	$GLOBALS['phpgw']->template->pfp('out', 'head');
	unset($tpl_vars);

	flush();

	echo "\t<body>";

	if( isset($GLOBALS['phpgw_info']['flags']['noframework']) )
	{
//		echo '<div align = "left">';
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
