<?php
	/**
	* phpGroupWare - tts:
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package tts
	* @subpackage core
 	* @version $Id: class.menu.inc.php 1042 2008-05-26 15:19:05Z sigurd $
	*/

	/**
	 * Description
	 * @package tts
	 */

	class tts_menu
	{
		var $sub;

		var $public_functions = array
		(
			'links'	=> true,
		);

		function tts_menu($sub='')
		{
			if(!$sub)
			{
				$this->sub		= $sub;
			}
		}

		/**
		 * Get the menus for the tts
		 *
		 * @return array available menus for the current user
		 */
		public function get_menu()
		{
			$incoming_app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$GLOBALS['phpgw_info']['flags']['currentapp'] = 'tts';

			$acl = CreateObject('phpgwapi.acl');
			$menus = array();

			$start_page = 'tts.index';
			if ( isset($GLOBALS['phpgw_info']['user']['preferences']['tts']['default_start_page'])
					&& $GLOBALS['phpgw_info']['user']['preferences']['tts']['default_start_page'] )
			{
					$start_page = $GLOBALS['phpgw_info']['user']['preferences']['tts']['default_start_page'];
			}

			$menus['navbar'] = array
			(
				'tts' => array
				(
					'text'	=> lang('tts'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => "tts.ui{$start_page}") ),
					'image'	=> array('tts', 'navbar'),
					'order'	=> 35,
					'group'	=> 'facilities management'
				),
			);

			$menus['toolbar'] = array();
			if ( isset($GLOBALS['phpgw_info']['user']['apps']['admin']) )
			{

				$menus['admin'] = array
				(
					'options'	=> array
					(
						'text'	=> lang('Admin options'),
						'url'	=> $GLOBALS['phpgw']->link('/tts/admin.php')
					),
					'ticket_types'	=> array
					(
						'text'	=> lang('ticket types'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicategories.index', 'appname' => 'tts'))
					),
					'acl'	=> array
					(
						'text'	=> lang('configure access permissions'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uiadmin_acl.list_acl', 'acl_app' => 'tts'))
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
						'url'	=>$GLOBALS['phpgw']->link('/preferences/preferences.php',array('appname'=>'tts'))
					),
					array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Grant Access', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'preferences.uiadmin_acl.aclprefs', 'acl_app'=> 'tts') )
					),
					array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Edit Categories', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'preferences.uicategories.index','cats_app'=>'tts','cats_level'=>'True','global_cats'=>'True'))
					)
				);


				$menus['toolbar'][] = array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Preferences', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname'	=> 'tts')),
					'image'	=> array('tts', 'preferences')
				);
			}

			$children = array
			(
				'add'	=> array
				(
					'text'	=> lang('add'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'tts.uitts.add_ticket'))
				),
				'search'	=> array
				(
					'text'	=> lang('search'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'tts.uitts.search'))
				)
			);

			$menus['navigation'] = array
			(
				'list'	=> array
				(
					'text'	=> lang('tts'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'tts.uitts.index')),
					'children'	=> $children
				)
			);
			$GLOBALS['phpgw_info']['flags']['currentapp'] = $incoming_app;
			return $menus;
		}

		function links()
		{
			if(!isset($GLOBALS['phpgw_info']['user']['preferences']['tts']['horisontal_menus']) || $GLOBALS['phpgw_info']['user']['preferences']['tts']['horisontal_menus'] == 'no')
			{
				return;
			}
			$GLOBALS['phpgw']->xslttpl->add_file(array('menu'));
			$menu_brutto = execMethod('tts.menu.get_menu');
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
