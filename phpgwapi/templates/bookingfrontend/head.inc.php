<?php
	$GLOBALS['phpgw_info']['server']['no_jscombine'] = false;
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
//	$stylesheets[]	 = "/phpgwapi/templates/bookingfrontend/css/fontawesome.all.css";
	$stylesheets[]	 = "/phpgwapi/templates/base/css/fontawesome/css/all.min.css";

	$stylesheets[]	 = "/phpgwapi/templates/bookingfrontend/css/jquery.autocompleter.css";
	$stylesheets[]	 = "https://fonts.googleapis.com/css?family=Work+Sans";
	$stylesheets[]	 = "/phpgwapi/templates/bookingfrontend/css/custom.css";
	$stylesheets[]	 = "/phpgwapi/templates/bookingfrontend/css/normalize.css";
	$stylesheets[]   = "/phpgwapi/templates/bookingfrontend/css/rubik-font.css";

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
	$GLOBALS['phpgw']->template->set_var('headlogoimg', $headlogoimg);

	$loginlogo = $webserver_url . "/phpgwapi/templates/bookingfrontend/img/login-logo.svg";
	$GLOBALS['phpgw']->template->set_var('loginlogo', $loginlogo);

	$GLOBALS['phpgw']->template->set_var('logo_img', $logoimg);
	$GLOBALS['phpgw']->template->set_var('footer_logo_img', $footerlogoimg);
	$GLOBALS['phpgw']->template->set_var('logo_title', $logo_title);

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

	$municipality = $site_title;

	$GLOBALS['phpgw']->template->set_var('municipality', $municipality);

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

	if (!empty($config_frontend['url_uustatus']))
	{
		$lang_uustatus = lang('uustatus');
		$url_uustatus ="<span><a target='_blank' rel='noopener noreferrer'  href='{$config_frontend['url_uustatus']}'>{$lang_uustatus}</a></span>";
		$GLOBALS['phpgw']->template->set_var('url_uustatus', $url_uustatus);
	}

//loads jquery
	phpgwapi_jquery::load_widget('core');

	$javascripts	 = array();
	$javascripts[]	 = "/phpgwapi/js/popper/popper.min.js";
	$javascripts[]	 = "/phpgwapi/js/bootstrap/js/bootstrap.min.js";

	$javascripts[]	 = "/phpgwapi/templates/bookingfrontend/js/knockout-min.js";
	$javascripts[]	 = "/phpgwapi/templates/bookingfrontend/js/knockout.validation.js";
	$javascripts[]	 = "/phpgwapi/templates/bookingfrontend/js/jquery.autocompleter.js";
	$javascripts[]	 = "/phpgwapi/templates/bookingfrontend/js/common.js";
	$javascripts[]	 = "/phpgwapi/templates/bookingfrontend/js/custom.js";
	$javascripts[]	 = "/phpgwapi/templates/bookingfrontend/js/nb-NO.js";
	$javascripts[]	 = "/phpgwapi/js/dateformat/dateformat.js";
//	foreach ($javascripts as $javascript)
//	{
//		if (file_exists(PHPGW_SERVER_ROOT . $javascript))
//		{
//			$GLOBALS['phpgw']->template->set_var('javascript_uri', $webserver_url . $javascript . $cache_refresh_token);
//			$GLOBALS['phpgw']->template->parse('javascripts', 'javascript', true);
//		}
//	}

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
		foreach ($javascripts as $javascript)
		{
			if (file_exists(PHPGW_SERVER_ROOT . $javascript))
			{
				$GLOBALS['phpgw']->template->set_var('javascript_uri', $webserver_url . $javascript . $cache_refresh_token);
				$GLOBALS['phpgw']->template->parse('javascripts', 'javascript', true);
			}
		}
	}


	$bodoc	 = CreateObject('booking.bodocumentation');
	$manual	 = $bodoc->so->getFrontendDoc();

	$menuaction	 = phpgw::get_var('menuaction', 'GET', 'REQUEST', '');
	$id			 = phpgw::get_var('id', 'GET');
	if (strpos($menuaction, 'organization'))
	{
		$boorganization	 = CreateObject('booking.boorganization');
		$metainfo		 = $boorganization->so->get_metainfo($id);
		$description	 = preg_replace('/\s+/', ' ', strip_tags(html_entity_decode($metainfo['description_json']['no'])));
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
		$description = preg_replace('/\s+/', ' ', strip_tags(html_entity_decode($metainfo['description_json']['no'])));
		$keywords	 = $metainfo['name'] . "," . $metainfo['district'] . "," . $metainfo['city'];
	}
	elseif (strpos($menuaction, 'resource'))
	{
		$boresource	 = CreateObject('booking.boresource');
		$metainfo	 = $boresource->so->get_metainfo($id);
		$description = preg_replace('/\s+/', ' ', strip_tags(html_entity_decode($metainfo['description_json']['no'])));
		$keywords	 = $metainfo['name'] . "," . $metainfo['building'] . "," . $metainfo['district'] . "," . $metainfo['city'];
	}
	if ($keywords != '')
	{
		$keywords = '<meta name="keywords" content="' . htmlspecialchars($keywords) . '">';
	}
	else
	{
		$keywords = '<meta name="keywords" content="PorticoEstate">';
	}
	if (!empty($description))
	{
		$description = '<meta name="description" content="' . htmlspecialchars($description) . '">';
	}
	else
	{
		$description = '<meta name="description" content="PorticoEstate">';
	}
	if (!empty($config_frontend['metatag_author']))
	{
		$author = '<meta name="author" content="' . $config_frontend['metatag_author'] . '">';
	}
	else
	{
		$author = '<meta name="author" content="PorticoEstate https://github.com/PorticoEstate/PorticoEstate">';
	}
	if (!empty($config_frontend['metatag_robots']))
	{
		$robots = '<meta name="robots" content="' . $config_frontend['metatag_robots'] . '">';
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

	$site_base = $app == 'bookingfrontend' ? "/{$app}/" : '/index.php';

	$site_url			= $GLOBALS['phpgw']->link($site_base, array());
	$eventsearch_url = $GLOBALS['phpgw']->link('/bookingfrontend/',array('menuaction'=>'bookingfrontend.uieventsearch.show'));
	$placeholder_search = lang('Search');
	$myorgs_text = lang('Show my events');

	$userlang = $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'];
	$flag_no = "{$webserver_url}/phpgwapi/templates/base/images/flag_no.gif";
	$flag_en = "{$webserver_url}/phpgwapi/templates/base/images/flag_en.gif";

	$self_uri = $_SERVER['REQUEST_URI'];
	$separator = strpos($self_uri, '?') ? '&' : '?';
	$self_uri = str_replace(array("{$separator}lang=no", "{$separator}lang=en"), '', $self_uri);

	switch($GLOBALS['phpgw_info']['user']['preferences']['common']['template_set'])
	{
		case 'bookingfrontend_2':
			$selected_bookingfrontend_2 = ' selected = "selected"';
			$selected_bookingfrontend = '';
			break;
		case 'bookingfrontend':
			$selected_bookingfrontend_2 = '';
			$selected_bookingfrontend = ' selected = "selected"';
			break;
	}

	if($config_frontend['develope_mode'])
	{
		$template_selector = <<<HTML
		<li class="nav-item">
		   <select id = "template_selector" class="btn btn-link btn-sm nav-link dropdown-toggle" style="padding-top: .315rem;-webkit-appearance: none;-moz-appearance: none;">
			<option class="nav-link" value="bookingfrontend"{$selected_bookingfrontend}>Original</option>
			<option class="nav-link" value="bookingfrontend_2"{$selected_bookingfrontend_2}>Ny</option>
		   </select>
		</li>
HTML;
	}
	else
	{
		$template_selector = '';
	}

	$nav = <<<HTML

		<nav class="navbar navbar-default sticky-top navbar-expand-md navbar-light  header_borderline"   id="headcon">
			<div class="container-fluid header-container my_class">
			<div>
				<a class="navbar-brand brand-site-title" href="{$site_url}">{$site_title} </a>
				<a href="{$site_url}"><img class="navbar-brand brand-site-img" src="{$headlogoimg}" alt="{$logo_title}"/></a>
				</div>
		    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
		 <div class="collapse navbar-collapse" id="navbarSupportedContent">
				<!-- Search Box -->
				<!--div class="search-container">
					<form id="navSearchForm" class="search-form">
						<input type="text" class="search-input" placeholder="{$placeholder_search}"    id="searchInput"  />
						<button class="searchButton" type="submit" ><i class="fas fa-search"></i></button>
					</form>
				</div-->

		<ul class="navbar-nav flex-row ml-auto d-flex">
				<li class="nav-item">
					<a class="nav-link p-2" href="{$self_uri}{$separator}lang={$userlang}" aria-label="Norsk"><img src="{$flag_no}" alt="Norsk (Norway)" title="Norsk (Norway)" />
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link p-2" href="{$self_uri}{$separator}lang=en" aria-label="English"><img src="{$flag_en}" alt="English (United Kingdom)" title="English (United Kingdom)" />
					</a>
				</li>
				{$template_selector}
		</ul>		
		<div class="event_navbar_container">
			<div class="arrangement-link-box">
				<a class="Arrangement_link" href="{$eventsearch_url}">Arrangement</a>
			</div>
			<button onclick="toggleMyOrgs()" class="my_orgs_button" id="my_orgs_button" style="display:none;">
				<i id="my_orgs_icon" class="far fa-circle"></i>
				{$myorgs_text}
			</button>
		</div>
        <div class="navbar-organization-select">
        </div>
        </div>
        </div>
		</nav>
		<div class="overlay">
            <div id="loading-img"><i class="fas fa-spinner fa-spin fa-3x"></i></div>
        </div>
HTML;

	if(!empty($config_frontend['tracker_matomo_url']))
	{
		$tracker_matomo_url = rtrim($config_frontend['tracker_matomo_url'], '/') . '/';
		$tracker_matomo_id = (int)$config_frontend['tracker_matomo_id'];
		$tracker_matomo_code = <<<JS

	   <!-- Start Matomo Code -->
			<script>
			  var _paq = window._paq = window._paq || [];
			  /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
			  _paq.push(['trackPageView']);
			  _paq.push(['enableLinkTracking']);
			  (function() {
				var u="//{$tracker_matomo_url}";
				_paq.push(['setTrackerUrl', u+'osaka.php']);
				_paq.push(['setSiteId', '{$tracker_matomo_id}']);
				var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
				g.async=true; g.src=u+'osaka.js'; s.parentNode.insertBefore(g,s);
			  })();
			</script>
		<!-- End Matomo Code -->

JS;

		/**
		 * Alternative to avoid adblockers and no-script
		 */

//		$urlref = $_SERVER['HTTP_REFERER'];
//
//		$tracker_image = <<<HTML
//			<!-- Matomo Image Tracker -->
//			<img src="https://{$tracker_matomo_url}matomo.php?idsite={$tracker_matomo_id}&rec=1&urlref={$urlref}&send_image=0" style="border:0" alt="" />
//			<!-- End Matomo Image Tracker-->
//HTML;

	}


	$tpl_vars = array
	(
		'site_title'			 => $site_title,
		'css'					 => $GLOBALS['phpgw']->common->get_css($cache_refresh_token),
		'javascript'			 => $GLOBALS['phpgw']->common->get_javascript($cache_refresh_token),
		'img_icon'				 => $GLOBALS['phpgw']->common->find_image('phpgwapi', 'favicon.ico'),
		'str_base_url'			 => $GLOBALS['phpgw']->link('/', array(), true),
		'dateformat_backend'	 => $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],
		'site_url'				 => $GLOBALS['phpgw']->link($site_base, array()),
		'eventsearch_url'        => $GLOBALS['phpgw']->link('/bookingfrontend/',array('menuaction'=>'bookingfrontend.uieventsearch.show')),
		'webserver_url'			 => $webserver_url,
		'metainfo_author'		 => $author,
		'userlang'				 => $userlang,
		'metainfo_keywords'		 => $keywords,
		'metainfo_description'	 => $description,
		'metainfo_robots'		 => $robots,
		'lbl_search'			 => lang('Search'),
		'logofile'				 => $logofile_frontend,
		'header_search_class'	 => 'hidden',//(isset($_GET['menuaction']) && $_GET['menuaction'] == 'bookingfrontend.uisearch.index' ? 'hidden' : '')
		'nav'					 => empty($GLOBALS['phpgw_info']['flags']['noframework']) ? $nav : '',
		'tracker_code'			 => $tracker_matomo_code,
//		'tracker_image'			 => $tracker_image
	);


//	$user = $GLOBALS['phpgw']->accounts->get( $GLOBALS['phpgw_info']['user']['id'] );
//	_debug_array($user);

	$bouser	 = CreateObject('bookingfrontend.bouser', true);

	/**
	 * Might be set wrong in the ui-class
	 */
	$xslt_app = !empty($GLOBALS['phpgw_info']['flags']['xslt_app']) ? true : false;
	$org	 = CreateObject('booking.soorganization');
	$GLOBALS['phpgw_info']['flags']['xslt_app'] = $xslt_app;

	$user_url = $GLOBALS['phpgw']->link("/{$app}/", array('menuaction' => 'bookingfrontend.uiuser.show'));
	$lang_user = lang('My page');
	$tpl_vars['user_info_view'] = "<span><i class='fas fa-user ml-1 mr-1'></i><a href='{$user_url}'>{$lang_user}</a></span>";

	$user_data = phpgwapi_cache::session_get($bouser->get_module(), $bouser::USERARRAY_SESSION_KEY);
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
	else if(!empty($user_data['ssn']))
	{
			$tpl_vars['login_text_org']	 = '';
			$tpl_vars['login_text']		 = "{$user_data['first_name']} {$user_data['last_name']} :: " . lang('Logout');
			$tpl_vars['org_url']		 = '#';
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
