<?php
/**
 * probusiness template set
 * @author Philipp Kamps <pkamps@probusiness.de>
 * @author Daniel Briegert <dbriegert@probusiness.de>
 * @copyright Copyright (C) 2003-2005 Free Software Foundation http://www.fsf.org/
 * @license http://www.fsf.org/licenses/gpl.html GNU General Public License
 * @package phpgwapi
 * @subpackage gui
 * @version $Id: navbar.inc.php 17902 2007-01-24 16:04:52Z Caeies $
 */


	/**
	 * Parse navigation bar
	 *
	 * @param boolean $force
	 * @ignore
	 */
	function parse_navbar($force = false)
	{
		$tpl = createobject('phpgwapi.Template',PHPGW_TEMPLATE_DIR);
		$tpl->set_unknowns('remove');

		$tpl->set_file(array('navbar'           => 'navbar.tpl',
                         'navbar_app'       => 'navbar_app.tpl',
                         'navbar_app_select'=> 'navbar_app_select.tpl',
                         'navbar_app_table' => 'navbar_app_tablecontent.tpl'
                        ));

		$var['api_root'] = $GLOBALS['phpgw_info']['server']['webserver_url'] . '/phpgwapi/templates/probusiness/';
	/*
	 *  folder handling
	 */
		if ( $GLOBALS['phpgw_info']['user']['apps']['folders']['enabled'] == true )
		{
			$mtree = createobject('folders.uifolders', '');
			$var['switchlink'] = $mtree->get_switchlink();
			$folderMode = $mtree->get_folderMode();
		}

		if ( $folderMode == 'enabled' )
		{
			if ($GLOBALS['phpgw_info']['user']['apps']['folders']['enabled'] == true)
			{
				$var['navbarview'] = $mtree->get_iframe();
			}
		}
		else
		{

		/*
		 *  application list
		 */
			$navBarMode = $GLOBALS['phpgw_info']['user']['preferences']['common']['navbar_format'];
			$tpl->set_block('navbar_app_table','app_row','app_rows');
			foreach($GLOBALS['phpgw_info']['navbar'] as $app => $app_data)
			{

				$label = '';
				if ( $navBarMode == 'text' OR $navBarMode == 'icons_and_text' )
				{
					$label = $app_data['title'];
				}
				if ( $navBarMode == 'icons_and_text' )
				{
					$var['break'] = '<br />';
				}
				if (  $navBarMode == 'icons' OR $navBarMode == 'icons_and_text' OR $navBarMode == '')
				{
					
					$image = '<img src="' . $app_data['icon'] .
					         '" alt="' . $app_data['title'] .
					         '" title="' . $app_data['title'] .
					         '" />';
				}

				$var['appllink'] = $app_data['url'];
				$var['image'] = $image;
				$var['label'] = $label;
				$tpl->set_var($var);
				// mark actual selected application
				if ($GLOBALS['phpgw_info']['flags']['currentapp'] == $app)
				{
					$tpl->fp('appdiv','navbar_app_select');
				}
				else
				{
					$tpl->fp('appdiv','navbar_app');
				}
				$tpl->fp('app_rows','app_row',true);
			}
			$tpl->parse('navbarview','app_rows',false);
		}

		// get sidebox content and parse it as a menu
		// it's just a hack. You need to enable the folders module to get an ouput
		if ( $GLOBALS['phpgw_info']['user']['apps']['folders']['enabled'] == true )
		{
			$GLOBALS['phpgw']->hooks->single('sidebox_menu',$GLOBALS['phpgw_info']['flags']['currentapp']);
			$var['sideboxcontent'] = parseMenu();
		}

		if (isset($GLOBALS['phpgw_info']['navbar']['admin'])
			&& isset($GLOBALS['phpgw_info']['user']['preferences']['common']['show_currentusers'])
			&& $GLOBALS['phpgw_info']['user']['preferences']['common']['show_currentusers'])
		{
			$var['current_users'] = '<a href="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicurrentsessions.list_sessions')) .'">&nbsp;' .
					lang('Current users') . ': ' . $GLOBALS['phpgw']->session->total() . '</a>';
		}

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

		// This gonna change
		if (isset($cd))
		{
			$var['messages'] = $api_messages . '<br>' . checkcode($cd);
		}

		if (isset($GLOBALS['phpgw_info']['flags']['app_header']))
		{
			$var['current_app_header'] = $GLOBALS['phpgw_info']['flags']['app_header'];
		}

		$tpl->set_var($var);
		$tpl->pfp('out','navbar');

		// If the application has a header include, we now include it
		if (!@$GLOBALS['phpgw_info']['flags']['noappheader'] && @isset($_GET['menuaction']))
		{
			list($app,$class,$method) = explode('.',$_GET['menuaction']);
			if (is_array($GLOBALS[$class]->public_functions) && isset($GLOBALS[$class]->public_functions['header']) && $GLOBALS[$class]->public_functions['header'])
			{
				$GLOBALS[$class]->header();
			}
		}
		$GLOBALS['phpgw']->hooks->process('after_navbar');
	}


	/**
	* Parse navigation bar end
	* @ignore
	*/
	function parse_navbar_end()
	{
		$tpl = createobject('phpgwapi.Template',PHPGW_TEMPLATE_DIR);
		$tpl->set_unknowns('remove');
		$tpl->set_file(array('footer' => 'footer.tpl'));

		$var['powered_by'] = '[ layout powered by <a target="_blank" href="http://www.probusiness.de">pro|business AG</a> ]';

		if (isset($GLOBALS['phpgw_info']['navbar']['admin'])
			&& isset($GLOBALS['phpgw_info']['user']['preferences']['common']['show_currentusers'])
			&& $GLOBALS['phpgw_info']['user']['preferences']['common']['show_currentusers'])
		{
			$var['current_users'] = '<a href="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicurrentsessions.list_sessions'))
					. '">&nbsp;'
					. lang('Current users')
					. ': '
					. $GLOBALS['phpgw']->session->total()
					. '</a>';
		}

		$now = time();
		$var['user_info'] = $GLOBALS['phpgw']->common->display_fullname() . ' - '
				. lang($GLOBALS['phpgw']->common->show_date($now,'l')) . ' '
				. $GLOBALS['phpgw']->common->show_date($now,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);

		$tpl->set_var($var);
		$GLOBALS['phpgw']->hooks->process('navbar_end');
		$tpl->pfp('out','footer');
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
