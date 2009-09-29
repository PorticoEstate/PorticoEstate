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
		
		//Retrieve a contract identifier and load corresponding contract
		$contract_id = phpgw::get_var('contract_id');
		if(isset($contract_id))
		{
			$contract = rental_socontract::get_instance()->get_single($contract_id);
		}
		
		//Retrieve the type of query and perform type specific logic
		$query_type = phpgw::get_var('type');
		switch($query_type)
		{
			case 'notifications':
				$filters = array('contract_id' => phpgw::get_var('contract_id'));
				break;
			case 'notifications_for_user':
				$filters = array('account_id' => $GLOBALS['phpgw_info']['user']['account_id']);
				break;
		}
		
		$result_objects = rental_socontract::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
		$result_count = rental_socontract::get_instance()->get_count($search_for, $search_type, $filters);
		
		//Serialize the contracts found
		$rows = array();
		foreach ($result_objects as $result) {
			if(isset($result))
			{
				if($result->has_permission(PHPGW_ACL_READ)) // check for read permission
				{
					$rows[] = $result->serialize();
				}
			}
		}
		
		//Add context menu columns (actions and labels)
		array_walk($rows, array($this, 'add_actions'), array($type,isset($contract) ? $contract->serialize() : null ));

		//Build a YUI result from the data
		$result_data = array('results' => $rows, 'total_records' => $result_count);
		return $this->yui_results($result_data, 'total_records', 'results');
	}
	
	/**
	 * Add data for context menu
	 *
	 * @param $value pointer to
	 * @param $key ?
	 * @param $params [type of query, editable]
	 */
	public function add_actions(&$value, $key, $params)
	{
		$value['ajax'] = array();
		$value['actions'] = array();
		$value['labels'] = array();

		$type = $params[0];
		$serialized_contract = $params[1];
		
		switch($type)
		{
			case 'notifications':
				if($serialized_contract['permissions'][PHPGW_ACL_DELETE])
				{
					$value['ajax'][] = true;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uinotification.delete_notification', 'id' => $value['id'], 'contract_id' => $value['contract_id'])));
					$value['labels'][] = lang('delete');
				}
				break;
			case 'notifications_for_user':
				if($serialized_contract['permissions'][PHPGW_ACL_EDIT])
				{
					$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.edit', 'id' => $value['contract_id'])));
					$value['labels'][] = lang('edit_contract');
				}
				
				$value['ajax'][] = true;
				$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uinotification.dismiss_notification', 'id' => $value['id'])));
				$value['labels'][] = lang('remove_from_workbench');
				
				if($serialized_contract['permissions'][PHPGW_ACL_DELETE])
				{
					$value['ajax'][] = true;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uinotification.dismiss_notification_for_all', 'id' => $value['originated_from'], 'contract_id' => $value['contract_id'])));
					$value['labels'][] = lang('remove_from_all_workbenches');
				}
				break;
		}
	}

	/**
	 * Visible controller function for deleting a contract notification.
	 * 
	 * @return true on success/false otherwise
	 */
	public function delete_notification()
	{
		$notification_id = (int)phpgw::get_var('id');
		$contract_id = (int)phpgw::get_var('contract_id');
		$contract = rental_contract::get($contract_id);
		if($contract->has_permission(PHPGW_ACL_EDIT))
		{	
			rental_notification::delete_notification($notification_id);
			return true;
		}
		return false;
	}

	
	/**
	 * Visible controller function for dismissing a single workbench notification
	 * 
	 * @return true on success/false otherwise
	 */
	public function dismiss_notification()
	{
		$notification_id = (int)phpgw::get_var('id');
		
		//TODO: should we check to see if the notification exists on the current users workbench? 
		
		rental_notification::dismiss_notification($notification_id,strtotime('now'));
	}
	
	/**
	 * Visible controller function for dismissing all workbench notifications originated 
	 * from a given notification. The user must have EDIT privileges on a contract for
	 * this action.
	 * 
	 * @return true on success/false otherwise
	 */
	public function dismiss_notification_for_all()
	{
		//the source notification
		$notification_id = (int)phpgw::get_var('id');
		$contract_id = (int)phpgw::get_var('contract_id');
		$contract = rental_contract::get($contract_id);

		//TODO: should we check to see if the notification exists on the current users workbench? 
					
		if($contract->has_permission(PHPGW_ACL_EDIT))
		{
			rental_notification::dismiss_notification_for_all($notification_id);
			return true;
		}
		return false;
		
	}
}
?>