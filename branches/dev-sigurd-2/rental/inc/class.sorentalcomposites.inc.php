<?php
phpgw::import_class('rental.socommon');

class rental_sorentalcomposites extends rental_socommon
{
	function __construct()
	{
		parent::__construct('rental_composite',
		array
		(
					'composite_id'	=> array('type' => 'int'),
					'name'	=> array('type' => 'string'),
					'has_custom_address' => array('type' => 'bool'),
					'address_1'	=> array('type' => 'string'),
					'house_number' => array('type' => 'int'),
					'adresse1' => array('type' => 'string')
		));
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
		$filters = isset($params['filters']) && $params['filters'] ? $params['filters'] : array();

		$condition = $this->_get_conditions($query, $filters);
		
		// Calculate total number of records
		$this->db->query("SELECT count(1) AS count FROM $this->table_name $joins WHERE $condition", __LINE__, __FILE__);
		$this->db->next_record();
		$total_records = (int)$this->db->f('count');

		$order = $sort ? "ORDER BY $sort $dir ": '';
		
		// We interpret 'Eiendomsnavn' as the name of the composite object and not loc1_name or loc2_name. TODO: Is this okay?
		// TODO: Where can we get 'Gårds-/bruksnummer' from?
		// TODO: What is 'Type' in the prototype?
		// TODO: Should we ask for and let the address field on fm_location2 override the address found fm_location1? Do we know that the nothing higher than level 2 locations are rented? (The same question goes for the name of the location if we are to use it.)
		// XXX: The address ordering doesn't take custom addresses in consideration.
		$distinct = 'distinct on(rental_composite.composite_id)';
		$cols = 'rental_composite.composite_id, rental_composite.name, rental_composite.has_custom_address, rental_composite.address_1, rental_composite.house_number, fm_location1.adresse1';
		$from = $this->table_name;
		$joins = 'JOIN rental_unit ON (rental_composite.composite_id = rental_unit.composite_id) JOIN fm_location1 ON (rental_unit.loc1 = fm_location1.loc1)';

		if($order != '') // ORDER should be used
		{
			// We get a 'ERROR: SELECT DISTINCT ON expressions must match initial ORDER BY expressions' if we don't wrap the ORDER query.
			$this->db->limit_query("SELECT * FROM (SELECT $distinct $cols FROM $from $joins WHERE $condition) AS result $order", $start, __LINE__, __FILE__, $limit);
		}
		else
		{
			$this->db->limit_query("SELECT $distinct $cols FROM $from $joins WHERE $condition", $start, __LINE__, __FILE__, $limit);
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
			$results[] = $row;
		}
		if(count($results) > 0)
		{
    		foreach($results as $id => $result)
    		{
    		    $id_map[$result['id']] = $id;
    		    
    		}
    		foreach($this->fields as $field => $params)
    		{
    			
    		}
	    }
		return array(
			'total_records' => $total_records,
			'results'		=> $results
		);
	}

	/*
	function read($params){
		$resultArray = parent::read($params);
		if($resultArray != null && is_array($resultArray))
		{
			$resultsArray = &$resultArray['results'];
			if($resultsArray != null && is_array($resultsArray))
			{
				foreach($resultsArray as &$composite) // Runs through every composite object found
				{
					// We assume a custom address by default
					$composite['address_1'] .= ' '.$composite['house_number'];

					// Selects first and best unit from the composite
					$this->db->limit_query('SELECT location_id FROM rental_unit WHERE composite_id = '.(int)$composite['composite_id'], $start, __LINE__, __FILE__, 1);
					if($this->db->next_record())
					{
						$locationId = (int)$this->db->f('location_id', true);
						if($locationId > 0) // Should always be set
						{
							$this->db->limit_query('SELECT level, location_code FROM fm_locations WHERE id = '.$locationId, $start, __LINE__, __FILE__, 1);
							if($this->db->next_record())
							{
								$level = (int)$this->db->f('level', true); // On what level (property, building, etc) to find address
								$locationCode = $this->db->f('location_code', true); // E.g. 2711, 2103-06, 1101-01-U1-U1, 1101-01-02-02-224A
								switch($level)
								{
									case 3:
									case 4:
									case 5:
										$this->db->limit_query('SELECT loc1, loc2 FROM fm_location'.$level.' WHERE location_code = \''.$locationCode.'\'', $start, __LINE__, __FILE__, 1);
										$locationCodeLevel2 = null;
										if($this->db->next_record())
										{
											$locationCodeLevel2 = $this->db->f('loc1', true).'-'.$this->db->f('loc2', true);
										}
										// NOTE: No break here
									case 2:
										if($level == 2)
										{
											$locationCodeLevel2 = $locationCode;
										}
										$locationCodeLevel1 = null;
										if($locationCodeLevel2 != null)
										{
											$this->db->limit_query('SELECT loc1, adresse FROM fm_location2 WHERE location_code = \''.$locationCodeLevel2.'\'', $start, __LINE__, __FILE__, 1);
											if($this->db->next_record())
											{
												$locationCodeLevel1 = $this->db->f('loc1', true);
												if($composite['has_custom_address'] != '1' && $level == 2) // No custom address and we should use the address from level 2
												{
													$composite['address_1'] = $this->db->f('adresse', true);
												}
											}
										}
										// NOTE: No break here
									case 1:
										if($level == 1)
										{
											$locationCodeLevel1 = $locationCode;
										}
										if($locationCodeLevel1 != null)
										{
											$this->db->limit_query('SELECT loc1_name, adresse1 FROM fm_location1 WHERE location_code = \''.$locationCodeLevel1.'\'', $start, __LINE__, __FILE__, 1);
											if($this->db->next_record())
											{
												$composite['property_name'] = $this->db->f('loc1_name', true);
												if($composite['has_custom_address'] != '1' && $composite['address_1'] != null && $composite['address_1'] != '') // No custom address and we didn't get any address from level 2
												{
													$composite['address_1'] = $this->db->f('adresse1', true);
												}
											}
										}
										break;
								}
							}
						}
					}
				}
			}
		}
		return $resultArray;
	} // end function
	*/
}
?>