<?php
phpgw::import_class('rental.uicommon');

class rental_uinotification extends rental_uicommon
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
			case 'notifications':
					$result_objects = rental_notification::get_all(
						phpgw::get_var('startIndex'),
						phpgw::get_var('results'),
						phpgw::get_var('sort'),
						phpgw::get_var('dir'),
						phpgw::get_var('query'),
						phpgw::get_var('search_option'),
						array(
							//'account_id' => $GLOBALS['phpgw_info']['user']['account_id'], (show all notifications for each contract)
							'contract_id' => phpgw::get_var('contract_id')
						)
					);
					break;
				case 'notifications_for_user':
					$result_objects = rental_notification::get_workbench_notifications(
						phpgw::get_var('startIndex'),
						phpgw::get_var('results'),
						$GLOBALS['phpgw_info']['user']['account_id']
					);
					break;
		}
		
	}

}
?>