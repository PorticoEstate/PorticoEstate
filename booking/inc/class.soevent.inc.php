<?php
	phpgw::import_class('phpgwapi.datetime');
	phpgw::import_class('booking.socommon');

	class booking_soevent extends booking_socommon
	{

		function __construct()
		{
			parent::__construct('bb_event', array(
				'id' => array('type' => 'int'),
				'id_string' => array('type' => 'string', 'required' => false, 'default' => '0',
					'query' => true),
				'active' => array('type' => 'int', 'required' => true),
				'skip_bas' => array('type' => 'int', 'required' => true),
				'activity_id' => array('type' => 'int', 'required' => true),
				'application_id' => array('type' => 'int', 'required' => false),
				'name' => array('type' => 'string', 'query' => true, 'required' => true),
				'organizer' => array('type' => 'string', 'query' => true, 'required' => true),
				'homepage' => array('type' => 'string', 'query' => true, 'required' => false),
				'description' => array('type' => 'string', 'required' => false, 'query' => true),
				'equipment' => array('type' => 'string', 'query' => true, 'required' => false),
				'building_id' => array('type' => 'int', 'required' => true),
				'building_name' => array('type' => 'string', 'required' => true, 'query' => true),
				'from_' => array('type' => 'string', 'required' => true),
				'to_' => array('type' => 'string', 'required' => true),
				'cost' => array('type' => 'decimal', 'required' => true),
				'contact_name' => array('type' => 'string', 'required' => true, 'query' => true),
				'contact_email' => array('type' => 'string', 'sf_validator' => createObject('booking.sfValidatorEmail', array(), array(
					'invalid' => '%field% is invalid'))),
				'contact_phone' => array('type' => 'string'),
				'completed' => array('type' => 'int', 'required' => true, 'nullable' => false,
					'default' => '0','query' => true),
				'access_requested' => array('type' => 'int', 'required' => false, 'nullable' => true,
					'default' => '0'),
				'reminder' => array('type' => 'int', 'required' => true, 'nullable' => false,
					'default' => '1'),
				'is_public' => array('type' => 'int', 'required' => true, 'nullable' => false,
					'default' => '1'),
				'secret' => array('type' => 'string', 'required' => true),
				'sms_total' => array('type' => 'int', 'required' => false),
				'participant_limit' => array('type' => 'int', 'required' => false),
				'customer_organization_name' => array('type' => 'string', 'required' => False,
					'query' => true),
				'customer_organization_id' => array('type' => 'int', 'required' => False),
				'customer_identifier_type' => array('type' => 'string', 'required' => False),
				'customer_ssn' => array('type' => 'string', 'sf_validator' => createObject('booking.sfValidatorNorwegianSSN'),
					'required' => false),
				'customer_organization_number' => array('type' => 'string', 'sf_validator' => createObject('booking.sfValidatorNorwegianOrganizationNumber', array(), array(
					'invalid' => '%field% is invalid'))),
				'customer_internal' => array('type' => 'int', 'required' => true),
				'include_in_list' => array('type' => 'int', 'required' => true, 'nullable' => false, 'default' => '0'),
				'activity_name' => array('type' => 'string',
					'query' => true,
					'join' => array(
						'table' => 'bb_activity',
						'fkey' => 'activity_id',
						'key' => 'id',
						'column' => 'name'
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
						'column' => array('agegroup_id' => array('type' => 'int', 'required' => true),
							'male' => array('type' => 'int', 'required' => true), 'female' => array('type' => 'int',
								'required' => true)),
					)),
				'comments' => array('type' => 'string',
					'manytomany' => array(
						'table' => 'bb_event_comment',
						'key' => 'event_id',
						'column' => array('time' => array('type' => 'timestamp', 'read_callback' => 'modify_by_timezone'), 'author', 'comment', 'type'),
						'order' => array('sort' => 'time', 'dir' => 'ASC')
					)),
				'costs' => array('type' => 'string',
					'manytomany' => array(
						'table' => 'bb_event_cost',
						'key' => 'event_id',
						'column' => array('time' => array('type' => 'timestamp', 'read_callback' => 'modify_by_timezone'), 'author', 'comment', 'cost'),
						'order' => array('sort' => 'time', 'dir' => 'ASC')
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

		function update( $entry )
		{
			$receipt = parent::update($entry);

			$cost = $this->_marshal((float)$entry['cost'], 'decimal');
			$id = (int)$entry['id'];

			$description = mb_substr($entry['from_'], 0, -3, 'UTF-8') . ' - ' . mb_substr($entry['to_'], 0, -3, 'UTF-8');

			$sql = "UPDATE bb_completed_reservation SET cost = '{$cost}', from_ = '{$entry['from_']}',"
			. " to_ = '{$entry['to_']}', description = '{$description}'"
			. " WHERE reservation_type = 'event'"
			. " AND reservation_id = {$id}"
			. " AND export_file_id IS NULL";

			$this->db->query($sql, __LINE__, __FILE__);

			return $receipt;
		}

		function get_building_info( $id )
		{
			$sql = "SELECT bb_building.id, bb_building.name, bb_building.email,"
				. " bb_building.tilsyn_email, bb_building.tilsyn_email2"
				. " FROM bb_building, bb_resource, bb_event_resource, bb_building_resource"
				. " WHERE bb_resource.id=bb_building_resource.resource_id"
				. " AND bb_building.id=bb_building_resource.building_id"
				. " AND bb_resource.id=bb_event_resource.resource_id"
				. " AND bb_event_resource.event_id=" . intval($id);

			$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
			if (!$this->db->next_record())
			{
				return False;
			}
			return array('id' => $this->db->f('id', false),
				'name' => $this->db->f('name', true),
				'email' => $this->db->f('email', true),
				'tilsyn_email' => $this->db->f('tilsyn_email', true),
				'tilsyn_email2' => $this->db->f('tilsyn_email2', true));
		}

		function get_ordered_comments( $id )
		{
			$id = (int) $id;
			$results = array();
			$this->db->query("select time,author,comment,type from bb_event_comment where event_id=($id) order by time desc", __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = array('time' => $this->db->f('time', false),
					'author' => $this->db->f('author', true),
					'comment' => $this->db->f('comment', true),
					'type' => $this->db->f('type', false));
			}

			foreach ($results as &$value)
			{
				$this->modify_by_timezone($value['time']);
			}

			return $results;
		}

		function get_ordered_costs( $id )
		{
			$id = (int) $id;
			$results = array();
			$this->db->query("SELECT * FROM bb_event_cost WHERE event_id=($id) ORDER BY time DESC", __LINE__, __FILE__);
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

		function get_resource_info( $id )
		{
			$this->db->limit_query("SELECT bb_resource.id, bb_resource.name FROM bb_resource WHERE bb_resource.id=" . intval($id), 0, __LINE__, __FILE__, 1);
			if (!$this->db->next_record())
			{
				return False;
			}
			return array('id' => $this->db->f('id', false),
				'name' => $this->db->f('name', true));
		}

		function get_overlap_time_info( $resource_id, $overlap_id, $type )
		{
			if ($type == 'allocation')
			{
				$this->db->limit_query("SELECT bb_allocation.from_,bb_allocation.to_ FROM bb_allocation,bb_allocation_resource WHERE bb_allocation.id = $overlap_id
 AND  bb_allocation_resource.allocation_id  = $overlap_id AND bb_allocation_resource.resource_id =" . intval($resource_id), 0, __LINE__, __FILE__, 1);
			}
			else
			{
				$this->db->limit_query("SELECT bb_booking.from_,bb_booking.to_ FROM bb_booking,bb_booking_resource WHERE bb_booking.id = $overlap_id
 AND  bb_booking_resource.booking_id  = $overlap_id AND bb_booking_resource.resource_id =" . intval($resource_id), 0, __LINE__, __FILE__, 1);
			}
			if (!$this->db->next_record())
			{
				return False;
			}
			return array('from' => $this->db->f('from_', false),
				'to' => $this->db->f('to_', false));
		}

		function get_contact_mail( $id, $type )
		{
			$id = (int) $id;
			$data = array();
			if ($type == 'allocation')
			{
				$sql = "SELECT bb_organization_contact.email, bb_organization_contact.phone"
					. " FROM bb_organization_contact"
					. " WHERE organization_id IN (SELECT bb_allocation.organization_id FROM bb_allocation WHERE id=$id)";
			}
			else
			{
				$sql = "SELECT bb_group_contact.email, bb_group_contact.phone"
					. " FROM bb_group_contact"
					. " WHERE group_id IN (SELECT bb_booking.group_id FROM bb_booking WHERE id=$id)";
			}
			$this->db->query($sql, __LINE__, __FILE__);
			if ($result = $this->db->resultSet)
			{
				foreach ($result as $res)
				{
					$data[] = array(
						'email' => $res['email'],
						'phone'	=> $res['phone'],
					);
				}
			}

			return $data;
		}

		public function update_comment( $allids )
		{
			$db = $this->db;
			$config = CreateObject('phpgwapi.config', 'booking');
			$config->read();
			$external_site_address = isset($config->config_data['external_site_address']) && $config->config_data['external_site_address'] ? $config->config_data['external_site_address'] : $GLOBALS['phpgw_info']['server']['webserver_url'];

			$comment = lang('Multiple Events was created') . ',<br />' . lang('Event') . ' ';
			foreach ($allids as $id)
			{
				$comment .= '<a href="' . $external_site_address . '/?menuaction=booking.uievent.edit&id=' . $id[0] . '">#' . $id[0] . '</a>, ';
			}
			$comment = substr($comment, 0, -2);
			$comment .= '.';
			foreach ($allids as $id)
			{
				$myid = $id[0];
				$sql = "UPDATE bb_event_comment SET comment='" . $comment . "' WHERE event_id=" . intval($myid) . ";";
				$db->query($sql, __LINE__, __FILE__);
			}
		}

		public function add_single_comment( $event_id, $comment, $type = 'comment'  )
		{
			$sql = "INSERT INTO bb_event_comment (event_id, time, author, comment, type) VALUES (?, ?, ?, ?, ?)";
			$valueset=array();
			$valueset[] = array
			(
				1	=> array
				(
					'value'	=> $event_id,
					'type'	=> PDO::PARAM_INT
				),
				2	=> array
				(
					'value'	=> date('Y-m-d H:i'),
					'type'	=> PDO::PARAM_STR
				),
				3	=> array
				(
					'value'	=> $GLOBALS['phpgw_info']['user']['account_id'],
					'type'	=> PDO::PARAM_INT
				),
				4	=> array
				(
					'value'	=> $this->db->db_addslashes($comment),
					'type'	=> PDO::PARAM_STR
				),
				5	=> array
				(
					'value'	=> $type,
					'type'	=> PDO::PARAM_STR
				),
			);

			return $this->db->insert($sql, $valueset, __LINE__, __FILE__);
		}

		protected function doValidate( $entity, booking_errorstack $errors )
		{
			$event_id = $entity['id'] ? $entity['id'] : -1;
			// Make sure to_ > from_
			$from_ = new DateTime($entity['from_']);
			$to_ = new DateTime($entity['to_']);
			$start = $from_->format('Y-m-d H:i');
			$end = $to_->format('Y-m-d H:i');

			if ($from_ > $to_ || $from_ === $to_)
			{
				$errors['from_'] = lang('Invalid from date');
			}
			if (strlen($entity['contact_name']) > 50)
			{
				$errors['contact_name'] = lang('Contact information name is to long. max 50 characters');
			}
			if ($entity['resources'])
			{
				$rids = join(',', array_map("intval", $entity['resources']));
				// Check if we overlap with any existing event
				$this->db->query("SELECT e.id FROM bb_event e
									WHERE e.active = 1 AND e.id <> $event_id AND
									e.id IN (SELECT event_id FROM bb_event_resource WHERE resource_id IN ($rids)) AND
									((e.from_ >= '$start' AND e.from_ < '$end') OR
						 			 (e.to_ > '$start' AND e.to_ <= '$end') OR
						 			 (e.from_ < '$start' AND e.to_ > '$end'))", __LINE__, __FILE__);
				if ($this->db->next_record())
				{
					$errors['event'] = lang('Overlaps with existing event') . " #" . $this->db->f('id');
				}
				// Check if we overlap with any existing allocation
				$this->db->query("SELECT a.id FROM bb_allocation a
									WHERE a.active = 1 AND
									a.id IN (SELECT allocation_id FROM bb_allocation_resource WHERE resource_id IN ($rids)) AND
									((a.from_ >= '$start' AND a.from_ < '$end') OR
						 			 (a.to_ > '$start' AND a.to_ <= '$end') OR
						 			 (a.from_ < '$start' AND a.to_ > '$end'))", __LINE__, __FILE__);
				if ($result = $this->db->resultSet)
				{
					foreach ($result as $r)
					{
						$allocation[] = $r['id'];
					}
					/** Need the id's. text for ui is added later on **/
					$errors['allocation'] = $allocation;
				}

				// Check if we overlap with any existing booking
				$this->db->query("SELECT b.id FROM bb_booking b
									WHERE  b.active = 1 AND
									b.id IN (SELECT booking_id FROM bb_booking_resource WHERE resource_id IN ($rids)) AND
									((b.from_ >= '$start' AND b.from_ < '$end') OR
						 			 (b.to_ > '$start' AND b.to_ <= '$end') OR
						 			 (b.from_ < '$start' AND b.to_ > '$end'))", __LINE__, __FILE__);
				if ($result = $this->db->resultSet)
				{
					foreach ($result as $r)
					{
						$booking[] = $r['id'];
					}
					/** Need the id's. text for ui is added later on **/
					$errors['booking'] = $booking;
				}
			}
		}

		/**
		 * Find list of orders related to events - without payments
		 * @return array
		 */
		public function find_expired_orders()
		{
			$sql = "SELECT bb_purchase_order.id"
				. " FROM bb_purchase_order"
				. " LEFT JOIN bb_payment ON bb_purchase_order.id = bb_payment.order_id"
				. " JOIN bb_event ON bb_purchase_order.reservation_type = 'event' AND bb_purchase_order.reservation_id = bb_event.id"
				. " WHERE bb_payment.id IS NULL AND bb_event.to_ < now()";

			$orders = array();
			$this->db->query($sql, __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$orders[] = (int)$this->db->f('id');
			}

			return $orders;
		}

		public function find_expired($update_reservation_time)
		{
			$table_name = $this->table_name;
			$db = $this->db;
			$expired_conditions = $this->find_expired_sql_conditions($update_reservation_time);
			return $this->read(array('filters' => array('where' => $expired_conditions), 'results' => 1000));
		}

		protected function find_expired_sql_conditions($update_reservation_time)
		{
			$table_name = $this->table_name;
//			$now = date('Y-m-d');
			return "({$table_name}.active != 0 AND {$table_name}.completed = 0 AND {$table_name}.to_ < '{$update_reservation_time}')";
		}

		public function complete_expired( &$events )
		{
			$table_name = $this->table_name;
			$db = $this->db;
			$ids = join(', ', array_map(array($this, 'select_id'), $events));
			$sql = "UPDATE $table_name SET completed = 1 WHERE {$table_name}.id IN ($ids);";
			$db->query($sql, __LINE__, __FILE__);
		}

		public function find_request_access($stage, $time_ahead)
		{
			$table_name = $this->table_name;
			$db = $this->db;
			$request_access_conditions = $this->find_request_access_sql_conditions($stage, $time_ahead);
			return $this->read(array('filters' => array('where' => $request_access_conditions), 'results' => 1000));
		}

		protected function find_request_access_sql_conditions($stage, $time_ahead)
		{
			$table_name = $this->table_name;

			$slightly_before	 = date('Y-m-d H:i:s', (phpgwapi_datetime::user_localtime() + $time_ahead) );
			$now				 = date('Y-m-d H:i:s', phpgwapi_datetime::user_localtime());

			$_conditions = " bb_resource_e_lock.e_lock_resource_id IS NOT NULL"
				. " AND {$table_name}.active != 0"
				. " AND {$table_name}.access_requested < " . (int) $stage
				. " AND ('{$slightly_before}' BETWEEN {$table_name}.from_ AND {$table_name}.to_ OR '{$now}' BETWEEN {$table_name}.from_ AND {$table_name}.to_)";

			$sql = "SELECT DISTINCT bb_event.id FROM bb_event"
				. " JOIN bb_event_resource ON bb_event.id = bb_event_resource.event_id"
				. " JOIN bb_resource_e_lock ON bb_event_resource.resource_id = bb_resource_e_lock.resource_id"
				. " WHERE $_conditions";

			$this->db->query($sql, __LINE__, __FILE__);
			$ids = array(-1);
			while ($this->db->next_record())
			{
				$ids[] = $this->db->f('id');
			}

			$conditions = "({$table_name}.id IN (" . implode(', ', $ids) . "))";

			return $conditions;
		}

		public function complete_request_access( &$events, $stage )
		{
			$stage = $stage ? (int) $stage : 1;
			$table_name = $this->table_name;
			$db = $this->db;
			$ids = join(', ', array_map(array($this, 'select_id'), $events));
			$sql = "UPDATE $table_name SET access_requested = {$stage} WHERE {$table_name}.id IN ($ids);";
			$db->query($sql, __LINE__, __FILE__);
		}

		public function delete_event( $id )
		{
			$id = (int) $id;
			$db = $this->db;
			$db->transaction_begin();

			$table_name = $this->table_name . '_cost';
			$sql = "DELETE FROM $table_name WHERE event_id = ($id)";
			$db->query($sql, __LINE__, __FILE__);

			$table_name = $this->table_name . '_comment';
			$sql = "DELETE FROM $table_name WHERE event_id = ($id)";
			$db->query($sql, __LINE__, __FILE__);

			$table_name = $this->table_name . '_agegroup';
			$sql = "DELETE FROM $table_name WHERE event_id = ($id)";
			$db->query($sql, __LINE__, __FILE__);

			$table_name = $this->table_name . '_targetaudience';
			$sql = "DELETE FROM $table_name WHERE event_id = ($id)";
			$db->query($sql, __LINE__, __FILE__);

			$table_name = $this->table_name . '_date';
			$sql = "DELETE FROM $table_name WHERE event_id = ($id)";
			$db->query($sql, __LINE__, __FILE__);

			$table_name = $this->table_name . '_resource';
			$sql = "DELETE FROM $table_name WHERE event_id = ($id)";
			$db->query($sql, __LINE__, __FILE__);

			$sql = "SELECT id, parent_id FROM bb_purchase_order WHERE reservation_type = 'event' AND reservation_id = $id";

			$db->query($sql, __LINE__, __FILE__);
			$db->next_record();
			$purchase_order_id = (int)$db->f('id');
			$purchase_parent_order_id = (int)$db->f('parent_id');

			if($purchase_order_id)
			{
				if($purchase_parent_order_id)
				{
					$db->query("DELETE FROM bb_purchase_order_line WHERE order_id = $purchase_order_id", __LINE__, __FILE__);
					$db->query("DELETE FROM bb_purchase_order WHERE reservation_type = 'event' AND reservation_id = $id", __LINE__, __FILE__);
				}
				else
				{
					$db->query("UPDATE bb_purchase_order SET reservation_type = NULL, reservation_id = NULL WHERE reservation_type = 'event' AND reservation_id = $id", __LINE__, __FILE__);
				}
			}

			$sql = "SELECT id FROM bb_completed_reservation WHERE reservation_id = $id AND reservation_type = 'event' AND export_file_id IS NULL";
			$db->query($sql, __LINE__, __FILE__);
			$db->next_record();
			$completed_reservation_id = (int)$db->f('id');
			if($completed_reservation_id)
			{
				$sql = "DELETE FROM bb_completed_reservation_resource WHERE completed_reservation_id = $completed_reservation_id";
				$db->query($sql, __LINE__, __FILE__);

				$sql = "DELETE FROM bb_completed_reservation WHERE id = $completed_reservation_id";
				$db->query($sql, __LINE__, __FILE__);
			}

			$table_name = $this->table_name;
			$sql = "DELETE FROM $table_name WHERE id = ($id)";
			$db->query($sql, __LINE__, __FILE__);

			return	$db->transaction_commit();
		}

		public function update_id_string()
		{
			$table_name = $this->table_name;
			$db = $this->db;
			$sql = "UPDATE $table_name SET id_string = cast(id AS varchar)";
			$db->query($sql, __LINE__, __FILE__);
		}

		function get_building( $id )
		{
			$this->db->limit_query("SELECT name FROM bb_building where id=" . intval($id), 0, __LINE__, __FILE__, 1);
			if (!$this->db->next_record())
			{
				return False;
			}
			return $this->db->f('name', true);
		}

		function get_org( $orgnumber )
		{
			if(!$orgnumber)
			{
				return array();
			}

			$sql = "SELECT id,name,street,zip_code,city FROM bb_organization WHERE (organization_number='" . $orgnumber . "' OR customer_organization_number='" . $orgnumber . "') AND active != 0";

			$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
			if ($this->db->next_record())
			{
				$results = array(
					'id' => $this->db->f('id', false),
					'name' => $this->db->f('name', true),
					'street' => $this->db->f('street', true),
					'zip_code' => $this->db->f('zip_code', false),
					'city' => $this->db->f('city', true),
					);
			}
			else
			{
				return array();
			}

			return $results;
		}

		function get_buildings()
		{
			$results = array();
			$results[] = array('id' => 0, 'name' => lang('Not selected'));
			$this->db->query("SELECT id, name FROM bb_building WHERE active != 0 ORDER BY name ASC", __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = array('id' => $this->db->f('id', false),
					'name' => $this->db->f('name', true));
			}
			return $results;
		}

		function get_activities_main_level()
		{
			$results = array();
			$results[] = array('id' => 0, 'name' => lang('Not selected'));
			$this->db->query("SELECT id,name FROM bb_activity WHERE parent_id is NULL", __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = array('id' => $this->db->f('id', false), 'name' => $this->db->f('name', true));
			}
			return $results;
		}

		function get_activities( $id )
		{
			$results = array();
			$this->db->query("select id from bb_activity where id = ($id) or  parent_id = ($id) or parent_id in (select id from bb_activity where parent_id = ($id))", __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = $this->_unmarshal($this->db->f('id', false), 'int');
			}
			return $results;
		}

		function get_resources( $ids )
		{

			$results = array();
			$this->db->query("select name from bb_resource where id in ($ids)", __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = $this->db->f('name', true);
			}
			return $results;
		}

	function get_events_from_date($from_date=null, $to_date=null, $org_info=null, $building_id=null, $facility_type_id=null, $filter_organization=null, $logged_in_as = false, $start=0, $end=50)
	{
		$filter_organization_sql = null;
		$facility_type_id_sql = null;
		$org_info_sql = null;
		$to_date_sql = null;
		$building_id_sql = null;

		if ($filter_organization && $logged_in_as)
		{
			$filter_organization_sql = " AND bbe.customer_organization_number = '$logged_in_as'";
		}
		else if (!$filter_organization && $logged_in_as)
		{
			$filter_organization_sql = " AND (include_in_list = 1 OR bbe.customer_organization_number = '$logged_in_as')";
		}
		else
		{
			$filter_organization_sql = " AND include_in_list = 1";
		}

		if ($facility_type_id !== "")
		{
			$facility_type_id_sql = " AND bbrc.id = '$facility_type_id' ";
		}
		if ($building_id !== "")
		{
			$building_id_sql = " AND bbe.building_id = '$building_id' ";
		}

		if (!empty($org_info))
		{
			$org_number_sql = "";
			if ($org_info['organization_number'] !== '')
			{
				$org_pre_sql = " AND ";
				$org_number_sql = "bbe.customer_organization_number='" . $org_info['organization_number'] ."' ";
				$org_info_sql = $org_pre_sql . $org_number_sql;
			}

			if ($org_info['name'] !== "")
			{
				$org_pre_sql = " AND (";
				$org_name_sql = ($org_number_sql == '' ? " bbe.organizer='" .$org_info['name'] ."')" : " OR bbe.organizer='" . $org_info['name'] ."') ");
				$org_info_sql = $org_pre_sql . $org_number_sql . $org_name_sql;
			}
		}
		if ($to_date !== "")
		{
			$to_date_sql = " AND bbe.to_ <= '$to_date' ";
		}
		$sql_query = "
				SELECT DISTINCT ON (bbe.id, bbe.from_)
				    bbe.id as event_id,
			       	bbe.from_,
			       	bbe.to_,
				    bbe.building_id as building_id,
			       	bbe.building_name as location_name,
				    bbe.name as event_name,
				    bbe.customer_organization_id,
				    bbe.customer_organization_number,
					bbe.organizer,
			       	br.name as resource_name,
			    	bbrc.name as resource_type
				from bb_event bbe
				    inner join
				        bb_event_resource ber on bbe.id = ber.event_id
				    inner join
				        bb_resource br on ber.resource_id = br.id
				    inner join
				        bb_rescategory bbrc on br.rescategory_id = bbrc.id
				where
				  	bbe.from_ >= '$from_date' "
						.$to_date_sql
						.$org_info_sql
						.$building_id_sql
						.$facility_type_id_sql
						.$filter_organization_sql
						." AND bbe.active = 1
						ORDER BY bbe.from_ ASC";

		$this->db->limit_query($sql_query, $start, __LINE__, __FILE__, $end);

		$results = array();
		while ($this->db->next_record())
		{
			$results[] = array(
				'from' => $this->db->f('from_'),
				'to' => $this->db->f('to_'),
				'event_name' => $this->db->f('event_name', true),
				'location_name' => $this->db->f('location_name', true),
				'event_id' => $this->db->f('event_id'),
				'building_id' => $this->db->f('building_id'),
				'org_id' => $this->db->f('customer_organization_id'),
				'org_num' => $this->db->f('customer_organization_number'),
				'organizer' => $this->db->f('organizer', true)
			);

		}
		return $results;
	}
}
