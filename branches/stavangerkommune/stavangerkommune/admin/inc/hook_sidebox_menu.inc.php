<?php
	/**
	* Administration - Sidebox menu hook
	*
	* @author Pim Snel <pim@lingewoud.nl>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package admin
	* @subpackage hooks
	* @version $Id$
	*/

	{

	/*
		This hookfile is for generating an app-specific side menu used in the idots 
		template set.

		$menu_title speaks for itself
		$file is the array with link to app functions

		display_sidebox can be called as much as you like
	*/

		$menu_title = $GLOBALS['phpgw_info']['apps'][$appname]['title'] . ' '. lang('Menu');
		if (! $GLOBALS['phpgw']->acl->check('site_config_access',1,'admin'))
		{
			$file[] = array('text'	=> 'Site Configuration',
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiconfig.index', 'appname' => 'admin')) );
		}

		/*
		if (! $GLOBALS['phpgw']->acl->check('peer_server_access',1,'admin'))
		{
			$file[] = array('text'	=> 'Peer Servers',
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiserver.list_servers')));
		}
		*/

		if (! $GLOBALS['phpgw']->acl->check('account_access',1,'admin'))
		{
			$file[] = array('text'	=> 'addressmasters',
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiaclmanager.list_addressmasters', 'account_id' => $GLOBALS['phpgw_info']['user']['account_id'])));
		}

		if (! $GLOBALS['phpgw']->acl->check('account_access',1,'admin'))
		{
			$file[] = array('text' => 'User Accounts',
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiaccounts.list_users')));
		}

		if (! $GLOBALS['phpgw']->acl->check('group_access',1,'admin'))
		{
			$file[] = array('text' => 'User Groups',
					'url' =>  $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiaccounts.list_groups')));
		}

		if (! $GLOBALS['phpgw']->acl->check('applications_access',1,'admin'))
		{
			$file[] = array('text' => 'Applications',
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiapplications.get_list')));
		}

		if (! $GLOBALS['phpgw']->acl->check('global_categories_access',1,'admin'))
		{
			$file[] = array('text' => 'Global Categories',
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicategories.index')));
		}

		if (!$GLOBALS['phpgw']->acl->check('mainscreen_message_access',1,'admin') || !$GLOBALS['phpgw']->acl->check('mainscreen_message_access',2,'admin'))
		{
			$file[] = array('text' => 'Change Main Screen Message',
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uimainscreen.index')));
		}

		if (! $GLOBALS['phpgw']->acl->check('current_sessions_access',1,'admin'))
		{
			$file[] = array('text' => 'View Sessions',
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicurrentsessions.list_sessions')));
		}

		if (! $GLOBALS['phpgw']->acl->check('access_log_access',1,'admin'))
		{
			$file[] = array('text' => 'View Access Log',
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiaccess_history.list_history')));
		}

		if (! $GLOBALS['phpgw']->acl->check('error_log_access',1,'admin'))
		{
			$file[] = array('text' => 'View Error Log',
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uilog.list_log')));
		}

		if (! $GLOBALS['phpgw']->acl->check('error_log_access',1,'admin'))
		{
			$file[] = array ('text' => 'Edit Log Levels',
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiloglevels.edit_log_levels')));
		}


		if (! $GLOBALS['phpgw']->acl->check('applications_access',16,'admin'))
		{
			$file[] = array('text' => 'Find and Register all Application Hooks',
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiapplications.register_all_hooks')));
		}

		if (! $GLOBALS['phpgw']->acl->check('asyncservice_access',1,'admin'))
		{
			$file[] = array ('text' => 'Asynchronous timed services',
					'url' =>  $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiasyncservice.index')));
		}

		if (! $GLOBALS['phpgw']->acl->check('info_access',1,'admin'))
		{
			$file[] = array('text' => 'phpInfo',
					'url' =>  "javascript:openwindow('" . $GLOBALS['phpgw']->link('/admin/phpinfo.php') . "','700','600')");
		}

		display_sidebox($appname,$menu_title,$file);
	}
