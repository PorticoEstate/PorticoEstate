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
	 * @version $Id $
	 */

	phpgw::import_class('phpgwapi.uicommon');
	phpgw::import_class('logistic.sorequirement');
	phpgw::import_class('logistic.soactivity');
	phpgw::import_class('logistic.soproject');
	phpgw::import_class('property.soadmin_entity');
	phpgw::import_class('logistic.soresource_type_requirement');

	include_class('logistic', 'requirement');
	phpgw::import_class('phpgwapi.datetime');
	phpgw::import_class('phpgwapi.jquery');

	class uirequirement extends phpgwapi_uicommon
	{
		private $so;
		private $so_requirement_value;
		private $so_entity;
		private $so_activity;
		private $so_project;
		private $so_resource_type_requirement;
	
		public $public_functions = array(
			'query' => true,
			'index' => true,
			'add' => true,
			'edit' => true,
			'view' => true,
			'add_requirement_values' => true,
			'view_requirement_values' => true,
			'get_custom_attributes' => true
		);

		public function __construct()
		{
			parent::__construct();
			
			$this->so = CreateObject('logistic.sorequirement');
			$this->so_requirement_value = CreateObject('logistic.sorequirement_value');
			$this->so_entity	= CreateObject('property.soadmin_entity');
			$this->so_activity = CreateObject('logistic.soactivity');
			$this->so_project = CreateObject('logistic.soproject');
			$this->so_resource_type_requirement = CreateObject('logistic.soresource_type_requirement');
			

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "logistic::project::requirement";
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
			
			$activity_id = phpgw::get_var('activity_id');

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

			switch ($query_type)
			{
				default: // ... all composites, filters (active and vacant)
					phpgwapi_cache::session_set('logistic', 'requirement_query', $search_for);
					$filters = array('activity' => $activity_id);
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
								$result_data['results'], array($this, '_add_links'), "logistic.uirequirement.view");
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
			$activity_id = phpgw::get_var('activity_id');
			
			$data = array(
				'datatable_name'	=> lang('requirement'),
				'form' => array(
					'toolbar' => array(
						'item' => array(
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
					'source' => self::link(array('menuaction' => 'logistic.uirequirement.index', 'activity_id' => $activity_id, 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'id',
							'label' => lang('ID'),
							'sortable' => true,
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
							'key' => 'no_of_items',
							'label' => lang('No of items'),
							'sortable' => false
						),
						array(
							'key' => 'location_id',
							'label' => lang('Resource type'),
							'sortable' => false
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

		public function view()
		{
			$requirement_id = phpgw::get_var('id');

			if(isset($_POST['edit_requirement']))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uirequirement.edit', 'id' => $requirement_id));
			}
			else
			{
				if ($requirement_id && is_numeric($requirement_id))
				{
					$requirement = $this->so->get_single($requirement_id);
				}

				$location_info = $GLOBALS['phpgw']->locations->get_name($requirement->get_location_id());
				
				$data = array
					(
					'value_id' => !empty($requirement) ? $requirement->get_id() : 0,
					'requirement' => $requirement,
					'location' => $location_info,
					'view' => 'view_requirement'
				);

				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('logistic') . '::' . lang('Project') . '::' . lang('Requirement');
				self::render_template_xsl(array('requirement/requirement_item'), $data);
			}
		}

		public function add()
		{
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uirequirement.edit'));
		}

		public function edit()
		{
			$requirement_id = phpgw::get_var('id');
			$activity_id = phpgw::get_var('activity_id');

			if ($requirement_id && is_numeric($requirement_id))
			{
				$requirement = $this->so->get_single($requirement_id);
			}
			else
			{
				$requirement = new logistic_requirement();
			}
			
			if ($activity_id && is_numeric($activity_id))
			{
				$activity = $this->so_activity->get_single( $activity_id );
				$project = $this->so_project->get_single( $activity->get_project_id() );
			}
			
			if (isset($_POST['save_requirement']))
			{
				$requirement->set_id( phpgw::get_var('id') );
				$requirement->set_activity_id( phpgw::get_var('activity_id') );
				$requirement->set_no_of_items( phpgw::get_var('no_of_items') );
				$requirement->set_location_id( phpgw::get_var('location_id') );
			
				if(phpgw::get_var('start_date','string') != '')
				{
					$start_date_ts = phpgwapi_datetime::date_to_timestamp( phpgw::get_var('start_date','string') );
					$requirement->set_start_date($start_date_ts);
				}
				else
				{
					$requirement->set_start_date(0);
				}

				if( phpgw::get_var('end_date','string') != '')
				{
					$end_date_ts = phpgwapi_datetime::date_to_timestamp( phpgw::get_var('end_date','string') );
					$requirement->set_end_date($end_date_ts);
				}
				else
				{
					$requirement->set_end_date(0);
				}
					
				$user_id = $GLOBALS['phpgw_info']['user']['id'];
				$requirement->set_create_user($user_id);
				
				$requirement_id = $this->so->store($requirement);
					
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uirequirement.add_requirement_values', 'requirement_id' => $requirement_id));
			}
			else if (isset($_POST['cancel_requirement']))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uirequirement.view', 'id' => $requirement_id));
			}
			else
			{
				$accounts = $GLOBALS['phpgw']->acl->get_user_list_right(PHPGW_ACL_READ, 'run', 'logistic');

				$entity_list = execMethod('property.soadmin_entity.read', array('allrows' => true));
				
				$filters = array('project_type_id' => $project->get_project_type_id());
				$search_type = 'distinct_location_id';
				$distict_location_ids = $this->so_resource_type_requirement->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
				
				$distict_location_ids_array = array();
				
				foreach($distict_location_ids as $logistic_resource_type_requirement )
				{
					$location_id = $logistic_resource_type_requirement->get_id();
					$loc_arr = $GLOBALS['phpgw']->locations->get_name($location_id);
					
					$loc_arr['location_id'] = $location_id; 
					
					$distict_locations_array[] = $loc_arr; 
				}		
								
				$custom	= createObject('phpgwapi.custom_fields');

				$attribute_requirement_array = array();
								
				foreach($attribute_requirement_types as $attribute_requirement){
					$location_id = $attribute_requirement->get_location_id();
					$cust_attribute_id = $attribute_requirement->get_cust_attribute_id();
					
					$loc_arr = $GLOBALS['phpgw']->locations->get_name($location_id);
					$entity_arr = explode('.',$loc_arr['location']);

					$entity_id = $entity_arr[2];
					$cat_id = $entity_arr[3];
										
					$attrib_data = $custom->get('property', ".entity.{$entity_id}.{$cat_id}", $cust_attribute_id);
					
					$attribute_requirement_array[] = $attrib_data;
				}

				$tabs = $this->make_tab_menu($requirement_id);
				
				$data = array
				(
					'tabs'							=> $GLOBALS['phpgw']->common->create_tabs($tabs, 0),
					'view'							=> "requirement_details",
					'requirement' 			=> $requirement,
					'distict_locations' => $distict_locations_array,
				//	'attribute_requirement_array' => $attribute_requirement_array,
					'editable' => true,
				//	'entity_list' => $entity_list,
				//	'entity_id' => $entity_id,
				//	'category_id' => $category_id
				);
				
				if($activity_id > 0)
				{
					$data['activity'] = $activity;
				}
				
				
				$GLOBALS['phpgw']->jqcal->add_listener('start_date');
				$GLOBALS['phpgw']->jqcal->add_listener('end_date');

				//self::add_javascript('logistic', 'logistic', 'requirement.js');
				self::render_template_xsl(array('requirement/requirement_tabs', 'requirement/requirement_item'), $data);
			}
		}
		
		public function add_requirement_values()
		{
			$requirement_id = phpgw::get_var('requirement_id');
			
			if ($requirement_id && is_numeric($requirement_id))
			{
				$requirement = $this->so->get_single($requirement_id);
			}
			else
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uirequirement.edit'));
			}
			
			if (isset($_POST['save_requirement_values']))
			{
				$attributes_array = array();
				$attributes_array = phpgw::get_var('cust_attributes');
 
				foreach($attributes_array as $attribute)
				{
					$attribute_array = explode ( ":", $attribute );					
					$cust_attribute_id = $attribute_array[0];
					$operator = $attribute_array[1];
					$attrib_value = $attribute_array[2];

					$requirement_value = new logistic_requirement_value();
					$requirement_value->set_requirement_id( $requirement_id );
					$requirement_value->set_value( $attrib_value );
					$requirement_value->set_operator( $operator );
					$requirement_value->set_cust_attribute_id( $cust_attribute_id );	
					$user_id = $GLOBALS['phpgw_info']['user']['id'];
					$requirement_value->set_create_user($user_id);
					
					$this->so_requirement_value->store($requirement_value);	
				}
	
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uirequirement.view_requirement_values', 'requirement_id' => $requirement_id));
			}
			else
			{
				$location_id = $requirement->get_location_id();
				$activity_id = $requirement->get_activity_id();
				
				$custom_attributes_array = array();
				$custom_attributes_array = $this->get_custom_attributes($location_id, $activity_id);
				
				$tabs = $this->make_tab_menu($requirement_id);
				
				$data = array
				(
					'tabs'										=> $GLOBALS['phpgw']->common->create_tabs($tabs, 1),
					'view'										=> "requirement_values",
					'requirement' 						=> $requirement,
					'custom_attributes_array'	=> $custom_attributes_array,
					'distict_locations' 			=> $distict_locations_array,
					'editable' 								=> true,
				);
				
				if($activity_id > 0)
				{
					$data['activity'] = $activity;
				}
				
				phpgwapi_jquery::load_widget('core');
				
				self::add_javascript('logistic', 'logistic', 'requirement.js');
				self::render_template_xsl(array('requirement/requirement_tabs', 'requirement/requirement_values'), $data);
			}
		}
		
		public function view_requirement_values()
		{
			$requirement_id = phpgw::get_var('requirement_id');

			if ($requirement_id && is_numeric($requirement_id))
			{
				$requirement = $this->so->get_single($requirement_id);
			}
			else
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uirequirement.edit'));
			}
			
			if (isset($_POST['edit_requirement_values']))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uirequirement.add_requirement_values', 'requirement_id' => $requirement_id));
			}
			
			$filters = array('requirement_id' => $requirement_id);
			$requirement_values_array = $this->so_requirement_value->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
	
			$custom	= createObject('phpgwapi.custom_fields');
	
			foreach($requirement_values_array as $requirement_value)
			{
				$location_id = $requirement->get_location_id(); 
		
				$loc_arr = $GLOBALS['phpgw']->locations->get_name($location_id);
				$entity_arr = explode('.',$loc_arr['location']);

				$entity_id = $entity_arr[2];
				$cat_id = $entity_arr[3];
				$cust_attribute_id = $requirement_value->get_cust_attribute_id();
									
				$attrib_data = $custom->get('property', ".entity.{$entity_id}.{$cat_id}", $cust_attribute_id);


				$requirement_attributes_array[] = array(
																									"id" 							=> $requirement_value->get_id(),
																									"value" 					=> $requirement_value->get_value(),
																									"operator" 				=> $requirement_value->get_operator(), 
																									"cust_attribute" 	=> $attrib_data
																								);
			}

			print_r($requirement_attributes_array);
			
			$tabs = $this->make_tab_menu($requirement_id);
			
			$data = array
			(
				'tabs'													=> $GLOBALS['phpgw']->common->create_tabs($tabs, 1),
				'view'													=> "requirement_values",
				'requirement' 									=> $requirement,
				'requirement_attributes_array'	=> $requirement_attributes_array
			);
			
			self::render_template_xsl(array('requirement/requirement_tabs', 'requirement/requirement_values'), $data);	
		}
		
		public function get_custom_attributes($location_id, $activity_id){
				
				if($location_id == "")
				{
					$location_id = phpgw::get_var('location_id');
				}
				
				if($activity_id == "")
				{
					$activity_id = phpgw::get_var('activity_id');
				}

				$activity = $this->so_activity->get_single( $activity_id );
				$project = $this->so_project->get_single( $activity->get_project_id() );		
				$project_type_id = $project->get_project_type_id(); 
				
				$filters = array('location_id' => $location_id, 'project_type_id' => $project_type_id);
				$requirement_custom_attributes_array = $this->so_resource_type_requirement->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
				
				$custom	= createObject('phpgwapi.custom_fields');
				
				$attribute_requirement_array = array();
								
				foreach($requirement_custom_attributes_array as $attribute_requirement){
					$location_id = $attribute_requirement->get_location_id();
					$cust_attribute_id = $attribute_requirement->get_cust_attribute_id();
					
					$loc_arr = $GLOBALS['phpgw']->locations->get_name($location_id);
					$entity_arr = explode('.',$loc_arr['location']);

					$entity_id = $entity_arr[2];
					$cat_id = $entity_arr[3];
										
					$attrib_data = $custom->get('property', ".entity.{$entity_id}.{$cat_id}", $cust_attribute_id);
					
					$attribute_requirement_array[] = $attrib_data;
				}
				
				return $attribute_requirement_array;		
		}
		
		function make_tab_menu($control_id){
			$tabs = array();
			
			if($requirement_id > 0){
				
				$requirement = $this->so->get_single($requirement_id);
				
				$tabs[] = array(
							'label' => "1: " . lang('Requirement details'),
							'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uirequirement.edit', 
																				   'id' => $requirement->get_id()))
						);
			}else{
				$tabs = array( 
						   array(
							'label' => "1: " . lang('Requirement details')
						), array(
							'label' => "2: " . lang('Add constraints')
						));
			}
			
			return $tabs;
		} 
	}