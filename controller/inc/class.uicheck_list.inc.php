<?php
	phpgw::import_class('controller.uicommon');
	phpgw::import_class('controller.socheck_list');
	
	include_class('controller', 'check_list', 'inc/model/');
	include_class('controller', 'check_item', 'inc/model/');
	
	class controller_uicheck_list extends controller_uicommon
	{
		private $so;
		private $so_control;
		private $so_control_group;
		private $so_control_group_list;
		private $so_control_item;
		private $so_check_list;
		private $so_check_item;
				
		public $public_functions = array
		(
			'index'	=>	true,
			'view_check_lists_for_control'	=>	true,
			'save_check_list'	=>	true,
			'view_check_list'	=>	true
		);

		public function __construct()
		{
			parent::__construct();
			
			$this->so = CreateObject('controller.socheck_list');
			$this->so_control = CreateObject('controller.socontrol');
			$this->so_control_group = CreateObject('controller.socontrol_group');
			$this->so_control_group_list = CreateObject('controller.socontrol_group_list');
			$this->so_control_item = CreateObject('controller.socontrol_item');
			$this->so_check_list = CreateObject('controller.socheck_list');
			$this->so_check_item = CreateObject('controller.socheck_item');
			
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
		
		public function view_check_list()
		{
			$check_list_id = phpgw::get_var('check_list_id');
			$check_list = $this->so_check_list->get_single_with_control_item($check_list_id);
	
			$data = array
			(
				'check_list' => $check_list
			);
			
			//print_r($check_list);
			
			self::render_template_xsl('view_check_list', $data);
		}
		
		public function view_check_lists_for_control()
		{
			$control_id = phpgw::get_var('control_id');
			$control = $this->so_control->get_single($control_id);

		
			$check_list_array = $this->so->get_check_lists_for_control( $control_id );	
			
			$data = array
			(
				'control_as_array'	=> $control->toArray(),
				'check_list_array'		=> $check_list_array
			);
			
			self::render_template_xsl('view_check_lists', $data);
		}
		
		public function view_control_items_for_control()
		{
			$control_id = phpgw::get_var('control_id');
			$control = $this->so_control->get_single($control_id);
						
			$control_groups_array = $this->so_control_group_list->get_control_groups_by_control_id( $control_id );

			$saved_groups_with_items_array = array();
			
			foreach ($control_groups_array as $control_group)
			{	
				$control_group_id = $control_group->get_id();
				$saved_control_items = $this->so_control_item->get_control_items_by_control_id_and_group($control_id, $control_group_id);
				
				$saved_groups_with_items_array[] = array("control_group" => $control_group->toArray(), "control_items" => $saved_control_items);
			}	
		
			$data = array
			(
				'control_as_array'				=> $control->toArray(),
				'saved_groups_with_items_array'	=> $saved_groups_with_items_array
			);
								
			self::render_template_xsl('view_check_list', $data);
		}
		
		public function save_check_list(){
			$control_id = phpgw::get_var('control_id');
			$control = $this->so_control->get_single($control_id);

			$start_date = $control->get_start_date();
			$end_date = $control->get_end_date();
			$repeat_type = $control->get_repeat_type();
			$repeat_interval = $control->get_repeat_interval();
			
			$status = true;
			$comment = "Kommentar for sjekkliste";
			$deadline = $start_date;
			
			// Saving check_list
			$new_check_list = new controller_check_list();
			$new_check_list->set_control_id( $control_id );
			$new_check_list->set_status( $status );
			$new_check_list->set_comment( $comment );
			$new_check_list->set_deadline( $deadline );
			
			$check_list_id = $this->so_check_list->store( $new_check_list );
			
			$control_items_list = $this->so_control_item->get_control_items_by_control_id($control_id);
			
			foreach($control_items_list as $control_item){
				
				$status = true;
				$comment = "Kommentar for sjekk item";
				
				// Saving check_items for a list
				$new_check_item = new controller_check_item();
				$new_check_item->set_check_list_id( $check_list_id );
				
				$new_check_item->set_control_item_id( $control_item->get_id() );
				$new_check_item->set_status( $status );
				$new_check_item->set_comment( $comment );

				$saved_check_item = $this->so_check_item->store( $new_check_item );
			}
			
			$this->redirect(array('menuaction' => 'controller.uicheck_list.view_check_list_for_control', 'control_id'=>$control_id));	
		}
		
		public function query(){}
	}
