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

		function get_purchase_order( $_application_id = 0)
		{
			$application_ids = array(-1,  (int) $_application_id);

			if(!$_application_id)
			{
				return array();
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
				. " WHERE bb_purchase_order.cancelled IS NULL"
				. " AND bb_purchase_order.parent_id IS NULL AND bb_purchase_order.application_id IN (" . implode(',', $application_ids) . ")";

			$this->db->query($sql, __LINE__, __FILE__);

			$order		 = array();
			$sum		 = array();
			$total_sum	 = 0;
			while ($this->db->next_record())
			{
				$order_id		 = (int)$this->db->f('order_id');
				if (!isset($sum[$order_id]))
				{
					$sum[$order_id] = 0;
				}

				$_sum			 = (float)$this->db->f('amount') + (float)$this->db->f('tax');
				$sum[$order_id]	 = (float)$sum[$order_id] + $_sum;
				$total_sum		 += $_sum;

				$order['lines'][] = array(
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
				$order['sum']		 = $sum[$order_id];
			}

			return $order;
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