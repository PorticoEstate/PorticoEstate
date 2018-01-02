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
	 * @internal Development of this booking was funded by http://www.bergen.kommune.no/
	 * @package eventplanner
	 * @subpackage booking
	 * @version $Id: $
	 */
	phpgw::import_class('phpgwapi.uicommon');
	phpgw::import_class('phpgwapi.datetime');

	include_class('eventplanner', 'booking', 'inc/model/');

	class eventplanner_uibooking extends phpgwapi_uicommon
	{

		public $public_functions = array(
			'add' => true,
			'index' => true,
			'query' => true,
			'query_relaxed'=> true,
			'get_list'=> true,
			'view' => true,
			'edit' => true,
			'save' => true,
			'save_ajax' => true,
		);
		protected
			$fields,
			$permissions,
			$currentapp;

		public function __construct()
		{
			parent::__construct();
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('booking');
			$this->bo = createObject('eventplanner.bobooking');
			$this->fields = eventplanner_booking::get_fields();
			$this->permissions = eventplanner_booking::get_instance()->get_permission_array();
			$this->currentapp = $GLOBALS['phpgw_info']['flags']['currentapp'];
			self::set_active_menu("{$this->currentapp}::booking");
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

			$function_msg = lang('booking');

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
						'menuaction' => "{$this->currentapp}.uibooking.index",
						'phpgw_return_as' => 'json'
					)),
					'allrows' => true,
			//		'new_item' => self::link(array('menuaction' => 'eventplanner.uibooking.add')),
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
					'menuaction' => "{$this->currentapp}.uibooking.view"
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
					'menuaction' => "{$this->currentapp}.uibooking.edit"
				)),
				'parameters' => json_encode($parameters)
			);

			self::add_javascript($this->currentapp, 'portico', 'booking.index.js');
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
				$message = '';
				if($this->currentapp == 'eventplannerfrontend')
				{
					$message = lang('you need to log in to access this page.');
				}
				phpgw::no_access(false, $message);
			}

			if (!empty($values['object']))
			{
				$booking = $values['object'];
			}
			else
			{
				$id = !empty($values['id']) ? $values['id'] : phpgw::get_var('id', 'int');
				$calendar_id = phpgw::get_var('calendar_id', 'int');
				if(!$id && $calendar_id)
				{
					$id = $this->bo->get_booking_id_from_calendar($calendar_id);
				}
				$booking = $this->bo->read_single($id);
			}

			if(!$calendar_id)
			{
				$calendar_id = $booking->calendar_id;
			}

			$tabs = array();
			$tabs['first_tab'] = array(
				'label' => lang('booking'),
				'link' => '#first_tab',
				'function' => "set_tab('first_tab')"
			);

			$tabs['reports'] = array(
				'label' => lang('reports'),
				'link' => '#reports',
				'function' => "set_tab('reports')",
				'disable'	=> $booking->customer_id ? false : true
			);

			$bocommon = CreateObject('property.bocommon');

			$comments = (array)$booking->comments;
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

			$vendor_report_def = array(
				array('key' => 'id', 'label' => lang('id'), 'sortable' => true, 'resizeable' => true,'formatter' => 'JqueryPortico.formatLink'),
				array('key' => 'created', 'label' => lang('Date'), 'sortable' => true, 'resizeable' => true),
			);

			$vendor_report = array();

			$tabletools = array(
				array(
					'my_name' => 'add',
					'text' => lang('add'),
					'type' => 'custom',
					'className' => 'add',
					'custom_code' => "
								add_report('vendor');"
				)
			);

			$datatable_def[] = array(
				'container' => 'datatable-container_1',
				'requestUrl' => json_encode(self::link(array('menuaction' => "{$this->currentapp}.uivendor_report.query",
					'filter_booking_id' => $id ? $id : -1,
					'phpgw_return_as' => 'json'))),
				'ColumnDefs' => $vendor_report_def,
				'data' => json_encode($vendor_report),
				'tabletools' => $tabletools,
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$customer_report_def = array(
				array('key' => 'id', 'label' => lang('id'), 'sortable' => true, 'resizeable' => true,'formatter' => 'JqueryPortico.formatLink'),
				array('key' => 'created', 'label' => lang('Date'), 'sortable' => true, 'resizeable' => true),
			);

			$customer_report = array();
			$tabletools = array(
				array(
					'my_name' => 'add',
					'text' => lang('add'),
					'type' => 'custom',
					'className' => 'add',
					'custom_code' => "
								add_report('customer');"
				)
			);
			$datatable_def[] = array(
				'container' => 'datatable-container_2',
				'requestUrl' => json_encode(self::link(array('menuaction' => "{$this->currentapp}.uicustomer_report.query",
					'filter_booking_id' => $id ? $id : -1,
					'phpgw_return_as' => 'json'))),
				'ColumnDefs' => $customer_report_def,
				'data' => json_encode($customer_report),
				'tabletools' => $tabletools,
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$calendar = createObject('eventplanner.bocalendar')->read_single($calendar_id, true, $relaxe_acl = true);

			$application = createObject('eventplanner.boapplication')->read_single($calendar->application_id, true, $relaxe_acl = true);

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

			$application_url = self::link(array('menuaction' => "{$this->currentapp}.uiapplication.edit", 'id' => $calendar->application_id));
			$lang_application = lang('application');
			if($this->currentapp == 'eventplannerfrontend')
			{
				$application_url = self::link(array('menuaction' => "{$this->currentapp}.uievents.edit", 'id' => $calendar->application_id));
				$lang_application = lang('event');
			}

			if($booking->customer_id && !$booking->customer_name)
			{
				$booking->customer_name = createObject('eventplanner.bocustomer')->read_single($booking->customer_id)->name;
			}

			$data = array(
				'datatable_def' => $datatable_def,
				'form_action' => self::link(array('menuaction' => "{$this->currentapp}.uibooking.save", 'calendar_id' => $calendar_id)),
				'cancel_url' => self::link(array('menuaction' => "{$this->currentapp}.uibooking.index",)),
				'calendar'	=>$calendar,
				'booking' => $booking,
				'application' => $application,
				'application_type_list' => $application_type_list,
				'new_customer_url' => self::link(array('menuaction' => "{$this->currentapp}.uicustomer.add")),
				'application_url' => $application_url,
				'lang_application' => $lang_application,
				'customer_url' => self::link(array('menuaction' => "{$this->currentapp}.uicustomer.edit", 'id' => $booking->customer_id)),
				'mode' => $mode,
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'value_active_tab' => $active_tab
			);
			phpgwapi_jquery::formvalidator_generate(array());
			self::add_javascript($this->currentapp, 'portico', 'booking.edit.js');
			phpgwapi_jquery::load_widget('autocomplete');
			self::render_template_xsl(array('booking', 'datatable_inline'), array($mode => $data));
		}

		public function save()
		{
			parent::save();
		}

		public function save_ajax()
		{
			return parent::save(true);
		}
	}