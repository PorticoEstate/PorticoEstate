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
					'adresse1' => array('type' => 'string'),
					'gab_id' => array('type' => 'string')
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
		// TODO: Should we ask for and let the address field on fm_location2 override the address found fm_location1? Do we know that the nothing higher than level 2 locations are rented? (The same question goes for the name of the location if we are to use it.)
		// XXX: The address ordering doesn't take custom addresses in consideration.
		$distinct = 'distinct on(rental_composite.composite_id)';
		$cols = 'rental_composite.composite_id, rental_composite.name, rental_composite.has_custom_address, rental_composite.address_1, rental_composite.house_number, fm_location1.adresse1, fm_gab_location.gab_id';
		$joins = 'JOIN rental_unit ON (rental_composite.composite_id = rental_unit.composite_id) JOIN fm_location1 ON (rental_unit.loc1 = fm_location1.loc1) JOIN fm_gab_location ON (rental_unit.loc1 = fm_gab_location.loc1)';

		if($order != '') // ORDER should be used
		{
			// We get a 'ERROR: SELECT DISTINCT ON expressions must match initial ORDER BY expressions' if we don't wrap the ORDER query.
			$this->db->limit_query("SELECT * FROM (SELECT $distinct $cols FROM $this->table_name $joins WHERE $condition) AS result $order", $start, __LINE__, __FILE__, $limit);
		}
		else
		{
			$this->db->limit_query("SELECT $distinct $cols FROM $this->table_name $joins WHERE $condition", $start, __LINE__, __FILE__, $limit);
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
			$row['gab_id'] = substr($row['gab_id'],4,5).' / '.substr($row['gab_id'],9,4);
			$results[] = $row;
		}
		return array(
			'total_records' => $total_records,
			'results'		=> $results
		);
	}
}
?>