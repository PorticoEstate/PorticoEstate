<?php
$GLOBALS['phpgw_info']['server']['no_jscombine'] = true;
phpgw::import_class('phpgwapi.jquery');
phpgw::import_class('phpgwapi.template_portico');

if (!isset($GLOBALS['phpgw_info']['server']['site_title'])) {
    $GLOBALS['phpgw_info']['server']['site_title'] = lang('please set a site name in admin &gt; siteconfig');
}

$webserver_url = $GLOBALS['phpgw_info']['server']['webserver_url'];

$app = $GLOBALS['phpgw_info']['flags']['currentapp'];

$cache_refresh_token = '';
if (!empty($GLOBALS['phpgw_info']['server']['cache_refresh_token'])) {
    $cache_refresh_token = "?n={$GLOBALS['phpgw_info']['server']['cache_refresh_token']}";
}

$config_frontend = CreateObject('phpgwapi.config', 'bookingfrontend')->read();
$config_backend = CreateObject('phpgwapi.config', 'booking')->read();

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
//			alert(err);
		}
JS;

if ($tracker_id) {
    $GLOBALS['phpgw']->js->add_code('', $tracker_code1);
    $GLOBALS['phpgw']->js->add_code('', $tracker_code2);
}

$GLOBALS['phpgw']->template->set_root(PHPGW_TEMPLATE_DIR);
$GLOBALS['phpgw']->template->set_unknowns('remove');
$GLOBALS['phpgw']->template->set_file('head', 'head.tpl');
$GLOBALS['phpgw']->template->set_block('head', 'stylesheet', 'stylesheets');
$GLOBALS['phpgw']->template->set_block('head', 'javascript', 'javascripts');

$stylesheets = array();
$stylesheets[] = "/phpgwapi/js/bootstrap5/vendor/twbs/bootstrap/dist/css/bootstrap.min.css";
$stylesheets[] = "/phpgwapi/templates/base/css/fontawesome/css/all.min.css";
$stylesheets[] = "/phpgwapi/templates/bookingfrontend/css/jquery.autocompleter.css";
$stylesheets[] = "/phpgwapi/templates/bookingfrontend/css/normalize.css";
$stylesheets[] = "/phpgwapi/templates/bookingfrontend/css/rubik-font.css";
$stylesheets[] = "/phpgwapi/js/select2/css/select2.min.css";
$stylesheets[] = "/phpgwapi/js/jquery/css/redmond/jquery-ui.min.css";

foreach ($stylesheets as $stylesheet) {
    if (file_exists(PHPGW_SERVER_ROOT . $stylesheet)) {
        $GLOBALS['phpgw']->template->set_var('stylesheet_uri', $webserver_url . $stylesheet . $cache_refresh_token);
        $GLOBALS['phpgw']->template->parse('stylesheets', 'stylesheet', true);
    }
}

if (!empty($GLOBALS['phpgw_info']['server']['site_title'])) {

    $site_title = $GLOBALS['phpgw_info']['server']['site_title'];
}

$headlogopath = $webserver_url . "/phpgwapi/templates/bookingfrontend_2/styleguide/gfx/";

//loads jquery
phpgwapi_jquery::load_widget('core');

$javascripts = array();
$javascripts[] = "/phpgwapi/js/popper/popper.min.js";
$javascripts[] = "/phpgwapi/js/bootstrap5/vendor/twbs/bootstrap/dist/js/bootstrap.min.js";
$javascripts[] = "/phpgwapi/js/select2/js/select2.min.js";
$javascripts[] = "/phpgwapi/templates/bookingfrontend/js/knockout-min.js";
$javascripts[] = "/phpgwapi/templates/bookingfrontend/js/knockout.validation.js";
$javascripts[] = "/phpgwapi/templates/bookingfrontend/js/jquery.autocompleter.js";
$javascripts[] = "/phpgwapi/templates/bookingfrontend_2/js/common.js";
$javascripts[] = "/phpgwapi/templates/bookingfrontend/js/custom.js";
$javascripts[] = "/phpgwapi/templates/bookingfrontend/js/nb-NO.js";
$javascripts[] = "/phpgwapi/js/dateformat/dateformat.js";

foreach ($javascripts as $javascript) {
    if (file_exists(PHPGW_SERVER_ROOT . $javascript)) {
        $GLOBALS['phpgw']->template->set_var('javascript_uri', $webserver_url . $javascript . $cache_refresh_token);
        $GLOBALS['phpgw']->template->parse('javascripts', 'javascript', true);
    }
}

$bodoc = CreateObject('booking.bodocumentation');
$manual = $bodoc->so->getFrontendDoc();

$menuaction = phpgw::get_var('menuaction', 'GET');
$id = phpgw::get_var('id', 'GET');
if (strpos($menuaction, 'organization')) {
    $boorganization = CreateObject('booking.boorganization');
    $metainfo = $boorganization->so->get_metainfo($id);
    $description = preg_replace('/\s+/', ' ', strip_tags($metainfo['description']));
    $keywords = $metainfo['name'] . "," . $metainfo['shortname'] . "," . $metainfo['district'] . "," . $metainfo['city'];
} elseif (strpos($menuaction, 'group')) {
    $bogroup = CreateObject('booking.bogroup');
    $metainfo = $bogroup->so->get_metainfo($id);
    $description = preg_replace('/\s+/', ' ', strip_tags($metainfo['description']));
    $keywords = $metainfo['name'] . "," . $metainfo['shortname'] . "," . $metainfo['organization'] . "," . $metainfo['district'] . "," . $metainfo['city'];
} elseif (strpos($menuaction, 'building')) {
    $bobuilding = CreateObject('booking.bobuilding');
    $metainfo = $bobuilding->so->get_metainfo($id);
    $description = preg_replace('/\s+/', ' ', strip_tags($metainfo['description']));
    $keywords = $metainfo['name'] . "," . $metainfo['district'] . "," . $metainfo['city'];
} elseif (strpos($menuaction, 'resource')) {
    $boresource = CreateObject('booking.boresource');
    $metainfo = $boresource->so->get_metainfo($id);
    $description = preg_replace('/\s+/', ' ', strip_tags($metainfo['description']));
    $keywords = $metainfo['name'] . "," . $metainfo['building'] . "," . $metainfo['district'] . "," . $metainfo['city'];
}
if ($keywords != '') {
    $keywords = '<meta name="keywords" content="' . htmlspecialchars($keywords) . '">';
} else {
    $keywords = '<meta name="keywords" content="PorticoEstate">';
}
if (!empty($description)) {
    $description = '<meta name="description" content="' . htmlspecialchars($description) . '">';
} else {
    $description = '<meta name="description" content="PorticoEstate">';
}
if (!empty($config_frontend['metatag_author'])) {
    $author = '<meta name="author" content="' . $config_frontend['metatag_author'] . '">';
} else {
    $author = '<meta name="author" content="PorticoEstate https://github.com/PorticoEstate/PorticoEstate">';
}
if (!empty($config_frontend['metatag_robots'])) {
    $robots = '<meta name="robots" content="' . $config_frontend['metatag_robots'] . '">';
} else {
    $robots = '<meta name="robots" content="none">';
}
if (!empty($config_frontend['site_title'])) {
    $site_title = $config_frontend['site_title'];
} else {
    $site_title = $GLOBALS['phpgw_info']['server']['site_title'];
}

if (!empty($GLOBALS['phpgw_info']['server']['logo_title'])) {
    $logo_title = $GLOBALS['phpgw_info']['server']['logo_title'];
} else {
    $logo_title = 'Logo';
}

$site_base = $app == 'bookingfrontend' ? "/{$app}/" : '/index.php';

$site_url = $GLOBALS['phpgw']->link($site_base, array());
$eventsearch_url = $GLOBALS['phpgw']->link('/bookingfrontend/', array('menuaction' => 'bookingfrontend.uieventsearch.show'));
$placeholder_search = lang('Search');
$myorgs_text = lang('Show my events');

$userlang = $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'];
$flag_no = "{$webserver_url}/phpgwapi/templates/base/images/flag_no.gif";
$flag_en = "{$webserver_url}/phpgwapi/templates/base/images/flag_en.gif";

$self_uri = $_SERVER['REQUEST_URI'];
$separator = strpos($self_uri, '?') ? '&' : '?';
$self_uri = str_replace(array("{$separator}lang=no", "{$separator}lang=en"), '', $self_uri);

switch ($GLOBALS['phpgw_info']['user']['preferences']['common']['template_set']) {
    case 'bookingfrontend_2':
        $selected_bookingfrontend_2 = ' checked';
        $selected_bookingfrontend = '';
        break;
    case 'bookingfrontend':
        $selected_bookingfrontend_2 = '';
        $selected_bookingfrontend = ' checked';
        break;
}
$about = "";
$faq = "";

if($config_frontend['develope_mode'])
{
	$template_selector = <<<HTML
              <div>
                <h3>Template valg</h3>
                <p>Hvilken template ønsker du?</p>
                <form class="d-flex flex-column">
                  <label class="choice mb-3">
                    <input type="radio" id="template_bookingfrontend" name="select_template" value="bookingfrontend" {$selected_bookingfrontend} />
                    Gammel
                    <span class="choice__radio"></span>
                  </label>
                  <label class="choice mb-5">
                    <input type="radio" id="template_bookingfrontend_2" name="select_template" value="bookingfrontend_2" {$selected_bookingfrontend_2} />
                    Ny
                    <span class="choice__radio"></span>
                  </label>
                </form>
              </div>
HTML;
}
else
{
	$template_selector = '';
}

$nav = <<<HTML
<div class="border-top border-2 py-5">
  <nav class="navbar mb-5">
    <a href="{$site_url}" class="navbar__logo">
      <img src="{$headlogopath}/logo_aktiv_kommune_horizontal.png" alt="Aktiv kommune logo" class="navbar__logo__img">
      <img src="{$headlogopath}/logo_aktiv_kommune.png" alt="Aktiv kommune logo" class="navbar__logo__img--desktop">
    </a>
    <div class="d-flex d-lg-none">
      <button class="pe-btn nav-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasLeft" aria-controls="offcanvasLeft" aria-label="Åpne hovedmeny">
        <span></span>
        <span></span>
        <span></span>
      </button>
    </div>
    <div class="navbar__section navbar__section--right d-none d-lg-flex">
      <!-- Button trigger modal -->
      <button type="button" class="pe-btn pe-btn--transparent navbar__section__language-selector" data-bs-toggle="modal" data-bs-target="#selectLanguage" aria-label="Velg språk">
        <img src="{$headlogopath}/norway.png" alt="Norsk flagg" class="">
        <i class="fas fa-chevron-down"></i>
      </button>

      <!-- Modal -->
      <div class="modal fade" id="selectLanguage" tabindex="-1" aria-labelledby="selectLanguage" aria-hidden="true">
        <div class="modal-dialog modal-sm">
          <div class="modal-content">
            <div class="modal-header border-0">
              <button type="button" class="btn-close text-grey-light" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body d-flex justify-content-center pt-0 pb-4">
              <div>
                <h3>Velg språk</h3>
                <p>Hvilket språk ønsker du?</p>
                <form class="d-flex flex-column">
                  <label class="choice mb-3">
                    <input type="radio" name="select_language" value="norwegian" checked />
                    <img src="{$headlogopath}/norway.png" alt="Norsk flagg" class=""> Norsk
                    <span class="choice__radio"></span>
                  </label>
                  <label class="choice mb-5">
                    <input type="radio" name="select_language" value="english" />
                    <img src="{$headlogopath}/united-kingdom.png" alt="Engelsk flagg" class=""> English
                    <span class="choice__radio"></span>
                  </label>
                  <button type="button" class="pe-btn pe-btn-primary w-auto">Lagre</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
            <button type="button" class="pe-btn pe-btn--transparent navbar__section__language-selector" data-bs-toggle="modal" data-bs-target="#selectTemplate" aria-label="Velg template">
        Template
        <i class="fas fa-chevron-down"></i>
      </button>
            <div class="modal fade" id="selectTemplate" tabindex="-1" aria-labelledby="selectTemplate" aria-hidden="true">
        <div class="modal-dialog modal-sm">
          <div class="modal-content">
            <div class="modal-header border-0">
              <button type="button" class="btn-close text-grey-light" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body d-flex justify-content-center pt-0 pb-4">
             {$template_selector}
            </div>
          </div>
        </div>
      </div>
      <ul class="list-unstyled navbar__section__links">
        <li><a href="{$about}">Hva er Aktiv kommune?</a></li>
        <li><a href="{$faq}">FAQ</a></li>
      </ul>
      <button type="button" class="pe-btn pe-btn-primary py-3">Logg inn</button>
    </div>
  </nav>
</div>
        <div class="offcanvas offcanvas-start main-menu" tabindex="-1" id="offcanvasLeft" aria-labelledby="offcanvasLeftLabel">
          <div class="offcanvas-header justify-content-end">
            <button type="button" class="pe-btn pe-btn--transparent text-xl" data-bs-dismiss="offcanvas" aria-label="Close">
              <i class="fas fa-times"></i>
            </button>
          </div>
          <div class="offcanvas-body">
               <div>
                <h3>Velg språk</h3>
                <p>Hvilket språk ønsker du?</p>
                <form class="d-flex flex-column">
                  <label class="choice mb-3">
                    <input type="radio" name="select_language" value="norwegian" checked />
                    <img src="{$headlogopath}/norway.png" alt="Norsk flagg" class=""> Norsk
                    <span class="choice__radio"></span>
                  </label>
                  <label class="choice mb-5">
                    <input type="radio" name="select_language" value="english" />
                    <img src="{$headlogopath}/united-kingdom.png" alt="Engelsk flagg" class=""> English
                    <span class="choice__radio"></span>
                  </label>
                </form>
              </div>
              <div>
<ul class="list-unstyled">
        <li><a href="${about}">Hva er Aktiv kommune?</a></li>
        <li><a href="${faq}">FAQ</a></li>
      </ul>
      </div>
      	{$template_selector}
          </div>
        </div>
HTML;

$tpl_vars = array
(
    'site_title' => $site_title,
    'css' => $GLOBALS['phpgw']->common->get_css($cache_refresh_token),
    'javascript' => $GLOBALS['phpgw']->common->get_javascript($cache_refresh_token),
    'img_icon' => $GLOBALS['phpgw']->common->find_image('phpgwapi', 'favicon.ico'),
    'str_base_url' => $GLOBALS['phpgw']->link('/', array(), true),
    'dateformat_backend' => $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],
    'site_url' => $GLOBALS['phpgw']->link($site_base, array()),
    'eventsearch_url' => $GLOBALS['phpgw']->link('/bookingfrontend/', array('menuaction' => 'bookingfrontend.uieventsearch.show')),
    'webserver_url' => $webserver_url,
    'metainfo_author' => $author,
    'userlang' => $userlang,
    'metainfo_keywords' => $keywords,
    'metainfo_description' => $description,
    'metainfo_robots' => $robots,
    'lbl_search' => lang('Search'),
    'header_search_class' => 'hidden',//(isset($_GET['menuaction']) && $_GET['menuaction'] == 'bookingfrontend.uisearch.index' ? 'hidden' : '')
    'nav' => empty($GLOBALS['phpgw_info']['flags']['noframework']) ? $nav : ''
);

$GLOBALS['phpgw']->template->set_var($tpl_vars);

$GLOBALS['phpgw']->template->pfp('out', 'head');

$hostname = $_SERVER['SERVER_NAME'];
$port = $_SERVER['SERVER_PORT'];

$LOGIN = $hostname . ':' . $port;

$GLOBALS['phpgw']->template->set_var('LOGIN', $LOGIN);

unset($tpl_vars);
