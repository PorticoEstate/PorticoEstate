<?php
	/**
	* phpGroupWare - HRM: a  human resource competence management system.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package hrm
	* @subpackage core
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package hrm
	 */

	class hrm_menu
	{
		var $sub;

		var $public_functions = array
		(
			'links'	=> True,
		);

		function hrm_menu($sub='')
		{
			$this->sub		= $sub;
		}

		/**
		 * Get the menus for the hrm
		 *
		 * @return array available menus for the current user
		 */
		public function get_menu()
		{
			$start_page = 'user';
			if ( isset($GLOBALS['phpgw_info']['user']['preferences']['hrm']['default_start_page'])
					&& $GLOBALS['phpgw_info']['user']['preferences']['hrm']['default_start_page'] )
			{
					$start_page = $GLOBALS['phpgw_info']['user']['preferences']['hrm']['default_start_page'];
			}

			$menus['navbar'] = array
			(
				'hrm' => array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('hrm', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => "hrm.ui{$start_page}.index") ),
					'image'	=> array('hrm', 'navbar'),
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
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicategories.index', 'appname' => 'hrm') )
					),
					'training'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('training category', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'hrm.uicategory.index', 'type' => 'training') )
					),
					'skill_level'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('skill level', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'hrm.uicategory.index', 'type' => 'skill_level') )
					),
					'experience'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('experience category', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'hrm.uicategory.index', 'type' => 'experience') )
					),
					'qualification'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('qualification category', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'hrm.uicategory.index', 'type' => 'qualification') )
					),
					'acl'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Configure Access Permissions', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uiadmin_acl.list_acl', 'acl_app' => 'hrm') )
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
						'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname' => 'hrm', 'type'=> 'user') )
					),
					array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Grant Access', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'preferences.uiadmin_acl.aclprefs', 'acl_app'=> 'hrm') )
					)
				);

				$menus['toolbar'][] = array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Preferences', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname'	=> 'hrm')),
					'image'	=> array('hrm', 'preferences')
				);
			}
			$job_children = array
			(
				'job_type'	=> array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Job type', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.index'))
				),
				'organisation'	=> array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Organisation', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.hierarchy'))
				)
			);

			$menus['navigation'] = array
			(
				'user'	=> array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('User', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uiuser.index'))
				),
				'job'	=> array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Job type', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uijob.index')),
					'children' => $job_children
				),
				'place'	=> array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('PLace', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'hrm.uiplace.index'))
				)
			);
			return $menus;
		}

		function links()
		{
			if(!isset($GLOBALS['phpgw_info']['user']['preferences']['hrm']['horisontal_menus']) || $GLOBALS['phpgw_info']['user']['preferences']['hrm']['horisontal_menus'] == 'no')
			{
				return;			
			}
			$GLOBALS['phpgw']->xslttpl->add_file(array('menu'));
			$menu_brutto = execMethod('hrm.menu.get_menu');
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
