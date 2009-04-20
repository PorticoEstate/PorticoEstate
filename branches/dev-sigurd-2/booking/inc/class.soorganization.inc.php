<?php
	phpgw::import_class('booking.socommon');
	
	class booking_soorganization extends booking_socommon
	{
		function __construct()
		{
			parent::__construct('bb_organization', 
				array(
					'id'		=> array('type' => 'int'),
					'name'		=> array('type' => 'string', 'required' => True, 'query' => True),
					'homepage'	=> array('type' => 'string', 'required' => True, 'qyery' => True)
				)
			);
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
		}
	}
