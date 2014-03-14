<?php
	/**
	* phpGroupWare
	*
	* phpgroupware base
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgroupware
	* @version $Id: home.php 11511 2013-12-08 20:57:07Z sigurdne $
	*/

	/**
	* @global array $phpgw_info
	*/
	$GLOBALS['phpgw_info'] = array();

	$GLOBALS['phpgw_info']['flags'] = array
	(
		'noheader'					=> true,
		'nonavbar'					=> true,
		'currentapp'				=> 'home',
		'enable_network_class'		=> true,
		'enable_contacts_class'		=> true,
		'enable_nextmatchs_class'	=> true,
		'template_set'				=> 'mobilefrontend',
		'custom_frontend'			=> 'mobilefrontend'

	);

	/**
	* Include phpgroupware header
	*/
	require_once '../header.inc.php';

/*
	// check if forward parameter is set
	if ( isset($_GET['phpgw_forward']) && is_array($_GET['phpgw_forward']) )
	{
		foreach($_GET as $name => $value)
		{
			// find phpgw_ in the $_GET parameters but skip phpgw_forward because of redirect call below
			if (ereg('phpgw_', $name) && ($name != 'phpgw_forward'))
			{
				$name = substr($name, 6); // cut 'phpgw_'
				$extra_vars[$name] = $value;
			}
		}

		$GLOBALS['phpgw']->redirect_link($_GET['phpgw_forward'], $extra_vars);
		exit;
	}
*/
	if ( isset($GLOBALS['phpgw_info']['server']['force_default_app'])
		&& $GLOBALS['phpgw_info']['server']['force_default_app'] != 'user_choice')
	{
		$GLOBALS['phpgw_info']['user']['preferences']['common']['default_app'] = $GLOBALS['phpgw_info']['server']['force_default_app'];
	}

/*
	if (isset($_GET['cd']) && $_GET['cd']=='yes'
		&& isset($GLOBALS['phpgw_info']['user']['preferences']['common']['default_app'])
		&& $GLOBALS['phpgw_info']['user']['preferences']['common']['default_app']
		&& $GLOBALS['phpgw_info']['user']['apps'][$GLOBALS['phpgw_info']['user']['preferences']['common']['default_app']])
	{
		$GLOBALS['phpgw']->redirect_link('/' . $GLOBALS['phpgw_info']['user']['preferences']['common']['default_app'] . '/' . 'index.php');
		exit;
	}
	else */
	{

		phpgw::import_class('phpgwapi.jquery');
		phpgwapi_jquery::load_widget('core');
		$GLOBALS['phpgw']->common->phpgw_header();
		echo parse_navbar();
	}

	$GLOBALS['phpgw']->translation->add_app('mainscreen');
	if (lang('mainscreen_message') != '!mainscreen_message')
	{
		echo '<div class="msg">' . lang('mainscreen_message') . '</div>';
	}


	// This initializes the users portal_order preference if it does not exist.
	if ( (!isset($GLOBALS['phpgw_info']['user']['preferences']['portal_order']) || !is_array($GLOBALS['phpgw_info']['user']['preferences']['portal_order']) )
		&& $GLOBALS['phpgw_info']['apps'] )
	{
		$GLOBALS['phpgw']->preferences->delete('portal_order');
		$order = 0;
		foreach ( $GLOBALS['phpgw_info']['apps'] as $p )
		{
			if ( isset($GLOBALS['phpgw_info']['user']['apps'][$p['name']])
				&& $GLOBALS['phpgw_info']['user']['apps'][$p['name']] )
			{
				$GLOBALS['phpgw']->preferences->add('portal_order', ++$order, $p['id']);
			}
		}
		$GLOBALS['phpgw_info']['user']['preferences'] = $GLOBALS['phpgw']->preferences->save_repository();
	}

	if ( isset($GLOBALS['phpgw_info']['user']['preferences']['portal_order'])
		&& is_array($GLOBALS['phpgw_info']['user']['preferences']['portal_order']) )
	{
		$app_check = array();
		ksort($GLOBALS['phpgw_info']['user']['preferences']['portal_order']);
		foreach($GLOBALS['phpgw_info']['user']['preferences']['portal_order'] as $app)
		{
			if(!isset($app_check[$app]) || !$app_check[$app])
			{
				$app_check[$app] = true;
				$sorted_apps[] = $GLOBALS['phpgw']->applications->id2name($app);
			}
		}
	}

	$controller_url = $GLOBALS['phpgw']->link( '/index.php', array('menuaction' => 'controller.uicontrol.control_list') );
	$controller_text = lang('controller');
	$tts_url = $GLOBALS['phpgw']->link( '/index.php', array('menuaction' => 'property.uitts.index') );
	$tts_text = lang('ticket');
	
	$temp_menu = <<<HTML
	<div id="home-menu">
		<a href="{$controller_url}">{$controller_text}</a>
		<a href="{$tts_url}">{$tts_text}</a>
	</div>
HTML;

	echo $temp_menu;

	$GLOBALS['phpgw']->hooks->process('home_mobilefrontend', $sorted_apps);

	if ( isset($GLOBALS['portal_order']) && is_array($GLOBALS['portal_order']) )
	{
		$GLOBALS['phpgw']->preferences->delete('portal_order');
		foreach ( $GLOBALS['portal_order']  as $app_order => $app_id )
		{
			$GLOBALS['phpgw']->preferences->add('portal_order', $app_order, $app_id);
		}
		$GLOBALS['phpgw']->preferences->save_repository();
	}
	$GLOBALS['phpgw']->common->phpgw_footer();
