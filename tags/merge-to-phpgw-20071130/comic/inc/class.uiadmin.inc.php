<?php

/*************************************************************************\
* Daily Comics (phpGroupWare application)                                 *
* http://www.phpgroupware.org                                             *
* This file is written by: Sam Wynn <neotexan@wynnsite.com>               *
*                          Rick Bakker <r.bakker@linvision.com>           *
* --------------------------------------------                            *
* This program is free software; you can redistribute it and/or modify it *
* under the terms of the GNU General Public License as published by the   *
* Free Software Foundation; either version 2 of the License, or (at your  *
* option) any later version.                                              *
\*************************************************************************/

/* $Id: class.uiadmin.inc.php 17909 2007-01-24 17:26:17Z Caeies $ */

class uiadmin
{
	var $bo;
	var $functions;

	var $public_functions = array(
		'uiadmin'		=> TRUE,
		'global_options'	=> TRUE,
		'get_form'		=> TRUE
	);

	function uiadmin()
	{
		$this->bo		= CreateObject('comic.boadmin');
		$this->functions	= CreateObject('comic.bofunctions');
	}

	function global_options()
	{
		global $phpgw, $phpgw_info;

		if ($_POST['submit']!='')
		{
			if ($_POST['submit']==lang("Submit"))
			{
				$field = $this->get_form();
				// checks can be added here.
				$field['message'] = $this->bo->update_global_options($field);
			}
			if ($_POST['submit']==lang("Done"))
			{
				header('Location: '.$phpgw->link('/admin/index.php'));
			}
		}
		else
		{
			$field           = $this->bo->admin_global_options_data();
			$field['message'] = '';
		}

		$g_censor_level = $this->functions->select_box('g_censor_level');
		$g_image_source = $this->functions->select_box('g_image_source');

		$field['title']                  = lang("Daily Comics - Global Options");
		$field['image_source_label']     = lang("Image Source");
		$field['remote_enabled_label']   = lang("Remote (Parse/Snarf) Enabled");
		$field['censor_level_label']     = lang("Censorship Level");
		$field['filesize_label']         = lang("Max File size");
		$field['override_enabled_label'] = lang("Censorship Override Enabled");
		$field['submit']                 = lang("Submit");
		$field['reset']                  = lang("Reset");
		$field['done']                   = lang("Done");
		$field['action_url']             = $phpgw->link('/index.php', array('menuaction' => 'comic.uiadmin.global_options'));

		$phpgw->common->phpgw_header();
		// echo parse_navbar();
		print(parse_navbar());

		if ($field['remote_enabled'] == 1)
		{
			$field['remote_enabled'] = "checked";
		}
		else
		{
			$field['remote_enabled'] = '';
		}

		if ($field['override_enabled'] == 1)
		{
			$field['override_enabled'] = "checked";
		}
		else
		{
			$field['override_enabled'] = '';
		}

		$options_tpl = CreateObject('phpgwapi.Template',$phpgw->common->get_tpl_dir('comic'));
		$options_tpl->set_unknowns("remove");
		$options_tpl->set_file(array(coptions  => 'option.common.tpl'));

		for ($loop = 0; $loop < count($g_censor_level); $loop++)
		{
			$selected = '';
			if ($field['censor_level'] == $loop)
			{
				$selected = "selected";
			}
			$options_tpl->set_var(array(OPTION_VALUE    => $loop,
				OPTION_SELECTED => $selected,
				OPTION_NAME     => $g_censor_level[$loop]));
			$options_tpl->parse(option_list, "coptions", TRUE);
		}
		$field['censor_level_options'] = $options_tpl->get("option_list");

		for ($loop = 0; $loop < count($g_image_source); $loop++)
		{
			$selected = '';
			if ($field['image_source'] == $loop)
			{
				$selected = "selected";
			}
			$options_tpl->set_var(array(OPTION_VALUE    => $loop,
				OPTION_SELECTED => $selected,
				OPTION_NAME     => $g_image_source[$loop]));
			$options_tpl->parse(option_list2, "coptions", TRUE);
		}
		$field['image_source_options'] = $options_tpl->get("option_list2");

		$phpgw->template->set_file(array('main'=>'admin_global_options.tpl'));
		$phpgw->template->set_var(array(
			'action_url'			=> $field['action_url'],
			'title_color'			=> $phpgw_info['theme']['th_bg'],
			'title'				=> $field['title'],
			'row_1_color'			=> $this->functions->row_color(),
			'message'			=> $field['message'],
			'censor_level_color'		=> $this->functions->row_color(),
			'censor_level_label'		=> $field['censor_level_label'],
			'censor_level_options'		=> $field['censor_level_options'],
			'override_enabled_color' 	=> $this->functions->row_color(),
			'override_enabled_label'	=> $field['override_enabled_label'],
			'override_enabled'		=> $field['override_enabled'],
			'image_source_color'		=> $this->functions->row_color(),
			'image_source_label'		=> $field['image_source_label'],
			'image_source_options'		=> $field['image_source_options'],
			'remote_enabled_color'		=> $this->functions->row_color(),
			'remote_enabled_label'		=> $field['remote_enabled_label'],
			'remote_enabled'		=> $field['remote_enabled'],
			'filesize_color'		=> $this->functions->row_color(),
			'filesize_label'		=> $field['filesize_label'],
			'filesize'			=> $field['filesize'],
			'row_2_color'			=> $this->functions->row_color(),
			'submit'			=> $field['submit'],
			'reset'				=> $field['reset'],
			'done'				=> $field['done']));
		$phpgw->template->parse('out', 'main', TRUE);
		$phpgw->template->p('out');
	}

	function get_form()
	{
		$field['censor_level']		= $_POST['censor_level'];
		$field['override_enabled']	= $_POST['override_enabled'];
		$field['image_source']		= $_POST['image_source'];
		$field['remote_enabled']	= $_POST['remote_enabled'];
		$field['filesize']		= $_POST['filesize'];

		return ($field);
	}
}

?>
