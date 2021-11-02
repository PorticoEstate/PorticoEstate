<?php
	phpgw::import_class('booking.socommon');

	class booking_soapplication extends booking_socommon
	{

		function __construct()
		{
			parent::__construct('bb_application', array(
				'id'							 => array('type' => 'int'),
				'id_string'						 => array('type'		 => 'string', 'required'	 => false, 'default'	 => '0',
					'query'		 => true),
				'active'						 => array('type' => 'int'),
				'display_in_dashboard'			 => array('type' => 'int'),
				'type'							 => array('type' => 'string'),
				'status'						 => array('type' => 'string', 'required' => true),
				'secret'						 => array('type' => 'string', 'required' => true),
				'created'						 => array('type' => 'timestamp'), //,'read_callback' => 'modify_by_timezone'),
				'modified'						 => array('type' => 'timestamp'), //,'read_callback' => 'modify_by_timezone'),
				'building_name'					 => array('type' => 'string', 'required' => true, 'query' => true),
				'frontend_modified'				 => array('type' => 'timestamp'), //,'read_callback' => 'modify_by_timezone'),
				'owner_id'						 => array('type' => 'int', 'required' => true),
				'case_officer_id'				 => array('type' => 'int', 'required' => false),
				'activity_id'					 => array('type' => 'int', 'required' => true),
				'status'						 => array('type' => 'string', 'required' => true),
				'customer_identifier_type'		 => array('type' => 'string', 'required' => true),
				'customer_ssn'					 => array('type'			 => 'string', 'query'			 => true, 'sf_validator'	 => createObject('booking.sfValidatorNorwegianSSN', array(
						'full_required' => false)), 'required'		 => false),
				'customer_organization_number'	 => array('type'			 => 'string', 'query'			 => true,
					'sf_validator'	 => createObject('booking.sfValidatorNorwegianOrganizationNumber', array(), array(
						'invalid' => '%field% is invalid'))),
				'owner_name'					 => array('type'	 => 'string', 'query'	 => true,
					'join'	 => array(
						'table'	 => 'phpgw_accounts',
						'fkey'	 => 'owner_id',
						'key'	 => 'account_id',
						'column' => 'account_lid'
					)),
				'activity_name'					 => array('type'	 => 'string',
					'join'	 => array(
						'table'	 => 'bb_activity',
						'fkey'	 => 'activity_id',
						'key'	 => 'id',
						'column' => 'name'
					)),
				'name'							 => array('type' => 'string', 'query' => true, 'required' => true),
				'organizer'						 => array('type' => 'string', 'query' => true, 'required' => true),
				'homepage'						 => array('type' => 'string', 'query' => true, 'required' => false, 'read_callback' => 'validate_url'),
				'description'					 => array('type' => 'string', 'query' => true, 'required' => false),
				'equipment'						 => array('type' => 'string', 'query' => true, 'required' => false),
				'contact_name'					 => array('type' => 'string', 'query' => true, 'required' => true),
				'contact_email'					 => array('type'			 => 'string', 'required'		 => true, 'sf_validator'	 => createObject('booking.sfValidatorEmail', array(), array(
						'invalid' => '%field% is invalid'))),
				'contact_phone'					 => array('type' => 'string', 'required' => true),
				'case_officer_name'				 => array('type'	 => 'string', 'query'	 => true,
					'join'	 => array(
						'table'	 => 'phpgw_accounts',
						'fkey'	 => 'case_officer_id',
						'key'	 => 'account_id',
						'column' => 'account_lid'
					)),
				'audience'						 => array('type'		 => 'int', 'required'	 => true,
					'manytomany' => array(
						'table'	 => 'bb_application_targetaudience',
						'key'	 => 'application_id',
						'column' => 'targetaudience_id'
					)),
				'agegroups'						 => array('type'		 => 'int', 'required'	 => true,
					'manytomany' => array(
						'table'	 => 'bb_application_agegroup',
						'key'	 => 'application_id',
						'column' => array(
							'agegroup_id'	 => array('type' => 'int', 'required' => true),
							'male'			 => array('type' => 'int', 'required' => true),
							'female'		 => array('type' => 'int', 'required' => true)),
					)),
				'dates'							 => array('type'		 => 'timestamp', 'required'	 => true,
					'manytomany' => array(
						'table'	 => 'bb_application_date',
						'key'	 => 'application_id',
						'column' => array('from_', 'to_', 'id')
					)),
				'comments'						 => array('type'		 => 'string',
					'manytomany' => array(
						'table'	 => 'bb_application_comment',
						'key'	 => 'application_id',
						'column' => array('time' => array('type' => 'timestamp', 'read_callback' => 'modify_by_timezone'), 'author', 'comment', 'type'),
						'order'	 => array('sort' => 'time', 'dir' => 'ASC')
					)),
				'resources'						 => array('type'		 => 'int', 'required'	 => true,
					'manytomany' => array(
						'table'	 => 'bb_application_resource',
						'key'	 => 'application_id',
						'column' => 'resource_id'
					)),
				'responsible_street'			 => array('type' => 'string', 'required' => true),
				'responsible_zip_code'			 => array('type' => 'string', 'required' => true),
				'responsible_city'				 => array('type' => 'string', 'required' => true),
				'session_id'					 => array('type' => 'string', 'required' => false),
				'agreement_requirements'		 => array('type' => 'string', 'required' => false),
				'external_archive_key'			 => array('type' => 'string', 'required' => false),
				'customer_organization_name'	 => array('type'		 => 'string', 'required'	 => False,
					'query'		 => true),
				'customer_organization_id'		 => array('type' => 'int', 'required' => False),
				)
			);
		}

		protected function doValidate( $entity, booking_errorstack $errors )
		{
			$event_id = $entity['id'] ? $entity['id'] : -1;
			// Make sure to_ > from_
			foreach ($entity['dates'] as $date)
			{
				$from_	 = new DateTime($date['from_']);
				$to_	 = new DateTime($date['to_']);
				$start	 = $from_->format('Y-m-d H:i');
				$end	 = $to_->format('Y-m-d H:i');
				if ($from_ > $to_ || $from_ == $to_)
				{
					$errors['from_'] = lang('Invalid from date');
				}
				else if (empty($date['from_']) || empty($date['to_']))
				{
					$errors['dates'] = lang('date is required');
				}
			}
			if (strlen($entity['contact_name']) > 50)
			{
				$errors['contact_name'] = lang('Contact information name is to long. max 50 characters');
			}

			foreach ($entity['resources'] as $esource_id)
			{
				if ((int)$esource_id < 0)
				{
					continue;
				}
				$this->db->query("SELECT direct_booking, direct_booking_season_id FROM bb_resource WHERE id = " . (int)$esource_id, __LINE__, __FILE__);
				$this->db->next_record();
				$direct_booking = $this->db->f('direct_booking');

				if ($direct_booking && $direct_booking < time())
				{
					$direct_booking_season_id = $this->db->f('direct_booking_season_id');
					if (!$direct_booking_season_id)
					{
						$errors['season_boundary'] = lang("This resource is not related to a direct booking season");
					}
					else
					{
						foreach ($entity['dates'] as $date)
						{
							$from_	 = new DateTime($date['from_']);
							$to_	 = new DateTime($date['to_']);

							if (!CreateObject('booking.soseason')->timespan_within_season($direct_booking_season_id, $from_, $to_))
							{
								$errors['season_boundary'] = lang("This application is not within the selected season");
							}
						}
					}
				}
			}
		}

		function get_building_info( $id )
		{
			$id	 = (int)$id;
			$sql = "SELECT bb_building.id, bb_building.name"
				. " FROM bb_building, bb_resource, bb_application_resource, bb_building_resource"
				. " WHERE bb_building.id= bb_building_resource.building_id AND  bb_resource.id = bb_building_resource.resource_id AND bb_resource.id=bb_application_resource.resource_id AND bb_application_resource.application_id=({$id})";

			$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
			if (!$this->db->next_record())
			{
				return False;
			}
			return array('id'	 => $this->db->f('id', false),
				'name'	 => $this->db->f('name', true));
		}

//		function get_accepted($id)
		function get_rejected( $id )
		{
			$sql	 = "SELECT bad.from_, bad.to_
					FROM bb_application ba, bb_application_date bad, bb_event be
					WHERE ba.id=($id)
					AND ba.id=bad.application_id
					AND ba.id=be.application_id
					AND be.from_=bad.from_
					AND be.to_=bad.to_";
			$results = array();
			$this->db->query($sql, __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = array('from_'	 => $this->db->f('from_', false),
					'to_'	 => $this->db->f('to_', false));
			}
			return $results;
		}

//		function get_rejected($id)
		function get_accepted( $id )
		{
			$sql	 = "SELECT bad.from_, bad.to_ FROM bb_application ba, bb_application_date bad
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
			$this->db->query($sql, __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = array('from_'	 => $this->db->f('from_', false),
					'to_'	 => $this->db->f('to_', false));
			}
			return $results;
		}

		function get_tilsyn_email( $id )
		{
			$sql = "SELECT tilsyn_email, tilsyn_email2, email FROM bb_building where id=(select id from bb_building where name = '$id' AND active = 1)";
			$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
			if (!$this->db->next_record())
			{
				return False;
			}
			return array('email1' => $this->db->f('tilsyn_email', false),
				'email2' => $this->db->f('tilsyn_email2', false),
				'email3' => $this->db->f('email', false));
		}

		function get_resource_name( $id )
		{
			$list	 = implode(",", $id);
			$results = array();
			$this->db->query("SELECT name FROM bb_resource where id IN ($list)", __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = $this->db->f('name', true);
			}
			return $results;
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

		function get_buildings()
		{
			$results	 = array();
			$results[]	 = array('id' => 0, 'name' => lang('Not selected'));
			$this->db->query("SELECT id, name FROM bb_building WHERE active != 0 ORDER BY name ASC", __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = array('id'	 => $this->db->f('id', false),
					'name'	 => $this->db->f('name', true));
			}
			return $results;
		}

		function set_inactive( $id, $type )
		{
			if ($type == 'event')
			{
				$sql = "UPDATE bb_event SET active = 0 where id = ($id)";
			}
			elseif ($type == 'allocation')
			{
				$sql = "UPDATE bb_allocation SET active = 0 where id = ($id)";
			}
			elseif ($type == 'booking')
			{
				$sql = "UPDATE bb_booking SET active = 0 where id = ($id)";
			}
			else
			{
				throw new UnexpectedValueException('Encountered an unexpected error');
			}
			$this->db->query($sql, __LINE__, __FILE__);
			return;
		}

		function set_active( $id, $type )
		{
			if ($type == 'event')
			{
				$sql = "UPDATE bb_event SET active = 1 where id = ($id)";
			}
			elseif ($type == 'allocation')
			{
				$sql = "UPDATE bb_allocation SET active = 1 where id = ($id)";
			}
			elseif ($type == 'booking')
			{
				$sql = "UPDATE bb_booking SET active = 1 where id = ($id)";
			}
			else
			{
				throw new UnexpectedValueException('Encountered an unexpected error');
			}
			$this->db->query($sql, __LINE__, __FILE__);
			return;
		}

		function get_activities_main_level()
		{
			$results	 = array();
			$results[]	 = array('id' => 0, 'name' => lang('Not selected'));
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

		public function update_id_string()
		{
			$table_name	 = $this->table_name;
			$db			 = $this->db;
			$sql		 = "UPDATE $table_name SET id_string = cast(id AS varchar)";
			$db->query($sql, __LINE__, __FILE__);
		}

		public function delete_application( $id )
		{
			if ($this->db->get_transaction())
			{
				$this->global_lock = true;
			}
			else
			{
				$this->db->transaction_begin();
			}

			$sql = "DELETE FROM bb_document_application WHERE owner_id=" . (int)$id;
			$this->db->query($sql, __LINE__, __FILE__);

			$tablesuffixes = array('agegroup', 'comment', 'date', 'resource', 'targetaudience');
			foreach ($tablesuffixes as $suffix)
			{
				$table_name	 = sprintf('%s_%s', $this->table_name, $suffix);
				$sql		 = "DELETE FROM $table_name WHERE application_id=" . (int)$id;
				$this->db->query($sql, __LINE__, __FILE__);
			}
			$table_name	 = $this->table_name;
			$sql		 = "DELETE FROM $table_name WHERE id=" . (int)$id;
			$this->db->query($sql, __LINE__, __FILE__);

			if (!$this->global_lock)
			{
				return $this->db->transaction_commit();
			}
		}

		function check_collision( $resources, $from_, $to_, $session_id = null )
		{
			$filter_block = '';
			if ($session_id)
			{
				$filter_block = " AND session_id != '{$session_id}'";
			}

			$rids	 = join(',', array_map("intval", $resources));
			$sql	 = "SELECT bb_block.id
                      FROM bb_block
                      WHERE  bb_block.resource_id in ($rids)
                      AND ((bb_block.from_ <= '$from_' AND bb_block.to_ > '$from_')
                      OR (bb_block.from_ >= '$from_' AND bb_block.to_ <= '$to_')
                      OR (bb_block.from_ < '$to_' AND bb_block.to_ >= '$to_')) AND active = 1 {$filter_block}
                      UNION
					  SELECT ba.id
                      FROM bb_allocation ba, bb_allocation_resource bar
                      WHERE active = 1
                      AND ba.id = bar.allocation_id
                      AND bar.resource_id in ($rids)
                      AND ((ba.from_ <= '$from_' AND ba.to_ > '$from_')
                      OR (ba.from_ >= '$from_' AND ba.to_ <= '$to_')
                      OR (ba.from_ < '$to_' AND ba.to_ >= '$to_'))
                      UNION
                      SELECT be.id
                      FROM bb_event be, bb_event_resource ber, bb_event_date bed
                      WHERE active = 1
					  AND be.id = ber.event_id
                      AND be.id = bed.event_id
                      AND ber.resource_id in ($rids)
                      AND ((bed.from_ <= '$from_' AND bed.to_ > '$from_')
                      OR (bed.from_ >= '$from_' AND bed.to_ <= '$to_')
                      OR (bed.from_ < '$to_' AND bed.to_ >= '$to_'))";

			$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);

			if (!$this->db->next_record())
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
		function check_timespan_availability( $resources, $from_, $to_ )
		{
			$rids	 = join(',', array_map("intval", $resources));
			$nrids	 = count($resources);
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

		function update_external_archive_reference( $id, $external_archive_key )
		{
			$external_archive_key = $this->db->db_addslashes($external_archive_key);
			return $this->db->query("UPDATE bb_application SET external_archive_key = '{$external_archive_key}' WHERE id =" . (int)$id, __LINE__, __FILE__);
		}

		function check_booking_limit( $session_id, $resource_id, $ssn, $booking_limit_number_horizont, $booking_limit_number )
		{
			if (!$ssn || !$booking_limit_number_horizont || !$booking_limit_number)
			{
				return false;
			}

//			$timezone = !empty($GLOBALS['phpgw_info']['user']['preferences']['common']['timezone']) ? $GLOBALS['phpgw_info']['user']['preferences']['common']['timezone'] : 'UTC';
//
//			$now = new DateTime();
//
//			try
//			{
//				$DateTimeZone = new DateTimeZone($timezone);
//				$now->setTimezone($DateTimeZone);
//			}
//			catch (Exception $ex)
//			{
//
//			}
//
//			$future_limit_full	 = clone ($now);
//			$future_limit_full->modify("+{$booking_limit_number_horizont} days");
//
//			$history_limit_full	 = clone ($now);
//			$history_limit_full->modify("-{$booking_limit_number_horizont} days");
//
//			$timestamp_history = $history_limit_full->getTimestamp();
//			$timestamp_future = $future_limit_full->getTimestamp();
//
//			$interval_history = abs($timestamp_history - time());
//			$interval_future = $timestamp_future - time();

			$booking_horizont_seconds = (int)$booking_limit_number_horizont * 3600 * 24;

			$sql = "SELECT count(*) as cnt FROM"
				. " (SELECT bb_application.id FROM bb_application"
				. " JOIN bb_application_date ON bb_application.id = bb_application_date.application_id"
				. " JOIN bb_application_resource"
				. " ON bb_application.id = bb_application_resource.application_id AND bb_application_resource.resource_id = " . (int)$resource_id
				. " WHERE "
				. "( customer_ssn = '{$ssn}' AND status != 'REJECTED' "
				. " AND ((EXTRACT(EPOCH from (to_- current_date))) > -$booking_horizont_seconds"
				. " OR (EXTRACT(EPOCH from (current_date - from_))) < $booking_horizont_seconds)"
				. ")"
				. " OR (status = 'NEWPARTIAL1' AND session_id = '$session_id')"
				. " ) as t";

			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			$cnt = (int)$this->db->f('cnt');

			$limit_reached = 0;
			if ($cnt > $booking_limit_number)
			{
				$limit_reached = $cnt;
			}
			return $limit_reached;
		}

		function delete_purchase_order( $application_id )
		{
			if ($this->db->get_transaction())
			{
				$this->global_lock = true;
			}
			else
			{
				$this->db->transaction_begin();
			}

			$sql = "SELECT id AS order_id FROM bb_purchase_order WHERE application_id =" . (int)$application_id;

			$this->db->query($sql, __LINE__, __FILE__);
			$order_ids = array(-1);
			while ($this->db->next_record())
			{
				$order_ids[] = (int)$this->db->f('order_id');
			}
			$now = time();

//			$sql = "DELETE FROM bb_purchase_order_line WHERE order_id IN (" . implode(',', $order_ids) . ")";
//			$this->db->query($sql, __LINE__, __FILE__);
//			$sql = "DELETE FROM bb_purchase_order WHERE id IN (" . implode(',', $order_ids) . ")";
			$sql = "UPDATE bb_purchase_order SET cancelled = $now, application_id = NULL WHERE id IN (" . implode(',', $order_ids) . ")";
			$this->db->query($sql, __LINE__, __FILE__);

			if (!$this->global_lock)
			{
				return $this->db->transaction_commit();
			}
		}

		function get_purchase_order( &$applications )
		{
			if (!$applications['results'])
			{
				return;
			}

			$application_ids = array(-1);
			foreach ($applications['results'] as $application)
			{
				$application_ids[] = $application['id'];
			}

			$sql = "SELECT bb_purchase_order_line.* , bb_purchase_order.application_id,"
				. "CASE WHEN
					(
						bb_resource.name IS NULL
					)"
				. " THEN bb_service.name ELSE bb_resource.name END AS name"
				. " FROM bb_purchase_order JOIN bb_purchase_order_line ON bb_purchase_order.id = bb_purchase_order_line.order_id"
				. " JOIN bb_article_mapping ON bb_purchase_order_line.article_mapping_id = bb_article_mapping.id"
				. " LEFT JOIN bb_service ON (bb_article_mapping.article_id = bb_service.id AND bb_article_mapping.article_cat_id = 2)"
				. " LEFT JOIN bb_resource ON (bb_article_mapping.article_id = bb_resource.id AND bb_article_mapping.article_cat_id = 1)"
				. " WHERE bb_purchase_order.cancelled IS NULL AND bb_purchase_order.application_id IN (" . implode(',', $application_ids) . ")";

			$this->db->query($sql, __LINE__, __FILE__);

			$orders		 = array();
			$sum		 = array();
			$total_sum	 = 0;
			while ($this->db->next_record())
			{
				$application_id	 = (int)$this->db->f('application_id');
				$order_id		 = (int)$this->db->f('order_id');
				if (!isset($sum[$order_id]))
				{
					$sum[$order_id] = 0;
				}

				$_sum			 = (float)$this->db->f('amount') + (float)$this->db->f('tax');
				$sum[$order_id]	 = (float)$sum[$order_id] + $_sum;
				$total_sum		 += $_sum;

				$orders[$application_id][$order_id]['lines'][] = array(
					'order_id'				 => $order_id,
					'status'				 => (int)$this->db->f('status'),
					'article_mapping_id'	 => (int)$this->db->f('article_mapping_id'),
					'quantity'				 => (float)$this->db->f('quantity'),
					'unit_price'			 => (float)$this->db->f('unit_price'),
					'overridden_unit_price'	 => (float)$this->db->f('overridden_unit_price'),
					'currency'				 => $this->db->f('currency'),
					'amount'				 => (float)$this->db->f('amount'),
					'tax_code'				 => (int)$this->db->f('tax_code'),
					'tax'					 => (float)$this->db->f('tax'),
					'name'					 => $this->db->f('name', true),
				);

				$orders[$application_id][$order_id]['order_id']	 = $order_id;
				$orders[$application_id][$order_id]['sum']		 = $sum[$order_id];
			}

			foreach ($applications['results'] as &$application)
			{
				if (empty($orders[$application['id']]))
				{
					continue;
				}
				$application['orders'] = array_values($orders[$application['id']]);
			}

			$applications['total_sum'] = $total_sum;
			return $orders;
		}

		function add_purchase_order( $purchase_order )
		{
			if (empty($purchase_order['application_id']))
			{
				return false;
			}

			$value_set = array(
				'application_id' => (int)$purchase_order['application_id'],
				'status'		 => 0,
				'customer_id'	 => null
			);

			if ($this->db->get_transaction())
			{
				$this->global_lock = true;
			}
			else
			{
				$this->db->transaction_begin();
			}

			$this->db->query('INSERT INTO bb_purchase_order (' . implode(',', array_keys($value_set)) . ') VALUES ('
				. $this->db->validate_insert(array_values($value_set)) . ')', __LINE__, __FILE__);

			$order_id = $this->db->get_last_insert_id('bb_purchase_order', 'id');

			if (!empty($purchase_order['lines']))
			{
				$article_ids = array();
				foreach ($purchase_order['lines'] as $line)
				{
					$article_mapping_ids[] = $line['article_mapping_id'];
				}


				/**
				 * FIXME
				 */
				$current_pricing = createObject('booking.soarticle_mapping')->get_current_pricing($article_mapping_ids);

				$add_sql = "INSERT INTO bb_purchase_order_line ("
					. " order_id, status, article_mapping_id, quantity, unit_price,"
					. " overridden_unit_price, currency,  amount, tax_code, tax)"
					. " VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

				$insert_update = array();
				foreach ($purchase_order['lines'] as $line)
				{
					$current_price_info = $current_pricing[$line['article_mapping_id']];

					$unit_price = $current_price_info['price'];

					$overridden_unit_price	 = $unit_price;
					$currency				 = 'NOK';

					$amount = $overridden_unit_price * (float)$line['quantity'];

					$tax_code	 = $current_price_info['tax_code'];
					$percent	 = $current_price_info['percent'];

					$tax = $amount * $percent / 100;

					$insert_update[] = array(
						1	 => array(
							'value'	 => $order_id,
							'type'	 => PDO::PARAM_INT
						),
						2	 => array(
							'value'	 => 1,
							'type'	 => PDO::PARAM_INT
						),
						3	 => array(
							'value'	 => $line['article_mapping_id'],
							'type'	 => PDO::PARAM_INT
						),
						4	 => array(
							'value'	 => (float)$line['quantity'],
							'type'	 => PDO::PARAM_STR
						),
						5	 => array(
							'value'	 => (float)$unit_price,
							'type'	 => PDO::PARAM_STR
						),
						6	 => array(
							'value'	 => (float)$overridden_unit_price,
							'type'	 => PDO::PARAM_STR
						),
						7	 => array(
							'value'	 => $currency,
							'type'	 => PDO::PARAM_STR
						),
						8	 => array(
							'value'	 => $amount,
							'type'	 => PDO::PARAM_STR
						),
						9	 => array(
							'value'	 => $tax_code,
							'type'	 => PDO::PARAM_INT
						),
						10	 => array(
							'value'	 => (float)$tax,
							'type'	 => PDO::PARAM_STR
						),
					);
				}
				$this->db->insert($add_sql, $insert_update, __LINE__, __FILE__);
			}


			if (!$this->global_lock)
			{
				return $this->db->transaction_commit();
			}
		}

		function get_single_purchase_order( $order_id )
		{
			if (!$order_id)
			{
				return;
			}

			$sql = "SELECT bb_purchase_order_line.* , bb_purchase_order.application_id,"
				. "CASE WHEN
					(
						bb_resource.name IS NULL
					)"
				. " THEN bb_service.name ELSE bb_resource.name END AS name"
				. " FROM bb_purchase_order JOIN bb_purchase_order_line ON bb_purchase_order.id = bb_purchase_order_line.order_id"
				. " JOIN bb_article_mapping ON bb_purchase_order_line.article_mapping_id = bb_article_mapping.id"
				. " LEFT JOIN bb_service ON (bb_article_mapping.article_id = bb_service.id AND bb_article_mapping.article_cat_id = 2)"
				. " LEFT JOIN bb_resource ON (bb_article_mapping.article_id = bb_resource.id AND bb_article_mapping.article_cat_id = 1)"
				. " WHERE bb_purchase_order.id = " . (int)$order_id;

			$this->db->query($sql, __LINE__, __FILE__);

			$order		 = array();
			$sum		 = 0;
			$total_sum	 = 0;
			while ($this->db->next_record())
			{
				$application_id	 = (int)$this->db->f('application_id');
				$order_id		 = (int)$this->db->f('order_id');

				$_sum		 = (float)$this->db->f('amount') + (float)$this->db->f('tax');
				$sum		 = (float)$sum + $_sum;
				$total_sum	 += $_sum;

				$order['lines'][] = array(
					'application_id'		 => $application_id,
					'order_id'				 => $order_id,
					'status'				 => (int)$this->db->f('status'),
					'article_mapping_id'	 => (int)$this->db->f('article_mapping_id'),
					'quantity'				 => (float)$this->db->f('quantity'),
					'unit_price'			 => (float)$this->db->f('unit_price'),
					'overridden_unit_price'	 => (float)$this->db->f('overridden_unit_price'),
					'currency'				 => $this->db->f('currency'),
					'amount'				 => (float)$this->db->f('amount'),
					'tax_code'				 => (int)$this->db->f('tax_code'),
					'tax'					 => (float)$this->db->f('tax'),
					'name'					 => $this->db->f('name', true),
				);

				$order['order_id']	 = $order_id;
				$order['sum']		 = $sum;
			}
			return $order;
		}

		function add_payment( $order_id, $msn )
		{

			$sql = "SELECT count(id) AS cnt FROM bb_payment WHERE order_id =" . (int)$order_id;

			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			$cnt		 = (int)$this->db->f('cnt');
			$payment_attempt = $cnt +1;
			$remote_id	 = "{$msn}-{$order_id}-order-{$order_id}-{$payment_attempt}";

			$order = $this->get_single_purchase_order($order_id);


			$value_set = array(
				'order_id' => $order_id,
				'payment_method_id'	 => null,
				'payment_gateway_mode' => 'test',//test and live.
				'remote_id' => $remote_id,
				'remote_state' => null,
				'amount' => $order['sum'],
				'currency' => 'NOK',
				'refunded_amount' => '0.0',
				'refunded_currency' => 'NOK',
				'status' => 'new',// pending, completed, voided, partially_refunded, refunded
				'created' => time(),
				'autorized' => null,
				'expires' => null,
				'completet' => null,
				'captured' => null,
	//			'avs_response_code' => array('type' => 'varchar', 'precision' => '15', 'nullable' => true),
	//			'avs_response_code_label' => array('type' => 'varchar', 'precision' => '35', 'nullable' => true),
			);

			$this->db->query('INSERT INTO bb_payment (' . implode(',', array_keys($value_set)) . ') VALUES ('
				. $this->db->validate_insert(array_values($value_set)) . ')', __LINE__, __FILE__);
			return $remote_id;
		}
	}

	class booking_soapplication_association extends booking_socommon
	{

		function __construct()
		{
			parent::__construct('bb_application_association', array(
				'id'			 => array('type' => 'int'),
				'application_id' => array('type' => 'int'),
				'type'			 => array('type' => 'string', 'required' => true),
				'from_'			 => array('type' => 'timestamp', 'query' => true),
				'to_'			 => array('type' => 'timestamp'),
				'cost'			 => array('type' => 'decimal'),
				'active'		 => array('type' => 'int')));
		}
	}