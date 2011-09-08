<?php
	include_class('controller', 'model', 'inc/model/');
	
	class controller_check_item extends controller_model
	{
		public static $so;

		protected $id;
		protected $group_name;
		
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

		public function set_control_group($control_group)
		{
			$this->control_group = $control_group;
		}
		
		public function get_control_group(){ return $this->control_group; }
			
		/**
		 * Get a static reference to the storage object associated with this model object
		 * 
		 * @return the storage object
		 */
		public static function get_so()
		{
			if (self::$so == null) {
				self::$so = CreateObject('controller_check_item');
			}
			
			return self::$so;
		}
	}
?>