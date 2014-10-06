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

	/* $Id$ */

	/**
	* Import the jQuery class
	*/
	phpgw::import_class('phpgwapi.jquery');

	class uireg
	{
		var $template;
		var $bomanagefields;
		var $first_location = false;
		var $fields;
		var $bo;
		var $config;
		var $public_functions = array(
			'step1'   => True,
			'step2'   => True,
			'lostpw1' => True,
			'lostpw3' => True,
			'lostpw4' => True,
			'ready_to_activate' => True,
			'email_sent_lostpw' => True,
			'tos'     => True
		);

		function uireg()
		{
			$this->template = $GLOBALS['phpgw']->template;
			$this->bo = createobject ('registration.boreg');
			$this->bomanagefields = createobject ('registration.bomanagefields');
			$this->fields = $this->bomanagefields->get_field_list();
			$this->config = $this->bo->config;
		}

		function set_header_footer_blocks()
		{
			$this->template->set_file(array(
				'_layout' => 'layout.tpl'
			));
			$this->template->set_block('_layout','header');
			$this->template->set_block('_layout','footer');
		}

		function header($sub = '')
		{
			if(!$sub)
			{
				$sub = lang('Account registration');
			}
			$this->set_header_footer_blocks();
			$this->template->set_var('lang_header', $GLOBALS['phpgw_info']['server']['system_name'] . ' :: ' . $sub);
			$this->template->pfp('out','header');
		}

		function footer()
		{
			$this->template->pfp('out','footer');
		}

		function step1($errors = '',$r_reg = '',$o_reg = '')
		{
			if ($errors && $this->config['username_is'] == 'http')
			{
				$this->simple_screen ('error_general.tpl', $GLOBALS['phpgw']->common->error_list ($errors));
			}

			$show_username_prompt = True;
			/* Note that check_select_username() may not return */
			$select_username = $this->bo->check_select_username();
			if (!$select_username || is_string ($select_username))
			{
				$this->simple_screen ('error_general.tpl', $GLOBALS['phpgw']->common->error_list (array ($select_username)));
			}

			$this->header();
			$this->template->set_file(array(
				'_loginid_select' => 'loginid_select.tpl'
			));
			$this->template->set_block('_loginid_select','form');

			if ($errors)
			{
				$this->template->set_var('errors',$GLOBALS['phpgw']->common->error_list($errors));
			}

			$this->template->set_var('form_action',$GLOBALS['phpgw']->link('/registration/main.php',array('menuaction'=>'registration.boreg.step1', 'logindomain' => $_REQUEST['logindomain'] )));
			$this->template->set_var('lang_username',lang('Username'));
			$this->template->set_var('lang_submit',lang('Submit'));

			$var = $this->get_username_fields();
			$this->template->set_var($var);
			$this->template->pfp('out','form');

			$this->footer();
		}


		function get_username_fields()
		{
			$values = array();
			if( $GLOBALS['phpgw_info']['server']['domain_from_host'] 
				&& !$GLOBALS['phpgw_info']['server']['show_domain_selectbox'] )
			{
				$values = array
				(
					'domain_select'	=> '',
					'domain_from_host'	=> "<input type = 'hidden' name='logindomain' id='logindomain' value ='{$_SERVER['SERVER_NAME']}' />"
				);
			}
			elseif( $GLOBALS['phpgw_info']['server']['show_domain_selectbox'] )
			{

				$lang_domain = lang('domain');
				$domain_select =  <<<HTML
		<td>{$lang_domain}</td>
		<td>
			<select name="logindomain" id="logindomain" class="inputbox">
HTML;


				$options = '';
				foreach($GLOBALS['_phpgw_domain'] as $domain_name => $domain_vars)
				{
					$selected = '';
					if (isset($_REQUEST['logindomain']) && $_REQUEST['logindomain'] == $domain_name)
					{
						$selected = 'selected =  "selected"';
					}

					$domain_display_name = str_replace('_', ' ', $domain_name);
					$options .=  <<<HTML
					<option value='{$domain_name}'{$selected}>{$domain_display_name}</option>
HTML;
				}

				$domain_select .=  <<<HTML
				{$options}
			</select>
		</td>
HTML;

				$values = array
				(
					'domain_select'	=> $domain_select,
					'domain_from_host'	=> ''
				);
			}
			else
			{
				$values = array
				(
					'domain_select'	=> '',
					'domain_from_host'	=> ''
				);
			}
			return $values;
		}

		function step2($errors = array(),$r_reg = '',$o_reg = '',$missing_fields='')
		{
			phpgwapi_jquery::load_widget('core');
			$GLOBALS['phpgw']->js->validate_file('portico', 'ajax', 'registration');

			$show_password_prompt = True;
			$select_password = $this->bo->check_select_password();
			if (is_string ($select_password))
			{
				$this->simple_screen ('error_general.tpl', $select_password);
			}
			elseif (!$select_password)
			{
				$show_password_prompt = False;
			}

			$this->header();
			$this->template->set_file(array(
				'_personal_info' => 'personal_info.tpl'
			));
			$this->template->set_block('_personal_info','form');

			$tpl_vars = array
			(
				'css'			=> $GLOBALS['phpgw']->common->get_css(),
				'javascript'	=> $GLOBALS['phpgw']->common->get_javascript(),
				'str_base_url'	=> $GLOBALS['phpgw']->link('/', array('logindomain' => $_REQUEST['logindomain']), true),
			);

			$this->template->set_var($tpl_vars);

			if ($missing_fields)
			{
				while (list(,$field) = each($missing_fields))
				{
					$missing[$field] = True;
					$this->template->set_var('missing_' . $field,'<font color="#CC0000">*</font>');
				}
			}

			if (is_array($r_reg))
			{
				while (list($name,$value) = each($r_reg))
				{
					$post_values[$name] = $value;
					$this->template->set_var('value_' . $name,$value);
				}
			}

			if (is_array($o_reg))
			{
				while (list($name,$value) = each($o_reg))
				{
					$post_values[$name] = $value;
					$this->template->set_var('value_' . $name,$value);
				}
			}

//----
			if($this->config['username_is'] == 'email')
			{
				$this->template->set_var('message',lang('username as email'));
				$username_fields = $this->get_username_fields();
				$username_fields['lang_username'] = '<b>' . lang('username') . '</b>';
				$username_fields['value_username'] = $GLOBALS['phpgw']->session->appsession('loginid','registration');
				$this->template->set_var($username_fields);
			}
			else
			{
				$this->template->set_block ('form', 'username', 'empty');		
			}
//-------

			if ($errors)
			{
				$this->template->set_var('errors',$GLOBALS['phpgw']->common->error_list($errors));
			}

			$this->template->set_var('form_action',$GLOBALS['phpgw']->link('/registration/main.php',array('menuaction'=>'registration.boreg.step2','logindomain' => $_REQUEST['logindomain'])));
			$this->template->set_var('lang_password',lang('Password'));
			$this->template->set_var('lang_reenter_password',lang('Re-enter password'));
			$this->template->set_var('lang_submit',lang('Submit'));

			if (!$show_password_prompt)
			{
				$this->template->set_block ('form', 'password', 'empty');
			}

			$this->template->set_block ('form', 'other_fields_proto', 'other_fields_list');
			reset ($this->fields);
			
//_debug_array($this->fields);
			foreach ($this->fields as $num => $field_info)
			{
				$input_field = $this->get_input_field ($field_info, $post_values);

				$var = array (
					'missing_indicator' => $missing[$field_info['field_name']] ? '<font color="#CC0000">*</font>' : '',
					'bold_start'  => $field_info['field_required'] == 'Y' ? '<b>' : '',
					'bold_end'    => $field_info['field_required'] == 'Y' ? '</b>' : '',
					'lang_displayed_text' => ltrim(lang ($field_info['field_text']),'!'),
					'input_field' => $input_field
				);

				$this->template->set_var ($var);

				$this->template->parse ('other_fields_list', 'other_fields_proto', True);
			}

			if ($this->config['display_tos'])
			{
			$this->template->set_var('tos_link',$GLOBALS['phpgw']->link('/registration/main.php', array('menuaction' => 'registration.uireg.tos','logindomain' => $_REQUEST['logindomain'])));
			$this->template->set_var('lang_tos_agree',lang('I have read the terms and conditions and agree by them.'));
			if ($r_reg['tos_agree'])
			{
				$this->template->set_var('value_tos_agree', 'checked');
			}
			}
			else
			{
				$this->template->set_block ('form', 'tos', 'blank');
			}

			$this->template->pfp('out','form');
			$this->footer();
		}

		//
		// username
		//
		function lostpw1($errors = '',$r_reg = '')
		{
			$sub = lang('lost password');
			$this->header($sub);
			$this->template->set_file(array(
				'_lostpw_select' => 'lostpw_select.tpl'
			));
			$this->template->set_block('_lostpw_select','form');

			if ($errors)
			{
				$this->template->set_var('errors',$GLOBALS['phpgw']->common->error_list($errors));
			}

			$this->template->set_var('form_action',$GLOBALS['phpgw']->link('/registration/main.php',array('menuaction'=>'registration.boreg.lostpw1','logindomain' => $_REQUEST['logindomain'])));
			$this->template->set_var('lang_explain',lang('After you enter your username, instructions to change your password will be sent to you by e-mail to the address you gave when you registered.'));
			$this->template->set_var('lang_username',lang('Username'));
			$this->template->set_var('lang_submit',lang('Submit'));

			$this->template->pfp('out','form');
			$this->footer();
		}

		//
		// change password
		//
		function lostpw3($errors = '',$r_reg = '',$lid = '')
		{
			$this->header();
			$this->template->set_file(array(
				'_lostpw_change' => 'lostpw_change.tpl'
			));
			$this->template->set_block('_lostpw_change','form');

			if ($errors)
			{
				$this->template->set_var('errors',$GLOBALS['phpgw']->common->error_list($errors));
			}

			$this->template->set_var('form_action',$GLOBALS['phpgw']->link('/registration/main.php',array('menuaction'=>'registration.boreg.lostpw3','logindomain' => $_REQUEST['logindomain'])));
			$this->template->set_var('value_username', $lid);
			$this->template->set_var('lang_changepassword',lang("Change password for user"));
			$this->template->set_var('lang_enter_password',lang('Enter your new password'));
			$this->template->set_var('lang_reenter_password',lang('Re-enter your password'));
			$this->template->set_var('lang_change',lang('Change'));

			$this->template->pfp('out','form');
			$this->footer();
		}

		//
		// password was changed
		//
		function lostpw4()
		{
			$this->header();
			$this->template->set_file(array(
				'screen' => 'lostpw_changed.tpl'
			));
			$this->template->set_var('login_url',$GLOBALS['phpgw_info']['server']['webserver_url']);

			$this->template->pfp('out','screen');
			$this->footer();
		}

		function get_input_field ($field_info, $post_values)
		{
			$post_value = $post_values[$field_info['field_name']];

			$name = $field_info['field_name'];
			$values = explode (",", $field_info['field_values']);
			$required = $field_info['field_required'];
			$type = $field_info['field_type'];

			if (!$type)
			{
				$type = 'text';
			}

			if ($type == 'gender')
			{
				$values = array (
					'Male',
					'Female'
				);

				$type = 'dropdown';
			}

			if ($required == 'Y')
			{
				$a = 'r_reg';
			}
			else
			{
				$a = 'o_reg';
			}

			if ($type == 'text' || $type == 'email' || $type == 'first_name' ||
				$type == 'last_name' || $type == 'address' || $type == 'city' ||
				$type == 'zip' || $type == 'phone')
			{
				$rstring = '<input type=text name="' . $a . '[' . $name . ']" value="' . $post_value . '">';
			}

			if ($type == 'textarea')
			{
				$rstring = '<textarea name="' . $a . '[' . $name . ']" value="' . $post_value . '" cols="40" rows="5">' . $post_value . '</textarea>';
			}

			if ($type == 'dropdown')
			{
				if (!is_array ($values))
				{
					$rstring = "Error: Dropdown list '$name' has no values";
				}
				else
				{
					$rstring = "<select id=\"{$name}\" name=\"{$a}[{$name}]\"><option value=\"\"> </option>";
					foreach ($values as $value)
					{
						$value = trim ($value);

						$selected = '';
						if ($value == $post_value)
						{
							$selected = "selected";
						}

						$rstring .= '<option value="' . $value . '" ' . $selected . '>' . $value . '</option>';
					}

					$rstring .= "</select>";
				}
			}

			if ($type == 'checkbox')
			{
				unset ($checked);
				if ($post_value)
					$checked = "checked";

				$rstring = '<input type=checkbox name="' . $a . '[' . $name . ']" ' . $checked . '>';
			}

			if ($type == 'birthday' || $type == 'state' || $type == 'country')
			{
				$sbox = createobject ('phpgwapi.sbox');
			}

			if ($type == 'state')
			{
//				$rstring = $sbox->list_states ($a . '[' . $name . ']', $post_value);
			}

			if ($type == 'country')
			{
				$rstring = $sbox->country_select($post_value, $a . '[' . $name . ']');
				
				$rstring = str_replace('<select', '<select id = "country"', $rstring);
			}

			if ($type == 'birthday')
			{
				$rstring = $sbox->getmonthtext ($a . '[' . $name . '_month]', $post_values[$name . '_month']);
				$rstring .= $sbox->getdays ($a . '[' . $name . '_day]', $post_values[$name . '_day']);
				$rstring .= $sbox->getyears ($a . '[' . $name . '_year]', $post_values[$name . '_year'], 1900, date ('Y') + 1);
			}

			if ($type == 'location')
			{
				if($post_value)
				{
					$rstring = "<input id=\"{$name}\" type=\"text\" name=\"{$a}[{$name}]\" value=\"{$post_value}\">";
				}
				else
				{
				
				$rstring = <<<HTML
				<select id="{$name}" name="{$a}[{$name}]">
HTML;
				if(!$this->first_location)
				{
					$field_info_arr = explode('::', $this->fields[$name]['field_values']);
					$criteria = array
					(
						'location_code'	=> '',//$location_code,
						'child_level'	=> (int) ltrim($name, 'loc'),
						'field_name'	=> isset($field_info_arr[2]) && $field_info_arr[2] ? $field_info_arr[2] : "{$name}_name"
					);

					$locations = execMethod('property.solocation.get_children',$criteria);
					array_unshift($locations, array('id' => '', 'name' => lang('select')));

					foreach ($locations as $location)
					{
						$selected = $location['id'] == $post_value ? ' selected = "selected"' : '';
						$rstring .= <<<HTML
						<option value ="{$location['id']}" $selected>{$location['name']}</option>
HTML;
					}
				}

				$rstring .= <<<HTML
				</select>
HTML;
				}
				$this->first_location = true;
			}

			return $rstring;
		}

		function simple_screen($template_file, $text = '')
		{
			$this->header();
			$this->template->set_file(array(
				'screen' => $template_file
			));

			if ($text)
			{
				$this->template->set_var ('extra_text', $text);
			}

			$this->template->pfp('out','screen');
			$this->footer();
			exit;
		}

		function ready_to_activate()
		{
			$reg_id = phpgw::get_var('reg_id');

			if ($this->config['activate_account'] == 'email')
			{
				$this->simple_screen('confirm_email_sent.tpl');
			}
			else if ($this->config['activate_account'] == 'pending_approval')
			{
				$this->simple_screen('confirm_pending_approval.tpl');
			}
			else
			{
				/* ($this->config['activate_account'] == 'immediately') */
				$GLOBALS['phpgw']->redirect_link('/registration/main.php',array('menuaction'=>'registration.boreg.step4', 'reg_id' => $reg_id,'logindomain' => $_REQUEST['logindomain']));
			}
		}

		function email_sent_lostpw()
		{
			$this->simple_screen('confirm_email_sent_lostpw.tpl');
		}

		function welcome_screen()
		{
			$this->header();
			$this->template->set_file(array(
				'screen' => 'welcome_message.tpl'
			));
			$this->template->set_var('login_url',$GLOBALS['phpgw_info']['server']['webserver_url']);

			$this->template->pfp('out','screen');
			$this->footer();
		}

		function tos()
		{
			$this->simple_screen('tos.tpl');
		}
	}
