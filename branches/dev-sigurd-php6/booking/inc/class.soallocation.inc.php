<?php
	phpgw::import_class('booking.socommon');
	
	class booking_soallocation extends booking_socommon
	{
		function __construct()
		{
			parent::__construct('bb_allocation', 
				array(
					'id'			=> array('type' => 'int'),
					'active'		=> array('type' => 'int', 'required' => true),
					'organization_id'		=> array('type' => 'int', 'required' => true),
					'season_id'		=> array('type' => 'int', 'required' => 'true'),
					'from_'		=> array('type' => 'string', 'required'=> true),
					'to_'		=> array('type' => 'string', 'required'=> true),
					'cost'			=> array('type' => 'decimal', 'required' => true),
					'organization_name'	=> array('type' => 'string',
						  'query' => true,
						  'join' => array(
							'table' => 'bb_organization',
							'fkey' => 'organization_id',
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
							'table' => 'bb_allocation_resource',
							'key' => 'allocation_id',
							'column' => 'resource_id'
					)),
				)
			);
		}

		protected function doValidate($entity, booking_errorstack $errors)
		{
			$allocation_id = $entity['id'] ? $entity['id'] : -1;

			// FIXME: Validate: Season contains all resources
			
			if (count($errors) > 0) { return; /*Basic validation failed*/ }
			
			if (false == (boolean)intval($entity['active'])) {
				return; //Don't care about if allocation is within necessary boundaries if dealing with inactivated entity
			}
			
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
									WHERE a.active=1 AND a.id<>$allocation_id AND 
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
									WHERE b.active=1 AND b.allocation_id<>$allocation_id AND 
									b.id IN (SELECT booking_id FROM bb_booking_resource WHERE resource_id IN ($rids)) AND
									((b.from_ >= '$start' AND b.from_ < '$end') OR 
						 			 (b.to_ > '$start' AND b.to_ <= '$end') OR 
						 			 (b.from_ < '$start' AND b.to_ > '$end'))", __LINE__, __FILE__);
				if($this->db->next_record())
				{
					$errors['booking'] = lang('Overlaps with existing booking');
				}
			}
			
			if (!CreateObject('booking.soseason')->timespan_within_season($entity['season_id'], $from_, $to_)) {
				$errors['season_boundary'] = lang("This booking is not within the selected season");
			}
		}
	}
