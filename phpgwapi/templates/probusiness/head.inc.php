<?php
/**
 * probusiness template set header
 * @author Philipp Kamps <pkamps@probusiness.de>
 * @author Daniel Briegert <dbriegert@probusiness.de>
 * @copyright Copyright (C) 2003-2005 Free Software Foundation http://www.fsf.org/
 * @license http://www.fsf.org/licenses/gpl.html GNU General Public License
 * @package phpgwapi
 * @subpackage gui
 * @version $Id$
 */
	
	// css file handling
	$stylesheets = array();
	$stylesheets[] = "/phpgwapi/templates/pure/css/global.css";
	$stylesheets[] = "/phpgwapi/templates/pure/css/pure-min.css";
	$stylesheets[] = "/phpgwapi/templates/pure/css/pure-extension.css";
	$stylesheets[] = "/phpgwapi/templates/pure/css/grids-responsive-min.css";
	$stylesheets[] = "/phpgwapi/js/DataTables/DataTables/css/jquery.dataTables.min.css";
	$stylesheets[] = "/phpgwapi/js/DataTables/DataTables/css/dataTables.jqueryui.min.css";
	$stylesheets[] = "/phpgwapi/js/DataTables/Responsive/css/responsive.dataTables.min.css";

	$css_file = PHPGW_SERVER_ROOT . '/phpgwapi/templates/probusiness/css/'.$GLOBALS['phpgw_info']['user']['preferences']['common']['theme'].'.css';
	if (file_exists($css_file))
	{
		$stylesheets[] = $GLOBALS['phpgw_info']['server']['webserver_url'] . '/phpgwapi/templates/probusiness/css/'.$GLOBALS['phpgw_info']['user']['preferences']['common']['theme'].'.css';
	}
	else
	{
		$stylesheets[] = $GLOBALS['phpgw_info']['server']['webserver_url'] . '/phpgwapi/templates/probusiness/css/styles.css';
	}

	$tpl = CreateObject('phpgwapi.template',PHPGW_TEMPLATE_DIR);
	$tpl->set_unknowns('remove');

	$tpl->set_file(array('head' => 'head.tpl'));
	$tpl->set_block('head', 'theme_stylesheet', 'theme_stylesheets');

	foreach ( $stylesheets as $style )
	{
		$tpl->set_var('theme_style', $style);
		$tpl->parse('theme_stylesheets', 'theme_stylesheet', true);
	}

	$app = $GLOBALS['phpgw_info']['flags']['currentapp'];
	$app = $app ? ' ['.(isset($GLOBALS['phpgw_info']['apps'][$app]) ? $GLOBALS['phpgw_info']['apps'][$app]['title'] : lang($app)).']':'';

	$var = array
	(
		'img_icon'      => PHPGW_IMAGES_DIR . '/favicon.ico',
		'img_shortcut'  => PHPGW_IMAGES_DIR . '/favicon.ico',
		'font_family'   => (isset($GLOBALS['phpgw_info']['theme']['font'])?$GLOBALS['phpgw_info']['theme']['font']:''),
		'website_title' => (isset($GLOBALS['phpgw_info']['server']['site_title'])?$GLOBALS['phpgw_info']['server']['site_title']:'') . $app,
		'css'           => $GLOBALS['phpgw']->common->get_css(),
		'java_script'   => $GLOBALS['phpgw']->common->get_java_script(),
		'api_root'      => $GLOBALS['phpgw_info']['server']['webserver_url'] . '/phpgwapi/templates/probusiness/',
		'phpgw_root'	=> $GLOBALS['phpgw_info']['server']['webserver_url'] . '/',
		'str_base_url'	=> $GLOBALS['phpgw']->link('/'),
		'win_on_events'	=> $GLOBALS['phpgw']->common->get_on_events()
	);

	$tpl->set_var($var);
	$tpl->pfp('out','head');
	unset($tpl);

