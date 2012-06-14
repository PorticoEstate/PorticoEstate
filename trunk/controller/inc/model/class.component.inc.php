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
 	* @version $Id: class.control.inc.php 9548 2012-06-11 12:40:52Z vator $
	*/

	include_class('controller', 'model', 'inc/model/');
	include_class('controller', 'date_helper', 'inc/helper/');

	class controller_control extends controller_model
	{
		public static $so;
		
		protected $type;
		protected $id;
		protected $guid;
		protected $xml;
		protected $location_code;
		protected $loc_1;
		protected $address;
		
		// Objects
		protected $controls_list_array = array();
		
		/**
		 * Constructor.  Takes an optional ID.  If a contract is created from outside
		 * the database the ID should be empty so the database can add one according to its logic.
		 * 
		 * @param int $id the id of this composite
		 */
		
		public function set_type($type)
		{
			$this->type = $type;
		}
		
		public function get_type() { return $this->type; }
			
		public function set_id($id)
		{
			$this->id = $id;
		}
		
		public function get_id() { return $this->id; }
		
		public function set_guid($guid)
		{
			$this->guid = $guid;
		}
		
		public function get_guid() { return $this->guid; }
		
		
		public function set_xml($xml)
		{
			$this->xml = $xml;
		}
		
		public function get_xml() { return $this->xml; }
		
		public function set_location_code($location_code)
		{
			$this->location_code = $location_code;
		}
		
		public function get_location_code() { return $this->location_code; }
		
		public function set_loc_1($loc_1)
		{
			$this->loc_1 = $loc_1;
		}
		
		public function get_loc_1() { return $this->loc_1; }
		
		public function set_address($address)
		{
			$this->address = $address;
		}
		
		public function get_address() { return $this->address; }
		
		public function set_controls_list_array($controls_list_array)
		{
			$this->controls_list_array = $controls_list_array;
		}
		
		public function get_controls_list_array() { return $this->controls_list_array; }
		
		public function serialize()
		{
			return array(
				'type' => $this->get_type(),
				'id' => $this->get_id(),
				'guid' => $this->get_guid(),
				'xml' => $this->get_xml(),
				'location_code' => $this->get_location_code(),
				'loc_1' => $this->get_loc_1(),
				'address' => $this->get_address()
			);
		}
	}
