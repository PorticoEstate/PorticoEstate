<?php
	include_class('controller', 'model', 'inc/model/');
	
	class controller_control_item_list extends controller_model
	{
		public static $so;
		
		protected $id;
		protected $control_id;
		protected $control_item_id;
				
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

		public function set_control_id($control_id)
		{
			$this->control_id = $control_id;
		}
		
		public function get_control_id() { return $this->control_id; }
		
		public function set_control_item_id($control_item_id)
		{
			$this->control_item_id = $control_item_id;
		}
		
		public function get_control_item_id() { return $this->control_item_id; }
				
		/**
		 * Get a static reference to the storage object associated with this model object
		 * 
		 * @return the storage object
		 */
		public static function get_so()
		{
			if (self::$so == null) {
				self::$so = CreateObject('controller.socontrol_item_list');
			}
			
			return self::$so;
		}
	}
?>