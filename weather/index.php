<?php
    /**************************************************************************\
    * phpGroupWare - Weather                                                   *
    * http://www.phpgroupware.org                                              *
    * This file written by Sam Wynn <neotexan@wynnsite.com>                    *
    * --------------------------------------------                             *
    *  This program is free software; you can redistribute it and/or modify it *
    *  under the terms of the GNU General Public License as published by the   *
    *  Free Software Foundation; either version 2 of the License, or (at your  *
    *  option) any later version.                                              *
    \**************************************************************************/

    /* $Id: index.php 8454 2001-12-03 18:01:51Z milosch $ */

	$expire = date('D, M d Y h:i:s ', time()+3600).strftime('%Z');
	Header('Last Modified: ' . $expire);
	Header('Expires: ' . $expire);
	Header('Cache-Control: no-cache, must-revalidate');
	Header('Pragma: no-cache');
    
	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'weather', 
		'enable_nextmatchs_class' => True,
		'enable_network_class'    => True
	);
    include('../header.inc.php');
    
    weather_get_admin_data();
    weather_get_user_data();
    weather_display_title($title_c);

    weather_display_afo(
		$start,
		$matchs_c,
		$advisory_c,
		$forecast_c, $extforecast_c,
		$observation_c
	);

	weather_display_links($link_c);

	/**************************************************************************
	* determine the output template
	*************************************************************************/
	$template_format     = sprintf('format%02d', $weather_user['template']);
	if (!(file_exists(PHPGW_APP_TPL .'/'.$template_format.'.weather.tpl')))
	{
		$template_format = 'format00';
	}

	/**************************************************************************
	* pull it all together
	*************************************************************************/
	$body_tpl = CreateObject('phpgwapi.Template',
	$GLOBALS['phpgw']->common->get_tpl_dir('weather'));
	$body_tpl->set_unknowns('remove');
	$body_tpl->set_file('body', $template_format . '.weather.tpl');
	$body_tpl->set_var(array(
		'title'       => $title_c,
		'matchs'      => $matchs_c,
		'advisory'    => $advisory_c,
		'observation' => $observation_c,
		'forecast'    => $forecast_c,
		'extforecast' => $extforecast_c,
		'link'        => $link_c
	));
	$body_tpl->parse(BODY, "body");
	$body_tpl->p("BODY");

	$GLOBALS['phpgw']->common->phpgw_footer();
?>
