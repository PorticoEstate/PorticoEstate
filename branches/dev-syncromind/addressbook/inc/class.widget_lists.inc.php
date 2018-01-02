<?php
  /**************************************************************************\
  * phpGroupWare - widget_lists						     *
  * http://www.phpgroupware.org                                              *
  * This program is part of the GNU project, see http://www.gnu.org/         *
  *                                                                          *
  * Copyright 2003 Free Software Foundation, Inc.                            *
  *                                                                          *
  * Originally Written by Jonathan Alberto Rivera Gomez - jarg at co.com.mx  *
  * Current Maintained by Jonathan Alberto Rivera Gomez - jarg at co.com.mx  *
  * --------------------------------------------                             *
  * Development of this application was funded by http://www.sogrp.com       *
  * --------------------------------------------                             *
  *  This program is Free Software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/
	class widget_lists
	{
		var $title;
		var $form_name;
		var $use_combos;
		
		/**
		* Set the parameters for the list box with all options
		*	     
		* @param string $title The title of the widget
		* @param array $form_name The name form where it will be this widget
		* @param string $use_combos Use True if you want to have combos in your widget
		*/
		function __construct($title, $form_name, $use_combos=True, $use_filter=False)
		{
			$this->template	= &$GLOBALS['phpgw']->template;
			$this->title = $title;
			$this->form_name = $form_name;
			$this->use_combos = $use_combos;
			$this->use_filter = $use_filter;
		}
		
		/**
		* Draw the widget
		*	     
		* @return string The html code for this widget
		*/
		function get_widget()
		{
			$this->template->set_root($GLOBALS['phpgw']->common->get_tpl_dir('addressbook'));
			$this->template->set_file(array('widget_list_t' => 'widget_lists.tpl'));
			$this->template->set_block('widget_list_t', 'many_actions', 'many_actions');
			$this->template->set_block('widget_list_t', 'combos', 'combos');
			$this->template->set_block('widget_list_t', 'option_filter', 'option_filter');
			
			$this->template->set_var('lang_general_title', $this->title);
			$this->template->set_var('widget_list_form_name', $this->form_name);
			
			if($this->use_combos)
			{
				$this->template->set_var('lang_left_combo_title', $this->left_combo_title);
				$this->template->set_var('left_combo', $this->left_combo);
				$this->template->set_var('lang_right_combo_title', $this->right_combo_title);
				$this->template->set_var('right_combo', $this->right_combo);
				$this->template->parse('combos_lists', 'combos');
			}

			if($this->use_filter)
			{
				$this->template->set_var('filter_by_label',
							 lang('Filter by: '));
				$this->template->set_var('filter_by_option_list',
							 $this->get_list_filters());
				$this->template->set_var('search_by_label',
							 lang('Search: '));
				$this->template->set_var('all_option_list_filter', 
							 $this->get_list_filters());
				$this->template->parse('all_option_list_filter_body', 
						       'option_filter');
			}

			if(is_array($this->selected_option_list))
			{
				$this->all_option_list = array_diff($this->all_option_list, $this->selected_option_list);
			}
			else
			{
				$this->all_option_list = $this->all_option_list;
			}
			
			$this->template->set_var('lang_all_option_list_title', $this->all_option_list_title);
			$this->template->set_var('all_option_list_name', $this->all_option_list_name);
			$this->template->set_var('all_option_list', $this->get_option_list($this->all_option_list));
			$this->template->set_var('lang_selected_option_list_title', $this->selected_option_list_title);
			$this->template->set_var('selected_option_list_name', $this->selected_option_list_name);
			$this->template->set_var('selected_option_list', $this->get_option_list($this->selected_option_list));

			if($this->right_combo_link_opt)
			{
				$this->template->set_var('current_opt', $this->right_combo_name);
			}
			elseif($this->left_combo_link_opt)
			{
				$this->template->set_var('current_opt', $this->left_combo_name);
			}
			
			$this->template->set_var('th_bg',   $GLOBALS['phpgw_info']['theme']['th_bg']);
			$this->template->set_var('th_text', $GLOBALS['phpgw_info']['theme']['th_text']);
			$this->template->set_var('row_on',  $GLOBALS['phpgw_info']['theme']['row_on']);
			$this->template->set_var('row_off', $GLOBALS['phpgw_info']['theme']['row_off']);
			$this->template->set_var('row_text',$GLOBALS['phpgw_info']['theme']['row_text']);

			return $this->template->fp('out', 'many_actions');
		}

		/**
		* Set the parameters for the list box with all options
		*	     
		* @param string $title The title of the list box
		* @param array $list_name The name of the list box
		* @param string $list The array with the data for this list box ($list[$key] = $value)
		* @return mixed The option list box which have all options
		*/
		function set_all_option_list($title, $list_name, $list=array())
		{
			if(!is_array($list))
			{
				$list = array();
			}
			
			$this->all_option_list_title = $title;
			$this->all_option_list = $list;
			$this->all_option_list_name = $list_name;
		}

		/**
		* Set the parameters for the list box with selected options
		*	     
		* @param string $title The title of the list box
		* @param array $list_name The name of the list box
		* @param string $list The array with the data for this list box ($list[$key] = $value)
		* @return mixed The option list box which have selected options
		*/
		function set_selected_option_list($title, $list_name, $list=array())
		{
			if(!is_array($list))
			{
				$list = array();
			}

			$this->selected_option_list_title = $title;
			$this->selected_option_list = $list;
			$this->selected_option_list_name = $list_name;
			$this->old_option_list = $list;
		}

		/**
		* Set the parameters for the left combo
		*	     
		* @param string $title The title of the combo
		* @param array $list_name The name of the combo
		* @param string $list The array with the data for this list box ($list[$key] = $value)
		* @param string $selected The option selected for this combo
		* @param boolean $use_js For use javascript
		* @return mixed The left combo box 
		*/
		function set_left_combo($title, $list_name, $list=array(), $selected='', $use_js=False, $link_opt=False)
		{
			$this->left_combo_title = $title;
			$this->left_combo_name = $list_name;
			$this->left_combo = $this->get_combo($list_name, $list, $selected, $use_js);
			$this->left_combo_link_opt = $link_opt;
		}

		function set_left_text($title, $list_name, $value='')
		{
			$this->left_combo_title = $title;
			$this->left_combo = $this->get_text($list_name, $value);
		}
		
		/**
		* Set the parameters for the right combo
		*	     
		* @param string $title The title of the combo
		* @param array $list_name The name of the combo
		* @param string $list The array with the data for this list box ($list[$key] = $value)
		* @param string $selected The option selected for this combo
		* @param boolean $use_js For use javascript
		* @return mixed The right combo box 
		*/
		function set_right_combo($title, $list_name, $list=array(), $selected='', $use_js=False, $link_opt=False)
		{
			$this->right_combo_title = $title;
			$this->right_combo_name = $list_name;
			$this->right_combo = $this->get_combo($list_name, $list, $selected, $use_js);
			$this->right_combo_link_opt = $link_opt;
		}
		
		/**
		* Get the records to process, new and deleted
		*	     
		* @return array The array with all new records and deleted 
		* ($array = array(delete => values, insert => values, edit => values))
		*/
		function get_resul_list()
		{
			$pos = strpos($this->selected_option_list_name, '[]');
			$var_option_name = $pos?substr($this->selected_option_list_name,0,$pos):$this->selected_option_list_name;
			return $this->diff_arrays(array_keys($this->old_option_list), phpgw::get_var($var_option_name));
		}
		
		/**
		* Compare two arrays and return the diferences
		*	     
		* @param array $old_array The array with old options
		* @param array $new_array The array with new options
		* @return array The array with diferences
		* ($array = array(delete => values, insert => values, edit => values))
		*/
		function diff_arrays($old_array=array(), $new_array=array())
		{
			if(!is_array($old_array))
			{
				$old_array =  array();
			}
			
			if(!is_array($new_array))
			{
				$new_array =  array();
			}

			$result['delete'] = array_diff($old_array, $new_array);
			$result['insert'] = array_diff($new_array, $old_array);
			$result['edit'] = array_intersect($old_array, $new_array);
			return $result;
		}

		/**
		* Get the combo box
		*	     
		* @param string $name The name of the combo
		* @param array $list The array with the data for this list box ($list[$key] = $value)
		* @param string $selected The option selected for this combo
		* @param boolean $use_js For use javascript
		* @return mixed The combo box 
		*/
		function get_combo($name, $list=array(), $selected='', $use_js=False)
		{
			$js_str = $use_js?'onChange="this.form.submit();"':'';
			$str = '<select name="'.$name.'" '. $js_str .'  style="width:220">'.$this->get_option_list($list, $selected).'</select>';
			return $str;
		}

		function get_text($list_name, $value)
		{
			$str = '<input type="text" name="'.$list_name.'" value="'.$value.'">';
			return $str;
		}

		/**
		* Make the option list html code
		*	     
		* @param array $list The array with the data for this list box ($list[$key] = $value)
		* @param string $selected The option what you selected 
		* @return string The html code with all options
		*/
		function get_option_list($list=array(), $selected='')
		{
			$selected_option[$selected] = ' selected';
			if(is_array($list))
			{
				foreach($list as $key => $data)
				{
					$str .= '<option value="'.$key.'" ' . $selected_option[$key] . '>'.$data.'</option>';
				}
			}
			return $str;
		}
		
		/**
		* Get the javascript function for use in the form
		*	     
		* @return string The javascript function for use in the form
		*/
		function get_onsubmit_js_string()
		{
			return 'onsubmit="process_list(\''
				.$this->all_option_list_name
				.'\',\''
				.$this->selected_option_list_name.'\')"';
		}
		
		function set_list_filters($list_options, $selected='')
		{
			if(is_array($list_options))
			{
				$sel_opt[$selected] = 'selected';
				foreach($list_options as $key => $value)
				{
					$opt .= '<option value="'.$key.'"'.$sel_opt[$key].'>'.$value.'</option>';
				}
				$this->filter_option_list = $opt;
			}
			else
			{
				$this->filter_option_list = $list_options;
			}
		}
		
		function get_list_filters()
		{
			return $this->filter_option_list;
		}
		
		function get_onload_js_string()
		{
			return 'onload="setUpVisual(\''
				.$this->form_name
				.'\',\''.$this->all_option_list_name
				.'\',\'searchautocomplete\');obj1.bldUpdate();"';
		}
		
		/**
		* Get the javascript functions which are necesary for this widget
		*	     
		* @return string The javascript functions
		*/
		function java_script()
		{
			$tmp= '
			<SCRIPT LANGUAGE="JavaScript">

			function move_cbo(sboxname, cboxname) {
				sbox = document.'.$this->form_name.'.elements[sboxname];
				cbox = document.'.$this->form_name.'.elements[cboxname];
				if(sbox.length > 0)
				{
					sel_opt = sbox.options[sbox.selectedIndex].text;
				}
				else
				{
					sel_opt="";
				}
				sbox.length = 0;
				for(c = 0; c < cbox.length; c++) 
				{
					var no = new Option();
					no.value = cbox[c].value;
					no.text = cbox[c].text;
					if(no.text == sel_opt)
					{
						i = c;
					}
					sbox[c] = no;
				}
				if(i>0)
				{
					sbox.options[i].selected = true;
				}
			}

			function process_list(allboxname, myboxname) {
				mybox = document.'.$this->form_name.'.elements[myboxname];
				for(c = 0; c < mybox.options.length; c++) 
				{
					mybox.options[c].selected = true;
				}
			}

			</script>'
				.
			'<script src="'.$GLOBALS['phpgw']->link("/phpgwapi/js/contacts/selectboxes.js").'"> </script>';
			return $tmp;
		}
	}
