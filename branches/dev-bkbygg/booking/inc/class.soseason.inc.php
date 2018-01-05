<?php
	phpgw::import_class('booking.socommon');

	class booking_soseason extends booking_socommon
	{

		protected $so_boundary;

		function __construct()
		{
			parent::__construct('bb_season', array(
				'id' => array('type' => 'int'),
				'officer_id' => array('type' => 'int', 'required' => true),
				'active' => array('type' => 'int', 'required' => true),
				'building_id' => array('type' => 'int', 'required' => true),
				'name' => array('type' => 'string', 'query' => true, 'required' => true),
				'status' => array('type' => 'string', 'query' => true, 'required' => true),
				'from_' => array('type' => 'date', 'required' => true),
				'to_' => array('type' => 'date', 'required' => true),
				'building_name' => array('type' => 'string',
					'query' => true,
					'join' => array(
						'table' => 'bb_building',
						'fkey' => 'building_id',
						'key' => 'id',
						'column' => 'name'
					)),
				'officer_name' => array(
					'type' => 'string',
					'query' => true,
					'join' => array(
						'table' => 'phpgw_accounts',
						'fkey' => 'officer_id',
						'key' => 'account_id',
						'column' => 'account_lid'
					)
				),
				'resources' => array('type' => 'int', 'required' => true,
					'manytomany' => array(
						'table' => 'bb_season_resource',
						'key' => 'season_id',
						'column' => 'resource_id'
					))
				)
			);
			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];
		}

		function doValidate( $entity, booking_errorstack $errors )
		{
			parent::doValidate($entity, $errors);
			// Make sure to_ > from_
			if (count($errors) == 0)
			{
				$from_ = date_parse($entity['from_']);
				$to_ = date_parse($entity['to_']);
				if ($from_ > $to_)
				{
					$errors['from_'] = lang('Invalid from date');
				}
			}
			return $errors;
		}

		/**
		 * @param type $season_id
		 * @return type
		 * */
		public function retrieve_season_boundaries( $season_id, $coalesce_days = false )
		{
			return $this->get_boundary_storage()->retrieve_season_boundaries($season_id, $coalesce_days);
		}

		public function get_boundary_storage()
		{
			if (!$this->so_boundary)
			{
				$this->so_boundary = new booking_soseason_boundary();
			}

			return $this->so_boundary;
		}

		/**
		 * Checks if a specific timespan falls within the timespan of a season
		 *
		 * @param string|int $season_id The id of the season
		 * @param DateTime $from_
		 * @param DateTime $to_
		 *
		 * @return boolean
		 */
		public function timespan_within_season( $season_id, $from_, $to_ )
		{
			$season = $this->read_single($season_id);

			if (!$season)
			{
				throw new InvalidArgumentException('Invalid season_id');
			}

			if (!(isset($season['from_']) && ($season['to_'])))
			{
				throw new InvalidArgumentException('Invalid season');
			}

			if (strtotime($season['from_']) > strtotime($from_->format('Y-m-d')) || strtotime($season['to_']) < strtotime($to_->format('Y-m-d')))
			{
				return false;
			}

			$seconds_in_a_day = 86400;
			$days_in_period = abs(strtotime('+1 day', strtotime($to_->format('Y-m-d'))) - strtotime($from_->format('Y-m-d'))) / $seconds_in_a_day;

			if ($days_in_period <= 7)
			{
				$from_week_day = (int)$from_->format('N');
				$to_week_day = (int)$to_->format('N');
				$from_time = $from_->format('H:i:s');
				$to_time = $to_->format('H:i:s');
			}
			else
			{
				$from_week_day = 1;
				$to_week_day = 7;
				$from_time = '00:00:00';
				$to_time = '23:59:00';
			}

			if ($from_week_day > $to_week_day)
			{
				//booking week wraps around from end of week to start of week, 
				//so we split it into two periods and validate each by itself
				$end_of_week = strtotime('+' . (7 - $from_week_day) . ' days 23:59:00', strtotime($from_->format('Y-m-d')));
				$end_of_week = new DateTime(date('Y-m-d H:i:s', $end_of_week));
				$start_of_week = strtotime('-' . ($to_week_day - 1) . ' days 00:00:00', strtotime($to_->format('Y-m-d')));
				$start_of_week = new DateTime(date('Y-m-d H:i:s', $start_of_week));

				$end_of_week_f = $start_of_week->format('Y-m-d H:i:s');
				$start_of_week_f = $end_of_week->format('Y-m-d H:i:s');

				if (false == $this->timespan_within_season($season_id, $from_, $end_of_week))
					return false;
				if (false == $this->timespan_within_season($season_id, $start_of_week, $to_))
					return false;
				return true;
			}

			$from_wday_ts = strtotime(date('Y-m-d', 86400 * ($from_week_day - 1)) . ' ' . $from_time);
			$to_wday_ts = strtotime(date('Y-m-d', 86400 * ($to_week_day - 1)) . ' ' . $to_time);

			$coalesced_boundaries = $this->retrieve_season_boundaries($season_id, true);

			foreach ($coalesced_boundaries as $b)
			{
				if (strtotime($b['from_']) <= $from_wday_ts && strtotime($b['to_']) >= $to_wday_ts)
				{
					return true;
				}
			}

			return false;
		}

		public function update_id_string()
		{
			$db = $this->db;
			$sql = "UPDATE bb_allocation SET id_string = cast(id AS varchar)";
			$db->query($sql, __LINE__, __FILE__);
		}
	}

	class booking_soseason_boundary extends booking_socommon
	{

		function __construct()
		{
			parent::__construct('bb_season_boundary', array(
				'id' => array('type' => 'int'),
				'season_id' => array('type' => 'int', 'required' => true),
				'wday' => array('type' => 'int', 'required' => true),
				'from_' => array('type' => 'time', 'required' => true),
				'to_' => array('type' => 'time', 'required' => true)
				)
			);
			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];
		}

		function doValidate( $entity, booking_errorstack $errors )
		{
			parent::doValidate($entity, $errors);

			if ($entity['to_'] <= $entity['from_'])
			{
				$errors['to'] = lang('TO needs to be later than FROM');
			}

			$id = $this->_marshal($entity['id'] ? $entity['id'] : -1, 'int');
			$from_ = $this->_marshal($entity['from_'], 'time');
			$to_ = $this->_marshal($entity['to_'], 'time');
			$wday = $this->_marshal($entity['wday'], 'int');
			$season_id = intval($entity['season_id']);

			// Sub select that returns season_ids of all seasons that overlap
			// the current season in terms of date interval and resources
			$subselect = "SELECT DISTINCT(s2.id) " .
				"FROM bb_season s1 JOIN bb_season_resource AS sr1 ON(s1.id=sr1.season_id), " .
				"bb_season s2 JOIN bb_season_resource AS sr2 ON(s2.id=sr2.season_id) " .
				"WHERE (s1.from_ <= s2.to_) AND (s2.from_ <= s1.to_) " .
				"AND sr1.resource_id=sr2.resource_id " .
				"AND s1.active=1" .
				"AND s2.active=1" .
				"AND s1.id=$season_id";

			$this->db->query("SELECT 1 FROM bb_season_boundary AS sb1 " .
				"WHERE (sb1.from_ < {$to_}) AND ({$from_} < sb1.to_) " .
				"      AND sb1.wday = $wday " .
				"      AND sb1.id <> $id " .
				"      AND sb1.season_id IN ($subselect) ", __LINE__, __FILE__);

			if ($this->db->next_record())
			{
				$errors['overlaps'] = lang("This boundary overlaps another boundary");
			}

			return $errors;
		}

		/**
		 * @param type $season_id
		 * @return type
		 * */
		public function retrieve_season_boundaries( $season_id, $coalesce_days = false )
		{
			$view_sql = <<<EOT
			CREATE OR REPLACE TEMP VIEW bsbt AS SELECT
			TIMESTAMP 'epoch ' + (EXTRACT(EPOCH FROM from_)+86400*(wday-1)) * INTERVAL '1 second' as from_,
			TIMESTAMP 'epoch ' + (EXTRACT(EPOCH FROM to_)+86400*(wday-1)) * INTERVAL '1 second' as to_
			FROM bb_season_boundary WHERE season_id={$season_id};
EOT;

			$this->db->query($view_sql, __LINE__, __FILE__, true);

			$ranges_sql = <<<EOT
			SELECT from_,
				(
				SELECT MIN(to_)
				FROM bsbt AS C WHERE NOT EXISTS
				(
					SELECT *
					FROM bsbt AS D
					WHERE
					C.to_ >= D.from_
					AND C.to_ < D.to_)
					AND C.to_ >= A.from_
				) AS to_
			FROM bsbt AS A WHERE NOT EXISTS
			(
				SELECT *
				FROM bsbt AS B
				WHERE A.from_ > B.from_
				AND A.from_ <= B.to_
			) ORDER BY from_, to_
EOT;

			$this->db->query($ranges_sql, __LINE__, __FILE__);

			return $coalesce_days ? $this->coalesce_season_boundaries_over_days($this->db->resultSet) : $this->db->resultSet;
		}

		public function coalesce_season_boundaries_over_days( &$result_set )
		{
			$coalesced_result = array();
			while ($record = array_shift($result_set))
			{
				$this->coalesce_boundary($record, $result_set);
				$coalesced_result[] = $record;
			}
			return $coalesced_result;
		}

		protected function coalesce_boundary( &$r, &$result_set )
		{
			$ts_to = strtotime($r['to_']);

			if (!$ts_to >= strtotime('23:59:00', $ts_to))
				return;
			if (!$record = array_shift($result_set))
				return;

			$ts_from = strtotime($record['from_']);
			if ($ts_from <= strtotime('00:00:59', $ts_from))
			{
				$r['to_'] = $record['to_'];
				$r[1] = $record['to_'];
				$this->coalesce_boundary($r, $result_set);
			}
			else
			{
				array_unshift($result_set, $record);
			}
		}
	}

	class booking_sowtemplate_alloc extends booking_socommon
	{

		function __construct()
		{
			parent::__construct('bb_wtemplate_alloc', array(
				'id' => array('type' => 'int'),
				'organization_id' => array('type' => 'int', 'required' => true),
				'season_id' => array('type' => 'int', 'required' => true),
				'cost' => array('type' => 'decimal', 'required' => true),
				'wday' => array('type' => 'int', 'required' => true),
				'from_' => array('type' => 'time', 'required' => true),
				'to_' => array('type' => 'time', 'required' => true),
				'organization_name' => array('type' => 'string',
					'query' => true,
					'join' => array(
						'table' => 'bb_organization',
						'fkey' => 'organization_id',
						'key' => 'id',
						'column' => 'name'
					)),
				'shortname' => array('type' => 'string',
					'query' => true,
					'join' => array(
						'table' => 'bb_organization',
						'fkey' => 'organization_id',
						'key' => 'id',
						'column' => 'shortname'
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

		protected function doValidate( $entity, booking_errorstack $errors )
		{
			parent::doValidate($entity, $errors);
			// Make sure the template allocation doesn't overlap with any
			// other existing template allocation
			if ($entity['to_'] <= $entity['from_'])
			{
				$errors['to'] = lang('TO needs to be later than FROM');
			}
			if ($entity['cost'] < 0)
			{
				$errors['cost'] = lang('COST needs to be non-negative');
			}
			if (!$entity['resources'])
			{
				return;
			}
			$id = $this->_marshal($entity['id'] ? $entity['id'] : -1, 'int');
			$from_ = $this->_marshal($entity['from_'], 'time');
			$to_ = $this->_marshal($entity['to_'], 'time');
			$wday = $this->_marshal($entity['wday'], 'int');
			$resources = $this->_marshal($entity['resources'], 'intarray');
			$season_id = intval($entity['season_id']);
			$this->db->query(
				"SELECT 1 FROM bb_wtemplate_alloc a1, " .
				"bb_wtemplate_alloc_resource ar1 " .
				"WHERE ar1.allocation_id<>$id AND ar1.allocation_id=a1.id AND " .
				"      ar1.resource_id IN $resources AND " .
				"      a1.season_id = $season_id AND " .
				"      a1.wday = $wday AND " .
				"     ((a1.from_ >= $from_ AND a1.from_ < $to_) OR " .
				"	   (a1.to_ > $from_ AND a1.to_ <= $to_) OR " .
				"	   (a1.from_ < $from_ AND a1.to_ > $to_)) ", __LINE__, __FILE__);
			if ($this->db->next_record())
			{
				$errors['overlaps'] = lang("This allocation overlaps another allocation");
			}
			$this->db->query(
				"SELECT 1 FROM bb_season_boundary " .
				"WHERE wday = $wday AND from_ <= ${from_} AND to_ >= ${to_} AND season_id = ${season_id}", __LINE__, __FILE__);
			if (!$this->db->next_record())
			{
				$errors['overlaps'] = lang("This allocation is outside season boundaries");
			}
		}

		function delete( $id )
		{
			$this->db->transaction_begin();
			$this->db->query("DELETE FROM bb_wtemplate_alloc_resource WHERE allocation_id=" . intval($id), __LINE__, __FILE__);
			$this->db->query("DELETE FROM bb_wtemplate_alloc WHERE id=" . intval($id), __LINE__, __FILE__);
			return	$this->db->transaction_commit();
		}
	}