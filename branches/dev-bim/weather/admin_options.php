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

    /* $Id$ */

	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp'   => 'weather',
		'admin_header' => TRUE
	);
	include('../header.inc.php');

	$title             = lang('Weather Center Global Options');

	$gdlib_label       = lang('GD Library Enabled');
	$imagetype_label   = lang('Image Format');

	$remote_label      = lang('Remote Enabled');
	$filesize_label    = lang('Max File size');
	$imgsrc_label      = lang('Image Source');

	$action_label      = lang('Submit');
	$reset_label       = lang('Reset');
	$done_label        = lang('Done');

	$actionurl         = $GLOBALS['phpgw']->link('/weather/admin_options.php');
	$doneurl           = $GLOBALS['phpgw']->link('/admin/index.php');

	$message           = '';

	if ($submit)
	{
		$message = lang('Global Options Updated');

		$weather_admin['gdlib_enabled']   = $gdlib_enabled;
		$weather_admin['gdtype']          = $gdtype;
		$weather_admin['image_source']    = $image_source;
		$weather_admin['remote_enabled']  = $remote_enabled;
		$weather_admin['filesize']        = $filesize;

		weather_set_admin_data();
	}

	weather_get_admin_data();

	$remote_checked = $g_checked[$weather_admin['remote_enabled']];
	$gdlib_checked  = $g_checked[$weather_admin['gdlib_enabled']];

	image_source_options($weather_admin['image_source'], $image_source_c);
	image_type_options($weather_admin['gdtype'], $image_type_c);

	$options_tpl = CreateObject('phpgwapi.Template',$GLOBALS['phpgw']->common->get_tpl_dir('weather'));
	$options_tpl->set_unknowns('remove');
	$options_tpl->set_file(array(
		'message' => 'message.common.tpl',
		'options' => 'admin.options.tpl'
	));
	$options_tpl->set_var(array(
		'messagename'     => $message,
		'title'           => $title,
		'action_url'      => $actionurl,
		'action_label'    => $action_label,
		'done_url'        => $doneurl,
		'done_label'      => $done_label,
		'reset_label'     => $reset_label,
		'remote_label'    => $remote_label,
		'remote_checked'  => $remote_checked,
		'gdlib_label'     => $gdlib_label,
		'gdlib_checked'   => $gdlib_checked,
		'imagetype_label' => $imagetype_label,
		'image_options'   => $image_type_c,
		'imgsrc_label'    => $imgsrc_label,
		'imgsrc_options'  => $image_source_c,
		'filesize_label'  => $filesize_label,
		'filesize'        => $weather_admin['filesize']
	));

	$options_tpl->parse('message_part', 'message');
	$message_c = $options_tpl->get('message_part');

	$options_tpl->parse('body_part', 'options');
	$body_c = $options_tpl->get('body_part');

	/**************************************************************************
	* pull it all together
	*************************************************************************/
	$body_tpl = CreateObject('phpgwapi.Template',$GLOBALS['phpgw']->common->get_tpl_dir('weather'));
	$body_tpl->set_unknowns('remove');
	$body_tpl->set_file('body', 'admin.common.tpl');
	$body_tpl->set_var(array(
		'admin_message' => $message_c,
		'admin_body'    => $body_c
	));
	$body_tpl->parse('BODY', 'body');
	$body_tpl->p('BODY');

	$GLOBALS['phpgw']->common->phpgw_footer();
?>
