<?php
	phpgw::import_class('booking.socommon');
	
	class booking_soevent extends booking_socommon
	{
		function __construct()
		{
			parent::__construct('bb_event', 
				array(
					'id'		=> array('type' => 'int'),
                    'id_string' => array('type' => 'string', 'required' => false, 'default' => '0', 'query' => true),
					'active'	=> array('type' => 'int', 'required' => true),
					'activity_id'	=> array('type' => 'int', 'required' => true),
					'application_id'	=> array('type' => 'int', 'required' => false),
					'description' => array('type' => 'string', 'required'=> true, 'query' => true),
                    'building_id'	=> array('type' => 'int', 'required' => true),
    				'building_name' => array('type' => 'string', 'required'=> true, 'query' => true),
					'from_'		=> array('type' => 'string', 'required'=> true),
					'to_'		=> array('type' => 'string', 'required'=> true),
					'cost'		=> array('type' => 'decimal', 'required' => true),
					'contact_name' => array('type' => 'string', 'required'=> true, 'query' => true),
					'contact_email' => array('type' => 'string', 'sf_validator' => createObject('booking.sfValidatorEmail', array(), array('invalid' => '%field% is invalid'))),
					'contact_phone' => array('type' => 'string'),
					'completed'	=> array('type' => 'int', 'required' => true, 'nullable' => false, 'default' => '0'),
					'reminder'	=> array('type' => 'int', 'required' => true, 'nullable' => false, 'default' => '1'),
					'is_public'	=> array('type' => 'int', 'required' => true, 'nullable' => false, 'default' => '1'),
					'secret'	=> array('type' => 'string', 'required' => true),
					'sms_total'		=> array('type' => 'int', 'required' => false),
					'customer_organization_name' 	=> array('type' => 'string', 'required' => False, 'query' => true),
					'customer_organization_id' 		=> array('type' => 'int', 'required' => False),
					'customer_identifier_type' 		=> array('type' => 'string', 'required' => False),
					'customer_ssn' 					=> array('type' => 'string', 'sf_validator' => createObject('booking.sfValidatorNorwegianSSN'), 'required' => false),
					'customer_organization_number' 	=> array('type' => 'string', 'sf_validator' => createObject('booking.sfValidatorNorwegianOrganizationNumber', array(), array('invalid' => '%field% is invalid'))),
					'customer_internal'					=> array('type' => 'int', 'required'=>true),
					'activity_name'	=> array('type' => 'string',
						  'query' => true,
						  'join' 		=> array(
							'table' 	=> 'bb_activity',
							'fkey' 		=> 'activity_id',
							'key' 		=> 'id',
							'column' 	=> 'name'
					)),
					'audience' => array('type' => 'int', 'required' => true,
						  'manytomany' => array(
							'table' => 'bb_event_targetaudience',
							'key' => 'event_id',
							'column' => 'targetaudience_id'
					)),
					'agegroups' => array('type' => 'int', 'required' => true,
						  'manytomany' => array(
							'table' => 'bb_event_agegroup',
							'key' => 'event_id',
							'column' => array('agegroup_id' => array('type' => 'int', 'required' => true), 'male' => array('type' => 'int', 'required' => true), 'female' => array('type' => 'int', 'required' => true)),
					)),
					'comments' => array('type' => 'string',
						  'manytomany' => array(
							'table' => 'bb_event_comment',
							'key' => 'event_id',
							'column' => array('time', 'author', 'comment', 'type')
					)),
					'resources' => array('type' => 'int', 'required' => true,
						  'manytomany' => array(
							'table' => 'bb_event_resource',
							'key' => 'event_id',
							'column' => 'resource_id'
					)),
					'dates' => array('type' => 'timestamp',
						  'manytomany' => array(
							'table' => 'bb_event_date',
							'key' => 'event_id',
							'column' => array('from_', 'to_', 'id')
					)),
				)
			);
		}

		function get_building_info($id)
		{
			$this->db->limit_query("SELECT bb_building.id, bb_building.name, bb_building.email, bb_building.tilsyn_email, bb_building.tilsyn_email2 FROM bb_building, bb_resource, bb_event_resource WHERE bb_building.id=bb_resource.building_id AND bb_resource.id=bb_event_resource.resource_id AND bb_event_resource.event_id=" . intval($id), 0, __LINE__, __FILE__, 1);
			if(!$this->db->next_record())
			{
				return False;
			}
			return array('id' => $this->db->f('id', false),
						 'name' => $this->db->f('name', false),
						 'email' => $this->db->f('email', false),
						 'tilsyn_email' => $this->db->f('tilsyn_email', false),
						 'tilsyn_email2' => $this->db->f('tilsyn_email2', false));
		}

		function get_ordered_comments($id)
		{
			$results = array();
			$this->db->query("select time,author,comment,type from bb_event_comment where event_id=($id) order by time desc", __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = array('time' => $this->db->f('time', false),
                                 'author' => $this->db->f('author', false),
                                 'comment' => $this->db->f('comment', false),
						         'type' => $this->db->f('type', false));
			}
			return $results;
		}

		function get_resource_info($id)
		{
			$this->db->limit_query("SELECT bb_resource.id, bb_resource.name FROM bb_resource WHERE bb_resource.id=" . intval($id), 0, __LINE__, __FILE__, 1);
			if(!$this->db->next_record())
			{
				return False;
			}
			return array('id' => $this->db->f('id', false),
						 'name' => $this->db->f('name', false));
		}

		function get_overlap_time_info($resource_id,$overlap_id,$type)
		{
			if ($type == 'allocation')
			{
				$this->db->limit_query("SELECT bb_allocation.from_,bb_allocation.to_ FROM bb_allocation,bb_allocation_resource WHERE bb_allocation.id = $overlap_id
 AND  bb_allocation_resource.allocation_id  = $overlap_id AND bb_allocation_resource.resource_id =". intval($resource_id), 0, __LINE__, __FILE__, 1);
			}
			else
			{
				$this->db->limit_query("SELECT bb_booking.from_,bb_booking.to_ FROM bb_booking,bb_booking_resource WHERE bb_booking.id = $overlap_id
 AND  bb_booking_resource.booking_id  = $overlap_id AND bb_booking_resource.resource_id =". intval($resource_id), 0, __LINE__, __FILE__, 1);
			}			
			if(!$this->db->next_record())
			{
				return False;
			}
			return array('from' => $this->db->f('from_', false),
						 'to' => $this->db->f('to_', false));
			
		}

		function get_contact_mail($id,$type)
		{
            $mail = array();
			if ($type == 'allocation')
			{			
    			$sql = "SELECT bb_organization_contact.email FROM bb_organization_contact WHERE organization_id IN (SELECT bb_allocation.organization_id FROM bb_allocation WHERE id=$id)";
			}
			else
			{
	    		$sql = "SELECT bb_group_contact.email FROM bb_group_contact WHERE group_id IN (SELECT bb_booking.group_id FROM bb_booking WHERE id=$id)";
			}
            $this->db->query($sql, __LINE__, __FILE__);
            if($result = $this->db->resultSet)
            {
                foreach ($result as $res)
                {
                    $mail[] = $res['email'];
                }
            }

			return $mail;
		}

		public function update_comment($allids)
		{
			$db = $this->db;
			$config	= CreateObject('phpgwapi.config','booking');
			$config->read();
			$external_site_address = isset($config->config_data['external_site_address']) && $config->config_data['external_site_address'] ? $config->config_data['external_site_address'] : $GLOBALS['phpgw_info']['server']['webserver_url'];

			$comment = lang('Multiple Events was created').',<br />'.lang('Event').' ';
			foreach ($allids as $id)
			{
				$comment .= '<a href="'.$external_site_address.'/?menuaction=booking.uievent.edit&id='.$id[0].'">#'.$id[0].'</a>, ';
			}
			$comment = substr($comment, 0, -2); 
			$comment .= '.';
			foreach ($allids as $id)
			{
				$myid = $id[0];
				$sql = "UPDATE bb_event_comment SET comment='".$comment."' WHERE event_id=".intval($myid).";";
				$db->query($sql, __LINE__, __FILE__);
			}
		}


		protected function doValidate($entity, booking_errorstack $errors)
		{
			$event_id = $entity['id'] ? $entity['id'] : -1;
			// Make sure to_ > from_
			$from_ = new DateTime($entity['from_']);
			$to_ = new DateTime($entity['to_']);
			$start = $from_->format('Y-m-d H:i');
			$end = $to_->format('Y-m-d H:i');
			
			if($from_ > $to_)
			{
				$errors['from_'] = lang('Invalid from date');
			}
			if(strlen($entity['contact_name']) > 50)
			{
				$errors['contact_name'] = lang('Contact information name is to long. max 50 characters');
			}
			if($entity['resources'])
			{
				$rids = join(',', array_map("intval", $entity['resources']));
				// Check if we overlap with any existing event
				$this->db->query("SELECT e.id FROM bb_event e 
									WHERE e.active = 1 AND e.id <> $event_id AND 
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
									WHERE a.active = 1 AND  
									a.id IN (SELECT allocation_id FROM bb_allocation_resource WHERE resource_id IN ($rids)) AND
									((a.from_ >= '$start' AND a.from_ < '$end') OR 
						 			 (a.to_ > '$start' AND a.to_ <= '$end') OR 
						 			 (a.from_ < '$start' AND a.to_ > '$end'))", __LINE__, __FILE__);
				if($result = $this->db->resultSet)
				{
					foreach($result as $r)
					{
						$allocation[] = $r['id'];
					}					
					$errors['allocation'] = $allocation;
				}
			
				// Check if we overlap with any existing booking
				$this->db->query("SELECT b.id FROM bb_booking b 
									WHERE  b.active = 1 AND  
									b.id IN (SELECT booking_id FROM bb_booking_resource WHERE resource_id IN ($rids)) AND
									((b.from_ >= '$start' AND b.from_ < '$end') OR 
						 			 (b.to_ > '$start' AND b.to_ <= '$end') OR 
						 			 (b.from_ < '$start' AND b.to_ > '$end'))", __LINE__, __FILE__);
				if($result = $this->db->resultSet)
				{
					foreach($result as $r)
					{
						$booking[] = $r['id'];
					}					
					$errors['booking'] = $booking;
				}
	
			}
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
		
		public function complete_expired(&$events) {
			$table_name = $this->table_name;
			$db = $this->db;
			$ids = join(', ', array_map(array($this, 'select_id'), $events));
			$sql = "UPDATE $table_name SET completed = 1 WHERE {$table_name}.id IN ($ids);";
			$db->query($sql, __LINE__, __FILE__);
		}

		public function delete_event($id)
        {
			$db = $this->db;
			$table_name = $this->table_name.'_comment';
			$sql = "DELETE FROM $table_name WHERE event_id = ($id)";
			$db->query($sql, __LINE__, __FILE__);
			$table_name = $this->table_name.'_agegroup';
			$sql = "DELETE FROM $table_name WHERE event_id = ($id)";
			$db->query($sql, __LINE__, __FILE__);
			$table_name = $this->table_name.'_targetaudience';
			$sql = "DELETE FROM $table_name WHERE event_id = ($id)";
			$db->query($sql, __LINE__, __FILE__);
			$table_name = $this->table_name.'_date';
			$sql = "DELETE FROM $table_name WHERE event_id = ($id)";
			$db->query($sql, __LINE__, __FILE__);
			$table_name = $this->table_name.'_resource';
			$sql = "DELETE FROM $table_name WHERE event_id = ($id)";
			$db->query($sql, __LINE__, __FILE__);
			$table_name = $this->table_name;
			$sql = "DELETE FROM $table_name WHERE id = ($id)";
			$db->query($sql, __LINE__, __FILE__);
			return True;
		}

		public function update_id_string() {
			$table_name = $this->table_name;
			$db = $this->db;
			$sql = "UPDATE $table_name SET id_string = cast(id AS varchar)";
			$db->query($sql, __LINE__, __FILE__);
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

		function get_org($orgnumber)
		{
			$sql = "SELECT id,name FROM bb_organization WHERE (organization_number='".$orgnumber."' OR customer_organization_number='".$orgnumber."') AND active != 0";

			$this->db->limit_query($sql,0, __LINE__, __FILE__, 1);
			if($this->db->next_record())
			{
				$results = array('id' => $this->db->f('id', false),
						         'name' => $this->db->f('name', false));
			} else {
				return array();
			}

			return $results;
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
        function get_resources($ids)
        {

            $results = array();
            $this->db->query("select name from bb_resource where id in ($ids)", __LINE__, __FILE__);
            while ($this->db->next_record())
            {
                $results[] = $this->db->f('name', false);
            }
            return $results;
        }

	}
