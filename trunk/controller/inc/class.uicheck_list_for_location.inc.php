<?php
	
	phpgw::import_class('phpgwapi.yui');
	phpgw::import_class('controller.uicommon');
	phpgw::import_class('controller.socontrol_area');

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
	
		var $public_functions = array(
										'index' => true,
										'add_location_to_control' => true
									);

		function __construct()
		{
			parent::__construct();
			
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
							'label' => lang('Address'),
							'sortable'	=> false
						),
						array(
							'key' => 'loc1_name',
							'label' => lang('Address'),
							'sortable'	=> false
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

			$responsibility_roles_list =   execMethod('property.sogeneric.get_list',$_role_criteria);
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
		
		public function query()
		{
			$type_id = phpgw::get_var('type_id', 'int');
			$return_results	= phpgw::get_var('results', 'int', 'REQUEST', 0);

			$type_id = $type_id ? $type_id : 1;
			
			$location_list = array();

			$this->bo->sort = "ASC";
			$this->bo->start = phpgw::get_var('startIndex');
			
			$location_list = $this->bo->read(array('user_id' => $user_id, 'role_id' =>$role_id, 'type_id'=>$type_id,'lookup_tenant'=>$lookup_tenant,
												   'lookup'=>$lookup,'allrows'=>$this->allrows,'dry_run' =>$dry_run,'results' => $return_results));

			foreach($location_list as $location)
			{
				$results['results'][]= $location;	
			}
			
			$results['total_records'] = $this->bo->total_records;
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
			
			$value['ajax'][] = false;
			$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'property.uilocation.view', 'location_code' => $value['location_code'])));
			$value['labels'][] = lang('show');
			
			$value['ajax'][] = true;
			$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'controller.uicontrol.add_location_to_control', 'location_code' => $value['location_code'])));
			$value['labels'][] = lang('add_location');
		}
	}
