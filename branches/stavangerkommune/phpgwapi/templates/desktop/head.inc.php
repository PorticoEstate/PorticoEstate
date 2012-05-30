<?php
  /**************************************************************************\
  * phpGroupWare                                                             *
  * http://www.phpgroupware.org                                              *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id$ */

	if ( !isset($GLOBALS['phpgw_info']['server']['site_title']) )
	{
		$GLOBALS['phpgw_info']['server']['site_title'] = lang('please set a site name in admin &gt; siteconfig');
	}
	
	$tpl = CreateObject('phpgwapi.Template',PHPGW_TEMPLATE_DIR);
	$tpl->set_unknowns('remove');
	$tpl->set_file(array('head' => 'head.tpl'));

	$app = $GLOBALS['phpgw_info']['flags']['currentapp'];
	$app = $app ? ' ['.(isset($GLOBALS['phpgw_info']['apps'][$app]) ? $GLOBALS['phpgw_info']['apps'][$app]['title'] : lang($app)).']':'';

	if(file_exists(PHPGW_SERVER_ROOT . '/phpgwapi/templates/desktop/css/' . $GLOBALS['phpgw_info']['user']['preferences']['common']['theme'] . '.css'))
	{
			$theme_css = $GLOBALS['phpgw_info']['server']['webserver_url'] . '/phpgwapi/templates/desktop/css/'.$GLOBALS['phpgw_info']['user']['preferences']['common']['theme'].'.css';
	}
	else
	{
			$theme_css = $GLOBALS['phpgw_info']['server']['webserver_url'] . '/phpgwapi/templates/desktop/css/desktop.css';
	}

	$GLOBALS['phpgw']->js->set_onload('initPage();');

	$var = Array (
		'img_icon'      => PHPGW_IMAGES_DIR . '/favicon.ico',
		'img_shortcut'  => PHPGW_IMAGES_DIR . '/favicon.ico',
		'website_title' => $GLOBALS['phpgw_info']['server']['site_title'] . $app,
		'theme_css'		=> $theme_css,
		'css'			=> $GLOBALS['phpgw']->common->get_css(),
		'java_script'   => $GLOBALS['phpgw']->common->get_java_script(),
	);
	$tpl->set_var($var);
	$tpl->pfp('out','head');
	unset($tpl);
?>
