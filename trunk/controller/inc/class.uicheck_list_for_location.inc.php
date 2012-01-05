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
										'add_check_list_for_location' 	=> true,
										'save_check_list_for_location' 	=> true,
										'edit_check_list_for_location' 	=> true,
										'create_error_report_message' 	=> true,
										'view_control_info' 			=> true
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
	
		function index()
		{
			if(phpgw::get_var('phpgw_return_as') == 'json') {
				return $this->query();
			}
			$building_types  = execMethod('property.soadmin_location.read',array());
			
			$type_id = 1;
			
			$category_types = $this->bocommon->select_category_list(array(
																		'format'=>'filter',
																		'selected' => $this->cat_id,
																		'type' =>'location',
																		'type_id' =>$type_id,
																		'order'=>'descr'
																	));
			
			$district_list  = $this->bocommon->select_district_list('filter',$this->district_id);
			$default_value = array ('id'=>'','name'=>lang('no district'));
			array_unshift($district_list,$default_value);
			
			$part_of_town_list =  $this->bocommon->select_part_of_town('filter',$this->part_of_town_id,$this->district_id);
			$default_value = array ('id'=>'','name'=>lang('no part of town'));
			array_unshift($part_of_town_list,$default_value);
			
			$_role_criteria = array
					(
						'type'		=> 'responsibility_role',
						'filter'	=> array('location' => ".location.{$type_id}"),
						'order'		=> 'name'
					);

			$responsibility_roles_list =   execMethod('property.sogeneric.get_list',$_role_criteria);
			$default_value = array ('id'=>'','name'=>lang('no role'));
			array_unshift ($responsibility_roles,$default_value);
			
			$control_areas_array = $this->so_control_area->get_control_areas_as_array();
			$controls_array = $this->so_control->get_controls_by_control_area($control_areas_array[0]['id']);
			$control_id = $control_areas_array[0]['id'];
			
			if($control_id == null)
				$control_id = 0;
			
			$tabs = array( array(
						'label' => lang('View_locations_for_control')
					), array(
						'label' => lang('Add_locations_for_control'),
						'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicheck_list_for_location.add_location_to_control'))
					));
			
			$data = array(
				'tabs'					=> $GLOBALS['phpgw']->common->create_tabs($tabs, 0),
				'view'					=> "view_locations_for_control",
				'control_area_array' 	=> $control_areas_array,
				'control_array'			=> $control_array,
				'locations_table' => array(
					'source' => self::link(array('menuaction' => 'controller.uicontrol.get_locations_for_control', 'control_id' => $control_id ,'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'id',
							'label' => lang('ControlId'),
							'sortable'	=> true,
						),
						array(
							'key'	=>	'title',
							'label'	=>	lang('Property name'),
							'sortable'	=>	false
						),
						array(
							'key' => 'location_code',
							'label' => lang('location_code'),
							'sortable'	=> false
						),
						array(
							'key' => 'loc1_name',
							'label' => lang('Location_name'),
							'sortable'	=> false
						),
						array(
							'key' => 'actions',
							'hidden' => true
						),
						array(
							'key' => 'labels',
							'hidden' => true
						),
						array(
							'key' => 'ajax',
							'hidden' => true
						),array(
							'key' => 'parameters',
							'hidden' => true
						)						
					)
				)
			);
			
			phpgwapi_yui::load_widget('paginator');
			
			self::add_javascript('controller', 'yahoo', 'control_tabs.js');
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'ajax.js');

			self::render_template_xsl(array('control_location_tabs', 'common', 'view_locations_for_control'), $data);		
		}
		
		function add_location_to_control()
		{
			if(phpgw::get_var('phpgw_return_as') == 'json') {
				return $this->query();
			}
			$building_types  = execMethod('property.soadmin_location.read',array());
			
			$type_id = 1;
			
			$category_types = $this->bocommon->select_category_list(array(
																		'format'=>'filter',
																		'selected' => $this->cat_id,
																		'type' =>'location',
																		'type_id' =>$type_id,
																		'order'=>'descr'
																	));
			
			$district_list  = $this->bocommon->select_district_list('filter',$this->district_id);
			$default_value = array ('id'=>'','name'=>lang('no district'));
			array_unshift($district_list,$default_value);
			
			$part_of_town_list =  $this->bocommon->select_part_of_town('filter',$this->part_of_town_id,$this->district_id);
			$default_value = array ('id'=>'','name'=>lang('no part of town'));
			array_unshift($part_of_town_list,$default_value);
			
			$_role_criteria = array
					(
						'type'		=> 'responsibility_role',
						'filter'	=> array('location' => ".location.{$type_id}"),
						'order'		=> 'name'
					);

			$responsibility_roles_list = execMethod('property.sogeneric.get_list',$_role_criteria);
			$default_value = array ('id'=>'','name'=>lang('no role'));
			array_unshift ($responsibility_roles,$default_value);
			
			$control_areas_array = $this->so_control_area->get_control_areas_as_array();
			
			$tabs = array( array(
						'label' => lang('View_locations_for_control'),
						'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicheck_list_for_location.index'))
			
					), array(
						'label' => lang('Add_locations_for_control')
					));
					
			$data = array(
				'tabs'						=> $GLOBALS['phpgw']->common->create_tabs($tabs, 1),
				'view'						=> "add_location_to_control",
				'control_filters'			=> array(
					'control_area_array' 		=> $control_areas_array,
					'control_array' 			=> $control_array
				),
				'filter_form' 				=> array(
					'building_types' 			=> $building_types,
					'category_types' 			=> $category_types,
					'district_list' 			=> $district_list,
					'part_of_town_list' 		=> $part_of_town_list
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'controller.uicheck_list_for_location.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'location_code',
							'label' => lang('Property'),
							'sortable'	=> true,
							'formatter' => 'YAHOO.portico.formatLink'
						),
						array(
							'key'	=>	'loc1_name',
							'label'	=>	lang('Property name'),
							'sortable'	=>	false
						),
						array(
							'key' => 'adresse1',
							'label' => lang('Address'),
							'sortable'	=> false
						),
						array(
							'key' => 'postnummer',
							'label' => lang('Zip code'),
							'sortable'	=> false
						),
						array(
							'key' => 'actions',
							'hidden' => true
						),
						array(
							'key' => 'labels',
							'hidden' => true
						),
						array(
							'key' => 'ajax',
							'hidden' => true
						),array(
							'key' => 'parameters',
							'hidden' => true
						)						
					)
				)
			);
			
			phpgwapi_yui::load_widget('paginator');
			
			self::add_javascript('controller', 'yahoo', 'control_tabs.js');
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'ajax.js');

			self::render_template_xsl(array('control_location_tabs', 'common', 'add_location_to_control'), $data);		
		}
		
		function add_check_list_for_location(){
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
			
			self::render_template_xsl(array('add_check_list_for_location'), $data);
		}
		
		function edit_check_list_for_location(){
			$check_list_id = phpgw::get_var('check_list_id');
			
			$check_list = $this->so_check_list->get_single($check_list_id);
			
			// Fetches with check items
			$open_check_items = $this->so_check_item->get_check_items($check_list_id, 'open', 'control_item_type_1');

			// Fetches check list with check items
			$handled_check_items = $this->so_check_item->get_check_items($check_list_id, 'handled', 'control_item_type_1');
						
			$location_code = $check_list->get_location_code();
				
			// Fetches all control items for check list
			$control_items_for_check_list = $this->so_control_item->get_control_items_by_control_id($check_list->get_control_id());
			
			// Fetches check items that registeres measurement
			$measurement_check_items = $this->so_check_item->get_check_items($check_list_id, null, 'control_item_type_2');
						
			// Puts ids for control items that is registered as open check item in an array   
			$control_item_ids = array();
			foreach($open_check_items as $check_item){
				$control_item_ids[] = $check_item["control_item_id"];
			}
			
			// Puts ids for control items that is registered as handled check item in an array   
			foreach($handled_check_items as $check_item){
				$control_item_ids[] = $check_item["control_item_id"];
			}
			
			// Puts ids for control items that is registered check item measurements in an array   
			foreach($measurement_check_items as $check_item){
				$control_item_ids[] = $check_item["control_item_id"];
			}
			
			// Puts control items not registered as check item in an array
			$control_items_not_registered = array();
			foreach($control_items_for_check_list as $control_item){
				if( !in_array($control_item->get_id(), $control_item_ids) ){
					$control_items_not_registered[] = $control_item->toArray();
				}
			}

			$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
	
			$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
			
			$data = array
			(
				'check_list' 					=> $check_list->toArray(),
				'open_check_items' 				=> $open_check_items,
				'handled_check_items' 			=> $handled_check_items,
				'measurement_check_items' 		=> $measurement_check_items,
				'control_items_not_registered' 	=> $control_items_not_registered,
				'location_array'				=> $location_array,
				'date_format' 					=> $date_format
			);
			
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'jquery-ui.custom.min.js');
			self::add_javascript('controller', 'controller', 'custom_ui.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
			
			$GLOBALS['phpgw']->css->add_external_file('controller/templates/base/css/jquery-ui.custom.css');
			
			self::render_template_xsl('edit_check_list', $data);
		}
		
		function save_check_list_for_location(){
			$location_code = phpgw::get_var('location_code');
			$control_id = phpgw::get_var('control_id');
			$status = phpgw::get_var('status');
					
			$planned_date = phpgw::get_var('planned_date', 'string');
			$completed_date = phpgw::get_var('completed_date', 'string');
			$deadline_date = phpgw::get_var('deadline_date', 'string');
						
			$planned_date_ts = $this->get_timestamp_from_date( $planned_date ); 
			$deadline_date_ts = $this->get_timestamp_from_date( $deadline_date );
			
			$check_list = new controller_check_list();
			$check_list->set_location_code($location_code);
			$check_list->set_control_id($control_id);
			$check_list->set_status($status);
			$check_list->set_deadline( $deadline_date_ts );
			$check_list->set_planned_date($planned_date_ts);
			$check_list->set_completed_date($completed_date);
			
			$check_list_id = $this->so_check_list->add($check_list);
			
			$this->redirect(array('menuaction' => 'controller.uicheck_list_for_location.edit_check_list_for_location', 'check_list_id'=>$check_list_id));
		}
		
		function create_error_report_message(){
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
			
			self::render_template_xsl('create_error_report_message', $data);
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
			
			self::render_template_xsl('view_control_info', $data);
		}
		
		function get_timestamp_from_date( $date_string ){
			$pos_day = strpos($date_string, "/"); 
			$day =  substr($date_string, 0, $pos_day);
			
			$pos_month = strpos($date_string, "-");
			$len_month = $pos_month - $pos_day -1;
			$month = substr($date_string, $pos_day+1, $len_month);
			
			$year = substr($date_string, $pos_month + $len_month-1, strlen($date_string)-1);
			
			return mktime(0, 0, 0, $month, $day, $year);
		}
		
		public function query(){
			$type_id = phpgw::get_var('type_id');
			$return_results	= phpgw::get_var('results', 'int', 'REQUEST', 0);
			
			$type_id = $type_id ? $type_id : 1;
			
			$location_list = array();

			$this->bo->sort = "ASC";
			$this->bo->start = phpgw::get_var('startIndex');
			
			$location_list = $this->bo->read(array('user_id' => $user_id, 'role_id' =>$role_id, 'type_id'=>$type_id,'lookup_tenant'=>$lookup_tenant,
												   'lookup'=>$lookup,'allrows'=>$this->allrows,'dry_run' =>$dry_run));

			$rows_total = $this->bo->read(array('type_id' => $type_id, 'allrows' => true));
			
			foreach($location_list as $location)
			{
				$results['results'][]= $location;	
			}
			
			$results['total_records'] = count($rows_total);
			$results['start'] = $this->start;
			$results['sort'] = 'location_code';
			$results['dir'] = "ASC";
						
			array_walk($results['results'], array($this, 'add_actions'), array($type));
							
			return $this->yui_results($results);
		}
			
		public function add_actions(&$value, $key, $params)
		{
			unset($value['query_location']);
			
			$value['ajax'] = array();
			$value['actions'] = array();
			$value['labels'] = array();
			$value['parameters'] = array();
			
			$value['ajax'][] = true;
			$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'controller.uicontrol.add_location_to_control', 'location_code' => $value['location_code'])));
			$value['labels'][] = lang('add_location');
			$value['parameters'][] = "control_id";
		}
	}
