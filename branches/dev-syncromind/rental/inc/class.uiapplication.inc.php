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
	phpgw::import_class('rental.uicomposite');
	phpgw::import_class('rental.uicommon');
	phpgw::import_class('phpgwapi.datetime');

	include_class('rental', 'application', 'inc/model/');

	class rental_uiapplication extends rental_uicommon
	{

		public $public_functions = array(
			'add' => true,
			'index' => true,
			'query' => true,
			'view' => true,
			'edit' => true,
			'save' => true,
			'add_composite' => true,
			'remove_composite' => true
		);

		protected
			$fields,
			$composite_types,
			$payment_methods,
			$acl_location;
		private $bo;

		public function __construct()
		{
			parent::__construct();
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('application');
			$this->bo = createObject('rental.boapplication');
			$this->permissions = rental_application::get_instance()->get_permission_array();
			$this->composite_types = rental_application::get_composite_types();
			$this->payment_methods = rental_application::get_payment_methods();
			$this->fields = rental_application::get_fields();
			$this->acl_location = rental_application::acl_location;
			self::set_active_menu('rental::application');
		}

		private function get_composite_type_options( $selected = 0 )
		{
			$type_options = array();
			$type_list = execMethod('rental.bogeneric.get_list', array('type' => 'composite_type'));

			$type_options[] = array(
				'id' => '',
				'name' => lang('select')
			);

			foreach ($type_list as $type)
			{
				$type_options[] = array(
					'id' => $type['id'],
					'name' => $type['name'],
					'selected' => $type['id'] == $selected ? 1 : 0
				);
			}
			return $type_options;
		}

		private function get_status_options( $selected = 0 )
		{
			$status_options = array();
			$status_list = rental_application::get_status_list();

			$status_options[] = array(
				'id' => '',
				'name' => lang('all')
			);

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
							array(
								'type' => 'filter',
								'name' => 'filter_composite_type_id',
								'text' => lang('composite type'),
								'list' =>  $this->get_composite_type_options()
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
/*
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
*/
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
			$active_tab = !empty($values['active_tab']) ? $values['active_tab'] : phpgw::get_var('active_tab', 'string', 'REQUEST', 'application');
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('edit');
			if (empty($this->permissions[PHPGW_ACL_ADD]))
			{
				phpgw::no_access();
			}

			if (!empty($values['application']))
			{
				$application = $values['application'];
			}
			else
			{
				$application_id = !empty($values['application_id']) ? $values['application_id'] : phpgw::get_var('id', 'int');
				$application = $this->bo->read_single($application_id);
			}

			if (empty($this->permissions[PHPGW_ACL_EDIT]))
			{
				$step = 1;
			}
			else if ($application->get_id())
			{
				$step = 2;
			}

			$tabs = array();
			$tabs['application'] = array(
				'label' => lang('application'),
				'link' => '#application',
				'function' => "set_tab('application')"
			);
			$tabs['party'] = array(
				'label' => lang('party'),
				'link' => '#party',
				'function' => "set_tab('party')"
				);
			if($step > 1)
			{
				$tabs['assignment'] = array(
					'label' => lang('assignment'),
					'link' => '#assignment',
					'function' => "set_tab('assignment')"
				);
			}

			$composite_types = array();
			foreach ($this->composite_types as $_key => $_value)
			{
				$composite_types[] = array('id' => $_key, 'name' => $_value);
			}

			$payment_methods = array();
			foreach ($this->payment_methods as $_key => $_value)
			{
				$payment_methods[] = array('id' => $_key, 'name' => $_value);
			}

			$bocommon = CreateObject('property.bocommon');

			$GLOBALS['phpgw']->jqcal2->add_listener('date_start','datetime');
			$GLOBALS['phpgw']->jqcal2->add_listener('date_end','datetime');
			$GLOBALS['phpgw']->jqcal2->add_listener('assign_date_start','datetime');
			$GLOBALS['phpgw']->jqcal2->add_listener('assign_date_end','datetime');

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

			if(!$application->assign_date_start && $application->date_start)
			{
				$application->assign_date_start = $application->date_start;
			}
			if(!$application->assign_date_end && $application->date_end)
			{
				$application->assign_date_end = $application->date_end;
			}

			$data = array(
				'datatable_def' => $datatable_def,
				'form_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'rental.uiapplication.save')),
				'cancel_url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'rental.uiapplication.index',)),
				'application' => $application,
				'list_executive_officer' => array('options' => $executive_officer_options),
				'step'		=> $step,
				'value_ecodimb_descr' => ExecMethod('property.bogeneric.get_single_attrib_value', array(
					'type' => 'dimb',
					'id' => $application->ecodimb_id,
					'attrib_name' => 'descr')
				),
				'district_list' => array('options' => $bocommon->select_district_list('', $application->district_id)),
				'composite_type_list' => array('options' => $this->get_composite_type_options($application->composite_type_id)),
				'payment_method_list' => array('options' => $bocommon->select_list($application->payment_method, $payment_methods)),
				'status_list' => array('options' => $this->get_status_options($application->status)),
				'mode' => $mode,
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'value_active_tab' => $active_tab
			);

			// Composite
			$composite_id = (int)phpgw::get_var('id');

			$date = ($application->assign_date_start) ? date("Y-m-d", $application->assign_date_start) : "";
			$date = new DateTime($date);

			if ($date->format('w') != 1) {
				$date->modify('last monday');
			}

			$editable = phpgw::get_var('editable', 'bool');
			$type = 'all_composites';

			$filters = ExecMethod('rental.uicomposite.get_filters');

			foreach ($filters as $k1 => &$filter)
			{
				if ($filter['name'] == 'district_id')
				{
					unset($filters[$k1]);
					continue;
				}
				if ($filter['name'] == 'has_contract')
				{
					foreach ($filter['list'] as $k2 => $option)
					{
						if ($option['id'] == 'has_no_contract')
						{
							$filters[$k1]['list'][$k2]['selected'] = 'selected';
						}
					}
				}
			}

			$schedule['filters'] = $filters;

			$schedule['datasource_url'] = self::link(array(
				'menuaction' => 'rental.uicomposite.get_schedule',
				'editable' => ($editable) ? 1 : 0,
				'type' => $type,
				'phpgw_return_as' => 'json'
			));

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

			$toolbar = array();

			$toolbar[] = array(
				'name' => 'new',
				'text' => lang('new'),
				'action' => self::link(array(
					'menuaction' => 'rental.uicomposite.add'
				))
			);

			$toolbar[] = array(
				'name' => 'download',
				'text' => lang('download'),
				'action' => self::link(array(
					'menuaction' => 'rental.uicomposite.download',
					'type' => $type,
					'export' => true,
					'allrows' => true
				))
			);

			$toolbar[] = array(
				'name' => 'edit',
				'text' => lang('edit'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'rental.uicomposite.edit'
				)),
				'parameters' => $parameters
			);

			$toolbar[] = array(
				'name' => 'view',
				'text' => lang('show'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'rental.uicomposite.view'
				)),
				'parameters' => $parameters
			);

			$contract_types = rental_socontract::get_instance()->get_fields_of_responsibility();

			$valid_contract_types = array();
			if (isset($this->config->config_data['contract_types']) && is_array($this->config->config_data['contract_types']))
			{
				foreach ($this->config->config_data['contract_types'] as $_key => $_value)
				{
					if ($_value)
					{
						$valid_contract_types[] = $_value;
					}
				}
			}

			$create_types = array();
			foreach ($contract_types as $id => $label)
			{
				if ($valid_contract_types && !in_array($id, $valid_contract_types))
				{
					continue;
				}

				$names = $this->locations->get_name($id);
				if ($names['appname'] == $GLOBALS['phpgw_info']['flags']['currentapp'])
				{
					if ($this->hasPermissionOn($names['location'], PHPGW_ACL_ADD))
					{
						$create_types[] = array($id, $label);
					}
				}
			}

			foreach ($create_types as $create_type)
			{
				$toolbar[] = array
					(
					'name' => $create_type[1],
					'text' => lang('create_contract_' . $create_type[1]),
					'action' => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'rental.uicontract.add_from_composite',
						'responsibility_id' => $create_type[0]
					)),
					'attributes' => array(
						'class' => 'need-free'
					),
					'parameters' => $parameters
				);
			}
			
			$toolbar[] = array (
				'name' => 'reserve',
				'text' => 'Reserve',
				'callFunction' => array(
					'name' => 'reserveComposite',
					'args' => json_encode(array(
						'url' => self::link(array(
							'menuaction' => 'rental.uiapplication.add_composite',
							'phpgw_return_as' => 'json'
						))
					))
				),
				'attributes' => array(
					'class' => 'need-free'
				),
				'parameters' => $parameters
			);

			$schedule['composite_id'] = $composite_id;
			$schedule['date'] = $date;
			$schedule['picker_img'] = $GLOBALS['phpgw']->common->image('phpgwapi', 'cal');
			$schedule['toolbar'] = json_encode($toolbar);
			
			$composites['datasource_url'] = self::link(array(
				'menuaction' => 'rental.uicomposite.query',
				'editable' => ($editable) ? 1 : 0,
				'application_id' => $application->get_id(),
				'type' => $type,
				'phpgw_return_as' => 'json'
			));
			$uicols_composite = rental_socomposite::get_instance()->get_uicols();
			$columns_def = array();
			$uicols_count = count($uicols_composite['descr']);
			for ($i = 0; $i < $uicols_count; $i++)
			{
				if ($uicols_composite['input_type'][$i] != 'hidden')
				{
					$columns_def[$i]['key'] = $uicols_composite['name'][$i];
					$columns_def[$i]['label'] = $uicols_composite['descr'][$i];
					$columns_def[$i]['sortable'] = $uicols_composite['sortable'][$i];
				}
			}
			$composites['columns'] = json_encode($columns_def);
			$composites['application_id'] = $application->get_id();
			
			$toolbar_composites = array();
			$toolbar_composites[] = array (
				'name' => 'remove',
				'text' => 'Remove',
				'callFunction' => array(
					'name' => 'removeComposite',
					'args' => json_encode(array(
						'url' => self::link(array(
							'menuaction' => 'rental.uiapplication.remove_composite',
							'phpgw_return_as' => 'json'
						))
					))
				),
				'attributes' => array(
					'class' => 'need-free'
				),
				'parameters' => $parameters
			);
			$composites['toolbar'] = json_encode($toolbar_composites);

			$data['schedule'] = $schedule;
			$data['composites'] = $composites;

			phpgwapi_jquery::load_widget("datepicker");
			phpgwapi_jquery::formvalidator_generate(array('date', 'security', 'file'));
			phpgwapi_jquery::load_widget('autocomplete');
			self::add_javascript('rental','rental','schedule.js');
			self::add_javascript('rental', 'rental', 'application.edit.js');
			self::render_template_xsl(array('application', 'datatable_inline', 'rental_schedule'), array($mode => $data));
		}
		/*
		 * To be removed
		 * Add a new price item to the database.  Requires only a title.
		 */

		public function add()
		{
			if (empty($this->permissions[PHPGW_ACL_ADD]))
			{
				phpgw::no_access();
			}

			$this->edit();
		}

		public function save()
		{
			if (empty($this->permissions[PHPGW_ACL_ADD]))
			{
				phpgw::no_access();
			}
			$active_tab = phpgw::get_var('active_tab', 'string', 'REQUEST', 'application');

			$application_id = phpgw::get_var('id', 'int');

			$application = $this->bo->read_single($application_id, true);

			/*
			 * Overrides with incoming data from POST
			 */
			$application = $this->bo->populate($application);

			if($application->validate())
			{
				if($application->store($application))
				{
					phpgwapi_cache::message_set(lang('messages_saved_form'), 'message');
					self::redirect(array(
						'menuaction' => 'rental.uiapplication.edit',
						'id'		=> $application->get_id(),
						'active_tab' => $active_tab
						)
					);
				}
				else
				{
					phpgwapi_cache::message_set(lang('messages_form_error'), 'error');
					$this->edit(array('application'	=> $application, 'active_tab' => $active_tab));
				}
			}
			else
			{
				$this->edit(array('application'	=> $application, 'active_tab' => $active_tab));
			}
		}

		/**
		 * (non-PHPdoc)
		 * @see rental/inc/rental_uicommon#query()
		 */
		public function query()
		{
			$params = $this->bo->build_default_read_params();
			$applications = $this->bo->read($params);
			$status_text = rental_application::get_status_list();
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			foreach ($applications['results'] as $key => &$application)
			{
					$application['status'] = $status_text[$application['status']];
					$application['composite_type'] = $this->composite_types[$application['composite_type_id']];

					$application['entry_date'] = $GLOBALS['phpgw']->common->show_date($application['entry_date']);
					$application['assign_date_start'] = $GLOBALS['phpgw']->common->show_date($application['assign_date_start'], $dateformat);
					$application['assign_date_end'] = $GLOBALS['phpgw']->common->show_date($application['assign_date_end'], $dateformat);
					$application['executive_officer'] = $application['executive_officer'] ? $GLOBALS['phpgw']->accounts->get($application['executive_officer'])->__toString() : '';
					if (isset($params['filters']['composite_id']))
					{
						$composite_id = $params['filters']['composite_id'];
						$n_composites = 0;
						foreach ($application['composites'] as $value)
						{
							if ($value == $composite_id)
							{
								$n_composites++;
								break;
							}
						}
						if ($n_composites == 0)
						{
							$applications['total_records']--;
							$applications['recordsTotal']--;
							array_splice($applications['results'], $key, 1);
						}
					}
			}

			array_walk($applications["results"], array($this, "_add_links"), "rental.uiapplication.edit");

			return $this->jquery_results($applications);
		}
		
		public function add_composite()
		{
			$application_id = (int)phpgw::get_var('application_id');
			$composite_id = (int)phpgw::get_var('composite_id');
			
			$so_application = rental_soapplication::get_instance();
			$result = $so_application->add_composite($application_id, $composite_id);
			if ($result)
			{
				$message = 'Composite ' . $composite_id . ' ' . lang('has been added');
			}
			else
			{
				$message = 'Composite ' . $composite_id . ' ' . lang('not added');
			}
			return $message;
		}
		
		public function remove_composite()
		{
			$application_id = (int)phpgw::get_var('application_id');
			$composite_id = (int)phpgw::get_var('composite_id');
			
			$so_application = rental_soapplication::get_instance();
			$result = $so_application->remove_composite($application_id, $composite_id);
			if ($result)
			{
				$message = 'Composite ' . $composite_id . ' ' . lang('has been removed');
			}
			else
			{
				$message = 'Composite ' . $composite_id . ' ' . lang('not removed');
			}
			return $message;
		}
	}