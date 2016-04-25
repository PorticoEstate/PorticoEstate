<?php
	phpgw::import_class('booking.bocommon');

	class booking_bomassbooking extends booking_bocommon
	{

		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.somassbooking');
		}

		public function get_schedule( $id, $module )
		{
			$date = new DateTime(phpgw::get_var('date'));
			// Make sure $from is a monday
			if ($date->format('w') != 1)
			{
				$date->modify('last monday');
			}
			$prev_date = clone $date;
			$next_date = clone $date;
			$prev_date->modify('-1 week');
			$next_date->modify('+1 week');
			$building = $this->read_single($id);

			$building['buildings_link'] = self::link(array('menuaction' => $module . '.index'));
			$building['building_link'] = self::link(array('menuaction' => 'booking.uibuilding.show',
					'id' => $building['id']));
			$building['date'] = $date->format('Y-m-d');
			$building['week'] = intval($date->format('W'));
			$building['year'] = intval($date->format('Y'));
			$building['prev_link'] = self::link(array('menuaction' => $module . '.schedule',
					'id' => $building['id'], 'date' => $prev_date->format('Y-m-d')));
			$building['next_link'] = self::link(array('menuaction' => $module . '.schedule',
					'id' => $building['id'], 'date' => $next_date->format('Y-m-d')));
			for ($i = 0; $i < 7; $i++)
			{
				$building['days'][] = array('label' => sprintf('%s<br/>%s %s', lang($date->format('l')), lang($date->format('M')), $date->format('d')),
					'key' => $date->format('D'));
				$date->modify('+1 day');
			}
			return $building;
		}
	}