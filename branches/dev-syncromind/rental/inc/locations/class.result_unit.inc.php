<?php
	include_class('rental', 'organisational_location', 'inc/locations/');

	class result_unit extends organisational_location
	{

		public static $pattern = '.ORG.BK.__.____';
		protected static $start_index = 11;
		protected static $length_of_identifier = 4;

		public function __construct( int $location_id, string $name, string $description )
		{
			parent::__construct($location_id, $name, $description);
		}

		public static function get_identifier_from_name( string $name )
		{
			return substr($name, self::$start_index, self::$length_of_identifier);
		}
	}