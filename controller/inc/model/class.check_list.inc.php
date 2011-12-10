<?php
	include_class('controller', 'model', 'inc/model/');
	
	class controller_check_list extends controller_model
	{
		public static $so;

		protected $id;
		protected $control_id;
		protected $status;
		protected $comment;
		protected $deadline;
		protected $planned_date;
		protected $completed_date;
		protected $location_code;
		protected $component_id;
		protected $check_item_array = array();
		protected $control;
		
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
		
		public function set_deadline($deadline)
		{
			$this->deadline = $deadline;
		}
		
		public function get_deadline() { return $this->deadline; }
		
		public function set_check_item_array($check_item_array)
		{
			$this->check_item_array = $check_item_array;
		}
		
		public function get_check_item_array() { return $this->check_item_array; }
		
		public function set_planned_date($planned_date)
		{
			$this->planned_date = $planned_date;
		}
		
		public function get_planned_date() { return $this->planned_date; }
		
		public function set_completed_date($completed_date)
		{
			$this->completed_date = $completed_date;
		}
		
		public function get_completed_date() { return $this->completed_date; }
		
		public function set_location_code($location_code)
		{
			$this->location_code = $location_code;
		}
		
		public function get_location_code() { return $this->location_code; }
		
		public function set_component_id($component_id)
		{
			$this->component_id = $component_id;
		}
		
		public function get_component_id() { return $this->component_id; }
		
		public function set_control($control)
		{
			$this->control = $control;
		}
		
		public function get_control() { return $this->control; }
		
		public function serialize()
		{
			return array(
				'id' => $this->get_id(),
				'control_id' => $this->get_control_id(),
				'status' => $this->get_status(),
				'comment' => $this->get_comment(),
				'deadline' => $this->get_deadline()
				);
		}
	}
?>
