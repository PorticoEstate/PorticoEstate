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
	phpgw::import_class('logistic.soproject');

	include_class('logistic', 'project');

	class logistic_uibim_type_requirement extends phpgwapi_uicommon
	{

		private $so;
		private $so_project;
		public $public_functions = array(

			'query' => true,
			'index' => true,
			'edit' => true,
			'add' => true
		);

		public function __construct()
		{
			parent::__construct();

			$this->so = CreateObject('logistic.sobim_type_requirement');
			$this->so_project = CreateObject('logistic.soproject');

			$read    = $GLOBALS['phpgw']->acl->check('.project', PHPGW_ACL_READ, 'logistic');//1
			$add     = $GLOBALS['phpgw']->acl->check('.project', PHPGW_ACL_ADD, 'logistic');//2
			$edit    = $GLOBALS['phpgw']->acl->check('.project', PHPGW_ACL_EDIT, 'logistic');//4
			$delete  = $GLOBALS['phpgw']->acl->check('.project', PHPGW_ACL_DELETE, 'logistic');//8

			$manage  = $GLOBALS['phpgw']->acl->check('.project', 16, 'logistic');//16

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "admin::logistic::bim_type_requirement";
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
				default: // ... all composites, filters (active and vacant)
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
				array_walk(
								$result_data['results'], array($this, '_add_links'), "logistic.uibim_type_requirement.view");
			}
			return $this->yui_results($result_data);
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

			$entity_list = execMethod('property.soadmin_entity.read', array('allrows' => true));

			$data = array(
				'datatable_name'	=> lang('bim_type_requirement'),
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array('type' => 'filter',
								'name' => 'bim_type',
								'text' => lang('Bim types') . ':',
								'list' => $entity_list,
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
								'value' => lang('t_new_type_requirement'),
								'href' => self::link(array('menuaction' => 'logistic.uibim_type_requirement.add')),
								'class' => 'new_item'
							),
						),
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'logistic.uibim_type_requirement.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'location_id',
							'label' => lang('Location'),
							'sortable' => true
						),
						array(
							'key' => 'id',
							'label' => lang('ID'),
							'sortable' => true,
							'formatter' => 'YAHOO.portico.formatLink'
						),
						array(
							'key' => 'link',
							'hidden' => true
						)
					)
				),
			);

			self::render_template_xsl('datatable_common', $data);
		}

		public function edit()
		{

		}

		public function add()
		{
			$custom	= createObject('phpgwapi.custom_fields');
			$entity_list = execMethod('property.soadmin_entity.read', array('allrows' => true));

			//_debug_array($entity_list);

			foreach($entity_list as &$entry)
			{
				$cat_list = execMethod('property.soadmin_entity.read_category',(array('allrows'=>true,'entity_id'=>$entry['id'])));
				//_debug_array($cat_list);

				foreach($cat_list as &$cat)
				{
					$attrib_data = $custom->find('property',".entity.{$cat['entity_id']}.{$cat[id]}", 0, '','','',true, true);
					$cat['attrib'] = $attrib_data;
				//_debug_array($attrib_data);
				}

				$entry['cat_list'] = $cat_list;

			}

			//var_dump($entity_list);
			array_unshift($entity_list,array ('id'=>'','name'=> lang('select value')));
			$project_type_array = $this->so_project->get_project_types();

			$data = array
					(
					'img_go_home' => 'rental/templates/base/images/32x32/actions/go-home.png',
					'entities' => $entity_list,
					'project_types' => $project_type_array,
					'editable' => true
				);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('logistic') . '::' . lang('Project type');
			self::add_javascript('logistic', 'logistic', 'ajax.js');
			self::render_template_xsl(array('bim_type_requirement_item'), $data);
		}
	}