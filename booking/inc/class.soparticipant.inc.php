<?php
	phpgw::import_class('booking.socommon');

	class booking_soparticipant extends booking_socommon
	{

		function __construct()
		{
			parent::__construct('bb_participant_log', array(
				'id' => array('type' => 'int'),
				'reservation_type' => array('type' => 'string', 'required' => True, 'nullable' => False),
				'reservation_id' => array('type' => 'int', 'required' => True, 'nullable' => False),
				'from_' => array('type' => 'timestamp', 'required' => true),
				'to_' => array('type' => 'timestamp', 'required' => true),
				'phone' => array('type' => 'string', 'query' => true, 'required' => true),
				'email' => array('type' => 'string', 'sf_validator' => createObject('booking.sfValidatorEmail', array(), array(
						'invalid' => '%field% is invalid'))),
				)
			);
			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];
		}

		protected function preValidate( &$entity )
		{
		}


		protected function doValidate( $entity, booking_errorstack $errors )
		{
		}

	}