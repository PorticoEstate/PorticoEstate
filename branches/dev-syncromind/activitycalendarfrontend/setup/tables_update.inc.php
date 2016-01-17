<?php

	/**
	 * Update Activitycalendar from v 0.1 to 0.1.1
	 */

	$test[] = '0.1';
	function activitycalendarfrontend_upgrade0_1()
	{
		$c		 = createobject('phpgwapi.config', 'activitycalendarfrontend');
		$c->read();
		$c->config_data['anonymous_passwd'] = $c->config_data['anonymous_pass'];
		$c->save_repository(True);
		
		$GLOBALS['setup_info']['activitycalendarfrontend']['currentver'] = '0.1.1';
		return $GLOBALS['setup_info']['activitycalendarfrontend']['currentver'];
	}
	
