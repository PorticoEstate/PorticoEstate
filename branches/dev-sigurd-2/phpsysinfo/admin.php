<?php
	/**************************************************************************\
	* phpGroupWare - phpSysInfo Administration                                 *
	* http://www.phpgroupware.org                                              *
	* Written by Joseph Engo <jengo@phpgroupware.org>                          *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	$GLOBALS['phpgw_info']['flags'] = array
					(
						'admin_only'              => True,
						'currentapp'              => 'admin', //better for security
						'enable_nextmatchs_class' => True
					);
	include('../header.inc.php');

	$GLOBALS['phpgw']->template->set_root($GLOBALS['phpgw']->common->get_tpl_dir('phpsysinfo'));
	$GLOBALS['phpgw']->template->set_file(array
						(
							'admin' => 'admin.tpl'
						));
	
	if ( !isset($GLOBALS['phpgw']->config) || !is_object($GLOBALS['phpgw']->config) )
	{
		$GLOBALS['phpgw']->config = createObject('phpgwapi.config', 'phpsysinfo');
	}
	
	$GLOBALS['phpgw']->config->appname = 'phpsysinfo';
	$GLOBALS['phpgw']->config->read_repository();
	$config =& $GLOBALS['phpgw']->config->config_data;

	if ( !isset($config['theme']) )
	{
		$config['theme'] = $GLOBALS['phpgw_info']['user']['preferences']['common']['template_set'];
		$GLOBALS['phpgw']->config->save_repository();
	}

	if ( isset($_POST['submit']) && $_POST['submit'] 
		&& isset($_POST['theme']) && $_POST['theme'] )
	{
		$config['theme'] = $_POST['theme'];
		$GLOBALS['phpgw']->config->save_repository();
	}

	$GLOBALS['phpgw']->template->set_var('title',lang('phpSysInfo Theme Selection'));
	$GLOBALS['phpgw']->template->set_var('save_url',$GLOBALS['phpgw']->link('/phpsysinfo/admin.php'));
	$GLOBALS['phpgw']->template->set_var('lang_theme',lang('Theme'));
	$GLOBALS['phpgw']->template->set_var('lang_save',lang('Save'));


	$themes = get_themes();
	if ( is_array($themes) && count($themes) )
	{
		$selection = "<select name=\"theme\">\n";
		foreach ($themes as $theme) 
		{
			$selected = '';
			if ($theme == $config['theme'])
			{
				$selected = "selected=\"selected\"";
			}
			$selection .= "<option $selected value=\"$theme\">$theme</option>\n";
		}
		$selection .= "</select>\n";
		$GLOBALS['phpgw']->template->set_var('select_box', $selection);
	}
	else
	{
		$GLOBALS['phpgw']->template->set_var('select_box', lang('unable to load list of themes') );
	}

	$GLOBALS['phpgw']->template->pfp('out','admin');

	$GLOBALS['phpgw']->common->phpgw_footer();

	function get_themes()
	{
		$themes = array();

		$dir = $GLOBALS['phpgw']->common->get_app_dir('phpsysinfo') . "/templates";
		
		$d = dir($dir);
		if ( !is_object($d) )
		{
			return $themes;
		}

		while ( false !== ($f = $d->read()) ) 
		{
			if ( $f != '.' && $f != '..' && strtoupper($f) != 'CVS' && is_dir("{$dir}/{$f}") )
			{
				$themes[] = $f;
			}
		}
		$d->close();

		sort($themes);

		return $themes;
	}
?>
