<?php
phpgw::import_class('rental.uicommon');

class rental_uiunit extends rental_uicommon
{
	public $public_functions = array
	(
		'query'		=> true
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
			case 'included_units': // ... included areas in a composite
				$filters = array('composite_id'	=> phpgw::get_var('composite_id'));
				$result_objects = rental_sounit::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
				$object_count = rental_sounit::get_instance()->get_count($search_for, $search_type, $filters);
				break;
			case 'available_units':	// ... available areas for a composite, filters (date)
				 $filters = array('level' => phpgw::get_var('level'), 'available_date' => phpgw::get_var('available_date_hidden'));
				$query_result  = rental_sounit::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
				$object_count = rental_sounit::get_instance()->get_count($search_for, $search_type, $filters);
				break;
			case 'orphan_units': // ... all units not included in any composite
				$filters = array('orphan_units' => true);
				$query_result = rental_sounit::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
				$object_count = rental_sounit::get_instance()->get_count($search_for, $search_type, $filters);
				break;
		}
		
	}

}
?>