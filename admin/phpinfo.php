<?php
	/**************************************************************************\
	* phpGroupWare - administration                                            *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/
	/* $Id$ */

	$GLOBALS['phpgw_info']['flags'] = array
	(
		'currentapp'		=> 'admin',
		'menu_selection'	=> 'admin::admin::phpinfo'
	);

	if ( isset($_GET['noheader']) && $_GET['noheader'] )
	{
		$GLOBALS['phpgw_info']['flags'] = array
		(
			'nofooter'			=> true,
			'noframework'		=> true,
			'noheader'			=> true,
			'nonavbar'			=> true,
			'currentapp'		=> 'admin',
			'menu_selection'	=> 'admin::admin::phpinfo'
		);
	}

   	include_once('../header.inc.php');
  
  	if ( phpgw::get_var('noheader', 'bool', 'GET') )
  	{
  		$close = lang('close window');
  
  		echo <<<HTML
  			<div style="text-align: center;">
  				<a href="javascript:window.close();">{$close}</a>
  			</div>
  
HTML;
  	}
  
  	if ( function_exists('phpinfo') )
  	{
		phpinfo();
  	}
  	else
  	{
  		$error = lang('phpinfo is not available on this system!');
  		echo <<<HTML
  			<div class="error"><h1>$error</h1><div>
  
HTML;
 	}
