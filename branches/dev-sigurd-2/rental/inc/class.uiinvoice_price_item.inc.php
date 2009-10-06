<?php
phpgw::import_class('rental.uicommon');
phpgw::import_class('rental.soinvoice_price_item');
include_class('rental', 'contract', 'inc/model/');
include_class('rental', 'billing', 'inc/model/');

class rental_uiinvoice_price_item extends rental_uicommon
{
	
	public $public_functions = array
		(
			'query'		=> true,
			'download'	=> true
		);
	
	public function query()
	{
		// YUI variables for paging and sorting
		$start_index	= phpgw::get_var('startIndex', 'int');
		$num_of_objects	= phpgw::get_var('results', 'int', 'GET', 1000);
		$sort_field		= phpgw::get_var('sort');
		$sort_ascending	= phpgw::get_var('dir') == 'desc' ? false : true;
		// Form variables
		$search_for 	= phpgw::get_var('query');
		$search_type	= phpgw::get_var('search_option');
		// Create an empty result set
		$result_objects = array();
		$result_count = 0;
		//Retrieve the type of query and perform type specific logic
		$query_type = phpgw::get_var('type');
		switch($query_type)
		{
			case 'invoice_price_items':
				$filters = array('invoice_id' => phpgw::get_var('invoice_id'));
				$result_objects = rental_soinvoice_price_item::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
				$object_count = rental_soinvoice_price_item::get_instance()->get_count($search_for, $search_type, $filters);
				break;
		}
		
		//Create an empty row set
		$rows = array();
		foreach($result_objects as $result) {
			if(isset($result))
			{
				if($result->has_permission(PHPGW_ACL_READ))
				{
					// ... add a serialized result
					$rows[] = $result->serialize();
				}
			}
		}
		
		// ... add result data
		$result_data = array('results' => $rows, 'total_records' => $object_count);
		
		return $this->yui_results($result_data, 'total_records', 'results');
	}
		
}
?>