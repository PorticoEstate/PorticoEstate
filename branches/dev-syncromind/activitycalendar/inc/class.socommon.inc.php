<?php
	phpgw::import_class('rental.socommon');

	abstract class activitycalendar_socommon extends rental_socommon
	{

		public function __construct()
		{
			parent::__construct();
		}
	
		protected function generate_secret( $length = 10 )
		{
			return substr(base64_encode(rand(1000000000, 9999999999)), 0, $length);
		}
	}