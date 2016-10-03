<?php
	/**
	 * phpGroupWare - logistic: a part of a Facilities Management System.
	 *
	 * @author Erik Holm-Larsen <erik.holm-larsen@bouvet.no>
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
	phpgw::import_class('phpgwapi.uicommon_jquery');
	phpgw::import_class('logistic.soproject');
	phpgw::import_class('phpgwapi.jquery');

	include_class('logistic', 'project');

	class logistic_uiproject extends phpgwapi_uicommon_jquery
	{

		private $so;
		public $public_functions = array(
			'query' => true,
			'index' => true,
			'project_types' => true,
			'view' => true,
			'view_project_type' => true,
			'edit_project_type' => true,
			'edit_project_type_name' => true,
			'add' => true,
			'edit' => true,
			'save' => true
		);

		public function __construct()
		{
			parent::__construct();

			$this->so = CreateObject('logistic.soproject');

			$this->acl_read = $GLOBALS['phpgw']->acl->check('.project', PHPGW_ACL_READ, 'logistic');//1
			$this->acl_add = $GLOBALS['phpgw']->acl->check('.project', PHPGW_ACL_ADD, 'logistic');//2
			$this->acl_edit = $GLOBALS['phpgw']->acl->check('.project', PHPGW_ACL_EDIT, 'logistic');//4
			$this->acl_delete = $GLOBALS['phpgw']->acl->check('.project', PHPGW_ACL_DELETE, 'logistic');//8
			$this->acl_manage = $GLOBALS['phpgw']->acl->check('.project', 16, 'logistic');//16

			$GLOBALS['phpgw']->css->add_external_file('logistic/templates/base/css/base.css');

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "logistic::project";
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

			//Retrieve a project identifier and load corresponding project
			$project_id = phpgw::get_var('project_id');

			$exp_param = phpgw::get_var('export');
			$export = false;
			if (isset($exp_param))
			{
				$export = true;
				$num_of_objects = null;
			}

			//Retrieve the type of query and perform type specific logic
			$query_type = phpgw::get_var('type');
			$filters = array();
			//var_dump($query_type);
			switch ($query_type)
			{
				case 'project_type':
					phpgwapi_cache::session_set('logistic', 'project_type_query', $search_for);
					$search_type = 'project_type';
					$result_objects = $this->so->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters, $params['allrows']);
					$object_count = $this->so->get_count($search_for, $search_type, $filters);
					break;
				default: // ... all composites, filters (active and vacant)
					phpgwapi_cache::session_set('logistic', 'project_query', $search_for);
					$filters = array('project_type' => phpgw::get_var('project_type'));
					$result_objects = $this->so->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters, $params['allrows']);
					$object_count = $this->so->get_count($search_for, $search_type, $filters);
					break;
			}
			//var_dump($result_objects);
			//Create an empty row set
			$rows = array();
			foreach ($result_objects as $result)
			{
				if (isset($result))
				{
					$project = $result->serialize();
					$href = self::link(array('menuaction' => 'logistic.uiactivity.edit', 'project_id' => $project['id']));
					$project['add_activity_link'] = "<a class=\"btn-sm delete\" href=\"{$href}\">Legg til aktivitet</a>";
					$href_view = self::link(array('menuaction' => 'logistic.uiactivity.index', 'project_id' => $project['id']));
					$project['view_activity_link'] = "<a class=\"btn-sm delete\" href=\"{$href_view}\">Vis aktiviteter</a>";
					$rows[] = $project;
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
				if ($search_type && $search_type == 'project_type')
				{
					array_walk(
						$result_data['results'], array($this, '_add_links'), "logistic.uiproject.view_project_type");
				}
				else
				{
					array_walk(
						$result_data['results'], array($this, '_add_links'), "logistic.uiproject.view");
				}
			}
			return $this->jquery_results($result_data);
		}

		public function index()
		{
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			$project_type_array = $this->so->get_project_types();

			$data = array(
				'datatable_name' => lang('Overview projects'),
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array('type' => 'filter',
								'name' => 'project_type',
								'text' => lang('Project_type') . ':',
								'list' => $project_type_array,
							)
						)
					)
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'logistic.uiproject.index', 'phpgw_return_as' => 'json')),
					'allrows' => true,
					'editor_action' => self::link(array('menuaction' => 'logistic.uiproject.edit_project_name')),
					'new_item' => self::link(array('menuaction' => 'logistic.uiproject.add')),
					'field' => array(
						array(
							'key' => 'id',
							'label' => lang('Id'),
							'sortable' => true,
							'formatter' => 'JqueryPortico.formatLink'
						),
						array(
							'key' => 'name',
							'label' => lang('Project name'),
							'sortable' => true
						),
						array(
							'key' => 'description',
							'label' => lang('Project description'),
							'sortable' => false,
							'editor' => false
						),
						array(
							'key' => 'project_type_label',
							'label' => lang('Project type'),
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
							'key' => 'add_activity_link',
							'label' => lang('Add activity'),
							'sortable' => false
						),
						array(
							'key' => 'view_activity_link',
							'label' => lang('view activities'),
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
						'name' => 'project_id',
						'source' => 'id'
					),
				)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'new_activity',
				'text' => lang('t_new_activity'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'logistic.uiactivity.edit'
				)),
				'parameters' => json_encode($parameters)
			);

			self::render_template_xsl('datatable_jquery', $data);
		}

		public function project_types()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "admin::logistic::project_types";
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			$project_type_id = phpgw::get_var('id');
			$new_type = phpgw::get_var('new_type');
			$edit_type = phpgw::get_var('edit_type');

			if ($new_type || $edit_type)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uiproject.edit_project_type'));
			}
			else
			{
				//list project types
				$data = array(
					'datatable_name' => lang('Overview project types'),
					'form' => array(
						'toolbar' => array(
							'item' => array(
							)
						)
					),
					'datatable' => array(
						'source' => self::link(array('menuaction' => 'logistic.uiproject.project_types',
							'phpgw_return_as' => 'json', 'type' => 'project_type')),
						'editor_action' => self::link(array('menuaction' => 'logistic.uiproject.edit_project_type_name')),
						'new_item' => self::link(array('menuaction' => 'logistic.uiproject.project_types',
										'new_type' => 'yes')),
						'field' => array(
							array(
								'key' => 'id',
								'label' => lang('Id'),
								'sortable' => true,
								'formatter' => 'JqueryPortico.formatLink'
							),
							array(
								'key' => 'name',
								'label' => lang('Project type name'),
								'sortable' => false,
								'editor' => true
							),
							array(
								'key' => 'link',
								'hidden' => true
							)
						)
					),
				);

				$parameters = array(
					'parameter' => array(
						array(
							'name' => 'id',
							'source' => 'id'
						)
					)
				);

				if ($this->acl_read)
				{
					$data['datatable']['actions'][] = array
						(
						'my_name' => 'view',
						'statustext' => lang('view'),
						'text' => lang('view'),
						'action' => $GLOBALS['phpgw']->link('/index.php', array(
							'menuaction' => 'logistic.uiproject.edit_project_type'
						)),
						'parameters' => json_encode($parameters)
					);
				}

				self::render_template_xsl(array('datatable_jquery'), $data);
			}
		}

		public function view()
		{
			$project_id = phpgw::get_var('id');
			if (isset($_POST['edit_project']))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uiproject.edit',
					'id' => $project_id));
			}
			else if (isset($_POST['new_activity']))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uiactivity.edit',
					'project_id' => $project_id));
			}
			else
			{
				if ($project_id && is_numeric($project_id))
				{
					$project = $this->so->get_single($project_id);
				}

				$data = array
					(
					'project' => $project,
					'view' => 'view_project'
				);

				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('logistic') . '::' . lang('Project');
				self::render_template_xsl(array('project/project_item'), $data);
			}
		}

		public function view_project_type()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "admin::logistic::project_types";
			$project_type_id = phpgw::get_var('id');
			if (isset($_POST['edit_project_type']))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uiproject.edit_project_type',
					'id' => $project_type_id));
			}
			else if (isset($_POST['cancel_project_type']))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uiproject.project_types'));
			}
			else
			{
				if ($project_type_id && is_numeric($project_type_id))
				{
					$objects = $this->so->get(0,0,'',false,'', 'project_type', array(
						'id' => $project_type_id));
					if (count($objects) > 0)
					{
						$keys = array_keys($objects);
						$project = $objects[$keys[0]];
					}
				}
				$data = array(
					'project' => $project
				);

				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('logistic') . '::' . lang('Project type');
				self::render_template_xsl(array('project/project_type_item'), $data);
			}
		}

		public function edit_project_type()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "admin::logistic::project_types";
			$project_type_id = phpgw::get_var('id');
			if ($project_type_id && is_numeric($project_type_id))
			{
				$objects = $this->so->get(0,0,'',false,'', 'project_type', array(
					'id' => $project_type_id));
				if (count($objects) > 0)
				{
					$keys = array_keys($objects);
					$project = $objects[$keys[0]];
				}
			}
			else
			{
				$project = new logistic_project();
			}
			if (isset($_POST['save_project_type']))
			{
				$project_type_name = phpgw::get_var('title');
				if (!$project_type_id || is_null($project_type_id))
				{
					$project_type_id = $this->so->add_project_type($project_type_name);
				}
				else
				{
					$this->so->update_project_type($project_type_id, $project_type_name);
				}
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uiproject.view_project_type',
					'id' => $project_type_id));
			}
			else if (isset($_POST['cancel_project_type']))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uiproject.project_types'));
			}
			else
			{
				$data = array(
					'project' => $project,
					'editable' => true
				);

				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('logistic') . '::' . lang('Project type');
				self::render_template_xsl(array('project/project_type_item'), $data);
			}
		}

		public function edit_project_type_name()
		{
			$project_type_id = phpgw::get_var('id');
			$field_name = phpgw::get_var('field_name', 'string');

			if (!$this->acl_edit)
			{
				return lang('no access');
			}

			if ($project_type_id && is_numeric($project_type_id))
			{
				$objects = $this->so->get(0,0,'',false,'', 'project_type', array(
					'id' => $project_type_id));
				if (count($objects) > 0)
				{
					$keys = array_keys($objects);
					$project = $objects[$keys[0]];
				}
			}
			else
			{
				return "Ugyldig operasjon";
			}

			$project_type_name = phpgw::get_var('value');
			$this->so->update_project_type($project_type_id, $project_type_name);

			return lang('Project type name updated');
		}

		public function add()
		{
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uiproject.edit'));
		}

		public function edit( $project = null )
		{
			$project_id = phpgw::get_var('id');
			$activities = array();
			if ($project_id && is_numeric($project_id))
			{
				$project = $this->so->get_single($project_id);
				$activities = createObject('logistic.soactivity')->get(0, 1, 'name', true, '', '', array('project' => $project_id), true);
			}
			else
			{
				if ($project == null)
				{
					$project = new logistic_project();
				}
				if($project->get_id())
				{
					$activities = createObject('logistic.soactivity')->get(0, 1, 'name', true, '', '', array('project' => $project->get_id()), true);
				}
			}

			$project_types = $this->so->get_project_types();
			foreach ($project_types as &$p_type)
			{
				if ($project->get_project_type_id() == $p_type['id'])
				{
					$p_type['selected'] = 1;
				}
			}

			$project_list = $this->so->get_projects();

			foreach ($project_list as $key => $entry)
			{
				if($project_id && $entry['id'] ==  $project_id)
				{
					unset($project_list[$key]);
				}
			}

			$data = array
				(
				'project' => $project,
				'options' => $project_types,
				'project_list'	=> array('options' => $project_list),
				'activities'	=> false,//!!$activities,
				'editable' => true
			);

			$this->rich_text_editor('description');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('logistic') . '::' . lang('Project type');
			$GLOBALS['phpgw']->jqcal->add_listener('start_date');
			$GLOBALS['phpgw']->jqcal->add_listener('end_date');

			phpgwapi_jquery::load_widget('core');

			self::add_javascript('logistic', 'logistic', 'project.js');
			self::render_template_xsl(array('project/project_item'), $data);
		}

		public function save()
		{
			$project_id = phpgw::get_var('id');

			if ($project_id && is_numeric($project_id))
			{
				$project = $this->so->get_single($project_id);
			}
			else
			{
				$project = new logistic_project();
			}

			$project->populate();

			if ($project->validate())
			{
				$project_id = $this->so->store($project);
				$copy_from_project_id = phpgw::get_var('copy_project_activities', 'int');
				if($copy_from_project_id)
				{
					$this->so->copy_project_activities($copy_from_project_id,$project_id, $project->get_start_date());
				}
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uiproject.view',
					'id' => $project_id));
			}
			else
			{
				$this->edit($project);
			}
		}
	}