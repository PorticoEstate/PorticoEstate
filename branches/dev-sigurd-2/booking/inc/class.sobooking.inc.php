<?php
	phpgw::import_class('booking.socommon');
	
	class booking_sobooking extends booking_socommon
	{
		function __construct()
		{
			parent::__construct('bb_booking', 
				array(
					'id'			=> array('type' => 'int'),
					'active'		=> array('type' => 'int', 'required'=>true),
					'name'			=> array('type' => 'string', 'query' => true, 'required' => true),
					'group_id'		=> array('type' => 'int', 'required' => true),
					'from_'		=> array('type' => 'timestamp', 'required'=> true),
					'to_'		=> array('type' => 'timestamp', 'required'=> true),
					'season_id'		=> array('type' => 'int', 'required' => true),
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
					'season_name'	=> array('type' => 'string', 'query' => true,
						  'join' => array(
							'table' => 'bb_season',
							'fkey' => 'season_id',
							'key' => 'id',
							'column' => 'name'
					)),
					'audience' => array('type' => 'int', 'required' => true,
						  'manytomany' => array(
							'table' => 'bb_booking_targetaudience',
							'key' => 'booking_id',
							'column' => 'targetaudience_id'
					)),
					'agegroups' => array('type' => 'int', 'required' => true,
						  'manytomany' => array(
							'table' => 'bb_booking_agegroup',
							'key' => 'booking_id',
							'column' => array('agegroup_id', 'male', 'female')
					)),
					'resources' => array('type' => 'int', 'required' => true,
						  'manytomany' => array(
							'table' => 'bb_booking_resource',
							'key' => 'booking_id',
							'column' => 'resource_id'
					)),
				)
			);
		}

		protected function doValidate($entity, booking_errorstack $errors)
		{
			// FIXME: Validate: Season contains all resources
			// FIXME: Validate: Season from <= date, season to >= date
			// FIXME: Validate: booking from/to
			// Make sure to_ > from_
			$booking_id = $entity['id'] ? $entity['id'] : -1;
			$allocation_id = $entity['allocation_id'] ? $entity['allocation_id'] : -1;
			$from_ = new DateTime($entity['from_']);
			$to_ = new DateTime($entity['to_']);
			$start = $from_->format('Y-m-d H:i');
			$end = $to_->format('Y-m-d H:i');
			if($from_ > $to_)
			{
				$errors['from_'] = 'Invalid from date';
			}
			if($entity['resources'])
			{
				$rids = join(',', array_map("intval", $entity['resources']));
				// Check if we overlap with any existing event
				$this->db->query("SELECT e.id FROM bb_event e 
									WHERE e.active = 1 AND 
									e.id IN (SELECT event_id FROM bb_event_resource WHERE resource_id IN ($rids)) AND
									((e.from_ >= '$start' AND e.from_ < '$end') OR 
						 			 (e.to_ > '$start' AND e.to_ <= '$end') OR 
						 			 (e.from_ < '$start' AND e.to_ > '$end'))", __LINE__, __FILE__);
				if($this->db->next_record())
				{
					$errors['event'] = lang('Overlaps with existing event');
				}
				// Check if we overlap with any existing allocation
				$this->db->query("SELECT a.id FROM bb_allocation a 
									WHERE a.active = 1 AND a.id<>$allocation_id AND 
									a.id IN (SELECT allocation_id FROM bb_allocation_resource WHERE resource_id IN ($rids)) AND
									((a.from_ >= '$start' AND a.from_ < '$end') OR 
						 			 (a.to_ > '$start' AND a.to_ <= '$end') OR 
						 			 (a.from_ < '$start' AND a.to_ > '$end'))", __LINE__, __FILE__);
				if($this->db->next_record())
				{
					$errors['allocation'] = lang('Overlaps with existing allocation');
				}
			
				// Check if we overlap with any existing booking
				$this->db->query("SELECT b.id FROM bb_booking b 
									WHERE  b.active = 1 AND b.id<>$booking_id AND 
									b.id IN (SELECT booking_id FROM bb_booking_resource WHERE resource_id IN ($rids)) AND
									((b.from_ >= '$start' AND b.from_ < '$end') OR 
						 			 (b.to_ > '$start' AND b.to_ <= '$end') OR 
						 			 (b.from_ < '$start' AND b.to_ > '$end'))", __LINE__, __FILE__);
				if($this->db->next_record())
				{
					$errors['booking'] = lang('Overlaps with existing booking2');
				}
			}
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

		function resource_ids_for_allocations($allocations)
		{
			if(!$allocations)
			{
				return array();
			}
			$ids = join(',', array_map("intval", $allocations));
			$results = array();
			$this->db->query("SELECT resource_id FROM bb_allocation_resource WHERE allocation_id IN ($ids)", __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = $this->_unmarshal($this->db->f('resource_id', true), 'int');
			}
			return $results;
		}

		function allocation_ids_for_building($building_id, $start, $end)
		{
			$start = $start->format('Y-m-d H:i');
			$end = $end->format('Y-m-d H:i');
			$building_id = intval($building_id);
			$results = array();
			$this->db->query("SELECT bb_allocation.id AS id FROM bb_allocation JOIN bb_season ON (bb_allocation.season_id=bb_season.id AND bb_allocation.active=1) WHERE bb_season.building_id=$building_id AND ((bb_allocation.from_ >= '$start' AND bb_allocation.from_ < '$end') OR (bb_allocation.to_ > '$start' AND bb_allocation.to_ <= '$end') OR (bb_allocation.from_ < '$start' AND bb_allocation.to_ > '$end'))", __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = $this->_unmarshal($this->db->f('id', true), 'int');
			}
			return $results;
		}

		function booking_ids_for_building($building_id, $start, $end)
		{
			$start = $start->format('Y-m-d H:i');
			$end = $end->format('Y-m-d H:i');
			$building_id = intval($building_id);
			$results = array();
			$this->db->query("SELECT bb_booking.id AS id FROM bb_booking JOIN bb_season ON (bb_booking.season_id=bb_season.id AND bb_booking.active=1) WHERE bb_season.building_id=$building_id AND ((bb_booking.from_ >= '$start' AND bb_booking.from_ < '$end') OR (bb_booking.to_ > '$start' AND bb_booking.to_ <= '$end') OR (bb_booking.from_ < '$start' AND bb_booking.to_ > '$end'))", __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = $this->_unmarshal($this->db->f('id', true), 'int');
			}
			return $results;
		}

		function allocation_ids_for_resource($resource_id, $start, $end)
		{
			$start = $start->format('Y-m-d H:i');
			$end = $end->format('Y-m-d H:i');
			$resource_id = intval($resource_id);
			$results = array();
			$this->db->query("SELECT id FROM bb_allocation JOIN bb_allocation_resource ON (allocation_id=id AND resource_id=$resource_id) WHERE active=1 AND ((from_ >= '$start' AND from_ < '$end') OR (to_ > '$start' AND to_ <= '$end') OR (from_ < '$start' AND to_ > '$end'))", __LINE__, __FILE__);
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
			$this->db->query("SELECT id FROM bb_booking JOIN bb_booking_resource ON (booking_id=id AND resource_id=$resource_id) WHERE active=1 AND ((from_ >= '$start' AND from_ < '$end') OR (to_ > '$start' AND to_ <= '$end') OR (from_ < '$start' AND to_ > '$end'))", __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = $this->_unmarshal($this->db->f('id', true), 'int');
			}
			return $results;
		}

	}
