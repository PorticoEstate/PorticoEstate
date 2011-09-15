<?php
	phpgw::import_class('controller.uicommon');
	phpgw::import_class('property.boevent');
	phpgw::import_class('controller.socontrol');
	phpgw::import_class('controller.socontrol_item');
	phpgw::import_class('controller.socontrol_group');
	phpgw::import_class('controller.socontrol_type');
	
	include_class('controller', 'control', 'inc/model/');
	include_class('controller', 'control_group', 'inc/model/');
	include_class('controller', 'control_type', 'inc/model/');
	
	class controller_uicontrol_item extends controller_uicommon
	{
		private $bo; 
		private $so;
		private $so_proc;
		private $so_control_item;
		private $so_control_group;
		private $so_control_type;
		
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
			$this->so_control_type = CreateObject('controller.socontrol_type');
			$this->bo = CreateObject('property.boevent',true);
		}
		
		public function index()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "controller::control";
			
			$repeat_type = $this->bo->get_rpt_type_list();
			$repeat_day = $this->bo->get_rpt_day_list();

			if(isset($_POST['save_control_item'])) // The user has pressed the save button
			{
				if(isset($control_item)) // Edit control
				{
					$control_item->set_title(phpgw::get_var('title'));
					$control_item->set_required(phpgw::get_var('required'));
					$control_item->set_what_to_desc( strtotime( phpgw::get_var('what_to_desc')  ) );
					$control_item->set_how_to_desc( strtotime( phpgw::get_var('how_to_desc') ) );
					$control_item->set_control_group_id( strtotime( phpgw::get_var('control_group_id') ) );
					$control_item->set_control_type_id( strtotime( phpgw::get_var('control_type_id') ) );
									
					$this->so->add($control_item);
				}
				else // Add new control
				{

					$control_item = new controller_control();
					
					$control_item->set_title(phpgw::get_var('title'));
					$control_item->set_required(phpgw::get_var('required'));
					$control_item->set_what_to_desc( strtotime( phpgw::get_var('what_to_desc')  ) );
					$control_item->set_how_to_desc( strtotime( phpgw::get_var('how_to_desc') ) );
					$control_item->set_control_group_id( strtotime( phpgw::get_var('control_group_id') ) );
					$control_item->set_control_type_id( strtotime( phpgw::get_var('control_type_id') ) );
									
					$this->so->add($control_item);
				}
			}
			
			$control_item_type_array = $this->so_control_type->get_control_type_array();
			$control_item_group_array = $this->so_control_group->get_control_group_array();
			
			$this->render('control_item.php', array
						(
						'editable' => true,
						'control_type_array' => $control_item_type_array,
						'control_group_array' => $control_item_group_array 
						)
					);
		}
					
		public function query()
		{
			var_dump("Er i uicontrol");

		}	
	}