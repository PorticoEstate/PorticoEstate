<?php
	
	phpgw::import_class('phpgwapi.yui');
	phpgw::import_class('controller.uicommon');
	phpgw::import_class('controller.socontrol_area');
	//phpgw::import_class('bim.sobimitem');

	class controller_uicheck_list_for_component extends controller_uicommon
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
		private $so_bim;

		var $public_functions = array(
										'index' => true,
										'add_component_to_control' => true,
										'get_component_types_by_category' => true
									);

		function __construct()
		{
			parent::__construct();
			
			$this->bo					= CreateObject('property.bolocation',true);
			$this->bocommon				= & $this->bo->bocommon;
			$this->so_control_area 		= CreateObject('controller.socontrol_area');
			$this->so_control 			= CreateObject('controller.socontrol');
			//$this->so_bim				= CreateObject('bim.sobimitem_impl');
			//$this->so_bim				= new sobimitem_impl();
	
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
			
			self::set_active_menu('controller::control::component_for_check_list');
		}	
	
		function index()
		{
/*			if(phpgw::get_var('phpgw_return_as') == 'json') {
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

			// Fetches prosedures that are related to first control area in list
			$control_area_id = $control_areas_array[0]['id'];
			
			$lists = array
			(
				'building_types'			=> $building_types,
				'category_types'			=> $category_types,
				'district_list'				=> $district_list,
				'part_of_town_list'			=> $part_of_town_list,
				'responsibility_roles_list'	=> $responsibility_roles_list,
				'control_area_list'			=> $control_areas_array,
			);

		
			

			$data = array(
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'controller.uicheck_list_for_component.index', 'phpgw_return_as' => 'json')),
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
							'key' => 'checked',
							'label' => 'Velg',
							'sortable' => false,
							'formatter' => 'YAHOO.widget.DataTable.formatCheckbox',
							'className' => 'mychecks'
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
				'lists' => $lists
			);			
			
			//self::add_javascript('controller', 'yahoo', 'datatable.js');
			self::add_javascript('controller', 'controller', 'controller_datatable_test.js');
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
			//self::add_javascript('controller', 'yahoo', 'component_location.js');
			
			//$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'equipmens_location', 'controller' );

			//self::render_template_xsl('datatable', $data);
			self::render_template_xsl('component', $data);		
*/
			if(phpgw::get_var('phpgw_return_as') == 'json') {
				return $this->query();
			}
			$bim_types = $this->so_control->get_bim_types();

			$control_areas_array = $this->so_control_area->get_control_areas_as_array();
			$controls_array = $this->so_control->get_controls_by_control_area($control_areas_array[0]['id']);
			$control_id = $control_areas_array[0]['id'];
			
			if($control_id == null)
				$control_id = 0;
			
			$tabs = array( array(
						'label' => lang('View_component_for_control')
					), array(
						'label' => lang('Add_component_for_control'),
						'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicheck_list_for_component.add_component_to_control'))
					));
			
			$data = array(
				'tabs'					=> $GLOBALS['phpgw']->common->create_tabs($tabs, 0),
				'view'					=> "view_component_for_control",
				'control_area_array' 	=> $control_areas_array,
				'control_array'			=> $control_array,
				'locations_table' => array(
					'source' => self::link(array('menuaction' => 'controller.uicheck_list_for_component.index','phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'id',
							'label' => lang('ControlId'),
							'sortable'	=> true,
						),
						array(
							'key'	=>	'title',
							'label'	=>	lang('Title'),
							'sortable'	=>	false
						),
						array(
							'key' => 'bim_id',
							'label' => lang('Bim_id'),
							'sortable'	=> false
						),
						array(
							'key' => 'bim_name',
							'label' => lang('Bim_name'),
							'sortable'	=> false
						),
						array(
							'key' => 'bim_type',
							'label' => lang('Bim_type'),
							'sortable'	=> false
						)
					)
				)
			);
			
			
			phpgwapi_yui::load_widget('paginator');
			
			self::add_javascript('controller', 'yahoo', 'control_tabs.js');
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'ajax.js');

			self::render_template_xsl(array('control_component_tabs', 'common', 'view_component_for_control'), $data);
		}
		
		function add_component_to_control()
		{
			if(phpgw::get_var('save_component'))
			{
				//add component to control using component item ID
				$items_checked = array();
				$items = phpgw::get_var('values_assign');
				$item_arr = explode('|',$items);
				foreach($item_arr as $item)
				{
					$items_checked[] = explode(';',$item);
				}
				//var_dump($items_checked);
				
				$control_id = phpgw::get_var('control_id');
				//var_dump($control_id);
				if($control_id != null && is_numeric($control_id))
				{
					//add chosen component to control
					foreach($items_checked as $it)
					{
						$this->so_control->add_component_to_control($control_id, $it[0]);
					}
				}
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uicheck_list_for_component.index'));
				
			}
			else
			{
				if(phpgw::get_var('phpgw_return_as') == 'json') {
					return $this->get_component();
				}
				
				$bim_types = $this->so_control->get_bim_types();
				
				$control_areas_array = $this->so_control_area->get_control_areas_as_array();
				
				$tabs = array( array(
							'label' => lang('View_component_for_control'),
							'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicheck_list_for_component.index'))
				
						), array(
							'label' => lang('Add_component_for_control')
						));
						
				$data = array(
					'tabs'						=> $GLOBALS['phpgw']->common->create_tabs($tabs, 1),
					'view'						=> "add_component_to_control",
					'control_filters'			=> array(
						'control_area_array' 		=> $control_areas_array,
						'control_array' 			=> $control_array
					),
					'filter_form' 				=> array(
						'bim_types' 			=> $bim_types
					),
					'datatable' => array(
						'source' => self::link(array('menuaction' => 'controller.uicheck_list_for_component.add_component_to_control', 'phpgw_return_as' => 'json')),
						'field' => array(
							array(
								'key' => 'id',
								'label' => lang('ID'),
								'sortable'	=> true,
								'formatter' => 'YAHOO.portico.formatLink'
							),
							array(
								'key'	=>	'guid',
								'label'	=>	lang('GUID'),
								'sortable'	=>	false
							),
							array(
								'key' => 'type',
								'label' => lang('type'),
								'sortable'	=> false
							),
							array(
								'key' => 'checked',
								'label' => 'Velg',
								'sortable' => false,
								'formatter' => 'YAHOO.widget.DataTable.formatCheckbox',
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
							)						
						)
					)
				);
				
				
				phpgwapi_yui::load_widget('paginator');
				
				self::add_javascript('controller', 'yahoo', 'control_tabs.js');
				self::add_javascript('controller', 'controller', 'jquery.js');
				self::add_javascript('controller', 'controller', 'ajax.js');
	
				self::render_template_xsl(array('control_component_tabs', 'common', 'add_component_to_control'), $data);
			}
		}
		
		public function query()
		{
			$control_list = $this->so_control->get_control_component();
					
			foreach($control_list as $control)
			{
				$control['bim_name'] = $this->so_control->getBimItemAttributeValue($control['bim_item_guid'], 'description');
				$results['results'][]= $control;
			}
			
			$results['total_records'] = 10;
			$results['start'] = 1;
			$results['sort'] = 'id';
			array_walk($results['results'], array($this, 'add_links'), array($type));
							
			return $this->yui_results($results);
		}
		
		public function get_component()
		{
			
			/*$start					= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query					= phpgw::get_var('query');
			$sort					= phpgw::get_var('sort');
			$order					= phpgw::get_var('order');
			$filter					= phpgw::get_var('filter', 'int');
			$cat_id					= phpgw::get_var('cat_id');
			$lookup_tenant			= phpgw::get_var('lookup_tenant', 'bool');
			$district_id			= phpgw::get_var('district_id', 'int');
			$part_of_town_id		= phpgw::get_var('part_of_town_id', 'int');
			$status					= phpgw::get_var('status');
			$type_id				= phpgw::get_var('type_id', 'int');
			$allrows				= phpgw::get_var('allrows', 'bool');
			$location_code			= phpgw::get_var('location_code');*/
					
			$type_id = phpgw::get_var('bim_type_id');
			
			$start = phpgw::get_var('startIndex');
			
			$component_list = array();

			$sort = "ASC";

			$component_list = $this->so_control->getAllBimItems(10,$type_id);
			//var_dump($component_list); 

		
			$results = array();
			foreach($component_list as $component)
			{
				$component['checked'] = false;
				$results['results'][]= $component;
				$i++;
			}
			
			$results['total_records'] = count($component_list);
			$results['start'] = $start;
			$results['sort'] = 'id';
			$results['dir'] = "ASC";
						
			array_walk($results['results'], array($this, 'add_links'), array($type));
							
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
			$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicomposite.add_unit', 'location_code' => $value['location_code'])));
			$value['labels'][] = lang('add_location');
		}
		
		public function get_component_types_by_category()
		{
			$category = phpgw::get_var('ifc');
			if($ifc != null)
			{
				if($ifc = 1)
					$ifc = true;
				else
					$ifc = false;
			}
			
			
			$bim_types = $this->so_control->get_bim_types($ifc);
			if(count($bim_types)>0)
				return json_encode( $bim_types );
			else
				return null;
		}
	}