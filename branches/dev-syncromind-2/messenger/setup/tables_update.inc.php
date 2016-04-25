<?php
	$test[] = '0.8.1';

	function messenger_upgrade0_8_1()
	{
		return $GLOBALS['setup_info']['messenger']['currentver'] = '0.9.0';
	}
	$test[] = '0.9.0';

	function messenger_upgrade0_9_0()
	{
		$GLOBALS['phpgw']->locations->add('.', 'top', 'messenger', false);
		$GLOBALS['phpgw']->locations->add('.compose', 'compose messages to users', 'messenger', false);
		$GLOBALS['phpgw']->locations->add('.compose_groups', 'compose messages to groups', 'messenger', false);
		$GLOBALS['phpgw']->locations->add('.compose_global', 'compose global message', 'messenger', false);

		$GLOBALS['setup_info']['messenger']['currentver'] = '0.9.17.500';
		return $GLOBALS['setup_info']['messenger']['currentver'];
	}
