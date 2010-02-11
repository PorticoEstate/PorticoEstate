<?php

	/**
	 * Update Rental from v 0.0.27 to 0.1.0
	 */

	$test[] = '0.0.27';
	function rental_upgrade0_0_27()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('rental_party','org_enhet_id', array ('type' => 'int','precision' => 8, 'nullable' => true));
		
		$GLOBALS['setup_info']['rental']['currentver'] = '0.1.0';
		return $GLOBALS['setup_info']['rental']['currentver'];
	}
?>