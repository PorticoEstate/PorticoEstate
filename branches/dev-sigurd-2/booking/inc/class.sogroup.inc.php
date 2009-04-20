<?php
	phpgw::import_class('booking.socommon');
	
	class booking_sogroup extends booking_socommon
	{
		function __construct()
		{
			parent::__construct('bb_group', 
				array(
					'id'			=> array('type' => 'int'),
					'organization_id'	=> array('type' => 'int', 'required' => true),
					'name'			=> array('type' => 'string', 'query' => true, 'required' => true),
					'organization_name'	=> array('type' => 'string',
						  'query' => true,
						  'join' => array(
							'table' => 'bb_organization',
							'fkey' => 'organization_id',
							'key' => 'id',
							'column' => 'name'
						))
				)
			);
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
		}
	}
