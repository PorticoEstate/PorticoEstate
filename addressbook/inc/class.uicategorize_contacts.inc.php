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

	phpgw::import_class('phpgwapi.uicommon');
	phpgw::import_class('phpgwapi.datetime');
	
	class uicategorize_contacts extends phpgwapi_uicommon
	{
		var $public_functions = array
		(
			'index' => True,
			'save' => True,
			'get_persons_by_cat' => true
		);
		
		function __construct()
		{
			parent::__construct();
			
			$this->template	= &$GLOBALS['phpgw']->template;
			$this->bo = createObject('addressbook.boaddressbook');
			$this->cat = CreateObject('phpgwapi.categories');
			$this->contacts = CreateObject('phpgwapi.contacts');
			$this->currentapp = $GLOBALS['phpgw_info']['flags']['currentapp'];
			self::set_active_menu("{$this->currentapp}::categorize_contacts");
		}
		
		private function _populate( $data = array() )
		{
			$cat_id = phpgw::get_var('all_cats');

			$new_persons = phpgw::get_var('current_persons');
			
			$persons = $this->get_persons($cat_id);
			$current_persons = array();
			
			foreach($persons as $person)
			{
				$current_persons[] = $person['id'];
			}
			
			$values['cat_id'] = $cat_id;
			$values['persons'] = $this->bo->diff_arrays($current_persons, $new_persons);
				
			return $values;
		}
		
		function index()
		{
			$all_cats = $this->get_categories();
		
			$all_persons = $this->get_persons();
					
			$tabs = array();
			$tabs['categories'] = array('label' => lang('Categories'), 'link' => '#categories');

			$data = array(
				'form_action' => self::link(array('menuaction' => "{$this->currentapp}.uicategorize_contacts.save")),
				'cancel_url' => self::link(array('menuaction' => "{$this->currentapp}.uiaddressbook_persons.index")),
				'all_persons' => array('options' => $all_persons),
				'current_persons' => array('options' => array()),
				'categories' => array('options' => $all_cats),
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, 0),
				'image_loader' => $GLOBALS['phpgw']->common->image('property', 'ajax-loader', '.gif', false),
				'value_active_tab' => 0
			);
			
			self::add_javascript('addressbook', 'portico', 'categorize_contacts.index.js');
			self::render_template_xsl(array('categorize'), array('edit' => $data));
		}

		public function save($ajax = false)
		{
			if (!$_POST)
			{
				return $this->index();
			}

			/*
			 * Overrides with incoming data from POST
			 */
			$values = $this->_populate();

			if ($this->receipt['error'])
			{
				$this->index();
			}
			else
			{
				try
				{
					$this->save_categories_by_person($values);
				}
				catch (Exception $e)
				{
					if ($e)
					{
						phpgwapi_cache::message_set($e->getMessage(), 'error');
						$this->index();
						return;					
					}
				}

				self::redirect(array('menuaction' => "{$this->currentapp}.uicategorize_contacts.index"));
			}
		}
		
		function save_categories_by_person($values=array())
		{
			$selected_cat = $values['cat_id'];
			
			$delete = $values['persons']['delete'];
			$insert = $values['persons']['insert'];
			$edit = $values['persons']['edit'];
			
			if(empty($selected_cat))
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
		
		function get_persons_by_cat()
		{
			$cat_id = phpgw::get_var('cat_id');
			
			$persons = $this->get_persons();
			$all_persons = array();
			$current_persons = array();
			
			if ($cat_id)
			{
				$persons_by_cat = $this->get_persons($cat_id);
				$_current_persons = array();
				
				foreach($persons_by_cat as $person)
				{
					$_current_persons[] = $person['id'];
				}
			}

			foreach ($persons as $person)
			{
				if (in_array($person['id'], $_current_persons))
				{
					$current_persons[] = $person;
				}
				else
				{
					$all_persons[] = $person;
				}
			}
			
			return array('all_persons'=>$all_persons, 'current_persons'=>$current_persons);
		}

		function get_persons($cat_id=PHPGW_CONTACTS_CATEGORIES_ALL, $access=PHPGW_CONTACTS_ALL)
		{
			$criteria = $this->contacts->criteria_for_index($GLOBALS['phpgw_info']['user']['account_id'], $access, $cat_id);
			$persons = $this->contacts->get_persons(array('person_id', 'per_full_name'), '', '', 'last_name','ASC','',$criteria);
			
			if(is_array($persons))
			{
				foreach($persons as $data)
				{
					$persons_data[] = array('id'=> $data['person_id'], 'name' => $data['per_full_name']);
				}
				//asort($persons_data);
			}
			return $persons_data;
		}

		function get_categories()
		{
			$cats = $this->cat->return_array('all', 0, false, '', '', '', true);
			$all_cats = array();
			
			$all_cats[] = array('id' => '', 'name' => lang('Select option'));
			foreach ($cats as $data)
			{
				$all_cats[] = array('id'=> $data['id'], 'name' => $data['name']);
			}

			return $all_cats;
		}
	}
