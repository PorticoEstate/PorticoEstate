<?php
	/*	 * ************************************************************************\
	 * phpGroupWare - Registration                                              *
	 * http://www.phpgroupware.org                                              *
	 * This application written by Joseph Engo <jengo@phpgroupware.org>         *
	 * Modified by Jason Wies (Zone) <zone@users.sourceforge.net>               *
	 * Modified by Loic Dachary <loic@gnu.org>                                  *
	 * --------------------------------------------                             *
	 * Funding for this program was provided by http://www.checkwithmom.com     *
	 * --------------------------------------------                             *
	 *  This program is free software; you can redistribute it and/or modify it *
	 *  under the terms of the GNU General Public License as published by the   *
	 *  Free Software Foundation; either version 2 of the License, or (at your  *
	 *  option) any later version.                                              *
	  \************************************************************************* */

	/* $Id$ */

	class boreg
	{

		var $template;
		var $bomanagefields;
		var $fields = array();
		var $so;
		var $config;
		var $public_functions = array(
			'step1' => True,
			'step2' => True,
			'step4' => True,
			'lostpw1' => True,
			'lostpw2' => True,
			'lostpw3' => True,
			'get_locations' => true
		);

		function __construct()
		{
			$this->so = createobject('registration.soreg');
			$this->bomanagefields = createobject('registration.bomanagefields');
			$this->fields = $this->bomanagefields->get_field_list();
			$c = createobject('phpgwapi.config', 'registration');
			$c->read();
			$this->config = $c->config_data;
		}

		function step1()
		{
			$r_reg = phpgw::get_var('r_reg');
			$o_reg = phpgw::get_var('o_reg');

			$so = createobject('registration.soreg');

			if (!$r_reg['loginid'] && $this->config['username_is'] != 'email')
			{
				$errors[] = lang('You must enter a username');
			}

			if (!is_array($errors) && $r_reg['loginid'] && $so->account_exists($r_reg['loginid']))
			{
				$errors[] = lang('Sorry, that username is already taken.');
			}

			$ui = createobject('registration.uireg');
			if (is_array($errors))
			{
				$ui->step1($errors, $r_reg, $o_reg);
			}
			else
			{
				if ($this->config['username_is'] == 'email')
				{
					$GLOBALS['phpgw']->session->appsession('loginid', 'registration', $r_reg['email']);
					$r_reg['loginid'] = $r_reg['email'];
				}
				else
				{
					$GLOBALS['phpgw']->session->appsession('loginid', 'registration', $r_reg['loginid']);
				}
				$ui->step2();
			}
		}

		function step2()
		{
			$ui = createobject('registration.uireg');
			if (!$r_reg = phpgw::get_var('r_reg'))
			{
				$r_reg = array();
			}
			if (!$o_reg = phpgw::get_var('o_reg'))
			{
				$o_reg = array();
			}
			$fields = array();
			$errors = array();
//		_debug_array($r_reg);
//-------
			if ($this->config['username_is'] == 'email')
			{
//				$this->fields['loginid'] = array
//					(
//					'field_name' => 'loginid',
//					'field_text' => lang('username'),
//					'field_type' => 'email',
//					'field_values' => '',
//					'field_required' => 'Y',
//					'field_order' => 1
//				);

				if (!$r_reg['email'])
				{
					$missing_fields[] = 'email';
					$errors[] = lang('you must enter a username');
				}
				else
				{
					$loginid = $GLOBALS['phpgw']->session->appsession('loginid', 'registration');

					if ($r_reg['email'] != $loginid)
					{
						$GLOBALS['phpgw']->session->appsession('loginid', 'registration', $r_reg['email']);
						$loginid = false;
					}

					if (!$loginid)
					{
						if (execMethod('registration.soreg.account_exists', $r_reg['email']))
						{
							$errors[] = lang('Sorry, that username is already taken.');
						}
					}
				}
			}

//--------

			if ($this->config['password_is'] == 'http')
			{
				// remove entities to stop mangling
				$r_reg['passwd'] = $r_reg['passwd_confirm'] = html_entity_decode(phpgw::clean_value($_SERVER['PHP_AUTH_PW']));
			}

			if (($this->config['display_tos']) && !$r_reg['tos_agree'])
			{
				$missing_fields[] = 'tos_agree';
			}

			foreach ($r_reg as $name => $value)
			{
				if (!$value)
				{
					$missing_fields[] = $name;
				}
				$fields[$name] = $value;
			}
			reset($r_reg);

			if ($r_reg['adr_one_countryname'] == '  ')
			{
				$missing_fields[] = 'adr_one_countryname';
			}

			if ($r_reg['passwd'] != $r_reg['passwd_confirm'])
			{
				$errors[] = lang("The passwords you entered don't match");
				$missing_fields[] = 'passwd';
				$missing_fields[] = 'passwd_confirm';
			}

			if ($r_reg['passwd'])
			{
				$account = new phpgwapi_user();
				try
				{
					$account->validate_password($r_reg['passwd']);
				}
				catch (Exception $e)
				{
					$errors[] = $e->getMessage();
				}
			}

			reset($this->fields);

			foreach ($this->fields as $field_name => $field_info)
			{
				$name = $field_info['field_name'];
				$text = $field_info['field_text'];
				$values = explode(',', $field_info['field_values']);
				$required = $field_info['field_required'];
				$type = $field_info['field_type'];

				if ($required == 'Y')
				{
					$a = $r_reg;
				}
				else
				{
					$a = $o_reg;
				}

				$post_value = $a[$name];

				if ($type == 'email')
				{
					if ($post_value && (!preg_match('/@/', $post_value) || !preg_match("/\./", $post_value)))
					{
						if ($required == 'Y')
						{
							$errors[] = lang('You have entered an invalid email address');
							$missing_field[] = $name;
						}
					}
				}

				if ($type == 'birthday')
				{
					if (!checkdate($a[$name . '_month'], $a[$name . '_day'], $a[$name . '_year']))
					{
						if ($required == 'Y')
						{
							$errors[] = lang('You have entered an invalid birthday');
							$missing_fields[] = $name;
						}
					}
					else
					{
						$a[$name] = sprintf('%s/%s/%s', $a[$name . '_month'], $a[$name . '_day'], $a[$name . '_year']);
					}
				}

				if ($type == 'dropdown')
				{
					if ($post_value)
					{
						//while (list (, $value) = each($values))
                                                if (is_array($values))
                                                {
                                                    foreach ($values as $key => $value)
                                                    {
							if ($value == $post_value)
							{
								$ok = 1;
							}
                                                    }
                                                }

						if (!$ok)
						{
							$errors[] = lang('You specified a value for ' . $text . ' that is not a choice');

							$missing_fields[] = $name;
						}
					}
				}
			}

			if (is_array($o_reg))
			{
				reset($o_reg);
				foreach ($o_reg as $name => $value)
				{
					$fields[$name] = $value;
				}
			}

			if (is_array($missing_fields))
			{
				$errors[] = lang('You must fill in all of the required fields');
			}

			if (!$errors)
			{
				$headers = getallheaders();
				$ssn = $headers['uid'];
				if($ssn)
				{
					$ssn_hash = "{SHA}" . base64_encode(phpgwapi_common::hex2bin(sha1($ssn)));
					$fields['ssn_hash'] = $GLOBALS['phpgw']->db->db_addslashes($ssn_hash); // just to be safe :)
				}

				$so = createobject('registration.soreg');
				$reg_id = $so->step2($fields);
			}

			if ($errors)
			{
				$ui->step2($errors, $r_reg, $o_reg, $missing_fields);
			}
			else
			{
				$GLOBALS['phpgw']->session->appsession('loginid', 'registration', '');
				// Redirect them so they don't hit refresh and make a mess
				$GLOBALS['phpgw']->redirect_link('/registration/main.php', array('menuaction' => 'registration.uireg.ready_to_activate',
					'reg_id' => $reg_id, 'logindomain' => $_REQUEST['logindomain']));
			}
		}

		function step4()
		{
			$reg_id = phpgw::get_var('reg_id');
			$so = createobject('registration.soreg');
			$ui = createobject('registration.uireg');
			$reg_info = $so->valid_reg($reg_id);

			if (!$reg_info)
			{
				$ui->simple_screen('error_confirm.tpl');
				return False;
			}

			$so->create_account($reg_info['reg_lid'], $reg_info['reg_info']);
			$so->delete_reg_info($reg_id);
			setcookie('sessionid');
			setcookie('kp3');
			setcookie('domain');
			$ui->welcome_screen();
		}

		public function get_pending_user( $reg_id )
		{
			$so = createobject('registration.soreg');
			$reg_info = $so->valid_reg($reg_id);
			if (isset($reg_info['reg_info']) && $reg_info['reg_info'])
			{
				$reg_info['reg_info'] = unserialize(base64_decode($reg_info['reg_info']));
				unset($reg_info['reg_info']['passwd']);
				unset($reg_info['reg_info']['passwd_confirm']);
			}

			return $reg_info;
		}

		//
		// username
		//
		function lostpw1()
		{
			$r_reg = phpgw::get_var('r_reg');
			$so = createobject('registration.soreg');

			if (!$r_reg['loginid'])
			{
				$errors[] = lang('You must enter a username');
			}

			if (!is_array($errors) && !$GLOBALS['phpgw']->accounts->exists($r_reg['loginid']))
			{
				$errors[] = lang('Sorry, that username does not exist.');
			}

			if (!is_array($errors))
			{
				$error = $so->lostpw1($r_reg['loginid']);
				if ($error)
				{
					$errors[] = $error;
				}
			}

			$ui = createobject('registration.uireg');
			if (is_array($errors))
			{
				$ui->lostpw1($errors, $r_reg);
			}
			else
			{
				// Redirect them so they don't hit refresh and make a mess
				$GLOBALS['phpgw']->redirect_link('/registration/main.php', array('menuaction' => 'registration.uireg.email_sent_lostpw',
					'logindomain' => $_REQUEST['logindomain']));
			}
		}

		//
		// link sent by mail
		//
		function lostpw2()
		{
			$reg_id = phpgw::get_var('reg_id');

			$so = createobject('registration.soreg');
			$ui = createobject('registration.uireg');
			$reg_info = $so->valid_reg($reg_id);

			if (!$reg_info)
			{
				$ui->simple_screen('error_confirm.tpl');
				return False;
			}

			$so->lostpw2($reg_info['reg_lid']);

			$ui->lostpw3('', '', $reg_info['reg_lid']);
			return True;
		}

		//
		// new password
		//
		function lostpw3()
		{
			$r_reg = phpgw::get_var('r_reg');

			$lid = $GLOBALS['phpgw']->session->appsession('loginid', 'registration');
			if (!$lid)
			{
				$error[] = lang('Wrong session');
			}

			if ($r_reg['passwd'] != $r_reg['passwd_2'])
			{
				$errors[] = lang('The two passwords are not the same');
			}

			if (!$r_reg['passwd'])
			{
				$errors[] = lang('You must enter a password');
			}
			else
			{
				$account = new phpgwapi_user();
				try
				{
					$account->validate_password($r_reg['passwd']);
				}
				catch (Exception $e)
				{
					$errors[] = $e->getMessage();
				}
			}

			if (!is_array($errors))
			{
				$so = createobject('registration.soreg');
				$so->lostpw3($lid, $r_reg['passwd']);
			}

			$ui = createobject('registration.uireg');

			if (is_array($errors))
			{
				$ui->lostpw3($errors, $r_reg, $lid);
			}
			else
			{
				$ui->lostpw4();
			}

			return True;
		}

		function check_select_username()
		{
			if ($this->config['username_is'] == 'choice')
			{
				return True;
			}
			elseif ($this->config['username_is'] == 'http')
			{
				if (!$_SERVER['PHP_AUTH_USER'])
				{
					return "HTTP username is not set";
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/registration/main.php', array('menuaction' => 'registration.boreg.step1',
						'r_reg[loginid]' => $_SERVER['PHP_AUTH_USER'], 'logindomain' => $_REQUEST['logindomain']));
				}
			}

			return True;
		}

		function check_select_password()
		{
			if ($this->config['password_is'] == 'choice')
			{
				return True;
			}
			else if ($this->config['password_is'] == 'email')
			{
				return True;
			}
			elseif ($this->config['password_is'] == 'http')
			{
				if (!$_SERVER['PHP_AUTH_PW'])
				{
					return "HTTP password is not set";
				}
				else
				{
					return False;
				}
			}

			return True;
		}

		function get_locations()
		{
			$location_code = phpgw::get_var('location_code');
			$field = phpgw::get_var('field');
			if ($field)
			{
				$field_info_arr = explode('::', $this->fields[$field]['field_values']);
			}

			$criteria = array
				(
				'location_code' => $location_code,
				'child_level' => $field_info_arr[0],
				'field_name' => $field_info_arr[1]
			);

			$locations = execMethod('property.solocation.get_children', $criteria);
			$values = array
				(
				'child_level' => $field_info_arr[0],
				'locations' => $locations
			);

			return $values;
		}
	}