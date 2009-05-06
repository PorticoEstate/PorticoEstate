<?php
	phpgw::import_class('booking.bocommon');
	
	class booking_boagegroup extends booking_bocommon
	{
		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.soagegroup');
		}
		
		function fetch_age_groups()
		{
			return $this->so->read(array('filters'=>array('active'=>'1')));
		}
	}
