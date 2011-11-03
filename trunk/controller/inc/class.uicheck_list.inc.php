<?php
	phpgw::import_class('controller.uicommon');
	phpgw::import_class('controller.socheck_list');
	
	class controller_uicheck_list extends controller_uicommon
	{
		private $so;
		private $socontrol_group;
		private $socontrol_group_list;
		private $socontrol_item;
				
		public $public_functions = array
		(
			'index'	=>	true,
			'view_check_list_for_control'	=>	true
		);

		public function __construct()
		{
			parent::__construct();
			
			$this->so = CreateObject('controller.socheck_list');
			$this->socontrol_group = CreateObject('controller.socontrol_group');
			$this->socontrol_group_list = CreateObject('controller.socontrol_group_list');
			$this->socontrol_item = CreateObject('controller.socontrol_item');
			
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "controller::check_list";
		}
		
		public function index()
		{
			$check_list_array = $this->so->get_check_list();
			
			$data = array
			(
				'check_list_array'	=> $check_list_array
			);
			
			self::render_template_xsl('control_check_lists', $data);
		}
		
		public function view_check_list_for_control()
		{
			$control_id = phpgw::get_var('control_id');
			$control = $this->so->get_single($control_id);
			
			$control_groups_array = $this->socontrol_group_list->get_control_groups_by_control_id( $control_id );

			$saved_groups_with_items_array = array();
			
			foreach ($control_groups_array as $control_group)
			{	
				$control_group_id = $control_group->get_id();
				$saved_control_items = $this->socontrol_item->get_control_items_by_control_id_and_group($control_id, $control_group_id);
				
				$saved_groups_with_items_array[] = array("control_group" => $control_group->toArray(), "control_items" => $saved_control_items);
			}	
		
			$data = array
			(
				'control_id'					=> $control_id,
				'saved_groups_with_items_array'	=> $saved_groups_with_items_array
			);
			
			//print_r($saved_groups_with_items_array);
						
			self::render_template_xsl('view_check_list', $data);
		}
		
		public function query(){}
	}
