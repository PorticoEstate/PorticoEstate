<?php
	phpgw::import_class('booking.bocommon');
	
	class booking_boequipment extends booking_bocommon
	{
		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.soequipment');
		}
		public function populate_json_data($module) {
			$resources = $this->read();
			foreach($resources['results'] as &$resource)
			{
				$resource['link'] = $this->link(array('menuaction' => $module.'.show', 'id' => $resource['id']));
			}
			$data = array
			(
				'ResultSet' => array(
					"totalResultsAvailable" => $resources['total_records'], 
					"Result" => $resources['results']
				)
			);
			return $data;
		}
	}
