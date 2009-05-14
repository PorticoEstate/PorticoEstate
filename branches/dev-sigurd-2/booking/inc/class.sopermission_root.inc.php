<?php
	phpgw::import_class('booking.socommon');
	
	class booking_sopermission_root extends booking_socommon
	{
		
		function __construct()
		{
			parent::__construct('bb_permission_root', 
				array(
					'id'			=> array('type' => 'int'),
					'subject_id'	=> array('type' => 'int', 'required' => true),
					'subject_name'	=> array(
						'type' => 'string',
						'query' => true,
						'join' => array(
							'table' => 'phpgw_accounts',
							'fkey' => 'subject_id',
							'key' => 'account_id',
							'column' => 'account_lid'
						)
					)
				)
			);
		
			$this->account	= $GLOBALS['phpgw_info']['user']['account_id'];
		}
	}