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
					'hrm_categories'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Global Categories', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicategories.index', 'appname' => 'hrm') )
					),
					'training_category'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('training category', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'hrm.uicategory.index', 'type' => 'training') )
					),
					'skill_level'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('skill level', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'hrm.uicategory.index', 'type' => 'skill_level') )
					),
					'experience_category'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('experience category', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'hrm.uicategory.index', 'type' => 'experience') )
					),
					'qualification_category'	=> array
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
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'preferences.uiadmin_acl.aclprefs', 'acl_app'=> $appname) )
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

		function links($page='',$page_2='')
		{
			$currentapp='hrm';
			$sub = $this->sub;

			$menu = $GLOBALS['phpgw']->session->appsession('menu',substr(md5($currentapp.$sub . '_' . $page . '_' . $page_2),-20));

			if(!isset($menu) || !$menu)
			{
				$menu = array(); 

				$i=0;
				if($sub=='user')
				{
					$menu['module'][$i]['this']=True;
				}
				$menu['module'][$i]['url'] 		= $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $currentapp.'.uiuser.index'));
				$menu['module'][$i]['text'] 		= lang('User');
				$menu['module'][$i]['statustext'] 	= lang('User');
				$i++;

				if($sub=='job')
				{
					$menu['module'][$i]['this']=True;
				}
				$menu['module'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $currentapp.'.uijob.index'));
				$menu['module'][$i]['text']			=	lang('Job');
				$menu['module'][$i]['statustext']		=	lang('Job');
				$i++;

				if($sub=='place')
				{
					$menu['module'][$i]['this']=True;
				}
				$menu['module'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $currentapp.'.uiplace.index'));
				$menu['module'][$i]['text']			=	lang('PLace');
				$menu['module'][$i]['statustext']		=	lang('Place');
				$i++;

				$j=0;
				if ($sub == 'job')
				{
					if($page=='job_type')
					{
						$menu['sub_menu'][$j]['this']=True;
					}
					$menu['sub_menu'][$j]['url']			=	$GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $currentapp.'.uijob.index'));
					$menu['sub_menu'][$j]['text']			=	lang('Job type');
					$menu['sub_menu'][$j]['statustext']		=	lang('Job type');
					$j++;

					if($page=='hierarchy')
					{
						$menu['sub_menu'][$j]['this']=True;
					}
					$menu['sub_menu'][$j]['url']			=	$GLOBALS['phpgw']->link('/index.php', array('menuaction'=> $currentapp.'.uijob.hierarchy'));
					$menu['sub_menu'][$j]['text']			=	lang('Organisation');
					$menu['sub_menu'][$j]['statustext']		=	lang('Organisation');
					$j++;
				}

				$GLOBALS['phpgw']->session->appsession('menu',substr(md5($currentapp.$sub . '_' . $page . '_' . $page_2),-20),$menu);
			}
			$GLOBALS['phpgw']->session->appsession('menu_hrm','sidebox',$menu);
			return $menu;
		}
	}
