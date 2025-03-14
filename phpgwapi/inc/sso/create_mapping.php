<?php
	/**
	 * phpGroupware
	 *
	 * phpgroupware base
	 * @author Quang Vu DANG <quang_vu.dang@int-evry.fr>
	 * @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package phpgwapi
	 * @subpackage sso
	 * @version $Id$
	 */

	/**
	 * The script provides an interface for creating the mapping if the user had an 
	 * existing account in phpGroupware (to which he/she will have to authenticate 
	 * during the process) and phpGroupware is configured to supports the mapping by table.
	 *
	 * Using with Single Sign-On(Shibbolelt, CAS, ...)
	 */
	class phpgwapi_create_mapping
	{

		public function __construct()
		{
			if (!isset($GLOBALS['phpgw_info']['server']['mapping']) || $GLOBALS['phpgw_info']['server']['mapping'] == 'id')
			{
				echo lang('Access denied');
				$GLOBALS['phpgw']->common->phpgw_exit();
			}
			if (!is_object($GLOBALS['phpgw']->mapping))
			{
				echo lang('Access denied');
				$GLOBALS['phpgw']->common->phpgw_exit();
			}
			if (!isset($_SERVER['REMOTE_USER']))
			{
				echo lang('Wrong configuration') . " REMOTE_USER not set";
				$GLOBALS['phpgw']->common->phpgw_exit();
			}
			if ($GLOBALS['phpgw']->mapping->get_mapping($_SERVER['REMOTE_USER']) != '')
			{
				echo('Username already taken');
				$GLOBALS['phpgw']->common->phpgw_exit();
			}
		}

		public function create_mapping()
		{
			$error = array();
			if (isset($_POST) && isset($_POST['submitit']))
			{
				$login		 = $_POST['login'];
				$password	 = $_POST['passwd'];
				$account_lid = $GLOBALS['phpgw']->mapping->exist_mapping($_SERVER['REMOTE_USER']);
				if ($account_lid == '' || $account_lid == $login)
				{
					if ($GLOBALS['phpgw']->mapping->valid_user($login, $password))
					{
						$GLOBALS['phpgw']->mapping->add_mapping($_SERVER['REMOTE_USER'], $login);
						$GLOBALS['phpgw']->redirect_link('/login.php');
					}
					else
					{
						$_GET['cd'] = 5;
					}
				}
				else
				{
					$_GET['cd']				 = 21;
					$_GET['phpgw_account']	 = $account_lid;
				}
			}

			$uilogin = new phpgw_uilogin(false);

			//Build vars :
			$variables					 = array();
			$variables['lang_message']	 = lang('this page let you build a mapping to an existing account !');
			$variables['lang_login']	 = lang('new mapping and login');
			$variables['partial_url']	 = 'login.php';
			$variables['extra_vars']	 = array('create_mapping' => true);
			if (isset($GLOBALS['phpgw_info']['server']['auto_create_acct']) && $GLOBALS['phpgw_info']['server']['auto_create_acct'] == True)
			{
				$variables['lang_additional_url']	 = lang('new account');
				$variables['additional_url']		 = $GLOBALS['phpgw']->link('login.php', array('create_account' => true));
			}
			$uilogin->phpgw_display_login($variables);
		}
	}	