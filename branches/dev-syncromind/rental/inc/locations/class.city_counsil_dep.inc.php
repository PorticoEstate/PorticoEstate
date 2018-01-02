<?php
	include_class('rental', 'organisational_location', 'inc/locations/');

	class city_counsil_dep extends organisational_location
	{

		public static $pattern = '.ORG.BK.__';
		protected static $start_index = 8;
		protected static $length_of_identifier = 2;
		protected $result_units = array();

		public function __construct( int $location_id, string $name, string $description )
		{
			parent::__construct($location_id, $name, $description);
		}

		public function add_result_unit( result_unit $result_unit )
		{
			$this->result_units[result_unit::get_identifier_from_name($result_unit->get_name())] = $result_unit;
		}

		public function get_result_unit( $level_identifier )
		{
			return $this->result_units[$level_identifier];
		}

		public function get_result_units()
		{
			return $this->result_units;
		}

		public static function get_identifier_from_name( string $name )
		{
			return substr($name, self::$start_index, self::$length_of_identifier);
		}
	}