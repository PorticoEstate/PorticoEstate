<?php
	class check_list_status_info
	{		
		private $id;
		private $status;
		private $status_text;
		private $deadline;

		public function __construct(){}
		
		public function set_id($id)
		{
			$this->id = $id;
		}
		
		public function get_id() { return $this->id; }
		
		public function set_status($status)
		{
			$this->status = $status;
		}
		
		public function get_status() { return $this->status; }
		
		public function set_status_text($status_text)
		{
			$this->status_text = $status_text;
		}
		
		public function get_status_text() { return $this->status_text; }
		
		public function set_deadline($deadline)
		{
			$this->deadline = $deadline;
		}
		
		public function get_deadline() { return $this->deadline; }
		
		
		public function serialize()
		{
			return array(
				'id' => $this->get_id(),
				'status' => $this->get_status(),
				'status_text' => $this->get_status_text(),
				'deadline' => $this->get_deadline()			
			);
		}
	}
?>