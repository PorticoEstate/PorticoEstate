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
		private $so;
		private $so_proc;
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

			$this->so = CreateObject('controller.socontrol');
			$this->so_proc = CreateObject('controller.soprocedure');
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
			if( isset($_POST['save_control']) )
			{
				$this->edit_control();	
			}
			else if( isset($_POST['save_control_groups']) )
			{
				$this->edit_control_groups();
			}
			else if( isset($_POST['save_control_items']) )
			{
				$this->edit_control_items();
			}
			else if( isset($_POST['show_receipt']) )
			{
				$this->show_receipt();
			}
			else{
				$this->view_control();
			}
		}
		
		public function view_control(){
			$tabs = array
			(
				'details'			=> array('label' => lang('Details'), 'link' => '#details'),
				'control_groups'	=> array('label' => lang('Control_groups'), 'link' => '#control_groups'),
				'control_items'		=> array('label' => lang('Control_items'), 'link' => '#control_items'),	
				'receipt'			=> array('label' => lang('Receipt'), 'link' => '#receipt')
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
			
			$GLOBALS['phpgw']->richtext->replace_element('description');
			$GLOBALS['phpgw']->richtext->generate_script();
			
			$data = array
			(
				'tabs'						=> phpgwapi_yui::tabview_generate($tabs, 'details'),
				'start_date'				=> $GLOBALS['phpgw']->yuical->add_listener('start_date',date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], time())),
				'end_date'					=> $GLOBALS['phpgw']->yuical->add_listener('end_date',date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], '')),
				'value_id'					=> !empty($control) ? $control->get_id() : 0,
				'img_go_home'				=> 'rental/templates/base/images/32x32/actions/go-home.png',
				'editable'					=> true,
				'control_area_options'		=> array('options' => $control_area_options),
				'procedure_options'			=> array('options' => $procedure_options)
			);
			
			self::add_javascript('controller', 'yahoo', 'control_tabs.js');
			self::render_template_xsl(array('control_tabs', 'control', 'control_groups', 'control_items', 'control_items_receipt'), $data);
		}
		
		
		public function edit_control(){
			
			$tabs = array
			(
				'details'			=> array('label' => lang('Details'), 'link' => '#details'),
				'control_groups'	=> array('label' => lang('Control_groups'), 'link' => '#control_groups'),
				'control_items'		=> array('label' => lang('Control_items'), 'link' => '#control_items'),
				'receipt'			=> array('label' => lang('Receipt'), 'link' => '#receipt')
			);
			
			$control_area_id = phpgw::get_var('control_area_id', 'int');	
			
			if(isset($control)) // Edit control
			{
				$control = $this->so->populate($control);
				//$this->so->add($control);
			}else{
				$new_control = new controller_control();
				$control = $this->so->populate($new_control);
				$saved_control_id = $this->so->add($control);
			}
			
			$control_group_array = $this->so_control_group->get_control_groups($control_area_id);
			
			$control_area = $this->so_control_area->get_single($control_area_id);
			
			foreach ($control_group_array as $control_group)
			{
				$control_groups[] = $control_group->serialize();
			}
			
			phpgwapi_yui::tabview_setup('control_tabview');
			
			$data = array
			(
				'tabs'						=> phpgwapi_yui::tabview_generate($tabs, 'control_groups'),
				'value_id'					=> !empty($control) ? $control->get_id() : 0,
				'img_go_home'				=> 'rental/templates/base/images/32x32/actions/go-home.png',
				'editable' 					=> true,
				'control_id'				=> $saved_control_id,
				'control_area'				=> $control_area->toArray(),
				'control_groups'			=> $control_groups					
			);
			
			self::add_javascript('controller', 'yahoo', 'control_tabs.js');
			self::render_template_xsl(array('control_tabs', 'control', 'control_groups', 'control_items', 'control_items_receipt'), $data);
		}
		
		public function edit_control_groups(){
			
			$tabs = array
			(
				'details'			=> array('label' => lang('Details'), 'link' => '#details'),
				'control_groups'	=> array('label' => lang('Control_groups'), 'link' => '#control_groups'),
				'control_items'		=> array('label' => lang('Control_items'), 'link' => '#control_items'),
				'receipt'			=> array('label' => lang('Receipt'), 'link' => '#receipt')
			);
			
			$control_id = phpgw::get_var('control_id', 'int');	
			
			$control_group_ids = array();
			$control_group_ids = phpgw::get_var('control_group_ids');

			$control_items_2D = array();
			
			// Fetching control items for each control group and populate array that is sent to xslt template
			foreach ($control_group_ids as $control_group_id)
			{	
				$control_items_array = $this->so_control_item->get_control_items($control_group_id);	
				
				$control_items = array();
				
				foreach ($control_items_array as $control_item)
				{
					$control_items[] = $control_item->serialize();
				}					

				$control_group = $this->so_control_group->get_single($control_group_id);
				
				$control_items_2D[] = array("control_group" => $control_group->toArray(), "control_item" => $control_items);
			}
			
			phpgwapi_yui::tabview_setup('control_tabview');
			
			$data = array
			(
				'tabs'					=> phpgwapi_yui::tabview_generate($tabs, 'control_items'),
				'value_id'				=> !empty($control) ? $control->get_id() : 0,
				'img_go_home'			=> 'rental/templates/base/images/32x32/actions/go-home.png',
				'choose_control_items' 	=> true,
				'control_id'			=> $control_id,
				'control_items'			=> $control_items_2D			
			);
			
			self::add_javascript('controller', 'yahoo', 'control_tabs.js');
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'custom_ui.js');
			self::render_template_xsl(array('control_tabs', 'control', 'control_groups', 'control_items', 'control_items_receipt'), $data);
		}
		
		public function edit_control_items(){
			
			//Setting up tab menu
			$tabs = array
			(
				'details'			=> array('label' => lang('Details'), 'link' => '#details'),
				'control_groups'	=> array('label' => lang('Control_groups'), 'link' => '#control_groups'),
				'control_items'		=> array('label' => lang('Control_items'), 'link' => '#control_items'),
				'receipt'			=> array('label' => lang('Receipt'), 'link' => '#receipt')
			);
				
			$control_id = phpgw::get_var('control_id');
			
			$control_item_ids = array();
			// Fetching selected control items
			$control_tag_ids = phpgw::get_var('control_tag_ids');
			
			// Saving control items
			foreach ($control_tag_ids as $control_item_tag)
			{	
				$control_item_id = substr($control_item_tag, 2, 2);
				
				$control_item_list = new controller_control_item_list();
				$control_item_list->set_control_id($control_id);
				$control_item_list->set_control_item_id($control_item_id);
				 				
				$this->so_control_item_list->add($control_item_list);
			}
			
			$control_group_ids = array();
			
			//Putting control_group_ids in array
			foreach ($control_tag_ids as $control_tag)
			{	
				$control_group_id = substr($control_tag, 0, 1);

				if(!in_array($control_group_id, $control_group_ids))
					$control_group_ids[] = $control_group_id;  
			}
			
			$control_receipt_items = array();
			
			//Populating array that is displayed as receipe
			foreach ($control_group_ids as $control_group_id)
			{	
				$saved_control_items = $this->so_control_item->get_control_items_by_control_id_and_group($control_id, $control_group_id);
				
				$control_group = $this->so_control_group->get_single($control_group_id);
				
				$control_item = $this->so_control_item->get_single($control_item_id);
				
				$control_receipt_items[] = array("control_group" => $control_group->toArray(), "control_items" => $saved_control_items);  
			}
			
			var_dump("Skriver ut control_receipt");
			print_r($control_receipt_items);
						
			phpgwapi_yui::tabview_setup('control_tabview');
					
			$data = array
			(
				'tabs'					=> phpgwapi_yui::tabview_generate($tabs, 'receipt'),
				'value_id'				=> !empty($control) ? $control->get_id() : 0,
				'img_go_home'			=> 'rental/templates/base/images/32x32/actions/go-home.png',
				'control_id'			=> $control_id,
				'control_receipt_items'	=> $control_receipt_items			
			);
			
			self::add_javascript('controller', 'yahoo', 'control_tabs.js');
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'custom_ui.js');
			self::add_javascript('controller', 'controller', 'yui_min_3_4_3.js');
			self::add_javascript('controller', 'controller', 'custom_drag_drop.js');
			self::render_template_xsl(array('control_tabs', 'control', 'control_groups', 'control_items', 'control_items_receipt'), $data);

		}
		
		public function show_receipt(){
			
			$tabs = array
			(
				'details'			=> array('label' => lang('Details'), 'link' => '#details'),
				'control_groups'	=> array('label' => lang('Control_groups'), 'link' => '#control_groups'),
				'control_items'		=> array('label' => lang('Control_items'), 'link' => '#control_items'),
				'receipt'			=> array('label' => lang('Receipt'), 'link' => '#receipt')
			);		
			
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
			
			phpgwapi_yui::tabview_setup('control_tabview');
			
			$data = array
			(
				'tabs'					=> phpgwapi_yui::tabview_generate($tabs, 'control_items'),
				'value_id'				=> !empty($control) ? $control->get_id() : 0,
				'img_go_home'			=> 'rental/templates/base/images/32x32/actions/go-home.png',
				'edit_control_items' 	=> false,
				'control_id'			=> $control_id,
				'control_items'			=> $control_items_2D			
			);
			
			self::add_javascript('controller', 'yahoo', 'control_tabs.js');
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'custom_ui.js');
			self::render_template_xsl(array('control_tabs', 'control', 'control_groups', 'control_items', 'control_items_receipt'), $data);

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