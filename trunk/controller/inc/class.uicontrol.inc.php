<?php
	phpgw::import_class('controller.uicommon');
	phpgw::import_class('property.boevent');
	phpgw::import_class('controller.socontrol');
	phpgw::import_class('controller.soprocedure');
	
	include_class('controller', 'control', 'inc/model/');

	class controller_uicontrol extends controller_uicommon
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
			$this->so_proc = CreateObject('controller.soprocedure');
			$this->bo = CreateObject('property.boevent',true);
		}
		
		public function index()
		{
			$repeat_type = $this->bo->get_rpt_type_list();
			$repeat_day = $this->bo->get_rpt_day_list();

			if(isset($_POST['save_control'])) // The user has pressed the save button
			{
				if(isset($control)) // If an control object is created
				{
					$control->set_title(phpgw::get_var('title'));
					$control->set_description($desc);
					$control->set_start_date( strtotime( phpgw::get_var('start_date')  ) );
					$control->set_end_date( strtotime( phpgw::get_var('end_date') ) );
									
					$this->so->add($control);
				}
				else 
				{

					$control = new controller_control();
					
					$control->set_title(phpgw::get_var('title'));
					$control->set_description(phpgw::get_var('description'));
					$control->set_start_date( strtotime( phpgw::get_var('start_date')  ) );
					$control->set_end_date( strtotime( phpgw::get_var('end_date') ) );
					$control->set_repeat_day( strtotime( phpgw::get_var('repeat_day') ) );
					$control->set_repeat_type( strtotime( phpgw::get_var('repeat_type') ) );
					$control->set_repeat_interval( strtotime( phpgw::get_var('repeat_interval') ) );
					
									
					$this->so->add($control);
				}
			}
			
			$procedure_array = $this->so_proc->get_procedure_array();
			
			$this->render('control.php', array
							(
							'editable' => true,
							'repeat_type' => $repeat_type,
							'repeat_day' => $repeat_day,
							'procedure_array' => $procedure_array 
							)
						);
		}
					
		public function query()
		{
			var_dump("Er i uicontrol");

		}	
	}