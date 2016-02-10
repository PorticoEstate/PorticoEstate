<?php
  /**************************************************************************\
  * phpGroupWare - catalog_manager                                           *
  * http://www.phpgroupware.org                                              *
  * This program is part of the GNU project, see http://www.gnu.org/         *
  *                                                                          *
  * Copyright 2003, 2008 Free Software Foundation, Inc.                      *
  *                                                                          *
  * Originally Written by Jonathan Alberto Rivera Gomez - jarg at co.com.mx  *
  * Current Maintained by Jonathan Alberto Rivera Gomez - jarg at co.com.mx  *
  * Written by Dave Hall <skwashd@phpgroupware.org>							 *
  * --------------------------------------------                             *
  * Development of this application was funded by http://www.sogrp.com       *
  * --------------------------------------------                             *
  *  This program is Free Software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

	class addressbook_catalog_manager
	{
		var $form_action;
		var $catalog_name;
		var $headers;
		var $array_name;
		var $objs_data;
		var $index;
		var $entry;
		var $action;
		var $form_fields;
		var $title;
		var $catalog_button_name;
		var $num_cols;
		var $key_edit_id;

		function __construct()
		{
		}

		function create_window($catalog_name, $entry, $title)
		{
			$GLOBALS['phpgw']->common->phpgw_header(true);

			$this->nextmatchs =& $GLOBALS['phpgw']->nextmatchs;
			$this->template =& $GLOBALS['phpgw']->template;

			$this->template->set_root(PHPGW_APP_TPL);

			$this->main_form($catalog_name, $entry, $title);
		}

		/**
		* Start to draw the html screens
		*
		* @param
		* @return
		*/
		function main_form($catalog_name, $entry, $title)
		{
			$this->template->set_file('form', 'form.tpl');

			$vars = array
			(
				'principal_tabs_inc'	=> '',
				'action'				=> $GLOBALS['phpgw']->link('/index.php', $this->form_action),
				'tab'					=> '',
				'current_tab_body'		=> $this->current_body($catalog_name, $entry, $title),
				'control_buttons'		=> ''
			);
			$this->template->set_var($vars);

			$this->template->pparse('out', 'form');
		}

		function current_body($catalog_name, $entry, $title)
		{
			$this->form_start();

			$this->template->set_var('lang_general_data', $title);

			$this->template->set_var('current_id_name', $this->key_edit_name);
			$this->template->set_var('current_id', $this->key_edit_id);

			$this->set_form_fields($this->form_fields);

			$this->select_catalog();

			$this->template->set_var('detail_fields', $this->get_detail_form($catalog_name, $this->headers,
											 $this->array_name, $this->objs_data,
											 $this->index));

			$this->form_end();

			return $this->template->fp('out', 'tab_body_general_data');
		}

		/**
		* Draw the detail form, this form is as show in comms, addr and othes windows
		*
		* @param string $tab The name which identify the section
		* @param array $headers Array with all headers which you want to show in the form
		* @param string $array_name The array name from you want to show data
		* @param array $objs_data Array with all properties of all data which you want to show
		* @param string $idx The index name (for edit mode use)
		* @param boolean $button Flag for indicate if you want draw the Add button
		* @return string All the detail form html code in the template
		*/
		function get_detail_form($catalog_name, $headers, $array_name, $objs_data, $idx, $button=True)
		{
			$this->template->set_file(array('detail_data'   => 'body_detail.tpl'));
			$this->template->set_block('detail_data','detail_body','detail_body');
			$this->template->set_block('detail_data','input_detail_row','input_detail_row');
			$this->template->set_block('detail_data','input_detail_data','input_detail_data');

			$add_button = '<input type="submit" name="'. $this->catalog_button_name .'" value="Add">';

			if($button)
			{
				//$this->template->set_var('caption_detail', $title);
				$this->template->set_var('add_button', $add_button);
			}
			else
			{
				$this->template->set_var('caption_detail', '');
				$this->template->set_var('add_button', '');
			}

			$this->template->set_var('row_class', 'th');
 			$row_class = 'row_on';

			$cols='';
			foreach($headers as $head)
			{
				$cols .= '<td>' . $head . '</td>';
			}

			$this->template->set_var('input_cols', $cols);

			$this->template->fp('input_detail', 'input_detail_data', True);
			$this->template->fp('detail_body_set', 'input_detail_row');

			if (is_array($this->$array_name))
			{
				foreach($this->$array_name as $k => $v)
				{
					$this->array_value = $v;

					$row_class = $this->nextmatchs->alternate_row_class($row_class);
					$this->template->set_var('row_class', $row_class);

					$cols='';
					reset($objs_data);
					foreach($objs_data as $type => $properties)
					{
						$cols .= '<td>' . $this->get_column_data($properties) . '</td>';
					}

					$this->template->set_var('input_cols', $cols);

					$this->template->fp('input_detail', 'input_detail_data', True);
					$this->template->fp('detail_body_set', 'input_detail_row');
				}
			}
			return $this->template->fp('out', 'detail_body');
		}

		/**
		* This function initialize the template for draw the tabs windows
		*
		* @param
		* @return
		*/
		function form_start()
		{
			$this->template->set_file(array('person_data'	=> 'current_catalog_body.tpl'));
			$this->template->set_block('person_data','tab_body_general_data','general_data');
 			$this->template->set_block('person_data','input_data','input_data');
		}

		/**
		* This function end the template for draw the tabs windows
		*
		* @param
		* @return
		*/
		function form_end()
		{
		}

		function set_form_fields($form_fields)
		{
 		//	$tr_color = $GLOBALS['phpgw_info']['theme']['row_on'];
			$row_class = 'row_on';
			ksort($form_fields, SORT_NUMERIC);
			$last_element = count($form_fields);
			$cols='';
			$count=1;

			foreach($form_fields as $key => $row)
			{
				$row_class = $this->nextmatchs->alternate_row_class($row_class);
				$cols = $cols . '<td>'.$row[0].'</td>'.'<td>'.$row[1].'</td>';;
				if($count == $this->num_cols || $key==$last_element)
				{
					$this->template->set_var('input_fields_cols', $cols);
					$this->template->fp('input_fields', 'input_data', True);
					$cols = '';
					$count=0;
				}
				$count ++;
			}
		}

		function set_row_input($field_name, $input_name, $input_value, $col)
		{
			if ($col==1)
			{
				$this->template->set_var('field_name_one', $field_name);
				$this->template->set_var('input_name_one', $input_name);
				$this->template->set_var('input_value_one', $input_value);
			}
			else
			{
				$this->template->set_var('field_name_two', $field_name);
				$this->template->set_var('input_name_two', $input_name);
				$this->template->set_var('input_value_two', $input_value);
			}
		}

		function set_row_other_input($field_name, $field_value, $col)
		{
			if ($col==1)
			{
				$this->template->set_var('field_other_name1', $field_name);
				$this->template->set_var('value_other_name1', $field_value);
			}
			else
			{
				$this->template->set_var('field_other_name2', $field_name);
				$this->template->set_var('value_other_name2', $field_value);
			}
		}

		function get_column_data($properties=array())
		{
			switch($properties['type'])
			{
				case 'data':
					$column_data = $this->array_value[$properties['field']];
					break;

				case 'text':
					$sub_name = '';
					if(isset($properties['field']) && $properties['field']!='')
					{
						$sub_name = "[{$this->array_value[$properties['field']]}] ";
					}
					if ( isset($this->array_value[$properties['value']]) )
					{
						$value = $this->array_value[$properties['value']];
					}
					else
					{
						$value = $properties['value'];
					}
					$name = $properties['name'] . $sub_name;
					$column_data = '<input type="text" name="'.$name.'" value="'.$value.'">';
					break;

				case 'radio':
					$checked = '';
					if ( $this->array_value[$properties['field']] == 'Y' )
					{
						$checked=' checked';
					}

					$column_data = "<input type=\"radio\" name=\"{$properties['name']}\""
									. "\" value=\"{$this->array_value[$properties['value']]}\"{$checked}>";
					break;

				case 'link':
					if (isset($properties['extra']) && $properties['extra'])// Sigurd: not sure where the 'extra' comes from
					{
						echo '$properties[extra] should be a part of the link</br>';
						_debug_array($properties['extra']);
					}
					$link = $GLOBALS['phpgw']->link('/index.php', array_merge($this->form_action,
								array($properties['action'] => $this->array_value[$properties['key']])));
					$column_data = '<a href="'.$link.'">'.$properties['mode'].'</a>';
					break;

				case 'combo':
					$column_data = '<select name="'.$properties['name'].'">'.$properties['value'].'</select>';
					break;
			}
			return $column_data;
		}

		function get_vars()
		{
			$this->entry = phpgw::get_var('entry');

			if ( phpgw::get_var($this->catalog_name.'_add_row') )
			{
				$this->action = 'insert';
			}
			else if ( phpgw::get_var($this->catalog_name.'_update_row') )
			{
				$this->action = 'update';
				$this->key = phpgw::get_var($this->key_edit_name);
			}
			else if ( phpgw::get_var($this->catalog_name.'_del_row') )
			{
				$this->action = 'delete';
				$this->key = phpgw::get_var($this->catalog_name.'_del_row');
			}
			else if ( phpgw::get_var($this->catalog_name.'_edit_row') )
			{
				$this->action = 'edit';
				$this->key = phpgw::get_var($this->catalog_name.'_edit_row');
			}
		}

		function validate_action($action='')
		{
			switch($action)
			{
				case 'insert':
					$this->insert($this->entry);
					unset($this->entry);
					break;

				case 'delete':
					$this->delete($this->key);
					unset($this->key);
					break;

				case 'edit':
					$this->edit($this->key);
					break;

				case 'update':
					$this->update($this->key, $this->entry);
					break;
			}
		}
	}
