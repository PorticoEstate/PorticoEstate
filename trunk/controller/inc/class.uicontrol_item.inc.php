<?php
	phpgw::import_class('controller.uicommon');
	phpgw::import_class('property.boevent');
	phpgw::import_class('controller.socontrol');
	phpgw::import_class('controller.socontrol_item');
	phpgw::import_class('controller.socontrol_group');
	phpgw::import_class('controller.socontrol_area');
	
	include_class('controller', 'control', 'inc/model/');
	
	class controller_uicontrol_item extends controller_uicommon
	{
		private $bo; 
		private $so;
		private $so_proc;
		private $so_control_item;
		private $so_control_group;
		private $so_control_area;
		
		public $public_functions = array
		(
			'index'	=>	true,
			'query'	=>	true,
			'display_control_items'	=>	true
		);

		public function __construct()
		{
			parent::__construct();

			$this->so = CreateObject('controller.socontrol');
			$this->so_control_item = CreateObject('controller.socontrol_item');
			$this->so_control_group = CreateObject('controller.socontrol_group');
			$this->so_control_area = CreateObject('controller.socontrol_area');
			$this->bo = CreateObject('property.boevent',true);
		}
		
		public function index()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "controller::control_item";
			
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
					$control_item->set_control_area_id( strtotime( phpgw::get_var('control_area_id') ) );
									
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
					$control_item->set_control_area_id( strtotime( phpgw::get_var('control_area_id') ) );
									
					$this->so->add($control_item);
				}
			}
			
			$control_area_array = $this->so_control_area->get_control_area_array();
			$control_group_array = $this->so_control_group->get_control_group_array();
			
			$this->render('control_item.php', array
						(
						'editable' => true,
						'control_area_array' => $control_area_array,
						'control_group_array' => $control_group_array 
						)
					);
		}
		
		public function display_control_items()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "controller::control_item_list";
			
					
			$this->render('control_item_list.php');
		}
					
		public function query()
		{
			
			$user_rows_per_page = 10;
			
			// YUI variables for paging and sorting
			$start_index	= phpgw::get_var('startIndex', 'int');
			$num_of_objects	= phpgw::get_var('results', 'int', 'GET', $user_rows_per_page);
			$sort_field		= phpgw::get_var('sort');
			if($sort_field == null)
			{
				$sort_field = 'control_item_id';
			}
			$sort_ascending	= phpgw::get_var('dir') == 'desc' ? false : true;
			//Create an empty result set
			$records = array();
			
			//Retrieve a contract identifier and load corresponding contract
			$control_item_id = phpgw::get_var('control_item_id');
			if(isset($control_item_id))
			{
				$control_item = rental_socontract::get_instance()->get_single($control_item_id);
			}
			
			/*
			//Retrieve the type of query and perform type specific logic
			$type = phpgw::get_var('type');
			switch($type)
			{
				case 'included_price_items':
					if(isset($contract))
					{
						$filters = array('contract_id' => $contract->get_id());
						$result_objects = rental_socontract_price_item::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
						$object_count = rental_socontract_price_item::get_instance()->get_count($search_for, $search_type, $filters);
					}
					break;
				case 'not_included_price_items': // We want to show price items in the source list even after they've been added to a contract
					$filters = array('price_item_status' => 'active','responsibility_id' => phpgw::get_var('responsibility_id'));
					$result_objects = rental_soprice_item::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
					$object_count = rental_soprice_item::get_instance()->get_count($search_for, $search_type, $filters);
					break;
				case 'manual_adjustment':
					$filters = array('price_item_status' => 'active','is_adjustable' => 'false');
					$result_objects = rental_soprice_item::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
					$object_count = rental_soprice_item::get_instance()->get_count($search_for, $search_type, $filters);
					break;
				default:
					//$filters = array('price_item_status' => 'active','responsibility_id' => phpgw::get_var('responsibility_id'));
					$result_objects = rental_soprice_item::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
					$object_count = rental_soprice_item::get_instance()->get_count($search_for, $search_type, $filters);
					break;
			}
		*/
			
		$result_objects = controller_socontrol_item::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
			
			// Create an empty row set
			$rows = array();
			foreach ($result_objects as $record) {
				if(isset($record))
				{
					// ... add a serialized record
					$rows[] = $record->serialize();
				}
			}
			$data = array('results' => $rows, 'total_records' => $object_count);
	
			$editable = phpgw::get_var('editable') == 'true' ? true : false;
	
			//Add action column to each row in result table
			array_walk(
				$data['results'], 
				array($this, 'add_actions'), 
				array(
					$control_item_id,
					$type,
					$editable
				)
			);
			return $this->yui_results($data, 'total_records', 'results');
		}	
}