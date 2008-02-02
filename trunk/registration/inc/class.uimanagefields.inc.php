<?php
	/**************************************************************************\
	* phpGroupWare - Registration                                              *
	* http://www.phpgroupware.org                                              *
	* This file written by Jason Wies (Zone) <zone@users.sourceforge.net>      *
	* Based on calendar/inc/class.uiholiday.inc.php by Mark Peters             *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	class uimanagefields
	{
		var $debug = False;
		var $base_url;
		var $bo;

		var $public_functions = array (
			'admin'  => True,
			'submit' => True
		);

		function uimanagefields ()
		{
			$GLOBALS['phpgw']->nextmatches = CreateObject ('phpgwapi.nextmatchs');

			$this->bo = CreateObject ('registration.bomanagefields');
			$this->bo->check_admin ();
			$this->base_url = $this->bo->base_url;
		}

		function admin ($message = NULL)
		{
			unset ($GLOBALS['phpgw_info']['flags']['noheader']);
			unset ($GLOBALS['phpgw_info']['flags']['nonavbar']);
			$GLOBALS['phpgw_info']['flags']['noappfooter'] = True;
			$GLOBALS['phpgw']->common->phpgw_header ();

			$p = $GLOBALS['phpgw']->template;
			$p->set_file(Array('fields'=>'fields.tpl'));

			if (!isset ($message))
			{
				$message = get_var('messages',Array('GET'));
			}

			$var = array (
				'action_url' => $GLOBALS['phpgw']->link ($this->base_url, 'menuaction=registration.uimanagefields.submit'),
				'message'    => $message,
				'lang_current_fields' => lang ('Current fields:'),
				'lang_name_and_shortdesc' => lang ('Name (blank unless Text, Textarea, Dropdown, Checkbox; else alphanumeric only)'),
				'lang_text'  => lang ('Text'),
				'lang_type'  => lang ('Type'),
				'lang_values_and_shortdesc' => lang ('Values (For Dropdown only; comma separated)'),
				'lang_required' => lang ('Required'),
				'lang_remove'   => lang ('Remove'),
				'lang_order'    => lang ('Order'),
				'lang_textarea' => lang ('Textarea'),
				'lang_dropdown' => lang ('Dropdown'),
				'lang_checkbox' => lang ('Checkbox'),
				'lang_email'    => lang ('Email'),
				'lang_first_name' => lang ('First Name'),
				'lang_last_name'  => lang ('Last Name'),
				'lang_address'  => lang ('Address'),
				'lang_city'     => lang ('City'),
				'lang_state'    => lang ('State'),
				'lang_zip'      => lang ('ZIP/Postal'),
				'lang_country'  => lang ('Country'),
				'lang_gender'   => lang ('Gender'),
				'lang_phone'    => lang ('Phone'),
				'lang_birthday' => lang ('Birthday'),
				'lang_cancel'   => lang ('Cancel'),
				'lang_update_add' => lang ('Update/Add')
			);
			$p->set_var ($var);

			$row_color = $GLOBALS['phpgw']->nextmatches->alternate_row_color ($row_color);
			$var = Array(
				'row_off' => $GLOBALS['phpgw']->nextmatches->alternate_row_color ($row_color)
			);
			$p->set_var ($var);

			$row_color = $GLOBALS['phpgw']->nextmatches->alternate_row_color ($row_color);
			$var = Array(
				'row_on' => $GLOBALS['phpgw']->nextmatches->alternate_row_color ($row_color)
			);
			$p->set_var ($var);

			$p->set_block ('fields', 'info', 'info_list');

			$fields = $this->bo->fields;
			while (list ($num, $field_info) = each ($fields))
			{
				unset ($field_required);

				if ($field_info['field_required'] == 'Y')
				{
					$field_required = "checked";
				}

				$var = array (
					'field_short_name' => $field_info['field_name'],
					'field_name' => $field_info['field_name'],
					'field_text' => $field_info['field_text'],
					'field_type' => $field_info['field_type'],
					'field_type_selected_text' => '',
					'field_type_selected_textarea' => '',
					'field_type_selected_dropdown' => '',
					'field_type_selected_checkbox' => '',
					'field_type_selected_email' => '',
					'field_type_selected_first_name' => '',
					'field_type_selected_last_name' => '',
					'field_type_selected_address' => '',
					'field_type_selected_city' => '',
					'field_type_selected_state' => '',
					'field_type_selected_zip' => '',
					'field_type_selected_country' => '',
					'field_type_selected_gender' => '',
					'field_type_selected_phone' => '',
					'field_type_selected_birthday' => '',
					'field_type_selected_' . $field_info['field_type'] => 'selected',
					'field_values' => $field_info['field_values'],
					'field_required' => $field_required,
					'field_order' => $field_info['field_order']
				);

				$p->set_var ($var);
				$p->parse ('info_list', 'info', True);
			}

			/* Add an empty entry line */
			$var = array (
				'field_short_name' => 'reg_new',
				'field_name' => '',
				'field_text' => '',
				'field_type' => '',
				'field_type_selected_text'       => '',
				'field_type_selected_textarea'   => '',
				'field_type_selected_dropdown'   => '',
				'field_type_selected_checkbox'   => '',
				'field_type_selected_email'      => '',
				'field_type_selected_first_name' => '',
				'field_type_selected_last_name'  => '',
				'field_type_selected_address'    => '',
				'field_type_selected_city'       => '',
				'field_type_selected_state'      => '',
				'field_type_selected_zip'        => '',
				'field_type_selected_country'    => '',
				'field_type_selected_gender'     => '',
				'field_type_selected_phone'      => '',
				'field_type_selected_birthday'   => '',
				'field_values'   => '',
				'field_required' => '',
				'field_remove'   => '',
				'field_order'    => ''
			);

			$p->set_var ($var);
			$p->parse ('info_list', 'info', True);

			$p->pfp ('out', 'fields');
		}

		function submit ()
		{
			$this->bo->check_admin ();

			$post_vars = $GLOBALS['HTTP_POST_VARS'];

			unset($GLOBALS['phpgw_info']['flags']['noheader']);
			unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
			$GLOBALS['phpgw_info']['flags']['noappfooter'] = True;

			$this->bo->submit ($post_vars);

			Header ('Location: ' . $GLOBALS['phpgw']->link ($this->base_url, 'menuaction=registration.uimanagefields.admin&message=Updated'));
		}
	}
?>
