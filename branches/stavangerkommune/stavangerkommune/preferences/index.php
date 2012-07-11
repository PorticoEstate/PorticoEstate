<?php
	/**
	* Preferences
	*
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package preferences
	* @version $Id$
	*/

	$phpgw_info = array();
	$GLOBALS['phpgw_info']['flags']['currentapp'] = 'preferences';
	
	/**
	 * Include phpgroupware header
	 */
	include('../header.inc.php');

	$GLOBALS['phpgw']->template->set_root(PHPGW_APP_TPL);
	$templates = array
	(
		'pref' => 'index.tpl'
	);

	$GLOBALS['phpgw']->template->set_file($templates);

	$GLOBALS['phpgw']->template->set_block('pref', 'list');
	$GLOBALS['phpgw']->template->set_block('pref', 'app_row');
	$GLOBALS['phpgw']->template->set_block('pref', 'app_row_noicon');
	$GLOBALS['phpgw']->template->set_block('pref', 'link_row');
	$GLOBALS['phpgw']->template->set_block('pref', 'spacer_row');

	if ( !$GLOBALS['phpgw']->acl->check('run', 1, 'preferences') )
	{
		die(lang('You do not have access to preferences'));
	}
	
	// This is where we will keep track of our position.
	// Developers won't have to pass around a variable then
	$session_data = $GLOBALS['phpgw']->session->appsession('session_data', 'preferences');

	if (! is_array($session_data))
	{
		$session_data = array('type' => 'user');
		$GLOBALS['phpgw']->session->appsession('session_data', 'preferences', $session_data);
	}

	$type = phpgw::get_var('type', 'string', 'GET');

	if ( !$type )
	{
		$type = $session_data['type'];
	}
	else
	{
		$session_data = array('type' => $type);
		$GLOBALS['phpgw']->session->appsession('session_data', 'preferences', $session_data);
	}

	$tabs = array();
	$tabs[] = array(
		'label' => lang('Your preferences'),
		'link'  => $GLOBALS['phpgw']->link('/preferences/index.php',array('type'=>'user'))
	);
	$tabs[] = array(
		'label' => lang('Default preferences'),
		'link'  => $GLOBALS['phpgw']->link('/preferences/index.php',array('type'=>'default'))
	);
	$tabs[] = array(
		'label' => lang('Forced preferences'),
		'link'  => $GLOBALS['phpgw']->link('/preferences/index.php',array('type'=>'forced'))
	);

	switch($type)
	{
		case 'default':
			$selected = 1;
			break;
		case 'forced':
			$selected = 2;
			break;
		case 'user':
		default:
			$selected = 0;
	}
	$GLOBALS['phpgw']->template->set_var('tabs', $GLOBALS['phpgw']->common->create_tabs($tabs, $selected));

	/**
	 * Dump a row header
	 * 
	 * @param $appname=''
	 * @param $icon
	 */ 
	function section_start($appname='', $icon='')
	{
		$GLOBALS['phpgw']->template->set_var('a_name', $appname);
		$GLOBALS['phpgw']->template->set_var('app_name', $appname);
		$GLOBALS['phpgw']->template->set_var('app_icon', $icon);
		if ( $icon )
		{
			$GLOBALS['phpgw']->template->parse('rows', 'app_row', true);
		}
		else
		{
			$GLOBALS['phpgw']->template->parse('rows', 'app_row_noicon', true);
		} 
	}

	/**
	 * 
	 * 
	 * @param string $pref_link
	 * @param string $pref_text
	 */
	function section_item($pref_link='', $pref_text='')
	{
		$GLOBALS['phpgw']->template->set_var('pref_link', $pref_link);

		if (strtolower($pref_text) == 'grant access' && isset($GLOBALS['phpgw_info']['server']['deny_user_grants_access']) && $GLOBALS['phpgw_info']['server']['deny_user_grants_access'])
		{
			return False;
		}
		else
		{
			$GLOBALS['phpgw']->template->set_var('pref_text', $pref_text);
		}

		$GLOBALS['phpgw']->template->parse('rows', 'link_row', true);
	} 

	/**
	 * 
	 */
	function section_end()
	{
		$GLOBALS['phpgw']->template->parse('rows', 'spacer_row', true);
	}

	/**
	 * 
	 * 
	 * @param $appname
	 * @param $file
	 * @param $file2
	 */
	function display_section($nav, $items)
	{
		section_start($nav['text'], $GLOBALS['phpgw']->common->image($nav['image'][0], $nav['image'][1]));
		foreach ( $items as $item )
		{
			section_item($item['url'], $item['text']);
		}
		section_end(); 
	}

	$menus = execMethod('phpgwapi.menu.get');
	foreach ( $GLOBALS['phpgw_info']['user']['apps'] as $app => $app_info )
	{
		if (isset($menus['preferences'][$app]))
		{
			display_section($menus['navbar'][$app], $menus['preferences'][$app]);
		}
	}

	$GLOBALS['phpgw']->template->pfp('out', 'list');
	$GLOBALS['phpgw']->common->phpgw_footer();
?>
