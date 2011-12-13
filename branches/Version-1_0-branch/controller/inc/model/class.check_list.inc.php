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
