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

	phpgw::import_class('phpgwapi.jquery');
	phpgw::import_class('phpgwapi.uicommon');
	phpgw::import_class('logistic.soproject');

	include_class('logistic', 'project');

	class logistic_uiresource_type_requirement extends phpgwapi_uicommon
	{

		private $so;
		private $so_project;
		public $public_functions = array(

			'query' => true,
			'index' => true,
			'edit' => true,
			'add' => true,
			'view' => true,
			'get_bim_level1' => true,
			'get_bim_level2' => true
		);

		public function __construct()
		{
			parent::__construct();

			$this->so = CreateObject('logistic.soresource_type_requirement');
			$this->so_project = CreateObject('logistic.soproject');

			$read    = $GLOBALS['phpgw']->acl->check('.project', PHPGW_ACL_READ, 'logistic');//1
			$add     = $GLOBALS['phpgw']->acl->check('.project', PHPGW_ACL_ADD, 'logistic');//2
			$edit    = $GLOBALS['phpgw']->acl->check('.project', PHPGW_ACL_EDIT, 'logistic');//4
			$delete  = $GLOBALS['phpgw']->acl->check('.project', PHPGW_ACL_DELETE, 'logistic');//8

			$manage  = $GLOBALS['phpgw']->acl->check('.project', 16, 'logistic');//16

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "admin::logistic::resource_type_requirement";
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
								$result_data['results'], array($this, '_add_links'), "logistic.uiresource_type_requirement.view");
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
				'datatable_name'	=> lang('resource_type_requirement'),
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array('type' => 'filter',
								'name' => 'entity_type',
								'text' => lang('Entity types') . ':',
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
								'href' => self::link(array('menuaction' => 'logistic.uiresource_type_requirement.add')),
								'class' => 'new_item'
							),
						),
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'logistic.uiresource_type_requirement.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'id',
							'label' => lang('ID'),
							'sortable' => true,
							'formatter' => 'YAHOO.portico.formatLink'
						),
						array(
							'key' => 'entity_label',
							'label' => lang('Entity'),
							'sortable' => true
						),
						array(
							'key' => 'category_label',
							'label' => lang('Category'),
							'sortable' => true
						),
						array(
							'key' => 'project_type_label',
							'label' => lang('Project_type'),
							'sortable' => true
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

		public function add()
		{
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uiresource_type_requirement.edit'));
		}

		public function edit()
		{
			$entity_so	= CreateObject('property.soadmin_entity');
			$custom	= createObject('phpgwapi.custom_fields');
			$req_type_id = phpgw::get_var('id');
			if($req_type_id && is_numeric($req_type_id))
			{
				$req_type = $this->so->get_single($req_type_id);
			}
			else
			{
				$req_type = new logistic_bim_item_type_requirement();
			}

			if (isset($_POST['save']))
			{
				$entity_id = phpgw::get_var('entity_id');
				$category_id = phpgw::get_var('category_id');
				$location_id = $GLOBALS['phpgw']->locations->get_id('property',".entity.{$entity_id}.{$category_id}");
				$req_type->set_location_id($location_id);
				$req_type->set_project_type_id(phpgw::get_var('project_type_id'));
				$cust_attr_ids = phpgw::get_var('attributes');
				foreach ($cust_attr_ids as $attr_id)
				{
					$req_type->set_cust_attribute_id($attr_id);
					$req_type_id = $this->so->store($req_type);
				}

				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uiresource_type_requirement.view', 'location_id' => $location_id));
			}
			else if (isset($_POST['cancel']))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uiresource_type_requirement.index'));
			}
			else
			{
				$entity_list = execMethod('property.soadmin_entity.read', array('allrows' => true));

				array_unshift($entity_list,array ('id'=>'','name'=> lang('select value')));
				if($req_type->get_entity_id())
				{
					foreach ($entity_list as &$e)
					{
						if($e['id'] == $req_type->get_entity_id())
						{
							$e['selected'] = 1;
						}
					}
					$category_list = $entity_so->read_category(array('allrows'=>true,'entity_id'=>$req_type->get_entity_id()));
					foreach ($category_list as &$c)
					{
						if($c['id'] == $req_type->get_category_id())
						{
							$c['selected'] = 1;
						}
					}

					$attributes = $custom->find('property',".entity.{$req_type->get_entity_id()}.{$req_type->get_category_id()}", 0, '','','',true, true);
					$selected_attributes = explode(',', $req_type->get_cust_attribute_id());
					foreach ($attributes as &$a)
					{
						if(in_array($a['id'], $selected_attributes))
						{
							$a['checked'] = 'checked';
						}
					}
				}
				$project_type_array = $this->so_project->get_project_types($req_type->get_project_type_id());

				$data = array
						(
						'value_id' => !empty($req_type) ? $req_type->get_id() : 0,
						'img_go_home' => 'rental/templates/base/images/32x32/actions/go-home.png',
						'entities' => $entity_list,
						'categories' => $category_list,
						'attributes' => $attributes,
						'project_types' => $project_type_array,
						'editable' => true,
						'req_type' => $req_type
					);

				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('logistic') . '::' . lang('Project type');

				phpgwapi_jquery::load_widget('core');

				self::add_javascript('logistic', 'logistic', 'resource_type_requirement.js');
				self::render_template_xsl(array('resource_type_requirement_item'), $data);
			}
		}

		public function get_bim_level1()
		{
			$entity_id		= phpgw::get_var('entity_id');
			$entity			= CreateObject('property.soadmin_entity');

			$category_list = $entity->read_category(array('allrows'=>true,'entity_id'=>$entity_id));

			return $category_list;
		}

		public function get_bim_level2()
		{
			$custom	= createObject('phpgwapi.custom_fields');
			$entity_id		= phpgw::get_var('entity_id');
			$cat_id		= phpgw::get_var('cat_id');

			$attrib_data = $custom->find('property',".entity.{$entity_id}.{$cat_id}", 0, '','','',true, true);

			return $attrib_data;
		}

		public function view()
		{
			$entity_so	= CreateObject('property.soadmin_entity');
			$custom	= createObject('phpgwapi.custom_fields');
			$req_type_id = phpgw::get_var('id');
			if(isset($_POST['edit']))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uiresource_type_requirement.edit', 'id' => $req_type_id));
			}

			if($req_type_id && is_numeric($req_type_id))
			{
				$req_type = $this->so->get_single($req_type_id);
				$entity = $entity_so->read_single($req_type->get_entity_id());
				$category = $entity_so->read_single_category($req_type->get_entity_id(),$req_type->get_category_id());
				$all_attributes = $custom->find('property',".entity.{$req_type->get_entity_id()}.{$req_type->get_category_id()}", 0, '','','',true, true);
				$attributes = array();
				$selected_attributes = explode(',', $req_type->get_cust_attribute_id());
				foreach ($all_attributes as $attr)
				{
					if(in_array($attr['id'], $selected_attributes))
					{
						$attributes[] = $attr;
					}
				}

				$objects = $this->so_project->get(null, null, null, null, null, 'project_type', array('id' => $req_type->get_project_type_id()));
				if (count($objects) > 0)
				{
					$keys = array_keys($objects);
					$project_type = $objects[$keys[0]];
				}

				$data = array
						(
						'value_id' => !empty($req_type) ? $req_type->get_id() : 0,
						'img_go_home' => 'rental/templates/base/images/32x32/actions/go-home.png',
						'req_type' => $req_type,
						'entity' => $entity,
						'category' => $category,
						'attributes' => $attributes,
						'project_type' => $project_type
					);

				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('logistic') . '::' . lang('Project type');
				self::render_template_xsl(array('resource_type_requirement_item'), $data);
			}
		}
	}
