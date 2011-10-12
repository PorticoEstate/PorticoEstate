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

	class controller_uicontrol extends controller_uicommon
	{
		private $bo;
		private $so_control;
		private $so_procedure;
		private $so_control_group;
		private $so_control_area; 
		private $so_control_item;
		private $so_control_item_list;
		
		public $public_functions = array
		(
			'index'	=>	true,
			'control_list'	=>	true,
			'view'	=>	true
		);

		public function __construct()
		{
			parent::__construct();

			$this->so_control = CreateObject('controller.socontrol');
			$this->so_procedure = CreateObject('controller.soprocedure');
			$this->bo = CreateObject('property.boevent',true);
			$this->so_control_group = CreateObject('controller.socontrol_group');
			$this->so_control_area = CreateObject('controller.socontrol_area');
			$this->so_control_item = CreateObject('controller.socontrol_item');
			$this->so_control_item_list = CreateObject('controller.socontrol_item_list');
			
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
							array(
								'type' => 'link',
								'value' => lang('New control'),
								'href' => self::link(array('menuaction' => 'controller.uicontrol.index'))
							),
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
							'key' => 'control_area_id',
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
		
	public function index()
		{
			$add_document_link = $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'controller.uiexample.index') );
			
			// Show tab control details
			if(phpgw::get_var('view') == "view_control")
			{
				$this->view_control();
			}
			// Save or update control details and show tab control groups
			else if( isset($_POST['save_control']) || phpgw::get_var('view') == "view_control_groups")
			{
				$this->edit_control();	
			}
			// Save control groups and show tab control items
			else if( isset($_POST['save_control_groups']) || phpgw::get_var('view') == "view_control_items")
			{
				$this->edit_control_groups();
			}
			// Save control items and show tab receipt
			else if( isset($_POST['save_control_items']) )
			{
				$this->edit_control_items();
			}
			// Save receipt
			else if( isset($_POST['save_receipt']) )
			{
				$this->save_receipt();
			}
			else{
				$this->view_control();
			}
		}
		
	public function view_control()
		{			
			$control_id = phpgw::get_var('control_id');
		
			// view control details
			if(isset($control_id) && $control_id > 0)
			{
				$control = $this->so_control->get_single($control_id);	
			}
								
			$procedures_array = $this->so_procedure->get_procedures_as_array();
			$control_areas_array = $this->so_control_area->get_control_areas_as_array();
			
			$tabs = array( array(
							'label' => lang('Details')
						), array(
							'label' => lang('Control_groups')
						), array(
							'label' => lang('Control_items')
						), array(
							'label' => lang('Receipt')
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
				'end_date'					=> $GLOBALS['phpgw']->yuical->add_listener('end_date',date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], time()))
			);
			
			self::add_javascript('controller', 'yahoo', 'control_tabs.js');
			self::render_template_xsl(array('control_tabs', 'control'), $data);
			$GLOBALS['phpgw']->richtext->replace_element('description');
			$GLOBALS['phpgw']->richtext->generate_script();
		}
		
		
	public function edit_control()
		{
			$control_id = phpgw::get_var('control_id');		
			
			if(phpgw::get_var('view') == "view_control_groups"){
				$control = $this->so_control->get_single($control_id);
			}
			// update control details
			else if(isset($control_id) && $control_id > 0 )
			{
				$control = $this->so_control->get_single($control_id);
				$control->populate();
				$this->so_control->store($control);
			}
			// add control details
			else {
				$control = new controller_control();
				$control->populate();
				$control_id = $this->so_control->store($control);
			}
						
			$control_area = $this->so_control_area->get_single($control->get_control_area_id());
						
			$control_groups_as_array = $this->so_control_group->get_control_groups_as_array($control->get_control_area_id());
			
			$tabs = array(
						array(
							'label' => lang('Details'),
							'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol.index', 'view' => "view_control", 'control_id' => $control_id))
						), 
						array(
							'label' => lang('Control_groups')
						), 
						array(
							'label' => lang('Control_items')
						), 
						array(
							'label' => lang('Receipt')
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
		
		public function edit_control_groups(){
			
			$control_id = phpgw::get_var('control_id', 'int');
			
			$control_group_ids = array();
			$control_group_ids = phpgw::get_var('control_group_ids');

			$groups_with_control_items = array();
					
			// Fetching control items for each control group and populates array
			foreach ($control_group_ids as $control_group_id)
			{	
				$group_control_items_array = $this->so_control_item->get_control_items_as_array($control_group_id);
				
				$control_group = $this->so_control_group->get_single($control_group_id);
				
				$groups_with_control_items[] = array("control_group" => $control_group->toArray(), "group_control_items" => $group_control_items_array);
			}			
			
			$tabs = array(
						array(
							'label' => lang('Details'),
							'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol.index', 'view' => "view_control", 'control_id' => $control_id))
						), 
							array(
							'label' => lang('Control_groups'),
							'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol.index', 'view' => "view_control_groups", 
																			       'control_id' => $control_id, 'control_group_ids' => $control_group_ids))
						), 
						array(
							'label' => lang('Control_items')
						), 
						array(
							'label' => lang('Receipt')
						)
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
			self::render_template_xsl(array('control_tabs', 'control_items'), $data);
		}
		
	public function edit_control_items()
		{
			$control_id = phpgw::get_var('control_id');
			$control_group_ids = phpgw::get_var('control_group_ids');
			
			// Fetching selected control items
			$control_tag_ids = phpgw::get_var('control_tag_ids');
			
			$control_item_ids = array();

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
	
			$control_group_ids = array();
			
			//Putting control_group_ids in array control_group_ids
			foreach ($control_tag_ids as $control_tag)
			{	
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
			
			unset($_POST['save_control_items']); 
			
			$tabs = array(
						array(
							'label' => lang('Details'),
							'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol.index', 'view' => "view_control", 'control_id' => $control_id))
						), 
						array(
							'label' => lang('Control_groups'),
							'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol.index', 'view' => "view_control_groups", 
																			       'control_id' => $control_id, 'control_group_ids' => $control_group_ids))
						), 
						array(
							'label' => lang('Control_items'),
							'link'  => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicontrol.index', 'view' => "view_control_items", 
																			       'control_id' => $control_id, 'control_group_ids' => $control_group_ids))
						), 
						array(
							'label' => lang('Receipt')
						)
					);
			
			$data = array
			(
				'tabs'					=> $GLOBALS['phpgw']->common->create_tabs($tabs, 3),
				'view'					=> "receipt",
				'control_id'			=> $control_id,
				'control_receipt_items'	=> $saved_groups_with_items_array
			);
			
			self::add_javascript('controller', 'yahoo', 'control_tabs.js');
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'custom_ui.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
			self::add_javascript('controller', 'controller', 'yui_min_3_4_3.js');
			self::add_javascript('controller', 'controller', 'custom_drag_drop.js');
			self::render_template_xsl(array('control_tabs', 'control_items_receipt'), $data);
		}
		
	public function save_receipt(){
			
			$control_id = phpgw::get_var('control_id');
			
			$control_item_ids = array();
			$control_item_ids = phpgw::get_var('control_item_ids');
			
			foreach ($control_item_ids as $control_item_id)
			{
				$control_item = $this->so_control_item->get_single($control_item_id);
						
				$control_item_list = new controller_control_item_list();
				$control_item_list->set_control_id($control_id);
				$control_item_list->set_control_item_id($control_item->get_id());
				
				$this->so_control_item_list->add($control_item_list);
			}
			
			$data = array
			(
				'tabs'					=> $GLOBALS['phpgw']->common->create_tabs($this->getTabMenu(), 3),
				'control_id'			=> $control_id,
				'control_items'			=> $control_items_2D			
			);
			
			self::add_javascript('controller', 'yahoo', 'control_tabs.js');
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'custom_ui.js');
			self::render_template_xsl(array('control_tabs', 'control_items_receipt'), $data);
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
			//var_dump($result_objects);
								
			$results = array();
			
			foreach($result_objects as $control_obj)
			{
				$results['results'][] = $control_obj->serialize();	
			}

			array_walk($results["results"], array($this, "_add_links"), "controller.uicontrol.view");

			return $this->yui_results($results);
		}
		
		public function view()
		{
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::'.lang('view');
			//Retrieve the procedure object
			$control_id = (int)phpgw::get_var('id');
			if(isset($_POST['edit_control']))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uicontrol.edit_control', 'id' => $control_id));
			}
			else
			{
				if(isset($control_id) && $control_id > 0)
				{
					$control = $this->so->get_single($control_id);
				}
				else
				{
					$this->render('permission_denied.php',array('error' => lang('invalid_request')));
					return;
				}
				
				$control_array = $control->toArray();
				//var_dump($control);
			
				$tabs = array
				(
					'details'	=> array('label' => lang('Details'), 'link' => '#details'),
					'control_groups'		=> array('label' => lang('Control_groups'), 'link' => '#control_groups'),
					'control_items'		=> array('label' => lang('Control_items'), 'link' => '#control_items')
				);
				
				$add_document_link = $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'controller.uiexample.index') );
					
				$procedure_array = $this->so_proc->get_procedure_array();
					
				foreach ($procedure_array as $procedure)
				{
					$procedure_options[] = $procedure->toArray();
				}
					
				$control_area_array = $this->so_control_area->get_control_area_array();
				
				foreach ($control_area_array as $control_area)
				{
					$control_area_options[] = $control_area->toArray();
				}
				
				phpgwapi_yui::tabview_setup('control_tabview');
				
				$data = array
				(
					'tabs'						=> phpgwapi_yui::tabview_generate($tabs, 'details'),
					'start_date'				=> $GLOBALS['phpgw']->yuical->add_listener('start_date',date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], time())),
					'end_date'					=> $GLOBALS['phpgw']->yuical->add_listener('end_date',date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], time())),
					'value_id'					=> !empty($control) ? $control->get_id() : 0,
					'img_go_home'				=> 'rental/templates/base/images/32x32/actions/go-home.png',
					'control'					=> $control_array,
					'control_area_options'		=> array('options' => $control_area_options),
					'procedure_options'			=> array('options' => $procedure_options)
				);
				
				self::add_javascript('controller', 'yahoo', 'control_tabs.js');
				self::render_template_xsl(array('control_tabs', 'control', 'control_groups', 'control_items'), $data);
			}
		}
	}