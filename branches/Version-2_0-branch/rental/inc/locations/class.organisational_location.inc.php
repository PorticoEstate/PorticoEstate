<?php

	abstract class organisational_location
	{

		public static $pattern = '.ORG.BK';

		public function __construct( int $location_id, string $name, string $description )
		{
			$this->location_id = $location_id;
			$this->name = $name;
			$this->description = $description;
		}

		public function get_location_id()
		{
			return $this->location_id;
		}

		public function get_name()
		{
			return $this->name;
		}

		public function get_description()
		{
			return $this->description;
		}

		public abstract static function get_identifier_from_name( string $name );

		public function get_level_identifier()
		{
			return $this->get_identifier_from_name($this->name);
		}
	}