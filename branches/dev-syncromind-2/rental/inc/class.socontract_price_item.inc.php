<?php
	phpgw::import_class('rental.socommon');

	class rental_socontract_price_item extends rental_socommon
	{

		protected static $so;

		/**
		 * Get a static reference to the storage object associated with this model object
		 * 
		 * @return rental_socontract_price_item the storage object
		 */
		public static function get_instance()
		{
			if (self::$so == null)
			{
				self::$so = CreateObject('rental.socontract_price_item');
			}
			return self::$so;
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
			$order = $sort_field ? "ORDER BY rental_contract_price_item.{$sort_field} $dir" : 'ORDER BY agresso_id ASC, title ASC';

			$filter_clauses = array();

			if (isset($filters[$this->get_id_field_name()]))
			{
				$id = $this->marshal($filters[$this->get_id_field_name()], 'int');
				$filter_clauses[] = "rental_contract_price_item.{$this->get_id_field_name()} = {$id}";
			}
			if (isset($filters['contract_id']))
			{
				$id = $this->marshal($filters['contract_id'], 'int');
				$filter_clauses[] = "contract_id = {$id}";
			}
			if (isset($filters['contract_ids_one_time']))
			{
				if($filters['credits'])
				{
					$filter_clauses[] = "is_one_time";
					$filter_clauses[] = "rental_contract_price_item.total_price < 0.00";
				}
				else if($filters['positive_one_time'])
				{
					$filter_clauses[] = "is_one_time";
					$filter_clauses[] = "rental_contract_price_item.total_price > 0.00";
				}
				else
				{
					$billing_term_id = (int)$filters['billing_term_id'];
					$sql = "SELECT months FROM rental_billing_term WHERE id = {$billing_term_id}";
					$result = $this->db->query($sql);
					if (!$result)
					{
						return;
					}
					if (!$this->db->next_record())
					{
						return;
					}
					$month = (int)$filters['month'];
					$year = (int)$filters['year'];
					$months = $this->unmarshal($this->db->f('months', true), 'int');
					$timestamp_end = strtotime("{$year}-{$month}-01"); // The first day in the month to bill for
					if ($months == 1)
					{
						$timestamp_start = $timestamp_end; // The first day of the period to bill for
					}
					else
					{
						$months = $months - 1;
						$timestamp_start = strtotime("-{$months} months", $timestamp_end); // The first day of the period to bill for
					}
					$timestamp_end = strtotime('+1 month', $timestamp_end); // The first day in the month after the one to bill for

					$filter_clauses[] = "is_one_time";
					$filter_clauses[] = "date_start < {$timestamp_end}";
					$filter_clauses[] = "date_start >= {$timestamp_start}";
				}
			}
			if (isset($filters['one_time']) && $filters['include_billed'])
			{
				$filter_clauses[] = "is_one_time";
			}
			if (isset($filters['one_time']) && !$filters['include_billed'])
			{
				$filter_clauses[] = "is_one_time AND NOT is_billed";
			}
			else if ($filters['include_billed'])
			{
				// Do not add filter on showing only price items that has not yet been billed
			}
			else
			{
				$filter_clauses[] = "NOT is_billed";
			}

			if (count($filter_clauses))
			{
				$clauses[] = join(' AND ', $filter_clauses);
			}

			$condition = join(' AND ', $clauses);

			if ($return_count) // We should only return a count
			{
				$cols = 'COUNT(DISTINCT(rental_contract_price_item.id)) AS count';
				$order = "";
			}
			else if (isset($filters['export']))
			{
				$cols = "rental_contract_price_item.id,rental_contract_price_item.location_factor,rental_contract_price_item.standard_factor,"
					. " rental_contract_price_item.custom_factor, rental_contract_price_item.price_item_id,"
					. " rental_contract_price_item.contract_id, rental_contract_price_item.area, rental_contract_price_item.count,"
					. " rental_contract_price_item.agresso_id, rental_contract_price_item.title, rental_contract_price_item.is_area,"
					. " rental_contract_price_item.price, rental_contract_price_item.total_price, rental_contract_price_item.is_one_time,"
					. " rental_contract_price_item.date_start, rental_contract_price_item.date_end,rental_price_item.type,"
					. " rental_contract_price_item.billing_id";
			}
			else
			{
				$cols = 'rental_contract_price_item.*, rental_price_item.type';
			}

			$tables = "rental_contract_price_item";
//		$joins = "";
			$joins = "{$this->join} rental_price_item ON (rental_price_item.id = rental_contract_price_item.price_item_id)";

			//var_dump("SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}");
			return "SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}";
		}

		protected function populate( int $price_item_id, &$price_item )
		{
			if ($price_item == null)
			{
				$price_item = new rental_contract_price_item($this->unmarshal($this->db->f('id'), 'int'));
				$price_item->set_price_item_id($this->unmarshal($this->db->f('price_item_id'), 'int'));
				$price_item->set_contract_id($this->unmarshal($this->db->f('contract_id'), 'int'));
				$price_item->set_title($this->unmarshal($this->db->f('title'), 'string'));
				$price_item->set_agresso_id($this->unmarshal($this->db->f('agresso_id'), 'string'));
				$price_item->set_is_area($this->unmarshal($this->db->f('is_area'), 'bool'));
				$price_item->set_is_one_time($this->unmarshal($this->db->f('is_one_time'), 'bool'));
				$price_item->set_is_billed($this->unmarshal($this->db->f('is_billed'), 'bool'));
				$price_item->set_price($this->unmarshal($this->db->f('price'), 'float'));
				$price_item->set_area($this->unmarshal($this->db->f('area'), 'float'));
				$price_item->set_count($this->unmarshal($this->db->f('count'), 'int'));
				$price_item->set_total_price($this->unmarshal($this->db->f('total_price'), 'float'));
				$price_item->set_date_start($this->unmarshal($this->db->f('date_start'), 'int'));
				$price_item->set_date_end($this->unmarshal($this->db->f('date_end'), 'int'));
				$price_type_id = (int)$this->db->f('type');
				$price_item->set_price_type_id($price_type_id);
				$price_item->set_price_type_title($price_type_id);
				$price_item->set_location_factor($this->unmarshal($this->db->f('location_factor'), 'float'));
				$price_item->set_standard_factor($this->unmarshal($this->db->f('standard_factor'), 'float'));
				$price_item->set_custom_factor($this->unmarshal($this->db->f('custom_factor'), 'float'));
				$price_item->set_billing_id($this->unmarshal($this->db->f('billing_id'), 'int'));
			}
			return $price_item;
		}

		/**
		 * Add a new contract_price_item to the database from import.  Adds the new insert id to the object reference.
		 *
		 * @param $price_item the contract_price_item to be added
		 * @return mixed receipt from the db operation
		 */
		public function import( &$price_item )
		{
			$price = $price_item->get_price() ? $price_item->get_price() : 0;
			$total_price = $price_item->get_total_price() ? $price_item->get_total_price() : 0;
			$rented_area = $price_item->get_area();

			// Build a db-friendly array of the composite object
			$values = array(
				$price_item->get_price_item_id(),
				$price_item->get_contract_id(),
				'\'' . $price_item->get_title() . '\'',
				'\'' . $price_item->get_agresso_id() . '\'',
				($price_item->is_area() ? "true" : "false"),
				str_replace(',', '.', $price),
				str_replace(',', '.', $rented_area),
				str_replace(',', '.', $price_item->get_count()),
				str_replace(',', '.', $total_price),
				($price_item->is_billed() ? "true" : "false")
			);

			$cols = array('price_item_id', 'contract_id', 'title', 'agresso_id', 'is_area',
				'price', 'area', 'count', 'total_price', 'is_billed');

			if ($price_item->get_date_start())
			{
				$values[] = $this->marshal($price_item->get_date_start(), 'int');
				$cols[] = 'date_start';
			}

			if ($price_item->get_date_end())
			{
				$values[] = $this->marshal($price_item->get_date_end(), 'int');
				$cols[] = 'date_end';
			}

			$q = "INSERT INTO rental_contract_price_item (" . join(',', $cols) . ") VALUES (" . join(',', $values) . ")";

			$result = $this->db->query($q);
			$receipt['id'] = $this->db->get_last_insert_id("rental_contract_price_item", 'id');

			$price_item->set_id($receipt['id']);

			return $receipt;
		}

		/**
		 * Add a new contract_price_item to the database.  Adds the new insert id to the object reference.
		 *
		 * @param $price_item the contract_price_item to be added
		 * @return mixed receipt from the db operation
		 */
		protected function add( &$price_item )
		{
			$price = $price_item->get_price() ? $price_item->get_price() : 0;
			$total_price = $price_item->get_total_price() ? $price_item->get_total_price() : 0;
			$rented_area = $price_item->get_area();
			$contract = rental_socontract::get_instance()->get_single($price_item->get_contract_id);
			if ($price_item->is_area())
			{
				$rented_area = $contract->get_rented_area();
				if ($rented_area == '')
				{
					$rented_area = 0;
				}
				$total_price = ($rented_area * $price);
			}

			// Build a db-friendly array of the composite object
			$values = array(
				$price_item->get_price_item_id(),
				$price_item->get_contract_id(),
				'\'' . $price_item->get_title() . '\'',
				'\'' . $price_item->get_agresso_id() . '\'',
				($price_item->is_area() ? "true" : "false"),
				str_replace(',', '.', $price),
				str_replace(',', '.', $rented_area),
				str_replace(',', '.', $price_item->get_count()),
				str_replace(',', '.', $total_price),
				($price_item->is_one_time() ? "true" : "false"),
				($price_item->is_billed() ? "true" : "false")
			);

			$cols = array('price_item_id', 'contract_id', 'title', 'agresso_id', 'is_area',
				'price', 'area', 'count', 'total_price', 'is_one_time', 'is_billed');

			if ($price_item->get_date_start())
			{
				$values[] = $this->marshal($price_item->get_date_start(), 'int');
				$cols[] = 'date_start';
			}

			if ($price_item->get_date_end())
			{
				$values[] = $this->marshal($price_item->get_date_end(), 'int');
				$cols[] = 'date_end';
			}

			if ($price_item->get_billing_id())
			{
				$values[] = $this->marshal($price_item->get_billing_id(), 'int');
				$cols[] = 'billing_id';
			}

			$q = "INSERT INTO rental_contract_price_item (" . join(',', $cols) . ") VALUES (" . join(',', $values) . ")";

			$result = $this->db->query($q);
			$receipt['id'] = $this->db->get_last_insert_id("rental_contract_price_item", 'id');

			$price_item->set_id($receipt['id']);

			return $receipt;
		}

		/**
		 * Update the database values for an existing contract price item.
		 *
		 * @param $price_item the contract price item to be updated
		 * @return result receipt from the db operation
		 */
		protected function update( $price_item )
		{
			$id = intval($price_item->get_id());

			/*
			 * Start correction by factors
			 */

			phpgw::import_class('rental.socomposite');
			$contract = rental_socontract::get_instance()->get_single($price_item->get_contract_id());
			$composites = $contract->get_composites();
			foreach ($composites as $composite_id => $composite)
			{
				$composite_obj = rental_socomposite::get_instance()->get_single($composite_id);
				break;
			}

			if(is_object($composite_obj))
			{
				$custom_factor = $composite_obj->get_custom_price_factor();
				$location_info = ExecMethod('rental.bogeneric.read', array(
					'location_info'=> array('type' => 'location_factor'),
					'custom_filter' => array('part_of_town_id = ' . (int)$composite_obj->get_part_of_town_id())
					)
				);
				$location_factor = (float)abs($location_info[0]['factor']) > 0 ? (float)$location_info[0]['factor'] : 1;
				$standard_info = ExecMethod('rental.bogeneric.read_single', array('type' => 'composite_standard', 'id' => $composite_obj->get_standard_id()));
				$standard_factor = (float)abs($standard_info['factor']) > 0 ? (float)$standard_info['factor'] : 1;
			}
			else
			{
				$custom_factor = 1;
				$location_factor = 1;
				$standard_factor = 1;
//				_debug_array($price_item->get_contract_id());
			}
			$custom_factor = $custom_factor ? (float)$custom_factor : 1;
			$location_factor = $location_factor ? (float)$location_factor : 1;
			$standard_factor = $standard_factor ? (float)$standard_factor : 1;

			$factor = $location_factor * $standard_factor * $custom_factor;
			$factor = $factor ? (float)$factor : 1;

			/*
			 * End correction by factors
			 */

			$price = $price_item->get_price() ? $price_item->get_price() : 0;
			//$total_price = $price_item->get_total_price() ? $price_item->get_total_price() : 0;
			//if($total_price == 0){
			//}

			if ($price_item->is_area())
			{
				$total_price = $price_item->get_area() * $price_item->get_price() * $factor;
			}
			else
			{
				$total_price = $price_item->get_count() * $price_item->get_price();
				$location_factor = 1;
				$standard_factor = 1;
				$custom_factor = 1;
			}

			// Build a db-friendly array of the composite object
			$values = array(
				"price_item_id=" . $price_item->get_price_item_id(),
				"contract_id=" . $price_item->get_contract_id(),
				"title=" . '\'' . $price_item->get_title() . '\'',
				"area=" . str_replace(',', '.', $price_item->get_area()),
				"count=" . str_replace(',', '.', $price_item->get_count()),
				"agresso_id=" . '\'' . $price_item->get_agresso_id() . '\'',
				"is_area=" . ($price_item->is_area() ? "true" : "false"),
				"price=" . str_replace(',', '.', $price),
				"total_price=" . str_replace(',', '.', $total_price),
				"date_start=" . $this->marshal($price_item->get_date_start(), 'int'),
				"date_end=" . $this->marshal($price_item->get_date_end(), 'int'),
				"is_one_time=" . ($price_item->get_is_one_time() == "true" ? "true" : "false"),
				"is_billed=" . ($price_item->is_billed() ? "true" : "false"),
				"location_factor = '{$location_factor}'",
				"standard_factor = '{$standard_factor}'",
				"custom_factor = '{$custom_factor}'",
				"billing_id=" . $this->marshal($price_item->get_billing_id(), 'int')
			);
			$this->db->query('UPDATE rental_contract_price_item SET ' . join(',', $values) . " WHERE id=$id", __LINE__, __FILE__);

			$receipt['id'] = $id;
			$receipt['message'][] = array('msg' => lang('Entity %1 has been updated', $entry['id']));
			return $receipt;
		}

		/**
		 * Select total sum of all "active" price-items on a contract.
		 *
		 * @param $contract_id	the id of the contract to generate total price on
		 * @return total_price	the total price
		 */
		public function get_total_price( $contract_id )
		{
			$ts_query = strtotime(date('Y-m-d')); // timestamp for query (today)
			//$this->db->query("SELECT sum(rcpi.total_price::numeric) AS sum_total FROM rental_contract_price_item rcpi, rental_price_item rpi WHERE rpi.id = rcpi.price_item_id AND NOT rpi.is_one_time AND rcpi.contract_id={$contract_id} AND ((rcpi.date_start <= {$ts_query} AND rcpi.date_end >= {$ts_query}) OR (rcpi.date_start <= {$ts_query} AND (rcpi.date_end is null OR rcpi.date_end = 0)) OR (rcpi.date_start is null AND (rcpi.date_end >= {$ts_query} OR rcpi.date_end is null)))");
//		$this->db->query("SELECT sum(total_price::numeric) AS sum_total FROM rental_contract_price_item WHERE NOT is_one_time AND contract_id={$contract_id}");
			$this->db->query("SELECT sum(total_price::numeric) AS sum_total FROM rental_contract_price_item WHERE contract_id={$contract_id} AND (NOT is_one_time OR NOT is_billed)");
			if ($this->db->next_record())
			{
				$total_price = $this->db->f('sum_total');
				return $total_price;
			}
		}

		/**
		 * Select total sum of all "active" price-items on a contract for invoice-generation.
		 * 
		 * @param $contract_id	the id of the contract to generate total price on 
		 * @return total_price	the total price
		 */
		public function get_total_price_invoice( $contract_id, $billing_term, $month, $year )
		{
			$billing_term_id = (int)$billing_term;
			if($billing_term == 5 )
			{
				return $this->get_total_price_invoice_credit($contract_id);
			}
			$sql = "SELECT months FROM rental_billing_term WHERE id = {$billing_term_id}";
			$result = $this->db->query($sql);
			if (!$result)
			{
				return;
			}
			if (!$this->db->next_record())
			{
				return;
			}
			$months = $this->unmarshal($this->db->f('months', true), 'int');
			$timestamp_end = strtotime("{$year}-{$month}-01"); // The first day in the month to bill for
			if ($months == 1)
			{
				$timestamp_start = $timestamp_end; // The first day of the period to bill for
			}
			else
			{
				$months = $months - 1;
				$timestamp_start = strtotime("-{$months} months", $timestamp_end); // The first day of the period to bill for
			}
			$timestamp_end = strtotime('+1 month', $timestamp_end); // The first day in the month after the one to bill for
			//$this->db->query("SELECT sum(rcpi.total_price::numeric) AS sum_total FROM rental_contract_price_item rcpi, rental_price_item rpi WHERE rpi.id = rcpi.price_item_id AND NOT rpi.is_one_time AND rcpi.contract_id={$contract_id} AND ((rcpi.date_start <= {$ts_query} AND rcpi.date_end >= {$ts_query}) OR (rcpi.date_start <= {$ts_query} AND (rcpi.date_end is null OR rcpi.date_end = 0)) OR (rcpi.date_start is null AND (rcpi.date_end >= {$ts_query} OR rcpi.date_end is null)))");
			$q_total_price = "SELECT sum(total_price::numeric) AS sum_total ";
			$q_total_price .= "FROM rental_contract_price_item ";
			$q_total_price .= "WHERE contract_id={$contract_id} ";
			$q_total_price .= "AND NOT is_billed ";
			$q_total_price .= "AND ((NOT date_start IS NULL AND date_start < {$timestamp_end}) OR date_start IS NULL) ";
			$q_total_price .= "AND ((NOT date_end IS NULL AND date_end >= {$timestamp_start}) OR date_end IS NULL)";
			$this->db->query($q_total_price);
			if ($this->db->next_record())
			{
				$total_price = $this->db->f('sum_total');
				return $total_price;
			}
		}
		public function get_total_price_invoice_credit( int $contract_id )
		{
			$q_total_price = "SELECT sum(total_price::numeric) AS sum_total ";
			$q_total_price .= "FROM rental_contract_price_item ";
			$q_total_price .= "WHERE contract_id={$contract_id} ";
			$q_total_price .= "AND NOT is_billed ";
			$q_total_price .= "AND total_price < 0.00 ";
			$this->db->query($q_total_price);
			if ($this->db->next_record())
			{
				$total_price = $this->db->f('sum_total');
				return $total_price;
			}
		}

		/**
		 * Select max area of all "active" price-items on a contract.
		 *
		 * @param $contract_id	the id of the contract to generate total price on
		 * @return max_area	the max area
		 */
		public function get_max_area( $contract_id )
		{
			$this->db->query("SELECT max(area) AS max_area FROM rental_contract_price_item WHERE contract_id={$contract_id} AND is_area");
			if ($this->db->next_record())
			{
				$max_area = $this->db->f('max_area');
				return $max_area;
			}
		}
	}