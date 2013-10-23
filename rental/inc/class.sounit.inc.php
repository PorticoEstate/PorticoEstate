<?php
	/**
	 * Frontend : a simplified tool for end users.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2010 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package Frontend
	 * @version $Id$
	 */

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	*/

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
		if(isset($filters['composite_id'])) // Areas/units already added to composite
		{
			$filter_clauses[] = "composite_id = {$this->marshal($filters['composite_id'], 'int')}";
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
		$data = execMethod('property.bolocation.read_single', array('location_code' => $location_code, 'extra' => array('view' => true)));
		$level = -1;
		$names = array();
		$levelFound = false;
		for($i = 1; !$levelFound; $i++)
		{
			$loc_name = 'loc'.$i.'_name';
			if(array_key_exists($loc_name, $data))
			{
				$level = $i;
				$names[$level] = $data[$loc_name];
			}
			else{
				$levelFound = true;
			}
		}
		$gab_id = '';
		$gabinfos  = execMethod('property.sogab.read', array('location_code' => $location_code, 'allrows' => true));
		if($gabinfos != null && is_array($gabinfos) && count($gabinfos) == 1)
		{
			$gabinfo = array_shift($gabinfos);
			$gab_id = $gabinfo['gab_id'];
		}
		$location = new rental_property_location($location_code, rental_uicommon::get_nicely_formatted_gab_id($gab_id), $level, $names);
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
				case 'bruttoareal':
					$location->set_area_gros($attributes['value']);
					break;
				case 'nettoareal':
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
	
	/**
	 * @see socommon->store()
	 */
	protected function add(&$unit)
	{
		$sql = "INSERT INTO rental_unit (composite_id, location_code) VALUES ({$this->marshal($unit->get_composite_id(), 'int')}, '{$unit->get_location_code()}')";
		$result = $this->db->query($sql);
		return $result ? true : false;
	}
	
	/**
	 * @see socommon->store()
	 */
	protected function update($unit)
	{
		// There's never anything to update on a unit
	}

	public function delete(int $unit_id)
	{
		$sql ="DELETE FROM rental_unit WHERE id = {$this->marshal($unit_id, 'int')}";
		$result = $this->db->query($sql);
		return $result ? true : false;
	}
	
}
?>
