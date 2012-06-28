<?php
	/**
	* phpGroupWare - controller: a part of a Facilities Management System.
	*
	* @author Erink Holm-Larsen <erik.holm-larsen@bouvet.no>
	* @author Torstein Vadla <torstein.vadla@bouvet.no>
	* @author Sigurd Nes <sigurdne@online.no>
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
 	* @version $Id: class.uicheck_list.inc.php 8628 2012-01-21 10:42:05Z vator $
	*/
	
	phpgw::import_class('phpgwapi.yui');

	/**
	* Import the jQuery class
	*/
	phpgw::import_class('phpgwapi.jquery');

	phpgw::import_class('phpgwapi.uicommon');
	phpgw::import_class('controller.socontrol_area');
	
	include_class('controller', 'check_list', 'inc/model/');
	include_class('controller', 'date_generator', 'inc/component/');
	include_class('controller', 'check_list_status_updater', 'inc/helper/');
	include_class('controller', 'date_helper', 'inc/helper/');
		
	class controller_uicontrol_location extends phpgwapi_uicommon
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
	
		var $public_functions = array
		(
			'index'								=> true,
			'view_locations_for_control' 		=> true,
			'register_control_to_location' 		=> true,
			'register_control_to_location_2'	=> true,
			'register_control_to_component'		=> true,
			'edit_component'					=> true,
			'get_locations_for_control' 		=> true,
			'get_location_category'				=> true,
			'get_district_part_of_town'			=> true,
			'query2'							=> true,
			'get_category_by_entity'			=> true,
			'get_entity_table_def'				=> true,
			'get_locations'						=> true,
			'get_location_type_category'		=> true
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
			$control_id = phpgw::get_var('control_id');
			if(phpgw::get_var('save_location'))
			{
				$values = phpgw::get_var('values');
				//add component to control using component item ID
				$values['control_location'] = isset($values['control_location']) && $values['control_location'] ? array_unique($values['control_location']) : array();
				$values['control_location_orig'] = isset($values['control_location_orig']) && $values['control_location_orig'] ? array_unique($values['control_location_orig']) : array();

				$ok = $this->so_control->register_control_to_location($control_id, $values);

				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uicontrol_location.index', 'control_id' => $control_id));

			}
			else
			{
				if(phpgw::get_var('phpgw_return_as') == 'json') {
					return $this->query();
				}
				$building_types  = execMethod('property.soadmin_location.read',array());
				//$type_id=phpgw::get_var('type_id');
				//if(!isset($type_id))
				$type_id = 1;
				
				$category_types = $this->bocommon->select_category_list(array(
																			'format'=>'filter',
																			'selected' => $this->cat_id,
																			'type' =>'location',
																			'type_id' =>$type_id,
																			'order'=>'descr'
																		));
				$default_value = array ('id'=>'','name'=>lang('no category selected'));
				array_unshift($category_types,$default_value);
																		
				$district_list  = $this->bocommon->select_district_list('filter',$this->district_id);
				$default_value = array ('id'=>'','name'=>lang('no district'));
				array_unshift($district_list,$default_value);
				
				$part_of_town_list =  $this->bocommon->select_part_of_town('filter',$this->part_of_town_id,$this->district_id);
				$default_value = array ('id'=>'','name'=>lang('no part of town'));
				array_unshift($part_of_town_list,$default_value);
				
				$_role_criteria = array
						(
							'type'		=> 'responsibility_role',
							'filter'	=> array('location_level' => (int)$type_id),
							'order'		=> 'name'
						);
	
				$responsibility_roles_list = execMethod('property.sogeneric.get_list',$_role_criteria);
				$default_value = array ('id'=>'','name'=>lang('no role'));
				array_unshift ($responsibility_roles,$default_value);
				
				$cats	= CreateObject('phpgwapi.categories', -1, 'controller', '.control');
				$cats->supress_info	= true;
				
				$control_areas = $cats->formatted_xslt_list(array('format'=>'filter','globals' => true,'use_acl' => $this->_category_acl));
								
				$control_areas_array = array();
				foreach($control_areas['cat_list'] as $cat_list)
				{
					$control_areas_array[] = array
					(
						'id' 	=> $cat_list['cat_id'],
						'name'	=> $cat_list['name'],
					);		
				}
						
				$data = array(
					'view'								=> "register_control_to_location",
					'control_id'					=> $control_id,
					'control_areas_array'	=> $control_areas_array,
					'filter_form' 				=> array(
					'building_types' 			=> $building_types,
					'category_types' 			=> $category_types,
					'district_list' 			=> $district_list,
					'part_of_town_list' 	=> $part_of_town_list
					),
					'datatable' => array(
						'source' => self::link(array('menuaction' => 'controller.uicontrol_location.index', 'phpgw_return_as' => 'json', 'view_type' => 'register_control','control_id_init'	=> $control_id)),
						'field' => array(
							array(
								'key' => 'location_registered',
								'hidden' => true
							),
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
								'key' => 'control_name',
								'label' => lang('control'),
								'sortable'	=> false
							),
							array(
									'key' => 'checked',
									'label' => 'Velg',
									'sortable' => false,
									'formatter' => 'formatterCheckLocation',
									'className' => 'mychecks'
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
				
				self::add_javascript('controller', 'controller', 'jquery.js');
				self::add_javascript('controller', 'controller', 'ajax.js');
				self::add_javascript('controller', 'yahoo', 'register_control_to_location.js');
	
				self::render_template_xsl(array('control_location/control_location_tabs', 'control_location/register_control_to_location', 'common'), $data);
			}		
		}
		
		// Returns locations for a control
		public function get_locations_for_control()
		{
			$control_id = phpgw::get_var('control_id');
			
			if(is_numeric($control_id) & $control_id > 0)
			{
				$locations_for_control_array = $this->so_control->get_locations_for_control($control_id);
			
				foreach($locations_for_control_array as $location)
				{
					$results['results'][]= $location;	
				}
				
				$results['total_records'] = count( $locations_for_control_array );
				$results['start'] = 1;
				$results['sort'] = 'location_code';
			}
			else
			{
				$results['total_records'] = 0;
			}				
			
			return $this->yui_results($results);
		}
		
		public function query()
		{
			$type_id = phpgw::get_var('type_id', 'int');
			$control_id = phpgw::get_var('control_id', 'int');
			$control_id_init = phpgw::get_var('control_id_init', 'int');
			$control_area_id = phpgw::get_var('control_area_id', 'int');

			$control_id = $control_id ? $control_id : $control_id_init;
			
			if($control_area_id && !execMethod('controller.socontrol.get_controls_by_control_area',$control_area_id))
			{
				$control_id = 0;
			}

			$control_info = execMethod('controller.socontrol.get_single', $control_id);
			$control_name = '';
			if($control_info)
			{
				$control_name = $control_info->get_title();
			}

			$view_type = phpgw::get_var('view_type');
			$return_results	= phpgw::get_var('results', 'int', 'REQUEST', 0);
			
			$type_id = $type_id ? $type_id : 1;
			
			$location_list = array();

			$this->bo->sort = "ASC";
			$this->bo->start = phpgw::get_var('startIndex');
			
			$location_list = $this->bo->read(array('user_id' => $user_id, 'role_id' =>$role_id, 'type_id'=>$type_id,'lookup_tenant'=>$lookup_tenant,
												   'lookup'=>$lookup,'allrows'=>$this->allrows,'dry_run' =>$dry_run, 'results' => $return_results));

			foreach($location_list as &$location)
			{
				$location['control_name'] = $control_name;
				$location['location_registered'] = !!$this->so_control->get_control_location($control_id, $location['location_code']);
				$results['results'][]= $location;	
			}
			
			$results['total_records'] = $this->bo->total_records;
			$results['start'] = $this->start;
			$results['sort'] = 'location_code';
			$results['dir'] = "ASC";
						
			array_walk($results['results'], array($this, 'add_links'), array($type));
							
			return $this->yui_results($results);
		}

		public function register_control_to_location_2()
		{
			$control_id = phpgw::get_var('control_id');
			$location_code = phpgw::get_var('location_code');
			
			$control_location  = null;
			$control_location_id = 0;
			
			$control_location = $this->so_control->get_control_location($control_id, $location_code);
			
			if($control_location == null ){
				
				$control_location_id = $this->so_control->register_control_to_location($control_id, $location_code);
			}
			
			if($control_location_id > 0)
				return json_encode( array( "status" => "saved" ) );
			else
				return json_encode( array( "status" => "not_saved" ) );
		}
		
		public function add_actions(&$value, $key, $params)
		{
			unset($value['query_location']);
			
			$value['ajax'] = array();
			$value['actions'] = array();
			$value['labels'] = array();
			$value['parameters'] = array();
		}
		
		/*
		 * Return categories based on chosen location
		 */
		public function get_location_category()
		{
			$type_id = phpgw::get_var('type_id');
		 	$category_types = $this->bocommon->select_category_list(array(
																		'format'=>'filter',
																		'selected' => 0,
																		'type' =>'location',
																		'type_id' =>$type_id,
																		'order'=>'descr'
																	));
			$default_value = array ('id'=>'','name'=>lang('no category selected'));
			array_unshift($category_types,$default_value);
			return json_encode( $category_types );
		}
		
		/*
		 * Return parts of town based on chosen district
		 */
		public function get_district_part_of_town()
		{
			$district_id = phpgw::get_var('district_id');
			$part_of_town_list =  $this->bocommon->select_part_of_town('filter',null,$district_id);
			$default_value = array ('id'=>'','name'=>lang('no part of town'));
			array_unshift($part_of_town_list,$default_value);

			return json_encode( $part_of_town_list );
		}


		/*

		 * Return parts of town based on chosen district
		 */
		public function get_category_by_entity()
		{
			$entity_id		= phpgw::get_var('entity_id');
			$entity			= CreateObject('property.soadmin_entity');

			$category_list = $entity->read_category(array('allrows'=>true,'entity_id'=>$entity_id));

/*			$default_value = array ('id'=>'','name'=>lang('select'));
			array_unshift($category_list,$default_value);
*/
			return $category_list;
		}

		function register_control_to_component()
		{
		    self::set_active_menu('controller::control::component_for_check_list');
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$receipt = array();

			if(phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query2();
			}

			$msgbox_data = array();
			if( phpgw::get_var('phpgw_return_as') != 'json' && $receipt = phpgwapi_cache::session_get('phpgwapi', 'phpgw_messages'))
			{
				phpgwapi_cache::session_clear('phpgwapi', 'phpgw_messages');
				$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);
				$msgbox_data = $GLOBALS['phpgw']->common->msgbox($msgbox_data);
			}

			$myColumnDefs = array();
			$datavalues = array();
			$myButtons	= array();

			$datavalues[] = array
			(
				'name'				=> "0",
				'values' 			=> json_encode(array()),
				'total_records'		=> 0,
				'permission'   		=> "''",
				'is_paginator'		=> 1,
				'edit_action'		=> "''",
				'footer'			=> 0
			);

			$myColumnDefs[0] = array
			(
				'name'		=> "0",
				'values'	=>	json_encode(array())
			);	

			$GLOBALS['phpgw']->translation->add_app('property');
			$entity			= CreateObject('property.soadmin_entity');
			$entity_list 	= $entity->read(array('allrows' => true));

			$district_list  = $this->bocommon->select_district_list('filter',$this->district_id);

			$part_of_town_list = execMethod('property.bogeneric.get_list', array('type'=>'part_of_town', 'selected' => $part_of_town_id ));
			$location_type_list = execMethod('property.soadmin_location.select_location_type');

			array_unshift($entity_list ,array ('id'=>'','name'=>lang('select')));
			array_unshift($district_list ,array ('id'=>'','name'=>lang('select')));
			array_unshift($part_of_town_list ,array ('id'=>'','name'=>lang('select')));
			array_unshift($location_type_list ,array ('id'=>'','name'=>lang('select')));

			$cats	= CreateObject('phpgwapi.categories', -1, 'controller', '.control');
			$cats->supress_info	= true;

			$control_area = $cats->formatted_xslt_list(array('format'=>'filter','globals' => true,'use_acl' => $this->_category_acl));

								
			$control_area_list = array();
			foreach($control_area['cat_list'] as $cat_list)
			{
				$control_area_list[] = array
				(
					'id' 	=> $cat_list['cat_id'],
					'name'	=> $cat_list['name'],
				);		
			}

			array_unshift ($control_area_list ,array ('id'=>'','name'=>lang('select')));

			
					
			$data = array
			(
				'td_count'						=> '""',
		//		'property_js'					=> json_encode($GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property2.js"),
				'datatable'						=> $datavalues,
				'myColumnDefs'					=> $myColumnDefs,
				'myButtons'						=> $myButtons,

				'msgbox_data'					=> $msgbox_data,
				'control_area_list'		=> array('options' => $control_area_list),
				'filter_form' 					=> array
													(
														'control_area_list'		=> array('options' => $control_area_list),
														'entity_list' 			=> array('options' => $entity_list),
														'district_list' 		=> array('options' => $district_list),
														'part_of_town_list'		=> array('options' => $part_of_town_list),
														'location_type_list'	=> array('options' => $location_type_list),
													),
				'update_action'					=> self::link(array('menuaction' => 'controller.uicontrol_location.edit_component'))
			);



			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');
			$theme = 'ui-lightness';
			$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/development-bundle/themes/{$theme}/jquery.ui.autocomplete.css");


			phpgwapi_yui::load_widget('connection');
			phpgwapi_yui::load_widget('loader');
			phpgwapi_yui::load_widget('tabview');

			phpgwapi_jquery::load_widget('core');
			phpgwapi_jquery::load_widget('autocomplete');

			self::add_javascript('controller', 'controller', 'ajax_control_to_component.js');
	//		self::add_javascript('controller', 'yahoo', 'register_control_to_component.js');
			self::add_javascript('controller', 'yahoo', 'register_control_to_component2.js');

			self::render_template_xsl(array('control_location/register_control_to_component' ), $data);
		}
	

		public function get_location_type_category()
		{
			$location_type			= phpgw::get_var('location_type', 'int');

			$values  = $this->bocommon->select_category_list(array
					(
						'format'=>'filter',
					//	'selected' => $this->cat_id,
						'type' =>'location',
						'type_id' =>$location_type,
						'order'=>'descr'
					)
				);

			return $values;
		}


		public function get_entity_table_def()
		{
			$entity_id			= phpgw::get_var('entity_id', 'int');
			$cat_id				= phpgw::get_var('cat_id', 'int');
			$boentity	= CreateObject('property.boentity',false, 'entity');
			$boentity->read(array('dry_run' => true));
			$uicols = $boentity->uicols;
			$columndef = array();

			$columndef[] = array
			(
				'key'		=> 'select',
				'label'		=> lang('select'),
				'sortable'	=> false,
				'formatter'	=> false,
				'hidden'	=> false,
				'formatter' => '',
				'className' => ''
			);

			$columndef[] = array
			(
				'key'		=> 'delete',
				'label'		=> lang('delete'),
				'sortable'	=> false,
				'formatter'	=> false,
				'hidden'	=> false,
				'formatter' => '',
				'className' => ''
			);

			$count_fields = 16;//count($uicols['name']);

			for ($i=0;$i<$count_fields;$i++)
			{
				if( $uicols['name'][$i])
				{
					$columndef[] = array
					(
						'key'		=> $uicols['name'][$i],
						'label'		=> $uicols['descr'][$i],
						'sortable'	=> $uicols['sortable'][$i],
						'formatter'	=> $uicols['formatter'][$i],
						'hidden'	=> $uicols['input_type'][$i] == 'hidden' ? true : false	,		
						'className'	=> $uicols['classname'][$i],
					);
				}
			}

//_debug_array($columndef);
			return $columndef;
		}


		public function get_locations()
		{
			$location_code = phpgw::get_var('location_code');
			$child_level = phpgw::get_var('child_level', 'int', 'REQUEST', 1);
			$part_of_town_id = phpgw::get_var('part_of_town_id', 'int');

			$criteria = array
			(
				'location_code'		=> $location_code,
				'child_level'		=> $child_level,
				'field_name'		=> "loc{$child_level}_name",
				'part_of_town_id'	=> $part_of_town_id
			);
	
			$locations = execMethod('property.solocation.get_children',$criteria);
			return $locations;
		}



		public function query2()
		{
			$entity_id			= phpgw::get_var('entity_id', 'int');
			$cat_id				= phpgw::get_var('cat_id', 'int');
			$district_id		= phpgw::get_var('district_id', 'int');
			$part_of_town_id	= phpgw::get_var('part_of_town_id', 'int');
			$control_id			= phpgw::get_var('control_id', 'int');
			$results 			= phpgw::get_var('results', 'int');
			$control_registered	= phpgw::get_var('control_registered', 'bool');

			if(!$entity_id && !$cat_id)
			{
				$values = array();
			}
			else
			{
				$location_id = $GLOBALS['phpgw']->locations->get_id('property', ".entity.{$entity_id}.{$cat_id}");
				$boentity	= CreateObject('property.boentity',false, 'entity');
				$boentity->results = $results;
				$values = $boentity->read(array('control_registered' => $control_registered, 'control_id' => $control_id));
			}		

			foreach($values as &$entry)
			{
				$checked = '';
				if($this->so_control->check_control_component($control_id,$location_id,$entry['id']))
				{
					$checked =  'checked = "checked" disabled = "disabled"';
					$entry['delete'] = "<input class =\"mychecks_delete\" type =\"checkbox\" name=\"values[delete][]\" value=\"{$control_id}_{$location_id}_{$entry['id']}\">";
				}
				$entry['select'] = "<input class =\"mychecks_add\" type =\"checkbox\" $checked name=\"values[register_component][]\" value=\"{$control_id}_{$location_id}_{$entry['id']}\">";
			}

			
			$results = $results ? $results : $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$return_data['recordsReturned'] = count($values);
			$return_data['totalRecords'] = $boentity->total_records;
			$return_data['startIndex'] = $this->start;
			$return_data['sort'] = 'location_code';
			$return_data['dir'] = "ASC";
			$return_data['pageSize'] = $results;
			$return_data['activePage'] = floor($this->start / $results) + 1;
			$return_data['records'] = $values;

			return $return_data;
		}

		public function edit_component()
		{
			if($values = phpgw::get_var('values'))
			{
				if(!$GLOBALS['phpgw']->acl->check('.admin', PHPGW_ACL_EDIT, 'property'))
				{
					$receipt['error'][]=true;
					phpgwapi_cache::message_set(lang('you are not approved for this task'), 'error');
				}
				if(!$receipt['error'])
				{

					if($this->so_control->register_control_to_component($values))
					{
						$result =  array
						(
							'status'	=> 'updated'
						);
					}
					else
					{
						$result =  array
						(
							'status'	=> 'error'
						);
					}
				}
			}

			if(phpgw::get_var('phpgw_return_as') == 'json')
			{
				if( $receipt = phpgwapi_cache::session_get('phpgwapi', 'phpgw_messages'))
				{
					phpgwapi_cache::session_clear('phpgwapi', 'phpgw_messages');
					$result['receipt'] = $receipt;
				}
				else
				{
					$result['receipt'] = array();
				}
				return $result;
			}
			else
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uicontrol_location.register_control_to_component'));
			}
		}
	}
