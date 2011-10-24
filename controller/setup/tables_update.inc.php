<?php

	 /* Update Controller from v 0.1 to 0.1.1
	 */

	$test[] = '0.1';
	function controller_upgrade0_1()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_procedure','procedure_id',array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => True
		));
		
		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_procedure','revision_no',array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => True
		));
		
		$GLOBALS['phpgw_setup']->oProc->AddColumn('controller_procedure','revision_date',array(
			'type' => 'int',
			'precision' => 8,
			'nullable' => True
		));
		
		$GLOBALS['setup_info']['controller']['currentver'] = '0.1.1';
		return $GLOBALS['setup_info']['controller']['currentver'];
	}
	
?>