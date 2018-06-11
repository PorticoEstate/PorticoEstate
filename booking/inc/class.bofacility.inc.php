<?php
	phpgw::import_class('booking.bocommon_global_manager_authorized');

	class booking_bofacility extends booking_bocommon_global_manager_authorized
	{

		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.sofacility');
		}


		public function populate_grid_data ($params)
		{
			$facilities = $this->so->read($params);

			$data = array(
				'total_records' => $facilities['total_records'],
				'start' => $facilities['start'],
				'sort' => $facilities['sort'],
				'dir' => $facilities['dir'],
				'results' => $facilities['results'],
			);

			return $data;
		}
		
	}
