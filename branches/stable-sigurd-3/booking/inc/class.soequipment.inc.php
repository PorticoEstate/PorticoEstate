<?php
	phpgw::import_class('booking.socommon');
	
	class booking_soequipment extends booking_socommon
	{
		function __construct()
		{
			parent::__construct('bb_equipment', 
				array(
					'id'			=> array('type' => 'int'),
					'resource_id'	=> array('type' => 'int', 'required' => true),
					'name'			=> array('type' => 'string', 'query' => true, 'required' => true),
					'description'	=> array('type' => 'string', 'query' => true, 'required' => true),
					'resource_name'	=> array('type' => 'string',
						  'query' => true,
						  'join' => array(
							'table' => 'bb_resource',
							'fkey' => 'resource_id',
							'key' => 'id',
							'column' => 'name'
						)
					)
				)
			);
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
		}
	}
