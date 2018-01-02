<?php

	$test[] = '0.1.1';

	function eventplannerfrontend_upgrade0_1_1()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw']->locations->delete('eventplannerfrontend', '.resource');
		$GLOBALS['phpgw']->locations->add('.events', 'events', 'eventplannerfrontend', $allow_grant = true);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['eventplannerfrontend']['currentver'] = '0.1.2';
		}
		return $GLOBALS['setup_info']['eventplannerfrontend']['currentver'];
	}
