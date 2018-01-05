<?php
	/**
	* Template navigation bar
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
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

		$tpl = createobject('phpgwapi.Template',PHPGW_TEMPLATE_DIR);

		$tpl->set_file('navbartpl', 'navbar.tpl');
		$tpl->set_block('navbartpl','preferences');
		$tpl->set_block('navbartpl','navbar');

		$var['img_root'] = $GLOBALS['phpgw_info']['server']['webserver_url'] . '/phpgwapi/templates/verdilak/images';
		$currentapp = $GLOBALS['phpgw_info']['flags']['currentapp'];
		$applications = '';
		$exclude = array('home', 'preferences', 'about', 'logout');
		$navbar = execMethod('phpgwapi.menu.get', 'navbar');
		prepare_navbar($navbar);
		foreach ( $navbar as $app => $app_data )
		{
			if ( in_array($app, $exclude) )
			{
				continue;
			}
			if ( $app == $currentapp)
			{
				$app_data['text'] = "[<b>{$app_data['text']}</b>]";
			}

			$applications .= <<<HTML
				<br>
				<a href="{$app_data['url']}">{$app_data['text']}</a>
HTML;
/*
			$icon = $GLOBALS['phpgw']->common->image($app_data['image'][0], $app_data['image'][1]);
			$applications .= <<<HTML
				<br>
				<a href="{$app_data['url']}">
					<img src="{$icon}" alt="{$app_data['text']}" title="{$app_data['text']}">
				</a>

HTML;
*/
		}


		$menu_array = execMethod('phpgwapi.menu.get_local_menu', $currentapp);
		$var['app_menu'] = phpgwapi_menu::render_horisontal_menu($menu_array);

		$var['applications'] = $applications;
		$var['logo'] = 'logo.png';

		$var['home_url']                = $GLOBALS['phpgw']->link('/home.php');
		$var['home_text']               = lang('home');
		$var['about_url']               = $GLOBALS['phpgw']->link('/about.php', array('appname' => $GLOBALS['phpgw_info']['flags']['currentapp']) );
		$var['about_text']              = lang('about');
		$var['logout_url']              = $GLOBALS['phpgw']->link('/logout.php');
		$var['logout_text']             = lang('logout');
		if ( isset($GLOBALS['phpgw_info']['user']['apps']['manual']) )
		{
			$var['help_url'] = "javascript:openwindow('"
			 . $GLOBALS['phpgw']->link('/index.php', array
			 (
			 	'menuaction'=> 'manual.uimanual.help',
			 	'app' => $GLOBALS['phpgw_info']['flags']['currentapp'],
			 	'section' => isset($GLOBALS['phpgw_info']['apps']['manual']['section']) ? $GLOBALS['phpgw_info']['apps']['manual']['section'] : '',
			 	'referer' => phpgw::get_var('menuaction')
			 )) . "','700','600')";

			$var['help_text'] = lang('help');
			$var['help_icon'] = 'icon icon-help';
		}

		if ( $GLOBALS['phpgw']->acl->check('run', PHPGW_ACL_READ, 'preferences') )
		{
			$var['preferences_url'] = $GLOBALS['phpgw']->link('/preferences/index.php');
			$var['preferences_text'] = lang('preferences');
		}

		if ($GLOBALS['phpgw_info']['flags']['currentapp'] == 'home')
		{
			$var['welcome_img'] = $GLOBALS['phpgw']->common->image('phpgwapi','welcome-red');
		}
		else
		{
			$var['welcome_img'] = $GLOBALS['phpgw']->common->image('phpgwapi','welcome-grey');
		}

		if ($GLOBALS['phpgw_info']['flags']['currentapp'] == 'preferences')
		{
			$var['preferences_img'] = $GLOBALS['phpgw']->common->image('phpgwapi','preferences-red');
		}
		else
		{
			$var['preferences_img'] = $GLOBALS['phpgw']->common->image('phpgwapi','preferences-grey');
		}
		$var['logout_img'] = $GLOBALS['phpgw']->common->image('phpgwapi','logout-grey');

		$var['powered_by'] = lang('Powered by phpGroupWare version %1',$GLOBALS['phpgw_info']['server']['versions']['phpgwapi']);

		if (isset($GLOBALS['phpgw_info']['navbar']['admin']) && isset($GLOBALS['phpgw_info']['user']['preferences']['common']['show_currentusers']))
		{
			$var['current_users'] = '<a style="font-family: Geneva,Arial,Helvetica,sans-serif; font-size: 12pt;" href="'
				. $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicurrentsessions.list_sessions')) . '">&nbsp;'
				. lang('Current users') . ': ' . $GLOBALS['phpgw']->session->total() . '</a>';
		}
		$now = time();
		$var['user_info'] = $GLOBALS['phpgw']->common->display_fullname() . ' - '
			. lang($GLOBALS['phpgw']->common->show_date($now,'l')) . ' '
			. $GLOBALS['phpgw']->common->show_date($now,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
//			. lang($GLOBALS['phpgw']->common->show_date($now,'F')) . ' '
//			. $GLOBALS['phpgw']->common->show_date($now,'d, Y');

		// Maybe we should create a common function in the phpgw_accounts_shared.inc.php file
		// to get rid of duplicate code.
		if (!isset($GLOBALS['phpgw_info']['user']['lastpasswd_change']) || $GLOBALS['phpgw_info']['user']['lastpasswd_change'] == 0)
		{
			$api_messages = lang('You are required to change your password during your first login')
                      . '<br> Click this image on the navbar: <img src="'
                      . $GLOBALS['phpgw']->common->image('preferences','navbar.png').'">';
		}
		else if ($GLOBALS['phpgw_info']['user']['lastpasswd_change'] < time() - (86400*30))
		{
			$api_messages = lang('it has been more then %1 days since you changed your password',30);
		}

		// This is gonna change
		if (isset($cd))
		{
			$var['messages'] = $api_messages . '<br>' . checkcode($cd);
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
		// check if user is allowed to change his prefs
		if (isset($GLOBALS['phpgw_info']['user']['apps']['preferences']) && $GLOBALS['phpgw_info']['user']['apps']['preferences'])
		{
			$tpl->parse('preferences_icon','preferences');
		}
		else
		{
			$tpl->set_var('preferences_icon','');
		}
		$tpl->pfp('out','navbar');

		// If the application has a header include, we now include it
		$menuaction = phpgw::get_var('menuaction', 'string', 'GET');
		if ( !isset($GLOBALS['phpgw_info']['flags']['noappheader'])
			&& $menuaction )
		{
			list($app,$class,$method) = explode('.', $menuaction);
			if ( is_array($GLOBALS[$class]->public_functions ) && isset($GLOBALS[$class]->public_functions['header']))
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
			'img_root'		=> $GLOBALS['phpgw_info']['server']['webserver_url'] . '/phpgwapi/templates/verdilak/images',
			'version'		=> $GLOBALS['phpgw_info']['server']['versions']['phpgwapi']
		);
		if (isset($GLOBALS['phpgw_info']['navbar']['admin']) && isset($GLOBALS['phpgw_info']['user']['preferences']['common']['show_currentusers']))
		{
			$var['current_users'] = '<a style="font-family: Geneva,Arial,Helvetica,sans-serif; font-size: 12pt;" href="'
				. $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicurrentsessions.list_sessions')) . '">&nbsp;'
				. '<font color="white">'.lang('Current users') . ': ' . $GLOBALS['phpgw']->session->total() . '</font></a>';
		}
		$now = time();
		$var['user_info'] = $GLOBALS['phpgw']->common->display_fullname() . ' - '
			. lang($GLOBALS['phpgw']->common->show_date($now,'l')) . ' '
			. $GLOBALS['phpgw']->common->show_date($now,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
		$var['powered_by'] = lang('Powered by phpGroupWare version %1',$GLOBALS['phpgw_info']['server']['versions']['phpgwapi']);

		$tpl->set_var($var);
		$GLOBALS['phpgw']->hooks->process('navbar_end');
		echo $tpl->pfp('out','footer');
	}

	/**
	* Display sidebox
	*
	* @param string $appname
	* @param string $menu_title
	* @param string $file
	* @ignore
	*/
	function display_sidebox($appname,$menu_title,$file)
	{

	// workaround for old hook technique
		$GLOBALS['phpgw_info']['user']['apps']['phpgw']['sidebox'][$menu_title] = $file;
	}

	function parseMenu()
	{
		$content = (isset($GLOBALS['phpgw_info']['user']['apps']['phpgw']['sidebox'])?$GLOBALS['phpgw_info']['user']['apps']['phpgw']['sidebox']:'');

		if (is_array($content) && $GLOBALS['phpgw_info']['flags']['currentapp'] != 'admin')
		{
			include_once (PHPGW_SERVER_ROOT.'/folders/phplayersmenu/lib/PHPLIB.php');
			include_once (PHPGW_SERVER_ROOT.'/folders/phplayersmenu/lib/layersmenu-common.inc.php');
			include_once (PHPGW_SERVER_ROOT.'/folders/phplayersmenu/lib/layersmenu.inc.php');
			include_once (PHPGW_SERVER_ROOT.'/folders/inc/class.layersmenu.inc.php');

			$mid = new phpgwLayersMenu();

			$mid->setLibjsdir(PHPGW_SERVER_ROOT.'/folders/phplayersmenu/libjs');

			$mid->setImgwww($GLOBALS['phpgw_info']['server']['webserver_url'].'/folders/phplayersmenu/images/');
			$mid->setImgdir(PHPGW_SERVER_ROOT.'/folders/phplayersmenu/images/');

			$mid->setHorizontalMenuTpl(PHPGW_SERVER_ROOT.'/folders/phplayersmenu/templates/layersmenu-horizontal_menu.ihtml');
			$mid->setSubMenuTpl(PHPGW_SERVER_ROOT.'/folders/phplayersmenu/templates/layersmenu-sub_menu.ihtml');

			$mid->parseStructureForMenu($content, 'sidebox');
			$mid->newHorizontalMenu('sidebox');

			$mid->printHeader();
			$return  = $mid->getMenu('sidebox');
			$mid->printFooter();

			return $return;
		}
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
