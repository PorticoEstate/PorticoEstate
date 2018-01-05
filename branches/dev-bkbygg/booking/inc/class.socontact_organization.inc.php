<?php
	phpgw::import_class('booking.socommon');

	class booking_socontact_organization extends booking_socommon
	{

		function __construct()
		{
			parent::__construct('bb_organization_contact', array(
				'id' => array('type' => 'int'),
				'organization_id' => array('type' => 'int', 'required' => True),
				'name' => array('type' => 'string', 'required' => True, 'query' => True),
				'ssn' => array('type' => 'string', 'sf_validator' => createObject('booking.sfValidatorNorwegianSSN')),
				'email' => array('type' => 'string', 'sf_validator' => createObject('booking.sfValidatorEmail', array(), array(
						'invalid' => '%field% contains an invalid email'))),
				'phone' => array('type' => 'string'),
				)
			);
			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];
		}
	}