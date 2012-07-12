<?php
	/**
	* Preferences - change password
	*
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package preferences
	* @version $Id$
	*/

	$GLOBALS['phpgw_info']['flags'] = array(
		'noheader'   => True,
		'nonavbar'   => True,
		'currentapp' => 'preferences'
	);

	/**
	 * Include phpgroupware header
	 */
	include('../header.inc.php');

	$n_passwd   = isset($_POST['n_passwd']) && $_POST['n_passwd'] ? $_POST['n_passwd'] : '';
	$n_passwd_2 = isset($_POST['n_passwd_2']) && $_POST['n_passwd_2'] ? $_POST['n_passwd_2'] : '';

	if (! $GLOBALS['phpgw']->acl->check('changepassword', 1, 'preferences') || (isset($_POST['cancel']) && $_POST['cancel']))
	{
		$GLOBALS['phpgw']->redirect_link('/preferences/index.php');
		$GLOBALS['phpgw']->common->phpgw_exit();
	}

	$GLOBALS['phpgw']->template->set_file(array(
		'form' => 'changepassword.tpl'
	));
	$GLOBALS['phpgw']->template->set_var('lang_enter_password',lang('Enter your new password'));
	$GLOBALS['phpgw']->template->set_var('lang_reenter_password',lang('Re-enter your password'));
	$GLOBALS['phpgw']->template->set_var('lang_change',lang('Change'));
	$GLOBALS['phpgw']->template->set_var('lang_cancel',lang('Cancel'));
	$GLOBALS['phpgw']->template->set_var('form_action',$GLOBALS['phpgw']->link('/preferences/changepassword.php'));

	if ($GLOBALS['phpgw_info']['server']['auth_type'] != 'ldap')
	{
		$GLOBALS['phpgw']->template->set_var('sql_message',lang('note: This feature does *not* change your email password. This will '
			. 'need to be done manually.'));
	}

	if (isset($_POST['change']) && $_POST['change'])
	{
		$errors = array();

		if ($n_passwd != $n_passwd_2)
		{
			$errors[] = lang('The two passwords are not the same');
		}
		else
		{
			$account	= new phpgwapi_user();
			try
			{
				$account->validate_password($n_passwd);
			}
			catch(Exception $e)
			{
				$errors[] = $e->getMessage();
			//	trigger_error($e->getMessage(), E_USER_WARNING);
			}
		}

		if (! $n_passwd)
		{
			$errors[] = lang('You must enter a password');
		}


		if (count($errors))
		{
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
			$GLOBALS['phpgw']->template->set_var('messages',$GLOBALS['phpgw']->common->error_list($errors));
			$GLOBALS['phpgw']->template->pfp('out','form');
			$GLOBALS['phpgw']->common->phpgw_exit(True);
		}

		$o_passwd = $GLOBALS['phpgw_info']['user']['passwd'];
		$passwd_changed = $GLOBALS['phpgw']->auth->change_password($o_passwd, $n_passwd);
		if (! $passwd_changed)
		{
			// This need to be changed to show a different message based on the result
			$GLOBALS['phpgw']->redirect_link('/preferences/index.php',array('cd'=>38));
		}
		else
		{
			$GLOBALS['phpgw_info']['user']['passwd'] = $GLOBALS['phpgw']->auth->change_password($o_passwd, $n_passwd);
			$GLOBALS['hook_values']['account_id'] = $GLOBALS['phpgw_info']['user']['account_id'];
			$GLOBALS['hook_values']['old_passwd'] = $o_passwd;
			$GLOBALS['hook_values']['new_passwd'] = $n_passwd;
			$GLOBALS['phpgw']->hooks->process('changepassword');
			$GLOBALS['phpgw']->redirect_link('/preferences/index.php',array('cd'=>18));
		}
	}
	else
	{
		$GLOBALS['phpgw_info']['flags']['app_header'] = lang('Change your password');
		$GLOBALS['phpgw']->common->phpgw_header();
		echo parse_navbar();

		$GLOBALS['phpgw']->template->pfp('out','form');
		$GLOBALS['phpgw']->common->phpgw_footer();
	}
?>
