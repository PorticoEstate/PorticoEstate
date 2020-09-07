<?php
	/**
	 * Update Bookingfrontend from v 0.1 to 0.1.1
	 */
	$test[] = '0.1';

	function bookingfrontend_upgrade0_1()
	{
		$c = createobject('phpgwapi.config', 'bookingfrontend');
		$c->read();
		$c->config_data['anonymous_user'] = 'bookingguest';
		$c->save_repository(True);

		$GLOBALS['setup_info']['bookingfrontend']['currentver'] = '0.1.1';
		return $GLOBALS['setup_info']['bookingfrontend']['currentver'];
	}


	/**
	 * Update Bookingfrontend from v 0.1.1 to 1.0
	 */

	$test[] = '0.1.1';
	function bookingfrontend_upgrade0_1_1()
	{
		$sql = "UPDATE phpgw_preferences SET preference_json=jsonb_set(preference_json, '{template_set}', '\"bookingfrontend\"', true)"
			. " WHERE preference_json->>'template_set' = 'aalesund'";
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);

		$GLOBALS['setup_info']['bookingfrontend']['currentver'] = '1.0';
		return $GLOBALS['setup_info']['bookingfrontend']['currentver'];
	}

