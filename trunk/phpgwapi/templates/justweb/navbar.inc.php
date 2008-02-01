<?php
	/**
	* Template navigation bar
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage gui
	* @version $Id: navbar.inc.php,v 1.20.2.3.2.3 2003/05/01 07:49:27 ralfbecker Exp $
	*/


	/**
	* Parse navigation var
	*
	* @param boolean $force
	* @ignore
	*/
	function parse_navbar($force = False)
	{
		$tpl = createobject('phpgwapi.Template',PHPGW_TEMPLATE_DIR);

		$tpl->set_file('navbar', 'navbar.tpl');
		$tpl->set_block('navbar','preferences','preferences_icon');
		
		// FIXME this is a lazy hack
		$var = array();
		$var['img_root'] = $GLOBALS['phpgw_info']['server']['webserver_url'] . '/phpgwapi/templates/justweb/images';
		$var['applications'] = '';

		$exclude = array('home', 'preferences', 'about', 'logout');
		$navbar = execMethod('phpgwapi.menu.get', 'navbar');
		prepare_navbar($navbar);
		foreach ( $navbar as $app => $app_data )
		{
			if ( in_array($app, $exclude) )
			{
				continue;
			}
			$img = $GLOBALS['phpgw']->common->image($app_data['image'][0], $app_data['image'][1]);
			$var['applications'] .= <<<HTML
			<a href="{$app_data['url']}"><img src="{$img}" alt="{$app_data['text']}" title="{$app_data['text']}"></a><br>

HTML;
			/* TODO this should be implemented at some point - skwashd feb08
			$tpl->set_var(array
			(
				'text'	=> strtoupper($app_data['text']),
				'url'	=> $app_data['url']
				'img'	=> $GLOBALS['phpgw']->common->image($app_data['image'][0], $app_data['image'][1])
			));
			$tpl->parse('apps', 'app', true);
			*/
		}
     
		$var['home_link'] = $GLOBALS['phpgw_info']['navbar']['home']['url'];
		$var['preferences_link'] = $GLOBALS['phpgw_info']['navbar']['preferences']['url'];
		$var['logout_link'] = $GLOBALS['phpgw_info']['navbar']['logout']['url'];
		$var['help_link'] = $GLOBALS['phpgw_info']['navbar']['about']['url'];

		if ($GLOBALS['phpgw_info']['flags']['currentapp'] == 'home')
		{
			$var['welcome_img'] = PHPGW_IMAGES_DIR . '/welcome-red.gif';
		}
		else
		{
			$var['welcome_img'] = PHPGW_IMAGES_DIR . '/welcome-grey.gif';
		}

		if ($GLOBALS['phpgw_info']['flags']['currentapp'] == 'preferences')
		{
			$var['preferences_img'] = PHPGW_IMAGES_DIR . '/preferences-red.gif';
		}
		else
		{
			$var['preferences_img'] = PHPGW_IMAGES_DIR . '/preferences-grey.gif';
		}
		
		$var['logout_img'] = PHPGW_IMAGES_DIR . '/logout-grey.gif';

		/*
		 * Maybe we should create a common function in the phpgw_accounts_shared.inc.php file
		 * to get rid of duplicate code.
		 */

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
			$var['messages'] = $api_messages . "<br>" . checkcode($cd);
		}

		if (isset($GLOBALS['phpgw_info']['flags']['app_header']))
		{
			$var['current_app_header'] = $GLOBALS['phpgw_info']['flags']['app_header'];
			$var['th_bg'] = $GLOBALS['phpgw_info']['theme']['th_bg'];
		}
		else
		{
			$tpl->set_block('navbar','app_header','app_header');
			$var['app_header'] = '';
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
		if (!@$GLOBALS['phpgw_info']['flags']['noappheader'] && @isset($GLOBALS['HTTP_GET_VARS']['menuaction']))
		{
			list($app,$class,$method) = explode('.',$GLOBALS['HTTP_GET_VARS']['menuaction']);
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
		$tpl = createobject('phpgwapi.Template',PHPGW_TEMPLATE_DIR);

		$tpl->set_file(
			array(
				'footer' => 'footer.tpl'
			)
		);
		$var = Array(
			'img_root'	=> $GLOBALS['phpgw_info']['server']['webserver_url'] . '/phpgwapi/templates/justweb/images',
			'table_bg_color'	=> $GLOBALS['phpgw_info']['theme']['navbar_bg'],
			'version'	=> $GLOBALS['phpgw_info']['server']['versions']['phpgwapi'],
			'user_info'	=> $GLOBALS['phpgw']->common->display_fullname() . ' - '
				. lang($GLOBALS['phpgw']->common->show_date(time(),'l')) . ' '
				. lang($GLOBALS['phpgw']->common->show_date(time(),'F')) . ' '     
				. $GLOBALS['phpgw']->common->show_date(time(),'d, Y')
		);
		$var['powered_by'] = lang('Powered by phpGroupWare version %1',$GLOBALS['phpgw_info']['server']['versions']['phpgwapi']);
		if (isset($GLOBALS['phpgw_info']['navbar']['admin']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['show_currentusers'])
		{
			$var['current_users'] = '<a href="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicurrentsessions.list_sessions')) . '">'
				. '<font color="#FFFFFF">'.lang('Current users') . ': ' . $GLOBALS['phpgw']->session->total() . '</font></a>';
		}
		$tpl->set_var($var);
		$GLOBALS['phpgw']->hooks->process('navbar_end');
		echo $tpl->pfp('out','footer');
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
		return strcmp($item1['text'], $item2['text']);
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
