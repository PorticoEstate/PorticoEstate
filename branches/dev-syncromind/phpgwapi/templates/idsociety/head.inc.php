<?php
	/**
	* Template header
	* @copyright Copyright (C) 2003-2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage gui
	* @version $Id$
	*/

	$bodyheader = 'bgcolor="'.$GLOBALS['phpgw_info']['theme']['bg_color'].'" alink="'.$GLOBALS['phpgw_info']['theme']['alink'].'" link="'.$GLOBALS['phpgw_info']['theme']['link'].'" vlink="'.$GLOBALS['phpgw_info']['theme']['vlink'].'"';

	$app = $GLOBALS['phpgw_info']['flags']['currentapp'];

	$GLOBALS['phpgw']->template->set_root(PHPGW_TEMPLATE_DIR);
	$GLOBALS['phpgw']->template->set_unknowns('remove');
	$GLOBALS['phpgw']->template->set_file('head', 'head.tpl');
	$GLOBALS['phpgw']->template->set_block('head', 'theme_stylesheet', 'theme_stylesheets');

	if ( !is_readable("/phpgwapi/templates/idsociety/css/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css") )
	{
		$GLOBALS['phpgw_info']['user']['preferences']['common']['theme'] = 'idsociety';
	}
	
	$theme_styles = array
	(
		'/phpgwapi/templates/idsociety/css/base.css',
		"/phpgwapi/templates/idsociety/css/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css",
		"/{$app}/templates/base/css/base.css",
		"/{$app}/templates/idsociety/css/base.css",
		"/{$app}/templates/idsociety/css/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css"
	);

	foreach ( $theme_styles as $style )
	{
		if( file_exists( PHPGW_SERVER_ROOT . $style ) )
		{
			$GLOBALS['phpgw']->template->set_var('theme_style', "{$GLOBALS['phpgw_info']['server']['webserver_url']}$style");
			$GLOBALS['phpgw']->template->parse('theme_stylesheets', 'theme_stylesheet', true);
		}
	}


	$var = array
	(
		'str_base_url'	=> $GLOBALS['phpgw']->link('/', array(), true),
		'img_icon'      => PHPGW_IMAGES_DIR . '/favicon.ico',
		'website_title'	=> "{$GLOBALS['phpgw_info']['server']['site_title']} [" . lang($app) . ']',
		'win_on_events'	=> $GLOBALS['phpgw']->common->get_on_events(),
		'css'			=> $GLOBALS['phpgw']->common->get_css(),
		'java_script'	=> $GLOBALS['phpgw']->common->get_java_script(),
		'win_on_events'	=> $GLOBALS['phpgw']->common->get_on_events()
	);
	$GLOBALS['phpgw']->template->set_var($var);
	$GLOBALS['phpgw']->template->pfp('out','head');
	unset($tpl);
?>
