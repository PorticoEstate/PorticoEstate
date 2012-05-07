<?php

	// Upgrade to 0.1.1 because we have changed dependency requirements. Only works with booking v 0.2.14 (or higher) now.
	$test[] = '0.1';
	function bookingfrontend_upgrade0_1()
	{
		$GLOBALS['setup_info']['bookingfrontend']['currentver'] = '0.1.1';
		return $GLOBALS['setup_info']['bookingfrontend']['currentver'];
	}
