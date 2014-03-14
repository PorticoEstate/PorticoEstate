<?php
	/**
	* phpGroupWare - controller: a part of a Facilities Management System.
	*
	* @author Erink Holm-Larsen <erik.holm-larsen@bouvet.no>
	* @author Torstein Vadla <torstein.vadla@bouvet.no>
	* @copyright Copyright (C) 2011,2012 Free Software Foundation, Inc. http://www.fsf.org/
	* This file is part of phpGroupWare.
	*
	* phpGroupWare is free software; you can redistribute it and/or modify
	* it under the terms of the GNU General Public License as published by
	* the Free Software Foundation; either version 2 of the License, or
	* (at your option) any later version.
	*
	* phpGroupWare is distributed in the hope that it will be useful,
	* but WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	* GNU General Public License for more details.
	*
	* You should have received a copy of the GNU General Public License
	* along with phpGroupWare; if not, write to the Free Software
	* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/
	* @package property
	* @subpackage controller
 	* @version $Id$
	*/

	include_class('controller', 'model', 'inc/model/');
	
	class controller_check_list extends controller_model
	{
		public static $so;
				
		const STATUS_NOT_DONE = 0;
		const STATUS_DONE = 1;
		const STATUS_CANCELED = 3;
		
		protected $id;
		protected $title;//
		protected $description;//
		protected $control_id;
		protected $status;
		protected $comment;
		protected $deadline;
		protected $start_date;
		protected $end_date;
		protected $planned_date;
		protected $completed_date;
		protected $location_code;
		protected $component_id;
		protected $location_id;
		protected $assigned_to;
		protected $billable_hours;
		protected $control_area_id;		

		// Aggregate fields. Fields not in a table
		protected $num_open_cases;
		protected $num_pending_cases;
		
		// Objects
		protected $check_item_array = array();
		// Array that contains error messages. Is populted in function validate
		protected $error_msg_array = array();
		
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

		public function set_title($title)
		{
			$this->title = $title;
		}
		
		public function get_title()
		{
			return $this->title;
		}

		public function set_description($description)
		{
			$this->description = $description;
		}
		
		public function get_description()
		{
			return $this->description;
		}

		public function set_end_date($end_date)
		{
			$this->end_date = $end_date;
		}
		
		public function get_end_date()
		{
			return $this->end_date;
		}
		
		public function set_start_date($start_date)
		{
			$this->start_date = $start_date;
		}
		
		public function get_start_date()
		{
			return $this->start_date;
		}

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
		
		public function set_location_id($location_id)
		{
			$this->location_id = $location_id;
		}
		
		public function get_location_id() { return $this->location_id; }

		public function get_num_open_cases() { return $this->num_open_cases; }
		
		public function set_num_open_cases($num_open_cases)
		{
			$this->num_open_cases = $num_open_cases;
		}

		public function get_num_pending_cases() { return $this->num_pending_cases; }
		
		public function set_num_pending_cases($num_pending_cases)
		{
			$this->num_pending_cases = $num_pending_cases;
		}
		
		public function set_control($control)
		{
			$this->control = $control;
		}
		
		public function get_control() { return $this->control; }
		
		public function get_error_msg_array() { return $this->error_msg_array; }
		
		public function set_error_msg_array( $error_msg_array )
		{
			$this->error_msg_array = $error_msg_array;
		}

		public function set_control_area_id($control_area_id)
		{
			$this->control_area_id = $control_area_id;
		}
		
		public function get_control_area_id()
		{
			return $this->control_area_id;
		}

		public function set_assigned_to($assigned_to)
		{
			$this->assigned_to = $assigned_to;
		}
		
		public function get_assigned_to()
		{
			return $this->assigned_to;
		}

		public function set_billable_hours($billable_hours)
		{
			$this->billable_hours = $billable_hours;
		}
		
		public function get_billable_hours()
		{
			return $this->billable_hours;
		}
		
		public function serialize()
		{
			return array(
				'id' 				=> $this->get_id(),
				'title' 			=> $this->get_title(),
				'description' 		=> $this->get_description(),
				'control_id' 		=> $this->get_control_id(),
				'status' 			=> $this->get_status(),
				'comment' 			=> $this->get_comment(),
				'deadline' 			=> $this->get_deadline(),
				'planned_date' 		=> $this->get_planned_date(),
				'completed_date' 	=> $this->get_completed_date(),
				'start_date' 		=> $this->get_start_date(),
				'end_date'			=> $this->get_end_date(),
				'control_area_id'	=> $this->get_control_area_id(),
				'location_code' 	=> $this->get_location_code(),
				'component_id' 		=> $this->get_component_id(),
				'location_id' 		=> $this->get_location_id(),
				'num_open_cases' 	=> $this->get_num_open_cases(),
				'assigned_to'		=> $this->get_assigned_to(),
				'billable_hours'	=> $this->get_billable_hours()
			);
		}
		
		public function validate()
		{
			$status = true;
	
			// Validate CONTROL ID
			if( empty( $this->control_id ) )
			{
				$status = false;
				$this->error_msg_array['control_id'] = "error_msg_4";
			}
		 
			// Validate STATUS		  		  
			if( ($this->status != controller_check_list::STATUS_NOT_DONE) && ($this->status != controller_check_list::STATUS_DONE) && ($this->status != controller_check_list::STATUS_CANCELED))
			{ 
				$status = false;
				$this->error_msg_array['status'] = "error_msg_2";
			}
		    
			// Validate COMPLETED DATE when STATUS:DONE		  		  
			if( ($this->status == controller_check_list::STATUS_DONE) && empty($this->completed_date) )
			{
				$status = false;
				$this->error_msg_array['completed_date'] = "error_msg_5";
			}
	
			// Validate DEADLINE	  		  
			if( empty( $this->deadline ) )
			{
				$status = false;
				$this->error_msg_array['deadline'] = "error_msg_1";
			}
	
			// Validate connection to COMPONENT/LOCATION
			if( empty( $this->location_code ) && empty( $this->component_id ) )
			{
				echo "FAILED: " . $this->location_code; 
				$status = false;
				$this->error_msg_array['location_code'] = "error_msg_6";
			}
	
			return $status;
		}
	}
