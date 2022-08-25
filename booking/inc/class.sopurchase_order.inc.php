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

		function add_purchase_order( $purchase_order )
		{
			if (empty($purchase_order['application_id']))
			{
				$msg = 'mangler referanse til søknad for å editere ordre';
				phpgwapi_cache::message_set($msg,'error');
				return false;
			}

			if (!empty($purchase_order['reservation_type']) && empty($purchase_order['reservation_id']))
			{
				return false;
			}


			if ($this->db->get_transaction())
			{
				$this->global_lock = true;
			}
			else
			{
				$this->db->transaction_begin();
			}

//--------  add or update master -------

			if(empty($purchase_order['reservation_id']))
			{
				$sql = "SELECT id FROM bb_purchase_order WHERE parent_id IS NULL AND application_id = " . (int)$purchase_order['application_id'];
			}
//--------  or add or update slave -------
			else
			{
				$sql = "SELECT id FROM bb_purchase_order WHERE reservation_type = '{$purchase_order['reservation_type']}' AND reservation_id = " . (int)$purchase_order['reservation_id'];
			}


			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			$order_id = (int)$this->db->f('id');
			if($order_id)
			{
				$this->db->query("DELETE FROM bb_purchase_order_line WHERE order_id = $order_id", __LINE__, __FILE__);
			}
			else
			{
				$value_set = array(
					'application_id'	 => (int)$purchase_order['application_id'] > 0 ? (int)$purchase_order['application_id'] : null,
					'status'			 => 0,
					'customer_id'		 => null,
					'reservation_type'	 => !empty($purchase_order['reservation_type']) ? $purchase_order['reservation_type'] : null,
					'reservation_id'	 => !empty($purchase_order['reservation_id']) ? (int) $purchase_order['reservation_id'] : null
				);

				$this->db->query('INSERT INTO bb_purchase_order (' . implode(',', array_keys($value_set)) . ') VALUES ('
				. $this->db->validate_insert(array_values($value_set)) . ')', __LINE__, __FILE__);

				$order_id = $this->db->get_last_insert_id('bb_purchase_order', 'id');
			}

//------------

			if (!empty($purchase_order['lines']))
			{
				$tax_codes = array();
				$sql = "SELECT id, percent_ FROM fm_ecomva";
				$this->db->query($sql, __LINE__, __FILE__);
				while ($this->db->next_record())
				{
					$tax_codes[(int)$this->db->f('id')] = (int)$this->db->f('percent_');
				}

				foreach ($purchase_order['lines'] as $line)
				{
					$article_mapping_ids[] = $line['article_mapping_id'];
				}

				/**
				 * FIXME
				 */
				$current_pricing = createObject('booking.soarticle_mapping')->get_current_pricing($article_mapping_ids);

				$add_sql = "INSERT INTO bb_purchase_order_line ("
					. " order_id, status, parent_mapping_id, article_mapping_id, quantity, unit_price,"
					. " overridden_unit_price, currency,  amount, tax_code, tax)"
					. " VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

				$insert_update = array();
				foreach ($purchase_order['lines'] as $line)
				{
					if(empty($line['quantity']) || !(float)$line['quantity'] > 0)
					{
						continue;
					}

					$current_price_info = $current_pricing[$line['article_mapping_id']];

					$_ex_tax_price	 = $line['ex_tax_price'];

					/**
					 * Overridden price from case officer - else price from database
					 */
					$currentapp = $GLOBALS['phpgw_info']['flags']['currentapp'];


					if($currentapp  == 'booking' && !is_null($_ex_tax_price ) && $_ex_tax_price != 'x') // restricted to backend
					{
						$unit_price = (float)$_ex_tax_price;
					}
					else
					{
						$unit_price = $current_price_info['price'];
					}

					$overridden_unit_price	 = $unit_price;
					$currency				 = 'NOK';

					// tax excluded
					$amount = $overridden_unit_price * (float)$line['quantity'];

					$_tax_code		 = $line['tax_code'];
					if($currentapp  == 'booking' && !is_null($_tax_code ) && $_tax_code != 'x') // restricted to backend
					{
						$tax_code	 = (int)$_tax_code;
						$percent	 = (int)$tax_codes[$tax_code];
					}
					else
					{
						$tax_code	 = $current_price_info['tax_code'];
						$percent	 = (int)$current_price_info['percent'];
					}

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
							'value'	 => (int)$line['parent_mapping_id'],
							'type'	 => PDO::PARAM_INT
						),
						4	 => array(
							'value'	 => $line['article_mapping_id'],
							'type'	 => PDO::PARAM_INT
						),
						5	 => array(
							'value'	 => (float)$line['quantity'],
							'type'	 => PDO::PARAM_STR
						),
						6	 => array(
							'value'	 => (float)$unit_price,
							'type'	 => PDO::PARAM_STR
						),
						7	 => array(
							'value'	 => (float)$overridden_unit_price,
							'type'	 => PDO::PARAM_STR
						),
						8	 => array(
							'value'	 => $currency,
							'type'	 => PDO::PARAM_STR
						),
						9	 => array(
							'value'	 => $amount,
							'type'	 => PDO::PARAM_STR
						),
						10	 => array(
							'value'	 => $tax_code,
							'type'	 => PDO::PARAM_INT
						),
						11	 => array(
							'value'	 => (float)$tax,
							'type'	 => PDO::PARAM_STR
						),
					);
				}
				$this->db->insert($add_sql, $insert_update, __LINE__, __FILE__);
			}


			if (!$this->global_lock)
			{
				$this->db->transaction_commit();
			}
			return $order_id;
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
			$sql = "UPDATE bb_purchase_order SET status = 0,  cancelled = $now, application_id = NULL WHERE id IN (" . implode(',', $order_ids) . ")";
			$this->db->query($sql, __LINE__, __FILE__);

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
				. " WHERE bb_purchase_order.id = " . (int)$order_id
				. " ORDER BY bb_purchase_order_line.id";

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

		function get_purchase_order( $application_id = 0, $reservation_type = '', $reservation_id = 0)
		{

			if(!$application_id && !($reservation_type && $reservation_id))
			{
				return array();
			}

			if ($reservation_type && !in_array($reservation_type, array('event', 'allocation')))
			{
				return array();
			}

			$tax_codes = array();
			$sql = "SELECT id, percent_ FROM fm_ecomva";
			$this->db->query($sql, __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$tax_codes[(int)$this->db->f('id')] = (int)$this->db->f('percent_');
			}

			$filtermethod = 'WHERE bb_purchase_order.cancelled IS NULL';

			if($reservation_type && (int) $reservation_id)
			{
				$filtermethod .= " AND bb_purchase_order.reservation_type = '{$reservation_type}' AND bb_purchase_order.reservation_id = " . (int) $reservation_id;
			}
			else if((int) $application_id)
			{
				$filtermethod .= " AND bb_purchase_order.parent_id IS NULL AND bb_purchase_order.application_id = " . (int) $application_id;
			}

			$sql = "SELECT bb_purchase_order_line.* , bb_purchase_order.application_id, bb_article_mapping.article_code,"
				. "CASE WHEN
					(
						bb_resource.name IS NULL
					)"
				. " THEN bb_service.name ELSE bb_resource.name END AS name"
				. " FROM bb_purchase_order JOIN bb_purchase_order_line ON bb_purchase_order.id = bb_purchase_order_line.order_id"
				. " JOIN bb_article_mapping ON bb_purchase_order_line.article_mapping_id = bb_article_mapping.id"
				. " LEFT JOIN bb_service ON (bb_article_mapping.article_id = bb_service.id AND bb_article_mapping.article_cat_id = 2)"
				. " LEFT JOIN bb_resource ON (bb_article_mapping.article_id = bb_resource.id AND bb_article_mapping.article_cat_id = 1)"
				. " {$filtermethod}"
				. " ORDER BY bb_purchase_order_line.id";

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

				$tax_code		 = (int)$this->db->f('tax_code');

				$order['lines'][] = array(
					'order_id'				 => $order_id,
					'status'				 => (int)$this->db->f('status'),
					'parent_mapping_id'		 => (int)$this->db->f('parent_mapping_id'),
					'article_mapping_id'	 => (int)$this->db->f('article_mapping_id'),
					'quantity'				 => (float)$this->db->f('quantity'),
					'unit_price'			 => (float)$this->db->f('unit_price'),
					'overridden_unit_price'	 => (float)$this->db->f('overridden_unit_price'),
					'currency'				 => $this->db->f('currency'),
					'amount'				 => (float)$this->db->f('amount'),
					'tax_code'				 => (int)$this->db->f('tax_code'),
					'article_code'			 => $this->db->f('article_code',true),
					'tax'					 => (float)$this->db->f('tax'),
					'name'					 => $this->db->f('name', true),
					'tax_percent'			 => $tax_codes[$tax_code]
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
				$purchase_order_id = $this->db->f('id');
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
				$purchase_order_id	 = $this->db->get_last_insert_id('bb_purchase_order', 'id');
				$this->copy_order_lines($order_id, $purchase_order_id);
			}

			return $purchase_order_id;
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
						'value'	 => (int)$this->db->f('parent_mapping_id'),
						'type'	 => PDO::PARAM_INT
					),
					4	 => array(
						'value'	 => (int)$this->db->f('article_mapping_id'),
						'type'	 => PDO::PARAM_INT
					),
					5	 => array(
						'value'	 => $this->db->f('unit_price'),
						'type'	 => PDO::PARAM_STR
					),
					6	 => array(
						'value'	 => $this->db->f('overridden_unit_price'),
						'type'	 => PDO::PARAM_STR
					),
					7	 => array(
						'value'	 => $this->db->f('currency'),
						'type'	 => PDO::PARAM_STR
					),
					8	 => array(
						'value'	 => $this->db->f('quantity'),
						'type'	 => PDO::PARAM_STR
					),
					9	 => array(
						'value'	 => $this->db->f('amount'),
						'type'	 => PDO::PARAM_STR
					),
					10	 => array(
						'value'	 => (int)$this->db->f('tax_code'),
						'type'	 => PDO::PARAM_INT
					),
					11	 => array(
						'value'	 => $this->db->f('tax'),
						'type'	 => PDO::PARAM_STR
					),
				);
			}

			$sql_insert = 'INSERT INTO bb_purchase_order_line'
				. ' (order_id, status, parent_mapping_id, article_mapping_id, unit_price, overridden_unit_price, currency, quantity, amount, tax_code, tax)'
				. ' VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

			if ($valueset)
			{
				return $this->db->insert($sql_insert, $valueset, __LINE__, __FILE__);
			}
		}

		function get_order_payments( $order_id )
		{
			if(empty($order_id))
			{
				return array();
			}

			$data	 = array();
			$sql	 = "SELECT * FROM bb_payment"
				. " WHERE order_id = {$order_id}"
				. " ORDER BY id";

			$this->db->query($sql, __LINE__, __FILE__);

			while ($this->db->next_record())
			{
				$payment_method_id =  $this->db->f('payment_method_id');
				$payment_method = $payment_method_id == 2 ? 'Etterfakturering' : 'Vipps';

				$data[] = array(
					'id'					 => $this->db->f('id'),
					'order_id'				 => $this->db->f('order_id'),
					'payment_method'		 => $payment_method,
					'payment_gateway_mode'	 => $this->db->f('payment_gateway_mode'),
					'remote_id'				 => $this->db->f('remote_id'),
					'remote_state'			 => $this->db->f('remote_state'),
					'amount'				 => (float)$this->db->f('amount'),
					'currency'				 => $this->db->f('currency'),
					'refunded_amount'		 => (float)$this->db->f('refunded_amount'),
					'refunded_currency'		 => $this->db->f('refunded_currency'),
					'status'				 => $this->db->f('status'),//'new', pending, completed, voided, partially_refunded, refunded
					'created'				 => $this->db->f('created'),
					'autorized'				 => $this->db->f('autorized'),
					'expires'				 => $this->db->f('expires'),
					'completet'				 => $this->db->f('completet'),
					'captured'				 => $this->db->f('captured'),
				);
			}
			return $data;
		}

	}