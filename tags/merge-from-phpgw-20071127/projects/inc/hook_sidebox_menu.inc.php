<?php
	/**
	* Project Manager - Sidebox-Menu for idots-template
	*
	* @author Pim Snel <pim@lingewoud.nl>
	* @copyright Copyright (C) 2000-2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package projects
	* @version $Id: hook_sidebox_menu.inc.php 18358 2007-11-27 04:43:37Z skwashd $
	* $Source: /sources/phpgroupware/projects/inc/hook_sidebox_menu.inc.php,v $
	*/

	{
			$boprojects	= CreateObject('projects.boprojects');
			$appname	= 'projects';

			if( isset($boprojects->siteconfig['show_sidebox']) && ($boprojects->siteconfig['show_sidebox'] == 'no') )
			{	// skip showing the sidebox
				return;
			}

/* Not yet ready
			switch ($_REQUEST['menuaction'])
			{
				case 'projects.uiprojects.list_projects':
				$actionmenu = true;
				$action_entry[] = array('text'	=> 'New Project',
										'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiprojects.edit_project')));

				if(!$_REQUEST['project_id'] && $_REQUEST['pro_main'])
				{
					$_REQUEST['project_id'] = $_REQUEST['pro_main'];
				}
				if($_REQUEST['project_id'])
				{
					$action_entry[] = array('text'	=> 'add work hours',
											'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiprojecthours.edit_hours','project_id'=>$_REQUEST['project_id'],'pro_main'=>$_REQUEST['pro_main'])));
				}
				break;

				default:
			}
			if($actionmenu)
			{
				$menu_title = lang('Actions');
				display_sidebox($appname,$menu_title,$action_entry);
			}
*/

			$menu_title = lang('Views');

			$file[] = array
			(
				'text'	=> 'Projects',
				'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'projects.uiprojects.list_projects','action' => 'mains'))
			);

			$file[] = array
			(
				'text'	=> 'Sub projects',
				'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'projects.uiprojects.list_projects','action' => 'subs'))
			);

			$file[] = array
			(
				'text'	=> 'Work hours',
				'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'projects.uiprojecthours.list_projects','action' => 'mains'))
			);

			$file[] = array
			(
				'text'	=> 'Controlling Sheet',
				'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'projects.uiprojecthours.controlling_sheet'))
			);

			$file[] = array
			(
				'text'	=> 'time tracker',
				'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'projects.uiprojecthours.ttracker'))
			);

			$file[] = array
			(
				'text'	=> 'Statistics',
				'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'projects.uistatistics.list_projects','action' => 'mains'))
			);

			if ( $boprojects->isprojectadmin('pad') || $boprojects->isprojectadmin('pmanager') )
			{
				$file[] = array
				(
					'text'	=> 'Budget',
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiprojects.list_budget','action'=>'mains'))
				);

				switch( $boprojects->siteconfig['accounting'] )
				{
					case 'activity':
						$file[] = array
						(
							'text'	=> 'Activities',
							'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiconfig.list_activities','action'=>'act'))
						);
						break;
					default:
						$file[] = array
						(
							'text'	=> 'Accounting',
							'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiconfig.config_accounting','action'=>'accounting'))
						);
				}
			}

			display_sidebox($appname, $menu_title, $file);

			unset($file);

			$menu_title = lang('Statistics');

			$file[] = array
			(
				'text'	=> 'work hours statistics',
				'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uistatistics.list_users_worktimes'))
			);

			$file[] = array
			(
				'text'	=> 'Gantt Chart',
				'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uistatistics.list_projects'))
			);

			$file[] = array
			(
				'text'	=> 'Projects per employee',
				'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uistatistics.list_users'))
			);

			$file[] = array
			(
				'text'	=> 'Employee per project',
				'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uistatistics.list_projects'))
			);

			/*if ($boprojects->isprojectadmin('pad') || $boprojects->isprojectadmin('pmanager'))
			{
				$file[] = array('text'	=> 'Export cost_accounting',
								'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiprojects.export_cost_accounting'));
			}*/

			display_sidebox($appname, $menu_title, $file);

			if ( $GLOBALS['phpgw_info']['user']['apps']['preferences'] )
			{
				$menu_title = lang('Preferences');

				$pref_file[] = array
				(
					'text'	=> 'Preferences',
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiconfig.preferences'))
				);

				$pref_file[] = array
				(
					'text'	=> 'Grant Access',
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'preferences.uiaclprefs.index','acl_app'=>$appname))
				);

				$pref_file[] = array
				(
					'text'	=> 'Edit categories',
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'preferences.uicategories.index','cats_app'=>'projects','cats_level'=>'True','global_cats'=>'True'))
				);

				if ( $boprojects->isprojectadmin('pad') || $boprojects->isprojectadmin('pmanager') )
				{
					$pref_file[] = array
					(
						'text'	=> 'Roles',
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiconfig.list_roles','action'=>'role'))
					);

					$pref_file[] = array
					(
						'text'	=> 'events',
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiconfig.list_events'))
					);
				}
				display_sidebox($appname, $menu_title, $pref_file);
			}

			if ( $GLOBALS['phpgw_info']['user']['apps']['admin'] )
			{
				$menu_title = lang('Administration');

				$admin_file[] = array
				(
					'text'	=> 'Site Configuration',
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'admin.uiconfig.index','appname'=> $appname))
				);

				$admin_file[] = array
				(
					'text'	=> 'managing committee',
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiconfig.list_admins','action'=>'pmanager'))
				);

				$admin_file[] = array
				(
					'text'	=> 'project administrators',
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiconfig.list_admins','action'=>'pad'))
				);

				$admin_file[] = array
				(
					'text'	=> 'sales department',
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiconfig.list_admins','action'=>'psale'))
				);

				$admin_file[] = array
				(
					'text'	=> 'Global Categories',
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'admin.uicategories.index','appname'=> $appname))
				);

				$admin_file[] = array
				(
					'text'	=> 'edit project id help msg',
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiconfig.config_proid_help_msg'))
				);

				display_sidebox($appname, $menu_title, $admin_file);
			}

			unset($boprojects);
	}
?>
