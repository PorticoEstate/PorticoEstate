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
					'allocation_id'	=> array('type' => 'int', 'required' => false),
					'activity_id'	=> array('type' => 'int', 'required' => true),
					'group_id'		=> array('type' => 'int', 'required' => true),
					'from_'		=> array('type' => 'timestamp', 'required'=> true),
					'to_'		=> array('type' => 'timestamp', 'required'=> true),
					'season_id'		=> array('type' => 'int', 'required' => true),
					'cost'		=> array('type' => 'decimal', 'required' => true),
					'completed'	=> array('type' => 'int', 'required' => true, 'nullable' => false, 'default' => '0'),
					'activity_name'	=> array('type' => 'string',
						  'query' => true,
						  'join' => array(
							'table' => 'bb_activity',
							'fkey' => 'activity_id',
							'key' => 'id',
							'column' => 'name'
					)),
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
							'column' => array('agegroup_id' => array('type' => 'int', 'required' => true), 'male' => array('type' => 'int', 'required' => true), 'female' => array('type' => 'int', 'required' => true)),
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
			// FIXME: Validate: booking from/to
			
			if (count($errors) > 0) { return; /*Basic validation failed*/ }
			
			 if (false == (boolean)intval($entity['active'])) {
				return; //Don't care about if booking is within necessary boundaries if dealing with inactivated entity
			}
			
			$booking_id = $entity['id'] ? $entity['id'] : -1;
			$allocation_id = $entity['allocation_id'] ? $entity['allocation_id'] : -1;
			$from_ = new DateTime($entity['from_']);
			$to_ = new DateTime($entity['to_']);
			$start = $from_->format('Y-m-d H:i');
			$end = $to_->format('Y-m-d H:i');
			
			if(strtotime($start) > strtotime($end)) {
				$errors['from_'] = 'Invalid from date';
				return; //No need to continue validation if dates are invalid
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
					$errors['booking'] = lang('Overlaps with existing booking');
				}
				if($allocation_id != -1)
				{
					$this->db->query("SELECT a.id FROM bb_allocation a 
										WHERE a.active = 1 AND a.id = $allocation_id AND 
										(a.from_ <= '$start' AND a.to_ >= '$end')", __LINE__, __FILE__);
					if(!$this->db->next_record())
					{
						$errors['booking'] = lang("This booking is outside the organization's allocated time");
					}
					$this->db->query("SELECT count(1) FROM bb_allocation_resource 
									WHERE allocation_id = $allocation_id AND resource_id IN ($rids)", __LINE__, __FILE__);
					$this->db->next_record();
					if($this->db->f('count', true) != count($entity['resources']))
					{
						$errors['booking'] = lang("The booking uses resources not in the containing allocation");
					}
				}		
			}
			
			if (!CreateObject('booking.soseason')->timespan_within_season($entity['season_id'], $from_, $to_)) {
				$errors['season_boundary'] = lang("This booking is not within the selected season");
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

		function resource_ids_for_events($events)
		{
			if(!$events)
			{
				return array();
			}
			$ids = join(',', array_map("intval", $events));
			$results = array();
			$this->db->query("SELECT resource_id FROM bb_event_resource WHERE event_id IN ($ids)", __LINE__, __FILE__);
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

		function event_ids_for_building($building_id, $start, $end)
		{
			$start = $start->format('Y-m-d H:i');
			$end = $end->format('Y-m-d H:i');
			$building_id = intval($building_id);
			$results = array();
			$this->db->query("SELECT DISTINCT(bb_event.id) AS id FROM bb_event JOIN bb_event_resource ON (bb_event.id=event_id AND resource_id IN(SELECT id FROM bb_resource WHERE building_id=$building_id)) WHERE ((bb_event.from_ >= '$start' AND bb_event.from_ < '$end') OR (bb_event.to_ > '$start' AND bb_event.to_ <= '$end') OR (bb_event.from_ < '$start' AND bb_event.to_ > '$end'))", __LINE__, __FILE__);
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

		function event_ids_for_resource($resource_id, $start, $end)
		{
			$start = $start->format('Y-m-d H:i');
			$end = $end->format('Y-m-d H:i');
			$resource_id = intval($resource_id);
			$results = array();
			$this->db->query("SELECT id FROM bb_event JOIN bb_event_resource ON (event_id=id AND resource_id=$resource_id) WHERE active=1 AND ((from_ >= '$start' AND from_ < '$end') OR (to_ > '$start' AND to_ <= '$end') OR (from_ < '$start' AND to_ > '$end'))", __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = $this->_unmarshal($this->db->f('id', true), 'int');
			}
			return $results;
		}
		
		public function find_expired() {
			$table_name = $this->table_name;
			$db = $this->db;
			$expired_conditions = $this->find_expired_sql_conditions();
			return $this->read(array('where' => $expired_conditions, 'results' => 500));
		}
		
		protected function find_expired_sql_conditions() {
			$table_name = $this->table_name;
			$now = date('Y-m-d');
			return "({$table_name}.active != 0 AND {$table_name}.completed = 0 AND {$table_name}.to_ < '{$now}')";
		}
		
		public function complete_expired(&$bookings) {
			$table_name = $this->table_name;
			$db = $this->db;
			$expired_conditions = $this->find_expired_sql_conditions();
			$ids = join(', ', array_map(array($this, 'select_id'), $bookings));
			$sql = "UPDATE $table_name SET completed = 1 WHERE $expired_conditions AND {$table_name}.id IN ($ids);";
			$db->query($sql, __LINE__, __FILE__);
		}
	}
