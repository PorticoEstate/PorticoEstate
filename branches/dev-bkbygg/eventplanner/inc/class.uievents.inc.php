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
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/
	 * @package eventplanner
	 * @subpackage events
	 * @version $Id: $
	 */
	phpgw::import_class('phpgwapi.uicommon');
	phpgw::import_class('phpgwapi.datetime');

	include_class('eventplanner', 'events', 'inc/model/');

	class eventplanner_uievents extends phpgwapi_uicommon
	{
		public $public_functions = array(
			'index' => true,
			'query' => true,
			'edit' => true
		);

		protected
			$fields,
			$permissions,
			$currentapp;

		public function __construct()
		{
			parent::__construct();
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('events');
			$this->bo = createObject('eventplanner.boevents');
			$this->cats = & $this->bo->cats;
			$this->fields = eventplanner_events::get_fields();
			$this->permissions = eventplanner_events::get_instance()->get_permission_array();
			$this->currentapp = $GLOBALS['phpgw_info']['flags']['currentapp'];
			self::set_active_menu("{$this->currentapp}::events");
		}

		private function get_status_options( $selected = 0 )
		{
			$status_options = array();
			$status_list = eventplanner_events::get_status_list();

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
				'app' => $this->currentapp,
				'ui' => 'vendor',
				'function' => 'get_list',
				'label_attr' => 'name',
				'text' => lang('vendor') . ':',
				'requestGenerator' => 'requestWithVendorFilter'
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

			$function_msg = lang('events');

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
						'menuaction' => "{$this->currentapp}.uievents.index",
						'phpgw_return_as' => 'json'
					)),
					'allrows' => true,
					'sorted_by'	=> array('key' => 3, 'dir' => 'asc'),
					'editor_action' => '',
					'field' => parent::_get_fields()
				)
			);

			if($this->currentapp == 'eventplanner')
			{
				$filters = $this->_get_filters();

				foreach ($filters as $filter)
				{
					array_unshift($data['form']['toolbar']['item'], $filter);
				}
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
				'action' => self::link(array
					(
					'menuaction' => "{$this->currentapp}.uievents.edit"
				)),
				'parameters' => json_encode($parameters)
			);

			self::add_javascript($this->currentapp, 'portico', 'events.index.js');
			phpgwapi_jquery::load_widget('numberformat');

			self::render_template_xsl('datatable_jquery', $data);
		}


		public function edit( )
		{
			$active_tab = !empty($values['active_tab']) ? $values['active_tab'] : phpgw::get_var('active_tab', 'string', 'REQUEST', 'first_tab');
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('show');
			if (empty($this->permissions[PHPGW_ACL_READ]))
			{
				phpgw::no_access();
			}

			$id =  phpgw::get_var('id', 'int');
			$application = $this->bo->read_single($id);

			$tabs = array();
			$tabs['first_tab'] = array(
				'label' => lang('event'),
				'link' => '#first_tab'
			);


			$dates_def = array(
				array('key' => 'id', 'label' => lang('id'), 'sortable' => true, 'resizeable' => true,'formatter' => 'JqueryPortico.formatLink'),
				array('key' => 'from_', 'label' => lang('From'), 'sortable' => false, 'resizeable' => true),
				array('key' => 'to_', 'label' => lang('To'), 'sortable' => false, 'resizeable' => true),
				array('key' => 'status', 'label' => lang('status'), 'sortable' => false, 'resizeable' => true),
				array('key' => 'customer_name', 'label' => lang('who'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'location', 'label' => lang('location'), 'sortable' => false, 'resizeable' => true),
				array('key' => 'comment', 'label' => lang('Note'), 'sortable' => false, 'resizeable' => true),
				array('key' => 'application_id', 'hidden' => true),
			);


			$datatable_def[] = array(
				'container' => 'datatable-container_0',
				'requestUrl' => json_encode(self::link(array('menuaction' => "{$this->currentapp}.uicalendar.query_relaxed",
					'filter_application_id' => $id,
					'filter_active'	=> 1,
					'redirect'	=> 'booking',
					'phpgw_return_as' => 'json'))),
				'ColumnDefs' => $dates_def,
				'data' => json_encode(array()),
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
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

			$category = $this->cats->return_single($application->category_id);

			$application->public_type = $application->non_public == 1 ? lang('application public type non public') : lang('application public type public');

			$data = array(
				'datatable_def' => $datatable_def,
				'cancel_url' => self::link(array('menuaction' => "{$this->currentapp}.uievents.index",)),
				'application' => $application,
				'category_name' => $category[0]['name'],
				'status_list' => array('options' => $this->get_status_options($application->status)),
				'application_type_list' => $application_type_list,
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'value_active_tab' => $active_tab
			);
			self::render_template_xsl(array('events', 'application_info', 'datatable_inline'), array('edit' => $data));
		}


		public function save()
		{
			//Nothing to do here
			return false;
		}


		public function query()
		{
			$params = $this->bo->build_default_read_params();
			$params['filters']['status'] = eventplanner_events::STATUS_APPROVED;
			$values = $this->bo->read($params);
			array_walk($values["results"], array($this, "_add_links"), "{$this->called_class_arr[0]}.{$this->called_class_arr[1]}.edit");

			return $this->jquery_results($values);
		}
	}