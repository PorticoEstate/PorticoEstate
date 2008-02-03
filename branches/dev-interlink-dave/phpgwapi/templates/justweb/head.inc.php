<?php
	/**
	* Template header
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage gui
	* @version $Id: head.inc.php,v 1.13.2.2.2.7 2003/08/28 05:37:31 skwashd Exp $
	*/
	
	if ( !is_object($GLOBALS['phpgw']->js) )
	{
		$GLOBALS['phpgw']->js =& createObject('phpgwapi.javascript');
	}
	$GLOBALS['phpgw']->js->add_event('load', 'init();' );

	$app = $GLOBALS['phpgw_info']['flags']['currentapp'];
	$app = $app ? ' ['.(isset($GLOBALS['phpgw_info']['apps'][$app]) ? $GLOBALS['phpgw_info']['apps'][$app]['title'] : lang($app)).']':'';

	$tpl = CreateObject('phpgwapi.Template',PHPGW_TEMPLATE_DIR);
	$tpl->set_unknowns('remove');
	$tpl->set_file(array('head' => 'head.tpl'));

	$var = Array (
		'img_icon'      => PHPGW_IMAGES_DIR . '/favicon.ico',
		'img_shortcut'  => PHPGW_IMAGES_DIR . '/favicon.ico',
		'webserver_url'	=> $GLOBALS['phpgw_info']['server']['webserver_url'],
		'home'			=> $GLOBALS['phpgw']->link('/index.php'),
		'appt'			=> $GLOBALS['phpgw']->link('/index.php',Array('menuaction'=>'calendar.uicalendar.day')),
		'todo'			=> $GLOBALS['phpgw']->link('/index.php',Array('menuaction'=>'todo.uitodo.add')),
		'prefs'			=> $GLOBALS['phpgw']->link('/preferences/index.php'),
		'email'			=> $GLOBALS['phpgw']->link('/index.php',Array('menuaction'=>'email.uipreferences.preferences')),
		'calendar'		=> $GLOBALS['phpgw']->link('/index.php',Array('menuaction'=>'calendar.uipreferences.preferences')),
		'addressbook'	=> $GLOBALS['phpgw']->link('/index.php',Array('menuaction'=>'addressbook.uiaddressbook.preferences')),
		'website_title'	=> $GLOBALS['phpgw_info']['server']['site_title'] . $app,
		'css'			=> $GLOBALS['phpgw']->common->get_css(),
		'java_script'	=> $GLOBALS['phpgw']->common->get_java_script(),
		'str_base_url'	=> $GLOBALS['phpgw']->link('/'),
		'win_on_events'	=> $GLOBALS['phpgw']->common->get_on_events()
	);
	$tpl->set_var($var);
	$tpl->pfp('out','head');
	unset($tpl);
?>
