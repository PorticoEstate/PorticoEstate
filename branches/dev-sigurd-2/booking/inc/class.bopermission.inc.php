<?php
	phpgw::import_class('booking.bocommon');

	abstract class booking_bopermission extends booking_bocommon
	{
		function __construct()
		{
			parent::__construct();
			$object_type = substr(get_class($this), 21);
			$this->so = CreateObject(sprintf('booking.sopermission_%s', $object_type));
		}
	
		public function get_roles()
		{
			return $this->so->get_roles();
		}
	
		public function read_object($object_id)
		{
			return $this->so->read_object($object_id);
		}
	}