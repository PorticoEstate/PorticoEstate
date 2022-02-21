<?php

	class booking_sopurchase_order
	{
		protected
			$db,
			$db2,
			$join,
			$like;

		function __construct()
		{
			$this->db = & $GLOBALS['phpgw']->db;
			$this->db2 = clone($GLOBALS['phpgw']->db);
			$this->join = & $this->db->join;
			$this->like = & $this->db->like;
		}

		public function identify_purchase_order( $application_id, $reservation_id, $reservation_type = 'event' )
		{
			if (!$application_id || !$reservation_id)
			{
				return;
			}

			if (!in_array($reservation_type, array('event', 'allocation')))
			{
				return;
			}

			$this->db->query("UPDATE bb_purchase_order"
				. " SET reservation_type = '{$reservation_type}', reservation_id = " . (int)$reservation_id
				. " WHERE parent_id IS NULL"
				. " AND application_id =" . (int)$application_id, __LINE__, __FILE__);
		}

		public function copy_purchase_order_from_application( $reservation, $_reservation_id, $reservation_type = 'event' )
		{
			$application_id	 = (int)$reservation['application_id'];
			$reservation_id	 = (int)$_reservation_id;

			if (!$application_id || !$reservation_id)
			{
				return;
			}

			if (!in_array($reservation_type, array('event', 'allocation')))
			{
				return;
			}

			/**
			 * Find first order related to application
			 */
			$sql = "SELECT id FROM bb_purchase_order WHERE reservation_type IS NULL AND reservation_id IS NULL AND application_id = {$application_id}";
			$this->db->query($sql, __LINE__, __FILE__);
			if ($this->db->next_record())
			{
				/**
				 * Place the order where it belong
				 */
				$this->identify_purchase_order($application_id, $reservation_id, $reservation_type);
			}
			else
			{
				$sql = "SELECT * FROM bb_purchase_order WHERE application_id = {$application_id} AND parent_id IS NULL";
				$this->db->query($sql, __LINE__, __FILE__);
				$this->db->next_record();

				$order_id		 = (int)$this->db->f('id');
				$customer_id	 = (int)$this->db->f('customer_id');
				$valueset		 = array(
					'parent_id'			 => $order_id,
					'status'			 => (int)$this->db->f('status'),
					'application_id'	 => $application_id,
					'customer_id'		 => $customer_id ? $customer_id : null,
					'reservation_type'	 => $reservation_type,
					'reservation_id'	 => $reservation_id,
				);
				$insert_fields	 = implode(',', array_keys($valueset));
				$insert_values	 = $this->db->validate_insert(array_values($valueset));
				$this->db->query("INSERT INTO bb_purchase_order ({$insert_fields}) VALUES ({$insert_values})", __LINE__, __FILE__);
				$new_id			 = $this->db->get_last_insert_id('bb_purchase_order', 'id');
				$this->copy_order_lines($order_id, $new_id);
			}
		}

		function copy_order_lines( $from_id, $to_id )
		{

			$sql = "SELECT * FROM bb_purchase_order_line WHERE order_id = " . (int)$from_id;
			$this->db->query($sql, __LINE__, __FILE__);

			$valueset = array();

			while ($this->db->next_record())
			{
				$valueset[] = array(
					1	 => array(
						'value'	 => (int)$to_id,
						'type'	 => PDO::PARAM_INT
					),
					2	 => array(
						'value'	 => 1,
						'type'	 => PDO::PARAM_INT
					),
					3	 => array(
						'value'	 => (int)$this->db->f('article_mapping_id'),
						'type'	 => PDO::PARAM_INT
					),
					4	 => array(
						'value'	 => $this->db->f('unit_price'),
						'type'	 => PDO::PARAM_STR
					),
					5	 => array(
						'value'	 => $this->db->f('overridden_unit_price'),
						'type'	 => PDO::PARAM_STR
					),
					6	 => array(
						'value'	 => $this->db->f('currency'),
						'type'	 => PDO::PARAM_STR
					),
					7	 => array(
						'value'	 => $this->db->f('quantity'),
						'type'	 => PDO::PARAM_STR
					),
					8	 => array(
						'value'	 => $this->db->f('amount'),
						'type'	 => PDO::PARAM_STR
					),
					9	 => array(
						'value'	 => (int)$this->db->f('tax_code'),
						'type'	 => PDO::PARAM_INT
					),
					10	 => array(
						'value'	 => $this->db->f('tax'),
						'type'	 => PDO::PARAM_STR
					),
				);
			}

			$sql_insert = 'INSERT INTO bb_purchase_order_line'
				. ' (order_id, status, article_mapping_id, unit_price, overridden_unit_price, currency, quantity, amount, tax_code, tax)'
				. ' VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

			if ($valueset)
			{
				return $this->db->insert($sql_insert, $valueset, __LINE__, __FILE__);
			}
		}

	}