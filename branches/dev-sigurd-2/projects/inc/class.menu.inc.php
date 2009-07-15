<?php
	/**
	* Project Manager - Menus
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2009 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package projects
	* @subpackage core
 	* @version $Id: class.menu.inc.php 1013 2008-05-20 06:44:35Z sigurd $
	*/

	/**
	 * Description
	 * @package projects
	 */

	class projects_menu
	{
		var $sub;

		var $public_functions = array
		(
			'links'	=> true,
		);

		function projects_menu($sub='')
		{
			$this->sub		= $sub;
		}

		/**
		 * Get the menus for the projects
		 *
		 * @return array available menus for the current user
		 */
		public function get_menu()
		{
			$boprojects	= CreateObject('projects.boprojects');

	/*		if( isset($boprojects->siteconfig['show_sidebox']) && ($boprojects->siteconfig['show_sidebox'] == 'no') )
				return;
			}
*/
			$incoming_app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$GLOBALS['phpgw_info']['flags']['currentapp'] = 'projects';

			$start_page = 'projects';
			if ( isset($GLOBALS['phpgw_info']['user']['preferences']['projects']['default_start_page'])
					&& $GLOBALS['phpgw_info']['user']['preferences']['projects']['default_start_page'] )
			{
					$start_page = $GLOBALS['phpgw_info']['user']['preferences']['projects']['default_start_page'];
			}

			$menus['navbar'] = array
			(
				'projects' => array
				(
					'text'	=> lang('projects'),
	//				'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => "projects.ui{$start_page}.index") ),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => "projects.uiprojects.list_projects") ),
					'image'	=> array('projects', 'navbar'),
					'order'	=> 35,
					'group'	=> 'facilities management'
				),
			);

			$menus['toolbar'] = array();

			if ( isset($GLOBALS['phpgw_info']['user']['apps']['admin']) )
			{
				$menus['admin'] = array
				(
					'configuration'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Site Configuration', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'admin.uiconfig.index','appname'=> 'projects'))
					),
					'worktime_statusmail'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Worktime statusmail', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=>'projects.uiconfig.config_worktime_statusmail'))
					),
					'worktime_warnmail'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Worktime warnmail', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=>'projects.uiconfig.config_worktime_warnmail'))
					),
					'workhours_booking'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Workhours booking', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=>'projects.uiconfig.config_workhours_booking'))
					),
					'edit_locations'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('edit locations', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=>'projects.uiconfig.config_locations'))
					),

					'managing_committee'	=> array
					(
						'text'	=> lang('managing committee'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiconfig.list_admins','action'=>'pmanager'))
					),
					'administrators'	=> array
					(
						'text'	=> lang('project administrators'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiconfig.list_admins','action'=>'pad'))
					),
					'sales_department'	=> array
					(
						'text'	=> lang('sales department'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiconfig.list_admins','action'=>'psale'))
					),
					'categories'	=> array
					(
						'text'	=> lang('Global Categories'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'admin.uicategories.index','appname'=> 'projects'))
					),
					'project_id_help_msg'	=> array
					(
						'text'	=> lang('edit project id help msg'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiconfig.config_proid_help_msg'))
					),
					'acl'	=> array
					(
						'text'	=> lang('Configure Access Permissions'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uiadmin_acl.list_acl', 'acl_app' => 'projects') )
					)
				);
			}

			if ( isset($GLOBALS['phpgw_info']['user']['apps']['preferences']) )
			{
				$menus['preferences'] = array();
				$menus['preferences'][] = array
				(
					'text'	=> lang('Preferences'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiconfig.preferences'))
				);

				$menus['preferences'][] = array
				(
					'text'	=> lang('Grant Access'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'preferences.uiadmin_acl.aclprefs', 'acl_app'=> 'projects') )
				);

				$menus['preferences'][] = array
				(
					'text'	=> lang('Edit categories'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'preferences.uicategories.index','cats_app'=>'projects','cats_level'=>'True','global_cats'=>'True'))
				);

				if ( $boprojects->isprojectadmin('pad') || $boprojects->isprojectadmin('pmanager') )
				{
					$menus['preferences'][] = array
					(
						'text'	=> lang('Roles'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiconfig.list_roles','action'=>'role'))
					);

					$menus['preferences'][] = array
					(
						'text'	=> lang('events'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiconfig.list_events'))
					);

					$menus['preferences'][] = array
					(
						'text'	=> lang('surcharges'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'projects.uiconfig.list_surcharges'))
					);
				}

				$menus['toolbar'][] = array
				(
					'text'	=> lang('Preferences'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiconfig.preferences')),
	//				'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname'	=> 'projects')),
					'image'	=> array('projects', 'preferences')
				);
			}

			$statistics_children = array
			(
				'work_hours'	=> array
				(
					'text'	=> 'work hours statistics',
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uistatistics.list_users_worktimes'))
				),
				'gantt_chart'	=> array
				(
					'text'	=> 'Gantt Chart',
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uistatistics.list_projects'))
				),
				'per employee'	=> array
				(
				'text'	=> 'Projects per employee',
				'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uistatistics.list_users'))
				),
				'per project'	=> array
				(
				'text'	=> 'Employee per project',
				'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uistatistics.list_projects'))
				)
			);


			$menus['navigation'] = array
			(
				'projects'	=> array
				(
				'text'	=> 'Projects',
				'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'projects.uiprojects.list_projects','action' => 'mains'))
				),
				'sub_projects'	=> array
				(
				'text'	=> 'Sub projects',
				'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'projects.uiprojects.list_projects','action' => 'subs'))
				),
				'work_hours'	=> array
				(
				'text'	=> 'Work hours',
				'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'projects.uiprojecthours.list_projects','action' => 'mains'))
				),
				'controlling_sheet'	=> array
				(
				'text'	=> 'Controlling Sheet',
				'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'projects.uiprojecthours.controlling_sheet'))
				),
				'time_tracker'	=> array
				(
				'text'	=> 'time tracker',
				'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'projects.uiprojecthours.ttracker'))
				),
				'statistics'	=> array
				(
				'text'	=> 'Statistics',
				'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'projects.uistatistics.list_projects','action' => 'mains')),
				'children' =>$statistics_children
				)
			);

			if ( $boprojects->isprojectadmin('pad') || $boprojects->isprojectadmin('pmanager') )
			{
				$menus['navigation']['budget'] = array
				(
					'text'	=> 'Budget',
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiprojects.list_budget','action'=>'mains'))
				);

				switch( $boprojects->siteconfig['accounting'] )
				{
					case 'activity':
						$menus['navigation']['activities'] = array
						(
							'text'	=> 'Activities',
							'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiconfig.list_activities','action'=>'act'))
						);
						break;
					default:
						$menus['navigation']['accounting'] = array
						(
							'text'	=> 'Accounting',
							'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'projects.uiconfig.config_accounting','action'=>'accounting'))
						);
				}
			}



			$GLOBALS['phpgw_info']['flags']['currentapp'] = $incoming_app;
			return $menus;
		}

		function links()
		{
			if(!isset($GLOBALS['phpgw_info']['user']['preferences']['projects']['horisontal_menus']) || $GLOBALS['phpgw_info']['user']['preferences']['projects']['horisontal_menus'] == 'no')
			{
				return;
			}
			$GLOBALS['phpgw']->xslttpl->add_file(array('menu'));
			$menu_brutto = execMethod('projects.menu.get_menu');
			$selection = explode('::',$GLOBALS['phpgw_info']['flags']['menu_selection']);
			$level=0;
			$menu['navigation'] = $this->get_sub_menu($menu_brutto['navigation'],$selection,$level);
			return $menu;
		}

		function get_sub_menu($children = array(), $selection=array(),$level='')
		{
			$level++;
			$i=0;
			foreach($children as $key => $vals)
			{
				$menu[] = $vals;
				if($key == $selection[$level])
				{
					$menu[$i]['this'] = true;
					if(isset($menu[$i]['children']))
					{
						$menu[$i]['children'] = $this->get_sub_menu($menu[$i]['children'],$selection,$level);
					}
				}
				else
				{
					if(isset($menu[$i]['children']))
					{
						unset($menu[$i]['children']);
					}
				}
				$i++;
			}
			return $menu;
		}
	}
