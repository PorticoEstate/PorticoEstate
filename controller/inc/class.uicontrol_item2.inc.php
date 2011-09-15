<?php
	phpgw::import_class('controller.uicommon');
	phpgw::import_class('property.boevent');
	phpgw::import_class('controller.socontrol');
	phpgw::import_class('controller.socontrol_item');
	phpgw::import_class('controller.socontrol_group');
	
	include_class('controller', 'control', 'inc/model/');

	class controller_uicontrol_item2 extends controller_uicommon
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
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$this->so = CreateObject('controller.socontrol');
			$this->so_control_item = CreateObject('controller.socontrol_item');
			$this->so_control_group = CreateObject('controller.socontrol_group');
			$this->bo = CreateObject('property.boevent',true);
		}
		
		public function index()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "controller::control_item2";
			
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
			

			if($this->flash_msgs)
			{
				$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($this->flash_msgs);
				$msgbox_data = $GLOBALS['phpgw']->common->msgbox($msgbox_data);
			}

			foreach ($control_type_array as $control_type)
			{
				$control_type_options = array
				(
					'id'	=> $control_type->get_id(),
					'name'	=> $control_type->get_title()
					 
				);
			}

			foreach ($control_group_array as $control_group)
			{
				$control_group_options = array
				(
					'id'	=> $control_group->get_id(),
					'name'	=> $control_group->get_title()
					 
				);
			}

			$data = array
			(
				'value_id'				=> !empty($control) ? $control->get_id() : 0,
				'img_go_home'			=> 'rental/templates/base/images/32x32/actions/go-home.png',
				'editable' 				=> true,
				'control_item'			=> array('options' => $control_type_options),
				'control_group'			=> array('options' => $control_group_options),
			);


			$GLOBALS['phpgw']->richtext->replace_element('what_to_do');
			$GLOBALS['phpgw']->richtext->replace_element('how_to_do');
		//	$GLOBALS['phpgw']->richtext->generate_script();

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('controller') . '::' . lang('Control_item');
			$GLOBALS['phpgw']->xslttpl->add_file(array('control_item'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('item' => $data));

	//		$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'common', 'controller' );
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'controller.item', 'controller' );

		}
					

		public function query()
		{
			var_dump("Er i uicontrol");

		}	
	}
