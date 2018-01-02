<?php
	phpgw::import_class('booking.socommon');

	class booking_soagegroup extends booking_socommon
	{

		function __construct()
		{
			parent::__construct('bb_agegroup', array(
				'id' => array('type' => 'int'),
				'activity_id' => array('type' => 'int',
					'required' => true),
				'activity_name' => array('type' => 'string',
					'query' => true,
					'join' => array(
						'table' => 'bb_activity',
						'fkey' => 'activity_id',
						'key' => 'id',
						'column' => 'name'
					)),
				'name' => array('type' => 'string',
					'query' => true,
					'required' => true),
				'sort' => array('type' => 'int',
					'required' => true),
				'description' => array('type' => 'string',
					'query' => true,
					'required' => false),
				'active' => array('type' => 'int')
				)
			);
			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];
		}
	}