<?php
	/**
	* Template navigation bar
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage gui
	* @version $Id: navbar.inc.php 18011 2007-03-06 13:39:29Z sigurdne $
	*/


	/**
	* Parse navigation var
	*
	* @param boolean $force
	* @ignore
	*/
	function parse_navbar($force = False)
	{

		$GLOBALS['phpgw_info']['theme']['bg_color']    = '#FFFFFF';
		$GLOBALS['phpgw_info']['theme']['bg_text']     = '#000000';
		$GLOBALS['phpgw_info']['theme']['vlink']       = 'blue';
		$GLOBALS['phpgw_info']['theme']['alink']       = 'red';
		$GLOBALS['phpgw_info']['theme']['link']        = 'blue';
		$GLOBALS['phpgw_info']['theme']['row_on']      = '#CCEEFF';
		$GLOBALS['phpgw_info']['theme']['row_off']     = '#DDF0FF';
		$GLOBALS['phpgw_info']['theme']['row_text']    = '#000000';
		$GLOBALS['phpgw_info']['theme']['th_bg']       = '#80BBFF';
		$GLOBALS['phpgw_info']['theme']['th_text']     = '#000000';
		$GLOBALS['phpgw_info']['theme']['navbar_bg']   = '#80CCFF';
		$GLOBALS['phpgw_info']['theme']['navbar_text'] = '#FFFFFF';
		$GLOBALS['phpgw_info']['theme']['table_bg']    = '#7090FF';
		$GLOBALS['phpgw_info']['theme']['table_text']  = '#000000';
		$GLOBALS['phpgw_info']['theme']['font']        = 'Arial, Helvetica, san-serif';
		$GLOBALS['phpgw_info']['theme']['bg01']        = '#dadada';
		$GLOBALS['phpgw_info']['theme']['bg02']        = '#dad0d0';
		$GLOBALS['phpgw_info']['theme']['bg03']        = '#dacaca';
		$GLOBALS['phpgw_info']['theme']['bg04']        = '#dac0c0';
		$GLOBALS['phpgw_info']['theme']['bg05']        = '#dababa';
		$GLOBALS['phpgw_info']['theme']['bg06']        = '#dab0b0';
		$GLOBALS['phpgw_info']['theme']['bg07']        = '#daaaaa';
		$GLOBALS['phpgw_info']['theme']['bg08']        = '#da9090';
		$GLOBALS['phpgw_info']['theme']['bg09']        = '#da8a8a';
		$GLOBALS['phpgw_info']['theme']['bg10']        = '#da7a7a';


		$tpl = createobject('phpgwapi.Template',PHPGW_TEMPLATE_DIR);

		$tpl->set_file(
			array(
				'navbartpl' => 'navbar.tpl'
			)
		);
		$tpl->set_block('navbartpl','preferences');
		$tpl->set_block('navbartpl','navbar');

		$var['img_root'] = $GLOBALS['phpgw_info']['server']['webserver_url'] . '/phpgwapi/templates/verdilak/images';
		$var['table_bg_color'] = $GLOBALS['phpgw_info']['theme']['navbar_bg'];
		$var['navbar_text'] = $GLOBALS['phpgw_info']['theme']['navbar_text'];
		$applications = '';
		foreach($GLOBALS['phpgw_info']['navbar'] as $app => $app_data)
		{
			if ($app != 'home' && $app != 'preferences' && ! ereg('about',$app) && $app != 'logout')
			{
				if ($GLOBALS['phpgw_info']['user']['preferences']['common']['navbar_format'] != 'text')
				{
					$title = '<img src="' . $app_data['icon'] . '" alt="' . $app_data['title'] . '" title="'
						. $app_data['title'] . '" border="0">';
				}

				if ($GLOBALS['phpgw_info']['user']['preferences']['common']['navbar_format'] != 'icons')
				{
					$title .= '<br>' . $app_data['title'];
				}
				$applications .= '<br><a href="' . $app_data['url'] . '"';
				if (isset($app_data['target']) &&  $app_data['target'])
				{
					$applications .= ' target="' . $app_data['target'] . '"';
				}
				$applications .= '>' . $title . '</a>';
				unset($title);
			}
		}
		$var['applications'] = $applications;

		if (isset($GLOBALS['phpgw_info']['theme']['special_logo']))
		{
			$var['logo'] = $GLOBALS['phpgw_info']['theme']['special_logo'];
		}
		else
		{
			$var['logo'] = 'logo.png';
		}

		$var['home_link'] = $GLOBALS['phpgw_info']['navbar']['home']['url'];
		//XXX Caeies not sure of that :(
		$var['preferences_link'] = isset($GLOBALS['phpgw_info']['navbar']['preferences']) && isset($GLOBALS['phpgw_info']['navbar']['preferences']['url']) ? $GLOBALS['phpgw_info']['navbar']['preferences']['url'] : '' ;
		$var['logout_link'] = $GLOBALS['phpgw_info']['navbar']['logout']['url'];
		$var['help_link'] = $GLOBALS['phpgw_info']['navbar']['about']['url'];

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
 
		// get sidebox content and parse it as a menu
		// it's just a hack. You need to enable the folders module to get an ouput
		if (isset($GLOBALS['phpgw_info']['user']['apps']['folders']['enabled']) && $GLOBALS['phpgw_info']['user']['apps']['folders']['enabled'] == true )
		{
			$GLOBALS['phpgw']->hooks->single('sidebox_menu',$GLOBALS['phpgw_info']['flags']['currentapp']);
			$var['sideboxcontent'] = parseMenu();
		}


		// This is gonna change
		if (isset($cd))
		{
			$var['messages'] = $api_messages . '<br>' . checkcode($cd);
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
		if (!@$GLOBALS['phpgw_info']['flags']['noappheader'] && @isset($GLOBALS['HTTP_GET_VARS']['menuaction']))
		{
			list($app,$class,$method) = explode('.',$GLOBALS['HTTP_GET_VARS']['menuaction']);
			if (is_array($GLOBALS[$class]->public_functions) && isset($GLOBALS[$class]->public_functions['header']))
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
			'table_bg_color'	=> (isset($GLOBALS['phpgw_info']['theme']['navbar_bg'])?$GLOBALS['phpgw_info']['theme']['navbar_bg']:''),
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
