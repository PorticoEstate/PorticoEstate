<?php
phpgw::import_class('rental.socommon');
phpgw::import_class('rental.uicommon');

include_class('rental', 'composite', 'inc/model/');
include_class('rental', 'property', 'inc/model/');
include_class('rental', 'building', 'inc/model/');
include_class('rental', 'floor', 'inc/model/');
include_class('rental', 'section', 'inc/model/');
include_class('rental', 'room', 'inc/model/');
include_class('rental', 'contract_date', 'inc/model/');
include_class('rental', 'contract', 'inc/model/');

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
					$like_clauses[] = "fm_location1.adresse1 $this->like $like_pattern";
					$like_clauses[] = "rental_composite.address_1 $this->like $like_pattern";
					break;
				case "gab":
					$like_pattern = str_replace('/','',$like_pattern);
					$like_clauses[] = "fm_gab_location.gab_id $this->like $like_pattern";
					break;
				case "ident":
					$like_pattern = str_replace('/','',$like_pattern);
					$like_clauses[] = "substring(fm_gab_location.gab_id from 5 for 9) $this->like $like_pattern";
					break;
				case "property_id":
					$like_clauses[] = "fm_locations.location_code = $like_pattern";
				case "all":
					$like_clauses[] = "rental_composite.name $this->like $like_pattern";
					$like_clauses[] = "fm_location1.adresse1 $this->like $like_pattern";
					$like_clauses[] = "rental_composite.address_1 $this->like $like_pattern";
					$like_clauses[] = "fm_gab_location.gab_id $this->like $like_pattern";
					$like_pattern = str_replace('/','',$like_pattern);
					$like_clauses[] = "substring(fm_gab_location.gab_id from 5 for 9) $this->like $like_pattern";
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
		
		$tables = "rental_composite";
		$joins = 'LEFT JOIN rental_unit ON (rental_composite.id = rental_unit.composite_id) LEFT JOIN fm_location1 ON (rental_unit.loc1 = fm_location1.loc1) LEFT JOIN fm_gab_location ON (rental_unit.loc1 = fm_gab_location.loc1) LEFT JOIN fm_locations ON (rental_unit.location_id = fm_locations.id)';
		$distinct = 'distinct on(rental_composite.id)';
		$cols = 'rental_composite.id, rental_composite.name, rental_composite.has_custom_address, rental_composite.address_1, rental_composite.house_number, fm_location1.adresse1, fm_gab_location.gab_id';
		
		// Calculate total number of records
		$this->db->query("SELECT COUNT(distinct rental_composite.id) AS count FROM $tables $joins WHERE $condition", __LINE__, __FILE__);
		$this->db->next_record();
		$total_records = (int)$this->db->f('count');

		$order = $sort ? "ORDER BY $sort $dir ": '';
		
		// We interpret 'Eiendomsnavn' as the name of the composite object and not loc1_name or loc2_name. TODO: Is this okay?
		// TODO: Should we ask for and let the address field on fm_location2 override the address found fm_location1? Do we know that the nothing higher than level 2 locations are rented? (The same question goes for the name of the location if we are to use it.)
		// XXX: The address ordering doesn't take custom addresses in consideration.
		
		if($order != '') // ORDER should be used
		{
			// We get a 'ERROR: SELECT DISTINCT ON expressions must match initial ORDER BY expressions' if we don't wrap the ORDER query.
			$this->db->limit_query("SELECT * FROM (SELECT $distinct $cols FROM $tables $joins WHERE $condition) AS result $order", $start, __LINE__, __FILE__, $limit);
		}
		else
		{
			$this->db->limit_query("SELECT $distinct $cols FROM $tables $joins WHERE $condition", $start, __LINE__, __FILE__, $limit);
		}
		
		$results = array();
		
		while ($this->db->next_record())
		{
			$row = array();
			foreach($this->fields as $field => $fparams)
			{
      	$row[$field] = $this->unmarshal($this->db->f($field, true), $params['type']);
			}
			if($row['has_custom_address'] == '1') // There's a custom address
			{
				$row['adresse1'] = $row['address_1'].' '.$row['house_number'];
			}
			if($row['name'] == null || trim($row['name']) == '') // Composite doesn't have a name
			{
				$row['name'] = lang('rental_rc_no_name_composite', $row['id']);
			}
			$row['gab_id'] = rental_uicommon::get_nicely_formatted_gab_id($row['gab_id']);
			$results[] = $row;
		}
		
		$composites = array();
		
		// Go through each returned row and create composite objects
		foreach ($results as $row) {
			$composite = new rental_composite();
			
			$composite->set_id($row['id']);
			$composite->set_description($row['description']);
			$composite->set_is_active($row['is_active']);
			$composite->set_name($row['name']);
			$composite->set_has_custom_address($row['has_custom_address']);

			$composite->set_address_1($row['adresse1']);
			$composite->set_address_2($row['adresse2']);
			$composite->set_house_number($row['house_number']);
			$composite->set_postcode($row['postnummer']);
			$composite->set_place($row['poststed']);
			
			$composite->set_custom_address_1($row['address_1']);
			$composite->set_custom_address_2($row['address_2']);
			$composite->set_custom_house_number($row['house_number']);
			$composite->set_custom_postcode($row['postcode']);
			$composite->set_custom_place($row['place']);
						
			$composite->set_gab_id($row['gab_id']);

			$composites[] = $composite;
		}
		
		return $composites;
	}

	/**
	 * Get single rental composite
	 * 
	 * @param	$id	id of the rental composite to return
	 * @return a rental_composite
	 */
	function get_single($id)
	{
		$id = (int)$id;
		
		// First we get all the data we have about the composite
		// We only ask for one row because we're only using the address data from the first area (something like an educated guess of the address and gab code)
		// TODO: Is it safe for us to use LEFT JOIN like this? (There have been examples of location codes missing in the gab table)
		$sql = "SELECT distinct(rental_composite.id), name, description, has_custom_address, address_1, house_number, is_active, postcode, place, fm_locations.location_code, level, adresse1, adresse2, postnummer, poststed, gab_id FROM {$this->table_name} LEFT JOIN rental_unit ON (rental_composite.id = rental_unit.composite_id) LEFT JOIN fm_locations ON (rental_unit.location_id = fm_locations.id) LEFT JOIN fm_location1 ON (rental_unit.loc1 = fm_location1.location_code) LEFT JOIN fm_gab_location ON (fm_locations.location_code = fm_gab_location.location_code) WHERE rental_composite.id={$id}";
		$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
		
		// Return array
		$row = array();
		
		// Traverse single record in result set and add actual value to fields
		$this->db->next_record();
		foreach($this->fields as $field => $fparams)
		{
     		$row[$field] = $this->unmarshal($this->db->f($field, true), $fparams['type']);
		}
		$row['gab_id'] = rental_uicommon::get_nicely_formatted_gab_id($row['gab_id']);
		
		// Second we find all areas that belongs to composite
		$this->db->query("SELECT level, location_code FROM fm_locations {$this->join} rental_unit ON (fm_locations.id = rental_unit.location_id) WHERE composite_id = {$id}");
		// ..and store them in an array
		$units = array();
		while ($this->db->next_record()) {
			$level = $this->unmarshal($this->db->f('level', true), 'int');
			$location_code = $this->unmarshal($this->db->f('location_code', true), 'string');
			$units[] = array('level' => $level, 'location_code' => $location_code);
		}
		
		//Accumulated areas
		$area_gros = 0;
		$area_net = 0;
		
		foreach ($units as $unit) // Goes through each rental unit (location) that belongs to this composite and add up their areas
		{
			// Column names mostly used for the areas:
			$area_column_gros = 'bta';
			$area_column_net = 'bra';
			
			// ... properties doesn't have areas, so we check location level 2 to work out the areas of whole properties (level 1)
			if ($unit['level'] == 1)
			{
				$sql = "SELECT {$area_column_gros}, {$area_column_net} FROM fm_location2 WHERE fm_location2.loc1 = '{$unit['location_code']}'";
			} 
			else // ... not level 1
			{
				// ... on level 5 the area columns have different names..
				if ($unit['level'] == 5)
				{
					$area_column_gros = 'bruksareal';
					$area_column_net = 'bruttoareal';
				}
				$sql = "SELECT {$area_column_gros}, {$area_column_net} FROM fm_location{$unit['level']} WHERE fm_location{$unit['level']}.location_code = '{$unit['location_code']}'";
			}

			$this->db->query($sql);
			while($this->db->next_record())
			{
				$area_gros += $this->unmarshal($this->db->f($area_column_gros, true), 'float');
				$area_net += $this->unmarshal($this->db->f($area_column_net, true), 'float');
			}
		} // end foreach
		
		$row['area_gros'] = $area_gros;
		$row['area_net'] = $area_net;
		
		$composite = new rental_composite();
			
		$composite->set_id($row['id']);
		$composite->set_description($row['description']);
		$composite->set_is_active($row['is_active']);
		$composite->set_name($row['name']);
		$composite->set_has_custom_address($row['has_custom_address']);

		$composite->set_address_1($row['adresse1']);
		$composite->set_address_2($row['adresse2']);
		$composite->set_house_number($row['house_number']);
		$composite->set_postcode($row['postnummer']);
		$composite->set_place($row['poststed']);
		
		$composite->set_custom_address_1($row['address_1']);
		$composite->set_custom_address_2($row['address_2']);
		$composite->set_custom_house_number($row['house_number']);
		$composite->set_custom_postcode($row['postcode']);
		$composite->set_custom_place($row['place']);
					
		$composite->set_gab_id($row['gab_id']);
		
		$composite->set_area_gros($row['area_gros']);
		$composite->set_area_net($row['area_net']);
		
		return $composite;
	}
	
	/**
	 * Gets all areas that have been added to a composite
	 * 
	 * @param	params	array( (id=?) AND ordering information )
	 * @return	rows	array( (fieldname=fieldvalue) AND accumulated areas AND total number of included areas)
	 */
	function get_included_rental_units($id, $sort = null, $dir = 'asc', $start = 0, $results = null)
	{
		// TODO: Do we need a paginator for all the units?
		$id = (int)$id;
		
		//Return array
		$units = array();
		
		// First we find the number of areas available in total
		$sql = 'SELECT COUNT(fm_locations.location_code) AS count FROM fm_locations JOIN rental_unit ON (fm_locations.id = rental_unit.location_id) JOIN fm_location1 ON (rental_unit.loc1 = fm_location1.location_code) WHERE composite_id ='.$id;
		$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
		
		$order = '';
		if($sort != null && $sort != '') // We should ask for a ordered resultset 
		{
			$order = ' ORDER BY '.$sort.' '.$dir;
		}
		// Second we get ids for all areas for specified composite id
		$sql = 'SELECT level, fm_locations.location_code, fm_locations.id AS location_id FROM fm_locations JOIN rental_unit ON (fm_locations.id = rental_unit.location_id) JOIN fm_location1 ON (rental_unit.loc1 = fm_location1.location_code) WHERE composite_id ='.$id.$order;
		$this->db->query($sql, __LINE__, __FILE__);
		
		$unit_array = array();
		while ($this->db->next_record())
		{
			$level = $this->unmarshal($this->db->f('level', true), 'int');
			$location_code = $this->unmarshal($this->db->f('location_code', true), 'string');
			$location_id = $this->unmarshal($this->db->f('location_id', true), 'int');
			$unit_array[] = array('level' => $level, 'location_code' => $location_code, 'location_id' => $location_id);
		}
		
		// Go through each rental unit (location) that belongs to this composite and extract as much data as possible
		foreach ($unit_array as $unit)
		{
			$sql = '';
			$area_column_gros = 'bta';
			$area_column_net = 'bra';
			$address_column = 'adresse';
		
			// ... properties doesn't have areas, so we check location level 2 to work out the areas of whole properties (level 1)
			if ($unit['level'] == 1)
			{
				$address_column = 'adresse1';
				$sql = "SELECT loc1_name, loc2_name, {$address_column}, name, {$area_column_gros}, {$area_column_net} FROM fm_location2 JOIN fm_location1 ON (fm_location2.loc1 = fm_location1.loc1) JOIN fm_part_of_town ON (fm_location1.part_of_town_id = fm_part_of_town.part_of_town_id) WHERE fm_location2.loc1 = '{$unit['location_code']}'";
			} 
			else // ... not level 1
			{
				// ... on level 5 the area columns have different names..
				if ($unit['level'] == 5)
				{
					$area_column_gros = 'bruksareal';
					$area_column_net = 'bruttoareal';
				}
				$names_to_look_for_array = array(); // .. which location names to ask for (loc1_name, loc2_name, etc)
				$joins = array(); // ... which tables to join (fm_location1, fm_location2, etc)
				for($i = $unit['level']; $i > 0 ; $i--) // ... runs from current level to level 1
				{
					$names_to_look_for_array[] = 'loc'.$i.'_name';
					if($i != $unit['level'])
					{
						// ... we join all tables from fm_location[level] to fm_location1 to get as much info about the area as we can
						$join = "JOIN fm_location{$i} ON (fm_location".($i + 1).".loc{$i} = fm_location{$i}.loc{$i}";
						$condition_array = array();
						for($j = ($i - 1); $j > 0; $j--)
						{
							$condition_array[] = 'AND fm_location'.$unit['level'].'.loc'.$j.' = fm_location'.$i.'.loc'.$j;
						}
						$join .= ' '.implode (' ', $condition_array);
						$join .= ')';
						$joins[] = $join; 
					} 
				}
				$sql = 'SELECT '.implode(', ', $names_to_look_for_array).", {$address_column}, name, fm_location{$unit['level']}.{$area_column_gros}, fm_location{$unit['level']}.{$area_column_net} FROM fm_location{$unit['level']} ".implode(' ', $joins)." JOIN fm_part_of_town ON (fm_location1.part_of_town_id = fm_part_of_town.part_of_town_id) WHERE fm_location{$unit['level']}.location_code = '{$unit['location_code']}'";
			}

			$area_gros = 0;
			$area_net = 0;
			
			$this->db->query($sql);
			while($this->db->next_record())
			{
				// Create new rental_unit on correct level for each returned row
				$class = self::$unit_class_array[$unit['level']];
				$rental_unit = new $class($unit['location_code'], $unit['location_id']);
				
				$area_gros += $this->unmarshal($this->db->f($area_column_gros, true), 'float');
				$area_net += $this->unmarshal($this->db->f($area_column_net, true), 'float');

				$rental_unit->set_address($this->unmarshal($this->db->f($address_column, true), 'string'));
				
				$rental_unit->set_area_gros((int)$area_gros);
				$rental_unit->set_area_net((int)$area_net);
				
				switch($unit['level'])
				{
					case 5:
						$rental_unit->set_room_name($unit['loc5_name']);
					case 4:
						$rental_unit->set_section_name($unit['loc4_name']);
					case 3:
						$rental_unit->set_floor_name($unit['loc3_name']);
					case 2:
						$rental_unit->set_building_name($unit['loc2_name']);
					case 1:
						$rental_unit->set_property_name($unit['loc1_name']);
						$rental_unit->set_location_code_property($unit_row['loc1']);
						break;
				}
			}
			
			$units[] = $rental_unit;
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
	public function get_unit_array($level = 2, string $location_code_related = null, $start = 0, $num_of_hits = 10000, $sort_field = 'location_code', $sort_ascending = true)
	{
		// Return array
		$unit_array = array();
		// Level
		if(!is_int($level) || $level < rental_socommon::UNIT_PROPERTY || $level > rental_socommon::UNIT_ROOM) // Invalid (or not set) level
		{
			$level = rental_socommon::UNIT_BUILDING; // Default level
		}
		// Address
		$address_column = $level == rental_socommon::UNIT_PROPERTY ? 'adresse1' : 'adresse';
		// Conditions
		$condition_array = array();
		// Location code
		if($location_code_related != null) // Location code set - should only look for units in relation to this one
		{
			$location_code_related_array = explode('-', $location_code_related);
			for($i = 1; $i <= $level; $i++)
			{
				if(array_key_exists($i - 1, $location_code_related_array))
				{
					$condition_array[] = "fm_location{$level}.loc{$i} = '{$location_code_related_array[$i - 1]}'";
				}
			}
		}
		// Table
		$table = "fm_location{$level}";
		// Columns
		$cols = "fm_location{$level}.location_code, {$address_column}, fm_locations.id AS location_id, fm_location1.loc1";
		for($i = 1; $i <= $level; $i++)
		{
			$cols .= ", fm_location{$i}.loc{$i}, fm_location{$i}.loc{$i}_name";
		}
		$condition = "";
		if(count($condition_array) > 0)
		{
			$condition = ' WHERE 1 = 1';
			foreach($condition_array as $current_condition)
			{
				$condition .= ' AND '.$current_condition;
			}
		}
		// Joins
		$joins = '';
		for($i = 1; $i < $level; $i++)
		{
			$joins .= " LEFT JOIN fm_location{$i} ON (fm_location{$level}.loc1 = fm_location{$i}.loc1";
			for($j = 2; $j <= $i; $j++)
			{
				$joins .= ' AND fm_location'.($level).'.loc'.($j - 0).' = fm_location'.$i.'.loc'.($j - 0);
			}
			$joins .= ')';
		}
		$joins .= ' LEFT JOIN fm_locations ON (fm_location'.$level.'.location_code = fm_locations.location_code)';
		// Order
		$order = '';
		if(isset($sort_field))
		{
			switch($sort_field)
			{
				case 'location_code':
				default:
					$sort_field = "fm_location{$level}.location_code";
					break;
			}
			$order = 'ORDER BY '.$sort_field.($sort_ascending ? ' ASC' : ' DESC');
		}
		// Class to use for the units
		$class = self::$unit_class_array[$level]; // Picks the correct class to instanciate
		$sql = "SELECT $cols FROM $table $joins $condition $order";
//		var_dump($sql);
		$this->db->limit_query($sql, $start, __LINE__, __FILE__, $num_of_hits);
		while ($this->db->next_record()) {
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
		// Go through each rental unit (location) that belongs to this composite and add up their areas
		foreach ($unit_array as &$unit)
		{
			// TODO: Simplify this block:
			$sql = '';
			$area_column_gros = 'bta';
			$area_column_net = 'bra';
			// ... properties doesn't have areas, so we check location level 2 to work out the areas of whole properties (level 1)
			if ($level == 1)
			{
				$sql = "SELECT {$area_column_gros}, {$area_column_net} FROM fm_location2 JOIN fm_location1 ON (fm_location2.loc1 = fm_location1.loc1) WHERE fm_location2.loc1 = '{$unit->get_location_code()}'";
			} 
			else // ... not level 1
			{
				// ... on level 5 the area columns have different names..
				if ($level == 5)
				{
					$area_column_gros = 'bruksareal';
					$area_column_net = 'bruttoareal';
				}
				$sql = "SELECT {$area_column_gros}, {$area_column_net} FROM fm_location{$level} WHERE fm_location{$level}.location_code = '{$unit->get_location_code()}'";
			}
			
			$area_gros = $area_net = 0;
			$this->db->query($sql);
			while($this->db->next_record())
			{
				$area_gros += $this->unmarshal($this->db->f($area_column_gros, true), 'float');
				$area_net += $this->unmarshal($this->db->f($area_column_net, true), 'float');
			}
			$unit->set_area_gros((int)$area_gros);
			$unit->set_area_net((int)$area_net);  
		}
		// Finds belonging contracts
		foreach ($unit_array as &$unit)
		{
			// Belonging contracts
			$sql = "select DISTINCT rental_contract.id, date_start, date_end from rental_contract join rental_contract_composite on (rental_contract.id = rental_contract_composite.contract_id) JOIN rental_composite on (rental_contract_composite.composite_id = rental_composite.id) join rental_unit on (rental_composite.id = rental_unit.composite_id) join fm_locations on (rental_unit.location_id = fm_locations.id) join fm_location{$level} on (fm_locations.location_code = fm_location{$level}.location_code) where fm_location{$level}.location_code = '{$unit->get_location_code()}'";
//			var_dump($sql);
			$this->db->query($sql);
//			$counter = 0;
			while($this->db->next_record())
			{
//				var_dump(++$counter);
				$unit->add_contract_date(new rental_contract_date($this->unmarshal($this->db->f('date_start', true), 'string'), $this->unmarshal($this->db->f('date_end', true), 'string')));
			}
//			var_dump($unit);
		}

		// Finds belonging composites
		foreach ($unit_array as &$unit)
		{
			// Belonging contracts
			$sql = "select DISTINCT rental_composite.id FROM rental_composite JOIN rental_unit ON (rental_composite.id = rental_unit.composite_id) JOIN fm_locations ON (rental_unit.location_id = fm_locations.id) JOIN fm_location{$level} on (fm_locations.location_code = fm_location{$level}.location_code) where fm_location{$level}.location_code = '{$unit->get_location_code()}'";
//			var_dump($sql);

			$this->db->query($sql);
			while($this->db->next_record())
			{
				$unit->add_composite_id($this->unmarshal($this->db->f('id', true), 'float'));
			}
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
				
				// TODO: include tenant here whenever that db table is ready
				//$contract->set_tenant($tenant)
				
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
		$cols = array();
		$values = array();
		
		$values = array(
			'name = \'' . $composite->get_name() . '\'',
			'description = \'' . $composite->get_description() . '\'',
			'has_custom_address = ' . ($composite->has_custom_address() ? "true" : "false"),
			'address_1 = \'' . $composite->get_address_1() . '\'',
			'address_2 = \'' . $composite->get_address_2() . '\'',
			'house_number = \'' . $composite->get_house_number() . '\'',
			'postcode = \'' . $composite->get_postcode() . '\'',
			'place = \'' . $composite->get_place() . '\''
		);
				
		$cols = join(',', $cols);
		$this->db->query('UPDATE ' . $this->table_name . ' SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);
		
		$receipt['id'] = $id;
		$receipt['message'][] = array('msg'=>lang('Entity %1 has been updated', $entry['id']));
		
		$current_units = $this->get_included_rental_units($composite->get_id());
		
		// Add rental units from the composite object that aren't in the database
		foreach ($composite->get_included_rental_units() as $unit) {
			$has_unit = false;
			foreach ($current_units as $current_unit) {
				if ($unit->get_location_id() == $current_unit->get_location_id()) {
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
			foreach ($composite->get_included_rental_units() as $unit) {
				if ($current_unit->get_location_id() == $unit->get_location_id()) {
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
		$values = array(
			'name = \'' . $composite->get_name() . '\'',
			'description = \'' . $composite->get_description() . '\'',
			'has_custom_address = ' . ($composite->has_custom_address() ? "true" : "false"),
			'address_1 = \'' . $composite->get_address_1() . '\'',
			'address_2 = \'' . $composite->get_address_2() . '\'',
			'house_number = \'' . $composite->get_house_number() . '\'',
			'postcode = \'' . $composite->get_postcode() . '\'',
			'place = \'' . $composite->get_place() . '\''
		);
		
		$q ="INSERT INTO ".$this->table_name." (name) VALUES ('$values')";
		$result = $this->db->query($q);
		$receipt['id'] = $this->db->get_last_insert_id($this->table_name, 'id');
		
		$composite->set_id($receipt['id']);
		
		// Add rental units from the composite object
		foreach ($composite->get_included_rental_units() as $unit) {
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
