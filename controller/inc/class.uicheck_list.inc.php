<?php
	/**
	* phpGroupWare - controller: a part of a Facilities Management System.
	*
	* @author Erik Holm-Larsen <erik.holm-larsen@bouvet.no>
	* @author Torstein Vadla <torstein.vadla@bouvet.no>
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
	* @subpackage controller
 	* @version $Id$
	*/
	
	phpgw::import_class('phpgwapi.yui');
	phpgw::import_class('controller.uicommon');
	phpgw::import_class('controller.socontrol_area');
	phpgw::import_class('controller.socheck_list');
	
	include_class('controller', 'check_list', 'inc/model/');
	include_class('controller', 'check_item', 'inc/model/');
	include_class('controller', 'date_generator', 'inc/component/');
	include_class('controller', 'status_checker', 'inc/helper/');
	include_class('controller', 'date_helper', 'inc/helper/');
	
	class controller_uicheck_list extends controller_uicommon
	{
		private $so;
		private $so_control_area;
		private $so_control;
		private $so_control_item;
		private $so_check_item;
		private $so_procedure;
		private $so_control_group_list;
		private $so_control_group;
		private $so_control_item_list;
	
		var $public_functions = array(
										'index' 						=> true,
										'add_check_list' 				=> true,
										'save_check_list' 				=> true,
										'edit_check_list' 				=> true,
										'create_case_message' 			=> true,
										'view_control_info' 			=> true,
										'view_cases_for_check_list'		=> true,
										'update_check_list'				=> true,
										'print_check_list'				=> true,
										'register_case'					=> true,
										'view_open_cases'				=> true,
										'view_closed_cases'				=> true,
										'view_measurements'				=> true,
										'view_control_details'			=> true,
										'view_control_items'			=> true,
										'add_check_item_to_list'		=> true,
										'save_check_items'				=> true,
										'save_check_item'				=> true,
										'get_check_list_info'			=> true,
										'get_cases_for_check_list'		=> true
									);

		function __construct()
		{
			parent::__construct();

			$this->so_control_area 		= CreateObject('controller.socontrol_area');
			$this->so_control 			= CreateObject('controller.socontrol');
			$this->so					= CreateObject('controller.socheck_list');
			$this->so_control_item		= CreateObject('controller.socontrol_item');
			$this->so_check_item		= CreateObject('controller.socheck_item');
			$this->so_procedure			= CreateObject('controller.soprocedure');
			$this->so_control_group_list = CreateObject('controller.socontrol_group_list');
			$this->so_control_group		= CreateObject('controller.socontrol_group');
			$this->so_control_item_list = CreateObject('controller.socontrol_item_list');

			self::set_active_menu('controller::control::check_list');
		}	
		
		/**
		 * Public function for displaying checklists  
		 * 
		 * @param HTTP:: phpgw_return_as
		 * @return data array
		*/
		public function index()
		{
			if(phpgw::get_var('phpgw_return_as') == 'json') {
				return $this->query();
			}
			self::add_javascript('controller', 'yahoo', 'datatable.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');

			$data = array(
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array('type' => 'filter', 
								'name' => 'status',
								'text' => lang('Status'),
								'list' => array(
									array(
										'id' => 'none',
										'name' => lang('Not selected')
									), 
									array(
										'id' => 'NEW',
										'name' => lang('NEW')
									), 
									array(
										'id' => 'PENDING',
										'name' =>  lang('PENDING')
									), 
									array(
										'id' => 'REJECTED',
										'name' => lang('REJECTED')
									), 
									array(
										'id' => 'ACCEPTED',
										'name' => lang('ACCEPTED')
									)
								)
							),
							array('type' => 'text', 
								'text' => lang('searchfield'),
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
					'source' => self::link(array('menuaction' => 'controller.uicheck_list.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'id',
							'label' => lang('ID'),
							'sortable'	=> true,
							'formatter' => 'YAHOO.portico.formatLink'
						),
						array(
							'key'	=>	'title',
							'label'	=>	lang('Control title'),
							'sortable'	=>	false
						),
						array(
							'key' => 'start_date',
							'label' => lang('start_date'),
							'sortable'	=> false
						),
						array(
							'key' => 'planned_date',
							'label' => lang('planned_date'),
							'sortable'	=> false
						),
						array(
							'key' => 'end_date',
							'label' => lang('end_date'),
							'sortable'	=> false
						),
						array(
							'key' => 'link',
							'hidden' => true
						)
					)
				),
			);

			self::render_template_xsl('datatable', $data);
		}
		
		/**
		 * Public function for displaying the add check list form  
		 * 
		 * @param HTTP:: location code, control id, date
		 * @return data array
		*/
		function add_check_list(){
			$location_code = phpgw::get_var('location_code');
			$control_id = phpgw::get_var('control_id');
			$date = phpgw::get_var('date');
			
			$control = $this->so_control->get_single($control_id);
			
			if($date == null || $date == ''){
				$todays_date = mktime(0,0,0, date("m"), date("d"), date("Y"));
				$period_start_date = $todays_date;
				
				if( $control->get_repeat_type() == 1 )
				{
					$period_end_date = mktime(0,0,0, date("m")+1, date("d"), date("Y"));
				}else if( $control->get_repeat_type() == 2 )
				{
					$period_end_date = mktime(0,0,0, date("m"), date("d"), date("Y") + 1);
				}else if( $control->get_repeat_type() == 3 )
				{
					$period_end_date = mktime(0,0,0, date("m"), date("d"), date("Y") + $control->get_repeat_interval());				
				}
				
				$date_generator = new date_generator($control->get_start_date(), $control->get_end_date(), $period_start_date, $period_end_date, $control->get_repeat_type(), $control->get_repeat_interval());
							
				$calendar_array = $date_generator->get_dates();
			}
			else
			{
				$calendar_array[] = $date;
			}			

			$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
			
			$data = array
			(
				'location_array'	=> $location_array,
				'control'			=> $control->toArray(),
				'deadline'			=> $calendar_array[0],
				'date_format' 		=> $date_format			
			);
			
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'custom_ui.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
			self::add_javascript('controller', 'controller', 'jquery-ui.custom.min.js');
			
			$GLOBALS['phpgw']->css->add_external_file('controller/templates/base/css/jquery-ui.custom.css');
			
			self::render_template_xsl(array('check_list/check_list_tab_menu','check_list/add_check_list'), $data);
		}
		
		/**
		 * Public function for displaying the edit check list form  
		 * 
		 * @param HTTP:: check list id
		 * @return data array
		*/
		function edit_check_list(){
			$check_list_id = phpgw::get_var('check_list_id');

			$status_checker = new status_checker();
			$status_checker->update_check_list_status( $check_list_id );
				
			$check_list = $this->so->get_single($check_list_id);
			
			$control = $this->so_control->get_single($check_list->get_control_id());
			
			$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$location_code = $check_list->get_location_code();
	
			$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
						
			$data = array
			(
				'control' 			=> $control->toArray(),
				'check_list' 		=> $check_list->toArray(),
				'location_array'	=> $location_array,
				'date_format' 		=> $date_format
			);
			
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'jquery-ui.custom.min.js');
			self::add_javascript('controller', 'controller', 'custom_ui.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
			
			$GLOBALS['phpgw']->css->add_external_file('controller/templates/base/css/jquery-ui.custom.css');
			
			self::render_template_xsl(array('check_list/check_list_tab_menu','check_list/edit_check_list'), $data);
		}
		
		/**
		 * Public function for displaying the edit check list form
		 * 
		 * @param HTTP:: check list id
		 * @return data array
		*/
		function view_cases_for_check_list(){
			$check_list_id = phpgw::get_var('check_list_id');
			
			$check_list = $this->so->get_single($check_list_id);
			$control = $this->so_control->get_single($check_list->get_control_id());
				
			$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$location_code = $check_list->get_location_code();
	
			$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
			
			$data = array
			(
				'control' 			=> $control->toArray(),
				'check_list' 		=> $check_list->toArray(),
				'location_array'	=> $location_array,
				'date_format' 		=> $date_format
			);
			
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'jquery-ui.custom.min.js');
			self::add_javascript('controller', 'controller', 'custom_ui.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
			
			$GLOBALS['phpgw']->css->add_external_file('controller/templates/base/css/jquery-ui.custom.css');
			
			self::render_template_xsl(array('check_list/check_list_tab_menu', 'check_list/view_cases_for_check_list'), $data);
		}
		
		/**
		 * Public function for saving a check list
		 * 
		 * @param HTTP:: location code, control id, status etc.. (check list details) 
		 * @return data array
		*/
		function save_check_list(){
			$location_code = phpgw::get_var('location_code');
			$control_id = phpgw::get_var('control_id');
			$status = (int)phpgw::get_var('status');

			$deadline_date = phpgw::get_var('deadline_date', 'string');
			$planned_date = phpgw::get_var('planned_date', 'string');
			$completed_date = phpgw::get_var('completed_date', 'string');
			
			$comment = phpgw::get_var('comment', 'string');
							
			if($planned_date != '')
				$planned_date_ts = date_helper::get_timestamp_from_date( $planned_date, "d/m-Y" );

			if($deadline_date != '')
				$deadline_date_ts = date_helper::get_timestamp_from_date( $deadline_date, "d/m-Y" );
			
			if($completed_date != '')
				$completed_date_ts = date_helper::get_timestamp_from_date( $completed_date, "d/m-Y" );
			
			$check_list = new controller_check_list();
			$check_list->set_location_code($location_code);
			$check_list->set_control_id($control_id);
			$check_list->set_status($status);
			$check_list->set_comment($comment);
			$check_list->set_deadline( $deadline_date_ts );
			$check_list->set_planned_date($planned_date_ts);
			$check_list->set_completed_date($completed_date_ts);
			
			$check_list_id = $this->so->store($check_list);
			
			$this->redirect(array('menuaction' => 'controller.uicheck_list.edit_check_list', 'check_list_id'=>$check_list_id));
		}
		
		/**
		 * Public function for displaying the create message form
		 * 
		 * @param HTTP:: check list id 
		 * @return data array
		*/
		function create_case_message(){
			$check_list_id = phpgw::get_var('check_list_id');
						
			$check_list_with_check_items = $this->so->get_single_with_check_items($check_list_id);
						
			$control_id = $check_list_with_check_items["control_id"];
			$control = $this->so_control->get_single( $control_id );
			
			$location_code = $check_list_with_check_items["location_code"];  
				 
			$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
	
			$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
			
			$data = array
			(
				'location_array'		=> $location_array,
				'control_array'			=> $control->toArray(),
				'check_list' 			=> $check_list_with_check_items,
				'date_format' 			=> $date_format
			);
			
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'jquery-ui.custom.min.js');
			self::add_javascript('controller', 'controller', 'custom_ui.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
			
			$GLOBALS['phpgw']->css->add_external_file('controller/templates/base/css/jquery-ui.custom.css');
			
			self::render_template_xsl('create_case_messsage', $data);
		}
		
		/**
		 * Public function for displaying the create message form
		 * 
		 * @param HTTP:: check list id 
		 * @return data array
		*/
		public function view_control_info(){
			$check_list_id = phpgw::get_var('check_list_id');
			
			$check_list = $this->so->get_single($check_list_id);
			$control = $this->so_control->get_single($check_list->get_control_id());
			
			$cats = CreateObject('phpgwapi.categories', -1, 'controller', '.control');
			$cats->supress_info	= true;
			
			$control_areas = $cats->formatted_xslt_list(array('format'=>'filter','selected' => $control_area_id,'globals' => true,'use_acl' => $this->_category_acl));
			array_unshift($control_areas['cat_list'],array ('cat_id'=>'','name'=> lang('select value')));
			$control_areas_array2 = array();
			
			foreach($control_areas['cat_list'] as $cat_list)
			{
				$control_areas_array2[] = array
				(
					'id' 	=> $cat_list['cat_id'],
					'name'	=> $cat_list['name'],
				);		
			}

			// Fetches prosedures that are related to first control area in list
			$control_area_id = $control_areas_array2[1]['id'];
			$procedures_array = $this->so_procedure->get_procedures_by_control_area($control_area_id);
			$role_array = $this->so_control->get_roles();
			
			$location_code = $check_list->get_location_code();  
			$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
			
			$data = array
			(
				'location_array'		=> $location_array,
				'control'				=> $control->toArray(),
				'check_list'			=> $check_list->toArray(),
				'date_format' 			=> $date_format,
				'control_areas_array2'	=> array('options' => $control_areas_array2),
				'procedures_array'		=> $procedures_array,
				'role_array'			=> $role_array
			);

			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'jquery-ui.custom.min.js');
			
			self::render_template_xsl(array('check_list/check_list_tab_menu','check_list/view_control_info'), $data);
		}
		
		public function update_check_list(){
			$check_list_id = phpgw::get_var('check_list_id');
			$status = (int)phpgw::get_var('status');
			$comment = phpgw::get_var('comment');
			$deadline_date = phpgw::get_var('deadline_date');
			$completed_date = phpgw::get_var('completed_date');
			$planned_date = phpgw::get_var('planned_date');

			if($planned_date != ''){
				$planned_date_ts = date_helper::get_timestamp_from_date( $planned_date, "d/m-Y" );
			}else{
				$planned_date_ts = 0;
			} 
			
			if($completed_date != ''){
				$completed_date_ts = date_helper::get_timestamp_from_date( $completed_date, "d/m-Y" );
			}else{
				$completed_date_ts = 0;
			}
			
			// Fetches check_list from DB
			$update_check_list = $this->so->get_single($check_list_id);
			$update_check_list->set_status( $status );
			$update_check_list->set_comment( $comment );
			$update_check_list->set_completed_date( $completed_date_ts );
			$update_check_list->set_planned_date( $planned_date_ts );

			$check_list_id = $this->so->update( $update_check_list );
			
			if($check_list_id > 0)
				return json_encode( array( "status" => "updated" ) );
			else
				return json_encode( array( "status" => "not_updated" ) );
		}
		
		public function print_check_list(){
			$check_list_id = phpgw::get_var('check_list_id');
			$check_list = $this->so->get_single($check_list_id);
			
			$control = $this->so_control->get_single($check_list->get_control_id());
			$control_groups = $this->so_control_group_list->get_control_groups_by_control($control->get_id());
			
			$saved_groups_with_items_array = array();
			
			//Populating array with saved control items for each group
			foreach ($control_groups as $control_group)
			{	
				$saved_control_items = $this->so_control_item_list->get_control_items_by_control_and_group($control->get_id(), $control_group->get_id());
				
				$control_item = $this->so_control_item->get_single($control_item_id);
				
				$saved_groups_with_items_array[] = array("control_group" => $control_group->toArray(), "control_items" => $saved_control_items);
			}
			
			$data = array
			(
				'saved_groups_with_items_array'	=> $saved_groups_with_items_array,
				'check_list'					=> $check_list->toArray()
			);
			
			self::render_template_xsl('check_list/print_check_list', $data);
		}
		
		function view_control_details(){
			$control_id = phpgw::get_var('control_id');
			
			$control = $this->so_control->get_single($control_id);
			
			// Sigurd: START as categories
			$cats	= CreateObject('phpgwapi.categories', -1, 'controller', '.control');
			$cats->supress_info	= true;

			$control_areas = $cats->formatted_xslt_list(array('format'=>'filter','selected' => $control_area_id,'globals' => true,'use_acl' => $this->_category_acl));
			array_unshift($control_areas['cat_list'],array ('cat_id'=>'','name'=> lang('select value')));
			$control_areas_array2 = array();
			foreach($control_areas['cat_list'] as $cat_list)
			{
				$control_areas_array2[] = array
				(
					'id' 	=> $cat_list['cat_id'],
					'name'	=> $cat_list['name'],
				);		
			}
			// END as categories
			$control_area_id = $control_areas_array2[1]['id'];
			$procedures_array = $this->so_procedure->get_procedures_by_control_area($control_area_id);
			$role_array = $this->so_control->get_roles();
			
			$data = array
			(
				'control'	=> $control->toArray(),
				'procedures_array'			=> $procedures_array,
				'role_array'				=> $role_array
			);
			
			self::render_template_xsl('check_list/view_control_details', $data);
		}
						
		// Function that displays control groups and control items for a check list
		function register_case(){
			$check_list_id = phpgw::get_var('check_list_id');
			
			$check_list = $this->so->get_single($check_list_id);
			$control = $this->so_control->get_single($check_list->get_control_id());			
			
			$saved_control_groups = $this->so_control_group_list->get_control_groups_by_control($control->get_id());
		
			$control_groups_with_items_array = array();
			
			//Populating array with saved control items for each group
			foreach ($saved_control_groups as $control_group)
			{	
				$saved_control_items = $this->so_control_item_list->get_control_items_by_control_and_group($control->get_id(), $control_group->get_id());
				
				if(count($saved_control_items) > 0)				
					$control_groups_with_items_array[] = array("control_group" => $control_group->toArray(), "control_items" => $saved_control_items);
			}
			
			/* ================  Ikke slett!!! Kode som henter ut  utstyr basert på lokasjon  ==================       
			
			//get control items based on control group/component connection
			$control_groups_for_control = $this->so_control_group->get_control_group_ids_for_control($control->get_id());
			//_debug_array($control_groups_for_control);

			foreach($control_groups_for_control as $cg)
			{
				$components_for_control_group[] = array($cg => $this->so_control_group->get_components_for_control_group($cg));
			}
			
			//_debug_array($components_for_control_group);
			
			$control_group_check_items = array();
			foreach($components_for_control_group as $cg_components)
			{
				foreach($control_groups_for_control as $cg_control)
				{
					$components = $cg_components[$cg_control];
					//_debug_array($components);
					$location_has_component = false;
					foreach($components as $comp)
					{
						if(!$location_has_component)
						{
							//check if current location has component
							$location_has_component = $this->so_control_item->location_has_component($comp, $check_list->get_location_code);
						}
					}
					if($location_has_component)
					{
						//the check items for the control group shall be added
						$check_items = $this->so_control_item->get_items_for_control_group($control->get_id(), $cg_control);
						$control_group_check_items[] = $check_items;
					}
				}
			}
			=====================================================================*/
			//_debug_array($control_group_check_items);
					
			$location_array = execMethod( 'property.bolocation.read_single', array('location_code' => $check_list->get_location_code()) );
			
			$data = array
			(
				'control' 							=> $control->toArray(),
				'check_list' 						=> $check_list->toArray(),
				'location_array'					=> $location_array,
				'control_groups_with_items_array' 	=> $control_groups_with_items_array
			);
			
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'custom_ui.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
			
			self::render_template_xsl(array('check_list/check_list_tab_menu', 'check_list/register_case'), $data);
		}
		
		function view_open_cases(){
			$check_list_id = phpgw::get_var('check_list_id');
			
			$check_list = $this->so->get_single($check_list_id);
			
			$open_check_items_and_cases = $this->so_check_item->get_check_items_with_cases($check_list_id, "control_item_type_1", 'open', null, 'return_array');
			$open_check_items_and_measurements = $this->so_check_item->get_check_items_with_cases($check_list_id, "control_item_type_2", 'open_or_waiting', null, 'return_array');
			
			$data = array
			(
				'open_check_items_and_cases'		=> $open_check_items_and_cases,
				'open_check_items_and_measurements'	=> $open_check_items_and_measurements,
				'check_list' 						=> $check_list->toArray()
			);
			
			self::render_template_xsl( array('check_list/cases_tab_menu', 'check_list/view_open_cases'), $data );			
		}
		
		function view_closed_cases(){
			$check_list_id = phpgw::get_var('check_list_id');
			
			$check_list = $this->so->get_single($check_list_id);
			
			$closed_check_items_and_cases = $this->so_check_item->get_check_items_with_cases($check_list_id, "control_item_type_1", 'closed', null, 'return_array');
			$closed_check_items_and_measurements = $this->so_check_item->get_check_items_with_cases($check_list_id, "control_item_type_2", 'closed', null, 'return_array');

			//echo "SKRIVER UT LUKKEDE SAKER";
			//print_r($closed_check_items_and_cases);
			
			$data = array
			(
				'closed_check_items_and_cases'			=> $closed_check_items_and_cases,
				'closed_check_items_and_measurements'	=> $closed_check_items_and_measurements,
				'check_list' 							=> $check_list->toArray()
			);
			
			self::render_template_xsl( array('check_list/cases_tab_menu', 'check_list/view_closed_cases'), $data );
		}
		
		function view_control_items(){
			$check_list_id = phpgw::get_var('check_list_id');
			$check_list = $this->so->get_single($check_list_id);
						
			$control = $this->so_control->get_single($check_list->get_control_id());
			$control_groups = $this->so_control_group_list->get_control_groups_by_control($control->get_id());
			
			$saved_groups_with_items_array = array();
			
			//Populating array with saved control items for each group
			foreach ($control_groups as $control_group)
			{	
				$saved_control_items = $this->so_control_item_list->get_control_items_by_control_and_group($control->get_id(), $control_group->get_id());
				
				$control_item = $this->so_control_item->get_single($control_item_id);
				
				$saved_groups_with_items_array[] = array("control_group" => $control_group->toArray(), "control_items" => $saved_control_items);
			}
			
			$data = array
			(
				'saved_groups_with_items_array'	=> $saved_groups_with_items_array,
				'check_list'					=> $check_list->toArray()
			);
			
			self::render_template_xsl('check_list/view_control_items', $data);
		}
		
		function add_check_item_to_list(){
			$control_item_id = phpgw::get_var('control_item_id');
			$check_list_id = phpgw::get_var('check_list_id');
			$comment = phpgw::get_var('comment');
			$status = (int)phpgw::get_var('status');
			$type = phpgw::get_var('type');

			$check_item_obj = new controller_check_item();
			$check_item_obj->set_status($status);
			$check_item_obj->set_comment($comment);
			$check_item_obj->set_check_list_id($check_list_id);
			$check_item_obj->set_control_item_id($control_item_id);

			if($type == 'control_item_type_2'){
				$measurement = phpgw::get_var('measurement');
				$check_item_obj->set_measurement($measurement);
			}
			
			$check_item_id = $this->so_check_item->store( $check_item_obj );

			if($check_item_id > 0)
				return json_encode( array( "status" => "saved" ) );
			else
				return json_encode( array( "status" => "not_saved" ) );
		}
		
		public function save_check_items(){
			$check_item_ids = phpgw::get_var('check_item_ids');
			$check_list_id = phpgw::get_var('check_list_id');

			foreach($check_item_ids as $check_item_id){
				$status = phpgw::get_var('status_' . $check_item_id);
				$comment = phpgw::get_var('comment_' . $check_item_id);

				$check_item = $this->so_check_item->get_single($check_item_id);

				$check_item->set_status( $status );
				$check_item->set_comment( $comment );

				$this->so_check_item->store( $check_item );
			}

			$this->redirect(array('menuaction' => 'controller.uicheck_list_for_location.view_check_list', 'check_list_id'=>$check_list_id));
		}
		
		public function save_check_item(){
			$check_item_id = phpgw::get_var('check_item_id');
			$comment = phpgw::get_var('comment');
			$status = (int)phpgw::get_var('status');
									
			$check_item = $this->so_check_item->get_single($check_item_id);
			$control_item_id = $check_item->get_control_item_id();
			
			$control_item = $this->so_control_item->get_single($check_item->get_control_item_id());
			
			if($control_item->get_type() == 'control_item_type_2')
			{
				$measurement = phpgw::get_var('measurement');
				$check_item->set_measurement( $measurement );	
			}
			
			$check_item->set_status( $status );
			$check_item->set_comment( $comment );
			
			$check_item_id = $this->so_check_item->store( $check_item );

			if($check_item_id > 0){
				$status_checker = new status_checker();
				$status_checker->update_check_list_status( $check_item->get_check_list_id() );
				
				return json_encode( array( "status" => "saved" ) );
			}
			else
				return json_encode( array( "status" => "not_saved" ) );
		}
		
		// Returns check list info as JSON
		public function get_check_list_info()
		{
			$check_list_id = phpgw::get_var('check_list_id');
			$check_list = $this->so_check_list->get_single_with_check_items($check_list_id, "open");
			
			return json_encode( $check_list );
		}
		
		public function get_cases_for_check_list()
		{
			$check_list_id = phpgw::get_var('check_list_id');

			$check_items_with_cases = $this->so_check_item->get_check_items_with_cases($check_list_id, null, "open", null, "return_array");
			
			return json_encode( $check_items_with_cases );
		}
		
		public function query(){}
	}
