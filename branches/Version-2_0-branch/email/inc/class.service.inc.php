<?php
/**
 * EMail - Service
 *
 * @author Philipp Kamps <pkamps@probusiness.de>
 * @author Dave Hall <skwashd@phpgroupware.org>
 * @copyright Copyright (C) 2003-2007 Free Software Foundation, Inc. http://www.fsf.org/
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @package email
 * @version $Id$
 */


/**
 * Service
 *
 * @package email
 */	
class email_service
{
	/**
	* @param enable debugging
	*/ 
	private $debug = false;

	/**
	 * Get the menus for the email module
	 * 
	 * @return array available menus for the current user
	 */
	public function get_menu()
	{
		$menu = array();

		$menu['navbar'] = array
		(
			'email'	=> array
			(
				'text'	=> lang('Email'),
				'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'email.uiindex.index') ),
				'image'	=> array('email', 'navbar'),
				'order'	=> 6,
				'group'	=> 'office'
			)
		);


		if ( isset($GLOBALS['phpgw_info']['user']['apps']['admin']) )
		{
			$menu['admin'] = array();
			$menu['admin'][] = array
			(
				'text'	=> lang('Site Configuration'),
				'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiconfig.index', 'appname' => 'email') )
			);
		}

		$menu['toolbar'] = array
		(
			array
			(
				// this is a bit of a hack, but it means that we can degrade gracefully
				'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'email.uicompose.compose')) . '" onclick="window.open(\'' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'email.uicompose.compose')) . '\'); return false;"',
				'text'	=> lang('New'),
				'image'	=> array('email', 'new')
			),
			array
			(
				'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'email.uisearch.form')),
				'text'	=> lang('Search'),
				'image'	=> array('email', 'search')
			),
			array
			(
				'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'email.uifilters.filters_list') ),
				'text'	=> lang('Filters'),
				'image'	=> array('email', 'filters')
			)
		);

		if ( isset($GLOBALS['phpgw_info']['user']['apps']['preferences']) )
		{
			$menu['preferences'] = array
			(
				array
				(
					'text'	=> lang('EMail Preferences'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'email.uipreferences.preferences'))
				),
				array
				(
					'text'	=> lang('Extra EMail Accounts'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'email.uipreferences.ex_accounts_list'))
				),
				array
				(
					'text'	=> lang('EMail Filters'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'email.uifilters.filters_list'))
				)
			);

			$menu['toolbar'][] = array
			(
				'text'	=> lang('Preferences'),
				'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'email.uipreferences.preferences'))
			);

			$menu['toolbar'][] = array
			(
				'text'	=> lang('Accounts'),
				'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'email.uipreferences.ex_accounts_list'))
			);
		}

		$menu['navigation'] = $menu['toolbar'];

		$msg_bootstrap = CreateObject('email.msg_bootstrap');
		$msg_bootstrap->ensure_mail_msg_exists('email.bofolder.folder', false);

		$bopreferences = CreateObject('email.bopreferences');
//		$accts = $bopreferences->msg->a;
		$accts = array();
		$menu['folders'] = array();
		foreach ( $accts as $id => $acct )
		{
			if ( is_array($acct) && isset($acct['prefs']) )
			{
				$folders = array();
				if ( substr($acct['prefs']['mail_server_type'], 0, 4) == 'imap' )
				{
					$raw_folders = $GLOBALS['phpgw']->msg->get_arg_value('folder_list', $id, '', false);
					$sep = $acct['prefs']['imap_server_type'] == 'Cyrus' ? '.' : '/';
					$folders = self::process_folders($raw_folders, $sep, $id);
				}
				else // POP3 doesn't support folders
				{
					$folders = array( array
					(
						'text'	=> lang('inbox'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'email.uiindex.index', 'fldball[folder]' => 'INBOX', 'fldball[acctnum]' => $id)),
						'image'	=> array('email', 'folder')
					));
				}
				$menu['folders'][] = array
				(
					'text'		=> trim($acct['prefs']['account_name']) ? $acct['prefs']['account_name'] : $acct['prefs']['address'],
					'image'		=> array('email', 'account'),
					'children'	=> $folders
				);
			}
		}
		return $menu;
	}


	/**
	 * The method provides the list of email folders
	 * 
	 * @return array
	 * @access public
	 */
	function getFolderContent()
	{
		return array('content', 'this is being phased out');
	}

	/**
	* Convert a flat folder structure into a menu compatiable associate array
	*
	* @param array $folders the folders to parse
	* @param string $sep the folder separator character
	* @param int $acct the mail account id for the folders
	* @return array properly formatted folder array that can be used in a menu
	*/
	private static function process_folders($folders, $sep, $acct)
	{
		$nu_mboxes = array();
		foreach ( $folders as $id => $info)
		{
			$parts = explode($sep, $info['folder_long']);
			$ref =& $nu_mboxes;
			foreach ( $parts as $part)
			{
				//echo "$mpart ";
				if ( !isset($ref['children'][$part]) )
				{
					$ref['children'][$part] = array
					(
						'text'		=> $part,
						'url'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'email.uiindex.index', 'fldball[folder]' => $info['folder_long'], 'fldball[acctnum]' => $acct)),
						'children'	=> array(),
						'image'		=> array('email', 'folder')
					);
				}
				$ref = &$ref['children'][$part];
			}
		}
		return $nu_mboxes['children'];
	}
}
