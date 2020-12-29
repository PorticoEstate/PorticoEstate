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

	$javascripts[]	 = "/phpgwapi/js/popper/popper.min.js";
	$javascripts[]	 = "/phpgwapi/js/bootstrap/js/bootstrap.min.js";

	if( !isset($GLOBALS['phpgw_info']['flags']['noframework']) && !isset($GLOBALS['phpgw_info']['flags']['nonavbar']) )
	{
		$GLOBALS['phpgw_info']['user']['preferences']['common']['sidecontent'] = 'ajax_menu';//ajax_menu|jsmenu
		if (isset($GLOBALS['phpgw_info']['user']['preferences']['common']['sidecontent']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['sidecontent'] == 'ajax_menu')
		{
			phpgwapi_jquery::load_widget('contextMenu');
			$javascripts[] = "/phpgwapi/templates/bootstrap/js/sidenav.js";
		}

	}

	$stylesheets = array();
	$stylesheets[] = "/phpgwapi/templates/pure/css/global.css";
	$stylesheets[] = "/phpgwapi/templates/pure/css/pure-min.css";
	$stylesheets[] = "/phpgwapi/templates/pure/css/pure-extension.css";
	$stylesheets[] = "/phpgwapi/templates/pure/css/grids-responsive-min.css";

	$stylesheets[] = "/phpgwapi/js/bootstrap/css/bootstrap.min.css";

	$stylesheets[] = "/phpgwapi/templates/bookingfrontend/css/fontawesome.all.css";

//	$stylesheets[] = "/phpgwapi/templates/base/font-awesome/css/font-awesome.min.css";
	
	if($app != 'frontend')
	{
		$stylesheets[] = "/phpgwapi/templates/bootstrap/css/base.css";
	}


    if(isset($GLOBALS['phpgw_info']['user']['preferences']['common']['theme']))
	{
		$stylesheets[] = "/phpgwapi/templates/bootstrap/css/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css";
	}

	$stylesheets[] = "/{$app}/templates/bootstrap/css/base.css";
	if(isset($GLOBALS['phpgw_info']['user']['preferences']['common']['theme']))
	{
		$stylesheets[] = "/{$app}/templates/bootstrap/css/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css";
	}


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
		$test = PHPGW_SERVER_ROOT . $javascript;
		if( file_exists( PHPGW_SERVER_ROOT . $javascript ) )
		{
			$GLOBALS['phpgw']->template->set_var( 'javascript_uri', $webserver_url . $javascript . $cache_refresh_token );
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
	$concent_script = '';
	$privacy_url		= !empty($GLOBALS['phpgw_info']['server']['privacy_url']) ? $GLOBALS['phpgw_info']['server']['privacy_url'] : '';//https://www.bergen.kommune.no/omkommunen/personvern';

	if($privacy_url)
	{
		$privacy_message	= !empty($GLOBALS['phpgw_info']['server']['privacy_message']) ? $GLOBALS['phpgw_info']['server']['privacy_message'] : 'Personvern ved bruk av elektroniske skjema.';
		$lang_decline		= lang('decline');
		$lang_approve		= lang('approve');
		$lang_read_more		= lang('read more');
		$lang_privacy_policy = lang('privacy policy');

		$concent_script = <<<JS
		<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/cookieconsent@3/build/cookieconsent.min.css" />
		<script src="https://cdn.jsdelivr.net/npm/cookieconsent@3/build/cookieconsent.min.js" data-cfasync="false"></script>
		<script>

			window.addEventListener("load", function ()
			{
				window.cookieconsent.initialise({
					type: 'opt-out',
					"palette": {
						"popup": {
							"background": "#000"
						},
						"button": {
							"background": "#f1d600"
						}
					},
					"showLink": true,
					content: {
							header: 'Cookies used on the website!',
							message: '{$privacy_message}',
							dismiss: 'Got it!',
							allow: '{$lang_approve}',
							deny: '{$lang_decline}',
							link: '{$lang_read_more}',
							href: '{$privacy_url}',
							close: '&#x274c;',
							policy: '{$lang_privacy_policy}',
							target: '_blank',
					},
					position: "top",
					cookie: {
						name: 'cookieconsent_backend'
					},
					law: {
					 regionalLaw: true,
					},
					revokable:false,
					onStatusChange: function(status) {
						if(!this.hasConsented())
						{
							document.cookie = "cookieconsent_backend=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
							window.location.replace(phpGWLink('logout.php'));
						}
					 }
				})
			});

		</script>
JS;
	}

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
		'userlang'		=> $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'],
		'win_on_events'	=> $GLOBALS['phpgw']->common->get_on_events(),
		'navbar_config' => $_navbar_config,
		'lang_collapse_all'	=> lang('collapse all'),
		'lang_expand_all'	=> lang('expand all'),
		'concent_script'	=> $concent_script
	);

	$GLOBALS['phpgw']->template->set_var($tpl_vars);

	$GLOBALS['phpgw']->template->pfp('out', 'head');
	unset($tpl_vars);

	flush();


	if( isset($GLOBALS['phpgw_info']['flags']['noframework']) )
	{
//		echo '<body style="margin-left: 35px;">';
		echo '<body class="container">';
		register_shutdown_function('parse_footer_end_noframe');
	}

	function parse_footer_end_noframe()
	{
		$cache_refresh_token = '';
		if(!empty($GLOBALS['phpgw_info']['server']['cache_refresh_token']))
		{
			$cache_refresh_token = "?n={$GLOBALS['phpgw_info']['server']['cache_refresh_token']}";
		}
		$javascript_end = $GLOBALS['phpgw']->common->get_javascript_end($cache_refresh_token);

		$footer = <<<HTML
		</body>
		{$javascript_end}
	</html>
HTML;
		echo $footer;
	}
