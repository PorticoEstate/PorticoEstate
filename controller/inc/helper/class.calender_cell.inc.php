<?php
	class calendar_cell
	{		
		private $id;
		private $status;
		private $deadline;

		public function __construct(int $id, $status, $deadline)
		{
			$this->id = (int)$id;
			$this->status = $status;
			$this->deadline = $deadline;
		}
		
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
				'deadline' => $this->get_deadline()			
			);
		}
	}
?>