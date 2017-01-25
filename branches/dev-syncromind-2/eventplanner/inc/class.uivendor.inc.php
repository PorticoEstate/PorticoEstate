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
			'get_list' => true
		);

		protected
			$fields,
			$permissions,
			$currentapp;

		public function __construct()
		{
			parent::__construct();
			self::set_active_menu('eventplanner::vendor');
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('vendor');
			$this->bo = createObject('eventplanner.bovendor');
			$this->fields = eventplanner_vendor::get_fields();
			$this->permissions = eventplanner_vendor::get_instance()->get_permission_array();
			$this->currentapp = $GLOBALS['phpgw_info']['flags']['currentapp'];
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
				phpgw::no_access();
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
								'type' => 'checkbox',
								'name' => 'filter_active',
								'text' => lang('showall'),
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

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'view',
				'text' => lang('show'),
				'action' => self::link(array
					(
					'menuaction' => "{$this->currentapp}.uivendor.view"
				)),
				'parameters' => json_encode($parameters)
			);

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

			self::add_javascript('eventplanner', 'portico', 'vendor.index.js');
			phpgwapi_jquery::load_widget('numberformat');

			self::render_template_xsl('datatable_jquery', $data);
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
				'function' => "set_tab('first_tab')"
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

			$data = array(
				'datatable_def' => $datatable_def,
				'form_action' => self::link(array('menuaction' => "{$this->currentapp}.uivendor.save")),
				'cancel_url' => self::link(array('menuaction' => "{$this->currentapp}.uivendor.index",)),
				'vendor' => $vendor,
				'category_list' => array('options' => $this->get_category_options( $vendor->category_id )),
				'mode' => $mode,
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'value_active_tab' => $active_tab
			);
			phpgwapi_jquery::formvalidator_generate(array());
			self::add_javascript('eventplannerfrontend', 'portico', 'validate.js');
			self::render_template_xsl(array('vendor', 'datatable_inline'), array($mode => $data));
		}
		
		public function save()
		{
			parent::save();
		}
	}