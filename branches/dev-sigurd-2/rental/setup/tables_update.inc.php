<?php
	$test[] = '0.0.1';
	function booking_upgrade0_0_1()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		/*$GLOBALS['phpgw_setup']->oProc->DropTable('phpgw_rental');*/
		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'rental_objects', array(
				'fd' => array(
					'id' => array('type' => 'auto', 'nullable' => false),
					'name' => array('type' => 'varchar','precision' => '50','nullable' => False),
					'homepage' => array('type' => 'varchar','precision' => '50','nullable' => False)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['booking']['currentver'] = '0.0.2';
			return $GLOBALS['setup_info']['booking']['currentver'];
		}
	}
