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
	 * @subpackage customer_report
	 * @version $Id: $
	 */
	phpgw::import_class('phpgwapi.uicommon');
	phpgw::import_class('phpgwapi.datetime');

//	include_class('eventplanner', 'customer_report', 'inc/model/');

	class eventplanner_uicustomer_report extends phpgwapi_uicommon
	{

		public $public_functions = array(
			'columns' => true,
			'add' => true,
			'index' => true,
			'query' => true,
			'view' => true,
			'edit' => true,
			'save' => true,
		);

		protected
			$fields,
			$permissions,
			$custom_fields,
			$currentapp;

		public function __construct()
		{
			parent::__construct();
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('customer report');
			$this->bo = createObject('eventplanner.bocustomer_report');
			$this->cats = & $this->bo->cats;
			$this->fields = eventplanner_customer_report::get_fields();
			$this->permissions = eventplanner_customer_report::get_instance()->get_permission_array();
			$this->custom_fields = eventplanner_customer_report::get_instance()->get_custom_fields();
			$this->currentapp = $GLOBALS['phpgw_info']['flags']['currentapp'];
			self::set_active_menu("{$this->currentapp}::customer_report");
			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];
			$this->acl_location = eventplanner_customer_report::acl_location;
		}


		private function _get_filters()
		{
			$combos = array();
			$combos[] = array(
				'type' => 'autocomplete',
				'name' => 'customer',
				'app' => 'eventplanner',
				'ui' => 'customer',
				'label_attr' => 'name',
				'text' => lang('customer') . ':',
				'requestGenerator' => 'requestWithVendorFilter'
			);

			return $combos;

		}

		function columns()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw']->xslttpl->add_file(array('columns'), PHPGW_SERVER_ROOT."/property/templates/base");

			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;

			$values = phpgw::get_var('values');
			$receipt = array();

			if (isset($values['save']) && $values['save'])
			{
				$GLOBALS['phpgw']->preferences->account_id = $this->account;
				$GLOBALS['phpgw']->preferences->read();
				$GLOBALS['phpgw']->preferences->add('eventplanner', 'customer_report_columns', (array)$values['columns'], 'user');
				$GLOBALS['phpgw']->preferences->save_repository();

				$receipt['message'][] = array('msg' => lang('columns is updated'));
			}

			$function_msg = lang('Select Column');

			$link_data = array
				(
				'menuaction' => "{$this->currentapp}.uicustomer_report.columns",
			);

			$msgbox_data = $this->bo->msgbox_data($receipt);
//			self::message_set($receipt);

			$data = array
				(
				'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'column_list' => $this->bo->column_list($values['columns'], $allrows = true),
				'function_msg' => $function_msg,
				'form_action' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'lang_columns' => lang('columns'),
				'lang_none' => lang('None'),
				'lang_save' => lang('save'),
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('columns' => $data));
		}


		function _get_fields()
		{
			$fields = parent::_get_fields();
			$custom_fields = (array)createObject('phpgwapi.custom_fields')->find('eventplanner', $this->acl_location, 0, '', '', '', true, false);
			$selected = (array)$GLOBALS['phpgw_info']['user']['preferences']['eventplanner']['customer_report_columns'];

			foreach ($custom_fields as $custom_field)
			{
				if( in_array( $custom_field['id'], $selected)  ||  $custom_field['list'])
				{
					$fields[] = array(
						'key' => $custom_field['name'],
						'label' =>  $custom_field['input_text'],
						'sortable' => true,
						'hidden' => false,
					);
				}
			}

			return $fields;

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

			$function_msg = lang('customer report');

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
						'menuaction' => "{$this->currentapp}.uicustomer_report.index",
						'phpgw_return_as' => 'json'
					)),
					'allrows' => true,
					"columns" => array('onclick' => "JqueryPortico.openPopup({menuaction:'{$this->currentapp}.uicustomer_report.columns'}, {closeAction:'reload'})"),
					'editor_action' => '',
					'field' => self::_get_fields()
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

/*			$data['datatable']['actions'][] = array
				(
				'my_name' => 'view',
				'text' => lang('show'),
				'action' => self::link(array
					(
					'menuaction' => "{$this->currentapp}.uicustomer_report.view"
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
					'menuaction' => "{$this->currentapp}.uicustomer_report.edit"
				)),
				'parameters' => json_encode($parameters)
			);

			self::add_javascript($this->currentapp, 'portico', 'customer_report.index.js');
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
				$customer_report = $values['object'];
			}
			else
			{
				$id = !empty($values['id']) ? $values['id'] : phpgw::get_var('id', 'int');
				$customer_report = $this->bo->read_single($id);
			}

			$booking_id = $customer_report->booking_id ? $customer_report->booking_id : phpgw::get_var('booking_id', 'int');
			$booking = createObject('eventplanner.bobooking')->read_single($booking_id);
			$calendar_id = $booking->calendar_id;
			$calendar = createObject('eventplanner.bocalendar')->read_single($calendar_id, true, $relaxe_acl = true);

			$application = createObject('eventplanner.boapplication')->read_single($calendar->application_id, true, $relaxe_acl = true);
			$application->summary = '';
			$application->remark = '';
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

			$custom_values = $customer_report->json_representation ? $customer_report->json_representation : array();
			$custom_fields = createObject('booking.custom_fields','eventplanner');
			$fields = $this->custom_fields;
			foreach ($fields as $attrib_id => &$attrib)
			{
				$attrib['value'] = isset($custom_values[$attrib['name']]) ? $custom_values[$attrib['name']] : null;

				if (isset($attrib['choice']) && is_array($attrib['choice']) && $attrib['value'])
				{
					foreach ($attrib['choice'] as &$choice)
					{
						if (is_array($attrib['value']))
						{
							$choice['selected'] = in_array($choice['id'], $attrib['value']) ? 1 : 0;
						}
						else
						{
							$choice['selected'] = $choice['id'] == $attrib['value'] ? 1 : 0;
						}
					}
				}
			}
//			_debug_array($fields);
			$organized_fields = $custom_fields->organize_fields(eventplanner_customer_report::acl_location, $fields);

			$tabs = array();
			$tabs['first_tab'] = array(
				'label' => lang('customer report'),
				'link' => '#first_tab',
				'function' => "set_tab('first_tab')"
			);

			$data = array(
				'form_action' => self::link(array('menuaction' => "{$this->currentapp}.uicustomer_report.save")),
				'cancel_url' => self::link(array('menuaction' => "{$this->currentapp}.uicustomer_report.index",)),
				'report' => $customer_report,
				'booking'		=> $booking,
				'application'	=> $application,
				'application_type_list' => $application_type_list,
				'booking_url' => self::link(array('menuaction' => "{$this->currentapp}.uibooking.edit", 'id' => $booking->id, 'active_tab' => 'reports')),
				'mode' => $mode,
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'value_active_tab' => $active_tab,
				'attributes_group' => $organized_fields,
			);
			phpgwapi_jquery::formvalidator_generate(array('date', 'security', 'file'));
			phpgwapi_jquery::load_widget('autocomplete');
		//	self::add_javascript($this->currentapp, 'portico', 'customer_report.edit.js');
			self::render_template_xsl(array('report','application_info', 'datatable_inline', 'attributes_form'), array($mode => $data));
		}


		public function save()
		{
			parent::save();
		}

	}