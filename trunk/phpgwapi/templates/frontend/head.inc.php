<?php
	$GLOBALS['phpgw_info']['server']['no_jscombine']=true;
	phpgw::import_class('phpgwapi.jquery');
	phpgw::import_class('phpgwapi.template_portico');

	if ( !isset($GLOBALS['phpgw_info']['server']['site_title']) )
	{
		$GLOBALS['phpgw_info']['server']['site_title'] = lang('please set a site name in admin &gt; siteconfig');
	}

	$app = $GLOBALS['phpgw_info']['flags']['currentapp'];

	$config_frontend	= CreateObject('phpgwapi.config',$app)->read();

	$tracker_id = !empty($config_frontend['tracker_id']) ? $config_frontend['tracker_id'] : '';
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


	phpgwapi_jquery::load_widget('core');


	$old_ie = false;
	if (preg_match('/MSIE (6|7|8)/i', $_SERVER['HTTP_USER_AGENT']))
	{
		$old_ie = true;
		$message = lang('outdated browser: %1', $_SERVER['HTTP_USER_AGENT']);
		phpgwapi_cache::message_set($message, 'error');
	}


	$stylesheets = array();
	$stylesheets[] = "/phpgwapi/templates/pure/css/pure-min.css";
	$stylesheets[] = "/phpgwapi/templates/pure/css/pure-extension.css";
	if ($old_ie)
	{
		$stylesheets[] = "/phpgwapi/templates/pure/css/grids-responsive-old-ie-min.css";

	}
	else
	{
		$stylesheets[] = "/phpgwapi/templates/pure/css/grids-responsive-min.css";
	}

	$stylesheets[] = "/phpgwapi/js/DataTables/extensions/Responsive/css/responsive.dataTables.min.css";
//	$stylesheets[] = "/{$app}/templates/base/css/base.css";
//	$stylesheets[] = "/{$app}/css/frontend.css";
	$stylesheets[] = "/phpgwapi/templates/frontend/css/frontend.css";
//	$stylesheets[] = "/phpgwapi/js/jquery/mmenu/core/css/jquery.mmenu.css";
	$stylesheets[] = "/phpgwapi/js/jquery/mmenu/core/css/jquery.mmenu.all.css";

	if(isset($GLOBALS['phpgw_info']['user']['preferences']['common']['theme']))
	{
		$stylesheets[] = "/phpgwapi/templates/frontend/themes/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css";
		$stylesheets[] = "/{$app}/templates/frontend/themes/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css";
	}



	foreach ( $stylesheets as $stylesheet )
	{
		if( file_exists( PHPGW_SERVER_ROOT . $stylesheet ) )
		{
			$GLOBALS['phpgw']->template->set_var( 'stylesheet_uri', $GLOBALS['phpgw_info']['server']['webserver_url'] . $stylesheet );
			$GLOBALS['phpgw']->template->parse('stylesheets', 'stylesheet', true);
		}
	}

	$javascripts = array();
	$javascripts[] = "/phpgwapi/js/jquery/mmenu/core/js/jquery.mmenu.min.all.js";
	$javascripts[] = "/phpgwapi/templates/pure/js/mmenu.js";


	//FIXME: To consider...
	/*
	$javascripts[] = "/phpgwapi/templates/stavanger/js/minid.js";
*/
//	$javascripts[] = "/phpgwapi/templates/bookingfrontend/js/headroom.min.js";
//	$javascripts[] = "/phpgwapi/templates/bookingfrontend/js/jQuery.headroom.js";

	foreach ( $javascripts as $javascript )
	{
		if( file_exists( PHPGW_SERVER_ROOT . $javascript ) )
		{
			$GLOBALS['phpgw']->template->set_var( 'javascript_uri', $GLOBALS['phpgw_info']['server']['webserver_url'] . $javascript );
			$GLOBALS['phpgw']->template->parse('javascripts', 'javascript', true);
		}
	}

	$config	= CreateObject('phpgwapi.config',$app)->read();
	$logofile_frontend = !empty($config['logopath_frontend']) ? $config['logopath_frontend'] : '';
	$keywords = !empty($config['keywords']) ? $config['keywords'] : '';

	if($keywords)
	{
		$keywords = '<meta name="keywords" content="'.$keywords.'">';
	}
	else
	{
		$keywords = '<meta name="keywords" content="phpGroupWare">';
	}
	if(!empty($description))
	{
		$description = '<meta name="description" content="'.$description.'">';
	}
	else
	{
		$description = '<meta name="description" content="phpGroupWare">';
	}
	if (!empty($config['metatag_author']))
	{
		$author = '<meta name="author" content="'.$config['metatag_author'].'">';
	}
	else
	{
		$author = '<meta name="author" content="phpGroupWare http://www.phpgroupware.org">';
	}
	if (!empty($config['metatag_robots']))
	{
		$robots = '<meta name="robots" content="'.$config['metatag_robots'].'">';
	}
	else
	{
		$robots = '<meta name="robots" content="none">';
	}
	if (!empty($config_frontend['site_title']))
	{
		$site_title = $config_frontend['site_title'];
	}
	else
	{
		$site_title = $GLOBALS['phpgw_info']['server']['site_title'];
	}

	if(! $footer_info = $config_frontend['footer_info'])
	{
		$footer_info = "footer info settes i {$app} config";
	}

   phpgwapi_cache::session_set('phpgwapi', 'footer_info', $footer_info);

	$test = $GLOBALS['phpgw']->common->get_on_events();
    $test = str_replace('window.onload = function()','$(document).ready(function()',$test);
    $test = str_replace("\n}\n","\n})\n",$test);

	$tpl_vars = array
	(
		'css'			=> $GLOBALS['phpgw']->common->get_css(),
		'javascript'	=> $GLOBALS['phpgw']->common->get_javascript(),
		'img_icon'      => $GLOBALS['phpgw']->common->find_image('phpgwapi', 'favicon.ico'),
		'site_title'	=> $site_title,
		'str_base_url'	=> $GLOBALS['phpgw']->link('/', array(), true),
		'site_url'	=> $GLOBALS['phpgw']->link("/{$app}/", array()),
		'webserver_url'	=> $GLOBALS['phpgw_info']['server']['webserver_url'],
        'win_on_events'	=> $test,
		'metainfo_author' => $author,
		'metainfo_keywords' => $keywords,
		'metainfo_description' => $description,
		'metainfo_robots' => $robots,
		'lbl_search'   	=> lang('Search'),
		'logofile'		=> $logofile_frontend,
		'header_search_class'	=> 'hidden'//(isset($_GET['menuaction']) && $_GET['menuaction'] == 'bookingfrontend.uisearch.index' ? 'hidden' : '')
	);
	if ($manual !== null) 
	{
		$tpl_vars['manual_text'] = lang('manual');
		$tpl_vars['manual_url'] = $manual;
	}
	$user = $GLOBALS['phpgw']->accounts->get( $GLOBALS['phpgw_info']['user']['id'] );

	if($user && isset($_SESSION['phpgw_session']['session_flags']) && $_SESSION['phpgw_session']['session_flags'] == 'N')
	{
		$tpl_vars['login_text'] = $user->__toString() . ' :: ' . lang('Logout');
		$tpl_vars['login_url'] = 'logout.php';
	}
	else
	{
		$tpl_vars['login_text_org'] = '';
		$tpl_vars['login_text'] = lang('Login');
		$tpl_vars['login_url'] = 'login.php?after='. urlencode(json_encode($_GET));
		$login_parameter = !empty($config_frontend['login_parameter']) ? $config_frontend['login_parameter'] : '';
		$custom_login_url = !empty($config_frontend['custom_login_url']) ? $config_frontend['custom_login_url'] : '';
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
