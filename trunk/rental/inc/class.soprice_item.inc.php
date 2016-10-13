<?php
	phpgw::import_class('rental.socommon');

	include_class('rental', 'price_item', 'inc/model/');
	include_class('rental', 'contract', 'inc/model/');

	class rental_soprice_item extends rental_socommon
	{

		protected static $so;

		/**
		 * Get a static reference to the storage object associated with this model object
		 *
		 * @return rental_soprice_item the storage object
		 */
		public static function get_instance()
		{
			if (self::$so == null)
			{
				self::$so = CreateObject('rental.soprice_item');
			}
			return self::$so;
		}

		/**
		 * Get single price item
		 *
		 * @param	$id	id of the price item to return
		 * @return a rental_price_item
		 */
		function get_single( $id )
		{
			$id = (int)$id;

			$sql = "SELECT rpi.*, type.title AS resp_title FROM rental_price_item rpi left join rental_contract_responsibility type ON (type.location_id = rpi.responsibility_id) WHERE rpi.id = " . $id;
			$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
			$this->db->next_record();

			$price_item = new rental_price_item($this->unmarshal($this->db->f('id', true), 'int'));
			$price_item->set_title($this->unmarshal($this->db->f('title', true), 'string'));
			$price_item->set_agresso_id($this->unmarshal($this->db->f('agresso_id', true), 'string'));
			$price_item->set_is_area($this->unmarshal($this->db->f('is_area', true), 'bool'));
			$price_item->set_is_inactive($this->unmarshal($this->db->f('is_inactive', true), 'bool'));
			$price_item->set_is_adjustable($this->unmarshal($this->db->f('is_adjustable', true), 'bool'));
			$price_item->set_standard($this->unmarshal($this->db->f('standard', true), 'bool'));
			$price_item->set_price($this->unmarshal($this->db->f('price', true), 'float'));
			$price_item->set_responsibility_id($this->unmarshal($this->db->f('responsibility_id', true), 'string'));
			$price_item->set_responsibility_title($this->unmarshal($this->db->f('resp_title', true), 'string'));
			$price_type_id = (int)$this->db->f('type');
			$price_item->set_price_type_id($price_type_id);
			$price_item->set_price_type_title($price_type_id);

			return $price_item;
		}

		/**
		 * Get the first price item matching the given title
		 *
		 * @param string $title
		 * @return rental_price_item
		 */
		function get_single_with_title( $title )
		{
			$title = (string)$title;

			$sql = "SELECT rpi.*, type.title AS resp_title FROM rental_price_item left join rental_contract_responsibility type ON (type.location_id = rpi.responsibility_id) WHERE rpi.title LIKE '" . $title . "'";
			$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);

			if ($this->db->next_record())
			{
				$price_item = new rental_price_item($this->unmarshal($this->db->f('id', true), 'int'));
				$price_item->set_title($this->unmarshal($this->db->f('title', true), 'string'));
				$price_item->set_agresso_id($this->unmarshal($this->db->f('agresso_id', true), 'string'));
				$price_item->set_is_area($this->unmarshal($this->db->f('is_area', true), 'bool'));
				$price_item->set_is_inactive($this->unmarshal($this->db->f('is_inactive', true), 'bool'));
				$price_item->set_is_adjustable($this->unmarshal($this->db->f('is_adjustable', true), 'bool'));
				$price_item->set_standard($this->unmarshal($this->db->f('standard', true), 'bool'));
				$price_item->set_price($this->unmarshal($this->db->f('price', true), 'float'));
				$price_item->set_responsibility_id($this->unmarshal($this->db->f('responsibility_id', true), 'string'));
				$price_item->set_responsibility_title($this->unmarshal($this->db->f('resp_title', true), 'string'));
				$price_type_id = (int)$this->db->f('type');
				$price_item->set_price_type_id($price_type_id);
				$price_item->set_price_type_title($price_type_id);
				return $price_item;
			}

			return null;
		}

		/**
		 * Get the first price item matching the given agresso-id
		 * @param string $id
		 * @return rental_price_item
		 */
		function get_single_with_id( $id )
		{
			$id = (string)$id;
			$sql = "SELECT * FROM rental_price_item WHERE agresso_id LIKE '" . $id . "'";
			$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);

			if ($this->db->next_record())
			{
				$price_item = new rental_price_item($this->unmarshal($this->db->f('id', true), 'int'));
				$price_item->set_title($this->unmarshal($this->db->f('title', true), 'string'));
				$price_item->set_agresso_id($this->unmarshal($this->db->f('agresso_id', true), 'string'));
				$price_item->set_is_area($this->unmarshal($this->db->f('is_area', true), 'bool'));
				$price_item->set_is_inactive($this->unmarshal($this->db->f('is_inactive', true), 'bool'));
				$price_item->set_is_adjustable($this->unmarshal($this->db->f('is_adjustable', true), 'bool'));
				$price_item->set_standard($this->unmarshal($this->db->f('standard', true), 'bool'));
				$price_item->set_price($this->unmarshal($this->db->f('price', true), 'float'));
				$price_item->set_responsibility_id($this->unmarshal($this->db->f('responsibility_id', true), 'string'));
				$price_item->set_responsibility_title($this->unmarshal($this->db->f('resp_title', true), 'string'));
				$price_type_id = (int)$this->db->f('type');
				$price_item->set_price_type_id($price_type_id);
				$price_item->set_price_type_title($price_type_id);

				return $price_item;
			}

			return null;
		}

		/**
		 * Get a list of price_item objects matching the specific filters
		 *
		 * @param $start search result offset
		 * @param $results number of results to return
		 * @param $sort field to sort by
		 * @param $query LIKE-based query string
		 * @param $filters array of custom filters
		 * @return list of rental_composite objects
		 */
		function get_price_item_array( $start = 0, $results = 1000, $sort = null, $dir = '', $query = null, $search_option = null, $filters = array() )
		{
			$results = array();

			$condition = $this->get_conditions($query, $filters, $search_option);
			$order = $sort ? "ORDER BY $sort $dir " : '';

			$sql = "SELECT * FROM rental_price_item WHERE $condition $order";
			$this->db->limit_query($sql, $start, __LINE__, __FILE__, $limit);

			while ($this->db->next_record())
			{
				$price_item = new rental_price_item($this->unmarshal($this->db->f('id', true), 'int'));
				$price_item->set_title($this->unmarshal($this->db->f('title', true), 'string'));
				$price_item->set_agresso_id($this->unmarshal($this->db->f('agresso_id', true), 'string'));
				$price_item->set_is_area($this->unmarshal($this->db->f('is_area', true), 'bool'));
				$price_item->set_is_inactive($this->unmarshal($this->db->f('is_inactive', true), 'bool'));
				$price_item->set_is_adjustable($this->unmarshal($this->db->f('is_adjustable', true), 'bool'));
				$price_item->set_standard($this->unmarshal($this->db->f('standard', true), 'bool'));
				$price_item->set_price($this->unmarshal($this->db->f('price', true), 'float'));
				$price_item->set_responsibility_id($this->unmarshal($this->db->f('responsibility_id', true), 'int'));
				$price_type_id = (int)$this->db->f('type');
				$price_item->set_price_type_id($price_type_id);
				$price_item->set_price_type_title($price_type_id);

				$results[] = $price_item;
			}

			return $results;
		}

		protected function get_conditions( $query, $filters, $search_option )
		{
			$clauses = array('1=1');
			if ($query)
			{

				$like_pattern = "'%" . $this->db->db_addslashes($query) . "%'";
				$like_clauses = array();
				switch ($search_option)
				{
					case "id":
						$like_clauses[] = "rental_price_item.id = $query";
						break;
					case "title":
						$like_clauses[] = "rental_price_item.title $this->like $like_pattern";
						break;
					case "agresso_id":
						$like_clauses[] = "rental_price_item.agresso_id $this->like $like_pattern";
						break;
					case "all":
						$like_clauses[] = "rental_price_item.title $this->like $like_pattern";
						$like_clauses[] = "rental_price_item.agresso_id $this->like $like_pattern";
						break;
				}


				if (count($like_clauses))
				{
					$clauses[] = '(' . join(' OR ', $like_clauses) . ')';
				}
			}

			$filter_clauses = array();
			switch ($filters['is_area'])
			{
				case "true":
					$filter_clauses[] = "rental_price_item.is_area = TRUE";
					break;
				case "false":
					$filter_clauses[] = "rental_price_item.is_area = FALSE";
					break;
				case "both":
					break;
			}

			if (isset($filters['type']) && $filters['type'])
			{
				$filter_clauses[] = 'rental_price_item.type = ' . (int)$filters['type'];
			}

			if (count($filter_clauses))
			{
				$clauses[] = join(' AND ', $filter_clauses);
			}

			return join(' AND ', $clauses);
		}

		/**
		 * Add a new price_item to the database.  Adds the new insert id to the object reference.
		 *
		 * @param $price_item the price_item to be added
		 * @return result receipt from the db operation
		 */
		function add( &$price_item )
		{
			$price = $price_item->get_price() ? $price_item->get_price() : 0;
			// Build a db-friendly array of the composite object
			$values = array(
				'\'' . $price_item->get_title() . '\'',
				'\'' . $price_item->get_agresso_id() . '\'',
				($price_item->is_area() ? "true" : "false"),
				($price_item->is_inactive() ? "true" : "false"),
				($price_item->is_adjustable() ? "true" : "false"),
				($price_item->is_standard() ? "true" : "false"),
				str_replace(',', '.', $price),
				$price_item->get_responsibility_id(),
				$price_item->get_price_type_id()
			);

			$cols = array('title', 'agresso_id', 'is_area', 'is_inactive', 'is_adjustable',
				'standard', 'price', 'responsibility_id', 'type');

			$q = "INSERT INTO rental_price_item (" . join(',', $cols) . ") VALUES (" . join(',', $values) . ")";

			$result = $this->db->query($q);
			$receipt['id'] = $this->db->get_last_insert_id("rental_price_item", 'id');

			$price_item->set_id($receipt['id']);

			return $receipt;
		}

		/**
		 * Update the database values for an existing price item.
		 *
		 * @param $price_item the price item to be updated
		 * @return result receipt from the db operation
		 */
		function update( $price_item )
		{
			$id = intval($price_item->get_id());

			$values = array(
				'title = \'' . $price_item->get_title() . '\'',
				'agresso_id = \'' . $price_item->get_agresso_id() . '\'',
				'is_area = ' . ($price_item->is_area() ? "true" : "false"),
				'is_inactive = ' . ($price_item->is_inactive() ? "true" : "false"),
				'is_adjustable = ' . ($price_item->is_adjustable() ? "true" : "false"),
				'standard = ' . ($price_item->is_standard() ? "true" : "false"),
				'price = ' . str_replace(',', '.', $price_item->get_price()),
				'responsibility_id = ' . $price_item->get_responsibility_id(),
				'type = ' . $price_item->get_price_type_id()
			);

			$this->db->query('UPDATE rental_price_item SET ' . join(',', $values) . " WHERE id=$id", __LINE__, __FILE__);

			$receipt['id'] = $id;
			$receipt['message'][] = array('msg' => lang('Entity %1 has been updated', $entry['id']));
			return $receipt;
		}

		/**
		 * Update the database values for an existing contract price item.
		 *
		 * @param $price_item the contract price item to be updated
		 * @return result receipt from the db operation
		 */
		function update_contract_price_item( rental_contract_price_item $price_item )
		{
			$id = intval($price_item->get_id());
			$one_time = $price_item->get_is_one_time();

			$values = array(
				'price_item_id = ' . $price_item->get_price_item_id(),
				'contract_id = ' . $price_item->get_contract_id(),
				'area = ' . str_replace(',', '.', $price_item->get_area()),
				'count = ' . str_replace(',', '.', $price_item->get_count()),
				'title = \'' . $price_item->get_title() . '\'',
				'agresso_id = \'' . $price_item->get_agresso_id() . '\'',
				'is_area = ' . ($price_item->is_area() ? "true" : "false"),
				'is_one_time = ' . ((isset($one_time) && ($price_item->is_one_time() || $price_item->get_is_one_time() == 1)) ? "true" : "false"),
				'price = ' . str_replace(',', '.', $price_item->get_price())
			);

			if ($price_item->is_area())
			{
//			var_dump('total_price = '.$price_item->get_area().'*'.$price_item->get_price());
				$values[] = 'total_price = ' . str_replace(',', '.', ($price_item->get_area() * $price_item->get_price()));
			}
			else
			{
//			var_dump('total_price = '.$price_item->get_count().'*'.$price_item->get_price());
				$values[] = 'total_price = ' . str_replace(',', '.', ($price_item->get_count() * $price_item->get_price()));
			}

			if ($price_item->get_date_start())
			{
				$values[] = 'date_start = ' . $this->marshal($price_item->get_date_start(), 'int');
			}

			if ($price_item->get_date_end())
			{
				$values[] = 'date_end = ' . $this->marshal($price_item->get_date_end(), 'int');
			}

			$this->db->query('UPDATE rental_contract_price_item SET ' . join(',', $values) . " WHERE id=$id", __LINE__, __FILE__);

			$receipt['id'] = $id;
			$receipt['message'][] = array('msg' => lang('Entity %1 has been updated', $entry['id']));

			return $receipt;
		}

		/**
		 * This method removes a price item to a contract. Updates last edited hisory.
		 *
		 * @param $contract_id	the given contract
		 * @param $price_item	the price item to remove
		 * @return true if successful, false otherwise
		 */
		function remove_price_item( $contract_id, $price_item_id )
		{
			$q = "DELETE FROM rental_contract_price_item WHERE id = {$price_item_id} AND contract_id = {$contract_id}";
			$result = $this->db->query($q);
			if ($result)
			{
				rental_socontract::get_instance()->last_updated($contract_id);
				rental_socontract::get_instance()->last_edited_by($contract_id);
				return true;
			}
			return false;
		}

		/**
		 * This method adds a price item to a contract. Updates last edited history.
		 *
		 * @param $contract_id	the given contract
		 * @param $price_item	the price item to add
		 * @return true if successful, false otherwise
		 */
		function add_price_item( $contract_id, $price_item_id )
		{
			$location_factor = 1;
			$standard_factor = 1;
			$custom_factor = 1;
			$contract = rental_socontract::get_instance()->get_single($contract_id);
			$composites = $contract->get_composites();
			foreach ($composites as $composite_id => $composite)
			{
				$composite_obj = rental_socomposite::get_instance()->get_single($composite_id);
				break;
			}

			if(!$composite_obj)
			{
				$GLOBALS['phpgw']->log->message(array(
					'text' => "rental_soprice_item::add_price_item() : Contract %1 is missing composite ",
					'p1'   => $contract_id,
					'line' => __LINE__,
					'file' => __FILE__
				));
			}
			else
			{
				$custom_factor = $composite_obj->get_custom_price_factor();
				$custom_factor = $custom_factor ? (float)$custom_factor : 1;
				$location_info = ExecMethod('rental.bogeneric.read', array(
					'location_info'=> array('type' => 'location_factor'),
					'custom_filter' => array('part_of_town_id = ' . (int)$composite_obj->get_part_of_town_id())
					)
				);
				$location_factor = (float)abs($location_info[0]['factor']) > 0 ? (float)$location_info[0]['factor'] : 1;
				$standard_info = ExecMethod('rental.bogeneric.read_single', array('type' => 'composite_standard', 'id' => $composite_obj->get_standard_id()));
				$standard_factor = (float)abs($standard_info['factor']) > 0 ? (float)$standard_info['factor'] : 1;
			}

			$factor = $location_factor * $standard_factor * $custom_factor;
			$factor = $factor ? (float)$factor : 1;
			$price_item = $this->get_single($price_item_id);
			$rented_area = 0;
			$total_price = 0;
			if ($price_item->is_area())
			{
				$rented_area = $contract->get_rented_area();
				if ($rented_area == '')
				{
					$rented_area = 0;
				}
				$total_price = ($rented_area * $price_item->get_price() * $factor);
				//var_dump($total_price, $rented_area, $price_item->get_price());
			}
			else
			{
				$location_factor = 1;
				$standard_factor = 1;
				$custom_factor = 1;
			}
			if ($price_item)
			{
				$values = array(
					$price_item_id,
					$contract_id,
					"'" . $price_item->get_title() . "'",
					str_replace(',', '.', $rented_area),
					"'" . $price_item->get_agresso_id() . "'",
					$price_item->is_area() ? 'true' : 'false',
					(str_replace(',', '.', $price_item->get_price())),
					str_replace(',', '.', $total_price),
					$location_factor,
					$standard_factor,
					$custom_factor
				);
				$start_date_field = '';
				$end_date_field = '';

				if ($start_date = $contract->get_billing_start_date())
				{
					$values[] = $start_date;
					$start_date_field = ", date_start";
				}
				if ($end_date = $contract->get_billing_end_date())
				{
					$values[] = $end_date;
					$end_date_field = ", date_end";
				}

				$q = "INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, agresso_id, is_area, price, total_price, location_factor, standard_factor, custom_factor {$start_date_field} {$end_date_field}) VALUES (" . join(',', $values) . ")";
				//var_dump($q);
				$result = $this->db->query($q);
				if ($result)
				{
					rental_socontract::get_instance()->last_updated($contract_id);
					rental_socontract::get_instance()->last_edited_by($contract_id);
					return true;
				}
			}
			return false;
		}

		function reset_contract_price_item( $contract_id, $price_item_id )
		{
			//TODO: implement reset function
		}

		protected function get_id_field_name()
		{
			return 'id';
		}

		protected function get_query( string $sort_field, bool $ascending, string $search_for, string $search_type, array $filters, bool $return_count )
		{
			$clauses = array('1=1');

			//Add columns to this array to include them in the query
			$columns = array();

			$dir = $ascending ? 'ASC' : 'DESC';
			if ($sort_field == 'responsibility_title')
			{
				$sort_field = 'responsibility_id';
			}
			$order = $sort_field ? "ORDER BY $sort_field $dir" : "ORDER BY agresso_id ASC";

			$filter_clauses = array();
			$filter_clauses[] = "rpi.title != 'UNKNOWN'";

			if (isset($filters[$this->get_id_field_name()]))
			{
				$id = $this->marshal($filters[$this->get_id_field_name()], 'int');
				$filter_clauses[] = "{$this->get_id_field_name()} = {$id}";
			}
			if (isset($filters['price_item_status']))
			{
				$filter_clauses[] = "NOT is_inactive";
			}
			if (isset($filters['responsibility_id']))
			{
				$filter_clauses[] = "responsibility_id=" . $filters['responsibility_id'];
			}
			if (isset($filters['is_adjustable']))
			{
				$filter_clauses[] = "NOT is_adjustable";
			}

			if (count($filter_clauses))
			{
				$clauses[] = join(' AND ', $filter_clauses);
			}

			$condition = join(' AND ', $clauses);

			if ($return_count) // We should only return a count
			{
				$cols = 'COUNT(DISTINCT(rpi.id)) AS count';
				$order = '';
			}
			else
			{
				$cols = 'rpi.*, type.title AS resp_title';
			}

			$tables = "rental_price_item rpi";
			$join_responsibility = $this->left_join . ' rental_contract_responsibility type ON (type.location_id = rpi.responsibility_id)';
			$joins = $join_responsibility;

			return "SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}";
		}

		protected function populate( int $price_item_id, &$price_item )
		{
			if ($price_item == null)
			{
				$price_item = new rental_price_item($this->unmarshal($this->db->f('id'), 'int'));
				$price_item->set_title($this->unmarshal($this->db->f('title'), 'string'));
				$price_item->set_agresso_id($this->unmarshal($this->db->f('agresso_id'), 'string'));
				$price_item->set_is_area($this->unmarshal($this->db->f('is_area'), 'bool'));
				$price_item->set_is_inactive($this->unmarshal($this->db->f('is_inactive'), 'bool'));
				$price_item->set_is_adjustable($this->unmarshal($this->db->f('is_adjustable'), 'bool'));
				$price_item->set_standard($this->unmarshal($this->db->f('standard'), 'bool'));
				$price_item->set_price($this->unmarshal($this->db->f('price'), 'float'));
				$price_item->set_responsibility_id($this->unmarshal($this->db->f('responsibility_id', true), 'int'));
				$price_item->set_responsibility_title($this->unmarshal($this->db->f('resp_title', true), 'string'));
				$price_type_id = (int)$this->db->f('type');
				$price_item->set_price_type_id($price_type_id);
				$price_item->set_price_type_title($price_type_id);
			}
			return $price_item;
		}

		function has_active_contract( int $price_item_id )
		{
			$ts_query = strtotime(date('Y-m-d')); // timestamp for query (today)
			$q = "SELECT rpi.* FROM rental_price_item rpi, rental_contract_price_item rcpi, rental_contract rc WHERE rpi.id = {$price_item_id} AND rcpi.price_item_id = rpi.id AND rc.id = rcpi.contract_id AND rc.date_start <= {$ts_query} AND (rc.date_end >= {$ts_query} OR rc.date_end IS NULL)";
			$this->db->query($q);
			if ($this->db->next_record())
			{
				return true;
			}
			return false;
		}

		function get_manual_adjustable()
		{
			$query = "SELECT id, agresso_id, title, price FROM rental_price_item WHERE NOT is_inactive AND NOT is_adjustable ORDER BY id ASC";
			$this->db->query($query);
			while ($this->db->next_record())
			{
				$id = $this->db->f('id', true);
				$label = $this->db->f('agresso_id', true) . ' - ' . $this->db->f('title', true) . ' ; ' . lang('price') . ': ' . $this->db->f('price', true);
				$results[$id] = $label;
			}
			return $results;
		}

		function adjust_contract_price_items( int $price_item_id, $new_price )
		{
			$this->db->transaction_begin();
			$number_affected = 0;
			$db2 = clone($this->db);
			$q_contract_price_items = "SELECT * FROM rental_contract_price_item WHERE price_item_id={$price_item_id}";
			$this->db->query($q_contract_price_items);
			while ($this->db->next_record())
			{
				$total_price = 0.00;
				$curr_id = $this->db->f('id');
				$is_area = $this->unmarshal($this->db->f('is_area'), 'bool');
				if ($is_area)
				{
					$area = $this->unmarshal($this->db->f('area'), 'float');
					$total_price = $area * $new_price;
				}
				else
				{
					$count = $this->unmarshal($this->db->f('count'), 'int');
					$total_price = $count * $new_price;
				}
				$query = "UPDATE rental_contract_price_item SET price=$new_price, total_price=$total_price WHERE id=$curr_id";
				$db2->query($query);

				$number_affected ++;
			}

			if ($this->db->transaction_commit())
			{
				return $number_affected;
			}
			else
			{
				return 0;
			}
		}
	}