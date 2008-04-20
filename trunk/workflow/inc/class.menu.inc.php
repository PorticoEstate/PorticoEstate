<?php
	/**
	* phpGroupWare - Workflow
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package workflow
	* @subpackage core
 	* @version $Id: class.menu.inc.php 728 2008-02-09 16:44:51Z sigurd $
	*/

	/**
	 * Description
	 * @package workflow
	 */

	class workflow_menu
	{

		/**
		 * Get the menus for the workflow
		 *
		 * @return array available menus for the current user
		 */
		public function get_menu()
		{
			$menus['navbar'] = array
			(
				'workflow' => array
				(
					'text'	=> lang('workflow'),
					'url'	=> $GLOBALS['phpgw']->link('/workflow/index.php'),
					'image'	=> array('workflow', 'navbar'),
					'order'	=> 35,
					'group'	=> 'facilities management'
				),
			);

			$menus['toolbar'] = array();

			if ( isset($GLOBALS['phpgw_info']['user']['apps']['admin']) )
			{
				$menus['admin'] = array
				(
					'categories'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Global Categories', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicategories.index', 'appname' => 'workflow') )
					),
					'admin_processes'	=> array
					(
						'text'	=> lang('Admin Processes'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'workflow.ui_adminprocesses.form') )
					),
					'monitors'	=> array
					(
						'text'	=> lang('%1 Monitoring', 'workflow'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'workflow.ui_monitors.form') )
					),
					'acl'	=> array
					(
						'text'	=> lang('Configure Access Permissions'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uiadmin_acl.list_acl', 'acl_app' => 'workflow') )
					)
				);
			}

			if ( isset($GLOBALS['phpgw_info']['user']['apps']['preferences']) )
			{
				$menus['preferences'] = array
				(
					array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Preferences', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname' => 'workflow', 'type'=> 'user') )
					),
					array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Grant Access', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'preferences.uiadmin_acl.aclprefs', 'acl_app'=> 'workflow') )
					)
				);

				$menus['toolbar'][] = array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Preferences', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname'	=> 'workflow')),
					'image'	=> array('workflow', 'preferences')
				);
			}

			$menus['navigation'] = array
			(
				'new_instance'	=> array
				(
					'text'	=> lang('New Instance'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'workflow.ui_useropeninstance.form'))
				),
				'global_activities'	=> array
				(
					'text'	=> lang('Global activities'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'workflow.ui_useractivities.form', 'show_globals' => 1))
				),
				'my_processes'	=> array
				(
					'text'	=> lang('My Processes'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'workflow.ui_userprocesses.form'))
				),
				'my_activities'	=> array
				(
					'text'	=> lang('My Activities'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'workflow.ui_useractivities.form')),
				),
				'my_instances'	=> array
				(
					'text'	=> lang('My Instances'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'workflow.ui_userinstances.form'))
				)
			);
			return $menus;
		}
	}
