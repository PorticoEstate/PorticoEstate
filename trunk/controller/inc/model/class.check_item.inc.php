<?php
	include_class('controller', 'model', 'inc/model/');
	
	class controller_check_item extends controller_model
	{
		public static $so;

		protected $id;
		protected $control_item_id;
		protected $status;
		protected $comment;
		protected $check_list_id;
		protected $control_item;
		
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

		public function set_control_item_id($control_item_id)
		{
			$this->control_item_id = $control_item_id;
		}
		
		public function get_control_item_id() { return $this->control_item_id; }
				
		public function set_status($status)
		{
			$this->status = $status;
		}
		
		public function get_status() { return $this->status; }
		
		public function set_comment($comment)
		{
			$this->comment = $comment;
		}
		
		public function get_comment() { return $this->comment; }
		
		public function set_check_list_id($check_list_id)
		{
			$this->check_list_id = $check_list_id;
		}
		
		public function get_check_list_id() { return $this->check_list_id; }
		
		public function set_control_item($control_item)
		{
			$this->control_item = $control_item;
		}
		
		public function get_control_item() { return $this->control_item; }
			
			
		/**
		 * Get a static reference to the storage object associated with this model object
		 * 
		 * @return the storage object
		 */
		public static function get_so()
		{
/*			if (self::$so == null) {
				self::$so = CreateObject('controller.socontrol_item');
			}
			
			return self::$so;*/
		}
	}
?>