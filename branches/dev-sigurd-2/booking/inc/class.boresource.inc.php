<?php
	phpgw::import_class('booking.bocommon');
	
	class booking_boresource extends booking_bocommon
	{
		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.soresource');
			$this->activity_so = CreateObject('booking.soactivity');
		}
		
		function fetch_activities()
		{
			return $this->activity_so->read(array());
		}

		function get_activity_name($id)
		{
			$result = $this->activity_so->read_single($id);
			return $result['name'];
		}

		public function populate_grid_data($menuaction)
		{
			$resources = $this->read();
			foreach($resources['results'] as &$resource)
			{
				$resource['link']        = $this->link(array('menuaction' => $menuaction, 'id' => $resource['id']));
				$resource['activity_id'] = $this->get_activity_name($resource['activity_id']);
			}
			$data = array(
				 'ResultSet' => array(
					"totalResultsAvailable" => $resources['total_records'], 
					"Result"                => $resources['results'],
				)
			);
			return $data;
		}

	}
