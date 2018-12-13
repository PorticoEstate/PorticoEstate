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
	* Parse navigation bar
	*
	* @param boolean $force
	*/
 	function parse_navbar($force = False)
	{
		// we hack the template root here as this is the template set of last resort
		$tpl = CreateObject('phpgwapi.template', dirname(__FILE__), "remove");

		$tpl->set_file('navbar', 'navbar.tpl');
		$tpl->set_block('navbar', 'app', 'apps');

		$navbar = execMethod('phpgwapi.menu.get', 'navbar');
		prepare_navbar($navbar);
		foreach ( $navbar as $app => $app_data )
		{
			if($app == 'logout') // insert manual before logout
			{
				if ( isset($GLOBALS['phpgw_info']['user']['apps']['manual']) )
				{
					$tpl->set_var(array
					(
						'url' => "javascript:openwindow('"
							 . $GLOBALS['phpgw']->link('/index.php', array
							 (
							 	'menuaction'=> 'manual.uimanual.help',
							 	'app' => $GLOBALS['phpgw_info']['flags']['currentapp'],
							 	'section' => isset($GLOBALS['phpgw_info']['apps']['manual']['section']) ? $GLOBALS['phpgw_info']['apps']['manual']['section'] : '',
							 	'referer' => phpgw::get_var('menuaction')
							 )) . "','700','600')",

						'text' => lang('help'),
						'icon' => $GLOBALS['phpgw']->common->image('manual', 'navbar')
					));
				}			
				$tpl->parse('apps', 'app', true);
			}

			$tpl->set_var(array
			(
				'url'	=> $app_data['url'],
				'text'	=> $app_data['text'],
				'icon'	=> $GLOBALS['phpgw']->common->image($app_data['image'][0], $app_data['image'][1])
			));
			$tpl->parse('apps', 'app', true);
		}

		// Maybe we should create a common function in the phpgw_accounts_shared.inc.php file
		// to get rid of duplicate code.
		if ( !isset($GLOBALS['phpgw_info']['user']['lastpasswd_change'])
			|| $GLOBALS['phpgw_info']['user']['lastpasswd_change'] == 0)
		{
			$api_messages = lang('You are required to change your password during your first login')
				. '<br> Click this image on the navbar: <img src="'
				. $GLOBALS['phpgw']->common->image('preferences', 'navbar').'">';
		}
		else if ($GLOBALS['phpgw_info']['user']['lastpasswd_change'] < time() - (86400*30))
		{
			$api_messages = lang('it has been more then %1 days since you changed your password',30);
		}
 
		// This is gonna change
		if (isset($cd))
		{
			$var['messages'] = "<div class=\"warn\">$api_messages<br>\n" . checkcode($cd) . "</div>\n";
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
		$tpl->pfp('out', 'navbar');

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
	* Parse navigation bar end
	*/
	function parse_navbar_end()
	{
		// we hack the template root here as this is the template set of last resort
		$tpl = CreateObject('phpgwapi.template', dirname(__FILE__), "remove");

		$tpl->set_file('footer', 'footer.tpl');

		$var = array
		(
			'powered_by' => lang('Powered by phpGroupWare version %1',$GLOBALS['phpgw_info']['server']['versions']['phpgwapi'])
		);

		if (isset($GLOBALS['phpgw_info']['navbar']['admin'])
			&& isset($GLOBALS['phpgw_info']['user']['preferences']['common']['show_currentusers'])
			&& $GLOBALS['phpgw_info']['user']['preferences']['common']['show_currentusers'])
		{
			$var['current_users'] = '<a href="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicurrentsessions.list_sessions'))
				. '">&nbsp;' . lang('Current users') . ': ' . $GLOBALS['phpgw']->session->total() . '</a>';
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
