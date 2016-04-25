<?php
	/**************************************************************************\
	* phpGroupWare - Admin                                                     *
	* http://www.phpgroupware.org                                              *
	* This application written by Miles Lott <milosch@phpgroupware.org>        *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	/* Check currentapp and API upgrade status */

	if ($GLOBALS['phpgw_info']['flags']['currentapp'] != 'home'
		&& $GLOBALS['phpgw_info']['flags']['currentapp'] != 'welcome'
		&& $GLOBALS['phpgw_info']['flags']['currentapp'] != 'about'
//		&& (isset($GLOBALS['phpgw_info']['server']['checkappversions']) &&	$GLOBALS['phpgw_info']['server']['checkappversions'])
		)
	{
		if ((isset($GLOBALS['phpgw_info']['user']['apps']['admin']) &&
			$GLOBALS['phpgw_info']['user']['apps']['admin']) ||
			$GLOBALS['phpgw_info']['server']['checkappversions'] == 'All')
		{
			$require_upgrade = false;
			$_current = array();
			$app_name = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$GLOBALS['phpgw']->db->query("SELECT app_name,app_version FROM phpgw_applications WHERE app_name='$app_name' OR app_name='phpgwapi'",__LINE__,__FILE__);
			$app_info = array();
			while($GLOBALS['phpgw']->db->next_record())
			{
				$app_info[] = array
				(
					'db_version'	=> $GLOBALS['phpgw']->db->f('app_version'),
					'app_name'		=> $GLOBALS['phpgw']->db->f('app_name')
				); 
			}
			
			foreach($app_info as $app)
			{
				$_db_version  = $app['db_version'];
				$app_name	  = $app['app_name'];
				$_versionfile = $GLOBALS['phpgw']->common->get_app_dir($app_name) . '/setup/setup.inc.php';
				if(file_exists($_versionfile))
				{
					include($_versionfile);
					/* echo '<br>' . $_versionfile . ','; */
					$_file_version = $setup_info[$app_name]['version'];
					$_app_title    = str_replace('- ','-',ucwords(str_replace('_','- ',$setup_info[$app_name]['name'])));
					unset($setup_info);

					/* echo '<br>' . $app_name . ',' . $_db_version . ',' . $_file_version; */
					$test = $GLOBALS['phpgw']->common->cmp_version_long($_db_version,$_file_version);
					if($test == '')
					{
						$_current[$app_name] = True;
						if($app_name == 'phpgwapi')
						{
							$api_str = lang('The API is current') . '::OK';
						}
					}
					else
					{
						if($app_name == 'phpgwapi')
						{
							$api_str = lang('The API requires an upgrade');
							$require_upgrade = true;
						}
					}
					unset($test);
					unset($_file_version);
					unset($_app_title);
				}
				unset($_db_version);
				unset($_versionfile);
			}
			if(!isset($_current[$GLOBALS['phpgw_info']['flags']['currentapp']]))
			{
				$app_str  = '<br>' . lang('This application requires an upgrade') . ": {$GLOBALS['phpgw_info']['flags']['currentapp']}\n";
				$app_str .= '<br>' . lang('Please run setup to become current') . '.' . "\n";
				$require_upgrade = true;
			}
			else
			{
	//			$app_str  =  '<br>' . lang('This application is current') . "\n";
			}
		
			if($require_upgrade)
			{
				echo '<div class="error">';
				echo $api_str;
				echo $app_str;
				echo '</div>';
			}
		}
	}
	
/*
	if( phpgw::get_var('phpgw_return_as') != 'json' && $receipt = phpgwapi_cache::session_get('phpgwapi', 'phpgw_messages'))
	{
		phpgwapi_cache::session_clear('phpgwapi', 'phpgw_messages');
		$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);
		$msgbox_data = $GLOBALS['phpgw']->common->msgbox($msgbox_data);
		foreach($msgbox_data as & $message)
		{
			echo "<div class='{$message['msgbox_class']}'>";
			echo $message['msgbox_text'];
			echo '</div>';
		}
	}
*/
