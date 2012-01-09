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
 	* @version $Id: class.check_item.inc.php 8478 2012-01-03 12:36:37Z vator $
	*/

	include_class('controller', 'model', 'inc/model/');
	
	class controller_check_item_case extends controller_model
	{
		public static $so;

		protected $id;
		protected $check_item_id;
		protected $status;
		protected $location_id; 		// FOREKOMST I MELDINGSREGISTERET
		protected $location_item_id; 	// MELDINGS ID
		protected $descr;
		protected $user_id;
		protected $entry_date;
		protected $modified_date;
		protected $modified_by;
		
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

		public function set_check_item_id($check_item_id)
		{
			$this->check_item_id = $check_item_id;
		}
		
		public function get_check_item_id() { return $this->check_item_id; }
				
		public function set_status($status)
		{
			$this->status = (int)$status;
		}
		
		public function get_status() { return (int)$this->status; }
		
		public function get_location_id() { return (int)$this->location_id; }
		
		public function set_location_id($location_id)
		{
			$this->location_id = $location_id;
		}
		
		public function get_location_item_id() { return (int)$this->location_item_id; }
		
		public function set_location_item_id($location_item_id)
		{
			$this->location_item_id = $location_item_id;
		}

		public function get_descr() { return (int)$this->descr; }
		
		public function set_descr($descr)
		{
			$this->descr = $descr;
		}
		
		public function get_user_id() { return (int)$this->user_id; }
		
		public function set_user_id($user_id)
		{
			$this->user_id = $user_id;
		}
		
		public function get_entry_date() { return (int)$this->entry_date; }
		
		public function set_entry_date($entry_date)
		{
			$this->entry_date = $entry_date;
		}
		
		public function get_modified_date() { return (int)$this->modified_date; }
		
		public function set_modified_date($modified_date)
		{
			$this->modified_date = $modified_date;
		}
		
		public function get_modified_by() { return (int)$this->modified_by; }
		
		public function set_modified_by($modified_by)
		{
			$this->modified_by = $modified_by;
		}
	}
