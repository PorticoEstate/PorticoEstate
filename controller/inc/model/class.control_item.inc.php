<?php
	include_class('controller', 'model', 'inc/model/');
	
	class controller_control_item extends controller_model
	{
		public static $so;
		
		protected $id;
		protected $title;
		protected $required;
		protected $what_to_desc;
		protected $how_to_desc;
		protected $control_group_id;
		protected $control_type_id;
		
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

		public function set_title($title)
		{
			$this->title = $title;
		}
		
		public function get_title() { return $this->title; }
		
		public function set_required($required)
		{
			$this->required = $required;
		}
		
		public function get_required() { return $this->required; }
		
		public function set_what_to_desc($what_to_desc)
		{
			$this->what_to_desc = $what_to_desc;
		}
		
		public function get_what_to_desc() { return $this->what_to_desc; }
		
		public function set_how_to_desc($how_to_desc)
		{
			$this->how_to_desc = $how_to_desc;
		}
		
		public function get_how_to_desc() { return $this->how_to_desc; }
		
		public function set_control_group_id($control_group_id)
		{
			$this->control_group_id = $control_group_id;
		}
		
		public function get_control_group_id() { return $this->control_group_id; }
		
		public function set_control_type_id($control_type_id)
		{
			$this->control_type_id = $control_type_id;
		}
		
		public function get_control_type_id() { return $this->control_type_id; }
		
			
		/**
		 * Get a static reference to the storage object associated with this model object
		 * 
		 * @return the storage object
		 */
		public static function get_so()
		{
			if (self::$so == null) {
				self::$so = CreateObject('controller.sopurpose');
			}
			
			return self::$so;
		}
	}
?>