<?php
	/**
	* phpGroupWare Idots Template - Navigation Bar
	*
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @author Various Others <unknown>
	* @copyright Copyright (C) 2003-2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgroupware
	* @subpackage phpgwapi
	* @category gui
	* @version $Id$
	*/

	/*
		This program is free software: you can redistribute it and/or modify
		it under the terms of the GNU General Public License as published by
		the Free Software Foundation, either version 2 of the License, or
		(at your option) any later version.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License for more details.

		You should have received a copy of the GNU General Public License
		along with this program.  If not, see <http://www.gnu.org/licenses/>.
	*/

	/**
	* Parse navigation bar
	*
	* @param boolean $force
	* @ignore
	*/
	function parse_navbar($force = false)
	{
		$GLOBALS['phpgw']->template->set_root(PHPGW_TEMPLATE_DIR);

		$GLOBALS['phpgw']->template->set_file('navbar', 'navbar.tpl');
		$GLOBALS['phpgw']->template->set_block('navbar', 'navbar_item', 'navbar_items');
		$GLOBALS['phpgw']->template->set_block('navbar','navbar_header','navbar_header');
		$GLOBALS['phpgw']->template->set_block('navbar','extra_blocks_header','extra_block_header');
		$GLOBALS['phpgw']->template->set_block('navbar','extra_blocks_menu','extra_blocks_menu');
		$GLOBALS['phpgw']->template->set_block('navbar','extra_block_row','extra_block_row');
		$GLOBALS['phpgw']->template->set_block('navbar','extra_blocks_footer','extra_blocks_footer');
		$GLOBALS['phpgw']->template->set_block('navbar','navbar_footer','navbar_footer');

		$var['img_root'] = $GLOBALS['phpgw_info']['server']['webserver_url'] . '/phpgwapi/templates/idots/images';

		$applications = '';
		$items = 0;

		$navbar = execMethod('phpgwapi.menu.get', 'navbar');
		prepare_navbar($navbar);

		$navigation = execMethod('phpgwapi.menu.get', 'navigation');
		$sidecontent = 'sidebox';
		if ( isset($GLOBALS['phpgw_info']['user']['preferences']['common']['sidecontent']) 
			&& $GLOBALS['phpgw_info']['user']['preferences']['common']['sidecontent'] )
		{
			$sidecontent = $GLOBALS['phpgw_info']['user']['preferences']['common']['sidecontent'];
		}

		$excluded = array('home', 'preferences', 'about', 'logout');
		foreach ( $navbar as $app => $app_data )
		{
			if ( !in_array($app, $excluded)
				|| ($sidecontent != 'sidebox' && $sidecontent != 'jsmenu'))
			{
				$item = array
				(
					'app_name'		=> '',
					'alt_img_app'	=> lang($app),
					'img_app'		=> "{$var['img_root']}/noimage_nav.png",
					'url_app'		=> $app_data['url'],
					'app_name'		=> $app_data['text'],
					'img_app'		=> $GLOBALS['phpgw']->common->image($app_data['image'][0], $app_data['image'][1])
				);

				$GLOBALS['phpgw']->template->set_var($item);
				$GLOBALS['phpgw']->template->parse('navbar_items', 'navbar_item', true);
			}
		}
		
		if (isset($GLOBALS['phpgw_info']['flags']['app_header']))
		{
			$var['current_app_title'] = $GLOBALS['phpgw_info']['flags']['app_header'];
		}
		else
		{
			$var['current_app_title'] = lang($GLOBALS['phpgw_info']['flags']['currentapp']);
		}

		if (isset($GLOBALS['phpgw_info']['navbar']['admin']) 
			&& isset($GLOBALS['phpgw_info']['user']['preferences']['common']['show_currentusers'])
			&& $GLOBALS['phpgw_info']['user']['preferences']['common']['show_currentusers'] )
		{
			$var['current_users'] = '<a href="'
			. $GLOBALS['phpgw']->link('/index.php','menuaction=admin.uicurrentsessions.list_sessions') . '">'
			. lang('Current users') . ': ' . $GLOBALS['phpgw']->session->total() . '</a>';
		}
		$now = time();
		$var['user_info'] = '<b>'.$GLOBALS['phpgw']->common->display_fullname() .'</b>'. ' - '
		. lang($GLOBALS['phpgw']->common->show_date($now,'l')) . ' '
		. $GLOBALS['phpgw']->common->show_date($now, $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);

		if ( !isset($GLOBALS['phpgw_info']['user']['lastpasswd_change']) 
			|| $GLOBALS['phpgw_info']['user']['lastpasswd_change'] == 0)
		{
			$api_messages = lang('You are required to change your password during your first login')
			. '<br> Click this image on the navbar: <img src="'
			. $GLOBALS['phpgw']->common->image('preferences','navbar').'">';
		}
		else if ( isset($GLOBALS['phpgw_info']['user']['lastpasswd_change'])
			&& $GLOBALS['phpgw_info']['user']['lastpasswd_change'] < time() - (86400*30))
		{
			$api_messages = lang('it has been more then %1 days since you changed your password',30);
		}

		// This is gonna change
		if (isset($cd))
		{
			$var['messages'] = $api_messages . '<br>' . checkcode($cd);
		}

		$var['content_class'] = $sidecontent == 'sidebox' || $sidecontent == 'jsmenu' ? 'content' : 'content_nosidebox';

		$GLOBALS['phpgw']->template->set_var($var);
		$GLOBALS['phpgw']->template->pfp('out','navbar_header');

		if ( $sidecontent == 'sidebox' || $sidecontent == 'jsmenu' )
		{
			$menu_title = lang('General Menu');

			$menu['home'] = $navbar['home'];
			if ( isset($navbar['preferences']) )
			{
				$menu['preferences'] = $navbar['preferences'];
			}
			$menu['about'] = array
			(
				'text' => lang('About'),
				'url' => $GLOBALS['phpgw']->link('/about.php', array('app' => $GLOBALS['phpgw_info']['flags']['currentapp']))
			);
			$menu['logout'] = $navbar['logout'];

			display_sidebox($menu_title, $menu);
		}

		if ( isset($navigation[$GLOBALS['phpgw_info']['flags']['currentapp']])
			&& $GLOBALS['phpgw_info']['flags']['currentapp'] != 'admin'
			&& $GLOBALS['phpgw_info']['flags']['currentapp'] != 'preferences' )
		{
			$app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$GLOBALS['phpgw']->template->set_var('lang_title', $navbar[$app]['text']);
			$GLOBALS['phpgw']->template->pfp('out','extra_blocks_header');

			$menu = createObject('phpgwapi.menu');
			$app_menu = $menu->render_menu($app, $navigation[$app], $navbar[$app]);

			$GLOBALS['phpgw']->template->set_var(array('app_menu'=> $app_menu));
			$GLOBALS['phpgw']->template->pfp('out','extra_blocks_menu');
			$GLOBALS['phpgw']->template->pfp('out', 'extra_blocks_footer');
		}

		if ( isset($navbar['preferences']) 
			&& $GLOBALS['phpgw_info']['flags']['currentapp'] != 'preferences' )
		{
			$prefs = execMethod('phpgwapi.menu.get', 'preferences');
			if ( isset($prefs[$GLOBALS['phpgw_info']['flags']['currentapp']]) )
			{
//				display_sidebox(lang('preferences'), $prefs[$GLOBALS['phpgw_info']['flags']['currentapp']]);
			}
		}

		if ( isset($navigation['admin'][$GLOBALS['phpgw_info']['flags']['currentapp']]['children']) )
		{
//			display_sidebox(lang('administration'), $navigation['admin'][$GLOBALS['phpgw_info']['flags']['currentapp']]['children']);
		}

		$GLOBALS['phpgw']->template->pparse('out', 'navbar_footer');

		// If the application has a header include, we now include it
		if ( (!isset($GLOBALS['phpgw_info']['flags']['noappheader'])
			|| !$GLOBALS['phpgw_info']['flags']['noappheader'] )
			&& isset($_GET['menuaction']) )
		{
			list($app,$class,$method) = explode('.',$_GET['menuaction']);
			if (is_array($GLOBALS[$class]->public_functions) && isset($GLOBALS[$class]->public_functions['header']) )
			{
				$GLOBALS[$class]->header();
			}
		}
		$GLOBALS['phpgw']->template->set_root(PHPGW_APP_TPL);
		$GLOBALS['phpgw']->hooks->process('after_navbar');
	}

	/**
	* Display sidebox
	*
	* @param string $appname
	* @param string $menu_title
	* @param string $file
	*/
	function display_sidebox($menu_title, $menu)
	{
		$GLOBALS['phpgw']->template->set_var('lang_title', $menu_title);
		$GLOBALS['phpgw']->template->pfp('out','extra_blocks_header');
		
		foreach ( $menu as $key => $item )
		{
			if ( !isset($item['url']) )
			{
				$item['url'] = '';
			}

			if ( !isset($item['image']) )
			{
				$item['image'] = '';
			}

			if ( !isset($item['this']) )
			{
				$item['this'] = '';
			}

			sidebox_menu_item($item['url'], $item['text'], $item['image'], $item['this']);
		}

		$GLOBALS['phpgw']->template->pfp('out', 'extra_blocks_footer');
	}


	/**
	* Sidebox menu item
	*
	* @param string $item_link
	* @param string $item_text
	* @param string $item_image
	*/
	function sidebox_menu_item($item_link='', $item_text='', $item_image='', $highlight = '')
	{
		$GLOBALS['phpgw']->template->set_var(array
		(
			'lang_item'			=> $highlight ? "<strong>$item_text</strong>": $item_text,
			'item_link'			=> $item_link
		));
		$GLOBALS['phpgw']->template->pfp('out','extra_block_row');
	}
	
	/**
	* Parse navigation bar end
	* @ignore
	*/
	/**
	* Parse navigation bar end
	* @ignore
	*/
	function parse_navbar_end()
	{
		$GLOBALS['phpgw']->hooks->process('navbar_end');

		$GLOBALS['phpgw']->template->set_root(PHPGW_TEMPLATE_DIR);
		$GLOBALS['phpgw']->template->set_file('footer', 'footer.tpl');
		$var = array
		(
			'img_root'		=> $GLOBALS['phpgw_info']['server']['webserver_url'] . '/phpgwapi/templates/idots/images',
			'powered_by'	=> lang('Powered by phpGroupWare version %1', $GLOBALS['phpgw_info']['server']['versions']['phpgwapi']),
			'version'		=> $GLOBALS['phpgw_info']['server']['versions']['phpgwapi']
		);
		$GLOBALS['phpgw']->template->set_var($var);
		$GLOBALS['phpgw']->template->pfp('out','footer');
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
