<?php
	phpgw::import_class('controller.socheck_list');
	phpgw::import_class('controller.socheck_item');
	
	include_class('controller', 'check_list', 'inc/model/');
	include_class('controller', 'check_item', 'inc/model/');

	class status_checker {
		
		public function __construct()
		{
			$this->so_check_list = CreateObject('controller.socheck_list');
			$this->so_check_item = CreateObject('controller.socheck_item');
		}
			
		public function update_check_list_status( $check_list_id )
		{
			$check_list = $this->so_check_list->get_single( $check_list_id );
		
			$check_items = $this->so_check_item->get_check_items_with_cases($check_list_id, $control_item_type = null, $status = null, $messageStatus = null, "return_object");
			
			$num_open_cases = 0;
			$num_pending_cases = 0;
					
			foreach($check_items as $check_item){
				
				foreach($check_item->get_cases_array() as $case){
					
					if($case->get_status() == controller_check_item_case::STATUS_OPEN){
						$num_open_cases++;
					}
					
					if($case->get_status() == controller_check_item_case::STATUS_PENDING){
						$num_pending_cases++;
					}
				}	
			}
			
			$check_list->set_num_open_cases($num_open_cases);
			$check_list->set_num_pending_cases($num_pending_cases);
			
			if($num_open_cases > 0)
				$check_list->set_status(controller_check_list::STATUS_OPEN);
			
			$this->so_check_list->store($check_list);
		}
}