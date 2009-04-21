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

		public function get_schedule($id, $buildingmodule, $resourcemodule)
		{
			$date = new DateTime(phpgw::get_var('date'));
			// Make sure $from is a monday
			if($date->format('w') != 1)
			{
				$date->modify('last monday');
			}
			$prev_date = clone $date;
			$next_date = clone $date;
			$prev_date->modify('-1 week');
			$next_date->modify('+1 week');
			$resource = $this->read_single($id);
			$resource['buildings_link'] = self::link(array('menuaction' => $buildingmodule . '.index'));
			$resource['building_link'] = self::link(array('menuaction' => $buildingmodule . '.show', 'id' => $resource['building_id']));
			$resource['resource_link'] = self::link(array('menuaction' => $resourcemodule . '.show', 'id' => $resource['id']));
			$resource['date'] = $date->format('Y-m-d');
			$resource['week'] = intval($date->format('W'));
			$resource['year'] = intval($date->format('Y'));
			$resource['prev_link'] = self::link(array('menuaction' => $resourcemodule . '.schedule', 'id' => $resource['id'], 'date'=> $prev_date->format('Y-m-d')));
			$resource['next_link'] = self::link(array('menuaction' => $resourcemodule . '.schedule', 'id' => $resource['id'], 'date'=> $next_date->format('Y-m-d')));
			for($i = 0; $i < 7; $i++)
			{
				$resource['days'][] = array('label' => $date->format('l').'<br/>'.$date->format('M d'), 'key' => $date->format('D'));
				$date->modify('+1 day');
			}
			return $resource;
		}
	}
