<?php
	phpgw::import_class('booking.socommon');
	
	class booking_socontactperson extends booking_socommon
	{
		function __construct()
		{
			parent::__construct('bb_contact_person', 
				array(
					  'id' => array('type' => 'auto', 'nullable' => false),
					  'ssn' => array('type' => 'varchar', 'precision' => '12', 'nullable' => True,),
					  'name' => array('type' => 'varchar','precision' => '50','nullable' => False, 'query' => true,),
					  'homepage' => array('type' => 'varchar','precision' => '50','nullable' => False),
					  'phone' => array('type' => 'varchar','precision' => '50','nullable' => False, 'default'=>''),
					  'email' => array('type' => 'varchar','precision' => '50','nullable' => False, 'default'=>''),
					  'description' => array('type' => 'varchar','precision' => '1000','nullable' => False, 'default'=>''),
				)
			);
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
		}
	}

