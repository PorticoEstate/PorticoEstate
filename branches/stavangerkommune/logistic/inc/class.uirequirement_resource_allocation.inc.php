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
	 * @version $Id: class.uirequirement_resource_allocation.inc.php 11287 2013-09-11 11:37:14Z sigurdne $
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
	    private $read;
	    private $add;
	    private $edit;
	    private $delete;
	    private $manage;

		public $public_functions = array(
			'query' => true,
			'add' 	=> true,
			'edit' => true,
			'view' => true,
			'index' => true,
			'save' => true,
			'delete' => true
		);

		public function __construct()
		{
			parent::__construct();

			$this->so 					= createObject('logistic.sorequirement_resource_allocation');
			$this->so_activity 			= createObject('logistic.soactivity');
			$this->so_requirement 		= createObject('logistic.sorequirement');
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

			$this->read    = $GLOBALS['phpgw']->acl->check('.activity', PHPGW_ACL_READ, 'logistic');//1 
			$this->add     = $GLOBALS['phpgw']->acl->check('.activity', PHPGW_ACL_ADD, 'logistic');//2 
			$this->edit    = $GLOBALS['phpgw']->acl->check('.activity', PHPGW_ACL_EDIT, 'logistic');//4 
			$this->delete  = $GLOBALS['phpgw']->acl->check('.activity', PHPGW_ACL_DELETE, 'logistic');//8 
			$this->manage  = $GLOBALS['phpgw']->acl->check('.activity', 16, 'logistic');//16
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
							'label' => lang('Id'),
							'sortable' => true,
						),
						array(
							'key' => 'resource_type',
							'label' => lang('Resource type'),
							'sortable' => true
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
						array(
							'key' => 'delete_link',
							'label' => lang('Delete'),
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
			$lang_delete = lang('delete');
			$lang_assign = lang('assign');
			$lang_ticket = lang('ticket');
			foreach ($result_objects as $result)
			{
				if (isset($result))
				{
					$allocation = $result->serialize();

					if($short_desc = execMethod('property.soentity.get_short_description', 
							array('location_id' => $allocation['location_id'], 'id' => $allocation['resource_id'])))
					{
						$allocation['fm_bim_item_name'] = $short_desc;
					}

					if($allocation['inventory_id'])
					{
						$inventory = execMethod('property.soentity.get_inventory',array('inventory_id' => $allocation['inventory_id']));

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

						$allocation['location_code'] = $location_code;
						$allocation['fm_bim_item_address'] = $name;
					}
					
					$delete_href = self::link(array('menuaction' => 'logistic.uirequirement_resource_allocation.delete', 
																	'id' => $allocation['id'],
																	'phpgw_return_as' => 'json')
																);

					$delete_href = "javascript:load_delete_allocation({$allocation['id']});";

					$allocation['delete_link'] = "<a class=\"btn-sm \" href=\"{$delete_href}\">{$lang_delete}</a>";

					$assign_href = "javascript:load_assign_task(document.assign_task, {$allocation['id']});";
					
					$disabled = $allocation['ticket_id'] ? 'disabled = "disabled"' : '';

					if($allocation['ticket_id'])
					{

						 $related_href = self::link(array('menuaction' => 'property.uitts.view','id' => $allocation['ticket_id']));
						 $allocation['related'] = "<a  href=\"{$related_href}\">{$lang_ticket} #{$allocation['ticket_id']}</a>";
						
						$relation_info = execMethod('property.interlink.get_relation_info', 
										array('location' => '.ticket', 'id' => $allocation['ticket_id']));

						$allocation['status'] = $relation_info['statustext'];
					}

					$allocation['assign_job'] = "<input name='assign_requirement' type='checkbox' {$disabled} value='{$requirement_id}_{$allocation['id']}_{$allocation['location_id']}_{$allocation['resource_id']}_{$allocation['inventory_id']}' />"
					 . "<a class=\"btn-sm assign\" href=\"{$assign_href}\">{$lang_assign}</a>";
					 

					$rows[] = $allocation;
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

//			$accounts = $GLOBALS['phpgw']->acl->get_user_list_right(PHPGW_ACL_READ, 'run', 'logistic');
			$allocation_suggestions = array();

			if($requirement)
			{
				$requirement_values = $this->so_requirement_value->get(null, null, null, null, null, null, array('requirement_id' => $requirement->get_id()));

				$criterias_array = array();

				$location_id = $requirement->get_location_id();

				$loc_arr = $GLOBALS['phpgw']->locations->get_name($location_id);

				$criterias_array['location_id'] = $location_id;
				$criterias_array['allrows'] = true;

				$view_criterias_array = array();
				$custom	= createObject('phpgwapi.custom_fields');

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

					$attrib_data = $custom->get($loc_arr['appname'], $loc_arr['location'], $cust_attribute_id);

					$view_criterias_array[] =  array(
							'operator'						=> $operator_str,
							'value' 						=> $attrib_value,
							'cust_attribute_data'			=> $attrib_data
						);

					$condition = array(
						'operator' 		=> $operator_str,
						'value' 		=> $attrib_value,
						'attribute_id'	=> $cust_attribute_id
					);

					$criterias_array['conditions'][] = $condition;
				}


				$entity_category = execMethod('property.soadmin_entity.get_single_category', $location_id);

				$allocation_suggestions = execMethod('property.soentity.get_eav_list', $criterias_array);
				
				if($entity_category['enable_bulk'])
				{
					foreach ($allocation_suggestions as &$entry)
					{
						$entry['inventory'] = execMethod('property.boentity.get_inventory', array('location_id' => $location_id, 'id' => $entry['id']));
					}
				}

			}
//Start fuzzy

//_debug_array($allocation_suggestions);die();

			$suggestion_ids = array();


			foreach ($allocation_suggestions as $allocation_suggestion)
			{
				$suggestion_ids[] = $allocation_suggestion['id'];
			}

			reset($allocation_suggestions);

			$allocated = $this->so->check_calendar($requirement, $suggestion_ids);
//_debug_array($allocated);die();
//end fuzzy
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$activities = array();
			
			foreach ($allocation_suggestions as &$allocation_suggestion)
			{
				if(isset($allocated['items'][$allocation_suggestion['id']]))
				{
					$allocation_suggestion['allocated'] = true;
					$allocated_where = array();
					$allocated_date = array();
					foreach ($allocated['calendar'][$allocation_suggestion['id']] as $calendar_entry)
					{
						$allocated_date[] = $GLOBALS['phpgw']->common->show_date($calendar_entry['start_date'] ,$dateformat)
											. ' - ' . $GLOBALS['phpgw']->common->show_date($calendar_entry['end_date'] ,$dateformat);

						if(!isset($activities[$calendar_entry['activity_id']]))
						{
							$activities[$calendar_entry['activity_id']] = $this->so_activity->get_single( $calendar_entry['activity_id'] );
						}
						
						if($activities[$calendar_entry['activity_id']])
						{
							$allocated_where[] = $activities[$calendar_entry['activity_id']]->get_name();
						}
						else
						{
							$allocated_where[] = 'N/A';
						}
					}

					$allocation_suggestion['allocated_date'] = implode('; ', $allocated_date);
					$allocation_suggestion['allocated_where'] = implode('; ', $allocated_where);
				}
				if(isset($allocation_suggestion['inventory']))
				{
					foreach ($allocation_suggestion['inventory'] as & $inventory)
					{
						
						//Allocated to another requirement:
						if(isset($allocated['allocations'][$inventory['inventory_id']]['requirement_id']) && $requirement->get_id() != $allocated['allocations'][$inventory['inventory_id']]['requirement_id'])
						{
							$inventory['disabled'] = true;
						}
						
						$inventory['allocated_amount'] = $allocated['inventory'][$inventory['inventory_id']];

						$inventory['bookable_amount'] = (int)$inventory['inventory'] - (int)$allocated['allocations'][$inventory['inventory_id']]['total_allocated'];

						$inventory['allocation_id'] = $allocated['allocations'][$inventory['inventory_id']]['allocation_id'];
						
						if($allocated['allocations'][$inventory['inventory_id']]['start_date'])
						{
							$inventory['allocated_date'] = $GLOBALS['phpgw']->common->show_date($allocated['allocations'][$inventory['inventory_id']]['start_date'] ,$dateformat)
											. ' - ' . $GLOBALS['phpgw']->common->show_date($allocated['allocations'][$inventory['inventory_id']]['end_date'] ,$dateformat);
						}


					}
				}
			}

			$activity = $this->so_activity->get_single( $requirement->get_activity_id() );

			$data = array
			(
				'requirement' 				=> $requirement,
				'view_criterias_array' 		=> $view_criterias_array,
				'activity' 					=> $activity,
				'allocation_suggestions' 	=> $allocation_suggestions,
				'editable' 					=> true
			);

			self::render_template_xsl(array('allocation/book_resources'), $data);
		}

		public function save()
		{
			$requirement_id = phpgw::get_var('requirement_id');

			if($requirement_id && is_numeric($requirement_id))
			{
				$requirement = $this->so_requirement->get_single($requirement_id);
				$activity_id = $requirement->get_activity_id();
			}

			$user_id = $GLOBALS['phpgw_info']['user']['id'];
			$chosen_resources = phpgw::get_var('chosen_resources');
			
			$inventory_ids = phpgw::get_var('inventory_ids');
			$allocations = phpgw::get_var('allocations');
			//FIXME: Bruk 'allocation_id' i staden.

//_debug_array($inventory_ids_orig);die();
			$filters = array('requirement_id' => $requirement->get_id());
			$num_allocated = $this->so->get_count($search_for, $search_type, $filters);
							 
			$num_required = $requirement->get_no_of_items();

			$num_allowed_bookings = $num_required - $num_allocated;

			if($inventory_ids)
			{
				foreach ($inventory_ids as $resource => $allocated_amount)
				{
					if($allocated_amount)
					{
						$resource_arr = explode('_', $resource);
						$resource_id = $resource_arr[0];
						$inventory_id = $resource_arr[1];

						$resource_alloc = new logistic_requirement_resource_allocation();
						$resource_alloc->set_requirement_id( $requirement->get_id() );
						$resource_alloc->set_resource_id( $resource_id );
						$resource_alloc->set_inventory_id( $inventory_id );
						$resource_alloc->set_allocated_amount( $allocated_amount );
						$resource_alloc->set_id( $allocations[$resource] );
						$resource_alloc->set_location_id( $requirement->get_location_id() );
						$resource_alloc->set_create_user( $user_id );
						$resource_alloc->set_start_date( $requirement->get_start_date() );
						$resource_alloc->set_end_date( $requirement->get_start_date() );
						$resource_alloc_id = $this->so->store( $resource_alloc );

					}
				}

			}
			else if( count($chosen_resources) <=  $num_allowed_bookings)
			{
				foreach($chosen_resources as $resource_id)
				{
					$resource_alloc = new logistic_requirement_resource_allocation();
					$resource_alloc->set_requirement_id( $requirement->get_id() );
					$resource_alloc->set_resource_id( $resource_id );
					$resource_alloc->set_location_id( $requirement->get_location_id() );
					$resource_alloc->set_create_user( $user_id );
					$resource_alloc->set_start_date( $requirement->get_start_date() );
					$resource_alloc->set_end_date( $requirement->get_start_date() );

					$resource_alloc_id = $this->so->store( $resource_alloc );
				}

			}

			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'logistic.uiactivity.view_resource_allocation', 'activity_id' => $requirement->get_activity_id()));

		}

		public function delete()
		{
			if(!$this->delete)
			{
				return array( "status" => "not_deleted" );			
			}

			$resource_allocation_id = phpgw::get_var('id');

			$status = $this->so->delete($resource_allocation_id);

			if($status)
			{
				return json_encode( array( "status" => "deleted" ) );
			}
			else
			{
				return json_encode( array( "status" => "not_deleted" ) );
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
