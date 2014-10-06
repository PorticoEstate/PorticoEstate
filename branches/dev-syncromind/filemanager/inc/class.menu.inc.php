<?php
	/**
	* phpGroupWare - filemanager
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2011 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package filemanager
	* @subpackage core
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package filemanager
	 */

	class filemanager_menu
	{
		var $sub;

		var $public_functions = array
		(
			'links'	=> true,
		);

		function filemanager_menu($sub='')
		{
			$this->sub		= $sub;
		}

		/**
		 * Get the menus for the filemanager
		 *
		 * @return array available menus for the current user
		 */
		public function get_menu()
		{
			$incoming_app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$GLOBALS['phpgw_info']['flags']['currentapp'] = 'filemanager';


			$menus['navbar'] = array
			(
				'filemanager' => array
				(
					'text'	=> lang('filemanager'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => "filemanager.uifilemanager.index") ),
					'image'	=> array('filemanager', 'navbar'),
					'order'	=> 35,
					'group'	=> 'uifilemanager'
				),
			);

			$menus['toolbar'] = array();

			if ( $GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin')
			|| $GLOBALS['phpgw']->acl->check('admin', phpgwapi_acl::ADD, 'filemanager'))
			{
				$menus['admin'] = array
				(
					'site_configuration'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('site configuration', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'filemanager.uifilemanager.admin') ),
					),
					'edit_user_menu_actions'	=> array
					(
						'text'	=> lang('edit user menu actions'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'filemanager.uifilemanager.edit_actions') )
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
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'filemanager.uifilemanager.preferences') )
					)
				);

				$menus['toolbar'][] = array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Preferences', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'filemanager.uifilemanager.preferences') ),
					'image'	=> array('filemanager', 'preferences')
				);
			}
/*
			$menus['navigation'] = array
			(
				'filemanager'	=> array
				(
					'text'	=> lang('filemanager'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => "filemanager.uifilemanager.index") ),
				)
			);
*/
			$GLOBALS['phpgw_info']['flags']['currentapp'] = $incoming_app;
			return $menus;
		}

		function links()
		{
			if(!isset($GLOBALS['phpgw_info']['user']['preferences']['filemanager']['horisontal_menus']) || $GLOBALS['phpgw_info']['user']['preferences']['filemanager']['horisontal_menus'] == 'no')
			{
				return;
			}
			$GLOBALS['phpgw']->xslttpl->add_file(array('menu'));
			$menu_brutto = execMethod('filemanager.menu.get_menu');
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
