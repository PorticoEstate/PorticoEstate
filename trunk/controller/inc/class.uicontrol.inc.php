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
			'index'	=>	true
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
				'editable' 					=> true,
				'control_area_options'		=> array('options' => $control_area_options),
				'procedure_options'			=> array('options' => $procedure_options)
			);
			
			self::add_javascript('controller', 'yahoo', 'control_tabs.js');
			self::render_template_xsl(array('control_tabs', 'control', 'control_groups', 'control_items'), $data);
		}
		
		
		public function edit_control(){
			
			$tabs = array
			(
				'details'	=> array('label' => lang('Details'), 'link' => '#details'),
				'control_groups'		=> array('label' => lang('Control_groups'), 'link' => '#control_groups'),
				'control_items'		=> array('label' => lang('Control_items'), 'link' => '#control_items')
			);
			
			$control_area_id = phpgw::get_var('control_area_id', 'int');	
			
			if(isset($control)) // Edit control
			{
				$control = $this->populate($control);
				//$this->so->add($control);
			}else{
				$new_control = new controller_control();
				$control = $this->populate($new_control);
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
			self::render_template_xsl(array('control_tabs', 'control', 'control_groups', 'control_items'), $data);
		}
		
		public function edit_control_groups(){
			
			$tabs = array
			(
				'details'			=> array('label' => lang('Details'), 'link' => '#details'),
				'control_groups'	=> array('label' => lang('Control_groups'), 'link' => '#control_groups'),
				'control_items'		=> array('label' => lang('Control_items'), 'link' => '#control_items')
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
			self::render_template_xsl(array('control_tabs', 'control', 'control_groups', 'control_items'), $data);
		}
		
		public function edit_control_items(){
			
			//Setting up tab menu
			$tabs = array
			(
				'details'			=> array('label' => lang('Details'), 'link' => '#details'),
				'control_groups'	=> array('label' => lang('Control_groups'), 'link' => '#control_groups'),
				'control_items'		=> array('label' => lang('Control_items'), 'link' => '#control_items')
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
				'tabs'					=> phpgwapi_yui::tabview_generate($tabs, 'control_items'),
				'value_id'				=> !empty($control) ? $control->get_id() : 0,
				'img_go_home'			=> 'rental/templates/base/images/32x32/actions/go-home.png',
				'control_id'			=> $control_id,
				'control_receipt_items'	=> $control_receipt_items			
			);
			
			self::add_javascript('controller', 'yahoo', 'control_tabs.js');
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'custom_ui.js');
			self::render_template_xsl(array('control_tabs', 'control', 'control_groups', 'control_items'), $data);

		}
		
		public function show_receipt(){
			
			$tabs = array
			(
				'details'			=> array('label' => lang('Details'), 'link' => '#details'),
				'control_groups'	=> array('label' => lang('Control_groups'), 'link' => '#control_groups'),
				'control_items'		=> array('label' => lang('Control_items'), 'link' => '#control_items')
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
			self::render_template_xsl(array('control_tabs', 'control', 'control_groups', 'control_items'), $data);

		}
		
		
		public function query()
		{
			
		}	
		
		public function populate($control){
						
			$control->set_title(phpgw::get_var('title', 'string'));
			$control->set_description(phpgw::get_var('description', 'string'));
			$control->set_start_date( strtotime( phpgw::get_var('start_date_hidden', 'int')));
			$control->set_end_date( strtotime( phpgw::get_var('end_date_hidden', 'int')));
			$control->set_repeat_type( phpgw::get_var('repeat_type', 'string'));
			$control->set_repeat_interval( phpgw::get_var('repeat_interval', 'string'));
			$control->set_procedure_id( phpgw::get_var('procedure_id', 'int'));
			$control->set_enabled( true );
			
			return $control;
			
		}
	}