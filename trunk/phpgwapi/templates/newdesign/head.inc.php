<?php
	phpgw::import_class('phpgwapi.yui');

	if ( !isset($GLOBALS['phpgw_info']['server']['site_title']) )
	{
		$GLOBALS['phpgw_info']['server']['site_title'] = lang('please set a site name in admin &gt; siteconfig');
	}

	$stylesheets = array
	(
		"{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi/js/yahoo/reset-fonts-grids/reset-fonts-grids.css",
		"{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi/templates/newdesign/css/base.css",
		"{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi/templates/newdesign/css/icons.css"
	);

	$app = $GLOBALS['phpgw_info']['flags']['currentapp'];

	if(file_exists(PHPGW_SERVER_ROOT . '/phpgwapi/templates/newdesign/css/' . $GLOBALS['phpgw_info']['user']['preferences']['common']['theme'] . '.css'))
		$theme_styles[] = "{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi/templates/newdesign/css/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css";

	if(file_exists(PHPGW_SERVER_ROOT . "/{$app}/templates/base/css/base.css"))
		$stylesheets[] = "{$GLOBALS['phpgw_info']['server']['webserver_url']}/{$app}/templates/base/css/base.css";

	if(file_exists(PHPGW_SERVER_ROOT . "/{$app}/templates/newdesign/css/base.css"))
		$stylesheets[] = "{$GLOBALS['phpgw_info']['server']['webserver_url']}/{$app}/templates/newdesign/css/base.css";

	if(file_exists(PHPGW_SERVER_ROOT . "/{$app}/templates/newdesign/css/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css"))
		$stylesheets[] = "{$GLOBALS['phpgw_info']['server']['webserver_url']}/{$app}/templates/idots/css/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css";


	$GLOBALS['phpgw']->template->set_root(PHPGW_TEMPLATE_DIR);
	$GLOBALS['phpgw']->template->set_unknowns('remove');
	$GLOBALS['phpgw']->template->set_file('head', 'head.tpl');
	$GLOBALS['phpgw']->template->set_block('head', 'stylesheet', 'stylesheets');

	//FIXME: support other CSS inclusions - see idots head.inc.php for an example
	foreach ( $stylesheets as $stylesheet )
	{
		$GLOBALS['phpgw']->template->set_var('stylesheet_uri', $stylesheet);
		$GLOBALS['phpgw']->template->parse('stylesheets', 'stylesheet', true);
	}

	phpgwapi_yui::load_widget( 'dragdrop' );
	//
	//$GLOBALS['phpgw']->js->validate_file( 'newdesign', 'base', '' );

	$GLOBALS['phpgw']->template->set_var(array
	(
		'javascript'	=> $GLOBALS['phpgw']->common->get_javascript(),
		'site_title'	=> "{$GLOBALS['phpgw_info']['server']['site_title']}",
		'url_root'		=> $GLOBALS['phpgw']->link('/', array(), true),
		'user_fullname' => $GLOBALS['phpgw']->common->display_fullname()
	));

	$GLOBALS['phpgw']->template->pfp('out','head');
?>

