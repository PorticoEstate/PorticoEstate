<?php
	/*	 * ************************************************************************\
	 * phpGroupWare - administration                                            *
	 * http://www.phpgroupware.org                                              *
	 * --------------------------------------------                             *
	 *  This program is free software; you can redistribute it and/or modify it *
	 *  under the terms of the GNU General Public License as published by the   *
	 *  Free Software Foundation; either version 2 of the License, or (at your  *
	 *  option) any later version.                                              *
	  \************************************************************************* */
	/* $Id$ */

	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp'	 => 'admin',
		'menu_selection' => 'admin::admin::phpinfo'
	);

	if (isset($_GET['noheader']) && $_GET['noheader'])
	{
		$GLOBALS['phpgw_info']['flags'] = array(
			'nofooter'		 => true,
			'noframework'	 => true,
			'noheader'		 => true,
			'nonavbar'		 => true,
			'currentapp'	 => 'admin',
			'menu_selection' => 'admin::admin::phpinfo'
		);
	}

	include_once('../header.inc.php');

	if (phpgw::get_var('noheader', 'bool', 'GET') && !phpgw::get_var('iframe', 'bool', 'GET'))
	{
		$close = lang('close window');

		echo <<<HTML
				<div style="text-align: center;">
					<a href="javascript:window.close();">{$close}</a>
				</div>
HTML;
	}

	if (function_exists('phpinfo'))
	{
		if (phpgw::get_var('get_info', 'bool', 'GET'))
		{
			phpinfo();
		}
		else
		{
			$link = $GLOBALS['phpgw']->link('/admin/phpinfo.php', array('get_info' => true, 'noheader' => true, 'iframe' => true));
			echo <<<HTML

				<script>
					function resizeIframe(obj)
					{
						obj.style.height = obj.contentWindow.document.documentElement.scrollHeight + 'px';
					}
				</script>

				<iframe id="phpinfo" src="{$link}" width="100%" frameborder="0" scrolling="no" onload="resizeIframe(this)" ></iframe>
HTML;
		}
	}
	else
	{
		$error = lang('phpinfo is not available on this system!');
		echo <<<HTML
				<div class="error"><h1>$error</h1><div>
	
HTML;
	}
