<?php
	phpgw::import_class('booking.bocommon');

	class booking_bodocumentation extends booking_bocommon
	{

		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.sodocumentation');
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
	}