<?php
	include_class('controller', 'model', 'inc/model/');
	
	class controller_procedure extends controller_model
	{
		public static $so;
		
		protected $id;
		protected $title;
		protected $purpose;
		protected $responsibility;
		protected $description;
		protected $reference;
		protected $attachment;
		
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
		
		public function set_purpose($purpose)
		{
			$this->purpose = $purpose;
		}
		
		public function get_purpose() { return $this->purpose; }
		
		public function get_responsibility() { return $this->responsibility; }
		
		public function set_responsibility($responsibility)
		{
			$this->responsibility = $responsibility;
		}
		
		public function set_description($description)
		{
			$this->description = $description;
		}
		
		public function get_description() { return $this->description; }
		
		public function set_reference($reference)
		{
			$this->reference = $reference;
		}
		
		public function get_reference() { return $this->reference; }
		
		public function set_attachment($attachment)
		{
			$this->attachment = $attachment;
		}
		
		public function get_attachment() { return $this->attachment; }
		
		/**
		 * Get a static reference to the storage object associated with this model object
		 * 
		 * @return the storage object
		 */
		public static function get_so()
		{
			if (self::$so == null) {
				self::$so = CreateObject('controller.soprocedure');
			}
			
			return self::$so;
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
			$procedure_arr = array();
			foreach ($class_methods as $class_method)
			{
				if( stripos($class_method , 'get_' ) === 0  && !in_array($class_method, $exclude))
				{
					$_class_method_part = explode('get_', $class_method);
					$procedure_arr[$_class_method_part[1]] = $this->$class_method();
				}
			}

//			_debug_array($procedure_arr);
			return $procedure_arr;
		}
	}
?>