<?php
/**
	 * phpGroupWare - eventplanner: a part of a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2016 Free Software Foundation, Inc. http://www.fsf.org/
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
	 * @internal Development of this vendor was funded by http://www.bergen.kommune.no/
	 * @package eventplanner
	 * @subpackage vendor
	 * @version $Id: $
	 */
	phpgw::import_class('phpgwapi.uicommon');
	phpgw::import_class('phpgwapi.datetime');

	include_class('eventplanner', 'vendor', 'inc/model/');

	class eventplanner_uivendor extends phpgwapi_uicommon
	{

		public $public_functions = array(
			'add' => true,
			'index' => true,
			'query' => true,
			'view' => true,
			'edit' => true,
			'save' => true,
			'get'	=> true,
			'get_list' => true
		);

		protected
			$fields,
			$permissions,
			$currentapp;

		public function __construct()
		{
			parent::__construct();
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('vendor');
			$this->bo = createObject('eventplanner.bovendor');
			$this->fields = eventplanner_vendor::get_fields();
			$this->permissions = eventplanner_vendor::get_instance()->get_permission_array();
			$this->currentapp = $GLOBALS['phpgw_info']['flags']['currentapp'];
			self::set_active_menu("{$this->currentapp}::vendor");
		}

		private function get_category_options( $selected = 0 )
		{
			$category_options = array();
			$category_list = execMethod('eventplanner.bogeneric.get_list', array('type' => 'vendor_category'));

			$category_options[] = array(
				'id' => '',
				'name' => lang('select')
			);

			foreach ($category_list as $category)
			{
				$category_options[] = array(
					'id' => $category['id'],
					'name' => $category['name'],
					'selected' => $category['id'] == $selected ? 1 : 0
				);
			}
			return $category_options;
		}

		public function index()
		{
			if (empty($this->permissions[PHPGW_ACL_READ]))
			{
				$message = '';
				if($this->currentapp == 'eventplannerfrontend')
				{
					$message = lang('you need to log in to access this page.');
				}
				phpgw::no_access(false, $message);
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			phpgwapi_jquery::load_widget('autocomplete');

			$function_msg = lang('vendor');

			$data = array(
				'datatable_name' => $function_msg,
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array(
								'type' => 'filter',
								'name' => 'filter_category_id',
								'text' => lang('category'),
								'list' =>  $this->get_category_options()
							),
							array(
								'type' =>  $this->currentapp == 'eventplanner' ? 'checkbox' : 'hidden',
								'name' => 'filter_active',
								'text' => $this->currentapp == 'eventplanner' ? lang('showall') : '',
								'value' =>  1,
								'checked'=> 1,
							)
						)
					)
				),
				'datatable' => array(
					'source' => self::link(array(
						'menuaction' => "{$this->currentapp}.uivendor.index",
						'phpgw_return_as' => 'json'
					)),
					'allrows' => true,
					'new_item' => self::link(array('menuaction' => "{$this->currentapp}.uivendor.add")),
					'editor_action' => '',
					'field' => parent::_get_fields()
				)
			);

			$parameters = array(
				'parameter' => array(
					array(
						'name' => 'id',
						'source' => 'id'
					)
				)
			);

/*			$data['datatable']['actions'][] = array
				(
				'my_name' => 'view',
				'text' => lang('show'),
				'action' => self::link(array
					(
					'menuaction' => "{$this->currentapp}.uivendor.view"
				)),
				'parameters' => json_encode($parameters)
			);
*/
			$data['datatable']['actions'][] = array
				(
				'my_name' => 'edit',
				'text' => lang('edit'),
				'action' => self::link(array
					(
					'menuaction' => "{$this->currentapp}.uivendor.edit"
				)),
				'parameters' => json_encode($parameters)
			);

			self::add_javascript('eventplannerfrontend', 'portico', 'vendor.index.js');
			phpgwapi_jquery::load_widget('numberformat');

			self::render_template_xsl('datatable_jquery', $data);
		}

		public function add()
		{
			self::set_active_menu("{$this->currentapp}::vendor::new_vendor");
			parent::add();
		}

		/*
		 * Edit the price item with the id given in the http variable 'id'
		 */

		public function edit( $values = array(), $mode = 'edit' )
		{
			$active_tab = !empty($values['active_tab']) ? $values['active_tab'] : phpgw::get_var('active_tab', 'string', 'REQUEST', 'first_tab');
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('edit');
			if (empty($this->permissions[PHPGW_ACL_ADD]))
			{
				phpgw::no_access();
			}

			if (!empty($values['object']))
			{
				$vendor = $values['object'];
			}
			else
			{
				$id = !empty($values['id']) ? $values['id'] : phpgw::get_var('id', 'int');
				$vendor = $this->bo->read_single($id);
			}

			$tabs = array();
			$tabs['first_tab'] = array(
				'label' => lang('vendor'),
				'link' => '#first_tab',
		//		'function' => "set_tab('first_tab')"
			);

			$tabs['application'] = array(
				'label' => lang('application'),
				'link' => '#application',
				'disable' => $id ? false : true,
	//			'function' => "set_tab('application')"
			);

			$bocommon = CreateObject('property.bocommon');

			$comments = (array)$vendor->comments;
			foreach ($comments as $key => &$comment)
			{
				$comment['value_count'] = $key +1;
				$comment['value_date'] = $GLOBALS['phpgw']->common->show_date($comment['time']);
			}

			$comments_def = array(
				array('key' => 'value_count', 'label' => '#', 'sortable' => true, 'resizeable' => true),
				array('key' => 'value_date', 'label' => lang('Date'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'author', 'label' => lang('User'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'comment', 'label' => lang('Note'), 'sortable' => true, 'resizeable' => true)
			);

			$datatable_def[] = array(
				'container' => 'datatable-container_0',
				'requestUrl' => "''",
				'ColumnDefs' => $comments_def,
				'data' => json_encode($comments),
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$application_def = array(
				array('key' => 'id', 'label' => lang('id'), 'sortable' => true, 'resizeable' => true,'formatter' => 'JqueryPortico.formatLink'),
				array('key' => 'title', 'label' => lang('title'), 'sortable' => false, 'resizeable' => true),
				array('key' => 'date_start', 'label' => lang('date start'), 'sortable' => false, 'resizeable' => true),
				array('key' => 'date_end', 'label' => lang('date end'), 'sortable' => false, 'resizeable' => true),
				array('key' => 'number_of_units', 'label' => lang('number of units'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'timespan', 'label' => lang('event timespan'), 'sortable' => false, 'resizeable' => true),
			);

			$tabletools = array(
				array(
					'my_name' => 'add',
					'text' => lang('add'),
					'type' => 'custom',
					'className' => 'add',
					'custom_code' => "
								add_application('{$this->currentapp}', '{$this->currentapp}.uiapplication.edit', {$id});"
				)
			);

			$datatable_def[] = array(
				'container' => 'datatable-container_1',
				'requestUrl' => json_encode(self::link(array('menuaction' => "{$this->currentapp}.uiapplication.query",
					'filter_vendor_id' => $id,
					'phpgw_return_as' => 'json'))),
				'tabletools' => $tabletools,
				'ColumnDefs' => $application_def,
				'data' => json_encode(array()),
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$config = CreateObject('phpgwapi.config', 'eventplanner')->read();
			$default_category = !empty($config['default_vendor_category']) ? $config['default_vendor_category'] : null;

			$vendor->organization_number = $vendor->organization_number ? $vendor->organization_number :  phpgw::get_var('org_id','int' , 'SESSION');
			$data = array(
				'datatable_def' => $datatable_def,
				'form_action' => self::link(array('menuaction' => "{$this->currentapp}.uivendor.save")),
				'cancel_url' => self::link(array('menuaction' => "{$this->currentapp}.uivendor.index",)),
				'vendor' => $vendor,
				'category_list' => array('options' => $this->get_category_options( $vendor->category_id ? $vendor->category_id : $default_category )),
				'mode' => $mode,
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'value_active_tab' => $active_tab
			);
			phpgwapi_jquery::formvalidator_generate(array());
			self::add_javascript('eventplannerfrontend', 'portico', 'validate.js');
			self::add_javascript($this->currentapp, 'portico', 'vendor.edit.js');
			self::render_template_xsl(array('vendor', 'datatable_inline'), array($mode => $data));
		}

		/*
		 * Get the vendor with the id given in the http variable 'id'
		 */

		public function get( $id = 0 )
		{
			if (empty($this->permissions[PHPGW_ACL_ADD]))
			{
				phpgw::no_access();
			}

			$id = !empty($id) ? $id : phpgw::get_var('id', 'int');

			$vendor = $this->bo->read_single($id)->toArray();

			unset($vendor['secret']);

			return $vendor;
		}

		public function save()
		{
			parent::save();
		}
	}