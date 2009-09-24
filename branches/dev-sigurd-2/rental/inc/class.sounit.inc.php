<?php
phpgw::import_class('rental.socommon');
phpgw::import_class('rental.uicommon');

include_class('rental', 'composite', 'inc/model/');
include_class('rental', 'property_location', 'inc/model/');

class rental_sounit extends rental_socommon
{

	/**
	 * Get a static reference to the storage object associated with this model object
	 * 
	 * @return the storage object
	 */
	public static function get_instance()
	{
		if (self::$so == null) {
			self::$so = CreateObject('rental.sounit');
		}
		return self::$so;
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