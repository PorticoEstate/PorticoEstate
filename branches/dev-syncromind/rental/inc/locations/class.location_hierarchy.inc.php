<?php
	include_class('rental', 'city_counsil_dep', 'inc/locations/');
	include_class('rental', 'result_unit', 'inc/locations/');

	class location_hierarchy
	{

		public static function get_hierarchy()
		{
			$city_counsil_departments = array();
			$city_counsil_dep = $GLOBALS['phpgw']->locations->get_subs_from_pattern('rental', city_counsil_dep::$pattern);
			foreach ($city_counsil_dep as $department)
			{
				$name = $department['name'];
				$dep = new city_counsil_dep($department['location_id'], $name, $department['descr']);
				$city_counsil_departments[city_counsil_dep::get_identifier_from_name($dep->get_name())] = $dep;
			}

			$result_units = $GLOBALS['phpgw']->locations->get_subs_from_pattern('rental', result_unit::$pattern);

			foreach ($result_units as $result_unit)
			{
				$unit = new result_unit($result_unit['location_id'], $result_unit['name'], $result_unit['descr']);
				$city_counsil_dep_identifier = city_counsil_dep::get_identifier_from_name($unit->get_name());
				$dep = $city_counsil_departments[$city_counsil_dep_identifier];
				$dep->add_result_unit($unit);
			}
			return $city_counsil_departments;
		}

		public static function get_name_of_location( int $location_id )
		{
			$location_name = $GLOBALS['phpgw']->locations->get_name($location_id);
			$result_unit_number = result_unit::get_identifier_from_name($location_name['location']);
			return $result_unit_number . " - " . $location_name['descr'];
		}
	}