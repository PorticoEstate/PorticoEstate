<?php
	include_class('controller', 'model', 'inc/model/');
	
	class controller_control_area extends controller_model
	{
		public static $so;

		protected $id;
		protected $title;
		
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
		
		public function get_title(){ return $this->title; }
			
		public function serialize()
		{
			return array(
					'id' => $this->get_id(),
					'title' => $this->get_title()
			);
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
			$control_item_arr = array();
			foreach ($class_methods as $class_method)
			{
				if( stripos($class_method , 'get_' ) === 0  && !in_array($class_method, $exclude))
				{
					$_class_method_part = explode('get_', $class_method);
					$control_item_arr[$_class_method_part[1]] = $this->$class_method();
				}
			}

//			_debug_array($control_item_arr);
			return $control_item_arr;
		}
		
		/**
		 * Get a static reference to the storage object associated with this model object
		 * 
		 * @return the storage object
		 */
		public static function get_so()
		{
			if (self::$so == null) {
				self::$so = CreateObject('controller_control_area');
			}
			
			return self::$so;
		}
	}
?>