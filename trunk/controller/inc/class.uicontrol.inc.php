<?php 
	phpgw::import_class('controller.uicommon');
	phpgw::import_class('property.boevent');
	phpgw::import_class('controller.socontrol');
	phpgw::import_class('controller.socontrol_group');
	phpgw::import_class('controller.socontrol_area');
	phpgw::import_class('controller.socontrol_item');
	phpgw::import_class('controller.soprocedure');
	
	include_class('controller', 'control', 'inc/model/');
	include_class('controller', 'control_area', 'inc/model/');

	class controller_uicontrol extends controller_uicommon
	{
		private $bo; 
		private $so;
		private $so_proc;
		private $so_control_group;
		private $so_control_area; 
		private $so_control_item; 
		
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
			
			self::set_active_menu('controller::control');
		}
		
		public function index()
		{
			if( !isset($_POST['save_control']) )
			{
				$this->view_control();	
			}
			else if( isset($_POST['save_control']) )
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
			
			$control_area_id = phpgw::get_var('control_area_id');
				
			if(isset($control)) // Edit control
			{
				$control = $this->populate($control);
				//$this->so->add($control);
			}else{
				$new_control = new controller_control();
				$control = $this->populate($new_control);
				//$this->so->add($control);
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
				'title'						=> $control_area->get_title(),
				'control_groups'			=> $control_groups					
			);
			
			self::add_javascript('controller', 'yahoo', 'control_tabs.js');
			self::render_template_xsl(array('control_tabs', 'control', 'control_groups', 'control_items'), $data);
		}
		
		public function edit_control_groups(){
			
			$tabs = array
			(
				'details'	=> array('label' => lang('Details'), 'link' => '#details'),
				'control_groups'		=> array('label' => lang('Control_groups'), 'link' => '#control_groups'),
				'control_items'		=> array('label' => lang('Control_items'), 'link' => '#control_items')
			);
			
			$control_group_ids = array();
			$control_group_ids = phpgw::get_var('control_group_ids');

			$control_items_2D = array();
			
			foreach ($control_group_ids as $control_group_id)
			{	
				$control_items_array = $this->so_control_item->get_control_items($control_group_id);	
				
				$control_items = array();
				
				foreach ($control_items_array as $control_item)
				{
					$control_items[] = $control_item->serialize();
				}					

				$control_group = $this->so_control_group->get_single($control_group_id);
				
				$control_items_2D[] = array("group_name" => $control_group->get_group_name(), "control_item" => $control_items);
			}
			
			phpgwapi_yui::tabview_setup('control_tabview');
			
			$data = array
			(
				'tabs'					=> phpgwapi_yui::tabview_generate($tabs, 'control_items'),
				'value_id'				=> !empty($control) ? $control->get_id() : 0,
				'img_go_home'			=> 'rental/templates/base/images/32x32/actions/go-home.png',
				'editable' 				=> true,
				'control_items'			=> $control_items_2D			
			);
			
			self::add_javascript('controller', 'yahoo', 'control_tabs.js');
			self::render_template_xsl(array('control_tabs', 'control', 'control_groups', 'control_items'), $data);
		}
		
		
		public function query()
		{
			var_dump("Er i uicontrol");

		}	
		
		public function populate($control){
			
			$control->set_title(phpgw::get_var('title'));
			$control->set_description(phpgw::get_var('description'));
			$control->set_start_date( strtotime( phpgw::get_var('start_date')  ) );
			$control->set_end_date( strtotime( phpgw::get_var('end_date') ) );
			$control->set_repeat_day( strtotime( phpgw::get_var('repeat_day') ) );
			$control->set_repeat_type( strtotime( phpgw::get_var('repeat_type') ) );
			$control->set_repeat_interval( strtotime( phpgw::get_var('repeat_interval') ) );
			$control->set_enabled( true );
			
			return $control;
			
		}
	}