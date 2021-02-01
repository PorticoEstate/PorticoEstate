<?php
	$GLOBALS['phpgw_info']['server']['no_jscombine'] = true;
	phpgw::import_class('phpgwapi.jquery');
	phpgw::import_class('phpgwapi.template_portico');

	if (!isset($GLOBALS['phpgw_info']['server']['site_title']))
	{
		$GLOBALS['phpgw_info']['server']['site_title'] = lang('please set a site name in admin &gt; siteconfig');
	}

	$webserver_url = $GLOBALS['phpgw_info']['server']['webserver_url'];

	$app = $GLOBALS['phpgw_info']['flags']['currentapp'];

	$cache_refresh_token = '';
	if (!empty($GLOBALS['phpgw_info']['server']['cache_refresh_token']))
	{
		$cache_refresh_token = "?n={$GLOBALS['phpgw_info']['server']['cache_refresh_token']}";
	}

	$config_frontend = CreateObject('phpgwapi.config', 'bookingfrontend')->read();
	$config_backend = CreateObject('phpgwapi.config', 'booking')->read();

	$tracker_id		 = !empty($config_frontend['tracker_id']) ? $config_frontend['tracker_id'] : '';
	$tracker_code1	 = <<<JS
		var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
		document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
JS;
	$tracker_code2	 = <<<JS
		try
		{
			var pageTracker = _gat._getTracker("{$tracker_id}");
			pageTracker._trackPageview();
		}
		catch(err)
		{
//			alert(err);
		}
JS;

	if ($tracker_id)
	{
		$GLOBALS['phpgw']->js->add_code('', $tracker_code1);
		$GLOBALS['phpgw']->js->add_code('', $tracker_code2);
	}

	$GLOBALS['phpgw']->template->set_root(PHPGW_TEMPLATE_DIR);
	$GLOBALS['phpgw']->template->set_unknowns('remove');
	$GLOBALS['phpgw']->template->set_file('head', 'head.tpl');
	$GLOBALS['phpgw']->template->set_block('head', 'stylesheet', 'stylesheets');
	$GLOBALS['phpgw']->template->set_block('head', 'javascript', 'javascripts');


	$stylesheets = array();


	$stylesheets[]	 = "/phpgwapi/js/bootstrap/css/bootstrap.min.css";
	$stylesheets[]	 = "/phpgwapi/templates/bookingfrontend/css/fontawesome.all.css";
	$stylesheets[]	 = "/phpgwapi/templates/bookingfrontend/css/jquery.autocompleter.css";
	$stylesheets[]	 = "https://fonts.googleapis.com/css?family=Work+Sans";
	$stylesheets[]	 = "/phpgwapi/templates/bookingfrontend/css/custom.css";
	$stylesheets[]	 = "/phpgwapi/templates/bookingfrontend/css/normalize.css";

	if (isset($GLOBALS['phpgw_info']['user']['preferences']['common']['theme']))
	{
		$stylesheets[]	 = "/phpgwapi/templates/bookingfrontend/themes/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css";
		$stylesheets[]	 = "/{$app}/templates/bookingfrontend/themes/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css";
	}

	foreach ($stylesheets as $stylesheet)
	{
		if (file_exists(PHPGW_SERVER_ROOT . $stylesheet))
		{
			$GLOBALS['phpgw']->template->set_var('stylesheet_uri', $webserver_url . $stylesheet . $cache_refresh_token);
			$GLOBALS['phpgw']->template->parse('stylesheets', 'stylesheet', true);
		}
	}

	if (!empty($GLOBALS['phpgw_info']['server']['logo_url']))
	{
		$footerlogoimg = $GLOBALS['phpgw_info']['server']['logo_url'];
		$GLOBALS['phpgw']->template->set_var('footer_logo_img', $footerlogoimg);
	}
	else
	{

		$footerlogoimg = $webserver_url . "/phpgwapi/templates/bookingfrontend/img/Aktiv-kommune-footer-logo.png";
		$GLOBALS['phpgw']->template->set_var('logoimg', $footerlogoimg);
	}

	if (!empty($GLOBALS['phpgw_info']['server']['bakcground_image']))
	{
		$footer_logo_url = $GLOBALS['phpgw_info']['server']['bakcground_image'];
		$GLOBALS['phpgw']->template->set_var('footer_logo_url', $footer_logo_url);
	}


	if (!empty($GLOBALS['phpgw_info']['server']['logo_title']))
	{
		$logo_title = $GLOBALS['phpgw_info']['server']['logo_title'];
	}
	else
	{
		$logo_title = 'Logo';
	}


	if (!empty($GLOBALS['phpgw_info']['server']['site_title']))
	{

		$site_title = $GLOBALS['phpgw_info']['server']['site_title'];
	}

	$headlogoimg = $webserver_url . "/phpgwapi/templates/bookingfrontend/img/Aktiv-kommune-logo.png";
//	$GLOBALS['phpgw']->template->set_var('headlogoimg', $headlogoimg);

	$loginlogo = $webserver_url . "/phpgwapi/templates/bookingfrontend/img/login-logo.svg";
	$GLOBALS['phpgw']->template->set_var('loginlogo', $loginlogo);

	$GLOBALS['phpgw']->template->set_var('logo_img', $logoimg);
	$GLOBALS['phpgw']->template->set_var('footer_logo_img', $footerlogoimg);
//	$GLOBALS['phpgw']->template->set_var('logo_title', $logo_title);

	$langmanual = lang('Manual');
	$GLOBALS['phpgw']->template->set_var('manual', $langmanual);

	$privacy = lang('Privacy');
	$GLOBALS['phpgw']->template->set_var('privacy', $privacy);


	$textaboutmunicipality = lang('About Active kommune');
	$GLOBALS['phpgw']->template->set_var('textaboutmunicipality', $textaboutmunicipality);

	$SIGNINN = lang('sign in');
	$GLOBALS['phpgw']->template->set_var('SIGNINN', $SIGNINN);

	$executiveofficer = lang('executiveofficer');
	$GLOBALS['phpgw']->template->set_var('executiveofficer', $executiveofficer);

	$executiveofficer_url = $webserver_url . "/";
	$GLOBALS['phpgw']->template->set_var('executiveofficer_url', $executiveofficer_url);

	$stringmunicipality = '  kommune';

//	$municipality =     $site_title   .   $stringmunicipality;

	$municipality = $site_title;

	$GLOBALS['phpgw']->template->set_var('municipality', $municipality);

//	$municipality_email = 'servicetorget@alesund.kommune.no';
//	$GLOBALS['phpgw']->template->set_var( 'municipality_email', $municipality_email );


	if (!empty($config_backend['support_address']))
	{
		$support_email = $config_backend['support_address'];
	}
	else
	{
		if (!empty($GLOBALS['phpgw_info']['server']['support_address']))
		{
			$support_email = $GLOBALS['phpgw_info']['server']['support_address'];
		}
		else
		{
			$support_email = 'support@aktivkommune.no';
		}
	}
	$GLOBALS['phpgw']->template->set_var('support_email', $support_email);

//loads jquery
	phpgwapi_jquery::load_widget('core');

	$javascripts	 = array();
	$javascripts[]	 = "/phpgwapi/js/popper/popper.min.js";
//  Alloy-ui disagrees with Bootstrap version 4.5.2 and hides buttons in some cases (event.info)
	$javascripts[]	 = "/phpgwapi/js/bootstrap/js/bootstrap.min.js";
	$javascripts[]	 = "/phpgwapi/templates/bookingfrontend/js/knockout-min.js";
	$javascripts[]	 = "/phpgwapi/templates/bookingfrontend/js/knockout.validation.js";
	$javascripts[]	 = "/phpgwapi/templates/bookingfrontend/js/jquery.autocompleter.js";
	$javascripts[]	 = "/phpgwapi/templates/bookingfrontend/js/common.js";
	$javascripts[]	 = "/phpgwapi/templates/bookingfrontend/js/custom.js";
	$javascripts[]	 = "/phpgwapi/templates/bookingfrontend/js/nb-NO.js";
	$javascripts[]	 = "/phpgwapi/js/dateformat/dateformat.js";


	foreach ($javascripts as $javascript)
	{
		if (file_exists(PHPGW_SERVER_ROOT . $javascript))
		{
			$GLOBALS['phpgw']->template->set_var('javascript_uri', $webserver_url . $javascript . $cache_refresh_token);
			$GLOBALS['phpgw']->template->parse('javascripts', 'javascript', true);
		}
	}

	$config = CreateObject('phpgwapi.config', 'booking')->read();

	$bodoc	 = CreateObject('booking.bodocumentation');
	$manual	 = $bodoc->so->getFrontendDoc();

	$menuaction	 = phpgw::get_var('menuaction', 'GET');
	$id			 = phpgw::get_var('id', 'GET');
	if (strpos($menuaction, 'organization'))
	{
		$boorganization	 = CreateObject('booking.boorganization');
		$metainfo		 = $boorganization->so->get_metainfo($id);
		$description	 = preg_replace('/\s+/', ' ', strip_tags($metainfo['description']));
		$keywords		 = $metainfo['name'] . "," . $metainfo['shortname'] . "," . $metainfo['district'] . "," . $metainfo['city'];
	}
	elseif (strpos($menuaction, 'group'))
	{
		$bogroup	 = CreateObject('booking.bogroup');
		$metainfo	 = $bogroup->so->get_metainfo($id);
		$description = preg_replace('/\s+/', ' ', strip_tags($metainfo['description']));
		$keywords	 = $metainfo['name'] . "," . $metainfo['shortname'] . "," . $metainfo['organization'] . "," . $metainfo['district'] . "," . $metainfo['city'];
	}
	elseif (strpos($menuaction, 'building'))
	{
		$bobuilding	 = CreateObject('booking.bobuilding');
		$metainfo	 = $bobuilding->so->get_metainfo($id);
		$description = preg_replace('/\s+/', ' ', strip_tags($metainfo['description']));
		$keywords	 = $metainfo['name'] . "," . $metainfo['district'] . "," . $metainfo['city'];
	}
	elseif (strpos($menuaction, 'resource'))
	{
		$boresource	 = CreateObject('booking.boresource');
		$metainfo	 = $boresource->so->get_metainfo($id);
		$description = preg_replace('/\s+/', ' ', strip_tags($metainfo['description']));
		$keywords	 = $metainfo['name'] . "," . $metainfo['building'] . "," . $metainfo['district'] . "," . $metainfo['city'];
	}
	if ($keywords != '')
	{
		$keywords = '<meta name="keywords" content="' . $keywords . '">';
	}
	else
	{
		$keywords = '<meta name="keywords" content="phpGroupWare">';
	}
	if (!empty($description))
	{
		$description = '<meta name="description" content="' . htmlspecialchars($description) . '">';
	}
	else
	{
		$description = '<meta name="description" content="phpGroupWare">';
	}
	if (!empty($config['metatag_author']))
	{
		$author = '<meta name="author" content="' . $config['metatag_author'] . '">';
	}
	else
	{
		$author = '<meta name="author" content="phpGroupWare http://www.phpgroupware.org">';
	}
	if (!empty($config['metatag_robots']))
	{
		$robots = '<meta name="robots" content="' . $config['metatag_robots'] . '">';
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

	if (!$footer_info = $config_frontend['footer_info'])
	{
		$footer_info = 'footer info settes i bookingfrontend config';
	}

	phpgwapi_cache::session_set('phpgwapi', 'footer_info', $footer_info);

//$test = $GLOBALS['phpgw']->common->get_on_events();
	$test	 = str_replace('window.onload = function()', '$(document).ready(function()', $test);
	$test	 = str_replace("\n}\n", "\n})\n", $test);

	$site_base = $app == 'bookingfrontend' ? "/{$app}/" : '/index.php';

	$site_url			= $GLOBALS['phpgw']->link($site_base, array());
	$placeholder_search = lang('Search');

	$nav = <<<HTML
   
		<nav class="navbar navbar-default sticky-top navbar-expand-md navbar-light  header_borderline"   id="headcon">
			<div class="container header-container my_class">
				<a class="navbar-brand brand-site-title" href="{$site_url}">{$site_title} </a>
				<a href="{$site_url}"><img class="navbar-brand brand-site-img" src="{$headlogoimg}" alt="{$logo_title}"/></a>
				<!-- Search Box -->
				<!--div class="search-container">
					<form id="navSearchForm" class="search-form">
						<input type="text" class="search-input" placeholder="{$placeholder_search}"    id="searchInput"  />
						<button class="searchButton" type="submit" ><i class="fas fa-search"></i></button>
					</form>
				</div-->
			</div>
            <div class="navbar-organization-select">
            </div>
		</nav>
		<div class="overlay">
            <div id="loading-img"><i class="fas fa-spinner fa-spin fa-3x"></i></div>
        </div>	
HTML;


	$tpl_vars = array
		(
		'css'					 => $GLOBALS['phpgw']->common->get_css($cache_refresh_token),
		'javascript'			 => $GLOBALS['phpgw']->common->get_javascript($cache_refresh_token),
		'img_icon'				 => $GLOBALS['phpgw']->common->find_image('phpgwapi', 'favicon.ico'),
		'str_base_url'			 => $GLOBALS['phpgw']->link('/', array(), true),
		'dateformat_backend'	 => $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],
		'webserver_url'			 => $webserver_url,
		'win_on_events'			 => $test,
		'metainfo_author'		 => $author,
		'userlang'				 => $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'],
		'metainfo_keywords'		 => $keywords,
		'metainfo_description'	 => $description,
		'metainfo_robots'		 => $robots,
		'lbl_search'			 => lang('Search'),
		'logofile'				 => $logofile_frontend,
		'header_search_class'	 => 'hidden',//(isset($_GET['menuaction']) && $_GET['menuaction'] == 'bookingfrontend.uisearch.index' ? 'hidden' : '')
		'nav'					 => empty($GLOBALS['phpgw_info']['flags']['noframework']) ? $nav : ''
	);


//	$user = $GLOBALS['phpgw']->accounts->get( $GLOBALS['phpgw_info']['user']['id'] );
//	_debug_array($user);

	$bouser	 = CreateObject('bookingfrontend.bouser', true);

	/**
	 * Might be set wrong in the ui-class
	 */
	$xslt_app = !empty($GLOBALS['phpgw_info']['flags']['xslt_app']) ? true : false;
	$org	 = CreateObject('bookingfrontend.uiorganization');
	$GLOBALS['phpgw_info']['flags']['xslt_app'] = $xslt_app;

	$user_url = $GLOBALS['phpgw']->link("/{$app}/", array('menuaction' => 'bookingfrontend.uiuser.show'));
	$lang_user = lang('My page');
	$tpl_vars['user_info_view'] = "<span><i class='fas fa-user ml-1 mr-1'></i><a href='{$user_url}'>{$lang_user}</a></span>";

	if ($bouser->is_logged_in())
	{

		if ($bouser->orgname == '000000000')
		{
			$tpl_vars['login_text_org']	 = lang('SSN not registred');
			$tpl_vars['login_text']		 = lang('Logout');
			$tpl_vars['org_url']		 = '#';
		}
		else
		{
			$org_url = $GLOBALS['phpgw']->link("/{$app}/", array('menuaction' => 'bookingfrontend.uiorganization.show',
				'id' => $org->get_orgid($bouser->orgnr, $bouser->ssn)));

			$lang_organization = lang('Organization');
			$tpl_vars['org_info_view'] = "<span><img class='login-logo' src='{$loginlogo}' alt='{$lang_organization}'></img><a href='{$org_url}'>{$bouser->orgname}</a></span>";
			$tpl_vars['login_text_org']	 = $bouser->orgname;
			$tpl_vars['login_text']		 = lang('Logout');
		}
		$tpl_vars['login_text']	 = $bouser->orgnr . ' :: ' . lang('Logout');
		$tpl_vars['login_url']	 = 'logout.php';
	}
	else
	{
		$tpl_vars['login_text_org']	 = '';
		$tpl_vars['org_url']		 = '#';
		$tpl_vars['login_text']		 = lang('Organization');
		$tpl_vars['login_url']		 = 'login.php?after=' . urlencode($_SERVER['QUERY_STRING']);
		$login_parameter			 = !empty($config_frontend['login_parameter']) ? $config_frontend['login_parameter'] : '';
		$custom_login_url			 = !empty($config_frontend['custom_login_url']) ? $config_frontend['custom_login_url'] : '';
		if ($login_parameter)
		{
			$login_parameter		 = ltrim($login_parameter, '&');
			$tpl_vars['login_url']	 .= "&{$login_parameter}";
//			$LOGIN  =   $tpl_vars['login_url'];
		}
		if ($custom_login_url)
		{
			$tpl_vars['login_url'] = $custom_login_url;
//			$LOGIN  =   $tpl_vars['login_url'];
		}
	}	

	$GLOBALS['phpgw']->template->set_var($tpl_vars);

	$GLOBALS['phpgw']->template->pfp('out', 'head');


//	$LOGIN  =   $custom_login_url;
//
//	$GLOBALS['phpgw']->template->set_var( 'LOGIN', $LOGIN );
//	$uri = $_SERVER['REQUEST_URI']; // $uri == example.com/sub
//	$exploded_uri = explode('/', $uri); //$exploded_uri == array('example.com','sub')
//	$domain_name = $exploded_uri[0];
//
//	$LOGIN  =   $domain_name;
//	$LOGIN  = $_SERVER['SERVER_NAME'];

	$hostname	 = $_SERVER['SERVER_NAME'];
	$port		 = $_SERVER['SERVER_PORT'];

	$LOGIN = $hostname . ':' . $port;

	$GLOBALS['phpgw']->template->set_var('LOGIN', $LOGIN);

	unset($tpl_vars);