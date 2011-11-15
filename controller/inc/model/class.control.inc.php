<?php
	include_class('controller', 'model', 'inc/model/');

	class controller_control extends controller_model
	{
		public static $so;
		
		protected $id;
		protected $title;
		protected $description;
		protected $start_date;
		protected $end_date;
		protected $repeat_type;
		protected $repeat_interval;
		protected $procedure_id;
		protected $procedure_name;
		protected $enabled;
		protected $requirement_id;
		protected $costresponsibility_id;
		protected $responsibility_id;
		protected $equipment_id;
		protected $equipment_type_id;
		protected $location_code;
		protected $control_area_id;
		protected $control_area_name;

		protected $check_lists_array = array();
		
		
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
		
		public function set_description($description)
		{
			$this->description = $description;
		}
		
		public function get_description() { return $this->description; }
		
		public function set_end_date($end_date)
		{
			$this->end_date = $end_date;
		}
		
		public function get_end_date() { return $this->end_date; }
		
		public function set_start_date($start_date)
		{
			$this->start_date = $start_date;
		}
		
		public function get_start_date() { return $this->start_date; }
		
		public function set_repeat_type($repeat_type)
		{
			$this->repeat_type = $repeat_type;
		}
					
		public function get_repeat_type() { return $this->repeat_type; }
		
		public function set_repeat_interval($repeat_interval)
		{
			$this->repeat_interval = $repeat_interval;
		}
		
		public function get_repeat_interval() { return $this->repeat_interval; }
		
		public function set_procedure_id($procedure_id)
		{
			$this->procedure_id = $procedure_id;
		}
		
		public function get_procedure_id() { return $this->procedure_id; }
		
		public function set_procedure_name($procedure_name)
		{
			$this->procedure_name = $procedure_name;
		}
		
		public function get_procedure_name() { return $this->procedure_name; }
		
		public function set_enabled($enabled)
		{
			$this->enabled = $enabled;
		}
		
		public function get_enabled() { return $this->enabled; }
		
		public function set_requirement_id($requirement_id)
		{
			$this->requirement_id = $requirement_id;
		}
		
		public function get_requirement_id() { return $this->requirement_id; }
		
		public function set_costresponsibility_id($costresponsibility_id)
		{
			$this->costresponsibility_id = $costresponsibility_id;
		}
		
		public function get_costresponsibility_id() { return $this->costresponsibility_id; }
		
		public function set_responsibility_id($responsibility_id)
		{
			$this->responsibility_id = $responsibility_id;
		}
		
		public function get_responsibility_id() { return $this->responsibility_id; }
		
		public function set_equipment_id($equipment_id)
		{
			$this->equipment_id = $equipment_id;
		}
		
		public function get_equipment_id() { return $this->equipment_id; }
		
		public function set_equipment_type_id($equipment_type_id)
		{
			$this->equipment_type_id = $equipment_type_id;
		}
		
		public function get_equipment_type_id() { return $this->equipment_type_id; }
		
		public function set_location_code($location_code)
		{
			$this->location_code = $location_code;
		}
		
		public function get_location_code() { return $this->location_code; }
		
		public function set_control_area_id($control_area_id)
		{
			$this->control_area_id = $control_area_id;
		}
		
		public function get_control_area_id() { return $this->control_area_id; }
		
		public function set_control_area_name($control_area_name)
		{
			$this->control_area_name = $control_area_name;
		}
		
		public function get_control_area_name() { return $this->control_area_name; }
		
		public function set_check_lists_array($check_lists_array)
		{
			$this->check_lists_array = $check_lists_array;
		}
		
		public function get_check_lists_array() { return $this->check_lists_array; }
		
		/**
		 * Get a static reference to the storage object associated with this model object
		 * 
		 * @return the storage object
		 */
		public static function get_so()
		{
			if (self::$so == null) {
				self::$so = CreateObject('controller.socontrol');
			}
			
			return self::$so;
		}
		
		public function populate()
		{
				$this->set_title(phpgw::get_var('title','string'));
				$this->set_description(phpgw::get_var('description','html'));
				$this->set_start_date(strtotime( phpgw::get_var('start_date_hidden','string') ));
				$this->set_end_date(strtotime( phpgw::get_var('end_date_hidden','string') ));
				$this->set_procedure_id(phpgw::get_var('procedure_id','int'));
				$this->set_control_area_id(phpgw::get_var('control_area_id','int'));
				$this->set_repeat_type(phpgw::get_var('repeat_type','int'));
				$this->set_repeat_interval(phpgw::get_var('repeat_interval','int'));
		}
		
		public function serialize()
		{
			return array(
				'id' => $this->get_id(),
				'title' => $this->get_title(),
				'description' => $this->get_description(),
				'start_date' => $this->get_start_date(),
				'end_date' => $this->get_end_date(),
				'procedure_id' => $this->get_procedure_id(),
				'procedure_name' => $this->get_procedure_name(),
				'control_area_id' => $this->get_control_area_id(),
				'control_area_name' => $this->get_control_area_name(),
			   	'repeat_type' => $this->get_repeat_type(),
				'repeat_interval' => $this->get_repeat_interval(),
			
				);
		}
	}
?>