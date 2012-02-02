<?php
	/**
	* phpGroupWare - controller: a part of a Facilities Management System.
	*
	* @author Erik Holm-Larsen <erik.holm-larsen@bouvet.no>
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
	
	class controller_control_item extends controller_model
	{
		public static $so;
		
		protected $id;
		protected $title;
		protected $required;
		protected $what_to_do;
		protected $how_to_do;
		protected $control_group_id;
		protected $control_group_name;
		protected $control_area_id;
		protected $control_area_name;
		protected $type;
		
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
		
		public function set_required($required)
		{
			$this->required = $required;
		}
		
		public function get_required() { return $this->required; }
		
		public function set_what_to_do($what_to_do)
		{
			$this->what_to_do = $what_to_do;
		}
		
		public function get_what_to_do() { return $this->what_to_do; }
		
		public function set_how_to_do($how_to_do)
		{
			$this->how_to_do = $how_to_do;
		}
		
		public function get_how_to_do() { return $this->how_to_do; }
		
		public function set_control_group_id($control_group_id)
		{
			$this->control_group_id = $control_group_id;
		}
		
		public function get_control_group_id() { return $this->control_group_id; }
		
		public function set_control_group_name($control_group_name)
		{
			$this->control_group_name = $control_group_name;
		}
		
		public function get_control_group_name() { return $this->control_group_name; }
		
		public function set_control_area_id($control_area_id)
		{
			$this->control_area_id = $control_area_id;
		}
		
		public function get_control_area_id() { return $this->control_area_id; }
		
		public function set_control_area_name($control_area_name)
		{
			$this->control_area_name = $control_area_name;
		}
		
		public function get_control_area_name() { return $this->control_area_name; }
		
		public function set_type($type)
		{
			$this->type = $type;
		}
		
		public function get_type() { return $this->type; }
			
		/**
		 * Get a static reference to the storage object associated with this model object
		 * 
		 * @return the storage object
		 */
		public static function get_so()
		{
			if (self::$so == null)
			{
				self::$so = CreateObject('controller.socontrol_item');
			}
			
			return self::$so;
		}
		
		 public function serialize()
		 {
			$result = array();
			$result['id'] = $this->get_id();
			$result['title'] = $this->get_title();
			$result['required'] = $this->get_required();
			$result['what_to_do'] = $this->get_what_to_do();
			$result['how_to_do'] = $this->get_how_to_do();
			$result['control_group'] = $this->get_control_group_name();
			$result['control_area'] = $this->get_control_area_name();
						
			return $result;
		}
	}
