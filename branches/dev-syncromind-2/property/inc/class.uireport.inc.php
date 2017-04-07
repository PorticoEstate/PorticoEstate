<?php
	/**
	 * phpGroupWare - property: a part of a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2012 Free Software Foundation, Inc. http://www.fsf.org/
	 * This file is part of phpGroupWare.
	 *
	 * phpGroupWare is free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation; either version 2 of the License, or
	 * (at your option) any later version.
	 *
	 * phpGroupWare is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with phpGroupWare; if not, write to the Free Software
	 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	 *
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/
	 * @package property
	 * @subpackage logistic
	 * @version $Id: class.uireport.inc.php 14913 2017-03-10 12:27:37Z sigurdne $
	 */
	phpgw::import_class('phpgwapi.uicommon_jquery');
	phpgw::import_class('phpgwapi.jquery');

	class property_uireport extends phpgwapi_uicommon_jquery
	{
		private $receipt = array();

		public $public_functions = array(
			'query' => true,
			'query_dataset' => true,
			'index' => true,
			'view' => true,
			'add' => true,
			'edit' => true,
			'save' => true,
			'delete' => true,
			'add_dataset' => true,
			'edit_dataset' => true,
			'save_dataset' => true,
			'delete_dataset' => true,
			'get_columns' => true,
			'download' => true
		);

		public function __construct()
		{
			parent::__construct();
			
			//$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$this->bo = CreateObject('property.boreport', true);
			$this->bocommon = & $this->bo->bocommon;
			$this->acl = & $GLOBALS['phpgw']->acl;			
		}

		public function download()
		{
			return;
		}

		private function _get_filters()
		{
			$views = $this->bo->get_datasets();;
			foreach ($views as $view)
			{
				$list[] = array('id' => $view['id'], 'name' => $view['name']);
			}
				
			$default_value = array('id' => '', 'name' => lang('Select'));
			array_unshift($list, $default_value);
			
			return $list;
		}
		
		/**
		 * Prepare UI
		 * @return void
		 */
		public function index()
		{
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				if (phpgw::get_var('dataset'))
				{
					return $this->query_dataset();
				}
				
				return $this->query();
			}
						
			$appname = lang('report');
			$function_msg = lang('list');
			
			$filters = $this->_get_filters();

			$tabletools = array();

			$parameters = array
				(
				'parameter' => array
					(
					array
						(
						'name' => 'id',
						'source' => 'id'
					)
				)
			);
			
			$tabletools[] = array
				(
				'my_name' => 'add',
				'text' => lang('new'),
				'type' => 'custom',
				'className' => 'add',
				'custom_code' => "
						var oArgs = " . json_encode(array(
							'menuaction' => 'property.uireport.add'
				)) . ";
						newReport(oArgs);
					"
			);

			$tabletools[] = array
				(
				'my_name' => 'edit',
				'text' => lang('edit'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uireport.edit'
				)),
				'parameters' => json_encode($parameters)
			);

			$tabletools[] = array
				(
				'my_name' => 'delete',
				'text' => lang('delete'),
				'confirm_msg' => lang('do you really want to delete this entry'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uireport.delete', 'phpgw_return_as' => 'json'
				)),
				'parameters' => json_encode($parameters)
			);
			
			$related_def = array
				(
				array('key' => 'id', 'label' => lang('ID'), 'sortable' => true, 'resizeable' => true, 'hidden' => true),
				array('key' => 'dataset_name', 'label' => lang('name'), 'sortable' => true, 'resizeable' => true)
			);


			$datatable_def[] = array
				(
				'container' => 'datatable-container_0',
				'requestUrl' => json_encode(self::link(array('menuaction' => 'property.uireport.index',
						'phpgw_return_as' => 'json'))),
				'ColumnDefs' => $related_def,
				'tabletools' => $tabletools,
				'config' => array(
					array('singleSelect' => true)
				)
			);

			$tabletools_views[] = array
				(
				'my_name' => 'add',
				'text' => lang('new'),
				'type' => 'custom',
				'className' => 'add',
				'custom_code' => "
						var oArgs = " . json_encode(array(
							'menuaction' => 'property.uireport.add_dataset'
				)) . ";
						newDataset(oArgs);
					"
			);

			$tabletools_views[] = array
				(
				'my_name' => 'edit',
				'text' => lang('edit'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uireport.edit_dataset'
				)),
				'parameters' => json_encode($parameters)
			);

			$tabletools_views[] = array
				(
				'my_name' => 'delete',
				'text' => lang('delete'),
				'confirm_msg' => lang('do you really want to delete this entry'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uireport.delete_dataset', 'phpgw_return_as' => 'json'
				)),
				'parameters' => json_encode($parameters)
			);
			
			$datatable_def[] = array
				(
				'container' => 'datatable-container_1',
				'requestUrl' => json_encode(self::link(array('menuaction' => 'property.uireport.index',
						'dataset' => '1', 'phpgw_return_as' => 'json'))),
				'ColumnDefs' => $related_def,
				'tabletools' => $tabletools_views,
				'config' => array(
					array('singleSelect' => true)
				)
			);
			
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			$tabs = array();
			$tabs['reports'] = array('label' => lang('reports'), 'link' => '#reports');
			$tabs['views'] = array('label' => lang('views'), 'link' => '#views');
			
			$data = array
				(
				'datatable_def' => $datatable_def,
				'list_views' => array('options' => $filters),
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, 'reports')
			);
			
			self::add_javascript('property', 'portico', 'report.index.js');
			
			self::render_template_xsl(array('report', 'datatable_inline'), array('lists' => $data));			
		}

		public function view()
		{
			if (!$this->acl_read)
			{
				phpgw::no_access();
				return;
			}
			$this->edit(null, $mode = 'view');
		}

		public function add()
		{
			$this->edit();
		}
		
		/**
		 * Prepare data for view and edit - depending on mode
		 *
		 * @param array  $values  populated object in case of retry
		 * @param string $mode    edit or view
		 * @param int    $id      entity id - no id means 'new'
		 *
		 * @return void
		 */
		public function edit( $values = array(), $mode = 'edit' )
		{
			$id = isset($values['id']) && $values['id'] ? $values['id'] : phpgw::get_var('id', 'int');

			if ($id)
			{
				$values = $this->bo->read_single($id);
			}
			
			$link_data = array
				(
				'menuaction' => "property.uireport.save",
				'id' => $id
			);
			
			$datasets = $this->bo->get_datasets();
			foreach ($datasets as $item)
			{
				$selected = 0;
				if ($values['dataset_id'] == $item['id']){
					$selected = 1;
				}				
				$list[] = array('id' => $item['id'], 'name' => $item['name'], 'selected' => $selected);
			}
			
			$default_value = array('id' => '', 'name' => lang('Select'));
			array_unshift($list, $default_value);
			
			$tabs = array();
			$tabs['report'] = array('label' => lang('report'), 'link' => '#report');
			$active_tab = 'report';
			
			$msgbox_data = $this->bocommon->msgbox_data($this->receipt);

			$data = array
			(
				'datatable_def' => array(),
				'editable' => $mode == 'edit',
				'form_action' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'cancel_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uireport.index')),
				'datasets' => array('options' => $list),
				'report_definition' => $values['report_definition'],
				'dataset_report_id' => $values['id'],
				'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab)
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . '::' . lang('report');

			self::add_javascript('property', 'portico', 'report.edit.js');

			self::render_template_xsl(array('report'), array('edit' => $data));
		}
		
		private function _populate( $data = array() )
		{
			$dataset_report_id = phpgw::get_var('dataset_report_id');
			$dataset_id = phpgw::get_var('dataset_id');
			
			$group = phpgw::get_var('group');
			$order = phpgw::get_var('order');
			$aggregate = phpgw::get_var('aggregate');
			$cbo_aggregate = phpgw::get_var('cbo_aggregate');
			$txt_aggregate = phpgw::get_var('txt_aggregate');

			$values['id'] = $dataset_report_id;

			if (!$dataset_id)
			{
				$this->receipt['error'][] = array('msg' => lang('Please select a view name !'));
			}
			
			if (!count($group))
			{
				$this->receipt['error'][] = array('msg' => lang('Please select a columns !'));
			}

			if (!count($aggregate))
			{
				$this->receipt['error'][] = array('msg' => lang('Please enter a agregate !'));
			}
			$values['report_definition']['group'] = $group;
			$values['report_definition']['order'] = $order;
			$values['report_definition']['aggregate'] = $aggregate;
			$values['report_definition']['cbo_aggregate'] = $cbo_aggregate;
			$values['report_definition']['txt_aggregate'] = $txt_aggregate;
			$values['dataset_id'] = $dataset_id;

			return $values;
		}
		
		public function save()
		{
			if (!$_POST)
			{
				return $this->edit();
			}
			
			/*
			 * Overrides with incoming data from POST
			 */
			$values = $this->_populate();

			if ($this->receipt['error'])
			{
				$this->edit($values);
			}
			else
			{
				try
				{
					$receipt = $this->bo->save($values);
					$id = $receipt['id'];
				}
				catch (Exception $e)
				{
					if ($e)
					{
						phpgwapi_cache::message_set($e->getMessage(), 'error');
						$this->edit($values);
						return;
					}
				}
				
				self::message_set($receipt);

				self::redirect(array('menuaction' => 'property.uireport.edit', 'id' => $id));
			}
		}
		
		function delete()
		{
			$id = phpgw::get_var('id');

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				$receipt = $this->bo->delete($id);
				
				if ($receipt['message'])
				{
					$message = $receipt['message'][0]['msg'];
				} else {
					$message = $receipt['error'][0]['msg'];
				}
				
				return $message;
			}
		}
		
		
		public function add_dataset()
		{
			$this->edit_dataset();
		}
		
		public function edit_dataset( $values = array(), $mode = 'edit' )
		{
			$id = isset($values['id']) && $values['id'] ? $values['id'] : phpgw::get_var('id', 'int');

			if ($id)
			{
				$values = $this->bo->read_single_dataset($id);
			}
			
			$link_data = array
				(
				'menuaction' => "property.uireport.save_dataset",
				'id' => $id
			);
			
			$views = $this->bo->get_views();
			foreach ($views as $view)
			{
				$selected = 0;
				if ($values['view_name'] == $view['name']){
					$selected = 1;
				}
				$list[] = array('id' => $view['name'], 'name' => $view['name'], 'selected' => $selected);
			}
			
			$default_value = array('id' => '', 'name' => lang('Select'));
			array_unshift($list, $default_value);
			
			$tabs = array();
			$tabs['report'] = array('label' => lang('report'), 'link' => '#report');
			$active_tab = 'report';
			
			$msgbox_data = $this->bocommon->msgbox_data($this->receipt);

			$data = array
			(
				'datatable_def' => array(),
				'editable' => $mode == 'edit',
				'form_action' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'cancel_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uireport.index')),
				'views' => array('options' => $list),
				'dataset_name' => $values['dataset_name'],		
				'dataset_id' => isset($values['id']) ? $values['id'] : '',
				'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab)
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . '::' . lang('report');

			self::add_javascript('property', 'portico', 'report.edit.js');

			self::render_template_xsl(array('report'), array('edit_dataset' => $data));
		}
		
		private function _populate_dataset( $data = array() )
		{
			$dataset_id = phpgw::get_var('dataset_id');
			$values = phpgw::get_var('values');

			$values['id'] = $dataset_id;

			if (!$values['view_name'])
			{
				$this->receipt['error'][] = array('msg' => lang('Please select a view name !'));
			}

			if (!$values['dataset_name'])
			{
				$this->receipt['error'][] = array('msg' => lang('Please enter a dataset name !'));
			}

			return $values;
		}
		
		public function save_dataset()
		{
			if (!$_POST)
			{
				return $this->edit_dataset();
			}

			/*
			 * Overrides with incoming data from POST
			 */
			$values = $this->_populate_dataset();

			if ($this->receipt['error'])
			{
				$this->edit_dataset($values);
			}
			else
			{
				try
				{
					$receipt = $this->bo->save_dataset($values);
					$id = $receipt['id'];
				}
				catch (Exception $e)
				{
					if ($e)
					{
						phpgwapi_cache::message_set($e->getMessage(), 'error');
						$this->edit_dataset($values);
						return;
					}
				}
				
				self::message_set($receipt);

				self::redirect(array('menuaction' => 'property.uireport.edit_dataset', 'id' => $id));
			}
		}
		
		function delete_dataset()
		{
			$id = phpgw::get_var('id');

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				$receipt = $this->bo->delete_dataset($id);
				
				if ($receipt['message'])
				{
					$message = $receipt['message'][0]['msg'];
				} else {
					$message = $receipt['error'][0]['msg'];
				}
				
				return $message;
			}
		}
		
		/**
		 * Fetch data from $this->bo based on parametres
		 * @return array
		 */
		public function query()
		{
			$query = phpgw::get_var('query');
			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');
			$export = phpgw::get_var('export', 'bool');

			$params = array(
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query' => $query ? $query : $search['value'],
				'order' => $columns[$order[0]['column']]['data'],
				'sort' => $order[0]['dir'],
				'dir' => $order[0]['dir'],
				'allrows' => phpgw::get_var('length', 'int') == -1 || $export,
			);

			$values = $this->bo->read($params);
		
			if ($export)
			{
				return $values;
			}

			$result_data = array('results' => $values);

			$result_data['total_records'] = $this->bo->total_records_reports;
			$result_data['draw'] = $draw;

			$link_data = array
				(
				'menuaction' => "property.uireport.edit"
			);

			return $this->jquery_results($result_data);
		}
		
		public function query_dataset()
		{
			$query = phpgw::get_var('query');
			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');
			$export = phpgw::get_var('export', 'bool');

			$params = array(
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query' => $query ? $query : $search['value'],
				'order' => $columns[$order[0]['column']]['data'],
				'sort' => $order[0]['dir'],
				'dir' => $order[0]['dir'],
				'allrows' => phpgw::get_var('length', 'int') == -1 || $export,
			);

			$values = $this->bo->read_dataset($params);
		
			if ($export)
			{
				return $values;
			}

			$result_data = array('results' => $values);

			$result_data['total_records'] = $this->bo->total_records_dataset;
			$result_data['draw'] = $draw;

			$link_data = array
				(
				'menuaction' => "property.uireport.edit_dataset"
			);

			return $this->jquery_results($result_data);
		}
		
		public function get_columns()
		{
			$dataset_id = phpgw::get_var('dataset_id');

			$columns = $this->bo->get_columns($dataset_id);
			
			return $columns;
		}
		
	}