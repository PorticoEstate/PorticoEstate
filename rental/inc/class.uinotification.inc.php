<?php
	phpgw::import_class('rental.uicommon');
	phpgw::import_class('rental.socontract');
	phpgw::import_class('rental.sonotification');
	phpgw::import_class('rental.soworkbench_notification');

	/**
	 * Controller class for notifications (both contract and workbench notifications)
	 */
	class rental_uinotification extends rental_uicommon
	{

		public $public_functions = array
			(
			'query' => true,
			'delete_notification' => true,
			'dismiss_notification' => true,
			'dismiss_notification_for_all' => true
		);

		public function query()
		{
			if ($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > 0)
			{
				$user_rows_per_page = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$user_rows_per_page = 10;
			}

			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');

			$start_index = phpgw::get_var('start', 'int', 'REQUEST', 0);
			$num_of_objects = (phpgw::get_var('length', 'int') <= 0) ? $user_rows_per_page : phpgw::get_var('length', 'int');
			$sort_field = ($columns[$order[0]['column']]['data']) ? $columns[$order[0]['column']]['data'] : 'date';
			$sort_ascending = ($order[0]['dir'] == 'desc') ? false : true;
			// Form variables
			$search_for = $search['value'];
			$search_type = phpgw::get_var('search_option', 'string', 'REQUEST', 'all');

			/* $start_index	= phpgw::get_var('startIndex', 'int');
			  $num_of_objects	= phpgw::get_var('results', 'int', 'GET', 10);
			  $sort_field		= phpgw::get_var('sort');
			  $sort_ascending	= phpgw::get_var('dir') == 'desc' ? false : true;
			  // Form variables
			  $search_for 	= phpgw::get_var('query');
			  $search_type	= phpgw::get_var('search_option'); */

			// Create an empty result set
			$result_objects = array();
			$result_count = 0;

			//Retrieve a contract identifier and load corresponding contract
			$contract_id = phpgw::get_var('contract_id');

			//Retrieve the type of query and perform type specific logic
			$query_type = phpgw::get_var('type');
			switch ($query_type)
			{
				case 'notifications':
					$filters = array('contract_id' => $contract_id);
					$result_objects = rental_sonotification::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
					$result_count = rental_sonotification::get_instance()->get_count($search_for, $search_type, $filters);
					break;
				case 'notifications_for_user':
					$filters = array('account_id' => $GLOBALS['phpgw_info']['user']['account_id']);
					$result_objects = rental_soworkbench_notification::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
					$result_count = rental_soworkbench_notification::get_instance()->get_count($search_for, $search_type, $filters);
					break;
			}


			//Serialize the contracts found
			$rows = array();
			foreach ($result_objects as $result)
			{
				if (isset($result))
				{
					$rows[] = $result->serialize();
				}
			}

			$result_data = array('results' => $rows);
			$result_data['total_records'] = $result_count;
			$result_data['draw'] = $draw;

			return $this->jquery_results($result_data);
		}

		/**
		 * Add data for context menu
		 *
		 * @param $value pointer to
		 * @param $key ?
		 * @param $params [type of query, editable]
		 */
		public function add_actions( &$value, $key, $params )
		{
			$value['ajax'] = array();
			$value['actions'] = array();
			$value['labels'] = array();

			$type = $params[0];
			$editable = $params[1];

			switch ($type)
			{
				case 'notifications':
					if ($editable)
					{
						$value['ajax'][] = true;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uinotification.delete_notification',
								'id' => $value['id'], 'contract_id' => $value['contract_id'])));
						$value['labels'][] = lang('delete');
					}
					break;
				case 'notifications_for_user':
					$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.view',
							'id' => $value['contract_id'])));
					$value['labels'][] = lang('view_contract');
					$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.edit',
							'id' => $value['contract_id'])));
					$value['labels'][] = lang('edit_contract');
					$value['ajax'][] = true;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uinotification.dismiss_notification',
							'id' => $value['id'])));
					$value['labels'][] = lang('remove_from_workbench');
					$value['ajax'][] = true;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uinotification.dismiss_notification_for_all',
							'id' => $value['originated_from'], 'contract_id' => $value['contract_id'])));
					$value['labels'][] = lang('remove_from_all_workbenches');
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
			$list_notification_id = phpgw::get_var('id');
			$contract_id = (int)phpgw::get_var('contract_id');

			$contract = rental_socontract::get_instance()->get_single($contract_id);
			$message = array();
			if ($contract->has_permission(PHPGW_ACL_EDIT))
			{
				//rental_sonotification::get_instance()->delete_notification($notification_id);
				//return true;
				foreach ($list_notification_id as $notification_id)
				{
					$result = rental_sonotification::get_instance()->delete_notification($notification_id);
					if ($result)
					{
						$message['message'][] = array('msg' => 'notification ' . $notification_id . ' ' . lang('has been removed'));
					}
					else
					{
						$message['error'][] = array('msg' => 'notification ' . $notification_id . ' ' . lang('not removed'));
					}
				}
			}
			return $message;
		}

		/**
		 * Visible controller function for dismissing a single workbench notification
		 *
		 * @return true on success/false otherwise
		 */
		public function dismiss_notification()
		{
			$list_notification_id = phpgw::get_var('id');
			//$notification_id = (int)phpgw::get_var('id');
			//$result = rental_soworkbench_notification::get_instance()->dismiss_notification($notification_id,strtotime('now'));
			$message = array();
			foreach ($list_notification_id as $notification_id)
			{
				$result = rental_soworkbench_notification::get_instance()->dismiss_notification($notification_id, strtotime('now'));
				if ($result)
				{
					$message['message'][] = array('msg' => 'notification ' . $notification_id . ' ' . lang('has been removed'));
				}
				else
				{
					$message['error'][] = array('msg' => 'notification ' . $notification_id . ' ' . lang('not removed'));
				}
			}

			return $message;
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
			//$notification_id = (int)phpgw::get_var('id');
			$list_notification_id = phpgw::get_var('id');
			$list_contract_id = phpgw::get_var('contract_id');
			//$contract_id = (int)phpgw::get_var('contract_id');
			$contract = rental_socontract::get_instance()->get_single($list_contract_id[0]);

			$message = array();
			if ($contract->has_permission(PHPGW_ACL_EDIT))
			{
				//$result = rental_soworkbench_notification::get_instance()->dismiss_notification_for_all($notification_id);
				foreach ($list_notification_id as $notification_id)
				{
					$result = rental_soworkbench_notification::get_instance()->dismiss_notification_for_all($notification_id);
					if ($result)
					{
						$message['message'][] = array('msg' => 'notification ' . $notification_id . ' ' . lang('has been removed'));
					}
					else
					{
						$message['error'][] = array('msg' => 'notification ' . $notification_id . ' ' . lang('not removed'));
					}
				}
			}

			return $message;
		}
	}