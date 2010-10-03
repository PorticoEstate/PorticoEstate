<?php
	/**
	* Template header
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage gui
	* @version $Id$
	*/
	phpgw::import_class('phpgwapi.yui');
	
	if ( !isset($GLOBALS['phpgw_info']['server']['site_title']) )
	{
		$GLOBALS['phpgw_info']['server']['site_title'] = lang('please set a site name in admin &gt; siteconfig');
	}
	
	$GLOBALS['phpgw']->template->set_root(PHPGW_TEMPLATE_DIR);
	$GLOBALS['phpgw']->template->set_unknowns('remove');
	$GLOBALS['phpgw']->template->set_file('head', 'head.tpl');
	$GLOBALS['phpgw']->template->set_block('head', 'theme_stylesheet', 'theme_stylesheets');

	$app = $GLOBALS['phpgw_info']['flags']['currentapp'];

	if ( !is_readable("/phpgwapi/templates/idsociety/css/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css") )
	{
		$GLOBALS['phpgw_info']['user']['preferences']['common']['theme'] = 'styles';
	}

	$stylesheets = array();
	if( !isset($GLOBALS['phpgw_info']['flags']['noframework']) )
	{

		phpgwapi_yui::load_widget('dragdrop');
		phpgwapi_yui::load_widget('element');
		phpgwapi_yui::load_widget('container');
		phpgwapi_yui::load_widget('connection');
		phpgwapi_yui::load_widget('resize');
		phpgwapi_yui::load_widget('layout');

		$stylesheets = array
		(
			"/phpgwapi/js/yahoo/reset-fonts-grids/reset-fonts-grids.css",
			"/phpgwapi/js/yahoo/tabview/assets/skins/sam/tabview.css",
			"/phpgwapi/js/yahoo/resize/assets/skins/sam/resize.css",
			"/phpgwapi/js/yahoo/layout/assets/skins/sam/layout.css",
		);
	}

	phpgwapi_yui::load_widget('button');

	$stylesheets[] = '/phpgwapi/js/yahoo/menu/assets/skins/sam/menu.css';
	$stylesheets[] = '/phpgwapi/js/yahoo/button/assets/skins/sam/button.css';
	$stylesheets[] = '/phpgwapi/templates/base/css/base.css';
	$stylesheets[] = '/phpgwapi/templates/verdilak/css/base.css';
	$stylesheets[] = "/phpgwapi/templates/verdilak/css/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css";
	$stylesheets[] = "/{$app}/templates/base/css/base.css";
	$stylesheets[] = "/{$app}/templates/verdilak/css/base.css";
	$stylesheets[] = "/{$app}/templates/verdilak/css/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css";

	foreach ( $stylesheets as $style )
	{

		if( file_exists( PHPGW_SERVER_ROOT . $style ) )
		{
				$GLOBALS['phpgw']->template->set_var('theme_style', "{$GLOBALS['phpgw_info']['server']['webserver_url']}$style");
			$GLOBALS['phpgw']->template->parse('theme_stylesheets', 'theme_stylesheet', true);
		}
	}

	$app = $app ? ' ['.(isset($GLOBALS['phpgw_info']['apps'][$app]) ? $GLOBALS['phpgw_info']['apps'][$app]['title'] : lang($app)).']':'';

	$GLOBALS['phpgw']->js->validate_file('base', 'core');
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
