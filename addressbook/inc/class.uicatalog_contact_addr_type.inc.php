<?php
	include('class.catalog_manager.inc.php');

	class uicatalog_contact_addr_type extends catalog_manager
	{
		var $public_functions = array('view' => True);
		var $modify = False;

		function uicatalog_contact_addr_type()
		{
			$this->_constructor();
			$this->bo = CreateObject('addressbook.bocatalog_contact_addr_type');

			$this->form_action = array('menuaction' => 'addressbook.uicatalog_contact_addr_type.view');
			$this->catalog_name = 'addr_types';
			$this->headers = array('Type', 'Edit', 'Delete');
			$this->array_name = 'addr_types_array';
			$this->index = 'key_addr_id';
			$this->title = 'Location Types - Catalog';
			$this->catalog_button_name = 'addr_types_add_row';
			$this->key_edit_name = 'addr_type_id';
			$this->num_cols = 1;

			$this->form_fields = array(1 => array('Type', $this->get_column_data(
								      array('type' => 'text',
									    'name' => 'entry[addr_description]',
									    'value'=> $this->entry['addr_description']))));

			$this->objs_data = array('value'=> array('type' => 'data',
								 'field' => 'addr_description'),
						 'edit' => array('type' => 'link',
								 'mode' => 'edit',
								 'key'  => 'addr_type_id',
								 'action'=> 'addr_types_edit_row',
								 'extra'=> ''),
						 'delete'=>array('type' => 'link',
								 'mode' => 'delete',
								 'key'  => 'addr_type_id',
								 'action'=> 'addr_types_del_row',
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
				$contacts->delete_sessiondata('addr_type');
				$contacts->delete_sessiondata('addr_type_flag');
			}
		}

		function select_catalog()
		{
			$this->addr_types_array = $this->bo->select_catalog();
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
			$this->catalog_button_name = 'addr_types_update_row';
			$this->key_edit_name = 'addr_type_id';
			$this->key_edit_id = $key;

			$record = $this->bo->get_record($key);
			$this->entry['addr_description'] = $record[0]['addr_description'];
			$this->form_fields = array(1 => array('Type', $this->get_column_data(
								      array('type' => 'text',
									    'name' => 'entry[addr_description]',
									    'value'=> $this->entry['addr_description']))));
		}
	}
?>
