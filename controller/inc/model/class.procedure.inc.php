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
		protected $start_date;
		protected $end_date;
		protected $procedure_id;
		protected $revision_no;
		protected $revision_date;
		
		/**
		 * Constructor.  Takes an optional ID.  If a procedure is created from outside
		 * the database the ID should be empty so the database can add one according to its logic.
		 * 
		 * @param int $id the id of this procedure
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
		
		public function set_responsibility($responsibility)
		{
			$this->responsibility = $responsibility;
		}
		
		public function get_responsibility() { return $this->responsibility; }
		
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
		
		public function set_start_date($start_date)
		{
			$this->start_date = $start_date;
		}
		
		public function get_start_date() { return $this->start_date; }
		
		public function set_end_date($end_date)
		{
			$this->end_date = $end_date;
		}
		
		public function get_end_date() { return $this->end_date; }
		
		public function set_procedure_id($procedure_id)
		{
			$this->procedure_id = $procedure_id;
		}
		
		public function get_procedure_id() { return $this->procedure_id; }
		
		public function set_revision_no($revision_no)
		{
			$this->revision_no = $revision_no;
		}
		
		public function get_revision_no() { return $this->revision_no; }
		
		public function set_revision_date($revision_date)
		{
			$this->revision_date = $revision_date;
		}
		
		public function get_revision_date() { return $this->revision_date; }
		
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
				
		public function serialize()
		{
			return array(
					'id' => $this->get_id(),
					'title' => $this->get_title(),
					'description' => $this->get_description(),
					'purpose' => $this->get_purpose(),
					'responsibility' => $this->get_responsibility(),
					'reference' => $this->get_reference(),
					'attachment' => $this->get_attachment(),
					'start_date' => $this->get_start_date(),
					'end_date' => $this->get_end_date(),
					'procedure_id' => $this->get_procedure_id(),
					'revision_no' => $this->get_revision_no(),
					'revision_date' => ($this->get_revision_date())?date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], $this->get_revision_date()):''
			);
		}
	}
?>