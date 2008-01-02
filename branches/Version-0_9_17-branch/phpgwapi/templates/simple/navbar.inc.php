<?php
	/**
	* Template navigation bar
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage gui
	* @version $Id: navbar.inc.php 17902 2007-01-24 16:04:52Z Caeies $
	*/


	/**
	* Parse navigation bar
	*
	* @param boolean $force
	*/
  function parse_navbar($force = False)
  {
		$tpl = createobject('phpgwapi.Template',PHPGW_TEMPLATE_DIR);
		$tpl->set_unknowns('remove');

		$tpl->set_file(
			array(
				'navbar'		=> 'navbar.tpl',
				'navbar_app'	=> 'navbar_app.tpl'
			)
		);

		$var['navbar_color'] = (isset($GLOBALS['phpgw_info']['theme']['navbar_bg'])?$GLOBALS['phpgw_info']['theme']['navbar_bg']:'#9999FF');

		$i = 1;
		foreach($GLOBALS['phpgw_info']['navbar'] as $app => $app_data)
		{
			if ($GLOBALS['phpgw_info']['user']['preferences']['common']['navbar_format'] == 'text')
			{
				$tabs[$i]['label'] = $app_data['title'];
				$tabs[$i]['link']  = $app_data['url'];
				if (ereg($GLOBALS['phpgw_info']['navbar'][$app],$_SERVER['PHP_SELF']))
				{
					$selected = $i;
				}
				$i++;
			}
			else
			{
				$title = '<img src="' . $app_data['icon'] . '" alt="' . $app_data['title'] . '" title="'
					. $app_data['title'] . '" border="0">';
				if ($GLOBALS['phpgw_info']['user']['preferences']['common']['navbar_format'] == 'icons_and_text')
				{
					$title .= "<br>" . $app_data['title'];
					$var['width'] = '7%';
				}
				else
				{
					$var['width']  = '3%';
				}
   
				$var['value'] = '<a href="' . $app_data['url'] . '">' . $title . '</a>';
				$var['align'] = 'center';
				$tpl->set_var($var);
				$tpl->parse('applications','navbar_app',True);
			}
		}
		if ($GLOBALS['phpgw_info']['user']['preferences']['common']['navbar_format'] == 'text')
		{
			$var['navbar_color'] = $GLOBALS['phpgw_info']['theme']['bg_color'];
			$var['align'] = 'right';
			$var['value'] = $GLOBALS['phpgw']->common->create_tabs($tabs,$selected);
			$tpl->set_var($var);
			$tpl->parse('applications','navbar_app',True);
		}

		if ($GLOBALS['phpgw_info']['server']['showpoweredbyon'] == 'top')
		{
			$var['powered_by'] = lang('Powered by phpGroupWare version %1',$GLOBALS['phpgw_info']['server']['versions']['phpgwapi']);
		}
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
//				. lang($GLOBALS['phpgw']->common->show_date($now,'F')) . ' '
//				. $GLOBALS['phpgw']->common->show_date($now,'d, Y');

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

		if (isset($GLOBALS['phpgw_info']['flags']['app_header']))
		{
			$var['current_app_header'] = $GLOBALS['phpgw_info']['flags']['app_header'];
			$var['th_bg'] = isset($GLOBALS['phpgw_info']['theme']['th_bg'])?$GLOBALS['phpgw_info']['theme']['th_bg']:'#D3DCE3';
		}
		else
		{
			$tpl->set_block('navbar','app_header','app_header');
			$var['app_header'] = '';
		}

		$tpl->set_var($var);
		$tpl->pfp('out','navbar');
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
		if ($GLOBALS['phpgw_info']['server']['showpoweredbyon'] == 'bottom')
		{
			$tpl = createobject('phpgwapi.Template',PHPGW_TEMPLATE_DIR);
			$tpl->set_unknowns('remove');
   
			$tpl->set_file(
				array(
					'footer' => 'footer.tpl'
				)
			);
			$var = Array(
				'table_bg_color'	=> (isset($GLOBALS['phpgw_info']['theme']['navbar_bg'])?$GLOBALS['phpgw_info']['theme']['navbar_bg']:'#9999FF'),
			);
			$var['powered_by'] = lang('Powered by phpGroupWare version %1',$GLOBALS['phpgw_info']['server']['versions']['phpgwapi']);
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
	}
