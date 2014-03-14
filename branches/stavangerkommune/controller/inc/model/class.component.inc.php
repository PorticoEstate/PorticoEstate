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
 	* @version $Id: class.component.inc.php 10810 2013-02-13 19:49:14Z sigurdne $
	*/

	include_class('controller', 'model', 'inc/model/');

	class controller_component extends controller_model
	{
		protected $type;
		protected $id;
		protected $location_id;
		protected $guid;
		protected $xml;
		// Not a table column
		protected $xml_short_desc;
		protected $type_str;
		protected $location_code;
		protected $p_location_code;
		protected $loc_1;
		protected $address;
		
		// Objects
		protected $controls_array = array();
		
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
		
		public function set_location_id($location_id)
		{
			$this->location_id = $location_id;
		}
		
		public function get_location_id() { return $this->location_id; }
		
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
		
		public function set_xml_short_desc($xml_short_desc)
		{
			$this->xml_short_desc = $xml_short_desc;
		}
		
		public function get_xml_short_desc() { return $this->xml_short_desc; }
		
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
		
		public function set_type_str($type_str)
		{
			$this->type_str = $type_str;
		}
		
		public function get_type_str() { return $this->type_str; }
		
		public function set_controls_array($controls_array)
		{
			$this->controls_array = $controls_array;
		}
		
		public function get_controls_array() { return $this->controls_array; }
		
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
