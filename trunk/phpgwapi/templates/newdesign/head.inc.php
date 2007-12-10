<?php
	if ( !isset($GLOBALS['phpgw_info']['server']['site_title']) )
	{
		$GLOBALS['phpgw_info']['server']['site_title'] = lang('please set a site name in admin &gt; siteconfig');
	}

	$stylesheets[] = "{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi/js/yahoo/reset-fonts-grids/reset-fonts-grids.css";
	$stylesheets[] = "{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi/templates/newdesign/css/base.css";

	$GLOBALS['phpgw']->template->set_root(PHPGW_TEMPLATE_DIR);
	$GLOBALS['phpgw']->template->set_unknowns('remove');
	$GLOBALS['phpgw']->template->set_file('head', 'head.tpl');
	$GLOBALS['phpgw']->template->set_block('head', 'stylesheet', 'stylesheets');

	foreach ( $stylesheets as $stylesheet )
	{
		$GLOBALS['phpgw']->template->set_var('stylesheet_uri', $stylesheet);
		$GLOBALS['phpgw']->template->parse('stylesheets', 'stylesheet', true);
	}

	$GLOBALS['phpgw']->template->set_var(array
	(

		'javascript'	=> $GLOBALS['phpgw']->common->get_javascript(),
		'site_title'	=> $GLOBALS['phpgw_info']['server']['site_title'] . $app
	));
	$GLOBALS['phpgw']->template->pfp('out','head');
?>

