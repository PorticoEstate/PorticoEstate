<?php
	include_class('activitycalendar', 'model', 'inc/model/');

	class activitycalendar_target extends activitycalendar_model
	{
		public static $so;
		
		protected $id;
		protected $name;
		protected $description;
		
		/**
		 * Constructor.  Takes an optional ID.  If a contract is created from outside
		 * the database the ID should be empty so the database can add one according to its logic.
		 * 
		 * @param int $id the id of this composite
		 */
		public function __construct(int $id = null)
		{
			$this->id = (int)$id;
		}
		
		public function set_id($id)
		{
			$this->id = $id;
		}
		
		public function get_id() { return $this->id; }
		
		public function set_description($description)
		{
			$this->description = $description;
		}
		
		public function get_description() { return $this->description; }

		public function set_name($name)
		{
			$this->name = $name;
		}
		
		public function get_name() { return $this->name; }
		
		public function serialize()
		{
			return;
		}
	}
?>