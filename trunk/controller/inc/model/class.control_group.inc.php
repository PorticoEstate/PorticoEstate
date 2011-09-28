<?php
	include_class('controller', 'model', 'inc/model/');
	
	class controller_control_group extends controller_model
	{
		public static $so;

		protected $id;
		protected $group_name;
		protected $procedure_id;
		protected $control_area_id;
		protected $building_part_id;
		protected $building_part_descr;
		
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

		public function set_group_name($group_name)
		{
			$this->group_name = $group_name;
		}
		
		public function get_group_name(){ return $this->group_name; }
		
		public function set_procedure_id($procedure_id)
		{
			$this->procedure_id = $procedure_id;
		}
		
		public function get_procedure_id(){ return $this->procedure_id; }
		
		public function set_control_area_id($control_area_id)
		{
			$this->control_area_id = $control_area_id;
		}
		
		public function get_control_area_id(){ return $this->control_area_id; }
		
		public function set_building_part_id($building_part_id)
		{
			$this->building_part_id = $building_part_id;
		}
		
		public function get_building_part_id(){ return $this->building_part_id; }
		
		public function set_building_part_descr($building_part_descr)
		{
			$this->building_part_descr = $building_part_descr;
		}
		
		public function get_building_part_descr(){ return $this->building_part_descr; }

		public function serialize()
		{
			$result = array();
			$result['id'] = $this->get_id();
			$result['group_name'] = $this->get_group_name();
			$result['procedure_id'] = $this->get_procedure_id();
			$result['control_area_id'] = $this->get_control_area_id();
			$result['building_part'] = $this->get_building_part_descr();
			
			return $result;
		}
		
		public function toArray()
		{

// Alternative 1
//			return get_object_vars($this);

// Alternative 2
			$exclude = array
			(
				'get_field', // feiler (foreldreklassen)
				'get_so',//unødvendig 
			);
			
			$class_methods = get_class_methods($this);
			$control_group_arr = array();
			foreach ($class_methods as $class_method)
			{
				if( stripos($class_method , 'get_' ) === 0  && !in_array($class_method, $exclude))
				{
					$_class_method_part = explode('get_', $class_method);
					$control_group_arr[$_class_method_part[1]] = $this->$class_method();
				}
			}

//			_debug_array($control_group_arr);
			return $control_group_arr;
		}
		
		/**
		 * Get a static reference to the storage object associated with this model object
		 * 
		 * @return the storage object
		 */
		public static function get_so()
		{
			if (self::$so == null) {
				self::$so = CreateObject('controller.socontrol_group');
			}
			
			return self::$so;
		}
	}
?>