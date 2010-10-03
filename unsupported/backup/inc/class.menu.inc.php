<?php
	/**
	* phpGroupWare - data backup for sql, ldap and email.
	* An online configurable backup app to store data offline
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2010 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package backup
	* @subpackage core
 	* @version $Id: class.menu.inc.php 1013 2008-05-20 06:44:35Z sigurd $
	*/

	/**
	 * Description
	 * @package backup
	 */

	class backup_menu
	{

		/**
		 * Get the menus for the backup
		 *
		 * @return array available menus for the current user
		 */
		public function get_menu()
		{
			$incoming_app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$GLOBALS['phpgw_info']['flags']['currentapp'] = 'backup';

			$menus['navbar'] = array
			(
				'backup' => array
				(
					'text'	=> lang('backup'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'backup.uibackup.web_backup') ),
					'image'	=> array('backup', 'navbar'),
					'order'	=> 35,
					'group'	=> 'core'
				),
			);

			$menus['toolbar'] = array();

			if ( $GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin')
			|| $GLOBALS['phpgw']->acl->check('admin', phpgwapi_acl::ADD, 'backup'))
			{
				$menus['admin'] = array
				(
					'configuration'	=> array
					(
						'text'	=> lang('configuration'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiconfig.index', 'appname' => 'backup') )
					),
					'admin'	=> array
					(
						'text'	=> lang('Backup Administration'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'backup.uibackup.backup_admin') )
					),
					'backup'	=> array
					(
						'text'	=> lang('backup'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'backup.uibackup.web_backup') )
					),
				);
			}

			$GLOBALS['phpgw_info']['flags']['currentapp'] = $incoming_app;
			return $menus;
		}
	}
