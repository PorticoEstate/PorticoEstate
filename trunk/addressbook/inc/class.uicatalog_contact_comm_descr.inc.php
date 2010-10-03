<?php
  /**************************************************************************\
  * phpGroupWare - catalog_manager                                           *
  * http://www.phpgroupware.org                                              *
  * This program is part of the GNU project, see http://www.gnu.org/         *
  *                                                                          *
  * Copyright 2003,2008 Free Software Foundation, Inc.                       *
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

	phpgw::import_class('addressbook.catalog_manager');

	class addressbook_uicatalog_contact_comm_descr extends addressbook_catalog_manager
	{
		var $public_functions = array('view' => True);
		var $modify = False;

		function __construct()
		{
			parent::__construct();

			$this->bo = CreateObject('addressbook.bocatalog_contact_comm_descr');

			$this->form_action = array('menuaction' => 'addressbook.uicatalog_contact_comm_descr.view');
			$this->catalog_name = 'comm_descr';
			$this->headers = array('Type', 'Description', 'Edit', 'Delete');
			$this->array_name = 'comm_descr_array';
			$this->index = '';
			$this->title = 'Communications Description - Catalog';
			$this->catalog_button_name = 'comm_descr_add_row';
			$this->key_edit_name = 'comm_descr_id';
			$this->num_cols = 2;

			$this->form_fields = array(1 => array('Type', $this->get_column_data(
								      array('type' => 'combo',
									    'name' => 'entry[comm_type]',
									    'value'=> $this->get_options()))),
						   2 => array('Description', $this->get_column_data(
								      array('type' => 'text',
									    'name' => 'entry[comm_description]',
									    'value'=> ''))));

			$this->objs_data = array('value'=> array('type' => 'data',
								 'field' => 'comm_type'),
						 'value1'=> array('type' => 'data',
								 'field' => 'comm_description'),
						 'edit' => array('type' => 'link',
								 'mode' => 'edit',
								 'key'  => 'comm_descr_id',
								 'action'=> 'comm_descr_edit_row',
								 'extra'=> ''),
						 'delete'=>array('type' => 'link',
								 'mode' => 'delete',
								 'key'  => 'comm_descr_id',
								 'action'=> 'comm_descr_del_row',
								 'extra'=> ''));
		}

		function view()
		{
			$this->get_vars();
			$this->validate_action($this->action);
			$this->create_window($this->catalog_name, $this->entry, $this->title);
			if($this->modify)
			{
				$contacts = CreateObject('phpgwapi.contacts');
				$contacts->delete_sessiondata('comm_descr');
				$contacts->delete_sessiondata('comm_descr_flag');
			}
		}

		function select_catalog()
		{
			$this->comm_descr_array = $this->bo->select_catalog();
		}

		function insert($fields)
		{
			$this->bo->insert($fields);
			$this->modify = True;
		}

		function delete($key)
		{
			$this->bo->delete($key);
			$this->modify = True;
		}

		function update($key, $fields)
		{
			$this->bo->update($key, $fields);
			$this->modify = True;
		}

		function edit($key)
		{
			$this->catalog_button_name = 'comm_descr_update_row';
			$this->key_edit_name = 'comm_descr_id';
			$this->key_edit_id = $key;

			$record = $this->bo->get_record($key);
			$entry['comm_type'] = $record[0]['comm_type'];
			$entry['comm_description'] = $record[0]['comm_description'];
			$this->form_fields = array(1 => array('Type', $this->get_column_data(
								      array('type' => 'combo',
									    'name' => 'entry[comm_type]',
									    'value'=> $this->get_options($entry['comm_type'])))),
						   2 => array('Description', $this->get_column_data(
								      array('type' => 'text',
									    'name' => 'entry[comm_description]',
									    'value'=> $entry['comm_description']))));
		}

		function get_options($selected='')
		{
			$this->comm_type_array = $this->bo->select_catalog_types();
			$options = '';
			foreach($this->comm_type_array as $option)
			{
				if ($option['comm_type_id'] == $selected)
				{
					$options .= '<option value="'.$option['comm_type_id'].'" selected>'
						. $option['comm_type_description'] . '</option>';

				}
				else
				{
					$options .= '<option value="'.$option['comm_type_id'].'">'
						. $option['comm_type_description'] . '</option>';
				}
			}
			return $options;
		}
	}
