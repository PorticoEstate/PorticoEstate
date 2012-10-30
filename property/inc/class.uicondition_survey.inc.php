<?php

	/**
	 * phpGroupWare - logistic: a part of a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2012 Free Software Foundation, Inc. http://www.fsf.org/
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
	phpgw::import_class('phpgwapi.jquery');

	class property_uicondition_survey extends phpgwapi_uicommon
	{

		private $bo;
		public $public_functions = array(
			'query' => true,
			'index' => true,
			'view' => true,
			'add' => true,
			'edit' => true,
			'save' => true
		);

		public function __construct()
		{
			parent::__construct();

			$this->bo 					= CreateObject('property.bocondition_survey');
			$this->bocommon				= & $this->bo->bocommon;
			$this->acl 					= & $GLOBALS['phpgw']->acl;
			$this->acl_location			= $this->bo->acl_location;
			$this->acl_read 			= $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');
			$this->acl_add 				= $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'property');
			$this->acl_edit 			= $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'property');
			$this->acl_delete 			= $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'property');
			$this->acl_manage 			= $this->acl->check($this->acl_location, 16, 'property');

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "property::condition_survey";
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

			$survey_type_array = array();

			$data = array(
				'datatable_name'	=> lang('condition survey'),
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array('type' => 'filter',
								'name' => 'project_type',
								'text' => lang('Project_type') . ':',
								'list' => $survey_type_array,
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
							array(
								'type' => 'link',
								'value' => lang('t_new_project'),
								'href' => self::link(array('menuaction' => 'property.uicondition_survey.add')),
								'class' => 'new_item'
							),
						),
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'property.uicondition_survey.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'name',
							'label' => lang('Project name'),
							'sortable' => true
						),
						array(
							'key' => 'id',
							'label' => lang('ID'),
							'sortable' => true,
							'formatter' => 'YAHOO.portico.formatLink'
						),
						array(
							'key' => 'description',
							'label' => lang('Project description'),
							'sortable' => false,
							'editor' => 'new YAHOO.widget.TextboxCellEditor({disableBtns:true})'
						),
						array(
							'key' => 'project_type_label',
							'label' => lang('Project type'),
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
							'name'		=> 'project_id',
							'source'	=> 'id'
						),
					)
				);

			$data['datatable']['actions'][] = array
					(
						'my_name'		=> 'new_activity',
						'text' 			=> lang('t_new_activity'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'logistic.uiactivity.edit'
						)),
						'parameters'	=> json_encode($parameters)
					);

			self::render_template_xsl('datatable_common', $data);
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
			//var_dump($query_type);
			switch ($query_type)
			{
				case 'project_type':
					phpgwapi_cache::session_set('logistic', 'project_type_query', $search_for);
					$search_type = 'project_type';
					$result_objects = $this->so->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
					$object_count = $this->so->get_count($search_for, $search_type, $filters);
					break;
				default: // ... all composites, filters (active and vacant)
					phpgwapi_cache::session_set('logistic', 'project_query', $search_for);
					$filters = array('project_type' => phpgw::get_var('project_type'));
					$result_objects = $this->so->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
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
				if ($search_type && $search_type == 'project_type')
				{
					array_walk(
									$result_data['results'], array($this, '_add_links'), "property.uicondition_survey.view_project_type");
				}
				else
				{
					array_walk(
									$result_data['results'], array($this, '_add_links'), "property.uicondition_survey.view");
				}
			}
			return $this->yui_results($result_data);
		}


		public function view()
		{
			$project_id = phpgw::get_var('id');
			if (isset($_POST['edit_project']))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uicondition_survey.edit', 'id' => $project_id));
			}
			else if (isset($_POST['new_activity']))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uiactivity.edit', 'project_id' => $project_id));
			}
			else
			{
				if ($project_id && is_numeric($project_id))
				{
					$project = $this->so->get_single($project_id);
				}

				$project_array = $project->toArray();

				$data = array
					(
					'value_id' => !empty($project) ? $project->get_id() : 0,
					'project' => $project,
					'view' => 'view_project'
				);

				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('logistic') . '::' . lang('Project');
				self::render_template_xsl(array('project/project_item'), $data);
			}
		}

		public function add()
		{
			$this->edit();
		}

		public function edit($mode = 'edit')
		{
			$id 	= phpgw::get_var('id', 'int');

			if(!$this->acl_add && !$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uicondition_survey.view', 'id'=> $id));
			}

			if($mode == 'view')
			{
				if( !$this->acl_read)
				{
					$this->bocommon->no_access();
					return;
				}
			}
			else
			{
				if(!$this->acl_add && !$this->acl_edit)
				{
					$this->bocommon->no_access();
					return;
				}
			}



			$project_id = phpgw::get_var('id');
			if ($project_id && is_numeric($project_id))
			{
				$project = $this->so->get_single($project_id);
			}
			else
			{
				if($project == null)
				{
					$project = new logistic_project();	
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
			
			$data = array
			(
				'project' => $project,
				'options' => $project_types,
				'editable' => true
			);

			$this->use_yui_editor('description');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('logistic') . '::' . lang('Project type');
			
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
			
			if( $project->validate() )
			{
				$project_id = $this->so->store($project);
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uicondition_survey.view', 'id' => $project_id));	
			}
			else
			{
				$this->edit( $project );
			}
		}
	}

