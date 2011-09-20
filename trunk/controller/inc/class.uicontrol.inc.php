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
			//self::set_active_menu('controller::example::normal_tabs');

            $type =  phpgw::get_var('type', 'string', 'REQUEST', null);

			switch($type)
			{
				case 'default':
					$selected = 1;
					$resource_id = 81;
					break;
				case 'forced':
					$selected = 2;
					$resource_id = 46;
					break;
				case 'user':
				default:
					$selected = 0;
					$resource_id = 80;
			}

			$add_document_link = $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'controller.uiexample.index') );
			$resource = array('id' => $resource_id, 'add_document_link' => $add_document_link, 'permission' => array('write' => true ) );

			$tabs = array
			(
				'details'	=> array('label' => lang('Details'), 'link' => '#details'),
				'list'		=> array('label' => lang('list'), 'link' => '#list'),
				'dates'		=> array('label' => lang('dates'), 'link' => '#dates'),
			);

			phpgwapi_yui::tabview_setup('example_tabview');

			$data = array
			(
				'tabs'						=> phpgwapi_yui::tabview_generate($tabs, 'details'),
				'resource'					=> $resource,
				'date'						=> $GLOBALS['phpgw']->yuical->add_listener('date',date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], time())),
				'value_id'					=> !empty($control) ? $control->get_id() : 0,
				'img_go_home'				=> 'rental/templates/base/images/32x32/actions/go-home.png',
				'editable' 					=> true,
				'procedure_options_array'	=> array('options' => $procedure_options_array)
			);
			self::add_javascript('controller', 'yahoo', 'example_normal_tabs.js');
			self::render_template_xsl('example_normal_tabs', $data);
		}
		
		public function edit()
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
			
			$procedure_array = $this->so_proc->get_procedure_array();
			
			foreach ($procedure_array as $procedure)
			{
				$procedure_options_array[] = array
				(
					'id'	=> $procedure->get_id(),
					'name'	=> $procedure->get_title()
					 
				);
			}

			$data = array
			(
				'value_id'					=> !empty($control) ? $control->get_id() : 0,
				'img_go_home'				=> 'rental/templates/base/images/32x32/actions/go-home.png',
				'editable' 					=> true,
				'procedure_options_array'	=> array('options' => $procedure_options_array),
				'date'		=> $GLOBALS['phpgw']->yuical->add_listener('date',date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], time()))
			);

			self::render_template_xsl('control', $data);
		}
					
		public function query()
		{
			var_dump("Er i uicontrol");

		}	
	}