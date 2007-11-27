<?php
    /**************************************************************************\
    * phpGroupWare - Daily Comics Global Options                               *
    * http://www.phpgroupware.org                                              *
    * This file written by Sam Wynn <neotexan@wynnsite.com>                    *
    * --------------------------------------------                             *
    *  This program is free software; you can redistribute it and/or modify it *
    *  under the terms of the GNU General Public License as published by the   *
    *  Free Software Foundation; either version 2 of the License, or (at your  *
    *  option) any later version.                                              *
    \**************************************************************************/

    /* $Id: admin_options.php 17302 2006-10-02 09:19:33Z skwashd $ */

	$GLOBALS['phpgw_info']['flags']	= array(
						'currentapp'	=> 'comic',
						'admin_header'	=> TRUE
						);
	
	include('../header.inc.php');

	if ( !isset($GLOBALS['phpgw_info']['user']['apps']['admin']) )
	{
		$GLOBALS['phpgw']->redirect_link('/home.php');
	}
	
	// Incoming Form vals
	$image_source		= isset($_POST['image_source']) ? (int) $_POST['image_source'] : 0;
	$censor_level		= isset($_POST['censor_level']) ? (int) $_POST['censor_level'] : 0;
	$override_enabled	= isset($_POST['override_enabled']) ? (int) $_POST['override_enabled'] : 0;
	$remote_enabled		= isset($_POST['remote_enabled']) ? (int) $_POST['remote_enabled'] : 0; 
	$filesize			= isset($_POST['filesize']) ? (int) $_POST['filesize'] : 0;
	
	$title             = lang("Daily Comics Global Options");
	$imgsrc_label      = lang("Image Source");
	$remote_label      = lang("Remote (Parse/Snarf) Enabled");
	$censor_label      = lang("Censorship Level");
	$filesize_label    = lang("Max File size");
	$override_label    = lang("Censorship Override Enabled");
	$action_label      = lang("Submit");
	$reset_label       = lang("Reset");
	$done_label        = lang("Done");
	$actionurl         = $GLOBALS['phpgw']->link('/comic/admin_options.php');
	$doneurl           = $GLOBALS['phpgw']->link('/admin/index.php') . '#comic';
	$message           = "";

	if ( isset($_POST['submit']) && $_POST['submit'] )
	{
		$sql 	= 'UPDATE phpgw_comic_admin SET'
			. ' admin_imgsrc=' . $image_source . ','
			. ' admin_rmtenabled=' . $remote_enabled . ','
			. ' admin_censorlvl=' . $censor_level . ','
			. ' admin_coverride=' . $override_enabled . ','
			. ' admin_filesize=' . $filesize;
		
		$GLOBALS['phpgw']->db->lock('phpgw_comic_admin');
		$GLOBALS['phpgw']->db->query($sql, __LINE__, __FILE__);
		$GLOBALS['phpgw']->db->unlock();

		$message = lang("Global Options Updated");

	}

	comic_admin_data($image_source,
		     $censor_level,
		     $override_enabled,
		     $remote_enabled,
		     $filesize);

	$remote_checked = "";
	if ($remote_enabled == 1)
	{
		$remote_checked = "checked";
	}

	$override_checked = "";
	if ($override_enabled == 1)
	{
		$override_checked = "checked";
	}

	$options_tpl = CreateObject('phpgwapi.Template',$GLOBALS['phpgw']->common->get_tpl_dir('comic'));
	$options_tpl->set_unknowns("remove");
	$options_tpl->set_file(
	array(message   => "message.common.tpl",
	      options   => "admin.options.tpl",
	      coptions  => "option.common.tpl"));

	for ($loop = 0; $loop < count($g_censor_level); ++$loop)
	{
		$selected = "";
		
		if ($censor_level == $loop)
		{
			$selected = "selected";
		}

		$options_tpl->set_var(array(
						OPTION_VALUE    => $loop,
						OPTION_SELECTED => $selected,
						OPTION_NAME     => $g_censor_level[$loop]
						)
					);
					
		$options_tpl->parse(option_list, "coptions", true);
	}
	$censor_level_c = $options_tpl->get("option_list");

	for ($loop = 0; $loop < count($g_image_source); $loop++)
	{
		$selected = "";
		
		if ($image_source == $loop)
		{
			$selected = 'selected';
		}
		
		$options_tpl->set_var(array(
						OPTION_VALUE    => $loop,
						OPTION_SELECTED => $selected,
						OPTION_NAME     => $g_image_source[$loop]
						)
					);
		$options_tpl->parse(option_list2, "coptions", TRUE);
	}
	$image_source_c = $options_tpl->get("option_list2");
	
	$options_tpl->set_var(array(
				'messagename'		=> $message,
				'title'			=> $title,
				'action_url'		=> $actionurl,
				'action_label'		=> $action_label,
				'done_url'		=> $doneurl,
				'done_label'		=> $done_label,
				'reset_label'		=> $reset_label,
				'filesize_label'	=> $filesize_label,
				'filesize'		=> $filesize,
				'override_label'	=> $override_label,
				'override_checked'	=> $override_checked,
				'remote_label'		=> $remote_label,
				'remote_checked'	=> $remote_checked,
				'censor_label'		=> $censor_label,
				'censor_options'	=> $censor_level_c,
				'imgsrc_label'		=> $imgsrc_label,
				'image_options'		=> $image_source_c
				)
			);

	$options_tpl->parse(message_part, "message");
	$message_c = $options_tpl->get("message_part");
	
	$options_tpl->parse(body_part, "options");
	$body_c = $options_tpl->get("body_part");
	
	/**************************************************************************
	* pull it all together
	*************************************************************************/
	$body_tpl = $GLOBALS['phpgw']->template;
	$body_tpl->set_unknowns("remove");
	$body_tpl->set_file(body, "admin.common.tpl");
	$body_tpl->set_var(array(
				'admin_message'	=> $message_c,
				'admin_body'	=> $body_c
				)
			);
			     
	$body_tpl->parse('BODY', 'body');
	$body_tpl->p('BODY');

	$GLOBALS['phpgw']->common->phpgw_footer();
?>
