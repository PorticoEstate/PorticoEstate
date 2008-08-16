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

  function parse_navbar($force = False)
  {
		$tpl = createobject('phpgwapi.Template',PHPGW_TEMPLATE_DIR);
		$tpl->set_unknowns('remove');

		$tpl->set_file(
			array(
				'navbar'	=> 'navbar.tpl',
				'navbar_app'	=> 'navbar_app.tpl'
			)
		);

		$tpl->set_block('navbar', 'app', 'apps');
		$tpl->set_block('navbar', 'prefs', 'prefs_block');

		$target = '';
		if ( isset($GLOBALS['phpgw_info']['flags']['navbar_target']) && $GLOBALS['phpgw_info']['flags']['navbar_target'] )
		{
			$target = ' target="' . $GLOBALS['phpgw_info']['flags']['navbar_target'] . '"';
		}

		$prefs_ok = False;
		foreach($GLOBALS['phpgw_info']['navbar'] as $app => $app_data)
		{
			if($app == 'preferences')
			{
				$tpl->set_var(array
				(
						'prefs_url'	=> $app_data['url'],
						'lang_prefs'	=> $app_data['title']
				));

				$tpl->parse('prefs_block', 'prefs');
				$prefs_ok = True;
				continue;
			}

			if($app == 'logout' || $app == 'about')
			{
				continue;
			}

			$app_data['target'] = $target;
			$tpl->set_var($app_data);
			$tpl->parse('apps', 'app', True);
		}

		if( !$prefs_ok )
		{
			$tpl->set_var('prefs', '');
		}
		
		$var = array();
		$var['lang_applications'] = lang('applications');
		$var['img_base_url'] = $GLOBALS['phpgw_info']['server']['webserver_url'] . '/phpgwapi/templates/desktop/images/';
		$var['lang_logout'] = lang('logout');
		$var['logout_url'] = $GLOBALS['phpgw_info']['navbar']['logout']['url'];
		$var['lang_about'] = lang('about');
		$var['about_url'] = $GLOBALS['phpgw_info']['navbar']['about']['url'];
		$var['cur_app_title'] = $GLOBALS['phpgw_info']['navbar'][$GLOBALS['phpgw_info']['flags']['currentapp']]['title'];
		$var['cur_app_icon'] = $GLOBALS['phpgw_info']['navbar'][$GLOBALS['phpgw_info']['flags']['currentapp']]['icon'];

		if ( isset($GLOBALS['phpgw_info']['server']['showpoweredbyon']) && $GLOBALS['phpgw_info']['server']['showpoweredbyon'] == 'top')
		{
			$var['powered_by'] = lang('Powered by phpGroupWare version %1',$GLOBALS['phpgw_info']['server']['versions']['phpgwapi']);
		}
		if (isset($GLOBALS['phpgw_info']['navbar']['admin']) 
			&& isset($GLOBALS['phpgw_info']['user']['preferences']['common']['show_currentusers']) 
			&& $GLOBALS['phpgw_info']['user']['preferences']['common']['show_currentusers'] )
		{
			$var['current_users'] = '<a href="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicurrentsessions.list_sessions'))
				. '">&nbsp;' . lang('Current users') . ': ' . $GLOBALS['phpgw']->session->total() . '</a>';
		}
		$now = time();
		$var['user_info'] = $GLOBALS['phpgw']->common->display_fullname() . ' - '
				. lang($GLOBALS['phpgw']->common->show_date($now,'l')) . ' '
				. $GLOBALS['phpgw']->common->show_date($now,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);

		// Maybe we should create a common function in the phpgw_accounts_shared.inc.php file
		// to get rid of duplicate code.
		if ($GLOBALS['phpgw_info']['user']['lastpasswd_change'] == 0)
		{
			$api_messages = lang('You are required to change your password during your first login')
				. '<br /> Click this image on the navbar: <img src="'
				. $GLOBALS['phpgw']->common->image('preferences','navbar.gif').'">';
		}
		elseif ($GLOBALS['phpgw_info']['user']['lastpasswd_change'] < time() - (86400*30))
		{
			$api_messages = lang('it has been more then %1 days since you changed your password',30);
		}
 
		// This is gonna change
		if (isset($cd))
		{
			$var['messages'] = $api_messages . '<br />' . checkcode($cd);
		}

		if (isset($GLOBALS['phpgw_info']['flags']['app_header']))
		{
			$var['current_app_header'] = $GLOBALS['phpgw_info']['flags']['app_header'];
		}
		else
		{
			$tpl->set_block('navbar','app_header','app_header');
			$var['app_header'] = '';
		}

		$tpl->set_var($var);
		$tpl->pfp('out','navbar');
		// If the application has a header include, we now include it
		if( (!(isset($GLOBALS['phpgw_info']['flags']['noappheader']) && $GLOBALS['phpgw_info']['flags']['noappheader']) ) && isset($_GET['menuaction']) )
		{
			list($app,$class,$method) = explode('.',$_GET['menuaction']);
			if (is_array($GLOBALS[$class]->public_functions) && isset($GLOBALS[$class]->public_functions['header']) && $GLOBALS[$class]->public_functions['header'] )
			{
				$GLOBALS[$class]->header();
			}
		}
		$GLOBALS['phpgw']->hooks->process('after_navbar');
		return;
	}

	function parse_navbar_end()
	{
		$tpl = createobject('phpgwapi.Template',PHPGW_TEMPLATE_DIR);
		$tpl->set_unknowns('remove');

		$tpl->set_file(
			array(
				'footer' => 'footer.tpl'
			)
		);
		$var = array();
		$var['powered_by'] = lang('Powered by phpGroupWare version %1',$GLOBALS['phpgw_info']['server']['versions']['phpgwapi']);
		if (isset($GLOBALS['phpgw_info']['navbar']['admin']) 
			&& isset($GLOBALS['phpgw_info']['user']['preferences']['common']['show_currentusers'])
			&& $GLOBALS['phpgw_info']['user']['preferences']['common']['show_currentusers'])
		{
			$var['current_users'] = '<a href="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicurrentsessions.list_sessions'))
				. '">' . lang('Current users') . ': ' . $GLOBALS['phpgw']->session->total() . '</a>';
		}
		$now = time();
		$var['user_info'] = $GLOBALS['phpgw']->common->display_fullname() . ' - '
				. lang($GLOBALS['phpgw']->common->show_date($now,'l')) . ' '
				. $GLOBALS['phpgw']->common->show_date($now, $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
		$tpl->set_var($var);
		$GLOBALS['phpgw']->hooks->process('navbar_end');
		$tpl->pfp('out','footer');
	}
