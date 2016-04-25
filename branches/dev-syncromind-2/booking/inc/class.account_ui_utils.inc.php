<?php

	class booking_account_ui_utils
	{

		public static function yui_accounts()
		{
			$query = phpgw::get_var('query');

			$account_info = $GLOBALS['phpgw']->accounts->get_list('accounts', 0, 'lid', '', $query, 20);
			$x = 0;

			$result = array();

			foreach ($account_info as $account)
			{
				$firstname = $account->firstname;
				$lastname = $account->lastname;
				$lastname AND $firstname .= ' ';
				$result[] = array(
					'name' => sprintf('%s (%s%s)', $account->lid, $firstname, $lastname),
					'id' => $account->id,
				);
			}

			$data = array(
				'ResultSet' => array(
					"totalResultsAvailable" => $GLOBALS['phpgw']->accounts->total,
					"Result" => $result
				)
			);
			return $data;
		}
	}