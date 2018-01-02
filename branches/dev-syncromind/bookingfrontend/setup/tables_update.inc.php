<?php
	/**
	 * Update Activitycalendar from v 0.1 to 0.1.1
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
