<?php
	/**************************************************************************\
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
	\**************************************************************************/

	/* $Id: class.boreg.inc.php 17907 2007-01-24 16:51:08Z Caeies $ */

	class boreg
	{
		var $template;
		var $bomanagefields;
		var $fields;
		var $so;
		var $public_functions = array(
			'step1' => True,
			'step2' => True,
			'step4' => True,
			'lostpw1' => True,
			'lostpw2' => True,
			'lostpw3' => True
		);

		function boreg()
		{
			$this->so = createobject ('registration.soreg');
			$this->bomanagefields = createobject ('registration.bomanagefields');
			$this->fields = $this->bomanagefields->get_field_list ();
		}

		function step1()
		{
			global $config, $r_reg;

			$so = createobject('registration.soreg');

			if (! $r_reg['loginid'])
			{
				$errors[] = lang('You must enter a username');
			}

			if (! is_array($errors) && $so->account_exists($r_reg['loginid']))
			{
				$errors[] = lang('Sorry, that username is already taken.');
			}

			$ui = createobject('registration.uireg');
			if (is_array($errors))
			{
				$ui->step1($errors,$r_reg,$o_reg);
			}
			else
			{
				$GLOBALS['phpgw']->session->appsession('loginid','registration',$r_reg['loginid']);
				$ui->step2();
			}
		}

		function step2()
		{
			global $config, $r_reg, $o_reg, $PHP_AUTH_USER, $PHP_AUTH_PW;
			//echo '<pre>'; print_r($r_reg); echo '</pre>';

			if ($config['password_is'] == 'http')
			{
				$r_reg['passwd'] = $r_reg['passwd_confirm'] = $PHP_AUTH_PW;
			}

			if (($config['display_tos']) && ! $r_reg['tos_agree'])
			{
				$missing_fields[] = 'tos_agree';
			}

			while (list($name,$value) = each($r_reg))
			{
				if (! $value)
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

			reset ($this->fields);
			while (list (,$field_info) = each ($this->fields))
			{
				$name = $field_info['field_name'];
				$text = $field_info['field_text'];
				$values = explode (',', $field_info['field_values']);
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
					if ($post_value && (!ereg ('@', $post_value) || ! ereg ("\.", $post_value)))
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
					if (!checkdate ($a[$name . '_month'], $a[$name . '_day'], $a[$name . '_year']))
					{
						if ($required == 'Y')
						{
							$errors[] = lang ('You have entered an invalid birthday');
							$missing_fields[] = $name;
						}
					}
					else
					{
							$a[$name] = sprintf ('%s/%s/%s', $a[$name . '_month'], $a[$name . '_day'], $a[$name . '_year']);
					}
				}

				if ($type == 'dropdown')
				{
					if ($post_value)
					{
						while (list (,$value) = each ($values))
						{
							if ($value == $post_value)
							{
								$ok = 1;
							}
						}

						if (!$ok)
						{
							$errors[] = lang ('You specified a value for ' . $text . ' that is not a choice');

							$missing_fields[] = $name;
						}
					}
				}
			}

			while (is_array($o_reg) && list($name,$value) = each($o_reg))
			{
				$fields[$name] = $value;
			}

			if (is_array ($o_reg))
			{
				reset($o_reg);
			}

			if (is_array($missing_fields))
			{
				$errors[] = lang('You must fill in all of the required fields');
			}

			if (! is_array($errors))
			{
				$so     = createobject('registration.soreg');
				$reg_id = $so->step2($fields);
			}

			$ui = createobject('registration.uireg');
			if (is_array($errors))
			{
				$ui->step2($errors,$r_reg,$o_reg,$missing_fields);
			}
			else
			{
				// Redirect them so they don't hit refresh and make a mess
				$GLOBALS['phpgw']->redirect($GLOBALS['phpgw']->link('/registration/main.php','menuaction=registration.uireg.ready_to_activate&reg_id=' . $reg_id));
			}
		}

		function step4()
		{
			global $reg_id;

			$so = createobject('registration.soreg');
			$ui = createobject('registration.uireg');
			$reg_info = $so->valid_reg($reg_id);

			if (! is_array($reg_info))
			{
				$ui->simple_screen('error_confirm.tpl');
				return False;
			}

			$so->create_account($reg_info['reg_lid'],$reg_info['reg_info']);
			$so->delete_reg_info($reg_id);
			setcookie('sessionid');
			setcookie('kp3');
			setcookie('domain');
			$ui->welcome_screen();
		}

		//
		// username
		//
		function lostpw1()
		{
			global $r_reg;

			$so = createobject('registration.soreg');

			if (! $r_reg['loginid'])
			{
				$errors[] = lang('You must enter a username');
			}

			if (! is_array($errors) && !$GLOBALS['phpgw']->accounts->exists($r_reg['loginid']))
			{
				$errors[] = lang('Sorry, that username does not exist.');
			}

			if(! is_array($errors))
			{
			        $error = $so->lostpw1($r_reg['loginid']);
				if($error)
				{
				  $errors[] = $error;
				}
			}
			
			$ui = createobject('registration.uireg');
			if (is_array($errors))
			{
				$ui->lostpw1($errors,$r_reg);
			}
			else
			{
				// Redirect them so they don't hit refresh and make a mess
				$GLOBALS['phpgw']->redirect($GLOBALS['phpgw']->link('/registration/main.php', array('menuaction' => 'registration.uireg.email_sent_lostpw')));
			}
		}

		//
		// link sent by mail
		//
		function lostpw2()
		{
			global $reg_id;

			$so = createobject('registration.soreg');
			$ui = createobject('registration.uireg');
			$reg_info = $so->valid_reg($reg_id);

			if (! is_array($reg_info))
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
			global $r_reg;

			$lid = $GLOBALS['phpgw']->session->appsession('loginid','registration');
			if(!$lid) {
			  $error[] = lang('Wrong session');
			}

			if ($r_reg[passwd] != $r_reg[passwd_2])
			{
			    $errors[] = lang('The two passwords are not the same');
			}

			if (! $r_reg[passwd])
			{
			    $errors[] = lang('You must enter a password');
			}

			if(! is_array($errors))
			{
			  $so = createobject('registration.soreg');
			  $so->lostpw3($lid, $r_reg[passwd]);
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

		function check_select_username ()
		{
			global $config, $PHP_AUTH_USER;

			if ($config['username_is'] == 'choice')
			{
				return True;
			}
			elseif ($config['username_is'] == 'http')
			{
				if (!$PHP_AUTH_USER)
				{
					return "HTTP username is not set";
				}
				else
				{
					$GLOBALS['phpgw']->redirect ($GLOBALS['phpgw']->link ('/registration/main.php', 'menuaction=registration.boreg.step1&r_reg[loginid]=' . $PHP_AUTH_USER));
				}
			}

			return True;
		}

		function check_select_password ()
		{
			global $config, $PHP_AUTH_PW;

			if ($config['password_is'] == 'choice')
			{
				return True;
			}
			elseif ($config['password_is'] == 'http')
			{
				if (!$PHP_AUTH_PW)
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
	}
