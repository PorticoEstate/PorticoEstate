<?php
	/**
	 * phpGroupWare
	 *
	 * phpgroupware base
	 * @author Quang Vu DANG <quang_vu.dang@int-evry.fr>
	 * @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package phpgroupware
	 * @version $Id$
	 */

	/**
	 * The script provides an interface for creating the new account
	 * if phpGroupware allows users to create the accounts
	 *
	 * Using with Signle Sign-On (Shibboleth, CAS, ...)
	 *
	 */

	class phpgwapi_create_account
	{

		private $login;

		public function __construct()
		{
			$phpgw_map_location = isset($_SERVER['HTTP_SHIB_ORIGIN_SITE']) ? $_SERVER['HTTP_SHIB_ORIGIN_SITE'] : 'local';
			$phpgw_map_authtype = isset($_SERVER['HTTP_SHIB_ORIGIN_SITE']) ? 'shibboleth':'remoteuser';

			//Create the mapping if necessary :
			if(isset($GLOBALS['phpgw_info']['server']['mapping']) && !empty($GLOBALS['phpgw_info']['server']['mapping']))
			{
				if(!is_object($GLOBALS['phpgw']->mapping))
				{
					$GLOBALS['phpgw']->mapping = CreateObject('phpgwapi.mapping', array('auth_type'=> $phpgw_map_authtype, 'location' => $phpgw_map_location));
				}
			}

			if (!isset($GLOBALS['phpgw_info']['server']['auto_create_acct']) || $GLOBALS['phpgw_info']['server']['auto_create_acct'] != True)
			{
				echo lang('Access denied');
				$GLOBALS['phpgw']->common->phpgw_exit();
			}
			if (!is_object($GLOBALS['phpgw']->mapping))
			{
				echo lang('Access denied');
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			if (isset($_SERVER["OIDC_groups"]))
			{
				$OIDC_groups = mb_convert_encoding(mb_convert_encoding($_SERVER["OIDC_groups"], 'ISO-8859-1', 'UTF-8'), 'UTF-8', 'ISO-8859-1');
				$ad_groups	= explode(",",$OIDC_groups);
				$default_group_lid	 = !empty($GLOBALS['phpgw_info']['server']['default_group_lid']) ? $GLOBALS['phpgw_info']['server']['default_group_lid'] : 'Default';
				if (!in_array($default_group_lid, $ad_groups))
				{
					echo lang('missing membership: "%1" is not in the list', $default_group_lid);
					$GLOBALS['phpgw']->common->phpgw_exit();
				}
			}
			else if(!phpgw::get_var('OIDC_pid', 'bool', 'SERVER'))
			{
				echo lang('Access denied');
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			$this->login = $GLOBALS['phpgw']->auth->get_username(true);

			if (empty($this->login))
			{
				//reserve fallback
				if( phpgw::get_var('OIDC_pid', 'bool', 'SERVER'))
				{
					$GLOBALS['phpgw']->redirect_link('login.php', array('skip_remote' => true));
				}
				//fallback failed
				echo lang('Did not find any username');
				$GLOBALS['phpgw']->common->phpgw_exit();
			}
			else
			{
				if ($GLOBALS['phpgw']->mapping->get_mapping($this->login) != '')
				{
					echo lang('Username already taken');
					$GLOBALS['phpgw']->common->phpgw_exit();
				}
				if (($account = $GLOBALS['phpgw']->mapping->exist_mapping($this->login)) != '')
				{
					$GLOBALS['phpgw']->redirect_link('login.php', array('create_mapping' => true,'cd' => '21', 'phpgw_account' => $account));
				}
			}
		}

		public function display_create()
		{
			$login = $this->login;

			$firstname	 = '';
			$lastname	 = '';
			if (isset($_SERVER["HTTP_SHIB_GIVENNAME"]))
			{
				$firstname = $_SERVER["HTTP_SHIB_GIVENNAME"];
			}
			if (isset($_SERVER["HTTP_SHIB_SURNAME"]))
			{
				$lastname = $_SERVER["HTTP_SHIB_SURNAME"];
			}

			if (isset($_SERVER["OIDC_given_name"]))
			{
				$firstname = phpgw::get_var('OIDC_given_name', 'string', 'SERVER');
			}
			if (isset($_SERVER["OIDC_family_name"]))
			{
				$lastname = phpgw::get_var('OIDC_family_name', 'string', 'SERVER');
			}

			$email	 = phpgw::get_var('OIDC_email', 'string', 'SERVER');
			$cellphone = '';

			if ($_SERVER['REQUEST_METHOD'] == 'POST' && phpgw::get_var('submitit', 'bool', 'POST'))
			{
				$submit = phpgw::get_var('submitit', 'bool', 'POST');
				if (!$GLOBALS['phpgw_info']['server']['mapping'] == 'id')// using REMOTE_USER for account_lid
				{
					$login = phpgw::get_var('login', 'string', 'POST');
				}
				$firstname	 = phpgw::get_var('firstname', 'string', 'POST');
				$lastname	 = phpgw::get_var('lastname', 'string', 'POST');
				$password1	 = !empty($_POST['passwd']) ? html_entity_decode(phpgw::get_var('passwd', 'string', 'POST')) : '';
				$password2	 = !empty($_POST['passwd_confirm']) ? html_entity_decode(phpgw::get_var('passwd_confirm', 'string', 'POST')) : '';
				$email		 = phpgw::get_var('email', 'email', 'POST');
				$cellphone	 = phpgw::get_var('cellphone', 'string', 'POST');
				
			}

			$error = array();
			if (isset($submit) && $submit)
			{
				if (!$login)
				{
					$error[] = lang('You have to choose a login');
				}

				if (!preg_match("/^[0-9_a-z]*$/i", $login))
				{
					$error[] = lang('Please submit just letters and numbers for your login');
				}
				if (!$password1)
				{
					$error[] = lang('You have to choose a password');
				}

				if ($password1 != $password2)
				{
					$error[] = lang('Please, check your password');
				}

				$account = new phpgwapi_user();
				try
				{
					$account->validate_password($password1);
				}
				catch (Exception $e)
				{
					$error[] = $e->getMessage();
				}

				if ($GLOBALS['phpgw']->accounts->exists($login))
				{
					$error[] = lang("user %1 already exists, please try another login", $login);
				}

				if (!is_array($error) || count($error) == 0)
				{
					if (!$firstname)
					{
						$firstname = $login;
					}
					if (!$lastname)
					{
						$lastname = $login;
					}

					$account_id = $GLOBALS['phpgw']->accounts->auto_add($login, $password1, $firstname, $lastname);

					if ($GLOBALS['phpgw_info']['server']['mapping'] == 'table') // using only mapping by table
					{
						$GLOBALS['phpgw']->mapping->add_mapping($_SERVER['REMOTE_USER'], $login);
					}
					else if ($GLOBALS['phpgw_info']['server']['mapping'] == 'all' && $login != $_SERVER['REMOTE_USER'])
					{
						$GLOBALS['phpgw']->mapping->add_mapping($_SERVER['REMOTE_USER'], $login);
					}

					if ($account_id)
					{
						if (!empty($email))
						{
							$title	 = lang('User access');
							$message = lang('account has been created');
							$from	 = "noreply<noreply@{$GLOBALS['phpgw_info']['server']['hostname']}>";
							$send	 = CreateObject('phpgwapi.send');

							try
							{
								$send->msg('email', $email, $title, stripslashes(nl2br($message)), '', '', '', $from, 'System message', 'html', '', array(), false);
							}
							catch (Exception $ex)
							{
								
							}
						}
						$preferences = createObject('phpgwapi.preferences', $account_id);
						$preferences->add('common', 'email', $email);
						if($cellphone)
						{
							$preferences->add('common', 'cellphone', $cellphone);
						}

						$preferences->save_repository();

						$GLOBALS['phpgw']->log->write(array('text'	 => 'I-Notification, user created %1',
							'p1'	 => $login));
					}

					$GLOBALS['phpgw']->redirect_link('/login.php');
				}
			}

			$uilogin = new phpgw_uilogin(false);

			$variables = array();
			if ($GLOBALS['phpgw_info']['server']['mapping'] == 'id')// using REMOTE_USER for account_lid
			{
				$variables['login_read_only'] = true;
			}
			$variables['lang_message'] = lang('your account doesn\'t exist, please fill in infos !');
			if (count($error))
			{
				$variables['lang_message'] .= $GLOBALS['phpgw']->common->error_list($error);
			}
			$variables['lang_login']			 = lang('new account and login');
			$variables['login']					 = $login;
			$variables['lang_firstname']		 = lang('firstname');
			$variables['lang_lastname']			 = lang('lastname');
			$variables['lang_email']			 = lang('email');
			$variables['lang_cellphone']		 = lang('cellphone');
			$variables['firstname']				 = $firstname;
			$variables['lastname']				 = $lastname;
			$variables['email']					 = $email;
			$variables['cellphone']				 = $cellphone;
			$variables['lang_confirm_password']	 = lang('confirm password');
			$variables['partial_url']			 = 'login.php';
			$variables['extra_vars']			 = array('create_account' => true);
			if (!($GLOBALS['phpgw_info']['server']['mapping'] == 'id'))
			{
				$variables['lang_additional_url']	 = lang('new mapping');
				$variables['additional_url']		 = $GLOBALS['phpgw']->link('login.php', array('create_mapping' => true));
			}

			$uilogin->phpgw_display_login($variables);
		}
	}