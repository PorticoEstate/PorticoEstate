<?php
  /**************************************************************************\
  * phpGroupWare - Calendar                                                  *
  * http://www.phpgroupware.org                                              *
  * Based on Webcalendar by Craig Knudsen <cknudsen@radix.net>               *
  *          http://www.radix.net/~cknudsen                                  *
  * Written by Mark Peters <skeeter@phpgroupware.org>                        *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id$ */


	if ( !isset($GLOBALS['phpgw_info']['user']['preferences']['calendar']['mainscreen_showevents'])
		|| !$GLOBALS['phpgw_info']['user']['preferences']['calendar']['mainscreen_showevents'] )
	{
		return;
	}
	$GLOBALS['phpgw']->translation->add_app('calendar');

	phpgw::import_class('phpgwapi.datetime');

	$GLOBALS['date'] = date('Ymd', phpgwapi_datetime::user_localtime() );
	$GLOBALS['g_year'] = substr($GLOBALS['date'],0,4);
	$GLOBALS['g_month'] = substr($GLOBALS['date'],4,2);
	$GLOBALS['g_day'] = substr($GLOBALS['date'],6,2);
	$GLOBALS['owner'] = $GLOBALS['phpgw_info']['user']['account_id'];
	$GLOBALS['css'] = "\n<style type=\"text/css\">\n<!--\n"
		. "@import url('calendar/templates/base/css/base.css')\n-->\n</style>\n";

	$page_ = explode('.',$GLOBALS['phpgw_info']['user']['preferences']['calendar']['defaultcalendar']);
	$_page = substr($page_[0],0,7);	// makes planner from planner_{user|category}
	if ( $_page=='index' || ($_page != 'day' && $_page != 'week' && $_page != 'month' && $_page != 'year' && $_page != 'planner'))
	{
		$_page = 'month';
		$GLOBALS['phpgw']->preferences->read();
		$GLOBALS['phpgw']->preferences->add('calendar','defaultcalendar','month');
		$GLOBALS['phpgw']->preferences->save_repository();
	}

	if(!@file_exists(PHPGW_INCLUDE_ROOT.'/calendar/inc/hook_home_'.$_page.'.inc.php'))
	{
		$_page = 'month';
		$GLOBALS['phpgw']->preferences->read();
		$GLOBALS['phpgw']->preferences->add('calendar','defaultcalendar','month');
		$GLOBALS['phpgw']->preferences->save_repository();
	}

	include_once(PHPGW_INCLUDE_ROOT.'/calendar/inc/hook_home_'.$_page.'.inc.php');
	
	$title = lang('Calendar');
	
	//TODO Make listbox css compliant
	$portalbox = CreateObject('phpgwapi.listbox', array
	(
		'title'	=> $title,
		'primary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
		'secondary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
		'tertiary'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
		'width'	=> '100%',
		'outerborderwidth'	=> '0',
		'header_background_image'	=> $GLOBALS['phpgw']->common->image('phpgwapi','bg_filler', '.png', False)
	));

	$app_id = $GLOBALS['phpgw']->applications->name2id('calendar');
	$GLOBALS['portal_order'][] = $app_id;
	$var = array
	(
		'up'	=> Array('url'	=> '/set_box.php', 'app'	=> $app_id),
		'down'	=> Array('url'	=> '/set_box.php', 'app'	=> $app_id),
		'close'	=> Array('url'	=> '/set_box.php', 'app'	=> $app_id),
		'question'	=> Array('url'	=> '/set_box.php', 'app'	=> $app_id),
		'edit'	=> Array('url'	=> '/set_box.php', 'app'	=> $app_id)
	);

	foreach ( $var as $key => $value )
	{
		$portalbox->set_controls($key,$value);
	}

	$portalbox->data = array();

	echo "\n".'<!-- BEGIN Calendar info -->'."\n".$portalbox->draw($GLOBALS['extra_data'])."\n".'<!-- END Calendar info -->'."\n";
	unset($cal);
?>
