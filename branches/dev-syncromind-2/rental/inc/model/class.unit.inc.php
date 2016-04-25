<?php

	/**
	 * Represents one single unit. A unit is one single combination of one
	 * composite from the rental module and one location from the property
	 * module.
	 *
	 */
	class rental_unit extends rental_model
	{

		protected static $so;
		protected $id;
		protected $composite_id;
		// The property location that this unit represents
		protected $location;

		public function __construct( int $id, $composite_id = -1, rental_property_location $location = null )
		{
			parent::__construct($id);
			$this->composite_id = (int)$composite_id;
			$this->location = $location;
		}

		public function get_composite_id()
		{
			return $this->composite_id;
		}

		public function set_location( $location )
		{
			$this->location = $location;
		}

		public function get_location()
		{
			return $this->location;
		}

		public function get_location_code()
		{
			if ($this->location != null)
			{
				return $this->location->get_location_code();
			}
			return -1;
		}

		/**
		 * Returns a string representation of this object.
		 *
		 * @return string with data about the object.
		 */
		public function __toString()
		{
			return "unit[id:{$this->get_id()},composite id:{$this->composite_id},location:{$this->get_location_code()}]";
		}

		public function serialize()
		{
			$result = array();
			$result['id'] = $this->get_id();
			$result['composite_id'] = $this->get_composite_id();
			$location = $this->get_location();
			if ($location != null)
			{
				$result = array_merge($result, $location->serialize());
			}

			return $result;
		}
	}