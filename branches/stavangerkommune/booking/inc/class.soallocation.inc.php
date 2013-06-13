<?php
	phpgw::import_class('booking.socommon');
	
	class booking_soallocation extends booking_socommon
	{
		const ERROR_CONFLICTING_BOOKING = 'booking';
		const ERROR_CONFLICTING_EVENT = 'event';
		const ERROR_CONFLICTING_ALLOCATION = 'allocation';
		
		protected static $allocation_conflict_error_types = array(
			self::ERROR_CONFLICTING_BOOKING => true,
			self::ERROR_CONFLICTING_EVENT => true,
			self::ERROR_CONFLICTING_ALLOCATION => true,
		);
		
		function __construct()
		{
			parent::__construct('bb_allocation', 
				array(
					'id'			=> array('type' => 'int'),
                    'id_string' => array('type' => 'string', 'required' => false, 'default' => '0', 'query' => true),
					'active'		=> array('type' => 'int', 'required' => true),
					'application_id'	=> array('type' => 'int', 'required' => false),
					'organization_id'		=> array('type' => 'int', 'required' => true),
					'building_name' => array('type' => 'string', 'required'=> true, 'query' => true),
					'season_id'		=> array('type' => 'int', 'required' => 'true'),
					'from_'		=> array('type' => 'string', 'required'=> true),
					'to_'		=> array('type' => 'string', 'required'=> true),
					'cost'			=> array('type' => 'decimal', 'required' => true),
					'completed'	=> array('type' => 'int', 'required' => true, 'default' => '0'),
					'organization_name'	=> array('type' => 'string',
						  'query' => true,
						  'join' => array(
							'table' => 'bb_organization',
							'fkey' => 'organization_id',
							'key' => 'id',
							'column' => 'name'
					)),
					'organization_shortname'	=> array('type' => 'string',
						  'query' => true,
						  'join' => array(
							'table' => 'bb_organization',
							'fkey' => 'organization_id',
							'key' => 'id',
							'column' => 'shortname'
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
		
		/** 
		 * Filters out any errors having to do with reservation conflicts 
		 * from an errors array leaving only errors of other types. If
		 * this function returns an empty array then the original errors
		 * array would have consisted of only reservation conflicts.
		 *
		 * @return array  
		 */
		public function filter_conflict_errors(array $errors) {
			return array_diff_key($errors, self::$allocation_conflict_error_types);
		}

		protected function doValidate($entity, booking_errorstack $errors)
		{
			set_time_limit(300);
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
				$errors['from_'] = lang('Invalid from date');
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
					$errors[self::ERROR_CONFLICTING_EVENT] = lang('Overlaps with existing event');
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
					$errors[self::ERROR_CONFLICTING_ALLOCATION] = lang('Overlaps with existing allocation');
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
					$errors[self::ERROR_CONFLICTING_BOOKING] = lang('Overlaps with existing booking');
				}
			}
			
			if (!CreateObject('booking.soseason')->timespan_within_season($entity['season_id'], $from_, $to_)) {
				$errors['season_boundary'] = lang("This booking is not within the selected season");
			}
		}

		function get_resource($id)
		{
			$this->db->limit_query("SELECT name FROM bb_resource where id=" . intval($id), 0, __LINE__, __FILE__, 1);
			if(!$this->db->next_record())
			{
				return False;
			}
			return $this->db->f('name', false);
		}

		function get_building($id)
		{
			$this->db->limit_query("SELECT name FROM bb_building where id=" . intval($id), 0, __LINE__, __FILE__, 1);
			if(!$this->db->next_record())
			{
				return False;
			}
			return $this->db->f('name', false);
		}

		function get_buildings()
		{
            $results = array();
			$results[] = array('id' =>  0,'name' => lang('Not selected'));
			$this->db->query("SELECT id, name FROM bb_building WHERE active != 0 ORDER BY name ASC", __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = array('id' => $this->db->f('id', false),
						           'name' => $this->db->f('name', false));
			}
			return $results;
		}

		function get_organization($id)
		{
			$this->db->limit_query("SELECT id FROM bb_organization where id=" . intval($id), 0, __LINE__, __FILE__, 1);
			if(!$this->db->next_record())
			{
				return False;
			}
			return $this->db->f('id', false);
		}

		function get_organizations()
		{
            $results = array();
			$results[] = array('id' =>  0,'name' => lang('Not selected'));
			$this->db->query("SELECT id, name FROM bb_organization WHERE active = 1 ORDER BY name ASC", __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = array('id' => $this->db->f('id', false),
						           'name' => $this->db->f('name', false));
			}
			return $results;
		}

		function get_season($id)
		{
			$this->db->limit_query("SELECT id FROM bb_season where id=" . intval($id), 0, __LINE__, __FILE__, 1);
			if(!$this->db->next_record())
			{
				return False;
			}
			return $this->db->f('id', false);
		}

		function get_seasons($build_id)
		{
            $results = array();
			$results[] = array('id' =>  0,'name' => lang('Not selected'));
            if (isset($build_id)) {
    			$this->db->query("SELECT id, name FROM bb_season WHERE status NOT IN ('ARCHIVED') AND building_id = ($build_id) ORDER BY name ASC", __LINE__, __FILE__);
            } else {
		    	$this->db->query("SELECT id, name FROM bb_season WHERE status NOT IN ('ARCHIVED') ORDER BY name ASC", __LINE__, __FILE__);
            }

			while ($this->db->next_record())
			{
				$results[] = array('id' => $this->db->f('id', false),
						           'name' => $this->db->f('name', false));
			}
			return $results;
		}
		function get_allocation_id($allocation)
		{

            $from = "'".$allocation['from_']."'";
            $to = "'".$allocation['to_']."'";
            $org_id = $allocation['organization_id'];
            $season_id = $allocation['season_id'];
            $resources = implode(",", $allocation['resources']);

            $sql = "SELECT id FROM bb_allocation ba2 WHERE ba2.from_ = ($from) AND ba2.to_ = ($to) AND ba2.organization_id = ($org_id) AND ba2.season_id = ($season_id) AND EXISTS ( SELECT 1 FROM bb_allocation  a,bb_allocation_resource b WHERE a.id = b.allocation_id AND b.resource_id IN ($resources))";

			$this->db->limit_query($sql, 0,__LINE__, __FILE__,1);
			if(!$this->db->next_record())
			{
				return False;
			}
			return $this->db->f('id', false);
        }

		function check_for_booking($id)
        {
            $sql = "SELECT id FROM bb_booking  WHERE allocation_id = ($id)";

			$this->db->limit_query($sql, 0,__LINE__, __FILE__,1);
			if(!$this->db->next_record())
			{
				return False;
			}
			return $this->db->f('id', false);
            
        }

		public function delete_allocation($id)
        {
			$db = $this->db;
			$table_name = $this->table_name.'_resource';
			$sql = "DELETE FROM $table_name WHERE allocation_id = ($id)";
			$db->query($sql, __LINE__, __FILE__);
			$table_name = $this->table_name;
			$sql = "DELETE FROM $table_name WHERE id = ($id)";
			$db->query($sql, __LINE__, __FILE__);
			return True;
		}

		public function update_id_string() 
        {
			$db = $this->db;
			$table_name = $this->table_name;
			$sql = "UPDATE $table_name SET id_string = cast(id AS varchar)";
			$db->query($sql, __LINE__, __FILE__);
		}
	
		public function find_expired() {
			$table_name = $this->table_name;
			$db = $this->db;
			$expired_conditions = $this->find_expired_sql_conditions();
			return $this->read(array('filters' => array('where' => $expired_conditions), 'results' => 1000));
		}
		
		protected function find_expired_sql_conditions() {
			$table_name = $this->table_name;
			$now = date('Y-m-d');
			return "({$table_name}.active != 0 AND {$table_name}.completed = 0 AND {$table_name}.to_ < '{$now}')";
		}
		
		public function complete_expired(&$allocations) {
			$table_name = $this->table_name;
			$db = $this->db;
			$ids = join(', ', array_map(array($this, 'select_id'), $allocations));
			$sql = "UPDATE $table_name SET completed = 1 WHERE {$table_name}.id IN ($ids);";
			$db->query($sql, __LINE__, __FILE__);
		}
	}
