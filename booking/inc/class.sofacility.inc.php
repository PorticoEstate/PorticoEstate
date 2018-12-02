<?php
	phpgw::import_class('booking.socommon');

	class booking_sofacility extends booking_socommon
	{

		function __construct()
		{
			parent::__construct('bb_facility', array(
				'id' => array('type' => 'int'),
				'name' => array('type' => 'string', 'required' => true, 'query' => true),
				'active' => array('type' => 'int', 'required' => true),
				)
			);
		}


		protected function doValidate( $entity, booking_errorstack $errors )
		{
			set_time_limit(300);
			if (count($errors) > 0)
			{
				// Basic validation failed
				return;
			}
		}

	}
