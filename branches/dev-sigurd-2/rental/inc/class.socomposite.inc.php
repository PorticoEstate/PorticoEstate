<?php
phpgw::import_class('rental.socommon');
phpgw::import_class('rental.uicommon');

include_class('rental', 'composite', 'inc/model/');
include_class('rental', 'property_location', 'inc/model/');
//include_class('rental', 'contract_date', 'inc/model/');
//include_class('rental', 'contract', 'inc/model/');
// XXX ^

class rental_socomposite extends rental_socommon
{
	function __construct()
	{
		parent::__construct('rental_composite',
		array
		(
					'id'	=> array('type' => 'int'),
					'description' => array('type' => 'string'),
					'is_active' => array('type', 'bool'),
 					'name'	=> array('type' => 'string'),
					'has_custom_address' => array('type' => 'bool'),
					'address_1'	=> array('type' => 'string'),
					'address_2'	=> array('type' => 'string'),
					'house_number' => array('type' => 'string'),
					'postcode' => array('type' => 'string'),
					'place' => array('type' => 'string'),
					'adresse1' => array('type' => 'string'),
					'adresse2' => array('type' => 'string'),
					'postnummer' => array('type' => 'int'),
					'poststed' => array('type' => 'string'),
					'gab_id' => array('type' => 'string'),
					'date_from' => array('type' => 'date'),
					'date_to' => array('type' => 'date')
		));
	}

	protected function get_conditions($query, $filters,$search_option)
	{
		$clauses = array('1=1');
		if($query)
		{

			$like_pattern = "'%" . $this->db->db_addslashes($query) . "%'";
			$like_clauses = array();
			switch($search_option){
				case "id":
					$like_clauses[] = "rental_composite.id = $query";
					break;
				case "name":
					$like_clauses[] = "rental_composite.name $this->like $like_pattern";
					break;
				case "address":
					$like_clauses[] = "rental_composite.address_1 $this->like $like_pattern";
					$like_clauses[] = "rental_composite.address_2 $this->like $like_pattern";
					$like_clauses[] = "rental_composite.house_number $this->like $like_pattern";
					break;
				case "property_id":
					$like_clauses[] = "rental_unit.location_code $this->like $like_pattern";
				case "all":
					$like_clauses[] = "rental_composite.id = $query";
					$like_clauses[] = "rental_composite.name $this->like $like_pattern";
					$like_clauses[] = "rental_composite.address_1 $this->like $like_pattern";
					$like_clauses[] = "rental_composite.address_2 $this->like $like_pattern";
					$like_clauses[] = "rental_composite.house_number $this->like $like_pattern";
					$like_clauses[] = "rental_unit.location_code $this->like $like_pattern";
					break;
			}

			if(count($like_clauses))
			{
				$clauses[] = '(' . join(' OR ', $like_clauses) . ')';
			}


		}

		$filter_clauses = array();
		switch($filters['is_active']){
			case "active":
				$filter_clauses[] = "rental_composite.is_active = TRUE";
				break;
			case "non_active":
				$filter_clauses[] = "rental_composite.is_active = FALSE";
				break;
			case "both":
				break;
		}

		if(isset($filters['contract_id'])){
			$filter_clauses[] = "rental_contract_composite.contract_id != ".$filters['contract_id'];
		}

		if(count($filter_clauses))
			{
				$clauses[] = join(' AND ', $filter_clauses);
			}

		return join(' AND ', $clauses);
	}


	/**
	 * Get a list of composite objects matching the specific filters
	 *
	 * @param $start search result offset
	 * @param $results number of results to return
	 * @param $sort field to sort by
	 * @param $query LIKE-based query string
	 * @param $filters array of custom filters
	 * @return list of rental_composite objects
	 */
	function get_composite_array($start = 0, $results = 1000, $sort = null, $dir = '', $query = null, $search_option = null, $filters = array())
	{
		$condition = $this->get_conditions($query, $filters,$search_option);

		$composite_not_in = '';
		if(isset($filters['contract_id'])){
			die('needs new impl 4');
			// XXX Å^
			$composite_not_in = "AND rental_composite.id NOT IN (SELECT composite_id FROM rental_contract_composite WHERE contract_id = ".$filters['contract_id'].")";
		}

		$tables = "rental_composite";
		$joins = "	{$this->join} rental_unit ON (rental_composite.id = rental_unit.composite_id)";
		$cols = 'rental_composite.id AS composite_id, rental_unit.location_code, rental_composite.name, rental_composite.has_custom_address, rental_composite.address_1, rental_composite.house_number, rental_composite.address_2, rental_composite.postcode, rental_composite.place';

		// Calculate total number of records
		$this->db->query("SELECT COUNT(distinct rental_composite.id) AS count FROM $tables $joins WHERE $condition $composite_not_in", __LINE__, __FILE__);
		$this->db->next_record();
		$total_records = (int)$this->db->f('count');

		$order = $sort ? "ORDER BY $sort $dir ": '';

//		var_dump("SELECT * FROM (SELECT $distinct $cols FROM $tables $joins WHERE $condition $composite_not_in) AS result $order");
		if($order != '') // ORDER should be used
		{
			// We get a 'ERROR: SELECT DISTINCT ON expressions must match initial ORDER BY expressions' if we don't wrap the ORDER query.
			$this->db->limit_query("SELECT * FROM (SELECT $distinct $cols FROM $tables $joins WHERE $condition $composite_not_in) AS result $order", $start, __LINE__, __FILE__, $limit);
		}
		else
		{
			$this->db->limit_query("SELECT $distinct $cols FROM $tables $joins WHERE $condition $composite_not_in", $start, __LINE__, __FILE__, $limit);
		}

		$composites = array();

		while ($this->db->next_record()) // Runs through all of the results
		{
			$composite_id = $this->unmarshal($this->db->f('composite_id', true), 'int');
			if(array_key_exists($composite_id, $composites)) // We've already added the composite to the array
			{
				$composite = &$composites[$composite_id];
			}
			else // We haven't added the composite yet
			{
				$composites[$composite_id] = new rental_composite($composite_id);
				$composite = &$composites[$composite_id];
			}
			$location_code = $this->unmarshal($this->db->f('location_code', true), 'string');
			// We get the data from the property module
			$data = execMethod('property.bolocation.read_single', $location_code);
			$level = -1;
			$generic_name = '';
			$names = array();
			$levelFound = false;
			for($i = 1; !$levelFound; $i++)
			{
				$loc_name = 'loc'.$i.'_name';
				if(array_key_exists($loc_name, $data))
				{
					$level = $i;
					$generic_name = $data[$loc_name];
					$names[$level] = $generic_name;
				}
				else{
					$levelFound = true;
				}
			}
			$gab_id = '';
			$gabinfos  = execMethod('property.sogab.read', array('location_code' => $location_code, 'sallrows' => true));
			if($gabinfos != null && is_array($gabinfos) && count($gabinfos) == 1)
			{
				$gabinfo = array_shift($gabinfos);
				$gab_id = $gabinfo['gab_id'];
			}
			$location = new rental_property_location($location_code, rental_uicommon::get_nicely_formatted_gab_id($gab_id), $name, $level, $names);
			$location->set_address_1($data['street_name'].' '.$data['street_number']);
			foreach($data['attributes'] as $attributes)
			{
				switch($attributes['column_name'])
				{
					case 'area_gross':
						$location->set_area_gros($attributes['value']);
						break;
					case 'area_net':
						$location->set_area_net($attributes['value']);
						break;
				}
			}

			$composite->set_description($this->unmarshal($this->db->f('description', true), 'string'));
			$composite->set_is_active($this->db->f('is_active'));
			$composite_name = $this->unmarshal($this->db->f('name', true), 'string');
			if($composite_name == null || $composite_name == '')
			{
				$composite_name = lang('no_name_composite', $composite_id);
			}
			$composite->set_name($composite_name);
			$composite->set_has_custom_address($this->unmarshal($this->db->f('has_custom_address', true), 'bool'));

			$composite->add_unit(new rental_unit($composite_id, $location));

			$composite->set_custom_address_1($this->unmarshal($this->db->f('address_1', true), 'string'));
			$composite->set_custom_address_2($this->unmarshal($this->db->f('address_2', true), 'string'));
			$composite->set_custom_house_number($this->unmarshal($this->db->f('house_number', true), 'string'));
			$composite->set_custom_postcode($this->unmarshal($this->db->f('postcode', true), 'string'));
			$composite->set_custom_place($this->unmarshal($this->db->f('place', true), 'string'));
		}
		return $composites;
	}

	/**
	 * Get single rental composite
	 *
	 * @param	$id	id of the rental composite to return
	 * @return a rental_composite
	 */
	function get_single($composite_id)
	{
		$composite_id = (int)$composite_id;

		$sql = "SELECT rental_unit.location_code, rental_composite.name, rental_composite.has_custom_address, rental_composite.address_1, rental_composite.house_number, rental_composite.address_2, rental_composite.postcode, rental_composite.place, rental_composite.is_active, rental_composite.description FROM rental_unit LEFT JOIN rental_composite ON (rental_composite.id = rental_unit.composite_id) WHERE rental_composite.id={$composite_id}";
		$this->db->query($sql, __LINE__, __FILE__);

		$composite = new rental_composite($composite_id);
		while ($this->db->next_record()) // Runs through all of the results
		{
			$location_code = $this->unmarshal($this->db->f('location_code', true), 'string');
			// We get the data from the property module
			$data = execMethod('property.bolocation.read_single', $location_code);
			$level = -1;
			$generic_name = '';
			$names = array();
			$levelFound = false;
			for($i = 1; !$levelFound; $i++)
			{
				$loc_name = 'loc'.$i.'_name';
				if(array_key_exists($loc_name, $data))
				{
					$level = $i;
					$generic_name = $data[$loc_name];
					$names[$level] = $generic_name;
				}
				else{
					$levelFound = true;
				}
			}
			$gab_id = '';
			$gabinfos  = execMethod('property.sogab.read', array('location_code' => $location_code, 'sallrows' => true));
			if($gabinfos != null && is_array($gabinfos) && count($gabinfos) == 1)
			{
				$gabinfo = array_shift($gabinfos);
				$gab_id = $gabinfo['gab_id'];
			}
			$location = new rental_property_location($location_code, rental_uicommon::get_nicely_formatted_gab_id($gab_id), $name, $level, $names);
			$location->set_address_1($data['street_name'].' '.$data['street_number']);
			foreach($data['attributes'] as $attributes)
			{
				switch($attributes['column_name'])
				{
					case 'area_gross':
						$location->set_area_gros($attributes['value']);
						break;
					case 'area_net':
						$location->set_area_net($attributes['value']);
						break;
				}
			}
			$composite->set_description($this->unmarshal($this->db->f('description', true), 'string'));
			$composite->set_is_active($this->db->f('is_active'));
			$composite_name = $this->unmarshal($this->db->f('name', true), 'string');
			if($composite_name == null || $composite_name == '')
			{
				$composite_name = lang('no_name_composite', $composite_id);
			}
			$composite->set_name($composite_name);
			$composite->set_has_custom_address($this->unmarshal($this->db->f('has_custom_address', true), 'bool'));

			$composite->add_unit(new rental_unit($composite_id, $location));

			$composite->set_custom_address_1($this->unmarshal($this->db->f('address_1', true), 'string'));
			$composite->set_custom_address_2($this->unmarshal($this->db->f('address_2', true), 'string'));
			$composite->set_custom_house_number($this->unmarshal($this->db->f('house_number', true), 'string'));
			$composite->set_custom_postcode($this->unmarshal($this->db->f('postcode', true), 'string'));
			$composite->set_custom_place($this->unmarshal($this->db->f('place', true), 'string'));
		}
		return $composite;
	}

	/**
	 * Gets all areas that have been added to a composite
	 *
	 * @param	params	array( (id=?) AND ordering information )
	 * @return	rows	array( (fieldname=fieldvalue) AND accumulated areas AND total number of included areas)
	 */
	function get_included_rental_units($composite_id, $sort = null, $dir = 'asc', $start = 0, $results = null)
	{
		$composite_id = (int)$composite_id;

		//Return array
		$units = array();

		// First we find the number of areas available in total
		$sql = 'SELECT COUNT(location_code) AS count FROM rental_unit WHERE composite_id ='.$composite_id;
		$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);

		$order = '';
		if($sort != null && $sort != '') // We should ask for a ordered resultset
		{
			$order = ' ORDER BY '.$sort.' '.$dir;
		}
		// Second we get ids for all areas for specified composite id
		$sql = 'SELECT location_code FROM rental_unit WHERE composite_id ='.$composite_id.$order;
		$this->db->query($sql, __LINE__, __FILE__);

		while ($this->db->next_record())
		{
			// We get the data from the property module
			$data = execMethod('property.bolocation.read_single', $location_code);
			$level = -1;
			$generic_name = '';
			$names = array();
			$levelFound = false;
			for($i = 1; !$levelFound; $i++)
			{
				$loc_name = 'loc'.$i.'_name';
				if(array_key_exists($loc_name, $data))
				{
					$level = $i;
					$generic_name = $data[$loc_name];
					$names[$level] = $generic_name;
				}
				else{
					$levelFound = true;
				}
			}
			$gab_id = '';
			$gabinfos  = execMethod('property.sogab.read', array('location_code' => $location_code, 'sallrows' => true));
			if($gabinfos != null && is_array($gabinfos) && count($gabinfos) == 1)
			{
				$gabinfo = array_shift($gabinfos);
				$gab_id = $gabinfo['gab_id'];
			}
			$location = new rental_property_location($location_code, rental_uicommon::get_nicely_formatted_gab_id($gab_id), $name, $level, $names);
			$location->set_address_1($data['street_name'].' '.$data['street_number']);
			foreach($data['attributes'] as $attributes)
			{
				switch($attributes['column_name'])
				{
					case 'area_gross':
						$location->set_area_gros($attributes['value']);
						break;
					case 'area_net':
						$location->set_area_net($attributes['value']);
						break;
				}
			}
			$units[] = new rental_unit($composite_id, $location);
		}

		return $units;
	}

	/**
	 * Returns an array of units on a specified level. It's possible to specify
	 * a location code to which the unit must be related and also paging and
	 * sorting.
	 *
	 * @param $level int 1-5 with type of unit.
	 * @param $location_code_related string with related location.
	 * @param $start int with start row.
	 * @param $num_of_hits int with number of hits to return.
	 * @param $sort_ascending bool telling to sort ascending or not.
	 * @return array of rental_unit objects.
	 */
	public function get_unit_array($level = 2, string $location_code_related = null, $start = 0, $num_of_hits = 10000, $sort = 'location_code', $sort_ascending = true)
	{
		// Return array
		$unit_array = array();
		// Location code
		$where = '';
		if($location_code_related != null) // Location code set - should only look for units in relation to this one
		{
			$where = ' WHERE location_code == ' . (int)$location_code_related;
		}

		// Calculate total number of records
		$this->db->query("SELECT COUNT(*) AS count FROM rental_unit $where", __LINE__, __FILE__);
		$this->db->next_record();
		$total_records = (int)$this->db->f('count');

		$dir = $sort_ascending ? 'asc' : 'desc';
		$order = $sort ? " ORDER BY $sort $dir ": '';

		$sql = 'SELECT composite_id, location_code FROM rental_unit'.$where.$order;

		$this->db->limit_query($sql, $start, __LINE__, __FILE__, $num_of_hits);
		while ($this->db->next_record()) // Runs through all of the results
		{
			$location_code = $this->unmarshal($this->db->f('location_code', true), 'string');
			// We get the data from the property module
			$data = execMethod('property.bolocation.read_single', $location_code);
			$level = -1;
			$generic_name = '';
			$names = array();
			$levelFound = false;
			$gab_id = '';
			$gabinfos  = execMethod('property.sogab.read', array('location_code' => $location_code, 'sallrows' => true));
			if($gabinfos != null && is_array($gabinfos) && count($gabinfos) == 1)
			{
				$gabinfo = array_shift($gabinfos);
				$gab_id = $gabinfo['gab_id'];
			}
			$location = new rental_property_location($location_code, rental_uicommon::get_nicely_formatted_gab_id($gab_id), $name, $level, $names);
			$location->set_address_1($data['street_name'].' '.$data['street_number']);
			foreach($data['attributes'] as $attributes)
			{
				switch($attributes['column_name'])
				{
					case 'area_gross':
						$location->set_area_gros($attributes['value']);
						break;
					case 'area_net':
						$location->set_area_net($attributes['value']);
						break;
				}
			}
			$unit_array[] = new rental_unit(-1, $location); // We set the composite id to -1 as we don't know if the unit is included in 0, 1 or more composites
		}
		return $unit_array;
	}

	/**
	 * Returns all contracts for a specified composite.
	 *
	 * @param $params array with parameters for the query
	 * @return array with 'total_records' and 'results'.
	 */
	public function get_contracts($id, $sort = null, $dir = null, $start = 0, $limit = 1000, $contract_status = null, $date = null)
	{
		// Params
		$id = (int)$id;

		// Default return data:
		$total_records = 0;
		$results = array();

		$contracts = array();

		if($id > 0) // Valid id
		{
			$tables = 'rental_contract';
			$joins = 'JOIN rental_contract_composite ON (rental_contract.id = rental_contract_composite.contract_id)';
			$condition = 'rental_contract_composite.composite_id = '.$id;
			$current_date = date('Y-m-d');
			switch($contract_date)
			{
				case 'all':
					/* no-op */
					break;
				case 'not_started':
					$condition .= " AND rental_contract.date_start > '{$current_date}'";
					break;
				case 'ended':
					$condition .= " AND rental_contract.date_end < '{$current_date}'";
					break;
				case 'active':
				default:
					$condition .= " AND (rental_contract.date_start <= '{$current_date}' AND rental_contract.date_end >= '{$current_date}')";
					break;
			}

			$order = '';

			if($sort != null) // We should sort results
			{
				$order = 'ORDER BY '.$sort.' '.($dir == 'desc' ? 'desc' : 'asc');
			}

			$this->db->query("SELECT COUNT(distinct rental_contract.id) AS count FROM $tables $joins WHERE $condition", __LINE__, __FILE__);
			$this->db->next_record();
			$total_records = (int)$this->db->f('count');

			$sql = "SELECT rental_contract.id, date_start, date_end FROM {$tables} {$joins} WHERE {$condition} {$order}";
			$this->db->limit_query($sql, $start, __LINE__, __FILE__, $limit);
			while($this->db->next_record())
			{
				$contract = new rental_contract($this->unmarshal($this->db->f('id', true), 'string'));

				$date_start =  date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], strtotime($this->unmarshal($this->db->f('date_start', true), 'date')));
	     	$date_end = date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], strtotime($this->unmarshal($this->db->f('date_end', true), 'date')));

				$contract->set_contract_date(new rental_contract_date($date_start, $date_end));

				// TODO: include party here whenever that db table is ready
				//$contract->set_party($party)

				$contracts[] = $contract;
			}
		}

		return $contracts;

		return array(
			'total_records' => $total_records,
			'results'		=> $results
		);
	}

	/**
	 * Update the database values for an existing composite object. Also updates associated rental units.
	 *
	 * @param $composite the composite to be updated
	 * @return result receipt from the db operation
	 */
	function update($composite)
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
			'is_active = \'' . ($composite->is_active() ? 'true' : 'false') . '\''
		);

		$this->db->query('UPDATE ' . $this->table_name . ' SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);
		$receipt['id'] = $id;
		$receipt['message'][] = array('msg'=>lang('Entity %1 has been updated', $entry['id']));

		$current_units = $this->get_included_rental_units($composite->get_id());

		// Add rental units from the composite object that aren't in the database
		foreach ($composite->get_units() as $unit) {
			$has_unit = false;
			foreach ($current_units as $current_unit) {
				if ($unit->get_location()->get_location_code() == $current_unit->get_location()->get_location_code()) {
					// This unit from the composite was found in the db
					$has_unit = true;
				}
			}
			if (!$has_unit) {
				$this->add_unit($composite->get_id(), $unit->get_location_id(), $unit->get_location_code());
			}
		}

		$current_units = $this->get_included_rental_units($composite->get_id());

		// Remove rental units that are in the database but have been removed from the composite object
		foreach ($current_units as $current_unit) {
			$unit_is_removed = true;
			foreach ($composite->get_units() as $unit) {
				if ($current_unit->get_location()->get_location_id() == $unit->get_location()->get_location_id()) {
					// This unit from the db was not found on the current composite
					$unit_is_removed = false;
				}
			}

			if ($unit_is_removed) {
				$this->remove_unit($composite->get_id(), $unit->get_location_id());
			}
		}

		return $receipt;
	}

	function get_orphan_rental_units($start = 0, $limit = 25, $sort_field = 'location_code', $sort_ascending = true)
	{
		$unit_array = array();

		$sql = "SELECT *
							FROM fm_locations
							LEFT JOIN rental_unit ON
								(fm_locations.id = rental_unit.location_id)
							LEFT JOIN fm_location1 ON
								(fm_locations.location_code = fm_location1.location_code AND fm_locations.level = 1)
							LEFT JOIN fm_location2 ON
								(fm_locations.location_code = fm_location2.location_code AND fm_locations.level = 2)
							LEFT JOIN fm_location3 ON
								(fm_locations.location_code = fm_location3.location_code AND fm_locations.level = 3)
							LEFT JOIN fm_location4 ON
								(fm_locations.location_code = fm_location4.location_code AND fm_locations.level = 4)
							LEFT JOIN fm_location5 ON
								(fm_locations.location_code = fm_location5.location_code AND fm_locations.level = 5)
							WHERE rental_unit.composite_id IS NULL";

		$this->db->limit_query($sql, $start, __LINE__, __FILE__, $limit);

		while ($this->db->next_record()) {
			// Create new rental_unit on correct level for each returned row
			$level = $this->unmarshal($this->db->f('level', true), 'int');
			$class = self::$unit_class_array[$level];
			$unit = new $class($this->unmarshal($this->db->f('location_code', true), 'string'), $this->unmarshal($this->db->f('location_id', true), 'string'));
			$unit->set_address($this->unmarshal($this->db->f($address_column, true), 'string'));
			switch ($level)
			{
				case 5:
					$unit->set_room_name($this->unmarshal($this->db->f('loc5_name', true), 'string'));
				case 4:
					$unit->set_section_name($this->unmarshal($this->db->f('loc4_name', true), 'string'));
				case 3:
					$unit->set_floor_name($this->unmarshal($this->db->f('loc3_name', true), 'string'));
				case 2:
					$unit->set_building_name($this->unmarshal($this->db->f('loc2_name', true), 'string'));
				case 1:
					$unit->set_property_name($this->unmarshal($this->db->f('loc1_name', true), 'string'));
					$unit->set_location_code_property($this->unmarshal($this->db->f('loc1', true), 'string'));
					break;
			}
			$unit_array[] = $unit;
		}

		return $unit_array;
	}

	function get_orphan_rental_unit_count()
	{
		$sql = "SELECT COUNT(id) as rowcount
							FROM fm_locations
							LEFT JOIN rental_unit ON
								(fm_locations.id = rental_unit.location_id)
							LEFT JOIN fm_location1 ON
								(fm_locations.location_code = fm_location1.location_code AND fm_locations.level = 1)
							LEFT JOIN fm_location2 ON
								(fm_locations.location_code = fm_location2.location_code AND fm_locations.level = 2)
							LEFT JOIN fm_location3 ON
								(fm_locations.location_code = fm_location3.location_code AND fm_locations.level = 3)
							LEFT JOIN fm_location4 ON
								(fm_locations.location_code = fm_location4.location_code AND fm_locations.level = 4)
							LEFT JOIN fm_location5 ON
								(fm_locations.location_code = fm_location5.location_code AND fm_locations.level = 5)
							WHERE rental_unit.composite_id IS NULL";
		$this->db->limit_query($sql, $start, __LINE__, __FILE__, $limit);
		$this->db->query($sql);
		$this->db->next_record();
		return $this->unmarshal($this->db->f('rowcount', true), 'int');
	}

	/**
	 * Add a new composite to the database.  Adds the new insert id to the object reference.
	 * Also saves included rental_unit objects.
	 *
	 * @param $composite the composite to be added
	 * @return result receipt from the db operation
	 */
	function add(&$composite)
	{
		// Build a db-friendly array of the composite object
		$cols = array('name', 'description', 'has_custom_address', 'address_1', 'address_2', 'house_number', 'postcode', 'place');
		$values = array(
			"'".$composite->get_name()."'",
			"'".$composite->get_description()."'",
			($composite->has_custom_address() ? "true" : "false"),
			"'".$composite->get_custom_address_1()."'",
			"'".$composite->get_custom_address_2()."'",
			"'".$composite->get_custom_house_number()."'",
			"'".$composite->get_custom_postcode()."'",
			"'".$composite->get_custom_place()."'"
		);

		$q ="INSERT INTO ".$this->table_name."(" . join(',', $cols) . ") VALUES (" . join(',', $values) . ")";
		$result = $this->db->query($q);
		$receipt['id'] = $this->db->get_last_insert_id($this->table_name, 'id');

		$composite->set_id($receipt['id']);

		// Add rental units from the composite object
		foreach ($composite->get_units() as $unit) {
			$this->add_unit($composite->get_id(), $unit->get_location_id(), $unit->get_location_code());
		}

		return $receipt;
	}

	function add_unit($composite_id, $location_id, $loc1)
	{
		$q = "INSERT INTO rental_unit (composite_id, location_id, loc1) VALUES ($composite_id, $location_id, '$loc1')";
		$result = $this->db->query($q);
	}

	function remove_unit($composite_id, $location_id)
	{
		$q = "DELETE FROM rental_unit WHERE composite_id = $composite_id AND location_id = $location_id";
		$result = $this->db->query($q);
	}
}
?>
