<?php
	phpgw::import_class('booking.bocommon');
	
	require_once "schedule.php";
	
	class booking_bobooking extends booking_bocommon
	{
		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.sobooking');
			$this->allocation_so = CreateObject('booking.soallocation');
			$this->resource_so = CreateObject('booking.soresource');
		}

		/**
		 * Return a building's schedule for a given week in a YUI DataSource
		 * compatible format
		 * 
		 * @param int	$building_id
		 * @param $date 
		 *
		 * @return array containing values from $array for the keys in $keys.
		 */
		function building_schedule($building_id, $date)
		{
			$from = clone $date;
			$from->setTime(0, 0, 0);
			// Make sure $from is a monday
			if($from->format('w') != 1)
			{
				$from->modify('last monday');
			}
			$to = clone $from;
			$to->modify('+7 days');
			$allocation_ids = $this->so->allocation_ids_for_building($building_id, $from, $to);
			$allocations = $this->allocation_so->read(array('filters'=> array('id' => $allocation_ids)));
			$allocations = $allocations['results'];
			foreach($allocations as &$allocation)
			{
				$allocation['name'] = $allocation['organization_name'];
			}
			$booking_ids = $this->so->booking_ids_for_building($building_id, $from, $to);
			$bookings = $this->so->read(array('filters'=> array('id' => $booking_ids)));
			$bookings = $bookings['results'];
			$bookings = array_merge($allocations, $bookings);
			$resource_ids = $this->so->resource_ids_for_bookings($booking_ids);
			$resource_ids = array_merge($this->so->resource_ids_for_allocations($allocation_ids));
			$resources = $this->resource_so->read(array('filters' => array('id' => $resource_ids)));
			$resources = $resources['results'];
			$bookings = $this->_split_multi_day_bookings($bookings, $from, $to);
			$results = build_schedule_table($bookings, $resources);
			return array('total_records'=>count($results), 'results'=>$results);
		}

		/**
		 * Return a resource's schedule for a given week in a YUI DataSource
		 * compatible format
		 * 
		 * @param int	$resource_id
		 * @param $date 
		 *
		 * @return array containg values from $array for the keys in $keys.
		 */
		function resource_schedule($resource_id, $date)
		{
			$from = clone $date;
			$from->setTime(0, 0, 0);
			// Make sure $from is a monday
			if($from->format('w') != 1)
			{
				$from->modify('last monday');
			}
			$to = clone $from;
			$to->modify('+7 days');
			$resource = $this->resource_so->read_single($resource_id);
			$allocation_ids = $this->so->allocation_ids_for_resource($resource_id, $from, $to);
			$allocations = $this->allocation_so->read(array('filters'=> array('id' => $allocation_ids)));
			$allocations = $allocations['results'];
			foreach($allocations as &$allocation)
			{
				$allocation['name'] = $allocation['organization_name'];
			}
			$booking_ids = $this->so->booking_ids_for_resource($resource_id, $from, $to);
			$bookings = $this->so->read(array('filters'=> array('id' => $booking_ids)));
			$bookings = $bookings['results'];
			$bookings = array_merge($allocations, $bookings);
			$bookings = $this->_split_multi_day_bookings($bookings, $from, $to);
			$results = build_schedule_table($bookings, array($resource));
			return array('total_records'=>count($results), 'results'=>$results);
		}

		/**
		 * Split Multi-day bookings into separate single-day bookings
		**/
		function _split_multi_day_bookings($bookings, $t0, $t1)
		{
			if($t1->format('H:i') == '00:00')
				$t1->modify('-1 day');
			$new_bookings = array();
			foreach($bookings as $booking)
			{
				$from = new DateTime($booking['from_']);
				$to = new DateTime($booking['to_']);
				// Basic one-day booking
				if($from->format('Y-m-d') == $to->format('Y-m-d'))
				{
					$booking['date'] = $from->format('Y-m-d');
					$booking['wday']  = date_format(date_create($booking['date']), 'D');
					$booking['from_'] = $from->format('H:i');
					$booking['to_'] = $to->format('H:i');
					// We need to use 24:00 instead of 00:00 to sort correctly
					$booking['to_'] = $booking['to_'] == '00:00' ? '24:00' : $booking['to_'];
					$new_bookings[] = $booking;
				}
				// Multi-day booking
				else
				{
					$start = clone max($from, $t0);
					$end = clone min($to, $t1);
					$date = clone $start;
					do
					{
						$new_booking = $booking;
						$new_booking['date'] = $date->format('Y-m-d');
						$new_booking['wday']  = date_format($date, 'D');
						$new_booking['from_'] = '00:00';
						$new_booking['to_'] = '00:00';
						if($new_booking['date'] == $from->format('Y-m-d'))
						{
							$new_booking['from_'] = $from->format('H:i');
						}
						else if($new_booking['date'] == $to->format('Y-m-d'))
						{
							$new_booking['to_'] = $to->format('H:i');
						}
						// We need to use 24:00 instead of 00:00 to sort correctly
						$new_booking['to_'] = $new_booking['to_'] == '00:00' ? '24:00' : $new_booking['to_'];
						$new_bookings[] = $new_booking;
						if($date->format('Y-m-d') == $end->format('Y-m-d'))
							break;
						$date->modify('+1 day');
					}
					while(true);
				}
			}
			return $new_bookings;
		}
	}
