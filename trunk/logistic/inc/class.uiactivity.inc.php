<?php

	/**
	 * phpGroupWare - logistic: a part of a Facilities Management System.
	 *
	 * @author Erik Holm-Larsen <erik.holm-larsen@bouvet.no>
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
	 * @subpackage logistic
	 * @version $Id$
	 */
	phpgw::import_class('phpgwapi.uicommon');
	phpgw::import_class('logistic.soactivity');

	include_class('logistic', 'actvity');

	class logistic_uiactivity extends phpgwapi_uicommon
	{

		private $so;
		private $so_project;
		public $public_functions = array(
			'query' => true,
			'add' 	=> true,
			'edit' => true,
			'view' => true,
			'index' => true
		);

		public function __construct()
		{
			parent::__construct();

			$this->so = createObject('logistic.soactivity');
			$this->so_project = createObject('logistic.soproject');
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "logistic::project::activity";
		}

		public function index()
		{
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}
			self::add_javascript('phpgwapi', 'yahoo', 'datatable.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');

			$project_array = $this->so_project->get_projects();
			$user_array = $this->get_user_array();

			$data = array(
				'datatable_name'	=> lang('activity'),
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array('type' => 'filter',
								'name' => 'project',
								'text' => lang('Project') . ':',
								'list' => $project_array,
							),
							array('type' => 'filter',
								'name' => 'user',
								'text' => lang('Responsible user') . ':',
								'list' => $user_array,
							),
							array('type' => 'text',
								'text' => lang('search'),
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
					'source' => self::link(array('menuaction' => 'logistic.uiactivity.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'name',
							'label' => lang('Activity name'),
							'sortable' => true
						),
						array(
							'key' => 'id',
							'label' => lang('ID'),
							'sortable' => true,
							'formatter' => 'YAHOO.portico.formatLink'
						),
						array(
							'key' => 'project_name',
							'label' => lang('Project'),
							'sortable' => false
						),
						array(
							'key' => 'start_date',
							'label' => lang('Start date'),
							'sortable' => false
						),
						array(
							'key' => 'end_date',
							'label' => lang('End date'),
							'sortable' => false
						),
						array(
							'key' => 'responsible_user_id',
							'label' => lang('Responsible user'),
							'sortable' => false
						),
						array(
							'key' => 'link',
							'hidden' => true
						)
					)
				),
			);


			$parameters = array
				(
					'parameter' => array
					(
						array
						(
							'name'		=> 'parent_id',
							'source'	=> 'id'
						),
						array
						(
							'name'		=> 'activity_id',
							'source'	=> 'id'
						),
					)
				);

			$data['datatable']['actions'][] = array
					(
						'my_name'		=> 'new',
						'text' 			=> lang('add sub activity'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'logistic.uiactivity.edit'
						)),
						'parameters'	=> json_encode($parameters)
					);

			$data['datatable']['actions'][] = array
					(
						'my_name'		=> 'new_requirement',
						'text' 			=> lang('t_new_requirement'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'logistic.uirequirement.edit'
						)),
						'parameters'	=> json_encode($parameters)
					);

			$data['datatable']['actions'][] = array
					(
						'my_name'		=> 'view_requirements',
						'text' 			=> lang('t_view_requirements'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'logistic.uirequirement.index'
						)),
						'parameters'	=> json_encode($parameters)
					);

			$data['datatable']['actions'][] = array
					(
						'my_name'		=> 'new_booking',
						'text' 			=> lang('t_new_booking'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'logistic.uibooking.edit'
						)),
						'parameters'	=> json_encode($parameters)
					);

			$data['datatable']['actions'][] = array
					(
						'my_name'		=> 'view_bookings',
						'text' 			=> lang('t_view_bookings'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'logistic.uibooking.index'
						)),
						'parameters'	=> json_encode($parameters)
					);



			self::render_template_xsl(array('datatable_common'), $data);
		}

		public function query()
		{
			$params = array(
				'start' => phpgw::get_var('startIndex', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('results', 'int', 'REQUEST', null),
				'query' => phpgw::get_var('query'),
				'sort' => phpgw::get_var('sort'),
				'dir' => phpgw::get_var('dir'),
				'filters' => $filters
			);

			if ($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > 0)
			{
				$user_rows_per_page = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$user_rows_per_page = 10;
			}
			// YUI variables for paging and sorting
			$start_index = phpgw::get_var('startIndex', 'int');
			$num_of_objects = phpgw::get_var('results', 'int', 'GET', $user_rows_per_page);
			$sort_field = phpgw::get_var('sort');
			$sort_ascending = phpgw::get_var('dir') == 'desc' ? false : true;
			// Form variables
			$search_for = phpgw::get_var('query');
			$search_type = phpgw::get_var('search_option');
			// Create an empty result set
			$result_objects = array();
			$result_count = 0;

			//Retrieve a contract identifier and load corresponding contract
			//$activity_id = phpgw::get_var('activity_id');

			$exp_param = phpgw::get_var('export');
			$export = false;
			if (isset($exp_param))
			{
				$export = true;
				$num_of_objects = null;
			}

			//Retrieve the type of query and perform type specific logic
			$query_type = phpgw::get_var('type');
			//var_dump($query_type);
			switch ($query_type)
			{
				default: // ... all activities, filters (active and vacant)
					phpgwapi_cache::session_set('logistic', 'activity_query', $search_for);
					$filters = array('project' => phpgw::get_var('project'), 'user' => phpgw::get_var('user'));
					$result_objects = $this->so->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
					$object_count = $this->so->get_count($search_for, $search_type, $filters);
					break;
			}

			//Create an empty row set
			$rows = array();
			foreach ($result_objects as $result)
			{
				if (isset($result))
				{
					$rows[] = $result->serialize();
				}
			}
			
			// ... add result data
			$result_data = array('results' => $rows);

			$result_data['total_records'] = $object_count;
			$result_data['start'] = $params['start'];
			$result_data['sort'] = $params['sort'];
			$result_data['dir'] = $params['dir'];

			$editable = phpgw::get_var('editable') == 'true' ? true : false;

			if (!$export)
			{
				//Add action column to each row in result table
				array_walk(
								$result_data['results'],
								array($this, '_add_links'),
								"logistic.uiactivity.view"
				);
			}

			return $this->yui_results($result_data);
		}

		public function add()
		{
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uiactivity.edit'));
		}

		public function edit()
		{
			$activity_id = phpgw::get_var('id');
			$parent_activity_id = phpgw::get_var('parent_id');
			
			if ($activity_id && is_numeric($activity_id))
			{
				$activity = $this->so->get_single($activity_id);
			}
			else
			{
				$activity = new logistic_activity();
			}

			if(phpgw::get_var('project_id') && phpgw::get_var('project_id') > 0)
			{
				$activity->set_project_id(phpgw::get_var('project_id'));
			}
			
			if($parent_activity_id > 0)
			{
				$activity->set_parent_id( $parent_activity_id );
				$parent_activity = $this->so->get_single( $parent_activity_id );
				$activity->set_project_id( $parent_activity->get_project_id() );
			}

			if (isset($_POST['save_activity']))
			{
				$user_id = $GLOBALS['phpgw_info']['user']['id'];
				$activity->set_id( phpgw::get_var('id') );
				$activity->set_name( phpgw::get_var('name') );
				$activity->set_update_user( $user_id );
				$activity->set_responsible_user_id( phpgw::get_var('responsible_user_id') );

				if(phpgw::get_var('start_date','string') != '')
				{
					$start_date_ts = phpgwapi_datetime::date_to_timestamp( phpgw::get_var('start_date','string') );
					$activity->set_start_date($start_date_ts);
				}
				else
				{
					$activity->set_start_date(0);
				}

				if( phpgw::get_var('end_date','string') != '')
				{
					$end_date_ts = phpgwapi_datetime::date_to_timestamp( phpgw::get_var('end_date','string') );
					$activity->set_end_date($end_date_ts);
				}
				else
				{
					$activity->set_end_date(0);
				}

				$activity_id = $this->so->store($activity);

				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uiactivity.view', 'id' => $activity_id, 'project_id' => $activity->get_project_id()));
			}
			else if (isset($_POST['cancel_activity']))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uiactivity.view', 'id' => $activity_id));
			}
			else
			{
				$accounts = $GLOBALS['phpgw']->acl->get_user_list_right(PHPGW_ACL_READ, 'run', 'logistic');
				
			  $activities = $this->so->get();
				$activities_array = $this->convert_to_array( $activities );
				
				$data = array
				(
					'responsible_users' => $accounts,
					'activities' => $activities_array,
					'activity' => $activity->toArray(),
					'editable' => true,
				);
				
				if($parent_activity_id > 0)
				{
					$data['parent_activity'] = $parent_activity->toArray();
				}

				$this->use_yui_editor('description');
				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('logistic') . '::' . lang('Add activity');

				$GLOBALS['phpgw']->jqcal->add_listener('start_date');
				$GLOBALS['phpgw']->jqcal->add_listener('end_date');

				self::add_javascript('logistic', 'logistic', 'ajax.js');
				self::render_template_xsl(array('activity_item'), $data);
			}
		}
		
		function convert_to_array($object_list)
		{
			$converted_array = array();
			
			foreach($object_list as $object)
			{
				$converted_array[] = $object->toArray();
			}
			
			return $converted_array; 
		}

		public function view()
		{
			$activity_id = phpgw::get_var('id');
			$project_id = phpgw::get_var('project_id');
			if (isset($_POST['edit_activity']))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uiactivity.edit', 'id' => $activity_id, 'project_id' => $project_id));
			}
			else if (isset($_POST['new_activity']))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uiactivity.edit', 'project_id' => $project_id));
			}
			else
			{
				if ($activity_id && is_numeric($activity_id))
				{
					$activity = $this->so->get_single($activity_id);
				}

				$activity_array = $activity->toArray();

				if ($this->flash_msgs)
				{
					$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($this->flash_msgs);
					$msgbox_data = $GLOBALS['phpgw']->common->msgbox($msgbox_data);
				}

				$activity->set_project_id($project_id);

				$data = array
					(
						'activity' => $activity->toArray(),
						'img_go_home' => 'rental/templates/base/images/32x32/actions/go-home.png',
						'dateformat' 				=> $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']
				);

				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('logistic') . '::' . lang('Project');
				self::render_template_xsl(array('activity_item'), $data);
			}
		}

		private function get_user_array()
		{
			$user_array = array();
			$user_array[] = array(
				'id' => '',
				'name' => lang('all_types')
			);
			$user_array[] = array(
				'id' => $GLOBALS['phpgw_info']['user']['account_id'],
				'name' => lang('my_activities'),
				'selected' => 1
			);

			return $user_array;
		}
	}
