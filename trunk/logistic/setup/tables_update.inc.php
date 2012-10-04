<?php
	 /* Update Logistic from v 0.0.1 to 0.0.2
	  * Add column 'description' to table activity
	  */

	$test[] = '0.0.1';
	function logistic_upgrade0_0_1()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('lg_activity','description',array(
			'type' => 'text',
			'nullable' => True
		));

		$GLOBALS['setup_info']['logistic']['currentver'] = '0.0.2';
		return $GLOBALS['setup_info']['logistic']['currentver'];
	}

	/* Update Logistic from v 0.0.2 to 0.0.3
	* Add locations
	*/

	$test[] = '0.0.2';
	function logistic_upgrade0_0_2()
	{
		$GLOBALS['phpgw']->locations->add('.', 'Topp', 'logistic');
		$GLOBALS['phpgw']->locations->add('.project', 'Prosjekt', 'logistic');
		$GLOBALS['phpgw']->locations->add('.activity', 'Aktivitet', 'logistic');

		$GLOBALS['setup_info']['logistic']['currentver'] = '0.0.3';
		return $GLOBALS['setup_info']['logistic']['currentver'];
	}
