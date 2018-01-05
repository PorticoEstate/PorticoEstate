<?php
	/**
	 * phpGroupWare - logistic: a part of a Facilities Management System.
	 *
	 * @author Erik Holm-Larsen <erik.holm-larsen@bouvet.no>
	 * @author Torstein Vadla <torstein.vadla@bouvet.no>
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2011,2012,2013,2014,2015 Free Software Foundation, Inc. http://www.fsf.org/
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
	phpgw::import_class('logistic.sorequirement');
	phpgw::import_class('logistic.sorequirement_resource_allocation');
	phpgw::import_class('phpgwapi.uicommon_jquery');
	phpgw::import_class('logistic.soactivity');
	phpgw::import_class('phpgwapi.jquery');

	include_class('logistic', 'actvity');

	class logistic_uiactivity extends phpgwapi_uicommon_jquery
	{

		private $so;
		private $so_project;
		private $so_requirement;
		private $so_resource_allocation;
		private $read;
		private $add;
		private $edit;
		private $delete;
		private $manage;
		public $public_functions = array(
			'query' => true,
			'add' => true,
			'edit' => true,
			'view' => true,
			'index' => true,
			'save' => true,
			'edit_favorite' => true,
			'view_resource_allocation' => true
		);

		public function __construct()
		{
			parent::__construct();

			$this->so = createObject('logistic.soactivity');
			$this->so_project = createObject('logistic.soproject');
			$this->so_requirement = CreateObject('logistic.sorequirement');
			$this->so_resource_allocation = CreateObject('logistic.sorequirement_resource_allocation');

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "logistic::project::activity";

			$this->acl_read = $GLOBALS['phpgw']->acl->check('.activity', PHPGW_ACL_READ, 'logistic');//1
			$this->acl_add = $GLOBALS['phpgw']->acl->check('.activity', PHPGW_ACL_ADD, 'logistic');//2
			$this->acl_edit = $GLOBALS['phpgw']->acl->check('.activity', PHPGW_ACL_EDIT, 'logistic');//4
			$this->acl_delete = $GLOBALS['phpgw']->acl->check('.activity', PHPGW_ACL_DELETE, 'logistic');//8
			$this->acl_manage = $GLOBALS['phpgw']->acl->check('.activity', 16, 'logistic');//16

			$GLOBALS['phpgw']->css->add_external_file('logistic/templates/base/css/base.css');
		}

		public function index()
		{
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			$project_array = $this->so_project->get_projects();
			$user_array = $this->get_user_array();

			$data = array(
				'datatable_name' => lang('Overview activities'),
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
							)
						)
					)
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'logistic.uiactivity.index', 'phpgw_return_as' => 'json',
						'filter' => phpgw::get_var('filter', 'int'))),
					'allrows' => true,
					'new_item' => self::link(array('menuaction' => 'logistic.uiactivity.add')),
					'field' => array(
						array(
							'key' => 'id',
							'label' => lang('Id'),
							'sortable' => true,
							'formatter' => 'JqueryPortico.formatLink'
						),
						array(
							'key' => 'name',
							'label' => lang('Activity name'),
							'sortable' => true
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
							'key' => 'responsible_user_name',
							'label' => lang('Responsible user'),
							'sortable' => false
						),
						array(
							'key' => 'status',
							'label' => lang('Status'),
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
						'name' => 'id',
						'source' => 'id'
					),
				)
			);

			$parameters2 = array
				(
				'parameter' => array
					(
					array
						(
						'name' => 'parent_id',
						'source' => 'id'
					),
				)
			);

			$parameters3 = array
				(
				'parameter' => array
					(
					array
						(
						'name' => 'activity_id',
						'source' => 'id'
					),
				)
			);


			$data['datatable']['actions'][] = array
				(
				'my_name' => 'new',
				'text' => lang('add sub activity'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'logistic.uiactivity.edit'
				)),
				'parameters' => json_encode($parameters2)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'new_requirement',
				'text' => lang('t_new_requirement'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'logistic.uirequirement.edit',
					'nonavbar' => true
				)),
				'parameters' => json_encode($parameters3)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'view_requirements',
				'text' => lang('t_view_requirements'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'logistic.uiactivity.view_resource_allocation'
				)),
				'parameters' => json_encode($parameters)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'add_favorite',
				'text' => lang('toggle as favorite'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'logistic.uiactivity.edit_favorite'
				)),
				'parameters' => json_encode($parameters)
			);


			self::render_template_xsl(array('datatable_jquery'), $data);
		}

		public function query()
		{
			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');

			if ($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > 0)
			{
				$user_rows_per_page = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$user_rows_per_page = 10;
			}

			$params = array(
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', $user_rows_per_page),
				'query' => !empty($search['value']) ? $search['value'] : '',
				'order' => !empty($columns[$order[0]['column']]['data']) ? $columns[$order[0]['column']]['data'] : '',
				'sort' => $order[0]['dir'],
				'allrows' => phpgw::get_var('length', 'int') == -1,
			);

			$start_index = $params['start'];
			$num_of_objects = $params['results'] < 0 ? null : $params['results'];
			$sort_field = $params['order'];
			$sort_ascending = $params['sort'] == 'desc' ? false : true;
			// Form variables
			$search_for = $params['query'];
			$search_type = phpgw::get_var('search_option', 'string', 'REQUEST', '');

			// Create an empty result set
			$result_objects = array();
			$result_count = 0;

			//Retrieve a contract identifier and load corresponding contract
			$exp_param = phpgw::get_var('export');
			$export = false;
			if (isset($exp_param))
			{
				$export = true;
				$num_of_objects = null;
			}

			//Retrieve the type of query and perform type specific logic
			$query_type = phpgw::get_var('type');

			switch ($query_type)
			{
				case 'children':
					$activity_id = phpgw::get_var('activity_id');
					$filters = array('id' => $activity_id);
					$result_objects = $this->so->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters, $params['allrows']);
					$object_count = $this->so->get_count('', '', array());
					array_shift($result_objects);
					if ($result_objects)
					{
						$object_count --;
					}
					break;
				case 'activity_id':
					$activity_id = phpgw::get_var('activity_id');
					$filters = array('id' => $activity_id);
					$result_objects = $this->so->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters, $params['allrows']);
					$object_count = $this->so->get_count($search_for, $search_type, $filters);
					break;
				default: // ... all activities, filters (active and vacant)
					phpgwapi_cache::session_set('logistic', 'activity_query', $search_for);
					$filters = array('project' => phpgw::get_var('project'), 'user' => phpgw::get_var('user'),
						'activity' => phpgw::get_var('filter', 'int'));
					$result_objects = $this->so->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters, $params['allrows']);
					$object_count = $this->so->total_records;
					break;
			}

			//Create an empty row set
			$rows = array();
			foreach ($result_objects as $activity)
			{
				if (isset($activity))
				{
					$filters = array('activity' => $activity->get_id());
					$requirements_for_activity = $this->so_requirement->get(0, 0, '', false, '', '', $filters);

					if (count($requirements_for_activity) > 0)
					{
						$total_num_alloc = 0;
						$total_num_required = 0;

						foreach ($requirements_for_activity as $requirement)
						{
							$filters = array('requirement_id' => $requirement->get_id());
							$num_allocated = $this->so_resource_allocation->get_count('', '', $filters);

							$num_required = $requirement->get_no_of_items();

							$total_num_alloc += $num_allocated;
							$total_num_required += $num_required;
						}

						if ($total_num_alloc == $total_num_required)
						{
							$status = "Behov dekket";
						}
						else
						{
							$status = "Udekket behov (" . ($total_num_required - $total_num_alloc) . ")";
						}
					}
					else
					{
						$status = "Ingen registerte behov";
					}

					$activity_arr = $activity->serialize();

					$activity_arr['status'] = $status;


					$href = self::link(array('menuaction' => 'logistic.uiactivity.view', 'id' => $activity_arr['id']));
					$activity_arr['id_link'] = "<a href=\"{$href}\">" . $activity_arr['id'] . "</a>";
					$activity_arr['name'] = "<a href=\"{$href}\">" . $activity_arr['name'] . "</a>";


					$rows[] = $activity_arr;
				}
			}

			// ... add result data
			$result_data = array('results' => $rows);

			$result_data['total_records'] = $object_count;
			$result_data['start'] = $params['start'];
			$result_data['sort'] = $params['sort'];
			$result_data['dir'] = $params['dir'];
			$result_data['draw'] = $draw;

			$editable = phpgw::get_var('editable') == 'true' ? true : false;

			if (!$export)
			{
				//Add action column to each row in result table
				array_walk($result_data['results'], array($this, '_add_links'), "logistic.uiactivity.view");
			}

			return $this->jquery_results($result_data);
		}

		public function add()
		{
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uiactivity.edit'));
		}

		public function edit( $activity = null )
		{
			$activity_id = phpgw::get_var('id');
			$parent_activity_id = phpgw::get_var('parent_id', 'int');
			$project_id = phpgw::get_var('project_id', 'int');

			if ($activity == null)
			{
				if ($activity_id && is_numeric($activity_id))
				{
					$activity = $this->so->get_single($activity_id);
					$project_id = $activity->get_project_id();
				}
				else
				{
					$activity = new logistic_activity();

					if ($project_id && is_numeric($project_id))
					{
						$project = $this->so_project->get_single($project_id);
						$activity->set_project_id($project_id);
					}
					else if ($parent_activity_id)
					{
						$activity->set_parent_id($parent_activity_id);
						$parent_activity = $this->so->get_single($parent_activity_id);
						$project_id = $parent_activity->get_project_id();
						$activity->set_project_id($project_id);

						$activity->set_start_date($parent_activity->get_start_date());
						$activity->set_end_date($parent_activity->get_end_date());
					}
				}
			}

			$accounts = $GLOBALS['phpgw']->acl->get_user_list_right(PHPGW_ACL_READ, 'run', 'logistic');

			$activities = $this->so->get(0, 0, 'name', true, '', '', array('project' => $project_id), true);

			if ($activity_id)
			{
				$exclude = array($activity_id);
				$children = $this->so->get_children($activity_id, 0, true);

				foreach ($children as $child)
				{
					$exclude[] = $child['id'];
				}

				$k = count($activities);
				for ($i = 0; $i < $k; $i++)
				{
					if (in_array($activities[$i]->get_id(), $exclude))
					{
						unset($activities[$i]);
					}
				}
			}

			$data = array
				(
				'responsible_users' => $accounts,
				'activities' => $project_id ? $activities : array(),
				'activity' => $activity,
				'editable' => true,
				'breadcrumb' => $this->_get_breadcrumb($activity_id, 'logistic.uiactivity.edit', 'id')
			);

			if ($project)
			{
				$data['project'] = $project;
			}

//			if($activity->get_parent_id() || $activity_id)
//			if(	$activity_id )
			if ($project_id)
			{
				$parent_activity = $this->so->get_single((int)$activity->get_parent_id());
				$data['parent_activity'] = $parent_activity;
			}
			else
			{
				$projects = $this->so_project->get(0,0,'',false,'','',array());
				$data['projects'] = $projects;
			}

			$this->rich_text_editor('description');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('logistic') . '::' . lang('Add activity');

			$GLOBALS['phpgw']->jqcal2->add_listener('start_date', 'datetime');
			$GLOBALS['phpgw']->jqcal2->add_listener('end_date', 'datetime');

			self::add_javascript('logistic', 'logistic', 'activity.js');
			phpgwapi_jquery::formvalidator_generate(array('location', 'date', 'security',
				'file'), 'activity_form');
			self::render_template_xsl('activity/add_activity_item', $data);
		}

		public function view()
		{
			$activity_id = phpgw::get_var('id', 'int');

			if ($activity_id && is_numeric($activity_id))
			{
				$activity = $this->so->get_single($activity_id);

				$responsible_user = $this->so->get_responsible_user($activity->get_responsible_user_id());

				$activity->set_responsible_user_name($responsible_user);
				$breadcrumb = $this->_get_breadcrumb($activity_id, 'logistic.uiactivity.view', 'id');
			}

			$datatable_def = array();

			$datatable_def[] = array
				(
				'container' => 'datatable-container_0',
				'requestUrl' => json_encode(self::link(array('menuaction' => 'logistic.uiactivity.index',
						'activity_id' => $activity_id, 'type' => 'children', 'phpgw_return_as' => 'json'))),
				'ColumnDefs' => array
					(
					array('key' => 'id', 'label' => lang('id'), 'sortable' => false),
					array('key' => 'name', 'label' => lang('name'), 'sortable' => false),
					array('key' => 'start_date', 'label' => lang('start date'), 'sortable' => false),
					array('key' => 'end_date', 'label' => lang('end date'), 'sortable' => false),
					array('key' => 'responsible_user_name', 'label' => lang('responsible'), 'sortable' => false),
					array('key' => 'status', 'label' => lang('status'), 'sortable' => false)
				),
				'data' => json_encode(array()),
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);
			$tabs = $this->make_tab_menu($activity_id);

			$data = array
				(
				'datatable_def' => $datatable_def,
				'tabs' => $GLOBALS['phpgw']->common->create_tabs($tabs, 'details'),
				'view' => 'activity_details',
				'activity' => $activity,
				'breadcrumb' => $breadcrumb
			);

			if ($activity->get_parent_id() > 0)
			{
				$parent_activity = $this->so->get_single($activity->get_parent_id());
				$data['parent_activity'] = $parent_activity;
			}

			self::render_template_xsl(array('activity/view_activity_item', 'activity/activity_tabs',
				'datatable_inline'), $data);
		}

		public function save()
		{
			$activity_id = phpgw::get_var('id');

			if ($activity_id && is_numeric($activity_id))
			{
				$activity = $this->so->get_single($activity_id);
			}
			else
			{
				$activity = new logistic_activity();
			}

			$activity->populate();

//_debug_array($activity);die();
			if ($activity->validate())
			{
				$activity_id = $this->so->store($activity);
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uiactivity.view',
					'id' => $activity_id, 'project_id' => $activity->get_project_id()));
			}
			else
			{
				$this->edit($activity);
			}
		}

		public function edit_favorite()
		{
			if ($activity_id = phpgw::get_var('id'))
			{
				$activity = $this->so->get_single($activity_id);

				if (isset($GLOBALS['phpgw_info']['user']['preferences']['logistic']['menu_favorites']) && $GLOBALS['phpgw_info']['user']['preferences']['logistic']['menu_favorites'])
				{
					$menu_favorites = $GLOBALS['phpgw_info']['user']['preferences']['logistic']['menu_favorites'];
				}
				else
				{
					$menu_favorites = array();
				}

				if (isset($menu_favorites['activity'][$activity_id]))
				{
					unset($menu_favorites['activity'][$activity_id]);
				}
				else
				{
					$menu_favorites['activity'][$activity_id] = $activity->get_name();
				}

				$GLOBALS['phpgw']->preferences->account_id = $GLOBALS['phpgw_info']['user']['account_id'];
				$GLOBALS['phpgw']->preferences->read();
				$GLOBALS['phpgw']->preferences->add('logistic', 'menu_favorites', $menu_favorites, 'user');
				$GLOBALS['phpgw']->preferences->save_repository();
				execMethod('phpgwapi.menu.clear');
			}
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uiactivity.index'));
		}

		public function view_resource_allocation()
		{
			$activity_id = phpgw::get_var('activity_id');
			$activity = $this->so->get_single($activity_id);


			$ColumnDefs0 = array
				(
				array('key' => 'id', 'label' => lang('id'), 'sortable' => true),
				array('key' => 'start_date', 'label' => lang('start date'), 'sortable' => false),
				array('key' => 'end_date', 'label' => lang('end date'), 'sortable' => false),
				array('key' => 'no_of_items', 'label' => lang('Num required'), 'sortable' => false),
				array('key' => 'allocated', 'label' => lang('Num allocated'), 'sortable' => false),
				array('key' => 'location_label', 'label' => lang('Resource type'), 'sortable' => false),
				array('key' => 'criterias', 'label' => lang('criterias'), 'sortable' => false),
				array('key' => 'status', 'label' => lang('Status requirement'), 'sortable' => false)
			);

			if ($this->acl_add)
			{
				$ColumnDefs0[] = array
					(
					'key' => 'alloc_link',
					'label' => lang('Allocate resources'),
					'sortable' => false,
				);
			}
			if ($this->acl_add)
			{
				$ColumnDefs0[] = array
					(
					'key' => 'edit_requirement_link',
					'label' => lang('Edit requirement'),
					'sortable' => false,
				);
			}

			if ($this->acl_delete)
			{
				$ColumnDefs0[] = array
					(
					'key' => 'delete_requirement_link',
					'label' => lang('Delete requirement'),
					'sortable' => false,
				);
			}

			$datatable_def = array();

			$datatable_def[] = array
				(
				'container' => 'requirement-container_0',
				'requestUrl' => json_encode(self::link(array('menuaction' => 'logistic.uirequirement.index',
						'activity_id' => $activity_id, 'phpgw_return_as' => 'json'))),
				'ColumnDefs' => $ColumnDefs0,
				'data' => json_encode(array()),
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$ColumnDefs1 = array
				(
				array('key' => 'id', 'label' => lang('id'), 'sortable' => true),
				array('key' => 'fm_bim_item_name', 'label' => 'Navn på ressurs', 'sortable' => true),
				array('key' => 'resource_type_descr', 'label' => lang('resource type descr'),
					'sortable' => true),
				array('key' => 'allocated_amount', 'label' => lang('allocated amount'), 'sortable' => false),
				array('key' => 'location_code', 'label' => lang('location code'), 'sortable' => true),
				array('key' => 'fm_bim_item_address', 'label' => lang('address'), 'sortable' => true),
				array('key' => 'delete_link', 'label' => lang('delete'), 'sortable' => false),
				array('key' => 'assign_job', 'label' => lang('assign job'), 'sortable' => false),
				array('key' => 'related', 'label' => lang('related'), 'sortable' => false),
				array('key' => 'status', 'label' => lang('Status requirement'), 'sortable' => false)
			);

			$datatable_def[] = array
				(
				'container' => 'allocation-container_0',
				'requestUrl' => "''", //json_encode(self::link(array('menuaction' => 'logistic.uirequirement_resource_allocation.index', 'requirement_id' => $requirement_id, 'type' => "requirement_id", 'phpgw_return_as' => 'json'))),
				'ColumnDefs' => $ColumnDefs1,
				'data' => json_encode(array()),
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$tabs = $this->make_tab_menu($activity_id);

			$data = array
				(
				'datatable_def' => $datatable_def,
				'tabs' => $GLOBALS['phpgw']->common->create_tabs($tabs, 'allocation'),
				'view' => 'requirement_overview',
				'activity' => $activity,
				'breadcrumb' => $this->_get_breadcrumb($activity_id, 'logistic.uiactivity.view_resource_allocation', 'activity_id'),
				'acl_add' => $this->acl_add
			);

			self::add_javascript('logistic', 'logistic', 'resource_allocation.js');
			self::add_javascript('logistic', 'logistic', 'requirement_overview.js');

			self::add_javascript('logistic', 'logistic', 'requirement.js');
			self::add_javascript('phpgwapi', 'tinybox2', 'packed.js');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/tinybox2/style.css');

			self::render_template_xsl(array('activity/view_activity_item', 'requirement/requirement_overview',
				'activity/activity_tabs', 'datatable_inline'), $data);
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

		function make_tab_menu( $activity_id )
		{
			$tabs = array();

			if ($activity_id > 0)
			{

				$activity = $this->so->get_single($activity_id);

				$tabs = array
					(
					'details' => array
						(
						'label' => "1: " . lang('Activity details'),
						'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'logistic.uiactivity.view',
							'id' => $activity->get_id()))
					),
					'allocation' => array
						(
						'label' => "2: " . lang('Requirement allocation'),
						'link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'logistic.uiactivity.view_resource_allocation',
							'activity_id' => $activity->get_id()))
					)
				);
			}
			else
			{
				$tabs = array
					(
					'details' => array
						(
						'label' => "1: " . lang('Activity details'),
						'link' => '#details',
						'disable' => 0
					),
					'allocation' => array
						(
						'label' => "2: " . lang('Requirement allocation'),
						'link' => '#allocation',
						'disable' => 1
					)
				);
			}

			return $tabs;
		}

		private function _get_breadcrumb( $activity_id, $menuaction, $id_name = 'id' )
		{
			if (!$activity_id)
			{
				return;
			}

			$path = $this->so->get_path($activity_id);

			foreach ($path as $menu_item)
			{
				if ($menu_item['id'] == $activity_id)
				{
					$breadcrumb_array[] = array("name" => $menu_item['name'], "link" => "", "current" => 1);
				}
				else
				{
					$_link = self::link(array('menuaction' => $menuaction, $id_name => $menu_item['id']));
					$breadcrumb_array[] = array("name" => $menu_item['name'], "link" => $_link,
						"current" => 0);
				}
			}

			return $breadcrumb_array;
		}
	}