<?php

	/**
	 * phpGroupWare - controller: a part of a Facilities Management System.
	 *
	 * @author Erik Holm-Larsen <erik.holm-larsen@bouvet.no>
	 * @author Torstein Vadla <torstein.vadla@bouvet.no>
	 * @copyright Copyright (C) 2011,2012 Free Software Foundation, Inc. http://www.fsf.org/
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
	 * @subpackage controller
	 * @version $Id$
	 */
	/**
	 * Import the jQuery class
	 */
	phpgw::import_class('phpgwapi.jquery');

	phpgw::import_class('phpgwapi.uicommon_jquery');
	phpgw::import_class('controller.socheck_list');
	phpgw::import_class('phpgwapi.datetime');

	include_class('controller', 'check_list', 'inc/model/');
	include_class('controller', 'check_item', 'inc/model/');
	include_class('controller', 'date_generator', 'inc/component/');
	include_class('controller', 'date_converter', 'inc/helper/');
	include_class('controller', 'location_finder', 'inc/helper/');

	class controller_uicheck_list extends phpgwapi_uicommon_jquery
	{
		protected $so;
		protected $so_control;
		protected $so_control_item;
		protected $so_check_item;
		protected $so_procedure;
		protected $so_control_group_list;
		protected $so_control_group;
		protected $so_control_item_list;
		protected $location_finder;

		private $read;
		private $add;
		private $edit;
		private $delete;
		private $acl_location;

		var $public_functions = array(
			'index' => true,
			'add_check_list' => true,
			'save_check_list' => true,
			'edit_check_list' => true,
			'print_check_list' => true,
			'view_control_info' => true,
			'view_control_details' => true,
			'view_control_items' => true,
			'get_check_list_info' => true,
			'get_cases_for_check_list' => true,
			'update_status' => true
		);

		function __construct()
		{
			parent::__construct();

			$this->so_control = CreateObject('controller.socontrol');
			$this->so = CreateObject('controller.socheck_list');
			$this->so_control_item = CreateObject('controller.socontrol_item');
			$this->so_check_item = CreateObject('controller.socheck_item');
			$this->so_procedure = CreateObject('controller.soprocedure');
			$this->so_control_group_list = CreateObject('controller.socontrol_group_list');
			$this->so_control_group = CreateObject('controller.socontrol_group');
			$this->so_control_item_list = CreateObject('controller.socontrol_item_list');
			$this->so_case	= CreateObject('controller.socase');
			$this->location_finder = new location_finder();

			$this->acl_location = '.checklist';

			$this->read	= $GLOBALS['phpgw']->acl->check('.checklist', PHPGW_ACL_READ, 'controller');//1 
			$this->add	 = $GLOBALS['phpgw']->acl->check('.checklist', PHPGW_ACL_ADD, 'controller');//2 
			$this->edit	= $GLOBALS['phpgw']->acl->check('.checklist', PHPGW_ACL_EDIT, 'controller');//4 
			$this->delete  = $GLOBALS['phpgw']->acl->check('.checklist', PHPGW_ACL_DELETE, 'controller');//8 

			self::set_active_menu('controller::control::check_list');

			if(phpgw::get_var('noframework', 'bool'))
			{
				$GLOBALS['phpgw_info']['flags']['noframework'] = true;
				phpgwapi_cache::session_set('controller','noframework',true);
			}
			else if (phpgwapi_cache::session_get('controller','noframework'))
			{
				$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			}
			$GLOBALS['phpgw']->css->add_external_file('controller/templates/base/css/base.css');
		}

		/**
		 * Public function for displaying checklists  
		 * 
		 * @param HTTP:: phpgw_return_as
		 * @return data array
		 */
		public function index()
		{
			if(phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			$data = array(
				'datatable_name' => 'Sjekkliste (Ikke i bruk)',
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array('type' => 'filter',
								'name' => 'status',
								'text' => lang('Status'),
								'list' => array(
									array(
										'id' => 'none',
										'name' => lang('Not selected')
									),
									array(
										'id' => 'NEW',
										'name' => lang('NEW')
									),
									array(
										'id' => 'PENDING',
										'name' => lang('PENDING')
									),
									array(
										'id' => 'REJECTED',
										'name' => lang('REJECTED')
									),
									array(
										'id' => 'ACCEPTED',
										'name' => lang('ACCEPTED')
									)
								)
							),
							array('type' => 'text',
								'text' => lang('searchfield'),
								'name' => 'query'
							),
							array(
								'type' => 'submit',
								'name' => 'search',
								'value' => lang('Search')
							),
						),
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'controller.uicheck_list.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'id',
							'label' => lang('ID'),
							'sortable' => true,
							'formatter' => 'JqueryPortico.formatLink'
						),
						array(
							'key' => 'title',
							'label' => lang('Control title'),
							'sortable' => false
						),
						array(
							'key' => 'start_date',
							'label' => lang('start_date'),
							'sortable' => false
						),
						array(
							'key' => 'planned_date',
							'label' => lang('planned_date'),
							'sortable' => false
						),
						array(
							'key' => 'end_date',
							'label' => lang('end_date'),
							'sortable' => false
						),
						array(
							'key' => 'link',
							'hidden' => true
						)
					)
				),
			);

			self::render_template_xsl('datatable_jquery', $data);
		}

		/**
		 * Public function for displaying the add check list form
		 * 
		 * @param HTTP:: location code, control id, date
		 * @return data array
		 */
		function add_check_list($check_list = null)
		{
			if($check_list == null)
			{
				$type = phpgw::get_var('type');
				$control_id = phpgw::get_var('control_id');
				$deadline_ts = phpgw::get_var('deadline_ts');
				$deadline_current = phpgw::get_var('deadline_current', 'bool');
				$serie_id = phpgw::get_var('serie_id', 'int');

				if($deadline_current)
				{
					$year = date('Y');
					$month = date('m');
					$a_date = "{$year}-{$month}-23";
					$deadline_ts	= mktime(00, 00, 00, $month, date('t', strtotime($a_date)), $year);
					unset($year);
					unset($month);
					unset($a_date);

					/*look for checklist with $deadline_ts = $deadline_current*/

					$check_list_id = $this->so_control->get_check_list_id_for_deadline($serie_id, $deadline_ts);
					if($check_list_id)
					{
						$this->redirect(array('menuaction' => 'controller.uicheck_list.edit_check_list', 'check_list_id' => $check_list_id));
					}
				}

				$check_list = new controller_check_list();
				$check_list->set_control_id($control_id);
				$check_list->set_deadline($deadline_ts);
			}
			else
			{
				if($check_list->get_component_id() > 0)
				{
					$type = "component";
				}
				else
				{
					$type = "location";
				}
			}

			if(!$location_code = $check_list->get_location_code())
			{
				$location_code = phpgw::get_var('location_code');
				$check_list->set_location_code($location_code);
				$location_array = execMethod('property.bolocation.read_single', array('location_code' => $check_list->get_location_code()));
				$level = $this->location_finder->get_location_level($location_code);
			}


			if($type == "component")
			{
				if($check_list != null)
				{
					$location_id = phpgw::get_var('location_id');
					$check_list->set_location_id($location_id);
					$component_id = phpgw::get_var('component_id');
					$check_list->set_component_id($component_id);
				}

				$component_arr = execMethod('property.soentity.read_single_eav', array('location_id' => $location_id, 'id' => $component_id));

				$location_code = $component_arr['location_code'];

				$check_list->set_location_code($location_code);
				$location_array = execMethod('property.bolocation.read_single', array('location_code' => $check_list->get_location_code()));
				$level = $this->location_finder->get_location_level($location_code);

				$location_name = execMethod('property.bolocation.get_location_name', $component_arr['location_code']);

				$short_desc = $location_name . '::' . execMethod('property.soentity.get_short_description', array('location_id' => $location_id, 'id' => $component_id));

				$component = new controller_component();
				$component->set_id($component_id);
				$component->set_location_id($location_id);
				$component->set_location_code($component_arr['location_code']);
				$component->set_xml_short_desc($short_desc);

				$component_array = $component->toArray();
				$building_location_code = $this->location_finder->get_building_location_code($component_arr['location_code']);

				$type = "component";
			}
			else
			{
				$type = "location";
			}

			$repeat_descr = '';
			if($serie = $this->so_control->get_serie($serie_id))
			{
				$repeat_type_array = array
					(
						"0"=> lang('day'),
						"1"=> lang('week'),
						"2"=> lang('month'),
						"3"=> lang('year')
					);
				$repeat_descr = "{$repeat_type_array[$serie['repeat_type']]}/{$serie['repeat_interval']}";
			}

			$control = $this->so_control->get_single($check_list->get_control_id());

			if($repeat_descr)
			{
				$repeat_descr .= " :: " .$control->get_title();
				$control->set_title($repeat_descr);
			}

			if(!$responsible_user_id = phpgw::get_var('assigned_to', 'int'))
			{
				$responsible_user_id = execMethod('property.soresponsible.get_responsible_user_id',
					array
					(
						'responsibility_id' => $control->get_responsibility_id(),
						'location_code' => $location_code
					)
				);
			}

			$year = date("Y", $deadline_ts);
			$month_nr = date("n", $deadline_ts);

			$level = $this->location_finder->get_location_level($location_code);
			$user_role = true;

			// Fetches buildings on property
			$buildings_on_property = $this->location_finder->get_buildings_on_property($user_role, $location_code, $level);

			$users = $GLOBALS['phpgw']->acl->get_user_list_right(PHPGW_ACL_ADD, $this->acl_location);

			$user_list_options = array();
			foreach ($users as $user)
			{
				$user_list_options[] = array
				(
					'id' => $user['account_id'],
					'name' => $user['account_lastname'] . ', ' . $user['account_firstname'],
					'selected'	=> $responsible_user_id == $user['account_id'] ? 1 : 0
				);
			}

			$config	= CreateObject('phpgwapi.config','controller');
			$config->read();

			$required_actual_hours = isset($config->config_data['required_actual_hours']) && $config->config_data['required_actual_hours'] ? $config->config_data['required_actual_hours'] : false;

			$data = array
			(
				'user_list' => array('options' => $user_list_options),
				'location_array' => $location_array,
				'component_array' => $component_array,
				'control' => $control,
				'check_list' => $check_list,
				'buildings_on_property' => $buildings_on_property,
				'type' => $type,
				'current_year' => $year,
				'current_month_nr' => $month_nr,
				'building_location_code' => $building_location_code,
				'location_level' => $level,
				'check_list_type' => 'add_check_list',
				'serie_id'			=> $serie_id,
				'required_actual_hours'	=> $required_actual_hours,
				'integration'			=> $this->_get_component_integration($location_id, $component_arr)

			);

			$GLOBALS['phpgw']->jqcal->add_listener('planned_date');
			$GLOBALS['phpgw']->jqcal->add_listener('completed_date');

			self::add_javascript('controller', 'controller', 'custom_ui.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
			self::add_javascript('controller', 'controller', 'check_list.js');

			self::render_template_xsl(array('check_list/add_check_list', 'check_list/fragments/nav_control_plan',
				'check_list/fragments/check_list_top_section', 'check_list/fragments/add_check_list_menu',
				'check_list/fragments/select_buildings_on_property'), $data);
		}

		/**
		 * Public function for displaying the edit check list form  
		 * 
		 * @param HTTP:: check list id
		 * @return data array
		 */
		function edit_check_list($check_list = null)
		{
			if($check_list == null)
			{
				$check_list_id = phpgw::get_var('check_list_id');
				$check_list = $this->so->get_single($check_list_id);
			}

			$repeat_descr = '';
			if($serie = $this->so_control->get_serie($check_list->get_serie_id()))
			{
				$repeat_type_array = array
					(
						"0"=> lang('day'),
						"1"=> lang('week'),
						"2"=> lang('month'),
						"3"=> lang('year')
					);
				$repeat_descr = "{$repeat_type_array[$serie['repeat_type']]}/{$serie['repeat_interval']}";
			}

			$control = $this->so_control->get_single($check_list->get_control_id());

			if($repeat_descr)
			{
				$repeat_descr .= " :: " .$control->get_title();
				$control->set_title($repeat_descr);
			}

			$component_id = $check_list->get_component_id();

			if($component_id > 0)
			{
				$location_id = $check_list->get_location_id();
				$component_id = $check_list->get_component_id();

				$component_arr = execMethod('property.soentity.read_single_eav', array('location_id' => $location_id, 'id' => $component_id));
				$location_name = execMethod('property.bolocation.get_location_name', $component_arr['location_code']);

				$short_desc = $location_name . '::' . execMethod('property.soentity.get_short_description', array('location_id' => $location_id, 'id' => $component_id));

				$component = new controller_component();
				$component->set_id($component_id);
				$component->set_location_id($location_id);
				$component->set_location_code($component_arr['location_code']);
				$component->set_xml_short_desc($short_desc);
				$component_array = $component->toArray();

				$type = 'component';
				$building_location_code = $this->location_finder->get_building_location_code($component_arr['location_code']);
			}
			else
			{
				$location_code = $check_list->get_location_code();
				$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
				$type = 'location';
				$level = $this->location_finder->get_location_level($location_code);
			}

			$year = date("Y", $check_list->get_deadline());
			$month = date("n", $check_list->get_deadline());

			$level = $this->location_finder->get_location_level($location_code);
			$user_role = true;

			// Fetches buildings on property
			$buildings_on_property = $this->location_finder->get_buildings_on_property($user_role, $location_code, $level);

			$users = $GLOBALS['phpgw']->acl->get_user_list_right(PHPGW_ACL_ADD, $this->acl_location);

			$responsible_user_id = $check_list->get_assigned_to();

			$user_list_options = array();
			foreach ($users as $user)
			{
				$user_list_options[] = array
				(
					'id' => $user['account_id'],
					'name' => $user['account_lastname'] . ', ' . $user['account_firstname'],
					'selected'	=> $responsible_user_id == $user['account_id'] ? 1 : 0
				);
			}

			$config	= CreateObject('phpgwapi.config','controller');
			$config->read();

			$required_actual_hours = isset($config->config_data['required_actual_hours']) && $config->config_data['required_actual_hours'] ? $config->config_data['required_actual_hours'] : false;

			$data = array
			(
				'user_list' => array('options' => $user_list_options),
				'control' => $control,
				'check_list' => $check_list,
				'$buildings_on_property' => $buildings_on_property,
				'location_array' => $location_array,
				'component_array' => $component_array,
				'type' => $type,
				'current_year' => $year,
				'current_month_nr' => $month,
				'building_location_code' => $building_location_code,
				'location_level' => $level,
				'required_actual_hours'	=> $required_actual_hours,
				'integration'			=> $this->_get_component_integration($location_id, $component_arr)
			);

			$GLOBALS['phpgw']->jqcal->add_listener('planned_date');
			$GLOBALS['phpgw']->jqcal->add_listener('completed_date');
			$GLOBALS['phpgw']->jqcal->add_listener('deadline_date');

			self::add_javascript('controller', 'controller', 'custom_ui.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
			self::add_javascript('controller', 'controller', 'check_list.js');
			self::add_javascript('controller', 'controller', 'check_list_update_status.js');

			self::render_template_xsl(array('check_list/fragments/check_list_menu', 'check_list/fragments/nav_control_plan',
				'check_list/fragments/check_list_top_section', 'check_list/edit_check_list',
				'check_list/fragments/select_buildings_on_property'), $data);
		}

		/**
		 * Get linked information from external systems - as pictures
		 * @param integer $location_id
		 * @param array $component_arr
		 * @return array integration info
		 */
		private function _get_component_integration($location_id, $_component_arr = array())
		{
			if(isset($_component_arr['id']) && $_component_arr['id'])
			{
				$component_id = $_component_arr['id'];
			}
			else
			{
				return array();
			}
			$attributes = $GLOBALS['phpgw']->custom_fields->find2($location_id, 0, '', 'ASC','attrib_sort', $allrows = true);
	
			$component_arr = execMethod('property.soentity.read_single_eav', array('location_id' => $location_id, 'id' => $component_id, 'values' => array('attributes' => $attributes)));

			$_custom_config	= CreateObject('admin.soconfig',$location_id);
			$_config = isset($_custom_config->config_data) && $_custom_config->config_data ? $_custom_config->config_data : array();

			$integration = array();
			foreach ($_config as $_config_section => $_config_section_data)
			{
				if(isset($_config_section_data['tab']) && $component_arr['id'])
				{
					if(!isset($_config_section_data['url']))
					{
						phpgwapi_cache::message_set("'url' is a required setting for integrations, '{$_config_section}' is disabled", 'error');
						break;
					}

					//get session key from remote system
					$arguments = array($_config_section_data['auth_hash_name'] => $_config_section_data['auth_hash_value']);
					$query = http_build_query($arguments);
					$auth_url = $_config_section_data['auth_url'];
					$request = "{$auth_url}?{$query}";

					$aContext = array
					(
						'http' => array
						(
							'request_fulluri' => true,
						),
					);

					if(isset($GLOBALS['phpgw_info']['server']['httpproxy_server']))
					{
						$aContext['http']['proxy'] = "{$GLOBALS['phpgw_info']['server']['httpproxy_server']}:{$GLOBALS['phpgw_info']['server']['httpproxy_port']}";
					}

					$cxContext = stream_context_create($aContext);
					$response = trim(file_get_contents($request, False, $cxContext));

					$_config_section_data['url']		= htmlspecialchars_decode($_config_section_data['url']);
					$_config_section_data['parametres']	= htmlspecialchars_decode($_config_section_data['parametres']);

					parse_str($_config_section_data['parametres'], $output);

					foreach ($output as $_dummy => $_substitute)
					{
						$_keys[] = $_substitute;

						$__value = false;
						if(!$__value = urlencode($component_arr[str_replace(array('__','*'),array('',''), $_substitute)]))
						{
							foreach ($component_arr['attributes'] as $_attribute)
							{
								if(str_replace(array('__','*'),array('',''), $_substitute) == $_attribute['name'])
								{
									$__value = urlencode($_attribute['value']);
									break;
								}
							}
						}

						if($__value)
						{
							$_values[] = $__value;
						}
					}

					unset($output);
					unset($__value);
					$_sep = '?';
					if (stripos($_config_section_data['url'],'?'))
					{
						$_sep = '&';
					}
					$_param = str_replace($_keys, $_values, $_config_section_data['parametres']);
					unset($_keys);
					unset($_values);
					$integration_src = "{$_config_section_data['url']}{$_sep}{$_param}";
					if($_config_section_data['action'])
					{
						$_sep = '?';
						if (stripos($integration_src,'?'))
						{
							$_sep = '&';
						}
						$integration_src .= "{$_sep}{$_config_section_data['action']}=" . $_config_section_data["action_{$mode}"];
					}

					if(isset($_config_section_data['location_data']) && $_config_section_data['location_data'])
					{
						$_config_section_data['location_data']	= htmlspecialchars_decode($_config_section_data['location_data']);
						parse_str($_config_section_data['location_data'], $output);
						foreach ($output as $_dummy => $_substitute)
						{
							$_keys[] = $_substitute;
							$_values[] = urlencode($component_arr['location_data'][trim($_substitute, '_')]);
						}
						$integration_src .= '&' . str_replace($_keys, $_values, $_config_section_data['location_data']);
					}

					$integration_src .= "&{$_config_section_data['auth_key_name']}={$response}";

					$integration[]	= array
					(
						'section'	=> $_config_section,
						'height'	=> isset($_config_section_data['height']) && $_config_section_data['height'] ? $_config_section_data['height'] : 500,
						'src'		=> $integration_src
					);
				}
			}
			return $integration;
		}

		/**
		 * Public function for saving a check list
		 * 
		 * @param HTTP:: location code, control id, status etc.. (check list details) 
		 * @return data array
		 */
		function save_check_list()
		{
			$check_list_id = phpgw::get_var('check_list_id');
			if(!$this->add && !$this->edit)
			{
				phpgwapi_cache::message_set('No access', 'error');
				$this->redirect(array('menuaction' => 'controller.uicheck_list.edit_check_list', 'check_list_id' => $check_list_id));
			}

			$control_id = phpgw::get_var('control_id', 'int');
			$serie_id = phpgw::get_var('serie_id', 'int');
			$status = (int) phpgw::get_var('status');
			$type = phpgw::get_var('type');
			$deadline_date = phpgw::get_var('deadline_date', 'string');
			$planned_date = phpgw::get_var('planned_date', 'string');
			$completed_date = phpgw::get_var('completed_date', 'string');
			$comment = phpgw::get_var('comment', 'string');
			$assigned_to = phpgw::get_var('assigned_to', 'int');
			$billable_hours = phpgw::get_var('billable_hours', 'float');

			$deadline_date_ts = date_converter::date_to_timestamp($deadline_date);

			$error = false;

			if($planned_date != '')
			{
				$planned_date_ts = date_converter::date_to_timestamp($planned_date);
			}
			else
			{
				$planned_date_ts = $deadline_date_ts;
			}

			if($completed_date != '')
			{
				$completed_date_ts = phpgwapi_datetime::date_to_timestamp($completed_date);
				$status = controller_check_list::STATUS_DONE;
			}
			else
			{
				$completed_date_ts = 0;
			}

			if($check_list_id > 0)
			{
				$check_list = $this->so->get_single($check_list_id);
				
				if($status == controller_check_list::STATUS_DONE)
				{
					if(! $this->_check_for_required($check_list) )
					{
						$this->redirect(array('menuaction' => 'controller.uicheck_list.edit_check_list', 'check_list_id' => $check_list_id));
					}
				}
			}
			else
			{
				if($status == controller_check_list::STATUS_DONE)
				{
					$status = controller_check_list::STATUS_NOT_DONE;
					$completed_date_ts = 0;
					$error_message =  "Status kunne ikke settes til utført - prøv igjen";
					$error = true;
					phpgwapi_cache::message_set($error_message, 'error');
				}

				$check_list = new controller_check_list();
				$check_list->set_control_id($control_id);
				$location_code = phpgw::get_var('location_code');
				$check_list->set_location_code($location_code);
				$check_list->set_serie_id($serie_id);


				if($type == "component")
				{
					$location_id = phpgw::get_var('location_id');
					$component_id = phpgw::get_var('component_id');
					$check_list->set_location_id($location_id);
					$check_list->set_component_id($component_id);
				}
			}

			$check_list->set_comment($comment);
			$check_list->set_deadline($deadline_date_ts);
			$check_list->set_planned_date($planned_date_ts);
			$check_list->set_completed_date($completed_date_ts);

			$orig_assigned_to = $check_list->get_assigned_to();

			$check_list->set_assigned_to($assigned_to);

			$config	= CreateObject('phpgwapi.config','controller');
			$config->read();

			$required_actual_hours = isset($config->config_data['required_actual_hours']) && $config->config_data['required_actual_hours'] ? $config->config_data['required_actual_hours'] : false;

			if($status == controller_check_list::STATUS_DONE && $required_actual_hours && $check_list->get_billable_hours() == 0 && !$billable_hours)
			{
				phpgwapi_cache::message_set(lang("Please enter billable hours"), 'error');
				$error = true;
			}
			else
			{
				$check_list->set_delta_billable_hours($billable_hours);
			}
			if ( $status == controller_check_list::STATUS_DONE && $this->_check_for_required($check_list) && !$error)
			{
				$check_list->set_status($status);
			}
			else if ($status == controller_check_list::STATUS_CANCELED && !$error)
			{
				$check_list->set_status($status);
			}

			if(!$error && $check_list->validate())
			{
				$check_list_id = $this->so->store($check_list);
				$serie = $this->so_control->get_serie($check_list->get_serie_id());

				/**
				 * Add an iCal-event if there is a serie - and the checklist is visited the first time - or assigned is changed
				 */
				if(($check_list_id && $serie && !phpgw::get_var('check_list_id')) || ($serie && $orig_assigned_to != $assigned_to) )
				{
					$bocommon= CreateObject('property.bocommon');
					$current_prefs_user = $bocommon->create_preferences('property',$GLOBALS['phpgw_info']['user']['account_id']);
					$from_address = "{$GLOBALS['phpgw_info']['user']['fullname']}<{$current_prefs_user['email']}>";
					$from_name = $GLOBALS['phpgw_info']['user']['fullname'];

					$to_name = $GLOBALS['phpgw']->accounts->id2name($assigned_to);
					$prefs_target = $bocommon->create_preferences('property',$assigned_to);
					$to_address = $prefs_target['email'];

					if(! $start_date = $check_list->get_planned_date())
					{
						$start_date = $check_list->get_deadline();
					}
					$startTime = $start_date + 8 * 3600;

					$endTime = $startTime + ( (float)$serie['service_time'] * 3600 ) + ( (float)$serie['controle_time'] * 3600 );

					if($check_list->get_component_id() > 0)
					{
						$component_arr = execMethod('property.soentity.read_single_eav', array('location_id' => $check_list->get_location_id(), 'id' => $check_list->get_component_id()));
						$location_name = execMethod('property.bolocation.get_location_name', $component_arr['location_code']);
						$short_desc = $location_name . '::' . execMethod('property.soentity.get_short_description', array('location_id' => $check_list->get_location_id(), 'id' => $check_list->get_component_id()));
						$location = $location_name;
					}

					$repeat_type_array = array
						(
							"0"=> lang('day'),
							"1"=> lang('week'),
							"2"=> lang('month'),
							"3"=> lang('year')
						);

					$subject = "{$repeat_type_array[$serie['repeat_type']]}/{$serie['repeat_interval']}";
					$subject .= "::{$serie['title']}::{$short_desc}";

					$link_backend = $GLOBALS['phpgw']->link('/index.php', array(
						'menuaction'	=> 'controller.uicheck_list.add_check_list',
						'control_id'	=> $check_list->get_control_id(),
						'location_id'	=> $check_list->get_location_id(),
						'component_id'	=> $check_list->get_component_id(),
						'serie_id'		=> $check_list->get_serie_id(),
						'type'			=> 'component',
						'assigned_to'	=> $check_list->get_assigned_to(),
						'deadline_current'	=> true

					),false,true,true);

					$link_mobilefrontend = $GLOBALS['phpgw']->link('/mobilefrontend/index.php', array(
						'menuaction'	=> 'controller.uicheck_list.add_check_list',
						'control_id'	=> $check_list->get_control_id(),
						'location_id'	=> $check_list->get_location_id(),
						'component_id'	=> $check_list->get_component_id(),
						'serie_id'		=> $check_list->get_serie_id(),
						'type'			=> 'component',
						'assigned_to'	=> $check_list->get_assigned_to(),
						'deadline_current'	=> true

					),false,true,true);

					$html_description = "<a href ='{$link_mobilefrontend}'>Serie#" . $check_list->get_serie_id() .'::Mobilefrontend</a><br/><br/>';
					$html_description .= "<a href ='{$link_backend}'>Serie#" . $check_list->get_serie_id() .'::Backend</a>';

					$_serie_id = $check_list->get_serie_id();
					$text_description = str_replace('&amp;', '&', "Serie#{$_serie_id}::Mobilefrontend:\\n{$link_mobilefrontend}\\n\\nSerie#{$_serie_id}::Backend:\\n{$link_backend}");

					if($from_address && $to_address)
					{
						$this->sendIcalEvent($from_name, $from_address, $to_name, $to_address, $startTime, $endTime, $subject, $html_description, $text_description, $location);
					}
					else
					{
						phpgwapi_cache::message_set("Mangler epostadresse til avsender eller addresat - eller begge", 'error');
					}
				}

				if($check_list_id > 0)
				{
					$this->redirect(array('menuaction' => 'controller.uicheck_list.edit_check_list', 'check_list_id' => $check_list_id));
				}
				else
				{
					$this->edit_check_list($check_list);
				}
			}
			else
			{
				if($check_list->get_id() > 0)
				{
					$this->edit_check_list($check_list);
				}
				else
				{
					$this->redirect(array('menuaction' => 'controller.uicheck_list.add_check_list',
						'control_id'		=> $control_id,
						'location_id'		=> $location_id,
						'component_id'		=> $component_id,
						'serie_id'			=> $serie_id,
						'deadline_ts'		=> $deadline_date_ts,
						'type'				=> $type,
						'assigned_to'		=> $assigned_to,
						'status'			=> $status,
					//	'billable_hours' => $billable_hours
					));
				}
			}
		}

		function get_files2($location_id, $data)
		{
			$config	= CreateObject('phpgwapi.config','controller');
			$config->read();
			$doc_types = isset($config->config_data['document_cat']) && $config->config_data['document_cat'] ? $config->config_data['document_cat'] : array();
			$sodocument	= CreateObject('property.sodocument');

			$loc_arr = $GLOBALS['phpgw']->locations->get_name($location_id);
			$type_arr = explode('.',  $loc_arr['location']);
			if(count($type_arr) != 4)
			{
				return array();
			}

			$type		= $type_arr[1];
			$entity_id	= $type_arr[2];
			$cat_id		= $type_arr[3];

			$document_list = array();
			foreach ($doc_types as $doc_type)
			{
				if($doc_type)
				{
					$document_list = array_merge($document_list, $sodocument->read_at_location(array(
						'entity_id' => $entity_id,'cat_id' => $cat_id, 'id' =>$data['id'], 'doc_type' => $doc_type, 'allrows' => true)));
				}
			}
		
//			$valid_types = isset($config->config_data['document_valid_types']) && $config->config_data['document_valid_types'] ? str_replace ( ',' , '|' , $config->config_data['document_valid_types'] ) : '.pdf';

			$values = array();
//			if($valid_types)
			{
				$lang_view = lang('click to view file');
				foreach($document_list as $entry)
				{
//					if ( !preg_match("/({$valid_types})$/i", $entry['document_name']) )
//					{
//						continue;
//					}
					$link_file_data = array
					(
						'menuaction'	=> 'property.uidocument.view_file',
						'id'			=> $entry['document_id'],
						'p_num'			=> $data['id'],
						'cat_id'		=> $cat_id,
						'entity_id'		=> $entity_id,
					);

					$values[] = array
					(
						'document_id'			=> $entry['document_id'],
						'file_name'				=> $entry['document_name'],
						'file_name'				=>'<a href="'.$GLOBALS['phpgw']->link('/index.php',$link_file_data)."\" target='_blank' title='{$lang_view}'>{$entry['document_name']}</a>",
						'link'					=> $entry['link'],
						'title'					=> $entry['title'],
						'doc_type'				=> $entry['doc_type'],
						'document_date'			=> $GLOBALS['phpgw']->common->show_date($entry['document_date'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']),
					);
				}
			}
			return $values;
		}
		function get_files($location_id, $data)
		{
			$vfs = CreateObject('phpgwapi.vfs');
			$vfs->override_acl = 1;

			$location_data = explode('-', $data['location_code']);
			$loc1 = isset($location_data[0]) && $location_data[0] ? $location_data[0] : 'dummy';

			$loc_arr = $GLOBALS['phpgw']->locations->get_name($location_id);

			$type_arr = explode('.',  $loc_arr['location']);

			if(count($type_arr) != 4)
			{
				return array();
			}

			$type = $type_arr[1];
			$entity_id = $type_arr[2];
			$cat_id = $type_arr[3];
			$category_dir = "{$type}_{$entity_id}_{$cat_id}";

			$files = $vfs->ls (array(
				'string' => "/property/{$category_dir}/{$loc1}/{$data['id']}",
				'relatives' => array(RELATIVE_NONE)));

			$vfs->override_acl = 0;

			$values		= array();
			foreach ($files as $file)
			{
				$values[] = array
				(
					'name' 		=> $file['name'],
					'directory'	=> $file['directory'],
					'file_id'	=> $file['file_id'],
					'mime_type'	=> $file['mime_type']
				);
			}

			$link_file_data = array
			(
				'menuaction'	=> 'property.uientity.view_file',
				'loc1'			=> $loc1,
				'id'			=> $data['id'],
				'cat_id'		=> $cat_id,
				'entity_id'		=> $entity_id,
				'type'			=> $type
			);

			foreach($values as &$_entry )
			{
				$_entry['file_name'] = '<a href="'.$GLOBALS['phpgw']->link('/index.php',$link_file_data).'&amp;file_name='.urlencode($_entry['name']).'" target="_blank" title="'.lang('click to view file').'">'.$_entry['name'].'</a>';
			}

			return $values;
		}

		function view_control_info()
		{
			$check_list_id = phpgw::get_var('check_list_id');

			$check_list = $this->so->get_single($check_list_id);
			$control = $this->so_control->get_single($check_list->get_control_id());

			$component_id = $check_list->get_component_id();

			if($component_id > 0)
			{
				$location_id = $check_list->get_location_id();
				$component_id = $check_list->get_component_id();

				$component_arr = execMethod('property.soentity.read_single_eav', array('location_id' => $location_id, 'id' => $component_id));

				$location_name = execMethod('property.bolocation.get_location_name', $component_arr['location_code']);

				$short_desc = $location_name . '::' . execMethod('property.soentity.get_short_description', array('location_id' => $location_id, 'id' => $component_id));

				$component = new controller_component();
				$component->set_id($component_id);
				$component->set_location_id($location_id);
				$component->set_location_code($component_arr['location_code']);
				$component->set_xml_short_desc($short_desc);
				$component_array = $component->toArray();

				$type = 'component';
				$building_location_code = $this->location_finder->get_building_location_code($component_arr['location_code']);
			}
			else
			{
				$location_code = $check_list->get_location_code();
				$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
				$type = 'location';
				$level = $this->location_finder->get_location_level($location_code);
			}

			$year = date("Y", $check_list->get_deadline());
			$month = date("n", $check_list->get_deadline());

			$level = $this->location_finder->get_location_level($location_code);
			$user_role = true;

			// Fetches buildings on property
			$buildings_on_property = $this->location_finder->get_buildings_on_property($user_role, $location_code, $level);

			$data = array
			(
				'control'					=> $control,
				'check_list'				=> $check_list,
				'buildings_on_property'		=> $buildings_on_property,
				'location_array'			=> $location_array,
				'component_array'			=> $component_array,
				'type'						=> $type,
				'current_year'				=> $year,
				'current_month_nr'			=> $month,
				'building_location_code'	=> $building_location_code,
				'location_level'			=> $level
			);

			phpgwapi_jquery::load_widget('core');
			self::add_javascript('controller', 'controller', 'check_list_update_status.js');

			self::render_template_xsl(array('check_list/fragments/check_list_menu', 'check_list/fragments/check_list_top_section',
				'check_list/fragments/nav_control_plan', 'check_list/view_control_info',
				'check_list/fragments/select_buildings_on_property'), $data);
		}

		function view_control_details()
		{
			$control_id = phpgw::get_var('control_id');

			$control = $this->so_control->get_single($control_id);

			$check_list_id = phpgw::get_var('check_list_id','int');

			$check_list = $this->so->get_single($check_list_id);

			$component_id = $check_list->get_component_id();
			$files = array();

			if($component_id > 0)
			{
				$location_id = $check_list->get_location_id();
				$component_arr = execMethod('property.soentity.read_single_eav', array('location_id' => $location_id, 'id' => $component_id));
				$files = $this->get_files2($location_id,$component_arr);
			}

			$data = array
			(
				'control'	=> $control,
				'files'		=> $files

			);

			self::render_template_xsl('check_list/view_control_details', $data);
		}

		function view_control_items()
		{
			$check_list_id = phpgw::get_var('check_list_id');
			$check_list = $this->so->get_single($check_list_id);

			$control = $this->so_control->get_single($check_list->get_control_id());
			$control_groups = $this->so_control_group_list->get_control_groups_by_control($control->get_id());

			$saved_groups_with_items_array = array();

			//Populating array with saved control items for each group
			foreach($control_groups as $control_group)
			{
				$saved_control_items = $this->so_control_item_list->get_control_items_by_control_and_group($control->get_id(), $control_group->get_id());

				$control_item = $this->so_control_item->get_single($control_item_id);

				$saved_groups_with_items_array[] = array("control_group" => $control_group->toArray(), "control_items" => $saved_control_items);
			}

			$data = array
			(
				'saved_groups_with_items_array' => $saved_groups_with_items_array,
				'check_list' => $check_list
			);

			self::render_template_xsl('check_list/view_control_items', $data);
		}

		public function print_check_list()
		{
			$check_list_id = phpgw::get_var('check_list_id');
			$check_list = $this->so->get_single($check_list_id);

			$control = $this->so_control->get_single($check_list->get_control_id());
			$control_groups = $this->so_control_group_list->get_control_groups_by_control($control->get_id());

												$location_code = $check_list->get_location_code();
												$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
												$level = $this->location_finder->get_location_level($location_code);
												//var_dump($location_array);

			$saved_groups_with_items_array = array();

			//Populating array with saved control items for each group
			foreach($control_groups as $control_group)
			{
				$saved_control_items = $this->so_control_item_list->get_control_items_by_control_and_group($control->get_id(), $control_group->get_id());

				$control_item = $this->so_control_item->get_single($control_item_id);

				$saved_groups_with_items_array[] = array("control_group" => $control_group->toArray(), "control_items" => $saved_control_items);
			}

			$data = array
			(
				'saved_groups_with_items_array' => $saved_groups_with_items_array,
				'check_list' => $check_list,
																'control' => $control->toArray(),
																'location_array' => $location_array,
																'location_level' => $level
			);

			self::render_template_xsl('check_list/print_check_list', $data);
		}

		// Returns check list info as JSON
		public function get_check_list_info()
		{
			$check_list_id = phpgw::get_var('check_list_id');
			$check_list = $this->so_check_list->get_single_with_check_items($check_list_id, "open");

			return json_encode($check_list);
		}

		// Returns open cases for a check list as JSON 
		public function get_cases_for_check_list()
		{
			$check_list_id = phpgw::get_var('check_list_id');

			$check_items_with_cases = $this->so_check_item->get_check_items_with_cases($check_list_id, null, "open", null);

			return json_encode($check_items_with_cases);
		}

		/**
		 * Public function for updateing status for a check list
		 * 
		 * @return json encoded array with status saved or not saved
		 */
		public function update_status()
		{
			if(!$this->add && !$this->edit)
			{
				return json_encode( array( "status" => 'not_saved', 'message' => '') );
			}

			$check_list_id = phpgw::get_var('check_list_id');
			$check_list_status = phpgw::get_var('status');
			$check_list = $this->so->get_single($check_list_id);

//
			$config	= CreateObject('phpgwapi.config','controller');
			$config->read();
			$ok = true;

			$required_actual_hours = isset($config->config_data['required_actual_hours']) && $config->config_data['required_actual_hours'] ? $config->config_data['required_actual_hours'] : false;
			if($check_list_status == controller_check_list::STATUS_DONE && $required_actual_hours && $check_list->get_billable_hours() == 0)
			{
				phpgwapi_cache::message_set(lang("Please enter billable hours"), 'error');
				$ok = false;
			}
//
			if ( !$this->_check_for_required($check_list) || !$ok)
			{
				$messages = phpgwapi_cache::message_get(true);
				$message = '';
				foreach($messages as $_type => $_message)
				{
					if($_type == 'error')
					{
						$i = 1;
						foreach($_message as $__message)
						{
							$message.= "#{$i}: " . preg_replace("/<\/br[^>]*>\s*\r*\n*/is", "\n", $__message['msg']) . "\n";
							$i++;
						}
					}

				}
				return json_encode( array( "status" => 'not_saved', 'message' => $message) );

			}

			if($check_list_status == controller_check_list::STATUS_DONE)
			{
				$check_list->set_completed_date(time());
			}
			else
			{
				$check_list_status = controller_check_list::STATUS_NOT_DONE;
				$check_list->set_completed_date(0);
			}

			$check_list->set_status( $check_list_status );

			if($this->so->store($check_list))
			{
	   			return json_encode( array( 'status' => $check_list_status) );
			}
			else
			{
				return json_encode( array( "status" => 'not_saved', 'message' => '') );
			}
		}

		public function query()
		{

		}

		/**
		* Check for required items on all groups and for all components registered to the location.
		* @param object $check_list
		* @return bool
		**/
		private function _check_for_required($check_list)
		{
			$ok = true;
			$control = $this->so_control->get_single($check_list->get_control_id());

			$saved_control_groups = $this->so_control_group_list->get_control_groups_by_control($control->get_id());

			$required_control_items = array();
			foreach ($saved_control_groups as $control_group)
			{	
				$control_items = $this->so_control_item_list->get_control_items_and_options_by_control_and_group($control->get_id(), $control_group->get_id(), "return_array");
				$component_location_id = $control_group->get_component_location_id();
				$component_criteria = $control_group->get_component_criteria();

				foreach ($control_items as $control_item)
				{
					if($control_item['required'])
					{
						$control_item['component_location_id'] = $component_location_id;
						$control_item['component_criteria'] = $component_criteria;
						$required_control_items[] = $control_item;
					}
				}
			}

			$components_at_location = array();
			$control_groups_with_items_array = array();
			
			$component_id = $check_list->get_component_id();

			if($component_id > 0)
			{
				$location_id = $check_list->get_location_id();
				$component_id = $check_list->get_component_id();
				
				foreach ($required_control_items as $required_control_item)
				{
					$_ok = $this->so_case->get_cases_by_component($location_id, $component_id, $required_control_item['id'],$check_list->get_id());
					if(!$_ok)
					{
						$error_message =  lang('missing value for required') . "</br>";
						$error_message .=  "\"{$required_control_item['title']}\"</br>";
						$error_message .= execMethod('property.soentity.get_short_description', array('location_id' => $location_id, 'id' => $component_id));
						$error_message .=  "</br>";
						phpgwapi_cache::message_set($error_message, 'error');
//						echo $error_message;
						$ok = false;
					}
				}
			}
			else
			{
				$location_code = $check_list->get_location_code();
				$location_code_search_components = $location_code;
				$type = 'location';

				foreach ($required_control_items as $required_control_item)
				{
					$criterias_array = array();

					$component_location_id = $required_control_item['component_location_id'];
					$criterias_array['location_id'] = $component_location_id;
					$criterias_array['location_code'] = $location_code_search_components;
					$criterias_array['allrows'] = true;

					$component_criteria = $required_control_item['component_criteria'];

					$conditions = array();
					foreach ($component_criteria as $attribute_id => $condition)
					{
						if($condition['value'])
						{
							eval('$condition_value = ' . "{$condition['value']};");
							$conditions[] = array
							(
								'attribute_id'	=> $attribute_id,
								'operator'		=> $condition['operator'],
								'value'			=> $condition_value
							);
						}
					}

					$criterias_array['conditions'] = $conditions;

					if( !isset($components_at_location[$component_location_id][$location_code_search_components])  || !$_components_at_location = $components_at_location[$component_location_id][$location_code_search_components])
					{
						$_components_at_location = execMethod('property.soentity.get_eav_list', $criterias_array);
						$components_at_location[$component_location_id][$location_code_search_components] = $_components_at_location;
					}
						
					if($_components_at_location)
					{
						foreach($_components_at_location as &$_component_at_location)
						{
							$_ok = $this->so_case->get_cases_by_component($_component_at_location['location_id'], $_component_at_location['id'], $required_control_item['id'], $check_list->get_id());

							if(!$_ok)
							{
								$error_message =  "mangler registrering for required</br>";
								$error_message .=  "{$required_control_item['title']}</br>";
								$error_message .= execMethod('property.soentity.get_short_description', array('location_id' => $_component_at_location['location_id'], 'id' => $_component_at_location['id']));
								$error_message .=  "</br>";
								phpgwapi_cache::message_set($error_message, 'error');
//								echo $error_message;
								$ok = false;
							}
						}
					}
				}
			}
			$config	= CreateObject('phpgwapi.config','controller');
			$config->read();

			$required_actual_hours = isset($config->config_data['required_actual_hours']) && $config->config_data['required_actual_hours'] ? $config->config_data['required_actual_hours'] : false;

			if($check_list->get_status == controller_check_list::STATUS_DONE && $required_actual_hours && $check_list->get_billable_hours() == 0)
			{
				phpgwapi_cache::message_set(lang("Please enter billable hours"), 'error');
				$ok = false;
			}

			return $ok;
		}

		/**
		 * 
		 * @param string $from_name
		 * @param string $from_address
		 * @param string $to_name
		 * @param string $to_address
		 * @param int $startTime
		 * @param int $endTime
		 * @param string $subject
		 * @param string $html_description
		 * @param string $text_description
		 * @param string $location
		 * @return type
		 */
		function sendIcalEvent($from_name, $from_address, $to_name, $to_address, $startTime, $endTime, $subject, $html_description,$text_description, $location)
		{
//			https://www.exchangecore.com/blog/sending-outlookemail-calendar-events-php/

			$domain = $GLOBALS['phpgw_info']['server']['hostname'];

			//Create Email Headers
			$mime_boundary = "----Meeting Booking----".md5(time());

			//Create Email Body (HTML)
			$message  = <<<HTML
			--{$mime_boundary}
			Content-Type: text/html; charset=UTF-8
			Content-Transfer-Encoding: 8bit

			<html>
			<body>
			<p>Dear {$to_name}</p>
			<p>{$html_description}</p>
			</body>
			</html>
			--{$mime_boundary}
HTML;
			//Create Email Body (HTML)
			$message  = <<<HTML
			<html>
			<body>
			<p>Dear {$to_name}</p>
			<p>{$html_description}</p>
			</body>
			</html>
HTML;

			$last_modified =  date("Ymd\TGis");
			$uid = date("Ymd\TGis", $startTime).rand()."@".$domain;
			$dtstamp = date("Ymd\TGis");
			$dtstart = date("Ymd\THis", $startTime);
			$dtend = date("Ymd\THis", $endTime);
			$timezone = $GLOBALS['phpgw_info']['user']['preferences']['common']['timezone'];


$ical = <<<HTML
BEGIN:VCALENDAR
PRODID: controller {$domain}
VERSION:2.0
CALSCALE:GREGORIAN
METHOD:REQUEST
X-WR-TIMEZONE:Europe/Oslo
BEGIN:VEVENT
ORGANIZER;CN="{$to_name}":MAILTO:{$to_address}
ATTENDEE;CN="{$to_name}";ROLE=REQ-PARTICIPANT;RSVP=TRUE:MAILTO:{$to_address}
DTSTAMP:{$dtstamp}
DTSTART:{$dtstart}
DTEND:{$dtend}
SEQUENCE:0
STATUS:TENTATIVE
SUMMARY:{$subject}
LOCATION:{$location}
DESCRIPTION:{$text_description}
UID:{$uid}
BEGIN:VALARM
TRIGGER:-PT15M
ACTION:DISPLAY
DESCRIPTION:Reminder
END:VALARM
END:VEVENT
END:VCALENDAR
HTML;

//ORGANIZER;CN="{$from_name}":MAILTO:{$from_address}
//ATTENDEE;CN="{$to_name}";ROLE=REQ-PARTICIPANT;RSVP=TRUE:MAILTO:{$to_address}



//			$message .= $ical;
			$message = $ical;


			//Might work....
			$attachment = array
			(
			//	'content'		=> base64_encode($ical),
				'content'		=> $ical,
				'name'			=> 'meeting.ics',
			//	'encoding'		=> 'base64',//'7bit',
				'encoding'		=> '7bit',
				'type'			=> "text/calendar;charset=utf-8; method=REQUEST",
				'disposition'	=> 'inline'
			);

//test
			$mail = createObject('phpgwapi.mailer_smtp');
			$mail->Subject = $subject;
			$mail->Body =<<<HTML
			<html>
			<body>
			<p>{$to_name}:</p>
			<p>{$html_description}</p>
			</body>
			</html>
HTML;
			$mail->AltBody = $text_description; // For non HTML email client
			$mail->Ical = $ical; //Your manually created ical code
			$mail->IsHTML(true);
			$mail->isSMTP();
			$mail->AddAddress($to_address);


			$from = str_replace(array('[',']'),array('<','>'),$from_address);
			$from_array = split('<', $from);
			unset($from);
			if ( count($from_array) == 2 )
			{
				$mail->From = trim($from_array[1],'>');
				$mail->FromName = $from_array[0];
			}
			else
			{
				$mail->From = $from_array[0];
				$mail->FromName = $from_name;
			}

			try
			{
				$mail->Send();
			}
			catch (phpmailerException $e)
			{
				phpgwapi_cache::message_set($e->getMessage(), 'error');
			}
			return;
//test
			$rc = false;
			if (isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server'])
			{
				$send= CreateObject('phpgwapi.send');
				try
				{
					$rc = $send->msg('email', $to_address, $subject, $message, $msgtype='Ical', $cc='', $bcc='', $from_address, $from_name,'html',$mime_boundary);//, array($attachment));
				}
				catch (phpmailerException $e)
				{
					phpgwapi_cache::message_set($e->getMessage(), 'error');
				}
			}
			else
			{
				phpgwapi_cache::message_set(lang('SMTP server is not set! (admin section)'), 'error');
			}

			return $rc;
		}
	}
