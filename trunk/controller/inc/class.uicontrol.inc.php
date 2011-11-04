<?php 
	phpgw::import_class('controller.uicommon');
	phpgw::import_class('property.boevent');
	phpgw::import_class('controller.socontrol');
	phpgw::import_class('controller.socontrol_group');
	phpgw::import_class('controller.socontrol_area');
	phpgw::import_class('controller.socontrol_item');
	phpgw::import_class('controller.socontrol_item_list');
	phpgw::import_class('controller.soprocedure');
	
	include_class('controller', 'control', 'inc/model/');
	include_class('controller', 'control_area', 'inc/model/');
	include_class('controller', 'control_item_list', 'inc/model/');
	include_class('controller', 'control_group_list', 'inc/model/');

	class controller_uicontrol extends controller_uicommon
	{
		private $bo;
		private $so;
		private $so_procedure;
		private $so_control_group;
		private $so_control_area; 
		private $so_control_item;
		private $so_control_item_list;
		private $so_control_group_list;
		
		public $public_functions = array
		(
			'index'	=>	true,
			'control_list'	=>	true,
			'view'	=>	true,
			'view_control_details'	=>	true,
			'save_control_details'	=>	true,
			'view_control_groups'	=>	true,
			'save_control_groups'	=>	true,
			'view_control_items'	=>	true,
			'save_control_items'	=>	true,
			'view_check_list'		=>	true
		);

		public function __construct()
		{
			parent::__construct();

			$this->so = CreateObject('controller.socontrol');
			$this->bo = CreateObject('property.boevent',true);
			$this->so_procedure = CreateObject('controller.soprocedure');
			$this->so_control_group = CreateObject('controller.socontrol_group');
			$this->so_control_area = CreateObject('controller.socontrol_area');
			$this->so_control_item = CreateObject('controller.socontrol_item');
			$this->so_control_item_list = CreateObject('controller.socontrol_item_list');
			$this->so_control_group_list = CreateObject('controller.socontrol_group_list');
			
			self::set_active_menu('controller::control');
		}
		
		public function control_list()
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
                                'text' => lang('Status').':',
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
							array('type' => 'filter',
								'name' => 'control_areas',
                                'text' => lang('Control_area').':',
                                'list' => $this->so_control_area->get_control_area_select_array(),
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
							array(
								'type' => 'link',
								'value' => $_SESSION['showall'] ? lang('Show only active') : lang('Show all'),
								'href' => self::link(array('menuaction' => $this->url_prefix.'.toggle_show_inactive'))
							),
							array(
								'type' => 'link',
								'value' => lang('New control'),
								'href' => self::link(array('menuaction' => 'controller.uicontrol.view_control_details'))
							),
						),
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'controller.uicontrol.control_list', 'phpgw_return_as' => 'json')),
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
							'key' => 'description',
							'label' => lang('description'),
							'sortable'	=> false
						),
						array(
							'key' => 'control_area_name',
							'label' => lang('Control area'),
							'sortable'	=> false
						),
						array(
							'key' => 'link',
							'hidden' => true
						)
					)
				),
			);
//_debug_array($data);

			self::render_template_xsl('datatable', $data);
		}
		
		public function view_control_details()
		{			
			$control_id = phpgw::get_var('control_id');
		
			if(isset($control_id) && $control_id > 0)
			{
				$control = $this->so->get_single($control_id);	
			}
								
			$procedures_array = $this->so_procedure->get_procedures_as_array();
			$control_areas_array = $this->so_control_area->get_control_areas_as_array();
			
			$tabs = array( array(
							'label' => "1: " . lang('Details')
						), array(
							'label' => "2: " . lang('Control_groups')
						), array(
							'label' => "3: " . lang('Control_items')
						), array(
							'label' => "4: " . lang('Check_list')
						));
			
			$data = array
			(
				'tabs'						=> $GLOBALS['phpgw']->common->create_tabs($tabs, 0),
				'view'						=> "control_details",
				'editable' 					=> true,
				'control'					=> (isset($control)) ? $control->toArray(): null,
				'control_areas_array'		=> $control_areas_array,
				'procedures_array'			=> $procedures_array,
				'start_date'				=> $GLOBALS['phpgw']->yuical->add_listener('start_date',date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], time())),
				'end_date'					=> $GLOBALS['phpgw']->yuical->add_listener('end_date',date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], ''))
			);
			
			self::add_javascript('controller', 'yahoo', 'control_tabs.js');
			self::render_template_xsl(array('control_tabs', 'control'), $data);
			$GLOBALS['phpgw']->richtext->replace_element('description');
			$GLOBALS['phpgw']->richtext->generate_script();
		}
		
		public function save_control_details(){
		
			$control_id = phpgw::get_var('control_id');		
			
			// Update control details
			if(isset($control_id) && $control_id > 0 )
			{
				$control = $this->so->get_single($control_id);
			}
			// Add details for control
			else {
				$control = new controller_control();
			}
			
			$control->populate();
			$control_id = $this->so->store($control);
		
			$this->redirect(array('menuaction' => 'controller.uicontrol.view_control_groups', 'control_id'=>$control_id, 'control_area_id'=>$control->get_control_area_id()));	
		}
						
		// Displays control groups based on which chosen control area
		public function view_control_groups(){
			
			$control_id = phpgw::get_var('control_id');
			$control_area_id = phpgw::get_var('control_area_id');
			
			$control_area = $this->so_control_area->get_single( $control_area_id );
						
			$control_groups_as_array = $this->so_control_group->get_control_groups_as_array($control_area->get_id(), 25);
			
			$tabs = array(
						array(
							'label' => "1: " . lang('Details'),
							'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol.view_control_details', 'view' => "view_control_details", 
																				   'control_id' => $control_id))
						), 
						array(
							'label' => "2: " . lang('Control_groups')
						), 
						array(
							'label' => "3: " . lang('Control_items')
						), 
						array(
							'label' => "4: " . lang('Check_list')
						)
					);
			
			$data = array
			(
				'tabs'						=> $GLOBALS['phpgw']->common->create_tabs($tabs, 1),
				'view'						=> "control_groups",
				'editable' 					=> true,
				'control_id'				=> $control_id,
				'control_area'				=> $control_area->toArray(),
				'control_groups'			=> $control_groups_as_array
			);
			
			self::add_javascript('controller', 'yahoo', 'control_tabs.js');
			self::render_template_xsl(array('control_tabs', 'control_groups'), $data);
		}
		
		// Gets a comma separated list of control groups, and displays control items for these groups
		public function view_control_items(){
			$control_id = phpgw::get_var('control_id', 'int');
			$control = $this->so->get_single($control_id);
			
			$control_group_ids = array();
			$control_group_ids = phpgw::get_var('control_group_ids');

			$groups_with_control_items = array();
					
			// Fetching control items for each control group and populates array that contains groups with chosen items 
			foreach ($control_group_ids as $control_group_id)
			{	
				$group_control_items_array = $this->so_control_item->get_control_items_as_array($control_group_id);
				
				$control_group = $this->so_control_group->get_single($control_group_id);
				
				$groups_with_control_items[] = array("control_group" => $control_group->toArray(), "group_control_items" => $group_control_items_array);
			}			
			
			$tabs = array(
						array(
							'label' => "1: " . lang('Details'),
							'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol.view_control_details', 'view' => "view_control_details", 
																				   'control_id' => $control_id))
						), 
							array(
							'label' => "2: " . lang('Control_groups'),
							'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol.view_control_groups', 'view' => "view_control_groups", 
																			       'control_id' => $control_id, 'control_group_ids' => $control_group_ids, 
																			       'control_area_id' => $control->get_control_area_id()))
						),
						array('label' => "3: " . lang('Control_items')), 
						array('label' => "4: " . lang('Check_list'))
					);
			
			$data = array
			(
				'tabs'						=> $GLOBALS['phpgw']->common->create_tabs($tabs, 2),
				'view'						=> 'control_items',
				'control_group_ids'			=> implode($control_group_ids, ","),
				'control_id'				=> $control_id,
				'groups_with_control_items'	=> $groups_with_control_items			
			);
			
			self::add_javascript('controller', 'yahoo', 'control_tabs.js');
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'custom_ui.js');
			self::render_template_xsl(array('control_tabs', 'choose_control_items'), $data);
		}
		
		// Saves chosen control items through receiving a comma separated list of control tags (1:2, control_group_id:control_item_id) 
		public function save_control_items(){
			$control_id = phpgw::get_var('control_id');
			$control_group_ids = explode(",", phpgw::get_var('control_group_ids'));
			
			// Fetching selected control items. Tags are on the format 1:2 (group:item). 
			$control_tag_ids = phpgw::get_var('control_tag_ids');
			
			$group_order_nr = 1;
			
			// Saving control groups 
			foreach ($control_group_ids as $control_group_id)
			{
				//var_dump("control_group_id: " . $control_group_id);
				$control_group_list = new controller_control_group_list();
				$control_group_list->set_control_id($control_id);
				$control_group_list->set_control_group_id($control_group_id);
				$control_group_list->set_order_nr($group_order_nr);
							
				$this->so_control_group_list->add($control_group_list);
				$group_order_nr++;
			}

			// Saving control items if submit save control items is clicked 
			foreach ($control_tag_ids as $control_item_tag)
			{	
				// Fetch control_item_id from tag string
				$control_item_id = substr($control_item_tag, strpos($control_item_tag, ":")+1, strlen($control_item_tag));
							
				// Saves control item
				$control_item_list = new controller_control_item_list();
				$control_item_list->set_control_id($control_id);
				$control_item_list->set_control_item_id($control_item_id);
				$this->so_control_item_list->add($control_item_list);
			}	
	
			$this->redirect(array('menuaction' => 'controller.uicontrol.view_check_list', 'control_id'=>$control_id, 'control_group_ids'=>$control_group_ids, 'control_tag_ids'=>$control_tag_ids ));	
		}
		
		public function view_check_list(){
			$control_id = phpgw::get_var('control_id');
			$control = $this->so->get_single($control_id);
			
			$control_group_ids = phpgw::get_var('control_group_ids');
			
			// Fetching selected control tag items. Tags are on the format 1:2 (control_group_id:control_item_id) 
			$control_tag_ids = phpgw::get_var('control_tag_ids');
			
			$control_group_ids = array();
			
			//Putting control_group_ids in array control_group_ids
			foreach ($control_tag_ids as $control_tag)
			{	
				// Fetching group id from tag	
				$control_group_id = substr($control_tag, 0, strpos($control_tag, ":"));

				if(!in_array($control_group_id, $control_group_ids))
					$control_group_ids[] = $control_group_id;  
			}
			
			$saved_groups_with_items_array = array();
			
			//Populating array with saved control items for each group
			foreach ($control_group_ids as $control_group_id)
			{	
				$saved_control_items = $this->so_control_item->get_control_items_by_control_id_and_group($control_id, $control_group_id);
				
				$control_group = $this->so_control_group->get_single($control_group_id);
				
				$control_item = $this->so_control_item->get_single($control_item_id);
				
				$saved_groups_with_items_array[] = array("control_group" => $control_group->toArray(), "control_items" => $saved_control_items);
			}
			
			$tabs = array(
						array(
							'label' => "1: " . lang('Details'),
							'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol.view_control_details', 
																				   'view' => "view_control_details", 'control_id' => $control_id))
						), 
						array(
							'label' => "2: " . lang('Control_groups'),
							'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol.view_control_groups', 
																				   'view' => "view_control_groups", 'control_id' => $control_id, 
																				   'control_group_ids' => $control_group_ids, 
																				   'control_area_id' => $control->get_control_area_id()))
						), 
							array(
							'label' => "3: " . lang('Control_items'),
							'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol.view_control_items', 
																				   'view' => "view_control_items", 'control_id' => $control_id, 
																				   'control_group_ids' => $control_group_ids))
						), 
							array(
							'label' => "4: " . lang('Check_list')
						)
					);
			
			$data = array
			(
				'tabs'					=> $GLOBALS['phpgw']->common->create_tabs($tabs, 3),
				'view'					=> "check_list",
				'control_id'				=> $control_id,
				'saved_groups_with_items_array'	=> $saved_groups_with_items_array
			);
			
			self::add_javascript('controller', 'yahoo', 'control_tabs.js');
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'custom_ui.js');
			self::add_javascript('controller', 'controller', 'yui_min_3_4_3.js');
			self::add_javascript('controller', 'controller', 'custom_drag_drop.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
			self::render_template_xsl(array('control_tabs', 'sort_check_list'), $data);
		}
		
		public function query()
		{
			$params = array(
				'start' => phpgw::get_var('startIndex', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('results', 'int', 'REQUEST', null),
				'query'	=> phpgw::get_var('query'),
				'sort'	=> phpgw::get_var('sort'),
				'dir'	=> phpgw::get_var('dir'),
				'filters' => $filters
			);
			
			$ctrl_area = phpgw::get_var('control_areas');
			if(isset($ctrl_area) && $ctrl_area > 0)
			{
				$filters['control_areas'] = $ctrl_area; 
			}
			
			$search_for = phpgw::get_var('query');

			if($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > 0)
			{
				$user_rows_per_page = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else {
				$user_rows_per_page = 10;
			}
			
			// YUI variables for paging and sorting
			$start_index	= phpgw::get_var('startIndex', 'int');
			$num_of_objects	= phpgw::get_var('results', 'int', 'GET', $user_rows_per_page);
			$sort_field		= phpgw::get_var('sort');
			if($sort_field == null)
			{
				$sort_field = 'control_group_id';
			}
			$sort_ascending	= phpgw::get_var('dir') == 'desc' ? false : true;
			//Create an empty result set
			$records = array();
			
			//Retrieve a contract identifier and load corresponding contract
			$control_id = phpgw::get_var('control_id');
			if(isset($control_id))
			{
				$control = $this->so->get_single($control_id);
			}

			$result_objects = $this->so->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
			$object_count = $this->so->get_count($search_for, $search_type, $filters);
			//var_dump($result_objects);
								
			$results = array();
			
			foreach($result_objects as $control_obj)
			{
				$results['results'][] = $control_obj->serialize();	
			}
			
			$results['total_records'] = $object_count;
			$results['start'] = $params['start'];
			$results['sort'] = $params['sort'];
			$results['dir'] = $params['dir'];

			array_walk($results["results"], array($this, "_add_links"), "controller.uicontrol.view_control");

			return $this->yui_results($results);
		}
	}
