<?php
	/**
	* Template navigation bar
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage gui
	* @version $Id: navbar.inc.php,v 1.17 2007/01/07 01:04:17 skwashd Exp $
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
		$GLOBALS['phpgw']->template->set_block('navbar', 'navbar_item', 'navbar_items');
		$GLOBALS['phpgw']->template->set_block('navbar','navbar_header','navbar_header');
		$GLOBALS['phpgw']->template->set_block('navbar','extra_blocks_header','extra_block_header');
		$GLOBALS['phpgw']->template->set_block('navbar','extra_block_row','extra_block_row');
		$GLOBALS['phpgw']->template->set_block('navbar','extra_block_spacer','extra_block_spacer');
		$GLOBALS['phpgw']->template->set_block('navbar','extra_blocks_footer','extra_blocks_footer');
		$GLOBALS['phpgw']->template->set_block('navbar','navbar_footer','navbar_footer');

		$var['img_root'] = $GLOBALS['phpgw_info']['server']['webserver_url'] . '/phpgwapi/templates/newdesign/images';

		$applications = '';
		$items = 0;

//_debug_array($GLOBALS['phpgw_info']['navbar']);


		foreach($GLOBALS['phpgw_info']['navbar'] as $app => $app_data)
		{
			if ($app != 'home' && $app != 'preferences' && $app != 'about' && $app != 'logout')
			{
				$item = array
					(
						'app_name'	=> '',
						'alt_img_app'	=> lang($app),
						'img_app'	=> "{$var['img_root']}/noimage_nav.png",
						'url_app'	=> $app_data['url']
					);

				switch($GLOBALS['phpgw_info']['user']['preferences']['common']['navbar_format'])
				{
					case 'icons':
						$item['img_app'] =& $app_data['icon'];
						break;
					case 'text':
						$item['app_name'] =& $item['alt_img_app'];
						break;
					default:
						$item['app_name'] =& $item['alt_img_app'];
						$item['img_app'] =& $app_data['icon'];
				}
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
		//echo $GLOBALS['phpgw_info']['flags']['currentapp'];

		echo '<div id="treeDiv2">';
		echo "<ul>";
		foreach($GLOBALS['phpgw_info']['navbar'] as $app => $app_data)
		{
			$GLOBALS['phpgw']->hooks->single('sidebox_menu', $app);
		}
		echo "</ul>";
		echo "</div>";

		//$GLOBALS['phpgw']->hooks->single('sidebox_menu',$GLOBALS['phpgw_info']['flags']['currentapp']);
		//$GLOBALS['phpgw']->hooks->single('sidebox_menu','newdesign');


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
		//echo "<li>$appname</li>";
		//echo "appname: '$appname', menu_title: '$menu_title', file: '$file', use_lang: '$use_lang'";
		//var_dump($file);
		//if(!$appname || ($appname == $GLOBALS['phpgw_info']['flags']['currentapp'] && is_array($file) ) )
		if( is_array($file) )
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
			$GLOBALS['phpgw']->template->set_var(array
			(
				'list_style_image'	=> ($item_image ? "url('{$item_image}')" : 'none'),
				'lang_item'			=> $current_item ? '<b>' . $lang_item . '</b>': $lang_item,
				'item_link'			=> $item_link
			));
			$GLOBALS['phpgw']->template->pfp('out','extra_block_row');
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
			'img_root'		=> $GLOBALS['phpgw_info']['server']['webserver_url'] . '/phpgwapi/templates/newdesign/images',
			'powered_by'	=> lang('Powered by phpGroupWare version %1', $GLOBALS['phpgw_info']['server']['versions']['phpgwapi']),
			'version'		=> $GLOBALS['phpgw_info']['server']['versions']['phpgwapi']
		);
		$GLOBALS['phpgw']->template->set_var($var);
		$GLOBALS['phpgw']->template->pfp('out','footer');
	}
?>
