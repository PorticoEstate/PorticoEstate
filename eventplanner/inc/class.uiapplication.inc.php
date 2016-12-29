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
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/ and Nordlandssykehuset
	 * @package eventplanner
	 * @subpackage application
	 * @version $Id: $
	 */
	phpgw::import_class('phpgwapi.uicommon');
	phpgw::import_class('phpgwapi.datetime');

	include_class('eventplanner', 'application', 'inc/model/');

	class eventplanner_uiapplication extends phpgwapi_uicommon
	{

		public $public_functions = array(
			'add' => true,
			'index' => true,
			'query' => true,
			'view' => true,
			'edit' => true,
			'save' => true,
		);

		protected
			$fields,
			$permissions;

		public function __construct()
		{
			parent::__construct();
			self::set_active_menu('eventplanner::application');
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('application');
			$this->bo = createObject('eventplanner.boapplication');
			$this->cats = & $this->bo->cats;
			$this->fields = eventplanner_application::get_fields();
			$this->permissions = eventplanner_application::get_instance()->get_permission_array();
		}

		private function get_status_options( $selected = 0 )
		{
			$status_options = array();
			$status_list = eventplanner_application::get_status_list();

			foreach ($status_list as $_key => $_value)
			{
				$status_options[] = array(
					'id' => $_key,
					'name' => $_value,
					'selected' => $_key == $selected ? 1 : 0
				);
			}
			return $status_options;
		}

		private function _get_filters()
		{
			$combos = array();
			$combos[] = array(
				'type' => 'autocomplete',
				'name' => 'vendor',
				'app' => 'eventplanner',
				'ui' => 'vendor',
				'label_attr' => 'name',
				'text' => lang('vendor') . ':',
				'requestGenerator' => 'requestWithVendorFilter'
			);

			$status_options = $this->get_status_options();
			array_unshift($status_options, array('id' => '','name' => lang('all')));

			$combos[] = array(
				'type' => 'filter',
				'name' => 'filter_status',
				'extra' => '',
				'text' => lang('status'),
				'list' => $status_options
			);

			$categories = $this->cats->formatted_xslt_list(array('format' => 'filter',
					'selected' => $this->cat_id, 'globals' => true, 'use_acl' => $this->_category_acl));
			$default_value = array('cat_id' => '', 'name' => lang('no category'));
			array_unshift($categories['cat_list'], $default_value);

			$_categories = array();
			foreach ($categories['cat_list'] as $_category)
			{
				$_categories[] = array('id' => $_category['cat_id'], 'name' => $_category['name']);
			}

			$combos[] = array('type' => 'filter',
				'name' => 'filter_category_id',
				'extra' => '',
				'text' => lang('category'),
				'list' => $_categories
			);

			return $combos;

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

			$function_msg = lang('application');

			$data = array(
				'datatable_name' => $function_msg,
				'form' => array(
					'toolbar' => array(
						'item' => array(
						)
					)
				),
				'datatable' => array(
					'source' => self::link(array(
						'menuaction' => 'eventplanner.uiapplication.index',
						'phpgw_return_as' => 'json'
					)),
					'allrows' => true,
					'new_item' => self::link(array('menuaction' => 'eventplanner.uiapplication.add')),
					'editor_action' => '',
					'field' => parent::_get_fields()
				)
			);

			$filters = $this->_get_filters();

			foreach ($filters as $filter)
			{
				array_unshift($data['form']['toolbar']['item'], $filter);
			}

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
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'eventplanner.uiapplication.view'
				)),
				'parameters' => json_encode($parameters)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'edit',
				'text' => lang('edit'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'eventplanner.uiapplication.edit'
				)),
				'parameters' => json_encode($parameters)
			);

			self::add_javascript('eventplanner', 'portico', 'application.index.js');
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
				$application = $values['object'];
			}
			else
			{
				$id = !empty($values['id']) ? $values['id'] : phpgw::get_var('id', 'int');
				$application = $this->bo->read_single($id);
			}


			$tabs = array();
			$tabs['first_tab'] = array(
				'label' => lang('application'),
				'link' => '#first_tab',
				'function' => "set_tab('first_tab')"
			);
			$tabs['demands'] = array(
				'label' => lang('demands'),
				'link' => '#demands',
				'function' => "set_tab('demands')"
			);
			$tabs['calendar'] = array(
				'label' => lang('calendar'),
				'link' => '#calendar',
				'function' => "set_tab('calendar')",
				'disable'	=> $id ? false : true
			);

			$bocommon = CreateObject('property.bocommon');

			$accounts = $GLOBALS['phpgw']->acl->get_user_list_right(PHPGW_ACL_EDIT, '.application', 'eventplanner');
			$case_officer_options[] = array('id' => '', 'name' => lang('select'), 'selected' => 0);
			foreach ($accounts as $account)
			{
				$case_officer_options[] = array(
					'id' => $account['account_id'],
					'name' => $GLOBALS['phpgw']->accounts->get($account['account_id'])->__toString(),
					'selected' => ($account['account_id'] == $application->case_officer_id) ? 1 : 0
				);
			}
			$comments = (array)$application->comments;
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

			$dates_def = array(
				array('key' => 'id', 'label' => lang('id'), 'sortable' => true, 'resizeable' => true,'formatter' => 'JqueryPortico.formatLink'),
				array('key' => 'from_', 'label' => lang('From'), 'sortable' => false, 'resizeable' => true),
				array('key' => 'to_', 'label' => lang('To'), 'sortable' => false, 'resizeable' => true),
				array('key' => 'active', 'label' => lang('active'), 'sortable' => false, 'resizeable' => true),
				array('key' => 'location', 'label' => lang('location'), 'sortable' => false, 'resizeable' => true),
				array('key' => 'customer_name', 'label' => lang('who'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'comment', 'label' => lang('Note'), 'sortable' => false, 'resizeable' => true),
				array('key' => 'application_id', 'hidden' => true),
			);

			$tabletools = array(
				array(
					'my_name' => 'add',
					'text' => lang('add'),
					'type' => 'custom',
					'className' => 'add',
					'custom_code' => "
								add_booking();"
				),
				array('my_name' => 'select_all'),
				array('my_name' => 'select_none'),
				array(
					'my_name' => 'enable',
					'text' => lang('enable'),
					'type' => 'custom',
					'custom_code' => "
								onActionsClick('enable');"
				),
				array(
					'my_name' => 'disable',
					'text' => lang('disable'),
					'type' => 'custom',
					'custom_code' => "
								onActionsClick('disable');"
				),
				array(
					'my_name' => 'edit',
					'text' => lang('edit'),
					'type' => 'custom',
					'custom_code' => "
								onActionsClick('edit');"
				)
			);

			$datatable_def[] = array(
				'container' => 'datatable-container_1',
				'requestUrl' => json_encode(self::link(array('menuaction' => 'eventplanner.uibooking.query',
					'filter_application_id' => $id,
					'filter_active'	=> 1,
					'phpgw_return_as' => 'json'))),
				'tabletools' => $tabletools,
				'ColumnDefs' => $dates_def,
				'data' => json_encode(array()),
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);
			$GLOBALS['phpgw']->jqcal->add_listener('date_start');
			$GLOBALS['phpgw']->jqcal->add_listener('date_end');
			$GLOBALS['phpgw']->jqcal2->add_listener('from_', 'datetime', $application->date_start, array(
					'min_date' => date('Y/m/d', $application->date_start),
					'max_date' => date('Y/m/d', $application->date_end)
				)
			);

			$application_type_list = execMethod('eventplanner.bogeneric.get_list', array('type' => 'application_type'));
			$types = (array)$application->types;
			if($types)
			{
				foreach ($application_type_list as &$application_type)
				{
					foreach ($types as $type)
					{
						if((!empty($type['type_id']) && $type['type_id'] == $application_type['id']) || ($type == $application_type['id']))
						{
							$application_type['selected'] = 1;
							break;
						}
					}
				}
			}
			$wardrobe_list = array();
			$wardrobe_list[] = array('id' => 0, 'name' => lang('no'));
			$wardrobe_list[] = array('id' => 1, 'name' => lang('yes'));

			foreach ($wardrobe_list as &$wardrobe)
			{
				$wardrobe['selected'] = $wardrobe['id'] == $application->wardrobe ? 1: 0;
			}

//			_debug_array($application_type_list);
//			_debug_array($application->types);
//			die();
			$data = array(
				'datatable_def' => $datatable_def,
				'form_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'eventplanner.uiapplication.save')),
				'cancel_url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'eventplanner.uiapplication.index',)),
				'application' => $application,
				'new_vendor_url' => self::link(array('menuaction' => 'eventplanner.uivendor.add')),
				'list_case_officer' => array('options' => $case_officer_options),
				'cat_select' => $this->cats->formatted_xslt_list(array(
					'select_name' => 'category_id',
					'selected'	=> $application->category_id,
					'use_acl' => $this->_category_acl,
					'required' => true)),
				'status_list' => array('options' => $this->get_status_options($application->status)),
				'application_type_list' => $application_type_list,
				'wardrobe_list'	=>  array('options' => $wardrobe_list),
				'mode' => $mode,
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'value_active_tab' => $active_tab
			);
			phpgwapi_jquery::formvalidator_generate(array('date', 'security', 'file'));
			phpgwapi_jquery::load_widget('autocomplete');
			self::add_javascript('eventplanner', 'portico', 'application.edit.js');
			self::render_template_xsl(array('application', 'datatable_inline'), array($mode => $data));
		}

		
		public function save()
		{
			parent::save();
		}

	}