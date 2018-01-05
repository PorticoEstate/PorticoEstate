<?php
	/**
	 * phpGroupWare - registration
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package registration
	 * @subpackage core
	 * @version $Id: class.menu.inc.php 4683 2010-01-30 17:16:00Z sigurd $
	 */

	/**
	 * Description
	 * @package registration
	 */
	class registration_menu
	{

		/**
		 * Get the menus for the registration
		 *
		 * @return array available menus for the current user
		 */
		public function get_menu()
		{
			$menus = array();

			$incoming_app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$GLOBALS['phpgw_info']['flags']['currentapp'] = 'registration';

			$menus['toolbar'] = array();


			$menus['navbar'] = array
				(
				'registration' => array
					(
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'registration.uipending.index')),
					'text' => lang('registration'),
					'image' => array('admin', 'navbar'),
					'order' => -4,
					'group' => 'systools'
				),
			);


			if ($GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin') || $GLOBALS['phpgw']->acl->check('admin', phpgwapi_acl::ADD, 'registration'))
			{
				$menus['admin'] = array
					(
					'index' => array
						(
						'text' => lang('Configuration'),
						'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiconfig.index',
							'appname' => 'registration'))
					),
					'fields' => array
						(
						'text' => $GLOBALS['phpgw']->translation->translate('Manage Fields', array(), true),
						'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'registration.uimanagefields.admin'))
					)
				);
			}


//			$menus['navigation'] = array();
			$menus['navigation']['pending'] = array
				(
				'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'registration.uipending.index')),
				'text' => lang('Pending for approval'),
				'image' => array('property', 'location'),
			);

			$GLOBALS['phpgw_info']['flags']['currentapp'] = $incoming_app;
			return $menus;
		}
	}