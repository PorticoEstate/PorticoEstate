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

	//phpgw::import_class('addressbook.catalog_manager');
	phpgw::import_class('phpgwapi.uicommon');

	class addressbook_uicatalog_contact_comm_descr extends phpgwapi_uicommon
	{
		var $public_functions = array
		(
			'view' => True,
			'save' => True,
			'delete' => True,
			'edit' => True
		);
		private $receipt = array();

		function __construct()
		{
			parent::__construct();

			$this->template	= &$GLOBALS['phpgw']->template;
			$this->bo = CreateObject('addressbook.bocatalog_contact_comm_descr');
			self::set_active_menu("admin::{$this->currentapp}::contact_comm_descr");
		}

		function view()
		{
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}
					
			$comm_type_array = $this->bo->select_catalog_types();
			$all_comm_type = array();
			
			if ($comm_type_array)
			{
				foreach ($comm_type_array as $k => $v)
				{
					$all_comm_type[] = array('id'=> $v['comm_type_id'], 'name' => $v['comm_type_description']);
				}
			}
		
			$tabs = array();
			$tabs['comm_descr_tab'] = array('label' => lang('Communications Description'), 'link' => '#comm_descr_tab');

			$tabletools[] = array
				(
				'my_name' => 'delete',
				'text' => lang('delete'),
				'type' => 'custom',
				'custom_code' => "
					var oArgs = " . json_encode(array(
					'menuaction' => "{$this->currentapp}.uicatalog_contact_comm_descr.delete",
					'phpgw_return_as' => 'json'
				)) . ";
					var parameters = " . json_encode(array('parameter' => array(array('name' => 'comm_descr_id',
							'source' => 'comm_descr_id')))) . ";
					deleteDescr(oArgs, parameters);
				"
			);

			$tabletools[] = array
				(
				'my_name' => 'edit',
				'text' => lang('edit'),
				'type' => 'custom',
				'custom_code' => "
					var oArgs = " . json_encode(array(
					'menuaction' => "{$this->currentapp}.uicatalog_contact_comm_descr.edit",
					'phpgw_return_as' => 'json'
				)) . ";
					var parameters = " . json_encode(array('parameter' => array(array('name' => 'comm_descr_id',
							'source' => 'comm_descr_id')))) . ";
					updateDescr(oArgs, parameters);
				"
			);
			
			$datatable_def[] = array
				(
				'container' => 'datatable-container_0',
				'requestUrl' => json_encode(self::link(array('menuaction' => "{$this->currentapp}.uicatalog_contact_comm_descr.view", 
						'phpgw_return_as' => 'json'))),
				'data' => json_encode(array()),
				'ColumnDefs' => array(
					array('key' => 'comm_type_id', 'label' => lang('type_id'), 'className' => '', 'sortable' => false, 'hidden' => true),
					array('key' => 'comm_type_name', 'label' => lang('Type'), 'className' => '', 'sortable' => false, 'hidden' => false),
					array('key' => 'comm_descr_id', 'label' => lang('descr_id'), 'className' => '', 'sortable' => false, 'hidden' => true),
					array('key' => 'comm_descr', 'label' => lang('Description'), 'className' => '', 'sortable' => false, 'hidden' => false)
				),
				'tabletools' => $tabletools,
				'config' => array(
					array('disableFilter' => true,
						'singleSelect' => true,
						'allrows' => false)
				)
			);
				
			$data = array(
				'datatable_def' => $datatable_def,
				'all_comm_type' => array('options' => $all_comm_type),
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, 0),
				'confirm_msg' => lang('do you really want to delete this entry'),
				'lang_descr' => lang('Please enter a description'),
				'value_active_tab' => 0
			);
			
			self::add_javascript('addressbook', 'portico', 'catalog_comm_descr.js');
			self::render_template_xsl(array('catalog_comm_descr', 'datatable_inline'), array('view' => $data));
		}

		public function query($relaxe_acl = false)
		{
			$draw = phpgw::get_var('draw', 'int');
			$start = phpgw::get_var('start', 'int', 'REQUEST', 0);
			$num_rows = phpgw::get_var('length', 'int', 'REQUEST', 10);
			
			$entries = $this->bo->select_catalog();

			$values = array();
			
			$comm_type_array = $this->bo->select_catalog_types();
			$all_comm_type = array();
			
			if ($comm_type_array)
			{
				foreach ($comm_type_array as $k => $v)
				{
					$all_comm_type[$v['comm_type_id']] = $v['comm_type_description'];
				}
			}
			
			foreach ($entries as $entry)
			{
				$values[] = array('comm_type_id' => $entry['comm_type_id'], 
					'comm_type_name' => $all_comm_type[$entry['comm_type_id']],
					'comm_descr_id' => $entry['comm_descr_id'], 
					'comm_descr' => $entry['comm_description']);
			}

			if ($num_rows == -1)
			{
				$out = $values;
			}
			else
			{
				$page = ceil(( $start / $num_rows));
				$files_part = array_chunk($values, $num_rows);
				$out = $files_part[$page];
			}
			
			$result_data = array('results' => $out);
			$result_data['total_records'] = count($values);
			$result_data['draw'] = $draw;

			return $this->jquery_results($result_data);
		}

		private function _populate( $data = array() )
		{
			$values['id'] = phpgw::get_var('comm_descr_id');
			$values['description'] = array('comm_type' => phpgw::get_var('comm_type_id'), 'comm_description' => phpgw::get_var('comm_descr'));
			
			if (!$values['description'])
			{
				$this->receipt['error'][] = array('msg' => lang('Please enter a description !'));
			}
			
			return $values;
		}	
	
		public function save($ajax = false)
		{
			$values = $this->_populate();
			
			if ($this->receipt['error'])
			{
				return $this->receipt;
			}
			
			try
			{
				if ($values['id'])
				{						
					$this->bo->update($values['id'], $values['description']);
				}
				else
				{ 
					$this->bo->insert($values['description']);
				}					
			}
			catch (Exception $e)
			{
				if ($e)
				{
					$this->receipt['error'][] = array('msg' => $e->getMessage());				
				}
			}
			
			$this->receipt['message'][] = array('msg' => lang('description has been saved'));
			
			return $this->receipt;
		}
		
		function delete()
		{
			$ids = phpgw::get_var('comm_descr_id');
			
			foreach($ids as $id)
			{
				$this->bo->delete($id);
			}
			
			return true;
		}
		
		function edit()
		{
			$ids = phpgw::get_var('comm_descr_id');
			$record = $this->bo->get_record($ids[0]);

			return array('comm_type_id'=>$record[0]['comm_type'], 'comm_descr_id'=>$ids[0], 'comm_descr'=>$record[0]['comm_description']);
		}
		
	}
