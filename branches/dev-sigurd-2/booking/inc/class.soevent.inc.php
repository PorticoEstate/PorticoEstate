<?php
	phpgw::import_class('booking.socommon');
	
	class booking_soevent extends booking_socommon
	{
		function __construct()
		{
			parent::__construct('bb_event', 
				array(
					'id'		=> array('type' => 'int'),
					'active'	=> array('type' => 'int', 'required' => true),
					'activity_id'	=> array('type' => 'int', 'required' => true),
					'application_id'	=> array('type' => 'int', 'required' => false),
					'description' => array('type' => 'string', 'required'=> true),
					'from_'		=> array('type' => 'string', 'required'=> true),
					'to_'		=> array('type' => 'string', 'required'=> true),
					'cost'		=> array('type' => 'decimal', 'required' => true),
					'contact_name' => array('type' => 'string', 'required'=> true),
					'contact_email' => array('type' => 'string', 'sf_validator' => createObject('booking.sfValidatorEmail', array(), array('invalid' => '%field% is invalid'))),
					'contact_phone' => array('type' => 'string'),
					'completed'	=> array('type' => 'int', 'required' => true, 'nullable' => false, 'default' => '0'),
					'reminder'	=> array('type' => 'int', 'required' => true, 'nullable' => false, 'default' => '1'),
					'is_public'	=> array('type' => 'int', 'required' => true, 'nullable' => false, 'default' => '1'),
					'secret'	=> array('type' => 'string', 'required' => true),
					'sms_total'		=> array('type' => 'int', 'required' => false),
					'customer_identifier_type' 		=> array('type' => 'string', 'required' => False),
					'customer_ssn' 						=> array('type' => 'string', 'sf_validator' => createObject('booking.sfValidatorNorwegianSSN'), 'required' => false),
					'customer_organization_number' 	=> array('type' => 'string', 'sf_validator' => createObject('booking.sfValidatorNorwegianOrganizationNumber', array(), array('invalid' => '%field% is invalid'))),
					'customer_internal'					=> array('type' => 'int', 'required'=>true),
					'activity_name'	=> array('type' => 'string',
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
					'resources' => array('type' => 'int', 'required' => true,
						  'manytomany' => array(
							'table' => 'bb_event_resource',
							'key' => 'event_id',
							'column' => 'resource_id'
					)),
				)
			);
		}

		function get_building_info($id)
		{
			$this->db->limit_query("SELECT bb_building.id, bb_building.name FROM bb_building, bb_resource, bb_event_resource WHERE bb_building.id=bb_resource.building_id AND bb_resource.id=bb_event_resource.resource_id AND bb_event_resource.event_id=" . intval($id), 0, __LINE__, __FILE__, 1);
			if(!$this->db->next_record())
			{
				return False;
			}
			return array('id' => $this->db->f('id', false),
						 'name' => $this->db->f('name', false));
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
	}
