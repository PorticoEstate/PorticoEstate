<?php
	/**************************************************************************\
	* phpGroupWare - Registration                                              *
	* http://www.phpgroupware.org                                              *
	* This file written by Jason Wies (Zone) <zone@users.sourceforge.net>    *
	* Based on calendar/inc/class.boholiday.inc.php by Mark Peters             *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	class bomanagefields
	{
		var $public_functions = Array(
			'get_field_list' => True
		);

		var $debug = False;
		var $base_url = '/index.php';

		var $so;
		var $fields;
		
		function bomanagefields()
		{
			$this->so = CreateObject ('registration.somanagefields');
			$this->fields = $this->get_field_list ();
		}

		function save_fields ($fields)
		{
			$current_fields = $this->get_field_list ();

			$name_transforms = array (
				'email'      => 'email',
				'first_name' => 'n_given',
				'last_name'  => 'n_family',
				'address'    => 'adr_one_street',
				'city'       => 'adr_one_locality',
				'state'      => 'adr_one_region',
				'zip'        => 'adr_one_postalcode',
				'country'    => 'adr_one_countryname',
				'gender'     => 'gender',
				'phone'      => 'tel_work',
				'birthday'  => 'bday'
			);

			reset ($fields);
			while (list (,$field_info) = each ($fields))
			{
				$orig_name = $field_info['field_name'];
				unset ($changed);

				if (($field_info['field_type'] == 'text' || $field_info['field_type'] == 'textarea' || $field_info['field_type'] == 'dropdown' || $field_info['field_type'] == 'checkbox') && !$field_info['field_name'])
				{
					continue;
				}

				reset ($name_transforms);
				while (list ($type, $transform_name) = each ($name_transforms))
				{
					if ($field_info['field_type'] == $type)
					{
						$field_info['field_name'] = $transform_name;
						$changed = 1;
						break;
					}
				}

				if ($changed && ($field_info['field_name'] != $orig_name))
				{
					unset ($this->fields[$orig_name]);
				}

				unset ($update);

				if (is_array ($current_fields[$field_info['field_name']]))
				{
					$update = 1;
				}

				if (!$field_info['field_order'])
				{
					$field_info['field_order'] = $this->get_next_field_order ($fields);
				}

				if ($update)
				{
					if ($this->debug)
					{
						echo "<br>UPDATE - $field_info[field_name]";
					}
					$this->so->update_field ($field_info);
				}
				else
				{
					if ($this->debug)
					{
						echo "<br>INSERT - $field_info[field_name]";
					}
					$this->so->insert_field ($field_info);
				}
			}

			reset ($current_fields);
			while (list (,$field_info) = each ($current_fields))
			{
				if (!is_array ($fields[$field_info['field_name']]))
				{
					if ($this->debug)
					{
						echo "<br>REMOVE - $field_info[field_name]";
					}
					$this->so->remove_field ($field_info);
				}
			}
		}

		function get_field_list ()
		{
			$fields = $this->so->get_field_list ();

			$rarray = array ();
			if (is_array ($fields))
			{
				reset ($fields);
				while (list ($num, $field_info) = each ($fields))
				{
					/* Convert the stored database values into comma delimited form */

					$field_values = unserialize (base64_decode ($field_info['field_values']));
					$fields[$num]['field_values'] = $field_values;

					$rarray[$field_info['field_name']] = $fields[$num];
				}
			}

			return $rarray;
		}

		function submit ($post_vars)
		{
			if (!is_array ($post_vars))
			{
				return -1;
			}

			/* This is all we have to do to add a new entry.  Neat, hun? */
			if ($post_vars['reg_new_name'] != 'reg_new' && !$post_vars['reg_new_remove'])
			{
				$this->fields[$post_vars['reg_new_name']] = array (
					'field_name' => 'reg_new'
				);
			}

			$fields = $this->fields;
			reset ($fields);
			while (list ($num, $field_info) = each ($fields))
			{
				$name = $field_info['field_name'];
				if ($post_vars[$name . '_remove'])
				{
					unset ($this->fields[$name]);
				}
				else
				{
					if ($post_vars[$name . '_name'] != $name)
					{
						unset ($this->fields[$name]);
					}

					$updated_field_info = array (
						'field_name'   => $post_vars[$name . '_name'],
						'field_text'   => $post_vars[$name . '_text'],
						'field_type'   => $post_vars[$name . '_type'],
						'field_values' => $post_vars[$name . '_values'],
						'field_required' => $post_vars[$name . '_required'] ? 'Y' : 'N',
						'field_order'  => $post_vars[$name . '_order']
					);

					$this->fields[$post_vars[$name . '_name']] = $updated_field_info;
				}
			}

			$rv = $this->save_fields ($this->fields);

			return $rv;
		}

		function get_next_field_order ($fields)
		{
			reset ($fields);
			while (list (,$field_info) = each ($fields))
			{
				if ($field_info['field_order'] > $max)
				{
					$max = $field_info['field_order'];
				}
			}

			return ($max + 1);
		}

		function check_admin()
		{
			$admin = False;
			if (@$GLOBALS['phpgw_info']['user']['apps']['admin'])
			{
				$admin = True;
			}
				
			if (!$admin)
			{
				Header ('Location: ' . $GLOBALS['phpgw']->link ('/index.php'));
			}
		}
	}
?>
