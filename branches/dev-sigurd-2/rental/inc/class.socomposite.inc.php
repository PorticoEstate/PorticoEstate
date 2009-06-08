<?php
phpgw::import_class('rental.socommon');

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
	
	function _get_conditions($query, $filters,$search_option)
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

		$condition = $this->_get_conditions($query, $filters,$search_option);
		
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
                    $row[$field] = $this->_unmarshal($this->db->f($field, true), $params['type']);
			}
			if($row['has_custom_address'] == '1') // There's a custom address
			{
				$row['adresse1'] = $row['address_1'].' '.$row['house_number'];
			}
			$row['gab_id'] = substr($row['gab_id'],4,4).' / '.substr($row['gab_id'],8,4).' / '.substr($row['gab_id'],12,4).' / '.substr($row['gab_id'],16,4);
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
		//Build and execute query to select a single rental cmposite based on id
		$cols = 'rental_composite.id, rental_composite.name, rental_composite.description, rental_composite.has_custom_address, rental_composite.address_1, rental_composite.house_number, rental_composite.is_active, rental_composite.postcode, rental_composite.place, fm_location1.adresse1, fm_location1.adresse2, fm_location1.postnummer, fm_location1.poststed, fm_gab_location.gab_id';
		$distinct = 'distinct on(rental_composite.id)';
		$joins = 'LEFT JOIN rental_unit ON (rental_composite.id = rental_unit.composite_id) LEFT JOIN fm_location1 ON (rental_unit.loc1 = fm_location1.loc1) LEFT JOIN fm_gab_location ON (rental_unit.loc1 = fm_gab_location.loc1) LEFT JOIN fm_locations ON (rental_unit.location_id = fm_locations.id)';
		$id = (int)$params['id'];
		$this->db->query("SELECT $cols FROM {$this->table_name} $joins WHERE rental_composite.id=$id", __LINE__, __FILE__);

		//Return array
		$row = array();
		
		//Traverse single record in result set and add actual value to fields
		$this->db->next_record();
		foreach($this->fields as $field => $fparams)
		{
     		$row[$field] = $this->_unmarshal($this->db->f($field, true), $fparams['type']);
		}
		//... alter gab_id to contain slashes
		$row['gab_id'] = substr($row['gab_id'],4,4).' / '.substr($row['gab_id'],8,4).' / '.substr($row['gab_id'],12,4).' / '.substr($row['gab_id'],16,4);
		$row['results'] = array();
		
		// Execute query to select all rental units belonging to this rental composite. Add values for level and location coce to unit tabe
		$this->db->query("SELECT fm_locations.* FROM rental_unit JOIN fm_locations ON (rental_unit.location_id = fm_locations.id) WHERE composite_id = {$id}");
		$units = array();
		while ($this->db->next_record()) {
			$level = $this->_unmarshal($this->db->f('level', true), 'int');
			$location_code = $this->_unmarshal($this->db->f('location_code', true), 'string');
			$units[] = array('level' => $level, 'location_code' => $location_code);
		}
		
		//Accumulated areas
		$area_gros = 0;
		$area_net = 0;
		
		// Go through each rental unit (location) that belongs to this composite and add up their areas
		foreach ($units as $unit)
		{
			$sql = '';
			$area_column_gros = 'bta';
			$area_column_net = 'bra';
			$address_column = 'adresse';
			$current_unit = &$row['results'][]; //..  
			$current_unit['location_code'] = $unit['location_code'];
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
			// XXX: Roy: Continue here: Implement paging and ordering
			//
			// Areas included ordering
			//
			$area_included_order = '';
			if(is_array($params['area_included']) && isset($params['area_included']['sort']) && $params['area_included']['sort'] != '') // Sort is set
			{
				$area_included_sort_direction = (isset($params['area_included']['sort_direction']) && $params['area_included']['sort_direction'] == 'desc' )? 'desc' : 'asc';
				$sort_field = $params['area_included']['sort'];
				// We have to map some of the columns to the correct database field
				switch ($sort_field)
				{
					case 'address':
						$sort_field = $address_column;
						break;
					case 'area_gros':
						$sort_field = $area_column_gros;
						break;
					case 'area_net':
						$sort_field = $area_column_net;
						break;
					case 'part_of_town':
						$sort_field = 'name';
						break;
				}
				$sql .= ' ORDER BY '.$sort_field.' '.$area_included_sort_direction;
			}
			
			$this->db->query($sql);
			while($this->db->next_record())
			{
				$area_gros += $this->_unmarshal($this->db->f($area_column_gros, true), 'float');
				$area_net += $this->_unmarshal($this->db->f($area_column_net, true), 'float');
				$current_unit['area_gros'] = (int)$area_column_gros; 
				$current_unit['area_net'] = (int)$area_column_net; 
				for($i = 1; $i <= $unit['level'] && $i <= 3; $i++) // Runs through all levels containing names
				{
					$current_unit['loc'.$i.'_name'] = $this->_unmarshal($this->db->f('loc'.$i.'_name', true), 'string');
				}
				$current_unit['address'] = $this->_unmarshal($this->db->f($address_column, true), 'string');
				$current_unit['part_of_town'] = $this->_unmarshal($this->db->f('name', true), 'string');
			}
		}
		$row['area_gros'] = $area_gros;
		$row['area_net'] = $area_net;
		$row['total_records'] = count($row['results']);
		
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
		
		$cols = 'fm_locations.*';
		$tables = 'rental_unit';
		$joins = 'JOIN fm_locations ON (rental_unit.location_id = fm_locations.id)';
		$condition = 'composite_id != '.$id;	
		
		$sql = "SELECT $cols FROM $tables $joins WHERE $condition";
		$this->db->query($sql);
		
		$units = array();
		while ($this->db->next_record()) {
			$level = $this->_unmarshal($this->db->f('level', true), 'int');
			$location_code = $this->_unmarshal($this->db->f('location_code', true), 'string');
			$units[] = array('level' => $level, 'location_code' => $location_code);
		}
		
		// Go through each rental unit (location) that belongs to this composite and add up their areas
		foreach ($units as $unit)
		{
			$sql = '';
			$address_column = 'adresse';
			$current_unit = &$results[];  
			$current_unit['location_code'] = $unit['location_code'];
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
			// XXX: Roy: Continue here: Implement paging and ordering
			//
			// Areas included ordering
			//
			$area_included_order = '';
			if(is_array($params['area_included']) && isset($params['area_included']['sort']) && $params['area_included']['sort'] != '') // Sort is set
			{
				$area_included_sort_direction = (isset($params['area_included']['sort_direction']) && $params['area_included']['sort_direction'] == 'desc' )? 'desc' : 'asc';
				$sort_field = $params['area_included']['sort'];
				// We have to map some of the columns to the correct database field
				switch ($sort_field)
				{
					case 'address':
						$sort_field = $address_column;
						break;
					case 'area_gros':
						$sort_field = $area_column_gros;
						break;
					case 'area_net':
						$sort_field = $area_column_net;
						break;
					case 'part_of_town':
						$sort_field = 'name';
						break;
				}
				$sql .= ' ORDER BY '.$sort_field.' '.$area_included_sort_direction;
			}
			
			$this->db->query($sql);
			while($this->db->next_record())
			{
				$area_gros += $this->_unmarshal($this->db->f($area_column_gros, true), 'float');
				$area_net += $this->_unmarshal($this->db->f($area_column_net, true), 'float');
				$current_unit['area_gros'] = (int)$area_column_gros; 
				$current_unit['area_net'] = (int)$area_column_net; 
				for($i = 1; $i <= $unit['level'] && $i <= 3; $i++) // Runs through all levels containing names
				{
					$current_unit['loc'.$i.'_name'] = $this->_unmarshal($this->db->f('loc'.$i.'_name', true), 'string');
				}
				$current_unit['address'] = $this->_unmarshal($this->db->f($address_column, true), 'string');
				$current_unit['part_of_town'] = $this->_unmarshal($this->db->f('name', true), 'string');
			}
		}
		
		$total_records = count($results);
		
		return array(
			'total_records' => $total_records,
			'results'		=> $results
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
		$contract_date = isset($params['contract_date']) && $params['contract_date'] ? $params['contract_date'] : null;
		
		// Default return data:
		$total_records = 0;
		$results = array();
		
		if($id > 0) // Valid id
		{
			$tables = 'rental_contract';
			$joins = 'JOIN rental_contract_composite ON (rental_contract.id = rental_contract_composite.contract_id) JOIN rental_contract_status ON (rental_contract.status_id = rental_contract_status.id)';
			$condition = 'rental_contract_composite.composite_id = '.$id;
			if($contract_status != null && $contract_status > 0){
				$condition .= ' AND rental_contract.status_id = '.$contract_status;
			}
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
			
			$this->db->query("SELECT COUNT(distinct rental_contract.id) AS count FROM $tables $joins WHERE $condition", __LINE__, __FILE__, $limit);
			$this->db->next_record();
			$total_records = (int)$this->db->f('count');
			
			$sql = "SELECT rental_contract.id, date_start, date_end, title FROM {$tables} {$joins} WHERE {$condition} {$order}";
			$this->db->limit_query($sql, $start, __LINE__, __FILE__, $limit);
			while($this->db->next_record())
			{
				$row = array();
	     		$row['id'] = $this->_unmarshal($this->db->f('id', true), 'string');
	     		$row['date_start'] =  date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], strtotime($this->_unmarshal($this->db->f('date_start', true), 'date')));
	     		$row['date_end'] = date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], strtotime($this->_unmarshal($this->db->f('date_end', true), 'date')));
	     		$row['tenant'] = ''; // TODO: We have to include tenant here whenever that table is ready
	     		$row['title'] = $this->_unmarshal($this->db->f('title', true), 'string');
				$results[] = $row;
			}
		}
		
		return array(
			'total_records' => $total_records,
			'results'		=> $results
		);
	}
	
	/**
	 * Returns array of available contract statuses
	 * @return array
	 * (
	 * 	id of status => textual presentation of status
	 * )
	 */
	public function get_contract_status_array()
	{
		$contract_status_array = array();
		$sql = 'SELECT id, title FROM rental_contract_status';
		$this->db->query($sql, __LINE__, __FILE__);
		while($this->db->next_record())
		{
			$contract_status_array[$this->_unmarshal($this->db->f('id', true), 'int')] = $this->_unmarshal($this->db->f('title', true), 'string');
		}
		return $contract_status_array;
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
			$values[] = $field . "=" . $this->_marshal($entry[$field], $params['type']);
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
}
?>
