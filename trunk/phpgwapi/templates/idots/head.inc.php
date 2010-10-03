<?php
	/**
	* phpGroupWare - idots template - header
	*
	* @copyright Copyright (C) 2003-2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgroupware
	* @subpackage phpgwapi
	* @version $Id: head.inc.php,v 1.4 2004/12/30 06:47:34 skwashd Exp 
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
		$GLOBALS['phpgw_info']['user']['preferences']['common']['theme'] = 'idots';
	}


	$stylesheets = array();
	if( !isset($GLOBALS['phpgw_info']['flags']['noframework']) )
	{

		$stylesheets = array
		(
			'/phpgwapi/js/yahoo/reset-fonts-grids/reset-fonts-grids.css',
			'/phpgwapi/js/yahoo/menu/assets/skins/sam/menu.css',
			'/phpgwapi/js/yahoo/tabview/assets/skins/sam/tabview.css',
		);
	}

	phpgwapi_yui::load_widget('button');

	$stylesheets[] = '/phpgwapi/templates/base/css/base.css';
	$stylesheets[] = '/phpgwapi/templates/idots/css/base.css';
	$stylesheets[] = "/phpgwapi/templates/idots/css/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css";
	$stylesheets[] = "/{$app}/templates/base/css/base.css";
	$stylesheets[] = "/{$app}/templates/idots/css/base.css";
	$stylesheets[] = "/{$app}/templates/idots/css/{$GLOBALS['phpgw_info']['user']['preferences']['common']['theme']}.css";

	foreach ( $stylesheets as $style )
	{
		if( file_exists( PHPGW_SERVER_ROOT . $style ) )
		{
				$GLOBALS['phpgw']->template->set_var('theme_style', "{$GLOBALS['phpgw_info']['server']['webserver_url']}$style");
			$GLOBALS['phpgw']->template->parse('theme_stylesheets', 'theme_stylesheet', true);
		}
	}
	
	$app = $app ? ' ['.(isset($GLOBALS['phpgw_info']['apps'][$app]) ? $GLOBALS['phpgw_info']['apps'][$app]['title'] : lang($app)).']':'';

	phpgwapi_yui::load_widget('menu');
	$GLOBALS['phpgw']->template->set_var(array
	(
		'css'			=> $GLOBALS['phpgw']->common->get_css(),
		'javascript'	=> $GLOBALS['phpgw']->common->get_javascript(),
		'img_icon'      => PHPGW_IMAGES_DIR . '/favicon.ico',
		'str_base_url'	=> $GLOBALS['phpgw']->link('/', array(), true),		
		'website_title'	=> "{$GLOBALS['phpgw_info']['server']['site_title']} | {$app}",
		'win_on_events'	=> $GLOBALS['phpgw']->common->get_on_events()
	));

	$GLOBALS['phpgw']->template->pfp('out','head');
