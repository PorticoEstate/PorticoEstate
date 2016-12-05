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
	 * @internal Development of this booking was funded by http://www.bergen.kommune.no/ and Nordlandssykehuset
	 * @package eventplanner
	 * @subpackage booking
	 * @version $Id: $
	 */
	phpgw::import_class('eventplanner.uicommon');
	phpgw::import_class('phpgwapi.datetime');

	include_class('eventplanner', 'booking', 'inc/model/');

	class eventplanner_uibooking extends eventplanner_uicommon
	{

		public $public_functions = array(
			'add' => true,
			'index' => true,
			'query' => true,
			'view' => true,
			'edit' => true,
			'save' => true,
			'save_ajax' => true,
			'update_active_status' => true,
			'update_schedule'		=> true
		);
		protected
			$fields,
			$permissions;

		public function __construct()
		{
			parent::__construct();
			self::set_active_menu('eventplanner::booking');
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('booking');
			$this->bo = createObject('eventplanner.bobooking');
			$this->fields = eventplanner_booking::get_fields();
			$this->permissions = eventplanner_booking::get_instance()->get_permission_array();
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
							array(
								'type' => 'autocomplete',
								'name' => 'application',
								'app' => 'eventplanner',
								'ui' => 'application',
								'label_attr' => 'title',
								'text' => lang('application') . ':',
								'requestGenerator' => 'requestWithApplicationFilter'
							),
							array(
								'type' => 'checkbox',
								'name' => 'filter_active',
								'text' => lang('showall'),
								'value' => 1,
								'checked' => 1,
							)
						)
					)
				),
				'datatable' => array(
					'source' => self::link(array(
						'menuaction' => 'eventplanner.uibooking.index',
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

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'view',
				'text' => lang('show'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'eventplanner.uibooking.view'
				)),
				'parameters' => json_encode($parameters)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'edit',
				'text' => lang('edit'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'eventplanner.uibooking.edit'
				)),
				'parameters' => json_encode($parameters)
			);

			self::add_javascript('eventplanner', 'portico', 'booking.index.js');
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
				$booking = $values['object'];
			}
			else
			{
				$id = !empty($values['id']) ? $values['id'] : phpgw::get_var('id', 'int');
				$booking = $this->bo->read_single($id);
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
				'function' => "set_tab('reports')"
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
				'requestUrl' => json_encode(self::link(array('menuaction' => 'eventplanner.uivendor_report.query',
					'filter_booking_id' => $id,
					'filter_active'	=> 1,
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
								add_report('vendor');"
				)
			);
			$datatable_def[] = array(
				'container' => 'datatable-container_2',
//				'requestUrl' => json_encode(self::link(array('menuaction' => 'eventplanner.uicustomer_report.query',
//					'filter_booking_id' => $id,
//					'filter_active'	=> 1,
//					'phpgw_return_as' => 'json'))),
				'requestUrl' => "''",
				'ColumnDefs' => $customer_report_def,
				'data' => json_encode($customer_report),
				'tabletools' => $tabletools,
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$application = createObject('eventplanner.boapplication')->read_single($booking->application_id);

//			$GLOBALS['phpgw']->jqcal2->add_listener('from_', 'datetime', $booking->from_, array(
//					'min_date' => date('Y/m/d', $application->date_start),
//					'max_date' => date('Y/m/d', $application->date_end)
//				)
//			);

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

			$data = array(
				'datatable_def' => $datatable_def,
				'form_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'eventplanner.uibooking.save')),
				'cancel_url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'eventplanner.uibooking.index',)),
				'booking' => $booking,
				'application' => $application,
				'application_type_list' => $application_type_list,
				'new_customer_url' => self::link(array('menuaction' => 'eventplanner.uicustomer.add')),
				'application_url' => self::link(array('menuaction' => 'eventplanner.uiapplication.edit', 'id' => $booking->application_id)),
				'customer_url' => self::link(array('menuaction' => 'eventplanner.uicustomer.edit', 'id' => $booking->customer_id)),
				'mode' => $mode,
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'value_active_tab' => $active_tab
			);
			phpgwapi_jquery::formvalidator_generate(array());
			self::add_javascript('eventplanner', 'portico', 'booking.edit.js');
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

		public function update_active_status()
		{
			$ids = phpgw::get_var('ids', 'int');
			$action = phpgw::get_var('action', 'string');

			if ($this->bo->update_active_status($ids, $action))
			{
				return array(
					'status_kode' => 'ok',
					'status' => lang('ok'),
					'msg' => lang('messages_saved_form')
				);
			}
			else
			{
				return array
				(
					'status_kode' => 'error',
					'status' => lang('error'),
					'msg' => lang('messages_form_error')
				);
			}
		}

		public function update_schedule( )
		{
			$id = phpgw::get_var('id', 'int');
			$from_ = phpgw::get_var('from_', 'date');
			if ($this->bo->update_schedule($id, $from_))
			{
				return array(
					'status_kode' => 'ok',
					'status' => lang('ok'),
					'msg' => lang('messages_saved_form')
				);
			}
			else
			{
				return array
				(
					'status_kode' => 'error',
					'status' => lang('error'),
					'msg' => lang('messages_form_error')
				);
			}

		}
	}