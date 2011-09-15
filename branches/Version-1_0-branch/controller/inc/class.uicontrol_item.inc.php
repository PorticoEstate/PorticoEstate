<?php
	phpgw::import_class('controller.uicommon');
	phpgw::import_class('property.boevent');
	phpgw::import_class('controller.socontrol');
	phpgw::import_class('controller.socontrol_item');
	phpgw::import_class('controller.socontrol_group');
	
	include_class('controller', 'control', 'inc/model/');

	class controller_uicontrol_item extends controller_uicommon
	{
		private $bo; 
		private $so;
		private $so_proc; 
		
		public $public_functions = array
		(
			'index'	=>	true
		);

		public function __construct()
		{
			parent::__construct();

			$this->so = CreateObject('controller.socontrol');
			$this->so_control_item = CreateObject('controller.socontrol_item');
			$this->so_control_group = CreateObject('controller.socontrol_group');
			$this->bo = CreateObject('property.boevent',true);
		}
		
		public function index()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "controller::control";
			
			$repeat_type = $this->bo->get_rpt_type_list();
			$repeat_day = $this->bo->get_rpt_day_list();

			if(isset($_POST['save_control'])) // The user has pressed the save button
			{
				if(isset($control)) // Edit control
				{
					$control->set_title(phpgw::get_var('title'));
					$control->set_description(phpgw::get_var('description'));
					$control->set_start_date( strtotime( phpgw::get_var('start_date')  ) );
					$control->set_end_date( strtotime( phpgw::get_var('end_date') ) );
					$control->set_repeat_day( strtotime( phpgw::get_var('repeat_day') ) );
					$control->set_repeat_type( strtotime( phpgw::get_var('repeat_type') ) );
					$control->set_repeat_interval( strtotime( phpgw::get_var('repeat_interval') ) );
					$control->set_enabled( true );
									
					$this->so->add($control);
				}
				else // Add new control
				{

					$control = new controller_control();
					
					$control->set_title(phpgw::get_var('title'));
					$control->set_description(phpgw::get_var('description'));
					$control->set_start_date( strtotime( phpgw::get_var('start_date')  ) );
					$control->set_end_date( strtotime( phpgw::get_var('end_date') ) );
					$control->set_repeat_day( strtotime( phpgw::get_var('repeat_day') ) );
					$control->set_repeat_type( strtotime( phpgw::get_var('repeat_type') ) );
					$control->set_repeat_interval( strtotime( phpgw::get_var('repeat_interval') ) );
					$control->set_enabled( true );
									
					$this->so->add($control);
				}
			}
			
			$control_item_array = $this->so_control_item->get_control_item_array();
			$control_group_array = $this->so_control_group->get_control_group_array();
			
			$this->render('control_item.php', array
						(
						'editable' => true,
						'control_item_array' => $control_item_array,
						'control_group_array' => $control_group_array 
						)
					);
		}
					
		public function query()
		{
			var_dump("Er i uicontrol");

		}	
	}