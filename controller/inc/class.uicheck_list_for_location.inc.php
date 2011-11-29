<?php
	
	phpgw::import_class('phpgwapi.yui');
	phpgw::import_class('controller.uicommon');
	phpgw::import_class('controller.socontrol_area');

	class controller_uicheck_list_for_location extends controller_uicommon
	{
		var $grants;
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
	
		var $public_functions = array(
										'index' => true,
									);

		function __construct()
		{
			parent::__construct();
			
			$GLOBALS['phpgw_info']['flags']['nonavbar'] = true; // menus added where needed via bocommon::get_menu
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			
			$this->bo					= CreateObject('property.bolocation',true);
			$this->bocommon				= & $this->bo->bocommon;
			$this->so_control_area 		= CreateObject('controller.socontrol_area');
			$this->so_control 			= CreateObject('controller.socontrol');
	
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
			
			$control_id = 186;
			
			$locations_for_control_array = $this->so_control->get_locations_for_control($control_id);
						
			
			
			
		/*
			$lists = array
			(
				'building_types'			=> $building_types,
				'category_types'			=> $category_types,
				'district_list'				=> $district_list,
				'part_of_town_list'			=> $part_of_town_list,
				'responsibility_roles_list'	=> $responsibility_roles_list,
				'control_area_list'			=> $control_areas_array,
			);
*/

			$data = array(
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array('type' => 'filter',
								'name' => 'building_types',
                                'text' => lang('Building_types').':',
                                'list' => $building_types
							),
							array('type' => 'filter',
								'name' => 'cat_id',
                                'text' => lang('Category_types').':',
                                'list' => $category_types
							),
							array('type' => 'filter',
								'name' => 'district_id',
                                'text' => lang('District_list').':',
                                'list' => $district_list
							),
							array('type' => 'filter',
								'name' => 'part_of_town_list',
                                'text' => lang('Part_of_town_list').':',
                                'list' => $part_of_town_list
							),
							array('type' => 'filter',
								'name' => 'responsibility_roles_list',
                                'text' => lang('responsibility_roles_list').':',
                                'list' => $responsibility_roles_list
							),
							array('type' => 'text', 
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
							'sotrable'	=>	false
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
							'key' => 'link',
							'hidden' => true
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
						),
						array(
							'key' => 'alert',
							'hidden' => true
						)
					)
				),
				'lists' 				=> $lists,
				'locations_for_control' => $locations_for_control_array,
				'control_area_list'		=> $control_areas_array
			);			
			
			//self::add_javascript('controller', 'yahoo', 'datatable.js');
			self::add_javascript('controller', 'controller', 'controller_datatable_test.js');
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'ajax.js');

			//self::render_template_xsl('datatable', $data);
			self::render_template_xsl('locations', $data);		
		}
		
		public function query(){
					
			$type_id = 1;
			
			$location_list = array();

			$this->bo->sort = "ASC";
						
			$location_list = $this->bo->read(array('user_id' => $user_id, 'role_id' =>$role_id, 'type_id'=>$type_id,'lookup_tenant'=>$lookup_tenant,
												   'lookup'=>$lookup,'allrows'=>$this->allrows,'dry_run' =>$dry_run));

			$results = array();

			foreach($location_list as $location)
			{
				$results['results'][]= $location;	
			}
			
			$results['total_records'] = 10;
			$results['start'] = 1;
			$results['sort'] = 'location_code';
						
			array_walk($results['results'], array($this, 'add_actions'), array($type));
							
			return $this->yui_results($results);
		}
			
		public function add_actions(&$value, $key, $params)
		{
			unset($value['query_location']);
			
			$value['ajax'] = array();
			$value['actions'] = array();
			$value['labels'] = array();
			
			$value['ajax'][] = false;
			$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'property.uilocation.view', 'location_code' => $value['location_code'])));
			$value['labels'][] = lang('show');
			
			$value['ajax'][] = true;
			$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'controller.uilocation_check_list', 'location_code' => $value['location_code'])));
			$value['labels'][] = lang('add_location');
		}
	}