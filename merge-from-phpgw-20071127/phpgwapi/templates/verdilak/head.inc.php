<?php
	/**
	* Template header
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage gui
	* @version $Id: head.inc.php 17630 2006-12-03 18:31:43Z sigurdne $
	*/
	
	if ( !isset($GLOBALS['phpgw_info']['server']['site_title']) )
	{
		$GLOBALS['phpgw_info']['server']['site_title'] = lang('please set a site name in admin &gt; siteconfig');
	}
	
	$GLOBALS['phpgw']->template->set_root(PHPGW_TEMPLATE_DIR);
	$GLOBALS['phpgw']->template->set_unknowns('remove');
	$GLOBALS['phpgw']->template->set_file('head', 'head.tpl');
	$GLOBALS['phpgw']->template->set_block('head', 'theme_stylesheet', 'theme_stylesheets');

	$app = $GLOBALS['phpgw_info']['flags']['currentapp'];

	$theme_styles[] = "{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi/templates/verdilak/css/base.css";
	if(isset($GLOBALS['phpgw_info']['user']['preferences']['common']['theme']) && file_exists(PHPGW_SERVER_ROOT . '/phpgwapi/templates/verdilak/css/' . $GLOBALS['phpgw_info']['user']['preferences']['common']['theme'] . '.css'))
	{
		$theme_styles[] = "{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi/templates/verdilak/css/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css";
	}
	else
	{
		$theme_styles[] = "{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi/templates/verdilak/css/styles.css";
		$GLOBALS['phpgw_info']['user']['preferences']['common']['theme'] = 'verdilak';
	}

	if(file_exists(PHPGW_SERVER_ROOT . "/{$app}/templates/base/css/base.css"))
	{
		$theme_styles[] = "{$GLOBALS['phpgw_info']['server']['webserver_url']}/{$app}/templates/base/css/base.css";
	}

	if(file_exists(PHPGW_SERVER_ROOT . "/{$app}/templates/verdilak/css/base.css"))
	{
		$theme_styles[] = "{$GLOBALS['phpgw_info']['server']['webserver_url']}/{$app}/templates/verdilak/css/base.css";
	}

	if(file_exists(PHPGW_SERVER_ROOT . "/{$app}/templates/verdilak/css/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css"))
	{
		$theme_styles[] = "{$GLOBALS['phpgw_info']['server']['webserver_url']}/{$app}/templates/verdilak/css/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css";
	}

	foreach ( $theme_styles as $style )
	{
		$GLOBALS['phpgw']->template->set_var('theme_style', $style);
		$GLOBALS['phpgw']->template->parse('theme_stylesheets', 'theme_stylesheet', true);
	}
	
	$app = $app ? ' ['.(isset($GLOBALS['phpgw_info']['apps'][$app]) ? $GLOBALS['phpgw_info']['apps'][$app]['title'] : lang($app)).']':'';

	$GLOBALS['phpgw']->template->set_var(array
	(
		'css'			=> $GLOBALS['phpgw']->common->get_css(),
		'javascript'	=> $GLOBALS['phpgw']->common->get_javascript(),
		'img_icon'      => PHPGW_IMAGES_DIR . '/favicon.ico',
		'img_shortcut'  => PHPGW_IMAGES_DIR . '/favicon.ico',
		'str_base_url'	=> $GLOBALS['phpgw']->link('/', array(), true),		
		'website_title'	=> $GLOBALS['phpgw_info']['server']['site_title'] . $app,
		'win_on_events'	=> $GLOBALS['phpgw']->common->get_on_events(),
		'phpgw_root'	=> $GLOBALS['phpgw_info']['server']['webserver_url'] . '/',
	));

	$GLOBALS['phpgw']->template->pfp('out','head');
?>

