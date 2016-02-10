<?php
  /**************************************************************************\
  * phpGroupWare - uicategorize_contacts                                     *
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

	class uicategorize_contacts
	{
		var $public_functions = array(
			'index' => True,
			'java_script' => True
			);
		
		function __construct()
		{
			$this->template	= &$GLOBALS['phpgw']->template;
			$this->lists = CreateObject('addressbook.widget_lists', 'Categories', 'categorize_contacts_form');
			$this->cat = CreateObject('phpgwapi.categories');
			$this->contacts = CreateObject('phpgwapi.contacts');
		}
		
		function index()
		{
			$this->get_vars();

			$this->lists->set_left_combo('Category', 'all_cats', $this->get_categories(), $this->selected_cat, True);
			$this->lists->set_all_option_list('All Persons', 'person_all[]', $this->get_all_persons());
			$this->lists->set_selected_option_list('Current Persons', 'person_current[]', 
							       $this->get_persons_by_cat($this->selected_cat));

			switch($this->action)
			{
			case 'save':
				$persons = $this->lists->get_resul_list();
				$this->save_categories_by_person($this->selected_cat, $persons);
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'addressbook.uicategorize_contacts.index', 'all_cats' => $this->selected_cat));
			case 'cancel':
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction' => 'addressbook.uiaddressbook.index', 'section' => 'Persons'));
			}
			$this->draw_form();
		}

		function draw_form()
		{
			$this->template->set_file(array('manage_cats_t' => 'categorize_contacts.tpl'));

			$list_widget = $this->lists->get_widget();
			$onsubjs = $this->lists->get_onsubmit_js_string();
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
			$this->template->set_var('action', 
						 $GLOBALS['phpgw']->link('/index.php',
									 array('menuaction' => 'addressbook.uicategorize_contacts.index')));
			$this->template->set_var('form_name', 'categorize_contacts_form');
			$this->template->set_var('onsubjs', $onsubjs);
			$this->template->set_var('widget_lists', $list_widget);
			$this->template->pfp('out', 'manage_cats_t');
			$GLOBALS['phpgw']->common->phpgw_exit();
		}
		
		function save_categories_by_person($selected_cat, $persons=array())
		{
			$delete = $persons['delete'];
			$insert = $persons['insert'];
			$edit = $persons['edit'];
			
			if($selected_cat==-1)
			{
				return;
			}
			
			if(is_array($delete))
			{
				foreach($delete as $person_id)
				{
					$cats = $this->contacts->get_cats_by_person($person_id);
					foreach($cats as $key => $value)
					{
						if($value == '' || $value==$selected_cat)
						{
							unset($cats[$key]);
						}
					}
					$this->contacts->edit_category($person_id, $cats, PHPGW_SQL_RUN_SQL);
				}
			}

			if(is_array($insert))
			{
				foreach($insert as $person_id)
				{
					$cats = $this->contacts->get_cats_by_person($person_id);
					$cats[] = $selected_cat;
					foreach($cats as $key => $value)
					{
						if($value == '')
						{
							unset($cats[$key]);
						}
					}
					$this->contacts->edit_category($person_id, $cats, PHPGW_SQL_RUN_SQL);
				}
			}
		}
		
		function get_all_persons()
		{
			return $this->get_persons();
		}
		
		function get_persons_by_cat($cat_id='')
		{
			if($cat_id)
			{
				return $this->get_persons($cat_id);
			}
		}

		function get_persons($cat_id=PHPGW_CONTACTS_CATEGORIES_ALL, $access=PHPGW_CONTACTS_ALL)
		{
			$criteria = $this->contacts->criteria_for_index(
				$GLOBALS['phpgw_info']['user']['account_id'], $access, $cat_id);
			$persons = $this->contacts->get_persons(
				array('person_id', 'per_full_name'),
				'', '', 'last_name','ASC','',$criteria);
			
			if(is_array($persons))
			{
				foreach($persons as $key => $data)
				{
					$persons_data[$data['person_id']] = $data['per_full_name'];
				}
				asort($persons_data);
			}
			return $persons_data;
		}

		function get_categories()
		{
			$cats = $this->cat->return_array($type,$start,$limit,$query,$sort,$order,False);
			$categories[-1] = 'Select one...';
			if(is_array($cats))
			{
				foreach($cats as $data)
				{
					$categories[$data['id']] = $data['name'];
				}
			}
			return $categories;
		}

		function java_script()
		{
			return $this->lists->java_script();
		}

		function get_vars()
		{
			$save = phpgw::get_var('save');
			$cancel = phpgw::get_var('cancel');

			if($save)
			{
				$this->action = 'save';
			}
			elseif($cancel)
			{
				$this->action = 'cancel';
			}
			$this->selected_cat = phpgw::get_var('all_cats');
		}
	}
