<?php
	phpgw::import_class('rental.socommon');

	abstract class activitycalendar_socommon extends rental_socommon
	{

		public function __construct()
		{
			parent::__construct();
		}
	
		protected function generate_secret( $length = 16 )
		{
			return bin2hex(random_bytes($length));
		}
	}