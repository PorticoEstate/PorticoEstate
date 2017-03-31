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
			'index' => true,
			'view' => true,
			'add' => true,
			'edit' => true,
			'save' => true,
			'delete' => true,	
			'get_columns' => true,
			'download' => true
		);

		public function __construct()
		{
			parent::__construct();
			
			//$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$this->bo = CreateObject('property.boreport', true);
			$this->acl = & $GLOBALS['phpgw']->acl;			
		}

		public function download()
		{
			return;
		}

		private function _get_filters()
		{
			$combos = array();

			$views = $this->bo->get_views();;
			foreach ($views as $view)
			{
				$list[] = array('id' => $view['name'], 'name' => $view['name']);
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
							'menuaction' => 'property.uireport.edit'
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
					'menuaction' => 'property.uireport.delete'
				)),
				'parameters' => json_encode($parameters)
			);
			
			$related_def = array
				(
				array('key' => 'id', 'label' => lang('ID'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'name', 'label' => lang('name'), 'sortable' => true, 'resizeable' => true)
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
							'menuaction' => 'property.uireport.edit_view'
				)) . ";
						newView(oArgs);
					"
			);

			$tabletools_views[] = array
				(
				'my_name' => 'edit',
				'text' => lang('edit'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uireport.edit_view'
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
					'menuaction' => 'property.uireport.delete_view'
				)),
				'parameters' => json_encode($parameters)
			);
			
			$datatable_def[] = array
				(
				'container' => 'datatable-container_1',
				'requestUrl' => json_encode(self::link(array('menuaction' => 'property.uireport.index',
						'phpgw_return_as' => 'json'))),
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

			/*if (!$this->acl_add && !$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uireport.view',
					'id' => $id));
			}

			if ($mode == 'view')
			{
				if (!$this->acl_read)
				{
					phpgw::no_access();
					return;
				}
			}
			else
			{
				if (!$this->acl_add && !$this->acl_edit)
				{
					phpgw::no_access();
					return;
				}
			}*/

			$link_data = array
				(
				'menuaction' => "property.uireport.save",
				'id' => $id
			);
			
			$views = $this->bo->get_views();
			foreach ($views as $view)
			{
				$list[] = array('id' => $view['name'], 'name' => $view['name']);
			}
			
			$default_value = array('id' => '', 'name' => lang('Select'));
			array_unshift($list, $default_value);
			
			$tabs = array();
			$tabs['report'] = array('label' => lang('report'), 'link' => '#report');
			$active_tab = 'report';

			$data = array
			(
				'datatable_def' => array(),
				'editable' => $mode == 'edit',
				'form_action' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'cancel_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uireport.index')),
				'views' => array('options' => $list),
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab)
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . '::' . lang('report');

			self::add_javascript('property', 'portico', 'report.edit.js');

			self::render_template_xsl(array('report'), array('edit' => $data));
		}
		
		/**
		 * Fetch data from $this->bo based on parametres
		 * @return array
		 */
		public function query()
		{
			$result_data = array('results' => array());

			$result_data['total_records'] = 0;
			$result_data['draw'] = 1;
			
			return $this->jquery_results($result_data);
		}
		
		public function get_columns()
		{
			$view = phpgw::get_var('view');

			$columns = $this->bo->get_columns($view);
			
			return $columns;
		}
		
	}