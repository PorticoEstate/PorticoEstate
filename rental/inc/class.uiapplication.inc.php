<?php
	/**
	 * phpGroupWare - property: a part of a Facilities Management System.
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
	 * @package rental
	 * @subpackage application
	 * @version $Id: $
	 */
	phpgw::import_class('rental.uicommon');
	phpgw::import_class('phpgwapi.datetime');

	include_class('rental', 'application', 'inc/model/');

	class rental_uiapplication extends rental_uicommon
	{

		public $public_functions = array
			(
			'add' => true,
			'index' => true,
			'query' => true,
			'view' => true,
			'edit' => true,
			'save' => true,
		);

		protected
			$fields,
			$composite_types,
			$payment_methods;

		public function __construct()
		{
			parent::__construct();
			self::set_active_menu('rental::application');
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('application');
			$this->acl = & $GLOBALS['phpgw']->acl;
			$this->acl_location = '.application';
			$this->acl_read = $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'rental');
			$this->acl_add = $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'rental');
			$this->acl_edit = $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'rental');
			$this->acl_delete = $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'rental');
			$this->acl_manage = $this->acl->check($this->acl_location, PHPGW_ACL_PRIVATE, 'rental'); // manage
			$this->composite_types = rental_application::get_composite_types();
			$this->payment_methods = rental_application::get_payment_methods();
			$this->fields = rental_application::get_fields();
		}

		private function get_status_options( $selected = 0 )
		{
			$status_options = array();
			$status_options[] = array(
				'id' => '',
				'name' => lang('all')
			);
			$status_options[] = array(
				'id' => rental_application::STATUS_REGISTERED,
				'name' => lang('registered')
			);
			$status_options[] = array(
				'id' => rental_application::STATUS_PENDING,
				'name' => lang('pending')
			);
			$status_options[] = array(
				'id' => rental_application::STATUS_REJECTED,
				'name' => lang('rejected')
			);
			$status_options[] = array(
				'id' => rental_application::STATUS_APPROVED,
				'name' => lang('approved')
			);

			foreach ($status_options as &$entry)
			{
				$entry['selected'] = $entry['id'] == $selected ? 1 : 0;
			}
			return $status_options;
		}

		private function _get_fields()
		{
			$values = array();
			foreach ($this->fields as $field => $field_info)
			{
				if($field_info['action'] & PHPGW_ACL_READ)
				{
					$data = array(
						'key' => $field,
						'label' => !empty($field_info['label']) ? lang($field_info['label']) : $field,
						'sortable' => !empty($field_info['sortable']) ? true : false,
						'hidden' => !empty($field_info['hidden']) ? true : false,
					);

					if(!empty($field_info['formatter']))
					{
						$data['formatter'] = $field_info['formatter'];
					}

					$values[] = $data;
				}
			}
			return $values;
		}

		public function index()
		{
			if (!$this->isExecutiveOfficer())
			{
				phpgw::no_access();
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			phpgwapi_jquery::load_widget('autocomplete');

			$status_options = $this->get_status_options();
			$function_msg = lang('application');

			$data = array(
				'datatable_name' => $function_msg,
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array(
								'type' => 'filter',
								'name' => 'filter_status',
								'text' => lang('status'),
								'list' => $status_options
							),
							array('type' => 'autocomplete',
								'name' => 'ecodimb',
								'app' => 'property',
								'ui' => 'generic',
								'label_attr' => 'descr',
						//		'show_id'=> true,
								'text' => lang('dimb') . ':',
								'requestGenerator' => 'requestWithDimbFilter',
							),
						)
					)
				),
				'datatable' => array(
					'source' => self::link(array(
						'menuaction' => 'rental.uiapplication.index',
						'phpgw_return_as' => 'json'
					)),
					'allrows' => true,
					'new_item' => self::link(array('menuaction' => 'rental.uiapplication.add')),
					'editor_action' => '',
					'field' => $this->_get_fields()
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
					'menuaction' => 'rental.uiapplication.view'
				)),
				'parameters' => json_encode($parameters)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'edit',
				'text' => lang('edit'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'rental.uiapplication.edit'
				)),
				'parameters' => json_encode($parameters)
			);

			self::add_javascript('rental', 'rental', 'application.index.js');
			phpgwapi_jquery::load_widget('numberformat');

			self::render_template_xsl('datatable_jquery', $data);
		}
		/*
		 * View the price item with the id given in the http variable 'id'
		 */

		public function view()
		{
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('view');

			if (!self::isExecutiveOfficer())
			{
				phpgw::no_access();
			}

			$this->edit(array(), 'view');
		}
		/*
		 * Edit the price item with the id given in the http variable 'id'
		 */

		public function edit( $values = array(), $mode = 'edit' )
		{
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('edit');
			if (!$this->acl_add)
			{
				phpgw::no_access();
			}

			if (!empty($values['application']))
			{
				$application = $values['application'];
			}
			else
			{
				$application_id = phpgw::get_var('id', 'int');

				if (!empty($values['application_id']))
				{
					$application_id = $values['application_id'];
				}
				if ($application_id)
				{
					$application = rental_application::get($application_id);
				}
				else
				{
					$application = new rental_application();
				}
			}

			if (!$this->acl_edit)
			{
				$step = 1;
			}
			else if ($application->get_id())
			{
				$step = 2;
			}

			$tabs = array();
			$tabs['application'] = array('label' => lang('application'), 'link' => '#application');
			$tabs['party'] = array('label' => lang('party'), 'link' => '#party');
			if($step > 1)
			{
				$tabs['assignment'] = array('label' => lang('assignment'), 'link' => '#assignment');
			}

			$active_tab = 'showing';

			$composite_type = array();
			foreach ($this->composite_types as $_key => $_value)
			{
				$composite_type[] = array('id' => $_key, 'name' => $_value);
			}

			$payment_methods = array();
			foreach ($this->payment_methods as $_key => $_value)
			{
				$payment_methods[] = array('id' => $_key, 'name' => $_value);
			}

			$bocommon = CreateObject('property.bocommon');

			$GLOBALS['phpgw']->jqcal->add_listener('date_start');
			$GLOBALS['phpgw']->jqcal->add_listener('date_end');
			$GLOBALS['phpgw']->jqcal->add_listener('assign_date_start');
			$GLOBALS['phpgw']->jqcal->add_listener('assign_date_end');

			$accounts = $GLOBALS['phpgw']->acl->get_user_list_right(PHPGW_ACL_EDIT, $this->acl_location, 'rental');
			$executive_officer_options[] = array('id' => '', 'name' => lang('nobody'), 'selected' => 0);
			foreach ($accounts as $account)
			{
				$executive_officer_options[] = array(
					'id' => $account['account_id'],
					'name' => $GLOBALS['phpgw']->accounts->get($account['account_id'])->__toString(),
					'selected' => ($account['account_id'] == $application->executive_officer) ? 1 : 0
				);
			}

			$data = array(
				'form_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'rental.uiapplication.save')),
				'cancel_url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'rental.uiapplication.index',)),
				'application' => $application,//->toArray(),
				'list_executive_officer' => array('options' => $executive_officer_options),
				'step'		=> $step,
				'value_ecodimb_descr' => ExecMethod('property.bogeneric.get_single_attrib_value', array(
					'type' => 'dimb',
					'id' => $application->ecodimb_id,
					'attrib_name' => 'descr')
				),
				'district_list' => array('options' => $bocommon->select_district_list('', $application->district_id)),
				'composite_type_list' => array('options' => $bocommon->select_list($application->composite_type, $composite_type)),
				'payment_method_list' => array('options' => $bocommon->select_list($application->payment_method, $payment_methods)),
				'status_list' => array('options' => $this->get_status_options($application->status)),
				'mode' => $mode,
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
			);
			phpgwapi_jquery::formvalidator_generate(array('date', 'security', 'file'));
			phpgwapi_jquery::load_widget('autocomplete');
			self::add_javascript('rental', 'rental', 'application.edit.js');

			self::render_template_xsl(array('application'), array($mode => $data));
		}
		/*
		 * To be removed
		 * Add a new price item to the database.  Requires only a title.
		 */

		public function add()
		{
			if (!$this->acl_add)
			{
				phpgw::no_access();
			}

			$this->edit();
		}

		public function save()
		{
			if (!$this->acl_add)
			{
				phpgw::no_access();
			}
			_debug_array($_POST);
			$application_id = phpgw::get_var('id', 'int');

			if ($application_id)
			{
				$application = rental_soapplication::get_instance()->read_single($application_id, true);
			}
			else
			{
				$application = new rental_application();
				$application->status = $application::STATUS_REGISTERED;
			}
			/*
			 * Overrides with incoming data from POST
			 */
			$application = $this->_populate($application);

			if($application->validate())
			{
				if($application->store($application))
				{
					phpgwapi_cache::message_set(lang('messages_saved_form'), 'message');
					self::redirect(array(
						'menuaction' => 'rental.uiapplication.edit',
						'id'		=> $application->get_id()
						)
					);
				}
				else
				{
					phpgwapi_cache::message_set(lang('messages_form_error'), 'error');

				}
			}
			else
			{
				$this->edit(array('application'	=> $application));
			}
		}

		private function _populate($application)
		{
			$fields = $this->fields;

			foreach ($fields as $field	=> $field_info)
			{
				if($field_info['action'] & PHPGW_ACL_ADD)
				{
					$application->set_field( $field, phpgw::get_var($field, $field_info['type'] ) );
				}
			}
			return $application;
		}


		/**
		 * (non-PHPdoc)
		 * @see rental/inc/rental_uicommon#query()
		 */
		public function query()
		{
			$params = $this->build_default_read_params();
			$applications = rental_soapplication::get_instance()->read($params);
			$status_text = array(
				rental_application::STATUS_REGISTERED => lang('registered'),
				rental_application::STATUS_PENDING	=> lang('pending'),
				rental_application::STATUS_REJECTED => lang('rejected'),
				rental_application::STATUS_APPROVED	=> lang('approved')
			);

			foreach ($applications['results'] as &$application)
			{
					$application['status'] = $status_text[$application['status']];
					$application['composite_type'] = $this->composite_types[$application['composite_type']];

					$application['entry_date'] = $GLOBALS['phpgw']->common->show_date($application['entry_date'], $dateformat);
					$application['executive_officer'] = $application['executive_officer'] ? $GLOBALS['phpgw']->accounts->get($application['executive_officer'])->__toString() : '';
			}
			array_walk($applications["results"], array($this, "_add_links"), "rental.uiapplication.edit");

			return $this->jquery_results($applications);
		}

		protected function build_default_read_params()
		{
			$fields = $this->fields;

			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');

			$params = array(
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query' => $search['value'],
				'sort' => $columns[$order[0]['column']]['data'],
				'dir' => $order[0]['dir'],
				'allrows' => phpgw::get_var('length', 'int') == -1,
			);

			foreach ($fields as $field => $_params)
			{
				if (!empty($_REQUEST["filter_$field"]))
				{
					$params['filters'][$field] = phpgw::get_var("filter_$field", $_params['type']);
				}
			}

			return $params;
		}
	}