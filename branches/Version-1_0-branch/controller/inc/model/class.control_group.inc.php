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
	
	class controller_control_group extends controller_model
	{
		public static $so;

		protected $id;
		protected $group_name;
		protected $procedure_id;
		protected $procedure_name;
		protected $control_area_id;
		protected $control_area_name;
		protected $building_part_id;
		protected $building_part_descr;
		protected $order_nr;
		protected $component_location_id;
		protected $component_criteria = array();
				
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

		public function set_group_name($group_name)
		{
			$this->group_name = $group_name;
		}
		
		public function get_group_name(){ return $this->group_name; }
		
		public function set_procedure_id($procedure_id)
		{
			$this->procedure_id = $procedure_id;
		}
		
		public function get_procedure_id(){ return $this->procedure_id; }
		
		public function set_procedure_name($procedure_name)
		{
			$this->procedure_name = $procedure_name;
		}
		
		public function get_procedure_name(){ return $this->procedure_name; }
		
		public function set_control_area_id($control_area_id)
		{
			$this->control_area_id = $control_area_id;
		}
		
		public function get_control_area_id(){ return $this->control_area_id; }
		
		public function set_control_area_name($control_area_name)
		{
			$this->control_area_name = $control_area_name;
		}
		
		public function get_control_area_name(){ return $this->control_area_name; }
		
		public function set_building_part_id($building_part_id)
		{
			$this->building_part_id = $building_part_id;
		}
		
		public function get_building_part_id(){ return $this->building_part_id; }
		
		public function set_building_part_descr($building_part_descr)
		{
			$this->building_part_descr = $building_part_descr;
		}
		
		public function get_building_part_descr(){ return $this->building_part_descr; }
		
		public function set_order_nr($order_nr)
		{
			$this->order_nr = $order_nr;
		}
		
		public function get_order_nr(){ return $this->order_nr; }


		public function set_component_location_id($component_location_id)
		{
			$this->component_location_id = $component_location_id;
		}
		
		public function get_component_location_id()
		{
			return $this->component_location_id;
		}

		public function set_component_criteria($component_criteria)
		{
			$this->component_criteria = $component_criteria;
		}
		
		public function get_component_criteria()
		{
			return $this->component_criteria;
		}

		public function serialize()
		{
			$result = array();
			$result['id'] = $this->get_id();
			$result['group_name'] = $this->get_group_name();
			$result['procedure'] = $this->get_procedure_name();
			$result['control_area'] = $this->get_control_area_name();
			$result['building_part'] = $this->get_building_part_descr();
			$result['order_nr'] = $this->get_order_nr();
			$result['component_location_id'] = $this->get_component_location_id();
			$result['component_criteria'] = $this->get_component_criteria();
			
			return $result;
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
			$control_group_arr = array();
			foreach ($class_methods as $class_method)
			{
				if( stripos($class_method , 'get_' ) === 0  && !in_array($class_method, $exclude))
				{
					$_class_method_part = explode('get_', $class_method);
					$control_group_arr[$_class_method_part[1]] = $this->$class_method();
				}
			}

//			_debug_array($control_group_arr);
			return $control_group_arr;
		}
		
		/**
		 * Get a static reference to the storage object associated with this model object
		 * 
		 * @return the storage object
		 */
		public static function get_so()
		{
			if (self::$so == null) {
				self::$so = CreateObject('controller.socontrol_group');
			}
			
			return self::$so;
		}
	}
