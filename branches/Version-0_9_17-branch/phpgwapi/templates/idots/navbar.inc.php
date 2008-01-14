<?php
	/**
	* Template navigation bar
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage gui
	* @version $Id: navbar.inc.php,v 1.24 2007/08/23 11:58:21 sigurdne Exp $
	*/


	/**
	* Parse navigation var
	*
	* @param boolean $force
	* @ignore
	*/
	function parse_navbar($force = False)
	{

		$GLOBALS['phpgw']->template->set_root(PHPGW_TEMPLATE_DIR);
		$GLOBALS['phpgw']->template->set_file('navbar', 'navbar.tpl');
		$GLOBALS['phpgw']->template->set_block('navbar','navbar_header','navbar_header');
		$GLOBALS['phpgw']->template->set_block('navbar','extra_blocks_header','extra_block_header');
		$GLOBALS['phpgw']->template->set_block('navbar','extra_block_row','extra_block_row');
		$GLOBALS['phpgw']->template->set_block('navbar','extra_block_spacer','extra_block_spacer');
		$GLOBALS['phpgw']->template->set_block('navbar','extra_blocks_footer','extra_blocks_footer');
		$GLOBALS['phpgw']->template->set_block('navbar','navbar_footer','navbar_footer');

		$var['img_root'] = $GLOBALS['phpgw_info']['server']['webserver_url'] . '/phpgwapi/templates/idots/images';
		$var['logo'] = isset($GLOBALS['phpgw_info']['server']['logo_file']) && $GLOBALS['phpgw_info']['server']['logo_file'] ? $GLOBALS['phpgw_info']['server']['logo_file'] : 'logo.png';
		$var['logo_title'] = isset($GLOBALS['phpgw_info']['server']['login_logo_title']) && $GLOBALS['phpgw_info']['server']['login_logo_title'] ? $GLOBALS['phpgw_info']['server']['login_logo_title'] : 'phpGroupWare Logo';
		$var['logo_url'] = isset($GLOBALS['phpgw_info']['server']['login_logo_url']) && $GLOBALS['phpgw_info']['server']['login_logo_url'] ? $GLOBALS['phpgw_info']['server']['login_logo_url'] : 'www.phpgroupware.org';

		$applications = '';
		$items = 0;
		$app_icons = '';
		$app_titles = '';
		foreach($GLOBALS['phpgw_info']['navbar'] as $app => $app_data)
		{
			if ($app != 'home' && $app != 'preferences' && $app != 'about' && $app != 'logout')
			{
				$title = $GLOBALS['phpgw_info']['apps'][$app]['title'];
				
				$icon = '<img src="' . $app_data['icon'] . '" alt="' . $title . 
					'" title="'. 	$title . '" />';

				$app_icons .= '<td class="navpanel"><a href="' . $app_data['url'] . '"';
				if (isset($GLOBALS['phpgw_info']['flags']['navbar_target']) &&
				$GLOBALS['phpgw_info']['flags']['navbar_target'])
				{
					$app_icons .= ' target="' . $GLOBALS['phpgw_info']['flags']['navbar_target'] . '"';
				}
				$app_icons .= '>' . $icon . "</a>&nbsp;&nbsp;</td>\r\n";

				$app_titles .= '<td align=center class="mainnote"><a href="'.$app_data['url'] . '"';
				if (isset($GLOBALS['phpgw_info']['flags']['navbar_target']) &&
				$GLOBALS['phpgw_info']['flags']['navbar_target'])
				{
					$app_titles .= ' target="' . $GLOBALS['phpgw_info']['flags']['navbar_target'] . '"';
				}
				$app_titles .= '>' . $title . "</a></td>\r\n";

				unset($icon);
				unset($title);
				$items++;
			}
		}

		$var['app_icons']  = $app_icons;
		$var['td_span'] = intval($items);
		$var['app_titles'] = $app_titles;
		switch ($GLOBALS['phpgw_info']['user']['preferences']['common']['navbar_format'])
		{
			case 'text':
				$var['app_icons'] = '<td colspan="' . ($items -1) . '">&nbsp;</td>';
				break;

			case 'icons':
				$var['app_titles'] = '<td colspan="' . ($items -1) . '">&nbsp;</td>';
				break;

			default: //icons_and_text
				//do nothing
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
			. $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicurrentsessions.list_sessions')) . '">'
			. lang('Current users') . ': ' . $GLOBALS['phpgw']->session->total() . '</a>';
		}
		$now = time();
		$var['user_info'] = '<b>'.$GLOBALS['phpgw']->common->display_fullname() .'</b>'. ' - '
		. lang($GLOBALS['phpgw']->common->show_date($now,'l')) . ' '
		. $GLOBALS['phpgw']->common->show_date($now, $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);

		if ( $GLOBALS['phpgw']->acl->check('changepassword', 1, 'preferences') )
		{
			if ( intval($GLOBALS['phpgw_info']['user']['lastpasswd_change']) == 0)
			{
				$api_messages = lang('You are required to change your password during your first login')
					. '<br /> Click <a href="' . $GLOBALS['phpgw']->link('/preferences/changepassword.php') . '">here</a>';
			}
			else if ($GLOBALS['phpgw_info']['user']['lastpasswd_change'] < time() - (86400*30))
			{
				$api_messages = lang('it has been more than %1 days since you changed your password',30)
					. '<br /> Click <a href="' . $GLOBALS['phpgw']->link('/preferences/changepassword.php') . '">' . lang('here') . '</a>';
			}

			if($api_messages)
			{
				$url = parse_url($GLOBALS['phpgw']->link('/preferences/changepassword.php'));
				if($_SERVER['PHP_SELF'] != $url['path'])
				{
					$var['messages'] = $api_messages;
				}
			}
		}

		// This is gonna change
		if (isset($cd))
		{
			$var['messages'] = $api_messages . '<br>' . checkcode($cd);
		}

		$GLOBALS['phpgw']->template->set_var($var);
		$GLOBALS['phpgw']->template->pfp('out','navbar_header');

		$menu_title = lang('General Menu');

		$file[] = array('text' => 'Home',
				'url' => $GLOBALS['phpgw_info']['navbar']['home']['url']);
		if ( isset($GLOBALS['phpgw_info']['navbar']['preferences']))
		{
			$file[] = array ('text' => 'Preferences',
					'url' => $GLOBALS['phpgw_info']['navbar']['preferences']['url'] 
							. '#' . $GLOBALS['phpgw_info']['flags']['currentapp']);
		}
		$file[] = array ('text' => 'About %1', 'url' => $GLOBALS['phpgw_info']['navbar']['about']['url']);
		$file[] = array ('text' => 'Logout', 'url' => $GLOBALS['phpgw_info']['navbar']['logout']['url']);

		display_sidebox('',$menu_title,$file);
		
		$GLOBALS['phpgw']->hooks->single('sidebox_menu',$GLOBALS['phpgw_info']['flags']['currentapp']);

		$GLOBALS['phpgw']->template->pparse('out','navbar_footer');

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
		unset($GLOBALS['phpgw_info']['navbar']);
	}

	/**
	* Display sidebox
	*
	* @param string $appname
	* @param string $menu_title
	* @param string $file
	* @param boolean $use_lang
	*/
	function display_sidebox($appname, $menu_title, $file, $use_lang = true)
	{
		
		if(!$appname || ($appname == $GLOBALS['phpgw_info']['flags']['currentapp'] && is_array($file) ) )
		{
			$var['lang_title'] = $menu_title;
			$GLOBALS['phpgw']->template->set_var($var);
			$GLOBALS['phpgw']->template->pfp('out','extra_blocks_header');
			
			foreach ( $file as $item )
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

				sidebox_menu_item($item['url'], $item['text'], $item['image'], $use_lang, $item['this']);
			}

			$GLOBALS['phpgw']->template->pfp('out','extra_blocks_footer');
		}
	}

	/**
	* Sidebox menu item
	*
	* @param string $item_link
	* @param string $item_text
	* @param string $item_image
	* @param boolean $use_lang
	*/
	function sidebox_menu_item($item_link='', $item_text='', $item_image='', $use_lang = True, $current_item = '')
	{
		if($item_text == '_NewLine_')
		{
			$GLOBALS['phpgw']->template->pfp('out','extra_block_spacer');
		}
		else
		{
			$lang_item = $use_lang ? lang($item_text) : $item_text;

			$var['icon_or_star']= !is_array($item_image)?($item_image ? $item_image : ''):'';
			$var['lang_item'] = $current_item ? '<b>' . $lang_item . '</b>': $lang_item;
			$var['item_link']=$item_link;

			$GLOBALS['phpgw']->template->set_var($var);		
			$GLOBALS['phpgw']->template->pparse('out','extra_block_row');
		}
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
	//	$GLOBALS['phpgw']->hooks->process('navbar_end');

		$GLOBALS['phpgw']->template->set_var($var);
		$GLOBALS['phpgw']->template->pfp('out','footer');
	}
