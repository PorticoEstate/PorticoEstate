<?php
	phpgw::import_class('booking.socommon');
	
	class booking_soapplication extends booking_socommon
	{
		function __construct()
		{
			parent::__construct('bb_application', 
				array(
					'id'		=> array('type' => 'int'),
                    'id_string' => array('type' => 'string', 'required' => false, 'default' => '0', 'query' => true),
					'active'	=> array('type' => 'int'),
					'display_in_dashboard' => array('type' => 'int'),
					'type'		=> array('type' => 'string'),
					'status'	=> array('type' => 'string', 'required' => true),
					'secret'	=> array('type' => 'string', 'required' => true),
					'created'	=> array('type' => 'timestamp'),
					'modified'	=> array('type' => 'timestamp'),
					'building_name' => array('type' => 'string', 'required'=> true, 'query' => true),
					'frontend_modified'	=> array('type' => 'timestamp'),
					'owner_id'	=> array('type' => 'int', 'required' => true),
					'case_officer_id'	=> array('type' => 'int', 'required' => false),
					'activity_id'	=> array('type' => 'int', 'required' => true),
					'status'	=> array('type' => 'string', 'required' => true),
					'customer_identifier_type' 		=> array('type' => 'string', 'required' => true),
					'customer_ssn' 						=> array('type' => 'string', 'query' => true, 'sf_validator' => createObject('booking.sfValidatorNorwegianSSN', array('full_required'=>false)), 'required' => false),
					'customer_organization_number' 	=> array('type' => 'string', 'query' => true, 'sf_validator' => createObject('booking.sfValidatorNorwegianOrganizationNumber', array(), array('invalid' => '%field% is invalid'))),
					'owner_name'	=> array('type' => 'string', 'query' => true,
						  'join' 		=> array(
							'table' 	=> 'phpgw_accounts',
							'fkey' 		=> 'owner_id',
							'key' 		=> 'account_id',
							'column' 	=> 'account_lid'
					)),
					'activity_name'	=> array('type' => 'string',
						  'join' 		=> array(
							'table' 	=> 'bb_activity',
							'fkey' 		=> 'activity_id',
							'key' 		=> 'id',
							'column' 	=> 'name'
					)),
					'description'	=> array('type' => 'string', 'query' => true, 'required' => true),
                    'equipment'	=> array('type' => 'string', 'query' => true, 'required' => false),
					'contact_name'	=> array('type' => 'string', 'query' => true, 'required'=> true),
					'contact_email'	=> array('type' => 'string', 'required'=> true, 'sf_validator' => createObject('booking.sfValidatorEmail', array(), array('invalid' => '%field% is invalid'))),
					'contact_phone'	=> array('type' => 'string'),
					'case_officer_name'	=> array('type' => 'string', 'query' => true,
						'join' => array(
							'table' => 'phpgw_accounts',
							'fkey' => 'case_officer_id',
							'key' => 'account_id',
							'column' => 'account_lid'
					)),
					'audience' => array('type' => 'int', 'required' => true,
						  'manytomany' => array(
							'table' => 'bb_application_targetaudience',
							'key' => 'application_id',
							'column' => 'targetaudience_id'
					)),
					'agegroups' => array('type' => 'int', 'required' => true,
						  'manytomany' => array(
							'table' => 'bb_application_agegroup',
							'key' => 'application_id',
							'column' => array('agegroup_id' => array('type' => 'int', 'required' => true), 'male' => array('type' => 'int', 'required' => true), 'female' => array('type' => 'int', 'required' => true)),
					)),
					'dates' => array('type' => 'timestamp', 'required' => true,
						  'manytomany' => array(
							'table' => 'bb_application_date',
							'key' => 'application_id',
							'column' => array('from_', 'to_', 'id')
					)),
					'comments' => array('type' => 'string',
						  'manytomany' => array(
							'table' => 'bb_application_comment',
							'key' => 'application_id',
							'column' => array('time', 'author', 'comment', 'type')
					)),
					'resources' => array('type' => 'int', 'required' => true,
						  'manytomany' => array(
							'table' => 'bb_application_resource',
							'key' => 'application_id',
							'column' => 'resource_id'
					)),
				)
			);
		}

		protected function doValidate($entity, booking_errorstack $errors)
		{
			$event_id = $entity['id'] ? $entity['id'] : -1;
			// Make sure to_ > from_
			foreach($entity['dates'] as $date)
			{
				$from_ = new DateTime($date['from_']);
				$to_ = new DateTime($date['to_']);
				$start = $from_->format('Y-m-d H:i');
				$end = $to_->format('Y-m-d H:i');
				if($from_ > $to_ || $from_ == $to_)
				{
					$errors['from_'] = lang('Invalid from date');
				}
			}
            if(strlen($entity['contact_name']) > 50) {
                $errors['contact_name'] = lang('Contact information name is to long. max 50 characters');
            }
		}

		function get_building_info($id)
		{
			$this->db->limit_query("SELECT bb_building.id, bb_building.name FROM bb_building, bb_resource, bb_application_resource WHERE bb_building.id=bb_resource.building_id AND bb_resource.id=bb_application_resource.resource_id AND bb_application_resource.application_id=" . intval($id), 0, __LINE__, __FILE__, 1);
			if(!$this->db->next_record())
			{
				return False;
			}
			return array('id' => $this->db->f('id', false),
						 'name' => $this->db->f('name', false));
		}

		function get_accepted($id)
		{
			$sql = "SELECT bad.from_, bad.to_
					FROM bb_application ba, bb_application_date bad, bb_event be
					WHERE ba.id=($id)
					AND ba.id=bad.application_id
					AND ba.id=be.application_id
					AND be.from_=bad.from_
					AND be.to_=bad.to_";
			$results = array();		
			$this->db->query($sql,__LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = array('from_' => $this->db->f('from_', false),
						           'to_' => $this->db->f('to_', false));
			}
			return $results;
		}

		function get_rejected($id)
		{
			$sql = "SELECT bad.from_, bad.to_ FROM bb_application ba, bb_application_date bad 
					WHERE ba.id=($id)
					AND ba.id=bad.application_id
					AND bad.id NOT IN (SELECT bad.id
					FROM bb_application ba, bb_application_date bad, bb_event be
					WHERE ba.id=($id) 
					AND ba.id=bad.application_id
					AND ba.id=be.application_id
					AND be.from_=bad.from_
					AND be.to_=bad.to_)";
			$results = array();		
			$this->db->query($sql,__LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = array('from_' => $this->db->f('from_', false),
						           'to_' => $this->db->f('to_', false));
			}
			return $results;
		}

		function get_tilsyn_email($id)
		{
			$sql = "SELECT tilsyn_email, tilsyn_email2 FROM bb_building where id=(select id from bb_building where name = '$id' AND active = 1)";
			$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
			if(!$this->db->next_record())
			{
				return False;
			}
			return array('email1' => $this->db->f('tilsyn_email', false),
						 'email2' => $this->db->f('tilsyn_email2', false));

		}

		function get_resource_name($id)
		{
			$list = implode(",",$id);
			$results = array();		
			$this->db->query("SELECT name FROM bb_resource where id IN ($list)",__LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = $this->db->f('name', false);
			}
			return $results;

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

		function set_inactive($id,$type)
		{
 			if ($type == 'event') {
				$sql = "UPDATE bb_event SET active = 0 where id = ($id)";
     		} elseif ($type == 'allocation') {
				$sql = "UPDATE bb_allocation SET active = 0 where id = ($id)";
			} elseif ($type == 'booking') {
				$sql = "UPDATE bb_booking SET active = 0 where id = ($id)";
			} else {
				throw new UnexpectedValueException('Encountered an unexpected error');
			}
			$this->db->query($sql, __LINE__, __FILE__);
			return;

		}

		function set_active($id,$type)
		{
 			if ($type == 'event') {
				$sql = "UPDATE bb_event SET active = 1 where id = ($id)";
     		} elseif ($type == 'allocation') {
				$sql = "UPDATE bb_allocation SET active = 1 where id = ($id)";
			} elseif ($type == 'booking') {
				$sql = "UPDATE bb_booking SET active = 1 where id = ($id)";
			} else {
				throw new UnexpectedValueException('Encountered an unexpected error');
			}
			$this->db->query($sql, __LINE__, __FILE__);
			return;

		}

        function get_activities_main_level()
        {
		    $results = array();
			$results[]  = array('id' =>0,'name' => lang('Not selected'));
			$this->db->query("SELECT id,name FROM bb_activity WHERE parent_id is NULL", __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = array('id' => $this->db->f('id', false), 'name' => $this->db->f('name', false));
			}
			return $results;

        }
        function get_activities($id)
        {
			$results = array();
			$this->db->query("select id from bb_activity where id = ($id) or  parent_id = ($id) or parent_id in (select id from bb_activity where parent_id = ($id))", __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = $this->_unmarshal($this->db->f('id', false), 'int');
			}
			return $results;

        }

		public function update_id_string() {
			$table_name = $this->table_name;
			$db = $this->db;
			$sql = "UPDATE $table_name SET id_string = cast(id AS varchar)";
			$db->query($sql, __LINE__, __FILE__);
		}

        function check_collision($resources, $from_, $to_)
        {
            $rids = join(',', array_map("intval", $resources));
            $sql  =  "SELECT ba.id
                      FROM bb_allocation ba, bb_allocation_resource bar
                      WHERE ba.id = bar.allocation_id
                      AND bar.resource_id in ($rids)
                      AND ((ba.from_ < '$from_' AND ba.to_ > '$from_')
                      OR (ba.from_ >= '$from_' AND ba.to_ <= '$to_')
                      OR (ba.from_ < '$to_' AND ba.to_ > '$to_'))
                      UNION
                      SELECT be.id
                      FROM bb_event be, bb_event_resource ber, bb_event_date bed
                      WHERE be.id = ber.event_id
                      AND be.id = bed.event_id
                      AND ber.resource_id in ($rids)
                      AND ((bed.from_ < '$from_' AND bed.to_ > '$from_')
                      OR (bed.from_ >= '$from_' AND bed.to_ <= '$to_')
                      OR (bed.from_ < '$to_' AND bed.to_ > '$to_'))";

            $this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);

            if(!$this->db->next_record())
            {
                return False;
            }
            return True;

        }

		/**
		 * Check if a given timespan is available for bookings or allocations
		 *
		 * @param resources 
		 * @param timespan start
		 * @param timespan end
		 *
		 * @return boolean
		 */
		function check_timespan_availability($resources, $from_, $to_)
		{
			$rids = join(',', array_map("intval", $resources));
			$nrids = count($resources);
			$this->db->query("SELECT id FROM bb_season
			                  WHERE id IN (SELECT season_id 
							               FROM bb_season_resource 
							               WHERE resource_id IN ($rids,-1) 
							               GROUP BY season_id 
							               HAVING count(season_id)=$nrids)", __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$season_id = $this->_unmarshal($this->db->f('id', false), 'int');
				if (CreateObject('booking.soseason')->timespan_within_season($season_id, new DateTime($from_), new DateTime($to_)))
				{
					return true;
				}
			}
			return false;
		}
	}

	class booking_soapplication_association extends booking_socommon
	{
		function __construct()
		{
			parent::__construct('bb_application_association', 
				array(
					'id'					=> array('type' => 'int'),
					'application_id'		=> array('type' => 'int'),
					'type'	=> array('type' => 'string', 'required' => true),
					'from_'	=> array('type' => 'timestamp','query' => true),
					'to_'	=> array('type' => 'timestamp'),
					'active' => array('type' => 'int')));
		}
	}
