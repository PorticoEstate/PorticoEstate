<?php
	include_class('activitycalendar', 'model', 'inc/model/');

	class activitycalendar_category extends activitycalendar_model
	{
		public static $so;
		
		protected $id;
		protected $parent_id;
		protected $name;
		
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
		
		public function set_parent_id($parent_id)
		{
			$this->parent_id = $parent_id;
		}
		
		public function get_parent_id() { return $this->parent_id; }

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