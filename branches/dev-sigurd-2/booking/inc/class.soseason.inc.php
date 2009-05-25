<?php
	phpgw::import_class('booking.socommon');
	
	class booking_soseason extends booking_socommon
	{
		function __construct()
		{
			parent::__construct('bb_season', 
				array(
					'id'			=> array('type' => 'int'),
					'officer_id'	=> array('type' => 'int', 'required' => true),
					'active'		=> array('type' => 'int', 'required' => true),
					'building_id'	=> array('type' => 'int', 'required' => true),
					'name'			=> array('type' => 'string', 'query' => true, 'required' => true),
					'status'		=> array('type' => 'string', 'required'=> true),
					'from_'		=> array('type' => 'date', 'required'=> true),
					'to_'		=> array('type' => 'date', 'required'=> true),
					'building_name'	=> array('type' => 'string',
						  'query' => true,
						  'join' => array(
							'table' => 'bb_building',
							'fkey' => 'building_id',
							'key' => 'id',
							'column' => 'name'
					)),
					'officer_name'	=> array(
						'type' => 'string',
						'query' => true,
						'join' => array(
							'table' => 'phpgw_accounts',
							'fkey' => 'officer_id',
							'key' => 'account_id',
							'column' => 'account_lid'
						)
					),
					'resources'	=> array('type' => 'int', 'required' => true,
						  'manytomany' => array(
							'table' => 'bb_season_resource',
							'key' => 'season_id',
							'column' => 'resource_id'
					))
				)
			);
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
		}

		function validate($entity)
		{
			$errors = parent::validate($entity);
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
	}

	class booking_soseason_boundary extends booking_socommon
	{
		function __construct()
		{
			parent::__construct('bb_season_boundary', 
				array(
					'id'			=> array('type' => 'int'),
					'season_id'		=> array('type' => 'int', 'required' => true),
					'wday'			=> array('type' => 'int', 'required' => true),
					'from_'			=> array('type' => 'time', 'required'=> true),
					'to_'			=> array('type' => 'time', 'required'=> true)
				)
			);
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
		}

		function validate($entity)
		{
			$errors = parent::validate($entity);

			if($entity['to_'] <= $entity['from_']) {
				$errors['to'] = 'TO needs to be later than FROM';
			}

			$id = $this->_marshal($entity['id'] ? $entity['id'] : -1, 'int');
			$from_ = $this->_marshal($entity['from_'], 'time');
			$to_ = $this->_marshal($entity['to_'], 'time');
			$wday = $this->_marshal($entity['wday'], 'int');
			$season_id = intval($entity['season_id']);

			// Sub select that returns season_ids of all seasons that overlap
			// the current season in terms of date interval and resources
			$subselect = "SELECT DISTINCT(s2.id) ".
				"FROM bb_season s1 JOIN bb_season_resource AS sr1 ON(s1.id=sr1.season_id), ".
				"bb_season s2 JOIN bb_season_resource AS sr2 ON(s2.id=sr2.season_id) ".
				"WHERE (s1.from_ <= s2.to_) AND (s2.from_ <= s1.to_) ".
				"AND sr1.resource_id=sr2.resource_id ".
				"AND s1.id=$season_id";

			$this->db->query("SELECT 1 FROM bb_season_boundary AS sb1 ".
				"WHERE (sb1.from_ < {$to_}) AND ({$from_} < sb1.to_) ".
				"      AND sb1.wday = $wday ".
				"      AND sb1.id <> $id ".
				"      AND sb1.season_id IN ($subselect) ",
				__LINE__,__FILE__);
	
			if($this->db->next_record())
			{
				$errors['overlaps'] = lang("This boundary overlaps another boundary");
			}
			
			return $errors;
		}

	}

	class booking_sowtemplate_alloc extends booking_socommon
	{
		function __construct()
		{
			parent::__construct('bb_wtemplate_alloc', 
				array(
					'id'			=> array('type' => 'int'),
					'organization_id'		=> array('type' => 'int', 'required' => true),
					'season_id'		=> array('type' => 'int', 'required' => true),
					'cost'			=> array('type' => 'decimal', 'required' => true),
					'wday'			=> array('type' => 'int', 'required' => true),
					'from_'			=> array('type' => 'time', 'required'=> true),
					'to_'			=> array('type' => 'time', 'required'=> true),
					'organization_name'	=> array('type' => 'string',
						  'query' => true,
						  'join' => array(
							'table' => 'bb_organization',
							'fkey' => 'organization_id',
							'key' => 'id',
							'column' => 'name'
					)),
					'resources' => array('type' => 'int', 'required' => true,
						  'manytomany' => array(
							'table' => 'bb_wtemplate_alloc_resource',
							'key' => 'allocation_id',
							'column' => 'resource_id'
					)),
				)
			);
		}

		function validate($entity)
		{
			$errors = parent::validate($entity);
			// Make sure the template allocation doesn't overlap with any
			// other existing template allocation
			if($entity['to_'] <= $entity['from_']) {
				$errors['to'] = 'TO needs to be later than FROM';
			}
			if($entity['cost'] < 0) {
				$errors['cost'] = 'COST needs to be non-negative';
			}
			$id = $this->_marshal($entity['id'] ? $entity['id'] : -1, 'int');
			$from_ = $this->_marshal($entity['from_'], 'time');
			$to_ = $this->_marshal($entity['to_'], 'time');
			$wday = $this->_marshal($entity['wday'], 'int');
			$resources = $this->_marshal($entity['resources'], 'intarray');
			$season_id = intval($entity['season_id']);
			$this->db->query(
					"SELECT 1 FROM bb_wtemplate_alloc a1, ".
					"bb_wtemplate_alloc_resource ar1 ".
					"WHERE ar1.allocation_id<>$id AND ar1.allocation_id=a1.id AND ".
					"      ar1.resource_id IN $resources AND ".
					"      a1.season_id = $season_id AND ".
					"      a1.wday = $wday AND ".
					"      (a1.from_ <= {$to_}) AND ({$from_} <= a1.to_)",
					__LINE__, __FILE__);
			if($this->db->next_record())
			{
				$errors['overlaps'] = lang("This allocation overlaps another allocation");
			}
			// FIXME: Make sure the allocation is inside all season/day boundaries
			return $errors;
		}

	}