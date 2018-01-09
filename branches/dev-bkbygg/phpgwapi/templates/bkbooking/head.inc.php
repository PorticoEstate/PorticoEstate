<?php
	$GLOBALS['phpgw_info']['server']['no_jscombine']=true;
	phpgw::import_class('phpgwapi.jquery');
	phpgw::import_class('phpgwapi.template_portico');

	if ( !isset($GLOBALS['phpgw_info']['server']['site_title']) )
	{
		$GLOBALS['phpgw_info']['server']['site_title'] = lang('please set a site name in admin &gt; siteconfig');
	}

	$webserver_url = $GLOBALS['phpgw_info']['server']['webserver_url'];

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

	phpgwapi_jquery::load_widget('core');

	$stylesheets = array();
	$stylesheets[] = "/phpgwapi/templates/pure/css/global.css";
	$stylesheets[] = "/phpgwapi/templates/pure/css/pure-min.css";
	$stylesheets[] = "/phpgwapi/templates/pure/css/pure-extension.css";
	$stylesheets[] = "/phpgwapi/templates/pure/css/grids-responsive-min.css";
	$stylesheets[] = "/phpgwapi/js/DataTables/DataTables/css/jquery.dataTables.min.css";
	$stylesheets[] = "/phpgwapi/js/DataTables/DataTables/css/dataTables.jqueryui.min.css";
	$stylesheets[] = "/phpgwapi/js/DataTables/Responsive/css/responsive.dataTables.min.css";

	$stylesheets[] = "/phpgwapi/templates/portico/css/base.css";
	$stylesheets[] = "/phpgwapi/templates/portico/css/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css";
	$stylesheets[] = "/{$app}/templates/base/css/base.css";
	$stylesheets[] = "/{$app}/templates/portico/css/base.css";
	$stylesheets[] = "/{$app}/templates/portico/css/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css";
	$stylesheets[] = "/phpgwapi/templates/bkbooking/css/frontend.css";
	$stylesheets[] = "/bookingfrontend/css/bookingfrontend.css";

	foreach ( $stylesheets as $stylesheet )
	{
		if( file_exists( PHPGW_SERVER_ROOT . $stylesheet ) )
		{
			$GLOBALS['phpgw']->template->set_var( 'stylesheet_uri', $webserver_url . $stylesheet );
			$GLOBALS['phpgw']->template->parse('stylesheets', 'stylesheet', true);
		}
	}

	//FIXME: To consider...
	/*
	$javascripts[] = "/phpgwapi/templates/stavanger/js/minid.js";
*/
	foreach ( $javascripts as $javascript )
	{
		if( file_exists( PHPGW_SERVER_ROOT . $javascript ) )
		{
			$GLOBALS['phpgw']->template->set_var( 'javascript_uri', $webserver_url . $javascript );
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
	$logofile_frontend = isset($config->config_data['logopath_frontend']) && $config->config_data['logopath_frontend'] ? $config->config_data['logopath_frontend'] : "/phpgwapi/templates/bkbooking/images/bergen_logo.png";

	$bodoc = CreateObject('booking.bodocumentation');
	$manual  =  $bodoc->so->getFrontendDoc();	

	$menuaction = phpgw::get_var('menuaction', 'GET');
	$id = phpgw::get_var('id', 'GET');
	if (strpos($menuaction, 'organization'))
	{
		$boorganization = CreateObject('booking.boorganization');
		$metainfo = $boorganization->so->get_metainfo($id);
		$description = preg_replace('/\s+/', ' ', strip_tags($metainfo['description']));
		$keywords = $metainfo['name'].",".$metainfo['shortname'].",".$metainfo['district'].",".$metainfo['city']; 
	} 
	elseif (strpos($menuaction, 'group'))
	{
		$bogroup = CreateObject('booking.bogroup');
		$metainfo = $bogroup->so->get_metainfo($id);
		$description = preg_replace('/\s+/', ' ', strip_tags($metainfo['description']));
		$keywords = $metainfo['name'].",".$metainfo['shortname'].",".$metainfo['organization'].",".$metainfo['district'].",".$metainfo['city']; 
	}	
	elseif (strpos($menuaction, 'building'))
	{
		$bobuilding = CreateObject('booking.bobuilding');
		$metainfo = $bobuilding->so->get_metainfo($id);
		$description = preg_replace('/\s+/', ' ', strip_tags($metainfo['description']));
		$keywords = $metainfo['name'].",".$metainfo['district'].",".$metainfo['city']; 
	}
	elseif (strpos($menuaction, 'resource'))
	{
		$boresource = CreateObject('booking.boresource');
		$metainfo = $boresource->so->get_metainfo($id);
		$description = preg_replace('/\s+/', ' ', strip_tags($metainfo['description']));
		$keywords = $metainfo['name'].",".$metainfo['building'].",".$metainfo['district'].",".$metainfo['city']; 
	}
	if($keywords != '')
	{
		$keywords = '<meta name="keywords" content="'.$keywords.'">';
	}
	else
	{
		$keywords = '<meta name="keywords" content="phpGroupWare">';
	}
	if($description != '')
	{
		$description = '<meta name="description" content="'.$description.'">';
	}
	else
	{
		$description = '<meta name="description" content="phpGroupWare">';
	}
	if ($config->config_data['metatag_author'] != '')
	{
		$author = '<meta name="author" content="'.$config->config_data['metatag_author'].'">';
	}
	else
	{
		$author = '<meta name="author" content="phpGroupWare http://www.phpgroupware.org">';
	}
	if ($config->config_data['metatag_robots'] != '')
	{
		$robots = '<meta name="robots" content="'.$config->config_data['metatag_robots'].'">';
	}
	else
	{
		$robots = '<meta name="robots" content="none">';
	}

	$test = $GLOBALS['phpgw']->common->get_on_events();
	$test = str_replace('window.onload = function()','$(document).ready(function()',$test);
	$test = str_replace("\n}\n","\n})\n",$test);
	$app = lang($app);
	$tpl_vars = array
	(
		'css'			=> $GLOBALS['phpgw']->common->get_css(),
		'javascript'	=> $GLOBALS['phpgw']->common->get_javascript(),
		'img_icon'		=> $GLOBALS['phpgw']->common->find_image('phpgwapi', 'favicon.ico'),
		'site_url'		=> $GLOBALS['phpgw']->link('/bookingfrontend/', array('menuaction'=>'bookingfrontend.uisearch.index')),
		'site_title'	=> "{$GLOBALS['phpgw_info']['server']['site_title']}",
		'str_base_url'	=> $GLOBALS['phpgw']->link('/', array(), true),
		'webserver_url'	=> $webserver_url,
//		'win_on_events'	=> $GLOBALS['phpgw']->common->get_on_events(),
		'win_on_events'	=> $test,
		'navbar_config' => $_navbar_config,
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
#		$tpl_vars['help_text'] = lang('help');
#		$tpl_vars['help_url'] = => '#';
	}
	$bouser = CreateObject('bookingfrontend.bouser');
	$org = CreateObject('bookingfrontend.uiorganization');
	$orgid = $org->get_orgid($bouser->orgnr);
	if($bouser->is_logged_in())
	{
		$tpl_vars['organization_json'] = json_encode(phpgwapi_cache::session_get($bouser->get_module(), $bouser::ORGARRAY_SESSION_KEY));

		$tpl_vars['change_org_header'] = lang('Change organization');

		if ( $bouser->orgname == '000000000')
		{
			$tpl_vars['login_text_org'] = lang('SSN not registred');
			$tpl_vars['login_text'] = lang('Logout');
			$tpl_vars['org_url'] = '#';
		}
		else
		{
			$tpl_vars['login_text_org'] = $bouser->orgname;
			$tpl_vars['login_text'] = lang('Logout');
			$tpl_vars['org_url'] = $GLOBALS['phpgw']->link('/bookingfrontend/', array('menuaction'=>'bookingfrontend.uiorganization.show', 'id'=> $orgid));
	//		$tpl_vars['org_url'] = "/bookingfrontend/?menuaction=bookingfrontend.uiorganization.show&id=".$orgid;
		}
		$tpl_vars['login_text'] = $bouser->orgnr . ' :: ' . lang('Logout');
		$tpl_vars['login_url'] = $GLOBALS['phpgw']->link('/bookingfrontend/logout.php', array());
	}
	else
	{
		$tpl_vars['login_text_org'] = '';
		$tpl_vars['org_url'] = '#';
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
