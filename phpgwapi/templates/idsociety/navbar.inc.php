<?php
	/**
	* Template navigation bar
	* @copyright Copyright (C) 2003-2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage gui
	* @version $Id$
	*/


	/**
	* Parse navigation var
	*
	* @param boolean $force
	* @ignore
	*/
	function parse_navbar($force = False)
	{
		$tpl = CreateObject('phpgwapi.template',PHPGW_TEMPLATE_DIR, 'remove');

		$tpl->set_file('navbar', 'navbar.tpl');
		$tpl->set_block('navbar','app', 'apps');
		$tpl->set_block('navbar','preferences','preferences_icon');

		$exclude = array('home', 'preferences', 'about', 'logout');
		$navbar = execMethod('phpgwapi.menu.get', 'navbar');
		prepare_navbar($navbar);
		foreach ( $navbar as $app => $app_data )
		{
			if ( in_array($app, $exclude) )
			{
				continue;
			}

			$tpl->set_var(array
			(
				'text'	=> strtoupper($app_data['text']),
				'url'	=> $app_data['url']
			));
			$tpl->parse('apps', 'app', true);
		}

		$var['home_link'] = $navbar['home']['url'];
		$var['preferences_link'] = $navbar['preferences']['url'];
		$var['logout_link'] = $navbar['logout']['url'];
		$var['help_link'] = $navbar['about']['url'];

		if ($GLOBALS['phpgw_info']['flags']['currentapp'] != 'home')
		{
			$var['welcome_img'] = $GLOBALS['phpgw']->common->image('phpgwapi','welcome2');
			$var['welcome_img_hover'] = $GLOBALS['phpgw']->common->image_on('phpgwapi','welcome2','_over');
		}
		else
		{
			$var['welcome_img'] = $GLOBALS['phpgw']->common->image_on('phpgwapi','welcome2','_over');
			$var['welcome_img_hover'] = $GLOBALS['phpgw']->common->image('phpgwapi','welcome2');
		}

		if ($GLOBALS['phpgw_info']['flags']['currentapp'] != 'preferences')
		{
			$var['preferences_img'] = $GLOBALS['phpgw']->common->image('phpgwapi','preferences2');
			$var['preferences_img_hover'] = $GLOBALS['phpgw']->common->image_on('phpgwapi','preferences2','_over');
		}
		else
		{
			$var['preferences_img'] = $GLOBALS['phpgw']->common->image_on('phpgwapi','preferences2','_over');
			$var['preferences_img_hover'] = $GLOBALS['phpgw']->common->image('phpgwapi','preferences2');
		}

		$var['logout_img'] = $GLOBALS['phpgw']->common->image('phpgwapi','log_out2');
		$var['logout_img_hover'] = $GLOBALS['phpgw']->common->image_on('phpgwapi','log_out2','_over');

		if ($GLOBALS['phpgw_info']['flags']['currentapp'] != 'about')
		{
			$var['about_img'] = $GLOBALS['phpgw']->common->image('phpgwapi','question_mark2');
			$var['about_img_hover'] = $GLOBALS['phpgw']->common->image_on('phpgwapi','question_mark2','_over');
		}
		else
		{
			$var['about_img'] = $GLOBALS['phpgw']->common->image_on('phpgwapi','question_mark2','_over');
			$var['about_img_hover'] = $GLOBALS['phpgw']->common->image('phpgwapi','question_mark2');
		}

		$var['content_spacer_middle_img'] = $GLOBALS['phpgw']->common->image('phpgwapi','content_spacer_middle');
		$var['em_img'] = $GLOBALS['phpgw']->common->image('phpgwapi','em');
		$var['logo_img'] = $GLOBALS['phpgw']->common->image('phpgwapi','logo');
		$var['top_spacer_middle_img'] = $GLOBALS['phpgw']->common->image('phpgwapi','top_spacer_middle');
		$var['nav_bar_left_spacer_img'] = $GLOBALS['phpgw']->common->image('phpgwapi','nav_bar_left_spacer');
		$var['nav_bar_left_top_bg_img'] = $GLOBALS['phpgw']->common->image('phpgwapi','nav_bar_left_top_bg');

		// "powered_by_color" and "_size" are is also used by number of current users thing
		$var['powered_by_size'] = '2';
		$var['powered_by_color'] = '#ffffff';
		if ($GLOBALS['phpgw_info']['server']['showpoweredbyon'] == 'top')
		{
			$var['powered_by'] = lang('Powered by phpGroupWare version %1',$GLOBALS['phpgw_info']['server']['versions']['phpgwapi']);
			$tpl->set_var($var);
		}
		else
		{
			$var['powered_by'] = '';
			$tpl->set_var($var);
		}

		if (isset($GLOBALS['phpgw_info']['navbar']['admin']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['show_currentusers'])
		{
			$var['current_users'] = '<a href="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicurrentsessions.list_sessions'))
			 	. '">&nbsp;' . lang('Current users') . ': ' . $GLOBALS['phpgw']->session->total() . '</a>';
			$tpl->set_var($var);
		}
		else
		{
			$var['current_users'] = '';
			$tpl->set_var($var);
		}

		$var['user_info_name'] = $GLOBALS['phpgw']->common->display_fullname();
		$now = time();
		$var['user_info_date'] =
				  lang($GLOBALS['phpgw']->common->show_date($now,'l')) . ' '
				. $GLOBALS['phpgw']->common->show_date($now,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
		$var['user_info'] = $var['user_info_name'] .' - ' .$var['user_info_date'];
		$var['user_info_size'] = '2';
		$var['user_info_color'] = '#000000';

		// Maybe we should create a common function in the phpgw_accounts_shared.inc.php file
		// to get rid of duplicate code.
		if ($GLOBALS['phpgw_info']['user']['lastpasswd_change'] == 0)
		{
			$api_messages = lang('You are required to change your password during your first login')
				. '<br> Click this image on the navbar: <img src="'
				. $GLOBALS['phpgw']->common->image('preferences','navbar.gif').'">';
		}
		elseif ($GLOBALS['phpgw_info']['user']['lastpasswd_change'] < time() - (86400*30))
		{
			$api_messages = lang('it has been more then %1 days since you changed your password',30);
		}
 
		// This is gonna change
		if (isset($cd))
		{
			$var['messages'] = $api_messages . '<br>' . checkcode($cd);
		}

		$var['th_bg'] = $GLOBALS['phpgw_info']['theme']['th_bg'];
		if (isset($GLOBALS['phpgw_info']['flags']['app_header']))
		{
			$var['current_app_header'] = $GLOBALS['phpgw_info']['flags']['app_header'];
		}
		else
		{
			$tpl->set_block('navbar','app_header','app_header');
			$var['app_header'] = '<br>';
		}

		$tpl->set_var($var);
		// check if user is allowed to change his prefs
		if ($GLOBALS['phpgw_info']['user']['apps']['preferences'])
		{
			$tpl->parse('preferences_icon','preferences');
		}
		else
		{
			$tpl->set_var('preferences_icon','');
		}
		$tpl->pfp('out','navbar');
		// If the application has a header include, we now include it
		if ( !isset($GLOBALS['phpgw_info']['flags']['noappheader']) && $menuaction = phpgw::get_var('menuaction', 'string', 'GET') )
		{
			list($app,$class,$method) = explode('.', $menuaction);
			if (is_array($GLOBALS[$class]->public_functions) && $GLOBALS[$class]->public_functions['header'])
			{
				$GLOBALS[$class]->header();
			}
		}
		$GLOBALS['phpgw']->hooks->process('after_navbar');
		return;
	}


	/**
	* Parse navigation bar end
	* @ignore
	*/
	function parse_navbar_end()
	{
		$tpl = CreateObject('phpgwapi.template',PHPGW_TEMPLATE_DIR);
		$tpl->set_unknowns('remove');

		$tpl->set_file(array('footer' => 'footer.tpl'));
		$tpl->set_block('footer','B_powered_bottom','V_powered_bottom');

		$var = array
		(
			'powered'	=> lang('Powered by phpGroupWare version %1', $GLOBALS['phpgw_info']['server']['versions']['phpgwapi']),
			'img_root'	=> PHPGW_IMAGES_DIR,
		);
		$tpl->set_var($var);
		$tpl->parse('V_powered_bottom','B_powered_bottom');

		$GLOBALS['phpgw']->hooks->process('navbar_end');
		$tpl->pfp('out','footer');
	}


	/**
	* Callback for usort($navbar)
	*
	* @param array $item1 the first item to compare
	* @param array $item2 the second item to compare
	* @return int result of comparision
	*/
	function sort_navbar($item1, $item2)
	{
		$a =& $item1['order'];
		$b =& $item2['order'];

		if ($a == $b)
		{
			return strcmp($item1['text'], $item2['text']);
		}
		return ($a < $b) ? -1 : 1;
	}

	/**
	* Organise the navbar properly
	*
	* @param array $navbar the navbar items
	* @return array the organised navbar
	*/
	function prepare_navbar(&$navbar)
	{
		uasort($navbar, 'sort_navbar');
	}
