<?php
	phpgw::import_class('booking.socommon');

	class booking_soaccount_code_set extends booking_socommon
	{

		function __construct()
		{
			$this->season_so = CreateObject('booking.soseason');
			$this->resource_so = CreateObject('booking.soresource');

			parent::__construct('bb_account_code_set', array(
				'id' => array('type' => 'int'),
				'name' => array('type' => 'string', 'required' => True, 'query' => True),
				'object_number' => array('type' => 'string', 'required' => True, 'nullable' => False), //c8 A
				'responsible_code' => array('type' => 'string', 'required' => True, 'nullable' => False), //c8 A
				'article' => array('type' => 'string', 'required' => True, 'nullable' => False), //c15 A
				'service' => array('type' => 'string', 'required' => True, 'nullable' => False), //c8 A
				'project_number' => array('type' => 'string', 'required' => True, 'nullable' => False), //c12 A, default 9
				'unit_number' => array('type' => 'string', 'required' => True, 'nullable' => False), //c12 A
				'unit_prefix' => array('type' => 'string', 'required' => True, 'nullable' => False), //c1 A (used for batch_id)
				'dim_4' => array('type' => 'string', 'required' => False, 'nullable' => True), //c8 A
				'dim_value_4' => array('type' => 'string', 'required' => False, 'nullable' => True), //c12 A
				'dim_value_5' => array('type' => 'string', 'required' => False, 'nullable' => True), //c12 A
				'invoice_instruction' => array('type' => 'string'), //c120 a
				'active' => array('type' => 'int'),
				)
			);
		}
	}