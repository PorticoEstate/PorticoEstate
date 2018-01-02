<?php
	phpgw::import_class('booking.socommon');

	class booking_sobooking extends booking_socommon
	{

		function __construct()
		{
			parent::__construct('bb_booking', array(
				'id' => array('type' => 'int'),
				'active' => array('type' => 'int', 'required' => true),
				'allocation_id' => array('type' => 'int', 'required' => false),
				'application_id' => array('type' => 'int', 'required' => false),
				'activity_id' => array('type' => 'int', 'required' => true),
				'building_name' => array('type' => 'string', 'required' => true, 'query' => true),
				'group_id' => array('type' => 'int', 'required' => true),
				'from_' => array('type' => 'timestamp', 'required' => true),
				'to_' => array('type' => 'timestamp', 'required' => true),
				'season_id' => array('type' => 'int', 'required' => true),
				'cost' => array('type' => 'decimal', 'required' => true),
				'sms_total' => array('type' => 'int', 'required' => false),
				'completed' => array('type' => 'int', 'required' => true, 'nullable' => false,
					'default' => '0'),
				'reminder' => array('type' => 'int', 'required' => true, 'nullable' => false,
					'default' => '1'),
				'secret' => array('type' => 'string', 'required' => true),
				'activity_name' => array('type' => 'string',
					'query' => true,
					'join' => array(
						'table' => 'bb_activity',
						'fkey' => 'activity_id',
						'key' => 'id',
						'column' => 'name'
					)),
				'group_name' => array('type' => 'string',
					'query' => true,
					'join' => array(
						'table' => 'bb_group',
						'fkey' => 'group_id',
						'key' => 'id',
						'column' => 'name'
					)),
				'group_shortname' => array('type' => 'string',
					'query' => true,
					'join' => array(
						'table' => 'bb_group',
						'fkey' => 'group_id',
						'key' => 'id',
						'column' => 'shortname'
					)),
				'building_id' => array('type' => 'string',
					'join' => array(
						'table' => 'bb_season',
						'fkey' => 'season_id',
						'key' => 'id',
						'column' => 'building_id'
					)),
				'season_name' => array('type' => 'string', 'query' => true,
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
						'column' => array('agegroup_id' => array('type' => 'int', 'required' => true),
							'male' => array('type' => 'int', 'required' => true), 'female' => array('type' => 'int',
								'required' => true)),
					)),
				'resources' => array('type' => 'int', 'required' => true,
					'manytomany' => array(
						'table' => 'bb_booking_resource',
						'key' => 'booking_id',
						'column' => 'resource_id'
					)),
				'costs' => array('type' => 'string',
					'manytomany' => array(
						'table' => 'bb_booking_cost',
						'key' => 'booking_id',
						'column' => array('time', 'author', 'comment', 'cost'),
						'order' => array('sort' => 'time', 'dir' => 'ASC')
					)),
				)
			);
		}

		function calculate_allocation_id( $entity )
		{
			if (!$entity['resources'])
			{
				return null;
			}
			$booking_id = $entity['id'] ? $entity['id'] : -1;
			$group_id = intval($entity['group_id']);
			$from_ = new DateTime($entity['from_']);
			$to_ = new DateTime($entity['to_']);
			$start = $from_->format('Y-m-d H:i');
			$end = $to_->format('Y-m-d H:i');
			$rids = join(',', array_map("intval", $entity['resources']));

			// Check if we overlap with any existing allocation
			$this->db->query("SELECT a.id FROM bb_allocation a 
								WHERE a.active = 1 AND a.organization_id IN (SELECT organization_id FROM bb_group WHERE id=$group_id) AND 
								a.id IN (SELECT allocation_id FROM bb_allocation_resource WHERE resource_id IN ($rids)) AND
								((a.from_ >= '$start' AND a.from_ < '$end') OR 
					 			 (a.to_ > '$start' AND a.to_ <= '$end') OR 
					 			 (a.from_ < '$start' AND a.to_ > '$end'))", __LINE__, __FILE__);
			if ($this->db->next_record())
			{
				return $this->db->f('id', false);
			}
			else
			{
				return null;
			}
		}

		protected function doValidate( $entity, booking_errorstack $errors )
		{
			// FIXME: Validate: Season contains all resources
			// FIXME: Validate: booking from/to

			if (count($errors) > 0)
			{
				return; /* Basic validation failed */
			}

			if (false == (boolean)intval($entity['active']))
			{
				return; //Don't care about if booking is within necessary boundaries if dealing with inactivated entity
			}

			$booking_id = $entity['id'] ? $entity['id'] : -1;
			$allocation_id = $entity['allocation_id'] ? $entity['allocation_id'] : -1;
			$from_ = new DateTime($entity['from_']);
			$to_ = new DateTime($entity['to_']);
			$start = $from_->format('Y-m-d H:i');
			$end = $to_->format('Y-m-d H:i');

			if ($entity['from_'] == $entity['to_'])
			{
				$errors['to_'] = lang('Invalid to date');
				return; //No need to continue validation if dates are invalid
			}
			if (strtotime($start) > strtotime($end))
			{
				$errors['from_'] = lang('Invalid from date');
				return; //No need to continue validation if dates are invalid
			}

			if ($GLOBALS['phpgw_info']['flags']['currentapp'] == 'bookingfrontend' &&
				$allocation_id == -1)
			{
				$errors['booking'] = lang("This booking is outside the organization's allocated time");
			}

			if ($entity['resources'])
			{
				$rids = join(',', array_map("intval", $entity['resources']));
				// Check if we overlap with any existing event
				$this->db->query("SELECT e.id FROM bb_event e 
									WHERE e.active = 1 AND 
									e.id IN (SELECT event_id FROM bb_event_resource WHERE resource_id IN ($rids)) AND
									((e.from_ >= '$start' AND e.from_ < '$end') OR
						 			 (e.to_ > '$start' AND e.to_ < '$end') OR 
						 			 (e.from_ < '$start' AND e.to_ > '$end'))", __LINE__, __FILE__);
				if ($this->db->next_record())
				{
					$errors['event'] = lang('Overlaps with existing event');
				}
				// Check if we overlap with any existing allocation
				$this->db->query("SELECT a.id FROM bb_allocation a 
									WHERE a.active = 1 AND a.id<>$allocation_id AND 
									a.id IN (SELECT allocation_id FROM bb_allocation_resource WHERE resource_id IN ($rids)) AND
									((a.from_ >= '$start' AND a.from_ < '$end') OR
						 			 (a.to_ > '$start' AND a.to_ < '$end') OR 
						 			 (a.from_ < '$start' AND a.to_ > '$end'))", __LINE__, __FILE__);
				if ($this->db->next_record())
				{
					$errors['allocation'] = lang('Overlaps other organizations allocation');
				}

				// Check if we overlap with any existing booking
				$this->db->query("SELECT b.id FROM bb_booking b 
									WHERE  b.active = 1 AND b.id<>$booking_id AND 
									b.id IN (SELECT booking_id FROM bb_booking_resource WHERE resource_id IN ($rids)) AND
									((b.from_ >= '$start' AND b.from_ < '$end') OR
						 			 (b.to_ > '$start' AND b.to_ < '$end') OR 
						 			 (b.from_ < '$start' AND b.to_ > '$end'))", __LINE__, __FILE__);
				if ($this->db->next_record())
				{
					$errors['booking'] = lang('Overlaps with existing booking');
				}
				if ($allocation_id != -1)
				{
					$this->db->query("SELECT a.id FROM bb_allocation a 
										WHERE a.active = 1 AND a.id = $allocation_id AND 
										(a.from_ <= '$start' AND a.to_ >= '$end')", __LINE__, __FILE__);
					if (!$this->db->next_record())
					{
						$errors['booking'] = lang("This booking is outside the organization's allocated time");
					}
					$this->db->query("SELECT count(1) FROM bb_allocation_resource 
									WHERE allocation_id = $allocation_id AND resource_id IN ($rids)", __LINE__, __FILE__);
					$this->db->next_record();
					if ($this->db->f('count', false) != count($entity['resources']))
					{
						$errors['booking'] = lang("The booking uses resources not in the containing allocation");
					}
				}
			}

			if (!CreateObject('booking.soseason')->timespan_within_season($entity['season_id'], $from_, $to_))
			{
				$errors['season_boundary'] = lang("This booking is not within the selected season");
			}
		}

		function resource_ids_for_bookings( $bookings )
		{
			if (!$bookings)
			{
				return array();
			}
			$ids = join(',', array_map("intval", $bookings));
			$results = array();
			$this->db->query("SELECT resource_id FROM bb_booking_resource WHERE booking_id IN ($ids)", __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = $this->_unmarshal($this->db->f('resource_id', false), 'int');
			}
			return $results;
		}

		function resource_ids_for_allocations( $allocations )
		{
			if (!$allocations)
			{
				return array();
			}
			$ids = join(',', array_map("intval", $allocations));
			$results = array();
			$this->db->query("SELECT resource_id FROM bb_allocation_resource WHERE allocation_id IN ($ids)", __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = $this->_unmarshal($this->db->f('resource_id', false), 'int');
			}
			return $results;
		}

		function resource_ids_for_events( $events )
		{
			if (!$events)
			{
				return array();
			}
			$ids = join(',', array_map("intval", $events));
			$results = array();
			$this->db->query("SELECT resource_id FROM bb_event_resource WHERE event_id IN ($ids)", __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = $this->_unmarshal($this->db->f('resource_id', false), 'int');
			}
			return $results;
		}

		function allocation_ids_for_building( $building_id, $start, $end )
		{
			$start = $start->format('Y-m-d H:i');
			$end = $end->format('Y-m-d H:i');
			$building_id = intval($building_id);
			$results = array();
			$this->db->query("SELECT bb_allocation.id AS id FROM bb_allocation JOIN bb_season ON (bb_allocation.season_id=bb_season.id AND bb_allocation.active=1) WHERE bb_season.building_id=$building_id AND bb_season.active=1 AND bb_season.status='PUBLISHED' AND ((bb_allocation.from_ >= '$start' AND bb_allocation.from_ < '$end') OR (bb_allocation.to_ > '$start' AND bb_allocation.to_ <= '$end') OR (bb_allocation.from_ < '$start' AND bb_allocation.to_ > '$end'))", __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = $this->_unmarshal($this->db->f('id', false), 'int');
			}
			return $results;
		}

		function booking_ids_for_building( $building_id, $start, $end )
		{
			$start = $start->format('Y-m-d H:i');
			$end = $end->format('Y-m-d H:i');
			$building_id = intval($building_id);
			$results = array();
			$this->db->query("SELECT bb_booking.id AS id FROM bb_booking JOIN bb_season ON (bb_booking.season_id=bb_season.id AND bb_booking.active=1) WHERE bb_season.building_id=$building_id AND bb_season.active=1 AND bb_season.status='PUBLISHED' AND ((bb_booking.from_ >= '$start' AND bb_booking.from_ < '$end') OR (bb_booking.to_ > '$start' AND bb_booking.to_ <= '$end') OR (bb_booking.from_ < '$start' AND bb_booking.to_ > '$end'))", __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = $this->_unmarshal($this->db->f('id', false), 'int');
			}
			return $results;
		}

		function event_ids_for_building( $building_id, $start, $end )
		{
			$start = $start->format('Y-m-d H:i');
			$end = $end->format('Y-m-d H:i');
			$building_id = intval($building_id);
			$results = array();
			$sql = "SELECT DISTINCT(bb_event.id) AS id"
				. " FROM bb_event JOIN bb_event_resource ON (bb_event.id=event_id AND resource_id"
				. " IN(SELECT id FROM bb_resource JOIN bb_building_resource ON bb_building_resource.resource_id = bb_resource.id WHERE building_id=$building_id))"
				. " WHERE bb_event.active=1 AND ((bb_event.from_ >= '$start' AND bb_event.from_ < '$end')"
				. " OR (bb_event.to_ > '$start' AND bb_event.to_ <= '$end')"
				. " OR (bb_event.from_ < '$start' AND bb_event.to_ > '$end'))";
			$this->db->query($sql, __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = $this->_unmarshal($this->db->f('id', false), 'int');
			}
			return $results;
		}

		function allocation_ids_for_resource( $resource_id, $start, $end )
		{
			$start = $start->format('Y-m-d H:i');
			$end = $end->format('Y-m-d H:i');
			$resource_id = intval($resource_id);
			$results = array();
			$sql = "SELECT bb_allocation.id AS id"
				. " FROM bb_allocation JOIN bb_allocation_resource ON (allocation_id=id AND resource_id=$resource_id)"
				. " JOIN bb_resource as res ON ( res.id=$resource_id)"
				. " JOIN bb_season ON (bb_allocation.season_id=bb_season.id AND bb_allocation.active=1)"
				. " JOIN bb_building_resource ON bb_building_resource.resource_id = res.id "
				. " WHERE bb_season.building_id=bb_building_resource.building_id AND bb_season.active=1"
				. " AND bb_season.status='PUBLISHED' AND ((bb_allocation.from_ >= '$start'"
				. " AND bb_allocation.from_ < '$end') OR (bb_allocation.to_ > '$start'"
				. " AND bb_allocation.to_ <= '$end') OR (bb_allocation.from_ < '$start' AND bb_allocation.to_ > '$end'))";

			$this->db->query($sql, __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = $this->_unmarshal($this->db->f('id', false), 'int');
			}
			return $results;
		}

		function booking_ids_for_resource( $resource_id, $start, $end )
		{
			$start = $start->format('Y-m-d H:i');
			$end = $end->format('Y-m-d H:i');
			$resource_id = intval($resource_id);
			$results = array();
			$sql = "SELECT bb_booking.id AS id"
				. " FROM bb_booking JOIN bb_booking_resource ON (booking_id=id AND resource_id=$resource_id)"
				. " JOIN bb_resource as res ON ( res.id=$resource_id)"
				. " JOIN bb_season ON (bb_booking.season_id=bb_season.id AND bb_booking.active=1)"
				. " JOIN bb_building_resource ON bb_building_resource.resource_id = res.id "
				. " WHERE bb_season.building_id=bb_building_resource.building_id AND bb_season.active=1"
				. " AND bb_season.status='PUBLISHED' AND ((bb_booking.from_ >= '$start'"
				. " AND bb_booking.from_ < '$end') OR (bb_booking.to_ > '$start'"
				. " AND bb_booking.to_ <= '$end') OR (bb_booking.from_ < '$start'"
				. " AND bb_booking.to_ > '$end'))";

			$this->db->query($sql, __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = $this->_unmarshal($this->db->f('id', false), 'int');
			}
			return $results;
		}

		function event_ids_for_resource( $resource_id, $start, $end )
		{
			$start = $start->format('Y-m-d H:i');
			$end = $end->format('Y-m-d H:i');
			$resource_id = intval($resource_id);
			$results = array();
			$this->db->query("SELECT id FROM bb_event"
				. " JOIN bb_event_resource ON (event_id=id AND resource_id=$resource_id)"
				. " WHERE active=1 AND ((from_ >= '$start' AND from_ < '$end')"
				. " OR (to_ > '$start' AND to_ <= '$end') OR (from_ < '$start'"
				. " AND to_ > '$end'))", __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = $this->_unmarshal($this->db->f('id', false), 'int');
			}
			return $results;
		}

		public function get_booking_id( $booking )
		{
			$from = "'" . $booking['from_'] . "'";
			$to = "'" . $booking['to_'] . "'";
			$gid = (int)$booking['group_id'];
			$season_id = (int)$booking['season_id'];
			$resources = implode(",", $booking['resources']);

			if(empty($booking['resources']))
			{
				return false;
			}

			$sql = "SELECT bb.id,bbr.resource_id FROM bb_booking bb,bb_booking_resource bbr WHERE bb.from_ = ($from) AND bb.to_ = ($to) AND bb.group_id = ($gid) AND bb.season_id = ($season_id) AND bb.id = bbr.booking_id AND EXISTS (SELECT 1 FROM bb_booking_resource bbr2 WHERE  bbr2.resource_id IN ($resources) AND bbr2.resource_id = bbr.resource_id)";

			$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
			if (!$this->db->next_record())
			{
				return False;
			}
			return $this->db->f('id', false);
		}

		public function check_allocation( $id )
		{
			$sql = "SELECT allocation_id as aid FROM bb_booking WHERE allocation_id = ( SELECT allocation_id FROM bb_booking WHERE id = ($id) ) GROUP BY allocation_id HAVING count(id) < 2";

			$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
			if (!$this->db->next_record())
			{
				return False;
			}
			return $this->db->f('aid', false);
		}

		function check_for_booking( $booking )
		{
			$from = "'" . $booking['from_'] . "'";
			$to = "'" . $booking['to_'] . "'";
			$gid = $booking['group_id'];
			$season_id = $booking['season_id'];
			$resources = implode(",", $booking['resources']);

			$sql = "SELECT id FROM bb_allocation ba2 WHERE ba2.from_ = ($from) AND ba2.to_ = ($to) AND ba2.organization_id = (SELECT organization_id FROM bb_group WHERE id = ($gid)) AND ba2.season_id = ($season_id) AND EXISTS ( SELECT 1 FROM bb_allocation  a,bb_allocation_resource b WHERE a.id = b.allocation_id AND b.resource_id IN ($resources)) AND NOT EXISTS (SELECT 1 FROM bb_booking bb WHERE ba2.id = bb.allocation_id)";

			$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
			if (!$this->db->next_record())
			{
				return False;
			}
			return $this->db->f('id', false);
		}

		public function delete_booking( $id )
		{
			$db = $this->db;
			$db->transaction_begin();
			$table_name = $this->table_name . '_cost';
			$sql = "DELETE FROM $table_name WHERE booking_id = ($id)";
			$db->query($sql, __LINE__, __FILE__);
			$table_name = $this->table_name . '_resource';
			$sql = "DELETE FROM $table_name WHERE booking_id = ($id)";
			$db->query($sql, __LINE__, __FILE__);
			$table_name = $this->table_name . '_targetaudience';
			$sql = "DELETE FROM $table_name WHERE booking_id = ($id)";
			$db->query($sql, __LINE__, __FILE__);
			$table_name = $this->table_name . '_agegroup';
			$sql = "DELETE FROM $table_name WHERE booking_id = ($id)";
			$db->query($sql, __LINE__, __FILE__);
			$table_name = $this->table_name;
			$sql = "DELETE FROM $table_name WHERE id = ($id)";
			$db->query($sql, __LINE__, __FILE__);
			return	$db->transaction_commit();
		}

		public function delete_allocation( $id )
		{
			$db = $this->db;
			$db->transaction_begin();
			$sql = "DELETE FROM bb_allocation_cost WHERE allocation_id = ($id)";
			$db->query($sql, __LINE__, __FILE__);
			$sql = "DELETE FROM bb_allocation_resource WHERE allocation_id = ($id)";
			$db->query($sql, __LINE__, __FILE__);
			$sql = "DELETE FROM bb_allocation WHERE id = ($id)";
			$db->query($sql, __LINE__, __FILE__);
			return	$db->transaction_commit();
		}

		public function got_no_allocation( $booking )
		{
			$table_name = $this->table_name;
			$db = $this->db;

			$from = "'" . $booking['from_'] . "'";
			$to = "'" . $booking['to_'] . "'";
			$org_id = (int)$booking['organization_id'];
			$season_id = (int)$booking['season_id'];
			$resources = implode(",", $booking['resources']);

			if(empty($booking['resources']))
			{
				return True;
			}

			$sql = "SELECT id FROM bb_allocation ba2 WHERE ba2.from_ = ($from) AND ba2.to_ = ($to) AND ba2.organization_id = ($org_id) AND ba2.season_id = ($season_id) AND EXISTS ( SELECT 1 FROM bb_allocation  a,bb_allocation_resource b WHERE a.id = b.allocation_id AND b.resource_id IN ($resources))";
			$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
			if (!$this->db->next_record())
			{
				return True;
			}
			else
			{
				return False;
			}
		}

		function get_organization( $id )
		{
			$this->db->limit_query("SELECT name FROM bb_organization where id=(select organization_id from bb_group where id=($id))", 0, __LINE__, __FILE__, 1);
			if (!$this->db->next_record())
			{
				return False;
			}
			return $this->db->f('name', false);
		}

		function get_groups_of_organization( $grp_id )
		{
			$this->db->limit_query("select organization_id from bb_group where id=($grp_id)", 0, __LINE__, __FILE__, 1);
			if (!$this->db->next_record())
			{
				return False;
			}
			return $this->db->f('organization_id', false);
		}

		function get_resource( $id )
		{
			$this->db->limit_query("SELECT name FROM bb_resource where id=" . intval($id), 0, __LINE__, __FILE__, 1);
			if (!$this->db->next_record())
			{
				return False;
			}
			return $this->db->f('name', false);
		}

		function get_building( $id )
		{
			$this->db->limit_query("SELECT name FROM bb_building where id=" . intval($id), 0, __LINE__, __FILE__, 1);
			if (!$this->db->next_record())
			{
				return False;
			}
			return $this->db->f('name', false);
		}

		function get_season( $id )
		{
			$this->db->limit_query("SELECT id FROM bb_season where id=" . intval($id), 0, __LINE__, __FILE__, 1);
			if (!$this->db->next_record())
			{
				return False;
			}
			return $this->db->f('id', false);
		}

		public function get_group_contacts_of_organization( $id )
		{
			$results = array();
			$sql = "SELECT bb_group_contact.id,bb_group_contact.group_id,bb_group_contact.email FROM bb_group,bb_group_contact WHERE bb_group.id=bb_group_contact.group_id AND bb_group.active = 1 AND bb_group.organization_id=(" . intval($id) . ")";
			$this->db->query($sql, __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = array('id' => $this->db->f('id', false),
					'group_id' => $this->db->f('group_id', false),
					'email' => $this->db->f('email', false));
			}
			return $results;
		}

		public function get_all_group_of_organization_from_groupid( $id )
		{
			$results = array();
			$sql = "SELECT bb_group_contact.id,bb_group_contact.group_id,bb_group_contact.email FROM bb_group,bb_group_contact WHERE bb_group.id=bb_group_contact.group_id AND bb_group.active = 1 AND bb_group.organization_id=(select organization_id from bb_group where id=(" . intval($id) . "))";
			$this->db->query($sql, __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = array('id' => $this->db->f('id', false),
					'group_id' => $this->db->f('group_id', false),
					'email' => $this->db->f('email', false));
			}
			return $results;
		}

		function get_organizations()
		{
			$results = array();
			$results[] = array('id' => 0, 'name' => lang('Not selected'));
			$this->db->query("SELECT id, name FROM bb_organization WHERE active = 1 ORDER BY name ASC", __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = array('id' => $this->db->f('id', false),
					'name' => $this->db->f('name', false));
			}
			return $results;
		}

		public function find_expired()
		{
			$table_name = $this->table_name;
			$db = $this->db;
			$expired_conditions = $this->find_expired_sql_conditions();
			return $this->read(array('filters' => array('where' => $expired_conditions), 'results' => 'all'));
		}

		protected function find_expired_sql_conditions()
		{
			$table_name = $this->table_name;
			$now = date('Y-m-d');
			return "({$table_name}.active != 0 AND {$table_name}.completed = 0 AND {$table_name}.to_ < '{$now}')";
		}

		public function complete_expired( &$bookings )
		{
			$table_name = $this->table_name;
			$db = $this->db;
			$ids = join(', ', array_map(array($this, 'select_id'), $bookings));
			$sql = "UPDATE $table_name SET completed = 1 WHERE {$table_name}.id IN ($ids);";
			$db->query($sql, __LINE__, __FILE__);

			//Avoid double invoices
			if ($ids)
			{
				$allocations = array();
				$sql = "SELECT DISTINCT allocation_id FROM bb_booking WHERE id IN ($ids) AND allocation_id IS NOT NULL";
				$db->query($sql, __LINE__, __FILE__);
				while ($this->db->next_record())
				{
					$allocations[] = $db->f('allocation_id');
				}

				if ($allocations)
				{
					$sql = 'UPDATE bb_allocation SET completed = 1 WHERE id IN (' . implode(',', $allocations) . ')';
					$db->query($sql, __LINE__, __FILE__);
				}
			}
		}

		function get_screen_resources( $building_id, $res = False )
		{
			$building_id = intval($building_id);
			if (intval($res) == 1)
			{
				$type = "AND ba.name IN ('Idrett','Friidrett','Svømming')";
			}
			elseif (intval($res) == 2)
			{
				$type = "AND ba.name IN ('Barnehage','Styrkerom','Møterom')";
			}
			else
			{
				$type = '';
			}
			$results = array();
			$sql = "SELECT br.id
                    FROM bb_resource br, bb_activity ba, bb_building_resource bre
                    WHERE ba.id = br.activity_id " . $type . "
                    AND br.id = bre.resource_id
                    AND bre.building_id = " . $building_id . "
                    AND br.active = 1
                    ORDER by br.sort";

			$this->db->query($sql, __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = $this->db->f('id', false);
			}
			return $results;
		}

		function get_screen_allocation( $building_id, $start, $end, $resources = False )
		{
			$start = $start->format('Y-m-d H:i');
			$end = $end->format('Y-m-d H:i');
			$building_id = intval($building_id);
			$results = array();
			$sql = "SELECT
                    bb_allocation.id AS id,
                    bb_allocation.building_name AS building_name,
                    bb_allocation.from_ AS from_,
                    bb_allocation.to_ AS to_,
                    bb_allocation.organization_id AS organization_id,
                    bb_resource.id AS resource_id,
                    bb_resource.name AS resource_name,
                    bb_resource.sort AS sort,
                    bb_building_resource.building_id AS building_id,
                    bb_organization.name AS organization_name,
                    bb_organization.shortname AS organization_shortname
                    FROM bb_allocation
                    INNER JOIN bb_allocation_resource ON (bb_allocation.id = bb_allocation_resource.allocation_id)
                    INNER JOIN bb_resource ON  (bb_allocation_resource.resource_id  = bb_resource.id)
                    INNER JOIN bb_building_resource ON (bb_building_resource.resource_id  = bb_resource.id)
                    INNER JOIN bb_organization ON  (bb_organization.id  = bb_allocation.organization_id)
                    WHERE bb_allocation.from_ > '" . $start . "' AND bb_allocation.to_ < '" . $end . "'
                    AND bb_building_resource.building_id = (" . $building_id . ")
                     " . $resources . "
                    AND bb_allocation.active = 1
                    ORDER BY building_name, sort, from_;";
			$this->db->query($sql, __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = array(
					'id' => $this->db->f('id', false),
					'building_id' => $this->db->f('building_id', false),
					'resource_id' => $this->db->f('resource_id', false),
					'organization_id' => $this->db->f('organization_id', false),
					'building_name' => $this->db->f('building_name', false),
					'resource_name' => $this->db->f('resource_name', false),
					'organization_name' => $this->db->f('organization_name', false),
					'organization_shortname' => $this->db->f('organization_shortname', false),
					'from_' => $this->db->f('from_', false),
					'to_' => $this->db->f('to_', false),
				);
			}
			return $results;
		}

		function get_screen_booking( $building_id, $start, $end, $resources = False )
		{
			$start = $start->format('Y-m-d H:i');
			$end = $end->format('Y-m-d H:i');
			$building_id = intval($building_id);

			$results = array();
			$sql = "SELECT
                    bb_booking.id AS id,
                    bb_booking.allocation_id AS allocation_id,
                    bb_booking.building_name as buidling_name,
                    bb_booking.from_ AS from_,
                    bb_booking.to_ AS to_,
                    bb_booking.group_id AS group_id,
                    bb_resource.id AS resource_id,
                    bb_resource.name AS resource_name,
                    bb_resource.sort AS sort,
                    bb_building_resource.building_id AS building_id,
                    bb_group.name AS group_name,
                    bb_group.shortname AS group_shortname
                    FROM bb_booking
                    INNER JOIN bb_booking_resource ON (bb_booking_resource.booking_id = bb_booking.id)
                    INNER JOIN bb_resource ON  (bb_booking_resource.resource_id  = bb_resource.id)
                    INNER JOIN bb_building_resource ON (bb_building_resource.resource_id  = bb_resource.id)
                    INNER JOIN bb_group ON (bb_group.id = bb_booking.group_id)
                    WHERE bb_booking.from_ > '" . $start . "' AND bb_booking.to_ < '" . $end . "'
                    AND bb_building_resource.building_id = (" . $building_id . ")
                     " . $resources . "
                    AND bb_booking.active = 1
                    ORDER BY building_name,sort, from_;";
			$this->db->query($sql, __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = array(
					'id' => $this->db->f('id', false),
					'building_id' => $this->db->f('building_id', false),
					'resource_id' => $this->db->f('resource_id', false),
					'group_id' => $this->db->f('group_id', false),
					'allocation_id' => $this->db->f('allocation_id', false),
					'building_name' => $this->db->f('building_name', false),
					'resource_name' => $this->db->f('resource_name', false),
					'group_name' => $this->db->f('group_name', false),
					'group_shortname' => $this->db->f('group_shortname', false),
					'from_' => $this->db->f('from_', false),
					'to_' => $this->db->f('to_', false),
				);
			}
			return $results;
		}

		function get_screen_event( $building_id, $start, $end, $resources = '' )
		{
			$start = $start->format('Y-m-d H:i:s');

			$test = $end->format('H:i');

			if ($test != '00:00')
			{
				$end = $end->format('Y-m-d H:i:s');
			}
			else
			{
				$end = $end->format('Y-m-d') . ' 24:00:00';
			}

			$building_id = intval($building_id);
			$results = array();
			$sql = "SELECT
                    bb_event.id AS id,
                    bb_event.building_name as building_name,
                    bb_event.description as description,
                    bb_event.from_ AS from_,
                    bb_event.to_ AS to_,
                    bb_resource.sort AS sort,
                    bb_resource.id AS resource_id,
                    bb_resource.name AS resource_name,
                    bb_building_resource.building_id AS building_id
                    FROM bb_event
                    INNER JOIN bb_event_resource ON (bb_event_resource.event_id = bb_event.id)
                    INNER JOIN bb_resource ON (bb_resource.id = bb_event_resource.resource_id)
                    INNER JOIN bb_building_resource ON (bb_building_resource.resource_id  = bb_resource.id)
                    WHERE
                    (
                    (bb_event.from_ >= '" . $start . "' AND bb_event.to_ <= '" . $end . "')
                    OR (bb_event.from_ < '" . $start . "' AND bb_event.to_ <= '" . $end . "' AND bb_event.to_ > '" . $start . "')
                    OR (bb_event.from_ >='" . $start . "' AND bb_event.from_ < '" . $end . "' AND bb_event.to_ > '" . $end . "')
                    OR (bb_event.from_ < '" . $start . "' AND bb_event.to_ > '" . $end . "')
                    )
                    AND bb_building_resource.building_id = (" . $building_id . ")
                     " . $resources . "
                    AND bb_event.active = 1
                    ORDER BY building_name,sort,from_;";
			$this->db->query($sql, __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = array(
					'id' => $this->db->f('id', false),
					'building_id' => $this->db->f('building_id', false),
					'resource_id' => $this->db->f('resource_id', false),
					'building_name' => $this->db->f('building_name', false),
					'resource_id' => $this->db->f('resource_id', false),
					'resource_name' => $this->db->f('resource_name', false),
					'description' => $this->db->f('description', false),
					'from_' => $this->db->f('from_', false),
					'to_' => $this->db->f('to_', false),
				);
			}
			return $results;
		}

		function get_ordered_costs( $id )
		{
			$results = array();
			$this->db->query("SELECT * FROM bb_booking_cost WHERE booking_id=($id) ORDER BY time DESC", __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = array(
					'time' => $this->db->f('time'),
					'author' => $this->db->f('author', true),
					'comment' => $this->db->f('comment', true),
					'cost' => $this->db->f('cost')
				);
			}
			return $results;
		}
	}