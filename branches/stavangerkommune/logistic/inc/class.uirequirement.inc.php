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
	phpgw::import_class('logistic.sorequirement_resource_allocation');

	include_class('logistic', 'requirement');
	phpgw::import_class('phpgwapi.datetime');
	phpgw::import_class('phpgwapi.jquery');

	class logistic_uirequirement extends phpgwapi_uicommon
	{
		private $so;
		private $so_requirement_value;
		private $so_entity;
		private $so_activity;
		private $so_project;
		private $so_resource_type_requirement;
		private $so_resource_allocation;
		private $nonavbar;

	    private $read;
	    private $add;
	    private $edit;
	    private $delete;
	    private $manage;

		public $public_functions = array
		(
			'query' 						=> true,
			'index' 						=> true,
			'add' 							=> true,
			'edit' 							=> true,
			'delete'						=> true,
			'view' 							=> true,
			'save' 							=> true,
			'add_requirement_values' 		=> true,
			'view_requirement_values'		=> true,
			'save_requirement_values'		=> true,
			'get_custom_attributes'			=> true,
			'assign_job'					=> true,
			'send_job_ticket'				=> true,
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
			$this->so_resource_allocation = CreateObject('logistic.sorequirement_resource_allocation');

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "logistic::project::requirement";

/*
			if( $this->nonavbar	= phpgw::get_var('nonavbar', 'bool'))
			{
				$GLOBALS['phpgw_info']['flags']['nonavbar'] = $this->nonavbar;
				$GLOBALS['phpgw_info']['flags']['noheader_xsl'] = true;
				$GLOBALS['phpgw_info']['flags']['nofooter']		= true;
			}
*/
			$GLOBALS['phpgw_info']['flags']['nonavbar'] = true;
			$GLOBALS['phpgw_info']['flags']['noheader_xsl'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter']		= true;


			$this->read    = $GLOBALS['phpgw']->acl->check('.activity', PHPGW_ACL_READ, 'logistic');//1 
			$this->add     = $GLOBALS['phpgw']->acl->check('.activity', PHPGW_ACL_ADD, 'logistic');//2 
			$this->edit    = $GLOBALS['phpgw']->acl->check('.activity', PHPGW_ACL_EDIT, 'logistic');//4 
			$this->delete  = $GLOBALS['phpgw']->acl->check('.activity', PHPGW_ACL_DELETE, 'logistic');//8 
			$this->manage  = $GLOBALS['phpgw']->acl->check('.activity', 16, 'logistic');//16

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

			//Sigurd
			$custom	= createObject('phpgwapi.custom_fields');

//_debug_array($rows);
			$line_id = 0; // optional preselect
			foreach($rows as &$entry)
			{

//-----------Sigurd
				$_filters = array('requirement_id' => $entry['id']);
				$requirement_values_array = $this->so_requirement_value->get(0, false, $sort_field, $sort_ascending, $search_for, $search_type, $_filters);

				$location_id = $entry['location_id'];
				$criterias = array();
				if( count( $requirement_values_array ) > 0 )
				{
					foreach($requirement_values_array as $requirement_value)
					{
						$loc_arr = $GLOBALS['phpgw']->locations->get_name($location_id);
						$cust_attribute_id = $requirement_value->get_cust_attribute_id();

						$attrib_data = $custom->get('property', $loc_arr['location'], $cust_attribute_id);

						$_criterie = $attrib_data['input_text'];

						if(isset($attrib_data['choice']))
						{
							foreach ($attrib_data['choice'] as $_choice)
							{
								if($_choice['id'] == $requirement_value->get_value())
								{
									$_criterie .= "::{$_choice['value']}";
									break;
								}
							}
						}
						else if($requirement_value->get_value())
						{
							$_criterie .= "::{$requirement_value->get_value()}";
						}

						$criterias[] = $_criterie;

//						$operator	= $requirement_value->get_operator();
					}
				}
				$entry['criterias'] = implode(',',$criterias);

//-------------
				$_checked = '';

				if($entry['id'] == $line_id)
				{
					$_checked = 'checked="checked"';
				}

				$num_required = $entry['no_of_items'];
	  
				$num_allocated = $this->so_resource_allocation->count_allocated($entry['id']);

				$entry['allocated'] = $num_allocated;
				$entry['select'] = "<input class=\"select_line\" type =\"radio\" {$_checked} name=\"values[select_line]\" value=\"{$entry['id']}\">";

				if($num_allocated == $num_required)
				{
					$entry['status'] = "OK";

					$entry['alloc_link'] = "<span class='btn-sm cancel'>Tildel ressurser</span>";
				}
				else
				{
					$num_remaining = $num_required - $num_allocated;
					$entry['status'] = "MANGLER (" . $num_remaining . ")";

					$href = self::link(array('menuaction' => 'logistic.uirequirement_resource_allocation.edit', 'requirement_id' => $entry['id']));
					$entry['alloc_link'] = "<a class=\"btn-sm alloc\" href=\"{$href}\">Tildel ressurser</a>";
				}

				//$href = self::link(array('menuaction' => 'logistic.uirequirement.edit', 'id' => $entry['id']));
				$href = "javascript:load_requirement_edit_id({$entry['id']});";
				$entry['edit_requirement_link'] = "<a class=\"btn-sm alloc\" href=\"{$href}\">Endre behov</a>";

				$href = "javascript:load_requirement_delete_id({$entry['id']});";
				$entry['delete_requirement_link'] = "<a class=\"btn-sm alloc\" href=\"{$href}\">Slett behov</a>";
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
				array_walk($result_data['results'], array($this, '_add_links'), "logistic.uirequirement.view");
			}
			return $this->yui_results($result_data);
		}

		public function index()
		{
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');
			$activity_id = phpgw::get_var('activity_id');

			$activity = $this->so_activity->get_single($activity_id);

			$data = array(
				'datatable_name'	=> lang('requirement'),
				'activity'	=> $activity,
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
							'key' => 'select',
							'label' => lang('select'),
							'sortable' => false,
						),
						array(
							'key' => 'id',
							'label' => lang('Id'),
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
							'label' => lang('Num required'),
							'sortable' => false
						),
						array(
							'key' => 'allocated',
							'label' => lang('Num allocated'),
							'sortable' => false
						),
						array(
							'key' => 'location_label',
							'label' => lang('Resource type'),
							'sortable' => false
						),
						array(
							'key' => 'link',
							'hidden' => true
						),
						array(
							'key' => 'id',
							'className' => 'requirement_id',
							'hidden' => true
						),
						array(
							'key' => 'alloc_link',
							'label' => lang('Allocate resources'),
							'sortable' => false,
						),
						array(
							'key' => 'status',
							'label' => lang('Status'),
							'sortable' => false,
						),
						array(
							'key' => 'edit_requirement_link',
							'label' => lang('Status'),
							'sortable' => false,
						),
					)
				),
			);

			phpgwapi_jquery::load_widget('core');

			self::add_javascript('logistic', 'logistic', 'resource_allocation.js');
			self::render_template_xsl( 'requirement/requirement_overview', $data);
		}

		public function view()
		{
			if( $nonavbar	= phpgw::get_var('nonavbar', 'bool'))
			{
				$GLOBALS['phpgw_info']['flags']['nonavbar'] = $nonavbar;
				$GLOBALS['phpgw_info']['flags']['noheader_xsl'] = true;
				$GLOBALS['phpgw_info']['flags']['nofooter']		= true;
			}

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

				$activity = $this->so_activity->get_single($requirement->get_activity_id());

				$location_info = $GLOBALS['phpgw']->locations->get_name($requirement->get_location_id());

				$tabs = $this->make_tab_menu($requirement_id);

				$data = array
				(
					'tabs'				=> $GLOBALS['phpgw']->common->create_tabs($tabs, 0),
					'view'				=> "requirement_details",
					'requirement' => $requirement,
					'activity' 	=> $activity,
					'location' 		=> $location_info,
				);

				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('logistic') . '::' . lang('Project') . '::' . lang('Requirement');
				self::render_template_xsl(array('requirement/requirement_tabs', 'requirement/requirement_item'), $data);
			}
		}

		public function add()
		{
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uirequirement.edit'));
		}

		public function edit($requirement = null)
		{
			if( $nonavbar	= phpgw::get_var('nonavbar', 'bool'))
			{
				$GLOBALS['phpgw_info']['flags']['nonavbar'] = $nonavbar;
				$GLOBALS['phpgw_info']['flags']['noheader_xsl'] = true;
				$GLOBALS['phpgw_info']['flags']['nofooter']		= true;
			}

			$requirement_id = phpgw::get_var('id');
			$activity_id = phpgw::get_var('activity_id');

			if ( ($requirement == null) && ($requirement_id) && (is_numeric($requirement_id)) )
			{
				$requirement = $this->so->get_single($requirement_id);

				$activity = $this->so_activity->get_single( $requirement->get_activity_id() );
				$project = $this->so_project->get_single( $activity->get_project_id() );
			}
			else
			{
				if($requirement == null)
				{
					$requirement = new logistic_requirement();
				}

				if ($activity_id && is_numeric($activity_id))
				{
					$activity = $this->so_activity->get_single( $activity_id );
					$requirement->set_start_date($activity->get_start_date());
					$requirement->set_end_date($activity->get_end_date());
					$project = $this->so_project->get_single( $activity->get_project_id() );
				}
			}

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
				'tabs'				=> $GLOBALS['phpgw']->common->create_tabs($tabs, 0),
				'view'				=> "requirement_details",
				'requirement' 		=> $requirement,
				'distict_locations' => $distict_locations_array,
				'editable'			=> true,
				'nonavbar'			=> $nonavbar
			);

			if($activity_id > 0)
			{
				$data['activity'] = $activity;
			}
			else
			{
				$activity = $this->so_activity->get_single( $requirement->get_activity_id() );
				$data['activity'] = $activity;
			}

			$GLOBALS['phpgw']->jqcal->add_listener('start_date', 'datetime');
			$GLOBALS['phpgw']->jqcal->add_listener('end_date', 'datetime');

			self::render_template_xsl(array('requirement/requirement_tabs', 'requirement/requirement_item'), $data);
		}

		public function save()
		{
			$requirement_id = phpgw::get_var('id', 'int');
			$new_location_id = phpgw::get_var('location_id');

			if( $nonavbar	= phpgw::get_var('nonavbar', 'bool'))
			{
				$GLOBALS['phpgw_info']['flags']['nonavbar'] = $nonavbar;
				$GLOBALS['phpgw_info']['flags']['noheader_xsl'] = true;
				$GLOBALS['phpgw_info']['flags']['nofooter']		= true;
			}

			if(!$this->read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uirequirement.view', 'id' => $requirement_id, 'nonavbar' => $nonavbar));
				return false; // in case redirect fail;
			}

			if ($requirement_id)
			{
				$requirement = $this->so->get_single($requirement_id);
				$old_location_id = $requirement->get_location_id();
			}
			else
			{
				$requirement = new logistic_requirement();
			}

			$requirement->populate();

			if( $requirement->validate() )
			{
//				$db_requirement = $this->so->get_db();
//				$db_requirement->transaction_begin();
				$GLOBALS['phpgw']->db->transaction_begin();
				$requirement_id = $this->so->store($requirement);

				$status_delete_values = true;
				if( ($old_location_id > 0) && (is_numeric($old_location_id) ) && ($old_location_id != $new_location_id) )
				{
					$status_delete_values = $this->so_requirement_value->delete_values( $requirement_id );
					$status_delete_resources = $this->so_resource_allocation->delete_resources( $requirement_id );
				}

				if( ($requirement_id > 0) && ($status_delete_values) && ($status_delete_resources) )
				{
//					$db_requirement->transaction_commit();
					$GLOBALS['phpgw']->db->transaction_commit();
				}
				else
				{
					$GLOBALS['phpgw']->db->transaction_commit();
//					$db_requirement->transaction_abort();
				}

				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uirequirement.view', 'id' => $requirement_id, 'nonavbar' => $nonavbar));
			}
			else
			{
				$this->edit($requirement);
			}
		}

		public function delete()
		{
			if(!$this->delete)
			{
				return false;
			}

			$requirement_id = phpgw::get_var('id', 'int');
			$GLOBALS['phpgw']->db->transaction_begin();
			try
			{
				$this->so_requirement_value->delete_values( $requirement_id );
				$this->so_resource_allocation->delete_resources( $requirement_id );
				$this->so->delete( $requirement_id );
			}
			catch (Exception $e)
			{
				if($e)
				{
					$GLOBALS['phpgw']->db->transaction_abort();

					$GLOBALS['phpgw']->log->error(array(
						'text'	=> 'uirequirement::delete() : error when trying to delete requirement: %1',
						'p1'	=> $e->getMessage(),
						'p2'	=> '',
						'line'	=> __LINE__,
						'file'	=> __FILE__
					));

				}

				return $e->getMessage();
			}
			$GLOBALS['phpgw']->db->transaction_commit();
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

			$location_id = $requirement->get_location_id();
			$activity_id = $requirement->get_activity_id();

			$custom_attributes_array = array();
			$custom_attributes_array = $this->get_custom_attributes($location_id, $activity_id);

			$filters = array('requirement_id' => $requirement_id);
			$requirement_values_array = $this->so_requirement_value->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);

			$custom	= createObject('phpgwapi.custom_fields');

			if( count( $requirement_values_array ) > 0 )
			{
				foreach($requirement_values_array as $requirement_value)
				{
					$location_id = $requirement->get_location_id();

					$loc_arr = $GLOBALS['phpgw']->locations->get_name($location_id);
					$entity_arr = explode('.',$loc_arr['location']);

					$entity_id = $entity_arr[2];
					$cat_id = $entity_arr[3];
					$cust_attribute_id = $requirement_value->get_cust_attribute_id();

					$attrib_data = $custom->get('property', ".entity.{$entity_id}.{$cat_id}", $cust_attribute_id);

					$temp_requirement_attributes_array[$cust_attribute_id][] = array(
						"id" 							=> $requirement_value->get_id(),
						"attrib_value"		=> $requirement_value->get_value(),
						"operator" 				=> $requirement_value->get_operator(),
						"cust_attribute" 	=> $attrib_data
					);
				}


				foreach($temp_requirement_attributes_array as $req_attrib)
				{
					if( count( $req_attrib ) > 1 )
					{
						if( $req_attrib[0]['operator'] == 'gt' )
						{
							$constraint_1 = $req_attrib[0];
							$constraint_2 = $req_attrib[1];
						}
						else
						{
							$constraint_1 = $req_attrib[1];
							$constraint_2 = $req_attrib[0];
						}

						$req_attrib[0]['operator'] = 'btw';
						$req_attrib[0]['attrib_value'] = $constraint_1['attrib_value'] . ":" . $constraint_2['attrib_value'];
						$requirement_attributes_array[] = $req_attrib[0];
					}
					else
					{
						$requirement_attributes_array[] = $req_attrib[0];
					}
				}
			}
			else
			{
				foreach($custom_attributes_array as $cust_attrib)
				{
					$requirement_attributes_array[] = array(
						"id" 							=> "",
						"attrib_value" 		=> "",
						"operator" 				=> "",
						"cust_attribute" 	=> $cust_attrib
					);
				}
			}

			$tabs = $this->make_tab_menu($requirement_id);

			$data = array
			(
				'tabs'							=> $GLOBALS['phpgw']->common->create_tabs($tabs, 1),
				'view'							=> "requirement_values",
				'requirement' 					=> $requirement,
				'requirement_attributes_array'	=> $requirement_attributes_array,
				'distict_locations' 			=> $distict_locations_array,
				'editable' 						=> true,
			);

			if($activity_id > 0)
			{
				$data['activity'] = $activity;
			}

			phpgwapi_jquery::load_widget('core');

			self::add_javascript('logistic', 'logistic', 'requirement.js');
			self::render_template_xsl(array('requirement/requirement_tabs', 'requirement/requirement_values'), $data);
		}

		public function view_requirement_values()
		{
			if( $nonavbar	= phpgw::get_var('nonavbar', 'bool'))
			{
				$GLOBALS['phpgw_info']['flags']['nonavbar'] = $nonavbar;
				$GLOBALS['phpgw_info']['flags']['noheader_xsl'] = true;
				$GLOBALS['phpgw_info']['flags']['nofooter']		= true;
			}

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

				$temp_requirement_attributes_array[$cust_attribute_id][] = array(
					"id" 							=> $requirement_value->get_id(),
					"attrib_value" 		=> $requirement_value->get_value(),
					"operator" 				=> $requirement_value->get_operator(),
					"cust_attribute" 	=> $attrib_data
				);
			}

			foreach($temp_requirement_attributes_array as $req_attrib)
			{
				if( count( $req_attrib ) > 1 )
				{
					if( $req_attrib[0]['operator'] == 'gt' )
					{
						$constraint_1 = $req_attrib[0];
						$constraint_2 = $req_attrib[1];
					}
					else
					{
						$constraint_1 = $req_attrib[1];
						$constraint_2 = $req_attrib[0];
					}

					$req_attrib[0]['operator'] = 'btw';
					$req_attrib[0]['attrib_value'] = $constraint_1['attrib_value'] . ":" . $constraint_2['attrib_value'];
					$requirement_attributes_array[] = $req_attrib[0];
				}
				else
				{
					$requirement_attributes_array[] = $req_attrib[0];
				}
			}

			$tabs = $this->make_tab_menu($requirement_id);

			$activity = $this->so_activity->get_single( $requirement->get_activity_id() ); 

			$data = array
			(
				'tabs'													=> $GLOBALS['phpgw']->common->create_tabs($tabs, 1),
				'view'													=> "requirement_values",
				'requirement' 									=> $requirement,
				'activity' 											=> $activity,
				'requirement_attributes_array'	=> $requirement_attributes_array
			);

			self::render_template_xsl(array('requirement/requirement_tabs', 'requirement/requirement_values'), $data);
		}

		public function save_requirement_values()
		{
				$requirement_id = phpgw::get_var('requirement_id');
				$attributes_array = array();
				$attributes_array = phpgw::get_var('cust_attributes');

				$this->so_requirement_value->delete_values($requirement_id);

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

				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uirequirement.view_requirement_values', 'requirement_id' => $requirement_id, 'nonavbar' => $this->nonavbar));
		}

		public function get_custom_attributes($location_id, $activity_id)
		{
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

			foreach($requirement_custom_attributes_array as $attribute_requirement)
			{
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

		public function assign_job()
		{
			$assign_requirement_json = str_replace('&quot;', '"', phpgw::get_var('assign_requirement'));

			$assign_requirement=json_decode($assign_requirement_json);
//_debug_array($assign_requirement);die();
			if(!$assign_requirement || !is_array($assign_requirement))
			{
				echo 'Nothing to do';
				return;
			}

			$allocations = array();
			foreach ($assign_requirement as $assign_entry)
			{
				$assign_arr = explode('_', $assign_entry);
				$requirement_id = $assign_arr[0];
				$allocation_id = $assign_arr[1];
				$location_id = $assign_arr[2];
				$item_id = $assign_arr[3];
				$inventory_id = (int)$assign_arr[4];

				$allocations[] = $this->so_resource_allocation->get_single($allocation_id);

			}


			$requirement = $this->so->get_single($requirement_id);

			$custom	= createObject('phpgwapi.custom_fields');

//--
			$_filters = array('requirement_id' => $requirement_id);
			$requirement_values_array = $this->so_requirement_value->get(0, false, $sort_field, $sort_ascending, $search_for, $search_type, $_filters);

			$location_id = $requirement->get_location_id();
			$criterias = array();
			if( count( $requirement_values_array ) > 0 )
			{
				foreach($requirement_values_array as $requirement_value)
				{
					$loc_arr = $GLOBALS['phpgw']->locations->get_name($location_id);

					$cust_attribute_id = $requirement_value->get_cust_attribute_id();

					$attrib_data = $custom->get('property', $loc_arr['location'], $cust_attribute_id);

					$_criterie = $attrib_data['input_text'];

					if(isset($attrib_data['choice']))
					{
						foreach ($attrib_data['choice'] as $_choice)
						{
							if($_choice['id'] == $requirement_value->get_value())
							{
								$_criterie .= "::{$_choice['value']}";
								break;
							}
						}
					}
					else if($requirement_value->get_value())
					{
						$_criterie .= "::{$requirement_value->get_value()}";
					}

					$criterias[] = $_criterie;

//					$operator	= $requirement_value->get_operator();
				}
			}


			$path = $this->so_activity->get_path($requirement->get_activity_id());

			$breadcrumb_array = array();
			foreach($path as $menu_item)
			{
				$breadcrumb_array[] = $menu_item['name'];
			}
			
			$title = implode(' -> ',$breadcrumb_array);


			$message .= 'Hvor: ' . $title . "\n\n";
			$requirement_descr = $loc_arr['descr'] . '::' . implode(',',$criterias);
			$message .= 'Hva: ' . $requirement_descr . "\n\n";
				
			#FIXME timezone..
			//$GLOBALS['phpgw']->common->show_date($requirement->get_start_date())
			//$message .= 'Frist:' . $GLOBALS['phpgw']->common->show_date($requirement->get_start_date()) . "\n\n";
				
			$datetime_format = "{$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']} H:i";
				
			$when = date($datetime_format, $requirement->get_start_date());
			$message .= 'Frist: ' . $when . "\n\n";

			foreach ($allocations as $allocation)
			{
				$message .= 'Antall: ';
				$message .= $allocation->get_allocated_amount();
				$message .= ' Fra: ';


				if($allocation->get_inventory_id())
				{
					$inventory = execMethod('property.soentity.get_inventory',array('inventory_id' => $allocation->get_inventory_id()));
					$system_location = $GLOBALS['phpgw']->locations->get_name($inventory[0]['p_location_id']);
					$name = 'N∕A';
					if( preg_match('/.location./i', $system_location['location']) )
					{
						$location_code = execMethod('property.solocation.get_location_code', $inventory[0]['p_id']);
						$location = execMethod('property.solocation.read_single', $location_code);
						$location_arr = explode('-', $location_code);
						$i=1;
						$name_arr = array();
						foreach($location_arr as $_dummy)
						{
							$name_arr[] = $location["loc{$i}_name"];
							$i++;
						}

						$name = implode('::', $name_arr);
					}
					else if( preg_match('/.entity./i', $system_location['location']) )
					{
						$name = execMethod('property.soentity.get_short_description', 
									array('location_id' => $inventory[0]['p_location_id'], 'id' => $inventory[0]['p_id']));
					}

				}

				$message .= "$name ($location_code)\n";
			}
				
// -------- 


			$catsObj = CreateObject('phpgwapi.categories', -1, 'property', '.ticket');
			$catsObj->supress_info = true;
			
			$categories	= $catsObj->formatted_xslt_list(array('select_name' => 'values[cat_id]','selected' => $this->cat_id, 'use_acl' => $this->_category_acl));


			$data = array
			(
				'requirement_id'			=> $requirement_id,
				'title'						=> $title,
				'title_size'				=> strlen($title) > 20 ? strlen($title) : 20,
				'categories'				=> $categories,
				'assign_requirement_json'	=> $assign_requirement_json,
				'requirement_descr'			=> $requirement_descr,
				'message'					=> $message,
				'priority_list'				=> array('options' => execMethod('property.botts.get_priority_list'))
			);
						
			if(count( $buildings_array ) > 0)
			{
				$data['buildings_array']  = $buildings_array;
			}
			else
			{
				$data['building_array'] = $building_array;
			}
						
			phpgwapi_jquery::load_widget('core');

			self::add_javascript('logistic', 'logistic', 'assign_job.js');
			
			self::render_template_xsl(array('allocation/assign_job'), $data);
//------

		}


		function send_job_ticket()
		{
			if(!$this->add && !$this->edit)
			{
				phpgwapi_cache::message_set('No access', 'error');
			}

			$assign_requirement_json = str_replace('&quot;', '"', phpgw::get_var('assign_requirement'));

			$assign_requirement=json_decode($assign_requirement_json);

			if(!$assign_requirement || !is_array($assign_requirement))
			{
				echo 'Nothing to do';
				return;
			}

			$allocations = array();
			foreach ($assign_requirement as $assign_entry)
			{
				$assign_arr = explode('_', $assign_entry);
				$requirement_id = $assign_arr[0];
				$allocation_id = $assign_arr[1];
				$location_id = $assign_arr[2];
				$item_id = $assign_arr[3];
				$inventory_id = (int)$assign_arr[4];

				$allocations[] = $this->so_resource_allocation->get_single($allocation_id);

			}

			$requirement = $this->so->get_single($requirement_id);
			
			// This value represents the type 
			$location_id = $GLOBALS['phpgw']->locations->get_id("logistic", ".activity");
			
			$ticket = array
			(
				'origin_id'         => $location_id,
				'origin_item_id'	=> $requirement->get_activity_id(), 
				'location_code' 	=> $location_code,
				'cat_id'			=> phpgw::get_var('message_cat_id', 'int'),
				'priority'			=> phpgw::get_var('priority', 'int'),
				'title'				=> phpgw::get_var('message_title', 'string'),
				'details'			=> phpgw::get_var('message', 'string'),
				'file_input_name'	=> 'file' // navn på felt som inneholder fil
			);
			
			$botts = CreateObject('property.botts',true);
			$message_ticket_id = $botts->add_ticket($ticket);
			if($location_id_ticket = $GLOBALS['phpgw']->locations->get_id('property', '.ticket'))
			{

//---Sigurd: start register allocation to ticket
				$GLOBALS['phpgw']->db->transaction_begin();

				$user_id = $GLOBALS['phpgw_info']['user']['id'];

				$interlink_verify = array();
				foreach ($assign_requirement as $assign_entry)
				{
					$assign_arr = explode('_', $assign_entry);
					$requirement_id = $assign_arr[0];
					$allocation_id = $assign_arr[1];
					$location_id = $assign_arr[2];
					$item_id = $assign_arr[3];
					$inventory_id = (int)$assign_arr[4];

					$interlink_data = array
					(
						'location1_id'      => $location_id,
						'location1_item_id' => $item_id,
						'location2_id'      => $location_id_ticket,
						'location2_item_id' => $message_ticket_id,
						'account_id'        => $user_id
					);

					if(!isset($interlink_verify[$location_id][$item_id][$location_id_ticket][$message_ticket_id]))
					{
						execMethod('property.interlink.add', $interlink_data);
						$interlink_verify[$location_id][$item_id][$location_id_ticket][$message_ticket_id] = true;
					}

					$allocation = $this->so_resource_allocation->get_single($allocation_id);
					$allocation->set_ticket_id($message_ticket_id);
					$this->so_resource_allocation->store($allocation);
				}

				$GLOBALS['phpgw']->db->transaction_commit();
			}

//---End register allocation to ticket
			
		}




		private function make_tab_menu($requirement_id)
		{
			$tabs = array();

			if($requirement_id > 0)
			{

				$requirement = $this->so->get_single($requirement_id);

				$tabs = array(
						   array(
							'label' => "1: " . lang('Requirement details'),
						   'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'logistic.uirequirement.view',
																				   	 'id' => $requirement->get_id(),
																				   	 'nonavbar' => $this->nonavbar))
						), array(
							'label' => "2: " . lang('Add constraints'),
							'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'logistic.uirequirement.view_requirement_values',
																				   	'requirement_id' => $requirement->get_id(),
																				   	 'nonavbar' => $this->nonavbar))
						));
			}
			else
			{
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
