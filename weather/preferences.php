<?php
    /**************************************************************************\
    * phpGroupWare - Weather Request Preferences                               *
    * http://www.phpgroupware.org                                              *
    * This file written by Sam Wynn <neotexan@wynnsite.com>                    *
    * --------------------------------------------                             *
    *  This program is free software; you can redistribute it and/or modify it *
    *  under the terms of the GNU General Public License as published by the   *
    *  Free Software Foundation; either version 2 of the License, or (at your  *
    *  option) any later version.                                              *
    \**************************************************************************/

    /* $Id: preferences.php 8454 2001-12-03 18:01:51Z milosch $ */

	$GLOBALS['phpgw_info']['flags']['currentapp'] = 'weather';
	include('../header.inc.php');

	$title              = lang('Weather Center Preferences');

	$layout_label       = lang('Display Layout');
	$template_label     = lang('Template');

	$sticker_label      = lang('Weather Stickers');
	$tenable_label      = lang('Title Sticker Enabled');
	$tsize_label        = lang('Title Sticker Size');
	$tmetar_label       = lang('Title Metar');
	$fpenable_label     = lang('Front Page Sticker Enabled');
	$fpsize_label       = lang('Front Page Sticker Size');
	$fpmetar_label      = lang('Front Page Metar');
	$wunderenable_label = lang('Wunderground Link Enabled');
	$sticker_src_label  = lang('Sticker Image Source');

	$remote_label       = lang('Weather Remote Data');
	$fenable_label      = lang('Forecasts Enabled');
	$oenable_label      = lang('Observations Enabled');
	$mavail_label       = lang('Available Metar Stations');

	$links_label        = lang('Weather Links');
	$lenable_label      = lang('Links Enabled');
	$lavail_label       = lang('Available Links');
	$city_label         = lang('City');
	$country_label      = lang('Country');
	$gstation_label     = lang('Global Station');
	$state_label        = lang('State');

	$action_label       = lang('Submit');
	$reset_label        = lang('Reset');
	$done_label         = lang('Done');

	$actionurl          = $GLOBALS['phpgw']->link('/weather/preferences.php');
	$doneurl            = $GLOBALS['phpgw']->link('/preferences/index.php');

	$message            = '';

	if ($submit)
	{
		$message = lang('Weather Preferences Updated');

		if ($metars)
		{
			$weather_user['metar'] = implode($metars,',');
		}
		else
		{
			$weather_user['metar'] = '';
		}

		if ($links)
		{
			$weather_user['links'] = implode($links, ',');
		}
		else
		{
			$weather_user['links'] = '';
		}

		$weather_user['observations_enabled'] = $observations_enabled;
		$weather_user['forecasts_enabled']    = $forecasts_enabled;
		$weather_user['links_enabled']        = $links_enabled;
		$weather_user['wunderground_enabled'] = $wunderground_enabled;
		$weather_user['template']             = $template_id;
		$weather_user['city']                 = $city;
		$weather_user['state_id']             = $stateid;
		$weather_user['country']              = $country;
		$weather_user['global_station']       = $gstation;
		$weather_user['title_enabled']        = $title_enabled;
		$weather_user['title_metar']          = $title_metar;
		$weather_user['title_size']           = $title_size;
		$weather_user['frontpage_enabled']    = $frontpage_enabled;
		$weather_user['frontpage_metar']      = $frontpage_metar;
		$weather_user['frontpage_size']       = $frontpage_size;
		$weather_user['sticker_source']       = $sticker_source;
		$weather_user['id']                   = $weather_id;

		weather_set_user_data();
	}

	weather_get_user_data();

	weather_template_options($weather_user['template'], $t_options_c, $t_images_c);
	metar_options($weather_user['metar'], $m_options_c, True);
	metar_options($weather_user['title_metar'],               $tm_options_c);
	metar_options($weather_user['frontpage_metar'],           $fpm_options_c);
	sticker_size_options($weather_user['title_size'],         $ts_options_c);
	sticker_size_options($weather_user['frontpage_size'],     $fps_options_c);
	state_options($weather_user['state_id'],                  $s_options_c);
	link_options($weather_user['links'],                      $l_options_c);
	sticker_source_options($weather_user['sticker_source'],   $ss_options_c);

	$wunderenable_checked = $g_checked[$weather_user['wunderground_enabled']];
	$title_checked        = $g_checked[$weather_user['title_enabled']];
	$fpage_checked        = $g_checked[$weather_user['frontpage_enabled']];
	$links_checked        = $g_checked[$weather_user['links_enabled']];
	$forecast_checked     = $g_checked[$weather_user['forecasts_enabled']];
	$observation_checked  = $g_checked[$weather_user['observations_enabled']];

	$prefs_tpl =  CreateObject('phpgwapi.Template',
	$GLOBALS['phpgw']->common->get_tpl_dir('weather'));
	$prefs_tpl->set_unknowns("remove");
	$prefs_tpl->set_file(array(
		'message' => 'message.common.tpl',
		'prefs'   => 'prefs.body.tpl'
	));
	$prefs_tpl->set_var(array(
		'messagename'         => $message,
		'title'               => $title,
		'action_url'          => $actionurl,
		'action_label'        => $action_label,
		'done_url'            => $doneurl,
		'done_label'          => $done_label,
		'reset_label'         => $reset_label,
		'weather_id'          => $weather_user['id'],

		'layout_label'        => $layout_label,
		'template_label'      => $template_label,
		'template_options'    => $t_options_c,
		'template_images'     => $t_images_c,

		'sticker_label'       => $sticker_label,
		'wunder_label'        => $wunderenable_label,
		'wunder_checked'      => $wunderenable_checked,
		'sticker_src_label'   => $sticker_src_label,
		'sticker_options'     => $ss_options_c,
		'tenable_label'       => $tenable_label,
		'title_checked'       => $title_checked,
		'tsize_label'         => $tsize_label,
		'tsize_options'       => $ts_options_c,
		'tmetar_label'        => $tmetar_label,
		'tmetar_options'      => $tm_options_c,
		'fpenable_label'      => $fpenable_label,
		'fpage_checked'       => $fpage_checked,
		'fpsize_label'        => $fpsize_label,
		'fpsize_options'      => $fps_options_c,
		'fpmetar_label'       => $fpmetar_label,
		'fpmetar_options'     => $fpm_options_c,

		'links_label'         => $links_label,
		'lenable_label'       => $lenable_label,
		'links_checked'       => $links_checked,
		'lavail_label'        => $lavail_label,
		'link_options'        => $l_options_c,
		'city_label'          => $city_label,
		'city'                => $weather_user['city'],
		'country_label'       => $country_label,
		'country'             => $weather_user['country'],
		'gstation_label'      => $gstation_label,
		'gstation'            => $weather_user['global_station'],
		'state_label'         => $state_label,
		'state_options'       => $s_options_c,

		'remote_label'        => $remote_label,
		'fenable_label'       => $fenable_label,
		'forecast_checked'    => $forecast_checked,
		'oenable_label'       => $oenable_label,
		'observation_checked' => $observation_checked,
		'mavail_label'        => $mavail_label,
		'metar_options'       => $m_options_c,
		'th_bg'               => $GLOBALS['phpgw_info']['theme']['th_bg'],
		'th_text'             => $GLOBALS['phpgw_info']['theme']['th_text']));

		$prefs_tpl->parse('message_part', 'message');
		$message_c = $prefs_tpl->get('message_part');

		$prefs_tpl->parse('body_part', 'prefs');
		$body_c = $prefs_tpl->get('body_part');

		/**************************************************************************
		* pull it all together
		*************************************************************************/
		$body_tpl =  CreateObject('phpgwapi.Template',
		$GLOBALS['phpgw']->common->get_tpl_dir('weather'));
		$body_tpl->set_unknowns('remove');
		$body_tpl->set_file('body', 'prefs.common.tpl');
		$body_tpl->set_var(array(
			'preferences_message' => $message_c,
			'preferences_body'    => $body_c));
		$body_tpl->parse('BODY', 'body');
		$body_tpl->p('BODY');

		$GLOBALS['phpgw']->common->phpgw_footer();
?>
