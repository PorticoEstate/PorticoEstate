<?php
	phpgw::import_class('booking.socommon');
	
	class booking_sobooking extends booking_socommon
	{
		function __construct()
		{
			parent::__construct('bb_booking', 
				array(
					'id'			=> array('type' => 'int'),
					'name'			=> array('type' => 'string', 'query' => true, 'required' => true),
					'group_id'		=> array('type' => 'int', 'required' => true),
					'group_name'	=> array('type' => 'string',
						  'query' => true,
						  'join' => array(
							'table' => 'bb_group',
							'fkey' => 'group_id',
							'key' => 'id',
							'column' => 'name'
					)),
					'building_id'	=> array('type' => 'string',
						  'join' => array(
							'table' => 'bb_season',
							'fkey' => 'season_id',
							'key' => 'id',
							'column' => 'building_id'
					)),
					'season_id'		=> array('type' => 'int', 'required' => 'true'),
					'season_name'	=> array('type' => 'string',
						  'query' => true,
						  'join' => array(
							'table' => 'bb_season',
							'fkey' => 'season_id',
							'key' => 'id',
							'column' => 'name'
					)),
					'resources' => array('type' => 'int', 'required' => true,
						  'manytomany' => array(
							'table' => 'bb_booking_resource',
							'key' => 'booking_id',
							'column' => 'resource_id'
					)),
					'from_'		=> array('type' => 'string', 'required'=> true),
					'to_'		=> array('type' => 'string', 'required'=> true)
				)
			);
		}

		function validate($entity)
		{
			$errors = parent::validate($entity);
			// FIXME: Validate: Season contains all resources
			// FIXME: Validate: Season from <= date, season to >= date
			// FIXME: Validate: booking from/to
			// Make sure to_ > from_
			if(!$errors)
			{
				$from_ = date_parse($entity['from_']);
				$to_ = date_parse($entity['to_']);
				if($from_ > $to_)
				{
					$errors['from_'] = 'Invalid from date';
				}
			}
			return $errors;
		}

		function resource_ids_for_bookings($bookings)
		{
			if(!$bookings)
			{
				return array();
			}
			$ids = join(',', array_map("intval", $bookings));
			$results = array();
			$this->db->query("SELECT resource_id FROM bb_booking_resource WHERE booking_id IN ($ids)", __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = $this->_unmarshal($this->db->f('resource_id', true), 'int');
			}
			return $results;
		}

		function booking_ids_for_building($building_id, $start, $end)
		{
			$start = $start->format('Y-m-d H:i');
			$end = $end->format('Y-m-d H:i');
			$building_id = intval($building_id);
			$results = array();
			$this->db->query("SELECT bb_booking.id AS id FROM bb_booking JOIN bb_season ON (bb_booking.season_id=bb_season.id) WHERE bb_season.building_id=$building_id AND ((bb_booking.from_ >= '$start' AND bb_booking.from_ < '$end') OR (bb_booking.to_ > '$start' AND bb_booking.to_ <= '$end') OR (bb_booking.from_ < '$start' AND bb_booking.to_ > '$end'))", __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = $this->_unmarshal($this->db->f('id', true), 'int');
			}
			return $results;
		}

		function booking_ids_for_resource($resource_id, $start, $end)
		{
			$start = $start->format('Y-m-d H:i');
			$end = $end->format('Y-m-d H:i');
			$resource_id = intval($resource_id);
			$results = array();
			$this->db->query("SELECT id FROM bb_booking JOIN bb_booking_resource ON (booking_id=id AND resource_id=$resource_id) WHERE ((from_ >= '$start' AND from_ < '$end') OR (to_ > '$start' AND to_ <= '$end') OR (from_ < '$start' AND to_ > '$end'))", __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = $this->_unmarshal($this->db->f('id', true), 'int');
			}
			return $results;
		}

	}
