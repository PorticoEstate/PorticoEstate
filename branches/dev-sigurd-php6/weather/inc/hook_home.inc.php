<?php
	/**************************************************************************\
	* phpGroupWare - hook home                                                 *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */
	{
	global $weather_user, $weather_admin, $g_image_type;
	global $strings,
	$wind_dir_text_short_array, $wind_dir_text_array,
	$cloud_condition_array, $weather_array;

	$d1 = strtolower(substr(PHPGW_APP_INC,0,3));
	if($d1 == "htt" || $d1 == "ftp" )
	{
		echo "Failed attempt to break in via an old Security Hole!<br>\n";
		$GLOBALS['phpgw']->common->phpgw_exit();
	} unset($d1);

	$tmp_app_inc = $GLOBALS['phpgw']->common->get_inc_dir('weather');

	include($tmp_app_inc . '/functions.inc.php');

	weather_get_admin_data();

	weather_get_user_data();

	if ($weather_user['frontpage_enabled'] == 1)
	{
		$app_id = $GLOBALS['phpgw']->applications->name2id('weather');
		$GLOBALS['portal_order'][] = $app_id;
		$GLOBALS['phpgw']->portalbox->set_params(array('app_id'	=> $app_id,
														'title'	=> lang('weather')));
		$GLOBALS['phpgw']->portalbox->draw(weather_display_frontpage());
	}
	}
?>
