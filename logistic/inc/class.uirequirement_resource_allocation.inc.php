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
	 * @version $Id: class.uiactivity.inc.php 10101 2012-10-03 09:46:51Z vator $
	 */
	phpgw::import_class('phpgwapi.uicommon');
	phpgw::import_class('logistic.soactivity');
	include_class('logistic', 'activity', '/inc/model/');
	include_class('logistic', 'requirement', '/inc/model/');
	include_class('logistic', 'requirement_value', '/inc/model/');
	include_class('logistic', 'requirement_resource_allocation', '/inc/model/');


	class logistic_uirequirement_resource_allocation extends phpgwapi_uicommon
	{

		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;
		var $type_id;
		var $location_code;

		private $bo;
		private $bocommon;
		private $so_activity;
		private $so_requirement;
		private $so_requirement_value;
		private $so;

		public $public_functions = array(
			'query' => true,
			'add' 	=> true,
			'edit' => true,
			'view' => true,
			'index' => true,
			'save' => true
		);

		public function __construct()
		{
			parent::__construct();

			$this->so 									= createObject('logistic.sorequirement_resource_allocation');
			$this->so_activity 					= createObject('logistic.soactivity');
			$this->so_requirement 			= createObject('logistic.sorequirement');
			$this->so_requirement_value = CreateObject('logistic.sorequirement_value');
			
		  $this->bo										= CreateObject('property.bolocation',true);
			$this->bocommon							= & $this->bo->bocommon;

			$this->type_id							= $this->bo->type_id;

			$this->start								= $this->bo->start;
			$this->query								= $this->bo->query;
			$this->sort									= $this->bo->sort;
			$this->order								= $this->bo->order;
			$this->filter								= $this->bo->filter;
			$this->cat_id								= $this->bo->cat_id;
			$this->part_of_town_id			= $this->bo->part_of_town_id;
			$this->district_id					= $this->bo->district_id;
			$this->status								= $this->bo->status;
			$this->allrows							= $this->bo->allrows;
			$this->lookup								= $this->bo->lookup;
			$this->location_code				= $this->bo->location_code;
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

			$user_array = $this->get_user_array();

			$data = array(
				'datatable_name'	=>  lang('Activity'). ' :: ' . lang('allocation'),
				'form' => array(
					'toolbar' => array(
						'item' => array(
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
					'source' => self::link(array('menuaction' => 'logistic.uirequirement_resource_allocation.index', 'phpgw_return_as' => 'json')),
					'field' => array(
					array(
							'key' => 'id',
							'label' => lang('ID'),
							'sortable' => true,
						),
						array(
							'key' => 'requirement_id',
							'label' => lang('Requirememnt id'),
							'sortable' => true
						),
						array(
							'key' => 'location_id',
							'label' => lang('Location id'),
							'sortable' => true
						),
						array(
							'key' => 'resource_id',
							'label' => lang('Resource id'),
							'sortable' => true
						),
					)
				),
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
				case 'requirement_id':
					$requirement_id = phpgw::get_var('requirement_id');
					$filters = array('requirement_id' => $requirement_id);
					$result_objects = $this->so->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
					$object_count = $this->so->get_count($search_for, $search_type, $filters);
					break;
				default: // ... all composites, filters (active and vacant)
					$result_objects = $this->so->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
					$object_count = $this->so->get_count($search_for, $search_type, $filters);
					break;
			}

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
								$result_data['results'], array($this, '_add_links'), "logistic.uirequirement_resource_allocation.view");
			}
			return $this->yui_results($result_data);
		}

		public function add()
		{
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uirequirement_resource_allocation.edit'));
		}

		public function edit()
		{
			$activity_id = phpgw::get_var('activity_id');
			$requirement_id = phpgw::get_var('requirement_id');
			$allocation_id = phpgw::get_var('id');

			if ($activity_id && is_numeric($activity_id))
			{
				$activity = $this->so_activity->get_single($activity_id);
			}
			
			if($requirement_id && is_numeric($requirement_id))
			{
				$requirement = $this->so_requirement->get_single($requirement_id);
			}

			if ($allocation_id && is_numeric($allocation_id))
			{
				$allocation = $this->so->get_single($allocation_id);
			}
			else
			{
				$allocation = new logistic_requirement_resource_allocation();
			}

			$accounts = $GLOBALS['phpgw']->acl->get_user_list_right(PHPGW_ACL_READ, 'run', 'logistic');

			if($requirement)
			{
				$loc_arr = $GLOBALS['phpgw']->locations->get_name($requirement->get_location_id());
				$entity_arr = explode('.',$loc_arr['location']);

				$requirement_values = $this->so_requirement_value->get(null, null, null, null, null, null, array('requirement_id' => $requirement->get_id()));

				$criterias_array = array();
				
				$location_id = $requirement->get_location_id();
				
				$loc_arr = $GLOBALS['phpgw']->locations->get_name($location_id);
				$entity_arr = explode('.',$loc_arr['location']);

				$entity_id = $entity_arr[2];
				$cat_id = $entity_arr[3];
				
				$criterias_array['location_id'] = $location_id;
				$criterias_array['allrows'] = true;
				
				foreach($requirement_values as $requirement_value)
				{
					$attrib_value = $requirement_value->get_value();
					$operator = $requirement_value->get_operator();
					$cust_attribute_id = $requirement_value->get_cust_attribute_id();
					
					if($operator == "eq")
					{
						$operator_str = "=";
					}
					else if($operator == "lt")
					{
						$operator_str = "<";
					}
					else if($operator == "gt")
					{
						$operator_str = ">";
					}
					
					$criteria_str = $column_name . " " . $operator_str . " " . $attrib_value;

					$condition = array(
						'operator' 		=> $operator_str,
						'value' 			=> $attrib_value,
						'attribute_id' => $cust_attribute_id
					);
					
					$criterias_array['conditions'][] = $condition;
				}
			}
		    
			$so_entity	= CreateObject('property.soentity',$entity_id,$cat_id);
			$allocation_suggestions = $so_entity->get_eav_list($criterias_array);

			print_r($allocation_suggestions);
			
			$data = array
			(
				'requirement' 						=> $requirement,
				'activity' 								=> $activity,
				'allocation_suggestions' 	=> $allocation_suggestions,
				'editable' 								=> true
			);
		
			self::render_template_xsl(array('allocation/allocation_suggestions'), $data);
		}

		public function save()
		{
			$requirement_id = phpgw::get_var('requirement_id');
			
			if($requirement_id && is_numeric($requirement_id))
			{
				$requirement = $this->so_requirement->get_single($requirement_id);
			}
			
			$chosen_resources = phpgw::get_var('chosen_resources');
			
			$user_id = $GLOBALS['phpgw_info']['user']['id'];
			
			foreach($chosen_resources as $resource_id)
			{
				$resource_alloc = new logistic_requirement_resource_allocation();
				$resource_alloc->set_requirement_id( $requirement->get_id() );
				$resource_alloc->set_resource_id( $resource_id );
				$resource_alloc->set_location_id( $requirement->get_location_id() );
				$resource_alloc->set_create_user( $user_id );
				
				$resource_alloc_id = $this->so->store( $resource_alloc );
			}
			

			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uirequirement_resource_allocation.view', 'id' => $allocation_id));
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
			$allocation_id = phpgw::get_var('allocation_id');
			$requirement_id = phpgw::get_var('requirement_id');
			if (isset($_POST['edit_allocation']))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uirequirement_resource_allocation.edit', 'id' => $activity_id, 'requirement_id' => $requirement_id, 'allocation_id' => $allocation_id));
			}
			else if (isset($_POST['new_activity']))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uirequirement_resource_allocation.edit', 'id' => $activity_id, 'requirement_id' => $requirement_id));
			}
			else
			{
				if ($activity_id && is_numeric($activity_id))
				{
					$activity = $this->so->get_single($activity_id);
				}
				if ($allocation_id && is_numeric($allocation_id))
				{
					$allocation = $this->so->get_single($allocation_id);
				}
				if ($requirement_id && is_numeric($requirement_id))
				{
					$requirement = $this->so_requirement->get_single($requirement_id);
				}

				if ($this->flash_msgs)
				{
					$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($this->flash_msgs);
					$msgbox_data = $GLOBALS['phpgw']->common->msgbox($msgbox_data);
				}

				$data = array
					(
						'activity' => $activity,
						'allocation' => $allocation,
						'requirement' => $requirement,
						'img_go_home' => 'rental/templates/base/images/32x32/actions/go-home.png',
						'dateformat' => $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']
				);

				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('logistic') . '::' . lang('Project') . '::' . lang('Activity') . '::' . lang('Allocation');
				self::render_template_xsl(array('allocation/allocation_item'), $data);
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
