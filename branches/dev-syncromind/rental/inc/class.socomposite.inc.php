<?php
	phpgw::import_class('rental.socommon');
	phpgw::import_class('rental.uicommon');

	include_class('rental', 'composite', 'inc/model/');
	include_class('rental', 'property_location', 'inc/model/');

	class rental_socomposite extends rental_socommon
	{

		protected static $so;

		/**
		 * Get a static reference to the storage object associated with this model object
		 *
		 * @return the storage object
		 */
		public static function get_instance()
		{
			if (self::$so == null)
			{
				self::$so = CreateObject('rental.socomposite');
			}

			return self::$so;
		}

		protected function get_query( string $sort_field, bool $ascending, string $search_for, string $search_type, array $filters, bool $return_count )
		{
			$location_id_into = $GLOBALS['phpgw']->locations->get_id('rental', '.RESPONSIBILITY.INTO');

			$clauses = array('1=1');
			if ($search_for)
			{
				$like_pattern = "'%" . $this->db->db_addslashes($search_for) . "%'";
				$like_clauses = array();
				switch ($search_type)
				{
					case "name":
						$like_clauses[] = "rental_composite.name $this->like $like_pattern";
						break;
					case "address":
						$like_clauses[] = "rental_composite.address_1 $this->like $like_pattern";
						$like_clauses[] = "rental_composite.address_2 $this->like $like_pattern";
						$like_clauses[] = "rental_composite.house_number $this->like $like_pattern";
						break;
					case "location_code":
						$like_clauses[] = "rental_unit.location_code $this->like $like_pattern";
						break;
					case "all":
						$like_clauses[] = "rental_composite.name $this->like $like_pattern";
						$like_clauses[] = "rental_composite.address_1 $this->like $like_pattern";
						$like_clauses[] = "rental_composite.address_2 $this->like $like_pattern";
						$like_clauses[] = "rental_composite.house_number $this->like $like_pattern";
						$like_clauses[] = "rental_unit.location_code $this->like $like_pattern";
						break;
				}
				if (count($like_clauses))
				{
					$clauses[] = '(' . join(' OR ', $like_clauses) . ')';
				}
			}

			$filter_clauses = array();
			switch ($filters['is_active'])
			{
				case "active":
					$filter_clauses[] = "rental_composite.status_id = 1";
					break;
				case "non_active":
					$filter_clauses[] = "rental_composite.status_id = 2";
					break;
				case "both":
					break;
			}

			if ((int)$filters['status_id'] > 0)
			{
				$filter_clauses[] = "rental_composite.status_id = " . (int) $filters['status_id'];
			}

			$special_query = false; //specify if the query should use distinct on rental_composite.id (used for selecting composites that has an active or inactive contract)
			$ts_query = strtotime(date('Y-m-d')); // timestamp for query (today)
			$availability_date_from = $ts_query;
			$availability_date_to = $ts_query;

			if (isset($filters['availability_date_from']) && $filters['availability_date_from'] != '')
			{
//				$availability_date_from = strtotime($filters['availability_date_from']);
				$availability_date_from =  phpgwapi_datetime::date_to_timestamp($filters['availability_date_from']);
			}

			if (isset($filters['availability_date_to']) && $filters['availability_date_to'] != '')
			{
//				$availability_date_to = strtotime($filters['availability_date_to']);
				$availability_date_to =  phpgwapi_datetime::date_to_timestamp($filters['availability_date_to']);
			}

			switch ($filters['has_contract'])
			{
				case "has_contract":
					$filter_clauses[] = "NOT rental_contract_composite.contract_id IS NULL"; // Composite must have a contract
					$filter_clauses[] = "NOT rental_contract.date_start IS NULL"; // The contract must have start date

					/* The contract's start date not after the end of the period if there is no end date */
					$filter_clauses[] = "
					(
						(
							((NOT rental_contract.date_start > $availability_date_to AND rental_contract.date_end IS NULL)
							OR
							(NOT rental_contract.date_start > $availability_date_to AND NOT rental_contract.date_end IS NULL AND NOT rental_contract.date_end < $availability_date_from))
						)
						OR
						(
							((NOT rental_application.date_start > $availability_date_to AND rental_application.date_end IS NULL)
							OR
							(NOT rental_application.date_start > $availability_date_to AND NOT rental_application.date_end IS NULL AND NOT rental_application.date_end < $availability_date_from))
						)
					)";
					$special_query = true;
					break;
				case "has_no_contract":
					$filter_clauses[] = "
				(
					-- rental_contract_composite.contract_id IS NULL OR
					-- rental_application_composite.application_id IS NULL OR
					NOT rental_composite.id IN
					(
						SELECT rental_composite.id FROM rental_composite 
						LEFT JOIN rental_contract_composite ON (rental_contract_composite.composite_id = rental_composite.id) 
						LEFT JOIN rental_contract ON (rental_contract.id = rental_contract_composite.contract_id) 
						LEFT JOIN rental_application_composite ON (rental_application_composite.composite_id = rental_composite.id)
						LEFT JOIN rental_application ON (rental_application.id = rental_application_composite.application_id)
						WHERE  
						(
							(
								NOT rental_contract_composite.contract_id IS NULL AND
								NOT rental_contract.date_start IS NULL AND
								((NOT rental_contract.date_start > $availability_date_to AND rental_contract.date_end IS NULL)
								OR
								(NOT rental_contract.date_start > $availability_date_to AND NOT rental_contract.date_end IS NULL AND NOT rental_contract.date_end < $availability_date_from))
							)
							OR
							(
								NOT rental_application_composite.application_id IS NULL AND
								NOT rental_application.date_start IS NULL AND
								((NOT rental_application.date_start > $availability_date_to AND rental_application.date_end IS NULL)
								OR
								(NOT rental_application.date_start > $availability_date_to AND NOT rental_application.date_end IS NULL AND NOT rental_application.date_end < $availability_date_from))
							)


						)
					)
				)
				";
					$special_query = true;
					break;
				case "both":
					break;
			}

			// Furnished, partly furnished, not furnished, not specified
			if (isset($filters['furnished_status']) & $filters['furnished_status'] < 4)
			{
				// Not specified
				if ($filters['furnished_status'] == 0)
					$filter_clauses[] = "rental_composite.furnish_type_id IS NULL";
				else
					$filter_clauses[] = "rental_composite.furnish_type_id=" . $filters['furnished_status'];
			}

			if (isset($filters['not_in_contract']))
			{
				$filter_clauses[] = "(rental_contract_composite.contract_id != " . $filters['not_in_contract'] . " OR rental_contract_composite.contract_id IS NULL)";
			}

			if (isset($filters['location_code']))
			{
				$filter_clauses[] = "rental_unit.location_code = '" . $filters['location_code'] . "'";
			}

			if (isset($filters['contract_id']))
			{
				$filter_clauses[] = "contract_id = {$this->marshal($filters['contract_id'], 'int')}";
			}

			if (isset($filters['application_id']))
			{
				$filter_clauses[] = "application_id = {$this->marshal($filters['application_id'], 'int')}";
			}

			if (isset($filters['composite_type_id']))
			{
				$filter_clauses[] = "rental_composite.composite_type_id = {$this->marshal($filters['composite_type_id'], 'int')}";
			}

			if (isset($filters[$this->get_id_field_name()]))
			{
				$filter_clauses[] = "rental_composite.id = {$this->marshal($filters[$this->get_id_field_name()], 'int')}";
			}
			

			$tables = "rental_composite";
			$joins = "	{$this->left_join} rental_unit ON (rental_composite.id = rental_unit.composite_id)";
			$joins .= "	{$this->left_join} rental_contract_composite ON (rental_contract_composite.composite_id = rental_composite.id)";
			$joins .= "	{$this->left_join} rental_contract ON (rental_contract.id = rental_contract_composite.contract_id)";
			$joins .= " {$this->left_join} rental_application_composite ON (rental_application_composite.composite_id = rental_composite.id)";
			$joins .= " {$this->left_join} rental_application ON (rental_application.id = rental_application_composite.application_id)";

			if (isset($filters['district_id']) && $filters['district_id'])
			{
				$joins .= "	{$this->join} fm_locations ON (rental_unit.location_code = fm_locations.location_code)";
				$joins .= "	{$this->join} fm_location1 ON (fm_location1.loc1 = fm_locations.loc1)";
				$joins .= "	{$this->join} fm_part_of_town ON (fm_location1.part_of_town_id = fm_part_of_town.id)";

				$filter_clauses[] = "fm_part_of_town.district_id =" . (int)$filters['district_id'];
			}
			if (count($filter_clauses))
			{
				$clauses[] = join(' AND ', $filter_clauses);
			}

			$condition = join(' AND ', $clauses);

			if ($return_count) // We should only return a count
			{
				$cols = 'COUNT(DISTINCT(rental_composite.id)) AS count';
			}
			else
			{
				if ($special_query)
				{
					$cols = "DISTINCT(rental_composite.id) AS composite_id,";
				}
				else
				{
					$cols = "rental_composite.id AS composite_id,";
				}

				$cols .= "rental_unit.id AS unit_id, rental_unit.location_code, rental_composite.name,
					rental_composite.has_custom_address, rental_composite.address_1, rental_composite.house_number,
					  rental_composite.address_2, rental_composite.postcode, rental_composite.place,
					  rental_composite.is_active, rental_composite.area, rental_composite.description,
					  rental_composite.furnish_type_id, rental_composite.standard_id,rental_composite.composite_type_id,rental_composite.status_id,
					  rental_composite.part_of_town_id, rental_composite.custom_price_factor, rental_composite.custom_price, rental_composite.price_type_id,";
				$cols .= "rental_contract.id AS contract_id, rental_contract.date_start, rental_contract.date_end, rental_contract.old_contract_id, ";
				$cols .= "rental_application.id AS application_id, rental_application.date_start AS application_date_start, rental_application.date_end AS application_date_end, ";
				$cols .= "
			CASE WHEN
			(
				(
				NOT rental_contract_composite.contract_id IS NULL AND
				NOT rental_contract.date_start IS NULL AND
				NOT rental_contract.location_id = {$location_id_into} AND
				((NOT rental_contract.date_start > $availability_date_to AND rental_contract.date_end IS NULL)
		 		OR
				(NOT rental_contract.date_start > $availability_date_to AND NOT rental_contract.date_end IS NULL AND NOT rental_contract.date_end < $availability_date_from))
				)
				OR
				(
				NOT rental_application_composite.application_id IS NULL AND
				NOT rental_application.date_start IS NULL AND
				((NOT rental_application.date_start > $availability_date_to AND rental_application.date_end IS NULL)
				OR
				(NOT rental_application.date_start > $availability_date_to AND NOT rental_application.date_end IS NULL AND NOT rental_application.date_end < $availability_date_from))
				)
			)
			THEN 'Ikke ledig' ELSE 'Ledig' END as status";
			}
			$dir = $ascending ? 'ASC' : 'DESC';
			if ($sort_field == 'name')
			{
				$sort_field = 'rental_composite.name';
			}
			else if ($sort_field == 'location_code')
			{
				$sort_field = 'rental_unit.location_code';
			}

			$order = $sort_field ? "ORDER BY {$this->marshal($sort_field, 'field')} $dir " : '';
			$this->sort_field = $sort_field;

			switch ($sort_field)
			{
				case 'status':
					$this->skip_limit_query = true;
					break;

				default:
					break;
			}

//	    _debug_array("SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}");

			return "SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}";
		}

		function populate( int $composite_id, &$composite )
		{
			if ($composite == null) // new object
			{
				$composite = new rental_composite($composite_id);
				$composite->set_description($this->unmarshal($this->db->f('description', true), 'string'));
				$composite->set_is_active($this->db->f('is_active'));
				$composite_name = $this->unmarshal($this->db->f('name', true), 'string');
				if ($composite_name == null || $composite_name == '')
				{
					$composite_name = lang('no_name_composite', $composite_id);
				}

				$composite->set_name($composite_name);
				$composite->set_has_custom_address($this->unmarshal($this->db->f('has_custom_address', true), 'bool'));
				$composite->set_custom_address_1($this->unmarshal($this->db->f('address_1', true), 'string'));
				$composite->set_custom_address_2($this->unmarshal($this->db->f('address_2', true), 'string'));
				$composite->set_custom_house_number($this->unmarshal($this->db->f('house_number', true), 'string'));
				$composite->set_custom_postcode($this->unmarshal($this->db->f('postcode', true), 'string'));
				$composite->set_custom_place($this->unmarshal($this->db->f('place', true), 'string'));
				$composite->set_area($this->unmarshal($this->db->f('area', true), 'float'));
				$composite->set_furnish_type_id($this->unmarshal($this->db->f('furnish_type_id'), 'int'));
				$composite->set_standard_id($this->unmarshal($this->db->f('standard_id'), 'int'));
				$composite->set_composite_type_id($this->unmarshal($this->db->f('composite_type_id'), 'int'));
				$composite->set_part_of_town_id($this->unmarshal($this->db->f('part_of_town_id'), 'int'));
				$composite->set_custom_price_factor($this->unmarshal($this->db->f('custom_price_factor', true), 'float'));
				$composite->set_price_type_id($this->unmarshal($this->db->f('price_type_id'), 'int'));
				$composite->set_custom_price($this->unmarshal($this->db->f('custom_price'), 'float'));
				$composite->set_status_id($this->unmarshal($this->db->f('status_id'), 'int'));
			}
			// Location code
			$location_code = $this->unmarshal($this->db->f('location_code', true), 'string');

			//Status
			$database_status = $this->unmarshal($this->db->f('status', true), 'string');
			$composite_status = $composite->get_status();

			if ($composite_status != 'Ikke ledig')
			{
				$composite->set_status($database_status);
			}

			$contract_id = $this->unmarshal($this->db->f('contract_id', true), 'int');

			// Adds contract to array in composite object if it's not already added
			if ($contract_id != 0 & !$composite->contains_contract($contract_id))
			{
				$contract = new rental_contract($contract_id);

				$start_date = $this->unmarshal($this->db->f('date_start', true), 'int');
				$end_date = $this->unmarshal($this->db->f('date_end', true), 'int');
				$old_contract_id = $this->unmarshal($this->db->f('old_contract_id', true), 'string');

				// Adds contract if end date is not specified or greater than todays date
				if ($end_date == 0 || $end_date > time())
				{
					$contract_date = new rental_contract_date($start_date, $end_date);
					$contract->set_contract_date($contract_date);
					$contract->set_old_contract_id($old_contract_id);

					$composite->add_contract($contract);
				}
			}
			
			if (!$composite->contains_unit($location_code))
			{
				//composite inneholder ikke unit -> legg den til
				$location = null;
				try
				{
					// We get the data from the property module
					$data = @execMethod('property.bolocation.read_single', array('location_code' => $location_code,
							'extra' => array('view' => true)));
					if ($data != null)
					{
						$level = -1;
						$names = array();
						$levelFound = false;
						for ($i = 1; $i < 6; $i++)
						{
							$loc_name = 'loc' . $i . '_name';
							if (array_key_exists($loc_name, $data))
							{
								$level = $i;
								$names[$level] = $data[$loc_name];
							}
						}
						$gab_id = '';
						$gabinfos = @execMethod('property.sogab.read', array('location_code' => $location_code,
								'allrows' => true));
						if ($gabinfos != null && is_array($gabinfos) && count($gabinfos) == 1)
						{
							$gabinfo = array_shift($gabinfos);
							$gab_id = $gabinfo['gab_id'];
						}
						$location = new rental_property_location($location_code, rental_uicommon::get_nicely_formatted_gab_id($gab_id), $level, $names);
						if (isset($data['street_name']) && $data['street_name'])
						{
							$location->set_address_1($data['street_name'] . ' ' . $data['street_number']);
						}
						//$location->set_address_1($data['address']);
						foreach ($data['attributes'] as $attributes)
						{
							switch ($attributes['column_name'])
							{
								case 'bruttoareal':
								case 'area_gross':
									$location->set_area_gros($attributes['value']);
									break;
								case 'nettoareal':
								case 'area_net':
									$location->set_area_net($attributes['value']);
									break;
							}
						}
					}
					else
					{
						$location = new rental_property_location($location_code, '', 1, array());
					}
				}
				catch (Exception $e)
				{
					$location = new rental_property_location($location_code, '', 1, array());
				}
				$composite->add_unit(new rental_unit($this->unmarshal($this->db->f('unit_id', true), 'int'), $composite_id, $location));
			}

			return $composite;
		}

		public function get_id_field_name( $extended_info = false )
		{
			if (!$extended_info)
			{
				$ret = 'composite_id';
			}
			else
			{
				$ret = array
					(
					'table' => 'rental_composite', // alias
					'field' => 'id',
					'translated' => 'composite_id'
				);
			}
			return $ret;
		}

		/**
		 * Update the database values for an existing composite object. Also updates associated rental units.
		 *
		 * @param $composite the composite to be updated
		 * @return result receipt from the db operation
		 */
		public function update( $composite )
		{
			$id = intval($composite->get_id());

			$values = array(
				'name = \'' . $composite->get_name() . '\'',
				'description = \'' . $composite->get_description() . '\'',
				'has_custom_address = ' . ($composite->has_custom_address() ? "true" : "false"),
				'address_1 = \'' . $composite->get_custom_address_1() . '\'',
				'address_2 = \'' . $composite->get_custom_address_2() . '\'',
				'house_number = \'' . $composite->get_custom_house_number() . '\'',
				'postcode = \'' . $composite->get_custom_postcode() . '\'',
				'place = \'' . $composite->get_custom_place() . '\'',
//				'is_active = \'' . ($composite->is_active() ? 'true' : 'false') . '\'',
				'object_type_id = ' . $composite->get_object_type_id(),
				'area = ' . $this->marshal($composite->get_area(), 'float'),
				'furnish_type_id = ' . $composite->get_furnish_type_id(),
				'standard_id = ' . $composite->get_standard_id(),
				'composite_type_id = ' . $composite->get_composite_type_id(),
				'part_of_town_id = ' . $composite->get_part_of_town_id(),
				'custom_price_factor = \'' . $composite->get_custom_price_factor() . '\'',
				'price_type_id = ' . $composite->get_price_type_id(),
				'status_id = ' . $composite->get_status_id(),
				'custom_price = \'' . $composite->get_custom_price() . '\''
			);

			$result = $this->db->query('UPDATE rental_composite SET ' . join(',', $values) . " WHERE id=$id", __LINE__, __FILE__);

			return $result != null;
		}

		/**
		 * Add a new composite to the database.  Adds the new insert id to the object reference.
		 * Also saves included rental_unit objects.
		 *
		 * @param $composite the composite to be added
		 * @return int with id of the composite
		 */
		public function add( &$composite )
		{
			// Build a db-friendly array of the composite object
			$cols = array('name', 'description', 'has_custom_address', 'address_1', 'address_2',
				'house_number', 'postcode', 'place', 'object_type_id', 'area', 'furnish_type_id',
				'standard_id','composite_type_id', 'part_of_town_id', 'custom_price_factor','price_type_id', 'status_id', 'custom_price');
			$values = array(
				"'" . $composite->get_name() . "'",
				"'" . $composite->get_description() . "'",
				($composite->has_custom_address() ? "true" : "false"),
				"'" . $composite->get_custom_address_1() . "'",
				"'" . $composite->get_custom_address_2() . "'",
				"'" . $composite->get_custom_house_number() . "'",
				"'" . $composite->get_custom_postcode() . "'",
				"'" . $composite->get_custom_place() . "'",
				$composite->get_object_type_id(),
				$this->marshal($composite->get_area(), 'float'),
				$composite->get_furnish_type_id(),
				$composite->get_standard_id(),
				$composite->get_composite_type_id(),
				$composite->get_part_of_town_id(),
				"'" . $composite->get_custom_price_factor() . "'",
				$composite->get_price_type_id(),
				$composite->get_status_id(),
				"'" . $composite->get_custom_price() . "'"
			);

			$query = "INSERT INTO rental_composite (" . join(',', $cols) . ") VALUES (" . join(',', $values) . ")";
			$result = $this->db->query($query);

			$composite_id = $this->db->get_last_insert_id('rental_composite', 'id');
			$composite->set_id($composite_id);
			return $composite_id;
		}

		/**
		 * HACK to return the location code for a given contract id. The metod
		 * could've been more generalized, but the Agresso file format already
		 * breaks the model of PE..
		 *
		 * @param $contract_id int with id of contract.
		 * @return string with location code, empty string if not found.
		 */
		public function get_building_location_code( $contract_id )
		{
			$query = "SELECT location_code FROM rental_unit {$this->left_join} rental_contract_composite ON (rental_contract_composite.composite_id = rental_unit.composite_id) WHERE rental_contract_composite.contract_id = {$contract_id}";
			$result = $this->db->limit_query($query, 0, __LINE__, __FILE__, 1);

			if ($result && $this->db->next_record()) // Query ok
			{
				$location_code = $this->db->f('location_code', true);
				if ($location_code != null && $location_code != '')
				{
					return substr(str_replace('-', '', $location_code), 0, 6);
				}
			}
			return '';
		}

		public function get_area( $composite_id )
		{
			$sql = "SELECT area FROM rental_composite WHERE id = " . $this->marshal($composite_id, 'float');
			$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);

			if ($this->db->next_record())
			{
				return $this->unmarshal($this->db->f('area', true), 'float');
			}

			return null;
		}

		public function get_uicols()
		{
			$uicols = array();

			$uicols['name'][] = 'id';
			$uicols['descr'][] = lang('serial');
			$uicols['sortable'][] = false;
			$uicols['input_type'][] = 'hidden';

			$uicols['name'][] = 'location_code';
			$uicols['descr'][] = lang('object_number');
			$uicols['sortable'][] = true;
			$uicols['input_type'][] = 'text';

			$uicols['name'][] = 'name';
			$uicols['descr'][] = lang('name');
			$uicols['sortable'][] = true;
			$uicols['input_type'][] = 'text';

			$uicols['name'][] = 'address';
			$uicols['descr'][] = lang('address');
			$uicols['sortable'][] = false;
			$uicols['input_type'][] = 'text';

			$uicols['name'][] = 'gab_id';
			$uicols['descr'][] = lang('propertyident');
			$uicols['sortable'][] = false;
			$uicols['input_type'][] = 'text';

			$uicols['name'][] = 'status';
			$uicols['descr'][] = lang('status');
			$uicols['sortable'][] = true;
			$uicols['input_type'][] = 'text';

			$uicols['name'][] = 'custom_price';
			$uicols['descr'][] = lang('custom price');
			$uicols['sortable'][] = true;
			$uicols['input_type'][] = 'text';

			$uicols['name'][] = 'price_type';
			$uicols['descr'][] = lang('price type');
			$uicols['sortable'][] = false;
			$uicols['input_type'][] = 'text';

			$uicols['name'][] = 'area_net';
			$uicols['descr'][] = lang('area_net');
			$uicols['sortable'][] = false;
			$uicols['input_type'][] = 'text';

			$uicols['name'][] = 'area_gros';
			$uicols['descr'][] = lang('area_gros');
			$uicols['sortable'][] = false;
			$uicols['input_type'][] = 'text';

			return $uicols;
		}
	}