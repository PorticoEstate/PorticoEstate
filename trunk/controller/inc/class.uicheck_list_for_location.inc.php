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
	include_class('controller', 'date_generator', 'inc/components/');
		
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
	
		var $public_functions = array(
										'index' => true,
										'view_locations_for_control' => true,
										'add_location_to_control' => true,
										'add_check_list_for_location' => true,
										'save_check_list_for_location' => true,
										'edit_check_list_for_location' => true
									);

		function __construct()
		{
			parent::__construct();
			
			$this->bo					= CreateObject('property.bolocation',true);
			$this->bocommon				= & $this->bo->bocommon;
			$this->so_control_area 		= CreateObject('controller.socontrol_area');
			$this->so_control 			= CreateObject('controller.socontrol');
			$this->so_check_list		= CreateObject('controller.socheck_list');
			
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
			
			$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
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
						
			$data = array
			(
				'location_array'	=> $location_array,
				'control_array'		=> $control->toArray(),
				'calendar_array'	=> $calendar_array
				'date_format' 		=> $date_format			
			);
			
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'custom_ui.js');
			self::add_javascript('controller', 'controller', 'jquery-ui.custom.min.js');
			
			$GLOBALS['phpgw']->css->add_external_file('controller/templates/base/css/jquery-ui.custom.css');
			
			self::render_template_xsl(array('add_check_list_for_location'), $data);
		}
		
		function edit_check_list_for_location(){
			$check_list_id = phpgw::get_var('check_list_id');
			
			$check_list = $this->so_check_list->get_single_with_check_items($check_list_id);
			
			$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
	
			$data = array
			(
				'check_list' 		=> $check_list,
				'date_format' 		=> $date_format
			);
	
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'jquery-ui.custom.min.js');
			self::add_javascript('controller', 'controller', 'custom_ui.js');
			
			$GLOBALS['phpgw']->css->add_external_file('controller/templates/base/css/jquery-ui.custom.css');
			
			self::render_template_xsl('edit_check_list', $data);
		}
		
		function save_check_list_for_location(){
			$location_code = phpgw::get_var('location_code');
			$control_id = phpgw::get_var('control_id');
			$status = phpgw::get_var('status');
			$planned_date = strtotime( phpgw::get_var('planned_date', 'string') );
			$completed_date = strtotime( phpgw::get_var('completed_date', 'string') );
			$deadline_date = strtotime( phpgw::get_var('deadline_date', 'string') );
				
			$check_list = new controller_check_list();
			$check_list->set_location_code($location_code);
			$check_list->set_control_id($control_id);
			$check_list->set_status($status);
			$check_list->set_deadline( $deadline_date );
			$check_list->set_planned_date($planned_date);
			$check_list->set_completed_date($completed_date);
			$check_list->set_equipment_id($equipment_id);

			$this->so_check_list->add($check_list);	
			
			$this->redirect(array('menuaction' => 'controller.uilocation_check_list.view_calendar', 'year'=>2011, 'month'=>10));
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
