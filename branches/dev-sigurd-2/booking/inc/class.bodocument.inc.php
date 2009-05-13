<?php
	phpgw::import_class('booking.bocommon');
	
	abstract class booking_bodocument extends booking_bocommon
	{
		function __construct()
		{
			parent::__construct();
			$owningType = substr(get_class($this), 19);
			$this->so = CreateObject(sprintf('booking.sodocument_%s', $owningType));
		}
		
		public function get_files_root()
		{
			return $this->so->get_files_root();
		}
		
		public function get_files_path()
		{
			return $this->so->get_files_path();
		}
		
		public function get_categories()
		{
			return $this->so->get_categories();
		}
		
		public function read_parent($owner_id)
		{
			return $this->so->read_parent($owner_id);
		}
	}