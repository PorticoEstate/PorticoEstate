<?php
/**
* Template header
* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
* @package phpgwapi
* @subpackage gui
* @version $Id$
*/
	$p = createobject('phpgwapi.preferences');
	$preferences = $p->read();
	if (isset ($preferences[$GLOBALS['phpgw_info']['flags']['currentapp']]['refreshTime']))
	{
		$refreshTime = $preferences[$GLOBALS['phpgw_info']['flags']['currentapp']]['refreshTime'] * 60;
	}
	if (!is_object($GLOBALS['phpgw']->js))
	{
		$GLOBALS['phpgw']->js = & createObject('phpgwapi.javascript');
	}
	$GLOBALS['phpgw']->js->add_event('load', 'pageInit();');
	$app = $GLOBALS['phpgw_info']['flags']['currentapp'];
	$app = $app ? ' ['. (isset ($GLOBALS['phpgw_info']['apps'][$app]) ? $GLOBALS['phpgw_info']['apps'][$app]['title'] : lang($app)).']' : '';

	$tpl = CreateObject('phpgwapi.Template', PHPGW_TEMPLATE_DIR);
	$tpl->set_unknowns('remove');
	$tpl->set_file(array ('head' => 'head.tpl'));

	$var = array
			(
				'img_icon' => PHPGW_IMAGES_DIR.'/favicon.ico',
				'img_shortcut' => PHPGW_IMAGES_DIR.'/favicon.ico',
				'website_title' => $GLOBALS['phpgw_info']['server']['site_title'],
				'app_name' => $app,
				'bg_color' => $GLOBALS['phpgw_info']['theme']['bg_color'],
				'refreshTime' => $refreshTime,
				'css' => $GLOBALS['phpgw']->common->get_css(),
				'java_script' => $GLOBALS['phpgw']->common->get_java_script(),
				'str_base_url' => $GLOBALS['phpgw']->link('/'),
				'webserver_url' => $GLOBALS['phpgw_info']['server']['webserver_url'],
				'win_on_events' => $GLOBALS['phpgw']->common->get_on_events()
			);

	$tpl->set_var($var);
	$tpl->pfp('out', 'head');
	unset ($tpl);
?>


