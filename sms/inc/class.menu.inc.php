<?php
	/**
	 * phpGroupWare - SMS:
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package sms
	 * @subpackage core
	 * @version $Id$
	 */

	/**
	 * Description
	 * @package sms
	 */
	class sms_menu
	{

		var $sub;
		var $public_functions = array
			(
			'links' => true,
		);

		function __construct( $sub = '' )
		{
			if (!$sub)
			{
				$this->sub = $sub;
			}
		}

		/**
		 * Get the menus for the sms
		 *
		 * @return array available menus for the current user
		 */
		public function get_menu()
		{
			$incoming_app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$GLOBALS['phpgw_info']['flags']['currentapp'] = 'sms';

			$acl = & $GLOBALS['phpgw']->acl;
			$menus = array();

			$start_page = 'sms.index';
			if (isset($GLOBALS['phpgw_info']['user']['preferences']['sms']['default_start_page']) && $GLOBALS['phpgw_info']['user']['preferences']['sms']['default_start_page'])
			{
				$start_page = $GLOBALS['phpgw_info']['user']['preferences']['sms']['default_start_page'];
			}

			$menus['navbar'] = array
				(
				'sms' => array
					(
					'text' => lang('sms'),
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => "sms.ui{$start_page}")),
					'image' => array('sms', 'navbar'),
					'order' => 35,
					'group' => 'facilities management'
				),
			);

			$menus['toolbar'] = array();
			if (isset($GLOBALS['phpgw_info']['user']['apps']['admin']))
			{

				$menus['admin'] = array
					(
					'config' => array
						(
						'text' => lang('config'),
						'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiconfig2.index',
							'location_id' => $GLOBALS['phpgw']->locations->get_id('sms', 'run')))
					),
					'refresh' => array
						(
						'text' => lang('Daemon manual refresh'),
						'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'sms.uisms.daemon_manual'))
					),
					'acl' => array
						(
						'text' => $GLOBALS['phpgw']->translation->translate('Configure Access Permissions', array(), true),
						'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uiadmin_acl.list_acl',
							'acl_app' => 'sms'))
					)
				);
			}

			if (isset($GLOBALS['phpgw_info']['user']['apps']['preferences']))
			{
				$menus['preferences'] = array
					(
					array
						(
						'text' => $GLOBALS['phpgw']->translation->translate('Preferences', array(), true),
						'url' => $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname' => 'sms',
							'type' => 'user'))
					),
					array
						(
						'text' => $GLOBALS['phpgw']->translation->translate('Grant Access', array(), true),
						'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uiadmin_acl.aclprefs',
							'acl_app' => 'sms'))
					)
				);

				$menus['toolbar'][] = array
					(
					'text' => $GLOBALS['phpgw']->translation->translate('Preferences', array(), true),
					'url' => $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname' => 'sms')),
					'image' => array('sms', 'preferences')
				);
			}

			$command_children = array
				(
				'log' => array
					(
					'text' => lang('log'),
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'sms.uicommand.log'))
				)
			);

			$menus['navigation'] = array
				(
				'inbox' => array
					(
					'text' => lang('Inbox'),
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'sms.uisms.index'))
				),
				'outbox' => array
					(
					'text' => lang('Outbox'),
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'sms.uisms.outbox'))
				),
				'autoreply' => array
					(
					'text' => lang('Autoreply'),
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'sms.uiautoreply.index'))
				),
				'board' => array
					(
					'text' => lang('Boards'),
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'sms.uiboard.index'))
				),
				'command' => array
					(
					'text' => lang('Command'),
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'sms.uicommand.index')),
					'children' => $command_children
				),
				'custom' => array
					(
					'text' => lang('Custom'),
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'sms.uicustom.index'))
				),
				'poll' => array
					(
					'text' => lang('Polls'),
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'sms.uipoll.index'))
				)
			);
			$GLOBALS['phpgw_info']['flags']['currentapp'] = $incoming_app;
			return $menus;
		}

		function links()
		{
			if (!isset($GLOBALS['phpgw_info']['user']['preferences']['sms']['horisontal_menus']) || $GLOBALS['phpgw_info']['user']['preferences']['sms']['horisontal_menus'] == 'no')
			{
				return;
			}
			$GLOBALS['phpgw']->xslttpl->add_file(array('menu'));
			$menu_brutto = execMethod('sms.menu.get_menu');
			$selection = explode('::', $GLOBALS['phpgw_info']['flags']['menu_selection']);
			$level = 0;
			$menu['navigation'] = $this->get_sub_menu($menu_brutto['navigation'], $selection, $level);
			return $menu;
		}

		function get_sub_menu( $children = array(), $selection = array(), $level = '' )
		{
			$level++;
			$i = 0;
			foreach ($children as $key => $vals)
			{
				$menu[] = $vals;
				if ($key == $selection[$level])
				{
					$menu[$i]['this'] = true;
					if (isset($menu[$i]['children']))
					{
						$menu[$i]['children'] = $this->get_sub_menu($menu[$i]['children'], $selection, $level);
					}
				}
				else
				{
					if (isset($menu[$i]['children']))
					{
						unset($menu[$i]['children']);
					}
				}
				$i++;
			}
			return $menu;
		}
	}