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

	class addressbook_uicatalog_contact_note_type extends addressbook_catalog_manager
	{
		var $public_functions = array('view' => True);
		var $modify = False;

		function __construct()
		{
			parent::__construct();

			$this->bo = CreateObject('addressbook.bocatalog_contact_note_type');

			$this->form_action = array('menuaction' => 'addressbook.uicatalog_contact_note_type.view');
			$this->catalog_name = 'note_types';
			$this->headers = array('Type', 'Edit', 'Delete');
			$this->array_name = 'note_types_array';
			$this->index = 'key_note_id';
			$this->title = 'Notes Type - Catalog';
			$this->catalog_button_name = 'note_types_add_row';
			$this->key_edit_name = 'note_type_id';
			$this->num_cols = 1;

			$this->form_fields = array(1 => array('Type', $this->get_column_data(
								      array('type' => 'text',
									    'name' => 'entry[note_description]',
									    'value'=> $this->entry['note_description']))));

			$this->objs_data = array('value'=> array('type' => 'data',
								 'field' => 'note_description'),
						 'edit' => array('type' => 'link',
								 'mode' => 'edit',
								 'key'  => 'note_type_id',
								 'action'=> 'note_types_edit_row',
								 'extra'=> ''),
						 'delete'=>array('type' => 'link',
								 'mode' => 'delete',
								 'key'  => 'note_type_id',
								 'action'=> 'note_types_del_row',
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
				$contacts->delete_sessiondata('note_type');
				$contacts->delete_sessiondata('note_type_flag');
			}
		}

		function select_catalog()
		{
			$this->note_types_array = $this->bo->select_catalog();
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
			$this->catalog_button_name = 'note_types_update_row';
			$this->key_edit_name = 'note_type_id';
			$this->key_edit_id = $key;

			$record = $this->bo->get_record($key);
			$this->entry['note_description'] = $record[0]['note_description'];
			$this->form_fields = array(1 => array('Type', $this->get_column_data(
								      array('type' => 'text',
									    'name' => 'entry[note_description]',
									    'value'=> $this->entry['note_description']))));
		}
	}
