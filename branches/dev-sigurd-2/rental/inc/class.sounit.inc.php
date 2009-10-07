<?php
phpgw::import_class('rental.socommon');
phpgw::import_class('rental.uicommon');

include_class('rental', 'composite', 'inc/model/');
include_class('rental', 'property_location', 'inc/model/');

class rental_sounit extends rental_socommon
{
	protected static $so;
	
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
	
	protected function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count)
	{
		$clauses = array('1=1');
		$filter_clauses = array();
		if(isset($filters['included_areas'])) // Areas/units already added to composite
		{
			$filter_clauses[] = "composite_id = {$this->marshal($filters['included_areas'], 'int')}";
		}
		if(isset($filters['available_areas'])) // Areas/unitos available for composite
		{
			// TODO: How advanced should we build this one? And should dates for vacancy be included?
			$filter_clauses[] = "composite_id != {$this->marshal($filters['available_areas'], 'int')}";
		}
		if(count($filter_clauses))
		{
			$clauses[] = join(' AND ', $filter_clauses);
		}
		$condition =  join(' AND ', $clauses);
		$tables = "rental_unit";
		$joins = '';
		if($return_count) // We should only return a count
		{
			$cols = 'COUNT(DISTINCT(id)) AS count';
		}
		else
		{
			$cols = 'id, composite_id, location_code';
		}
		$dir = $ascending ? 'ASC' : 'DESC';
		$order = $sort_field ? "ORDER BY {$this->marshal($sort_field, 'field')} $dir ": '';

		return "SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}";
	}
	
	protected function populate(int $unit_id, &$unit)
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
		return new rental_unit($this->unmarshal($this->db->f('id', true), 'int'), $this->unmarshal($this->db->f('composite_id', true), 'int'), $location);
	}
	
	protected function get_id_field_name()
	{
		return 'id';
	}
	
	
	public function add(&$unit)
	{
		$sql = "INSERT INTO rental_unit (composite_id, location_code) VALUES ({$unit->get_composite_id()}, {$unit->get_location()})";
		$result = $this->db->query($sql);
	}
	
	public function update($unit)
	{
		// There's never anything to update on a unit
	}

	public function delete(int $composite_id, string $location_code)
	{
		$sql ="DELETE FROM rental_unit WHERE composite_id = {$this->marshal($composite_id, 'int')} AND location_code = {$this->marshal($location_code, 'string')}";
		return $this->db->query($sql);
	}
	
}
?>