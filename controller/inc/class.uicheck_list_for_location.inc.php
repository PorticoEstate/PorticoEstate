<?php
	/**
	* phpGroupWare - controller: a part of a Facilities Management System.
	*
	* @author Erink Holm-Larsen <erik.holm-larsen@bouvet.no>
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
	
	include_class('controller', 'check_list', 'inc/model/');
	include_class('controller', 'date_generator', 'inc/component/');
	include_class('controller', 'status_checker', 'inc/helper/');
	include_class('controller', 'date_helper', 'inc/helper/');
		
	class controller_uicheck_list_for_location extends controller_uicommon
	{
		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;
		var $currentapp;
		var $type_id;
		var $location_code;
		
		private $so_control_area;
		private $so_control;
		private $so_check_list;
		private $so_control_item;
		private $so_check_item;
		private $so_procedure;
	
		var $public_functions = array(
										'index' => true,
										'view_locations_for_control' 	=> true,
										'add_location_to_control' 		=> true,
										'add_check_list' 				=> true,
										'save_check_list' 				=> true,
										'edit_check_list' 				=> true,
										'create_case_message' 			=> true,
										'view_control_info' 			=> true,
										'view_cases_for_check_list'		=> true
									);

		function __construct()
		{
			parent::__construct();
			
			$this->bo					= CreateObject('property.bolocation',true);
			$this->bocommon				= & $this->bo->bocommon;
			$this->so_control_area 		= CreateObject('controller.socontrol_area');
			$this->so_control 			= CreateObject('controller.socontrol');
			$this->so_check_list		= CreateObject('controller.socheck_list');
			$this->so_control_item		= CreateObject('controller.socontrol_item');
			$this->so_check_item		= CreateObject('controller.socheck_item');
			$this->so_procedure			= CreateObject('controller.soprocedure');
			
			$this->type_id				= $this->bo->type_id;
			
			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->filter				= $this->bo->filter;
			$this->cat_id				= $this->bo->cat_id;
			$this->part_of_town_id		= $this->bo->part_of_town_id;
			$this->district_id			= $this->bo->district_id;
			$this->status				= $this->bo->status;
			$this->allrows				= $this->bo->allrows;
			$this->lookup				= $this->bo->lookup;
			$this->location_code		= $this->bo->location_code;
			
			self::set_active_menu('controller::control::location_for_check_list');
		}	
	
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
				'control_array'		=> $control->toArray(),
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
		
		function edit_check_list(){
			$check_list_id = phpgw::get_var('check_list_id');
			
			$check_list = $this->so_check_list->get_single($check_list_id);
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
		
		function view_cases_for_check_list(){
			$check_list_id = phpgw::get_var('check_list_id');
			
			$check_list = $this->so_check_list->get_single($check_list_id);
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
		
		function save_check_list(){
			$location_code = phpgw::get_var('location_code');
			$control_id = phpgw::get_var('control_id');
			$status = (int)phpgw::get_var('status');

			$deadline_date = phpgw::get_var('deadline_date', 'string');
			$planned_date = phpgw::get_var('planned_date', 'string');
			$completed_date = phpgw::get_var('completed_date', 'string');
							
			if($planned_date != '')
				$planned_date_ts = date_helper::get_timestamp_from_date( $planned_date );

			if($deadline_date != '')
				$deadline_date_ts = date_helper::get_timestamp_from_date( $deadline_date );
			
			if($completed_date != '')
				$completed_date_ts = date_helper::get_timestamp_from_date( $completed_date );
			
			$check_list = new controller_check_list();
			$check_list->set_location_code($location_code);
			$check_list->set_control_id($control_id);
			$check_list->set_status($status);
			$check_list->set_deadline( $deadline_date_ts );
			$check_list->set_planned_date($planned_date_ts);
			$check_list->set_completed_date($completed_date_ts);
			
			$check_list_id = $this->so_check_list->store($check_list);
			
			$this->redirect(array('menuaction' => 'controller.uicheck_list_for_location.edit_check_list', 'check_list_id'=>$check_list_id));
		}
		
		function create_case_message(){
			$check_list_id = phpgw::get_var('check_list_id');
						
			$check_list_with_check_items = $this->so_check_list->get_single_with_check_items($check_list_id);
						
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
		
		public function view_control_info(){
			$check_list_id = phpgw::get_var('check_list_id');
			
			$check_list = $this->so_check_list->get_single($check_list_id);
			$control = $this->so_control->get_single($check_list->get_control_id());
			
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

			// Fetches prosedures that are related to first control area in list
			$control_area_id = $control_areas_array2[1]['id'];
			$procedures_array = $this->so_procedure->get_procedures_by_control_area_id($control_area_id);
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
		
		public function query(){}			
	}
