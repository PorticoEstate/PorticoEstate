<?php
	/**
	 * Frontend : a simplified tool for end users.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2010 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package Frontend
	 * @version $Id: class.uientity.inc.php 11914 2014-04-23 13:12:52Z sigurdne $
	 */
	/*
	  This program is free software: you can redistribute it and/or modify
	  it under the terms of the GNU General Public License as published by
	  the Free Software Foundation, either version 2 of the License, or
	  (at your option) any later version.

	  This program is distributed in the hope that it will be useful,
	  but WITHOUT ANY WARRANTY; without even the implied warranty of
	  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	  GNU General Public License for more details.

	  You should have received a copy of the GNU General Public License
	  along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	phpgw::import_class('frontend.uicommon');

	/**
	 * Controller
	 *
	 * @package Frontend
	 */
	class frontend_uicontroller extends frontend_uicommon
	{

		public $public_functions = array
		(
			'index' => true,
			'view' => true,
			'query' => true
		);

		public function __construct()
		{
			$this->acl_location = '.controller';
			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];

			$this->acl = & $GLOBALS['phpgw']->acl;
			$this->acl_read = $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'frontend');

			phpgwapi_cache::session_set('frontend', 'tab', $this->location_id);
			parent::__construct();
			$this->location_code = $this->header_state['selected_location'];
			$GLOBALS['phpgw']->translation->add_app('controller');
		}

		/**
		 * Get the sublevels of the org tree into one arry
		 */
		private function _get_children( $data = array(), &$_org_units )
		{
			foreach ($data as $entry)
			{
				$_org_units[$entry['id']] = true;
				if (isset($entry['children']) && $entry['children'])
				{
					$this->_get_children($entry['children'], $_org_units);
				}
			}
		}

		public function index()
		{
			$GLOBALS['phpgw_info']['apps']['manual']['section'] = 'controller.index';
			$this->insert_links_on_header_state();
			//redirect if no rights

			if (!$this->acl_read)
			{
				phpgw::no_access();
			}

			$filters = array();
			
			
			$so_control = CreateObject('controller.socontrol');
			
			$control_types = $so_control->get_controls_by_control_area();

			$user_id = $GLOBALS['phpgw_info']['user']['account_id'];

			$control_id = (int)phpgwapi_cache::user_get('controller', "calendar_planner_control_id", $user_id);


			$search_option = array(array('id' => '', 'name' => lang('select')));
			foreach ($control_types as $control_type)
			{
				$search_option[] = array(
					'id'		 => $control_type['id'],
					'name'		 => $control_type['title'],
					'selected'	 => $control_id == $control_type['id'] ? 1 : 0
				);
			}

			$filters[] = array
				(
				'type' => 'filter',
				'name' => 'control_id',
				'text' => lang('control type'),
				'list' => $search_option
			);

			$uicols = array();

			$uicols['name'][] = 'id';
			$uicols['descr'][] = lang('id');
			$uicols['name'][] = 'loc1_name';
			$uicols['descr'][] = lang('name');
			$uicols['name'][] = 'comment';
			$uicols['descr'][] = lang('comment');
			$uicols['name'][] = 'location_code';
			$uicols['descr'][] = lang('location_code');
			$uicols['name'][] = 'num_open_cases';
			$uicols['descr'][] = lang('num_open_cases');
			$uicols['name'][] = 'num_corrected_cases';
			$uicols['descr'][] = lang('num_corrected_cases');
			$uicols['name'][] = 'completed_date_text';
			$uicols['descr'][] = lang('completed_date');

			$count_uicols_name = count($uicols['name']);

			$uicols_helpdesk = array();
			for ($k = 0; $k < $count_uicols_name; $k++)
			{
				$params = array(
					'key' => $uicols['name'][$k],
					'label' => $uicols['descr'][$k],
					'sortable' => ($uicols['sortable'][$k]) ? true : false,
					'hidden' => ($uicols['input_type'][$k] == 'hidden') ? true : false
				);

				$params['sortable'] = false;
				if ($uicols['name'][$k] == 'id' || $uicols['name'][$k] == 'user' || $uicols['name'][$k] == 'completed_date_text')
				{
					$params['sortable'] = true;
				}
				if ($uicols['name'][$k] == 'id')
				{
					$params['hidden'] = true;
				}

				array_push($uicols_helpdesk, $params);
			}

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

			$tabletools[] = array
				(
				'my_name' => 'view',
				'text' => lang('view'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'frontend.uicontroller.view',
					'location_id' => $this->location_id,
				)),
				'target'	 => '_blank',
				'parameters' => json_encode($parameters)
			);


			$datatable_def[] = array
				(
				'container' => 'datatable-container_0',
				'requestUrl' => json_encode(self::link(array
						(
						'menuaction' => 'frontend.uicontroller.query',
						'location_id' => $this->location_id,
						'phpgw_return_as' => 'json'))
				),
				'ColumnDefs' => $uicols_helpdesk,
				'tabletools' => $tabletools,
				'config'	 => array(
					array('order' => json_encode(array(1, 'desc'))),
				)
			);

			$msglog = phpgwapi_cache::session_get('frontend', 'msgbox');
			phpgwapi_cache::session_clear('frontend', 'msgbox');

			$data = array(
				'header' => $this->header_state,
				'section' => array(
					'datatable_def' => $datatable_def,
					'tabs' => $this->tabs,
					'tabs_content' => $this->tabs_content,
					'filters' => $filters,
					'tab_selected' => $this->tab_selected,
					'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($GLOBALS['phpgw']->common->msgbox_data($msglog))
				),
				'lightbox_name' => lang('add ticket')
			);
			self::render_template_xsl(array('controller', 'datatable_inline', 'frontend'), $data);
		}

		public function query()
		{
			phpgwapi_cache::session_clear('frontend', 'msgbox');

			$so = CreateObject('controller.socheck_list');
			
			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');
			$control_id = phpgw::get_var('control_id', 'int');

			$user_id = $GLOBALS['phpgw_info']['user']['account_id'];

			if($control_id)
			{
				phpgwapi_cache::user_set('controller', "calendar_planner_control_id", $control_id, $user_id);
			}
			else
			{
				$control_id = (int)phpgwapi_cache::user_get('controller', "calendar_planner_control_id", $user_id);				
			}
			
			$params = array(
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query' => $search['value'],
				'order' => ($columns[$order[0]['column']]['data'] == 'subject') ? 'entry_date' : $columns[$order[0]['column']]['data'],
				'sort' => $order[0]['dir'],
				'allrows' => phpgw::get_var('length', 'int') == -1,
			);

			if (isset($this->location_code) && $this->location_code != '')
			{
				$params['location_code'] = $this->location_code;
				$values = $so->get_historic_check_lists( $control_id, null, $params['start'], $params['query'], $deviation = null, $params['allrows'], $this->location_code, $params['results']);

			}
			else
			{
				$values = array();
			}

			
			
			$condition_degree = 0;

			$soentity = createObject('property.soentity');
			
			$dateFormat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			foreach ($values as &$entry)
			{
				if($entry['location_id'])
				{
					$entry['loc1_name'] = $entry['loc1_name'] . '::' . $soentity->get_short_description(
						array
						(
							'location_id' => $entry['location_id'],
							'id' => $entry['component_id']
						)
					);
				}
				
				$entry['completed_date_text'] = $GLOBALS['phpgw']->common->show_date($entry['completed_date'], $dateFormat);

				if(isset($entry['findings_summary']['condition_degree'][1]))
				{
					$entry['findings_summary']['condition_degree_1'] = $entry['findings_summary']['condition_degree'][1];
					$condition_degree ++;
				}
				if(isset($entry['findings_summary']['condition_degree'][2]))
				{
					$entry['findings_summary']['condition_degree_2'] = $entry['findings_summary']['condition_degree'][2];
					$condition_degree ++;
				}
				if(isset($entry['findings_summary']['condition_degree'][3]))
				{
					$entry['findings_summary']['condition_degree_3'] = $entry['findings_summary']['condition_degree'][3];
					$condition_degree ++;
				}
			}

			
			$result_data = array('results' => $values);

			$result_data['total_records'] = $so->total_records;
			$result_data['draw'] = $draw;

			return $this->jquery_results($result_data);
		}
		
		public function view()
		{
			$check_list_id = phpgw::get_var('id', 'int');
			
			createObject('controller.uicheck_list')->get_report($check_list_id);
					
		}

	}