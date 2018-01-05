<?php
	/**
	 * phpGroupWare - property: a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2017 Free Software Foundation, Inc. http://www.fsf.org/
	 * This file is part of phpGroupWare.
	 *
	 * phpGroupWare is free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation; either version 2 of the License, or
	 * (at your option) any later version.
	 *
	 * phpGroupWare is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with phpGroupWare; if not, write to the Free Software
	 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA	02110-1301	USA
	 *
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package property
	 * @subpackage entity
	 * @version $Id: class.uientity.inc.php 16615 2017-04-23 10:01:37Z sigurdne $
	 */
	/**
	 * Description
	 * @package property
	 */

	class property_controller_helper
	{

		protected
			$check_lst_time_span,
			$type_app,
			$type,
			$acl_read,
			$acl_add,
			$acl_edit,
			$acl_delete;

		public $public_functions = array
			(
			'get_controls_at_component' => true,
			'get_assigned_history' => true,
			'get_cases' => true,
			'get_checklists'=>true,
			'get_cases_for_checklist' => true,
			'add_control' => true,
			'update_control_serie' => true
		);

		function __construct($data = array())
		{
			$this->acl_read = !empty($data['acl_read']) ? $data['acl_read'] : false;
			$this->acl_add = !empty($data['acl_add']) ? $data['acl_add'] : false;
			$this->acl_edit = !empty($data['acl_edit']) ? $data['acl_edit'] : false;
			$this->acl_delete = !empty($data['acl_delete']) ? $data['acl_delete'] : false;
			$this->type_app = !empty($data['type_app']) ? $data['type_app'] : array();
			$this->type = !empty($data['type']) ? $data['type'] : false;
		}


		public function get_check_lst_time_span()
		{
			return $this->check_lst_time_span;
		}

		public function jquery_results( $result = array() )
		{
			if (!$result)
			{
				$result['recordsTotal'] = 0;
				$result['recordsFiltered'] = 0;
				$result['data'] = array();
			}

			$result['recordsTotal'] = $result['total_records'];
			$result['recordsFiltered'] = $result['recordsTotal'];
			$result['data'] = (array)$result['results'];
			unset($result['results']);
			unset($result['total_records']);

			return $result;
		}

		function get_assigned_history()
		{
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;

			if ($this->acl_read)
			{
				phpgw::no_access();
			}
			$serie_id = phpgw::get_var('serie_id', 'int');
			$history = execMethod('controller.socontrol.get_assigned_history', array('serie_id' => $serie_id));
			$lang_user = lang('user');
			$lang_date = lang('date');

			$ret = <<<HTML
			<html>
				<head>
				</head>
				<body>
					<table style="width:90%" align = 'center'>
						<tr align = 'left'>
							<th>
								{$lang_user}
							</th>
							<th>
								{$lang_date}
							</th>
						</tr>

HTML;
			foreach ($history as $entry)
			{
				$date = $GLOBALS['phpgw']->common->show_date($entry['assigned_date']);
				$ret .= <<<HTML
						<tr align = 'left'>
							<td>
								{$entry['assigned_to_name']}
							</td>
							<td>
								{$date}
							</td>
						</tr>
HTML;
			}
			$ret .= <<<HTML
					</table>
				</body>
			</html>
HTML;
			echo $ret;
		}

		public function get_controls_at_component( $location_id = 0, $id = 0, $skip_json = false )
		{
			$location_id = $location_id ? $location_id : phpgw::get_var('location_id', 'int');

			if (!$location_id)
			{
				$entity_id = phpgw::get_var('entity_id', 'int');
				$cat_id = phpgw::get_var('cat_id', 'int');
				$type = phpgw::get_var('type', 'string', 'REQUEST', 'entity');

				$location_id = $GLOBALS['phpgw']->locations->get_id($this->type_app[$type], ".{$type}.{$entity_id}.{$cat_id}");
			}

			$id = $id ? $id : phpgw::get_var('id', 'int');
			if (!$id)
			{
				return array();
			}

			if (!$this->acl_read)
			{
				phpgw::no_access();
			}

			$repeat_type_array = array
				(
				"0" => lang('day'),
				"1" => lang('week'),
				"2" => lang('month'),
				"3" => lang('year')
			);

			$lang_history = lang('history');
			$controls = execMethod('controller.socontrol.get_controls_at_component', array(
				'location_id' => $location_id, 'component_id' => $id));
			foreach ($controls as &$entry)
			{
				$menuaction = 'controller.uicomponent.index';

				$control_link_data = array
					(
					'menuaction' => $menuaction,
					'location_id' => $location_id,
					'component_id' => $id,
					'filter_component' => "{$location_id}_{$id}"
				);

				$entry['title_text'] = $entry['title'];
				$entry['title'] = '<a href="' . $GLOBALS['phpgw']->link('/index.php', $control_link_data) . '" target="_blank">' . $entry['title'] . '</a>';
				$entry['assigned_to_name'] = "<a title=\"{$lang_history}\" onclick='javascript:showlightbox_assigned_history({$entry['serie_id']});'>{$entry['assigned_to_name']}</a>";

				$entry['start_date'] = $GLOBALS['phpgw']->common->show_date($entry['start_date'], $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
				$entry['repeat_type'] = $repeat_type_array[$entry['repeat_type']];
				$entry['total_time'] = $entry['service_time'] + $entry['controle_time'];
			}

			$phpgw_return_as = phpgw::get_var('phpgw_return_as');

			if (($phpgw_return_as == 'json' && $skip_json) || $phpgw_return_as != 'json')
			{
				return $controls;
			}

			$result_data = array
				(
				'results' => $controls,
				'total_records' => count($controls),
				'draw' => phpgw::get_var('draw', 'int')
			);

			return $this->jquery_results($result_data);
		}

		/**
		 * Get controller cases related to this item.
		 * @param integer $location_id
		 * @param integer $id
		 * @param integer $year
		 * @return string
		 */
		public function get_cases( $location_id = 0, $id = 0, $year = 0 )
		{
			if (!$location_id)
			{
				$location_id = phpgw::get_var('location_id', 'int');
			}
			if (!$id)
			{
				$id = phpgw::get_var('id', 'int');
			}
			if (!$year)
			{
				$year = phpgw::get_var('year', 'int');
			}

//			$year = $year ? $year : -1; //all

			$_controls = $this->get_controls_at_component($location_id, $id, $skip_json = true);

			$socase = CreateObject('controller.socase');
			$controller_cases = $socase->get_cases_by_component($location_id, $id);
			$_statustext = array();
			$_statustext[0] = lang('open');
			$_statustext[1] = lang('closed');
			$_statustext[2] = lang('pending');

			$_cases = array();
			foreach ($controller_cases as $case)
			{
				$_case_year = date('Y', $case['modified_date']);

				if ($_case_year != $year && $year != -1)
				{
					continue;
				}

				$socheck_list = CreateObject('controller.socheck_list');
				$control_id = $socheck_list->get_single($case['check_list_id'])->get_control_id();
				foreach ($_controls as $_control)
				{
					if ($_control['control_id'] == $control_id)
					{
						$_control_name = $_control['title_text'];
						break;
					}
				}
//						_debug_array($check_list);die();

				switch ($case['status'])
				{
					case 0:
					case 2:
						$_method = 'view_open_cases';
						break;
					case 1:
						$_method = 'view_closed_cases';
						break;
					default:
						$_method = 'view_open_cases';
				}

				$_link = $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => "controller.uicase.{$_method}",
					'check_list_id' => $case['check_list_id']
					)
				);


				$_value_arr = array();

				if($case['measurement'])
				{
					$_value_arr[] = $case['measurement'];
				}
				if($case['descr'])
				{
					$_value_arr[] = $case['descr'];
				}

				$_cases[] = array
					(
					'url' => "<a href=\"{$_link}\" > {$case['check_list_id']}</a>",
					'type' => $_control_name,
					'title' => "<a href=\"{$_link}\" > {$case['title']}</a>",
					'value' => implode('</br>', $_value_arr),
					'status' => $_statustext[$case['status']],
					'user' => $GLOBALS['phpgw']->accounts->get($case['user_id'])->__toString(),
					'entry_date' => $GLOBALS['phpgw']->common->show_date($case['modified_date'], $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']),
				);
				unset($_link);
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				$result_data = array
					(
					'results' => $_cases,
					'total_records' => count($_cases),
					'draw' => phpgw::get_var('draw', 'int')
				);

				return $this->jquery_results($result_data);
			}
			return $_cases;
		}

		/**
		 * Get controller cases related to this item and a spesific checklist.
		 * @return array
		 */
		public function get_cases_for_checklist()
		{
			$check_list_id = phpgw::get_var('check_list_id', 'int');
			$so_check_item = CreateObject('controller.socheck_item');
			$controller_cases = $so_check_item->get_check_items_with_cases($check_list_id, $_type = null, 'all', null, null);

			$_statustext = array();
			$_statustext[0] = lang('open');
			$_statustext[1] = lang('closed');
			$_statustext[2] = lang('pending');

			$_case_years = array();
			$_cases = array();

			$socheck_list = CreateObject('controller.socheck_list');
			$socontrol = CreateObject('controller.socontrol');

			foreach ($controller_cases as $check_item)
			{
				$checklist_id = $check_item->get_check_list_id();
				$control_id = $socheck_list->get_single($checklist_id)->get_control_id();

				$_control_name = $socontrol->get_single($control_id)->get_title();

				$cases_array = $check_item->get_cases_array();
				foreach ($cases_array as $case)
				{
					switch ($case->get_status())
					{
						case 0:
						case 2:
							$_method = 'view_open_cases';
							break;
						case 1:
							$_method = 'view_closed_cases';
							break;
						default:
							$_method = 'view_open_cases';
					}

					$_link = $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => "controller.uicase.{$_method}",
						'check_list_id' => $check_list_id
						)
					);
					$_value_arr = array();

					if($case->get_measurement())
					{
						$_value_arr[] = $case->get_measurement();
					}
					if($case->get_descr())
					{
						$_value_arr[] = $case->get_descr();
					}

					$_cases[] = array
						(
						'url' => "<a href=\"{$_link}\" > {$check_list_id}</a>",
						'type' => $_control_name,
						'title' => "<a href=\"{$_link}\" >" . $check_item->get_control_item()->get_title() . "</a>",
						'value' => implode('</br>', $_value_arr),
						'status' => $_statustext[$case->get_status()],
						'user' => $GLOBALS['phpgw']->accounts->get($case->get_user_id())->__toString(),
						'entry_date' => $GLOBALS['phpgw']->common->show_date($case->get_modified_date(), $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']),
					);
					unset($_link);
				}
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				$result_data = array
					(
					'results' => $_cases,
					'total_records' => count($_cases),
					'draw' => phpgw::get_var('draw', 'int')
				);

				return $this->jquery_results($result_data);
			}
			return $_cases;
		}

		/**
		 * Get controller checklists related to this item.
		 * @param integer $location_id
		 * @param integer $id
		 * @param integer $year
		 * @return string
		 */
		public function get_checklists( $location_id = 0, $id = 0, $year = 0 )
		{
			if (!$location_id)
			{
				$location_id = phpgw::get_var('location_id', 'int');
			}
			if (!$id)
			{
				$id = phpgw::get_var('id', 'int');
			}
			if (!$year)
			{
				$year = phpgw::get_var('year', 'int', 'REQUEST', date('Y'));
			}
			$socheck_list = CreateObject('controller.socheck_list');

			$start_and_end = $socheck_list->get_start_and_end_for_component($location_id, $id);
			$start_year = date('Y', $start_and_end['start_timestamp']);
			$end_year = date('Y', $start_and_end['end_timestamp']);
			if (!$year)
			{
				$year = $end_year;
			}

			for ($j = $start_year; $j < ($end_year + 1); $j++)
			{
				$this->check_lst_time_span[] = array(
					'id' => $j,
					'name' => $j,
					'selected' => $j == date('Y') ? 1 : 0
				);
			}

			$from_date_ts = mktime(0, 0, 0, 1, 1, $year);
			$to_date_ts = mktime(23, 59, 59, 12, 31, $year);
			$socontrol = CreateObject('controller.socontrol');

			$control_id_with_check_list_array = $socheck_list->get_check_lists_for_component($location_id, $id, $from_date_ts, $to_date_ts);

			$_statustext = array();
			$_statustext[0] = lang('open');
			$_statustext[1] = lang('closed');
			$_statustext[2] = lang('pending');
			$_check_list = array();
			foreach ($control_id_with_check_list_array as $control)
			{
				$_control_name = $socontrol->get_single($control->get_id())->get_title();
				$check_lists = $control->get_check_lists_array();

				foreach ($check_lists as $check_list)
				{
					$_link = $GLOBALS['phpgw']->link('/index.php', array(
							'menuaction' => "controller.uicheck_list.edit_check_list",
							'check_list_id' => $check_list->get_id()
							)
					);
					$_check_list[] = array
						(
						'id' => $check_list->get_id(),
						'control_name' => "<a href=\"{$_link}\" >{$_control_name}</a>",
						'status' => $_statustext[$check_list->get_status()],
						'user' => $GLOBALS['phpgw']->accounts->get($check_list->get_assigned_to())->__toString(),
						'deadline' => $GLOBALS['phpgw']->common->show_date($check_list->get_deadline(), $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']),
						'planned_date' => $GLOBALS['phpgw']->common->show_date($check_list->get_planned_date(), $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']),
						'completed_date' => $GLOBALS['phpgw']->common->show_date($check_list->get_completed_date(), $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']),
						'num_open_cases' => $check_list->get_num_open_cases(),
						'num_pending_cases' => $check_list->get_num_pending_cases(),
					);
					unset($_link);
				}
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				$result_data = array
					(
					'results' => $_check_list,
					'total_records' => count($_check_list),
					'draw' => phpgw::get_var('draw', 'int')
				);

				return $this->jquery_results($result_data);
			}
			return $_check_list;
		}

		public function add_control()
		{
			$location_id = phpgw::get_var('location_id', 'int');
			$id = phpgw::get_var('id', 'int');
			$control_id = phpgw::get_var('control_id', 'int');
			$assigned_to = phpgw::get_var('control_responsible', 'int');
			$start_date = phpgw::get_var('control_start_date', 'string');
			$repeat_type = phpgw::get_var('repeat_type', 'int');
			$repeat_interval = phpgw::get_var('repeat_interval', 'int');
			$repeat_interval = $repeat_interval ? $repeat_interval : 1;
			$controle_time = phpgw::get_var('controle_time', 'float');
			$service_time = phpgw::get_var('service_time', 'float');

//			$location_info = $GLOBALS['phpgw']->locations->get_name($location_id);
//
//			if (substr($location_info['location'], 1, 6) == 'entity')
//			{
//				$type = 'entity';
//				$type_info = explode('.', $location_info['location']);
//				$entity_id = $type_info[2];
//				$cat_id = $type_info[3];
//				$component_arr = $this->so->read_single(array('entity_id' => $entity_id, 'cat_id' => $cat_id, 'id' => $id));
//				$link = array
//					(
//					'menuaction' => "property.uientity.{$function}",
//					'entity_id' => $entity_id,
//					'cat_id' => $cat_id,
//					'id' => $id
//				);
//			}
//
//			$location_code = $component_arr['location_code'];

			if ($start_date)
			{
				phpgw::import_class('phpgwapi.datetime');
				$start_date = phpgwapi_datetime::date_to_timestamp($start_date);
			}

			$result = array
				(
				'status_kode' => 'error',
				'status' => lang('error'),
				'msg' => lang('Missing input')
			);

			if ($control_id && $assigned_to && $id)
			{
				if (!$GLOBALS['phpgw']->acl->check('.admin', PHPGW_ACL_EDIT, 'property'))
				{
					$receipt['error'][] = true;
					$result = array
						(
						'status_kode' => 'error',
						'status' => lang('error'),
						'msg' => lang('you are not approved for this task')
					);
				}
				if (!$receipt['error'])
				{
					$so_control = CreateObject('controller.socontrol');
					$values = array
						(
						'register_component' => array("{$control_id}_{$location_id}_{$id}"),
						'assigned_to' => $assigned_to,
						'start_date' => $start_date,
						'repeat_type' => $repeat_type,
						'repeat_interval' => $repeat_interval,
						'controle_time' => $controle_time,
						'service_time' => $service_time,
						'duplicate' => true
					);
					//				_debug_array($values);
					if ($add = $so_control->register_control_to_component($values))
					{
						$result = array
							(
							'status_kode' => 'ok',
							'status' => 'Ok',
							'msg' => lang('updated')
						);
					}
					else
					{
						$result = array
							(
							'status_kode' => 'error',
							'status' => lang('error'),
							'msg' => 'Noe gikk galt'
						);
					}
				}
			}
			return $result;
		}

		function add_check_list( $data = array() )
		{
			phpgw::import_class('controller.socheck_list');
			include_class('controller', 'check_list', 'inc/model/');

			$control_id = $data['control_id'];
			$type = 'component';
			$comment = '';
			$assigned_to = $data['assigned_to'];
			$billable_hours = phpgw::get_var('billable_hours', 'float');

			$deadline_date_ts = $data['start_date'];
			$planned_date_ts = $deadline_date_ts;
			$completed_date_ts = 0;

			$check_list = new controller_check_list();
			$check_list->set_control_id($control_id);
			$check_list->set_location_code($data['location_code']);
			$check_list->set_location_id($data['location_id']);
			$check_list->set_component_id($data['component_id']);

			$status = controller_check_list::STATUS_NOT_DONE;
			$check_list->set_status($status);
			$check_list->set_comment($comment);
			$check_list->set_deadline($deadline_date_ts);
			$check_list->set_planned_date($planned_date_ts);
			$check_list->set_completed_date($completed_date_ts);
			$check_list->set_assigned_to($assigned_to);
			$check_list->set_billable_hours($billable_hours);

			$socheck_list = CreateObject('controller.socheck_list');

			if ($check_list->validate() && $check_list_id = $socheck_list->store($check_list))
			{
				return $check_list_id;
			}
			else
			{
				return false;
			}
		}

		function update_control_serie()
		{
			if ($start_date = phpgw::get_var('control_start_date', 'string'))
			{
				phpgw::import_class('phpgwapi.datetime');
				$start_date = phpgwapi_datetime::date_to_timestamp($start_date);
			}

			$so_control = CreateObject('controller.socontrol');

			$values = array
				(
				'ids' => phpgw::get_var('ids', 'int'),
				'action' => phpgw::get_var('action', 'string'),
				'assigned_to' => phpgw::get_var('control_responsible', 'int'),
				'start_date' => $start_date,
//				'repeat_type'		=> phpgw::get_var('repeat_type', 'int'),
				'repeat_interval' => phpgw::get_var('repeat_interval', 'int'),
				'controle_time' => phpgw::get_var('controle_time', 'float'),
				'service_time' => phpgw::get_var('service_time', 'float')
			);
			$ret = $so_control->update_control_serie($values);

			if ($ret)
			{
				$result = array
					(
					'status_kode' => 'ok',
					'status' => 'Ok',
					'msg' => lang('updated')
				);
			}
			else
			{
				$result = array
					(
					'status_kode' => 'error',
					'status' => lang('error'),
					'msg' => 'Noe gikk galt'
				);
			}

			return $result;
		}

	}