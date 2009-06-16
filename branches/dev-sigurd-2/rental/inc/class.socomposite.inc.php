<?php
phpgw::import_class('rental.socommon');
phpgw::import_class('rental.uicommon');

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
	 * We override the parent method to hook in more specialized queries for
	 * this part of the system. (The DISTINCT JOIN and FROM handling in the common class
	 * isn't as advanced as needed here.)
	 * 
	 * Return all entries matching $params. Valid parameters:
	 *
	 * - $params['start']: Search result offset
	 * - $params['results']: Number of results to return
	 * - $params['sort']: Field to sort by
	 * - $params['query']: LIKE-based query string
	 * - $params['filters']: Array of custom filters
	 *
	 * @return array('total_records'=>X, 'results'=array(...))
	 */
	function read($params)
	{
		$start = isset($params['start']) && $params['start'] ? $params['start'] : 0;
		$results = isset($params['results']) && $params['results'] ? $data['results'] : 1000;
		$sort = isset($params['sort']) && $params['sort'] ? $params['sort'] : null;
		$dir = isset($params['dir']) && $params['dir'] ? $params['dir'] : '';
		$query = isset($params['query']) && $params['query'] ? $params['query'] : null;
		$search_option = isset($params['search_option']) && $params['search_option'] ? $params['search_option'] : null;
		$filters = isset($params['filters']) && $params['filters'] ? $params['filters'] : array();

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
		return array(
			'total_records' => $total_records,
			'results'		=> $results
		);
	}

	
	/**
	 * Read single rental composite record
	 * 
	 * @param	params	array( (id=?) AND ordering information )
	 * @return	rows	array( (fieldname=fieldvalue) AND accumulated areas AND total number of included areas)
	 */
	function read_single($params)
	{
		$id = (int)$params['id'];
		// First we get all the data we have about the composite
		// We only ask for one row because we're only using the address data from the first area (something like an educated guess of the address and gab code)
		// TODO: Is it safe for us to use LEFT JOIN like this? (There have been examples of location codes missing in the gab table)
		$sql = "SELECT distinct(rental_composite.id), name, description, has_custom_address, address_1, house_number, is_active, postcode, place, fm_locations.location_code, level, adresse1, adresse2, postnummer, poststed, gab_id FROM {$this->table_name} LEFT JOIN rental_unit ON (rental_composite.id = rental_unit.composite_id) LEFT JOIN fm_locations ON (rental_unit.location_id = fm_locations.id) LEFT JOIN fm_location1 ON (rental_unit.loc1 = fm_location1.location_code) LEFT JOIN fm_gab_location ON (fm_locations.location_code = fm_gab_location.location_code) WHERE rental_composite.id={$id}";
		$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
//		die($sql);
		
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
		
		return $row;
	}
	
	/**
	 * Gets all areas that have been added to a composite
	 * 
	 * @param	params	array( (id=?) AND ordering information )
	 * @return	rows	array( (fieldname=fieldvalue) AND accumulated areas AND total number of included areas)
	 */
	function get_included_rental_units($params)
	{
		// TODO: Do we need a paginator for all the units?
		$id = (int)$params['id'];
		$sort = isset($params['sort']) && $params['sort'] ? $params['sort'] : null;
		$dir = isset($params['dir']) && $params['dir'] ? ($params['dir'] == 'desc' ? 'desc' : 'asc') : 'asc'; // We set asc as direction unless specifically told otherwise
		
		//Return array
		$row = array();
		$row['results'] = array();
		
		// First we find the number of areas available in total
		$sql = 'SELECT COUNT(fm_locations.location_code) AS count FROM fm_locations JOIN rental_unit ON (fm_locations.id = rental_unit.location_id) JOIN fm_location1 ON (rental_unit.loc1 = fm_location1.location_code) WHERE composite_id ='.$id;
		$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
		if($this->db->next_record())
		{
			$row['total_records'] = $this->unmarshal($this->db->f('count', true), 'int');
		}
		
		$order = '';
		if($sort != null && $sort != '') // We should ask for a ordered resultset 
		{
			$order = ' ORDER BY '.$sort.' '.$dir;
		}
		// Second we get ids for all areas for specified composite id
		$sql = 'SELECT level, fm_locations.location_code, fm_locations.id AS location_id FROM fm_locations JOIN rental_unit ON (fm_locations.id = rental_unit.location_id) JOIN fm_location1 ON (rental_unit.loc1 = fm_location1.location_code) WHERE composite_id ='.$id.$order;
		$this->db->query($sql, __LINE__, __FILE__);
//		die($sql);
		
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
			$current_unit = &$row['results'][]; //..  
			$current_unit['location_code'] = $unit['location_code'];
			$current_unit['location_id'] = $unit['location_id'];
			$current_unit['loc1_name'] = lang(rental_rc_area_not_found);
			
			
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
				$area_gros += $this->unmarshal($this->db->f($area_column_gros, true), 'float');
				$area_net += $this->unmarshal($this->db->f($area_column_net, true), 'float');
				$current_unit['area_gros'] = (int)$area_gros; 
				$current_unit['area_net'] = (int)$area_net; 
				for($i = 1; $i <= $unit['level'] && $i <= 3; $i++) // Runs through all levels containing names
				{
					$current_unit['loc'.$i.'_name'] = $this->unmarshal($this->db->f('loc'.$i.'_name', true), 'string');
				}
				$current_unit['address'] = $this->unmarshal($this->db->f($address_column, true), 'string');
				$current_unit['part_of_town'] = $this->unmarshal($this->db->f('name', true), 'string');
			}
		}
		
		return $row;
	}
	
	/**
	 * 
	 * @param $params
	 * @return unknown_type
	 */
	function get_available_rental_units($params)
	{
		$id = $params['id'];
		$level = (int)$params['level'];
		$start = isset($params['start']) && $params['start'] ? $params['start'] : 0;
		$limit = isset($params['results']) && $params['results'] ? $params['results'] : 25;
		$sort = isset($params['sort']) && $params['sort'] ? $params['sort'] : null;
		$dir = isset($params['dir']) && $params['dir'] ? ($params['dir'] == 'desc' ? 'desc' : 'asc') : 'asc'; // We set asc as direction unless specifically told otherwise
		// Table
		$table = 'fm_location1';
		// Level
		if($level < 1 || $level > 5) // Invalid (or not set) level
		{
			$level = 1; // Default level
		}
		// Address
		$address_column = $level == 1 ? 'adresse1' : 'adresse';
		// Columns
		$cols = 'rental_contract.id, rental_contract.date_start, rental_contract.date_end, fm_location'.$level.'.location_code, loc1_name, fm_locations.id AS location_id, '.$address_column;
		for($i = 2; $i <= $level; $i++)
		{
			$cols .= ", fm_location{$i}.loc{$i}, loc{$i}_name";
		}
		// Joins
		$joins = '';
		for($i = 2; $i <= $level; $i++)
		{
			$joins .= " LEFT JOIN fm_location{$i} ON (fm_location1.loc1 = fm_location{$i}.loc1";
			for($j = 3; $j <= $i; $j++)
			{
				$joins .= ' AND fm_location'.($j - 1).'.loc'.($j - 1).' = fm_location'.$i.'.loc'.($j - 1);
			}
			$joins .= ')';
		}
		$joins .= ' LEFT JOIN fm_locations ON (fm_location1.loc1 = fm_locations.location_code)'
			.' LEFT JOIN rental_unit ON (fm_locations.id = rental_unit.location_id)'
			.' LEFT JOIN rental_composite ON (rental_unit.composite_id = rental_composite.id)'
			.' LEFT JOIN rental_contract_composite ON (rental_composite.id = rental_contract_composite.composite_id)'
			.' LEFT JOIN rental_contract ON (rental_contract_composite.contract_id = rental_contract.id)';
		// Condition
		$condition = " WHERE fm_location{$level}.location_code != ''";
		$condition .= " AND rental_composite.id != {$id}";
		// Order
		$order = ' ORDER BY fm_location1.location_code';
		for($i = 2; $i <= $level; $i++) 
		{
			$order .= ", fm_location{$i}.loc{$i}";
		}
		if($sort != null) // Sorting is set
		{
			switch($sort)
			{
				case 'address':
					$order = ' ORDER BY '.$address_column.' '.$dir;
					break;
				case 'loc2_name':
					if($level >= 2)
					{
						$order = ' ORDER BY loc2_name '.$dir;
					}
					break;
				case 'loc3_name':
					if($level >= 3)
					{
						$order = ' ORDER BY loc3_name '.$dir;
					}
					break;
				case 'loc4_name':
					if($level >= 4)
					{
						$order = ' ORDER BY loc4_name '.$dir;	
					}
					break;
				case 'loc5_name':
					if($level >= 5)
					{
						$order = ' ORDER BY loc5_name '.$dir;
					}
					break;
			}
		}
		// Count query
		$total_records = 0;
		$sql = "SELECT COUNT(*) AS count FROM $table $joins $condition";
		$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
		if ($this->db->next_record()) 
		{
			$total_records = $this->unmarshal($this->db->f('count', true), 'string');
		}
		// Main query
		$sql = "SELECT $cols FROM $table $joins $condition $order";
		$this->db->limit_query($sql, $start, __LINE__, __FILE__, $limit);

		$units = array();
		while ($this->db->next_record()) {
			$unit = array();
			$unit['location_code'] = $this->unmarshal($this->db->f('location_code', true), 'string');
			$unit['location_id'] = $this->unmarshal($this->db->f('location_id', true), 'string');
			$unit['loc1_name'] = $this->unmarshal($this->db->f('loc1_name', true), 'string');
			$unit['loc2_name'] = $this->unmarshal($this->db->f('loc2_name', true), 'string');
			$unit['loc3_name'] = $this->unmarshal($this->db->f('loc3_name', true), 'string');
			$unit['loc4_name'] = $this->unmarshal($this->db->f('loc4_name', true), 'string');
			$unit['loc5_name'] = $this->unmarshal($this->db->f('loc5_name', true), 'string');
			$unit['address'] = $this->unmarshal($this->db->f($address_column, true), 'string');
			$units[] = $unit;
		}
		
		// Go through each rental unit (location) that belongs to this composite and add up their areas
		foreach ($units as &$unit)
		{
			// TODO: Simplify this block:
			$sql = '';
			$area_column_gros = 'bta';
			$area_column_net = 'bra';
			// ... properties doesn't have areas, so we check location level 2 to work out the areas of whole properties (level 1)
			if ($level == 1)
			{
				$sql = "SELECT {$area_column_gros}, {$area_column_net} FROM fm_location2 JOIN fm_location1 ON (fm_location2.loc1 = fm_location1.loc1) WHERE fm_location2.loc1 = '{$unit['location_code']}'";
			} 
			else // ... not level 1
			{
				// ... on level 5 the area columns have different names..
				if ($level == 5)
				{
					$area_column_gros = 'bruksareal';
					$area_column_net = 'bruttoareal';
				}
				$sql = "SELECT {$area_column_gros}, {$area_column_net} FROM fm_location{$level} WHERE fm_location{$level}.location_code = '{$unit['location_code']}'";
			}
			
			$area_gros = $area_net = 0;
			$this->db->query($sql);
			while($this->db->next_record())
			{
				$area_gros += $this->unmarshal($this->db->f($area_column_gros, true), 'float');
				$area_net += $this->unmarshal($this->db->f($area_column_net, true), 'float');
			}
			$unit['area_gros'] = (int)$area_gros; 
			$unit['area_net'] = (int)$area_net; 
		}
		
		return array(
			'total_records' => $total_records,
			'results'		=> $units
		);
	}
	
	/**
	 * Returns all contracts for a specified composite.
	 * 
	 * @param $params array with parameters for the query
	 * @return array with 'total_records' and 'results'.
	 */
	public function get_contracts($params)
	{
		// Params
		$id = (int)$params['id'];		
		$start = isset($params['start']) && $params['start'] ? (int)$params['start'] : 0;
		$limit = isset($params['results']) && $params['results'] ? (int)$data['results'] : 1000;
		$sort = isset($params['sort']) && $params['sort'] ? $params['sort'] : null;
		$dir = isset($params['dir']) && $params['dir'] ? $params['dir'] : null;
		$contract_status = isset($params['contract_status']) && $params['contract_status'] ? (int)$params['contract_status'] : null;
		
		// Default return data:
		$total_records = 0;
		$results = array();
		
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
				$row = array();
	     		$row['id'] = $this->unmarshal($this->db->f('id', true), 'string');
	     		$row['date_start'] =  date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], strtotime($this->unmarshal($this->db->f('date_start', true), 'date')));
	     		$row['date_end'] = date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], strtotime($this->unmarshal($this->db->f('date_end', true), 'date')));
	     		$row['tenant'] = ''; // TODO: We have to include tenant here whenever that db table is ready
				$results[] = $row;
			}
		}
		
		return array(
			'total_records' => $total_records,
			'results'		=> $results
		);
	}
	
	function update($entry)
	{
		$id = intval($entry['id']);
		$cols = array();
		$values = array();
		$fields = array('id', 'description', 'is_active', 'name', 'address_1', 'address_2', 'house_number', 'postcode', 'place', 'has_custom_address');
		
		foreach($fields as $field)
		{
			$params = $this->fields[$field];
			
			if($field == 'id' || $params['join'] || $params['manytomany'])
			{
				continue;
			}
			$values[] = $field . "=" . $this->marshal($entry[$field], $params['type']);
		}
		
		$cols = join(',', $cols);
		$this->db->query('UPDATE ' . $this->table_name . ' SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);
		
		$receipt['id'] = $id;
		$receipt['message'][] = array('msg'=>lang('Entity %1 has been updated', $entry['id']));
		return $receipt;
	}
	
	function add($entry)
	{
		$q ="INSERT INTO ".$this->table_name." (name) VALUES ('$entry')";
		$result = $this->db->query($q);
		$receipt['id'] = $this->db->get_last_insert_id($this->table_name, 'id');;
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
