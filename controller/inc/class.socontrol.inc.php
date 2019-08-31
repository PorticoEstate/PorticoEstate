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
	phpgw::import_class('controller.socommon');

	include_class('controller', 'control', 'inc/model/');
	include_class('controller', 'check_list', 'inc/model/');
	include_class('controller', 'component', 'inc/model/');
	include_class('controller', 'control_location', 'inc/model/');

	class controller_socontrol extends controller_socommon
	{

		protected static $so;
		protected $global_lock = false;

		/**
		 * Get a static reference to the storage object associated with this model object
		 *
		 * @return controller_soparty the storage object
		 */
		public static function get_instance()
		{
			if (self::$so == null)
			{
				self::$so = CreateObject('controller.socontrol');
			}
			return self::$so;
		}

		/**
		 * Add a new control to database.
		 * @param $control control object
		 * @return bool true if successful, false otherwise
		 */
		function add( &$control )
		{
			$title = $control->get_title();

			$sql = "INSERT INTO controller_control (title) VALUES ('$title')";
			$result = $this->db->query($sql, __LINE__, __FILE__);

			if ($result)
			{
				// Set the new control ID
				$control->set_id($this->db->get_last_insert_id('controller_control', 'id'));

				// Forward this request to the update method
				return $this->update($control);
			}
			else
			{
				return false;
			}
		}

		/**
		 * Update the database values for an existing control object.
		 *
		 * @param $control the control to be updated
		 * @return bool true if successful, false otherwise
		 */
		function update( $control )
		{
			$id = intval($control->get_id());

			$values = array(
				'title = ' . $this->marshal($control->get_title(), 'string'),
				'description = ' . $this->marshal($control->get_description(), 'string'),
				'start_date = ' . $this->marshal($control->get_start_date(), 'int'),
				'end_date = ' . $this->marshal($control->get_end_date(), 'int'),
				'control_area_id = ' . $this->marshal($control->get_control_area_id(), 'int'),
				'repeat_type = ' . $this->marshal($control->get_repeat_type(), 'string'),
				'repeat_interval = ' . $this->marshal($control->get_repeat_interval(), 'string'),
				'procedure_id = ' . $this->marshal($control->get_procedure_id(), 'int'),
				'responsibility_id = ' . $this->marshal($control->get_responsibility_id(), 'int'),
				'ticket_cat_id = ' . $this->marshal($control->get_ticket_cat_id(), 'int'),
			);

			$result = $this->db->query('UPDATE controller_control SET ' . join(',', $values) . " WHERE id=$id", __LINE__, __FILE__);

			if ($result)
			{
				return $id;
			}
			else
			{
				return 0;
			}
		}

		/**
		 * Get controls that should be carried out on a location within period
		 *
		 * @param $location_code the locaction code for the location the control should be carried out for   
		 * @param $from_date start date for period
		 * @param $to_date end date for period
		 * @param $repeat_type Dag, Uke, Måned, År 
		 * @param $return_type return data as objects or as arrays
		 * @param $role_id responsible role for carrying out the control  
		 * @return array with controls as objects or arrays
		 */
		public function get_assigned_check_list_at_location( $from_date, $to_date, $repeat_type, $user_id, $completed = null, $return_type = "return_object" )
		{
			$user_id = (int)$user_id;
			$repeat_type = (int)$repeat_type;

			$check_list_array = array();

			$sql = "SELECT DISTINCT controller_check_list.location_code, controller_check_list.control_id, controller_check_list.id AS check_list_id,"
				. " procedure_id,requirement_id,costresponsibility_id,description, controller_control.start_date, end_date,deadline,planned_date, completed_date,"
				. " control_area_id, repeat_type,repeat_interval, title"
				. " FROM controller_check_list"
				. " {$this->join} controller_control ON controller_check_list.control_id = controller_control.id"
				. " {$this->join} controller_control_location_list ON controller_control_location_list.control_id = controller_control.id"
				. " WHERE controller_check_list.assigned_to = {$user_id} AND status = 0";

//_debug_array($sql);
			if ($repeat_type)
			{
//				$sql .= "AND controller_control.repeat_type = $repeat_type ";
			}

			//FIXME
			if ($completed)
			{
				$sql .= " AND ( planned_date < $to_date AND controller_check_list.completed_date IS NULL) ";

//				$sql .= " AND ((controller_control.start_date <= $to_date AND controller_control.end_date IS NULL) ";
//				$sql .= " OR (controller_control.start_date <= $to_date AND controller_control.end_date > $from_date ))";
//				$sql .= " AND controller_check_list.completed_date IS NULL ";
			}
			else
			{
				$sql .= " AND (planned_date > $from_date AND planned_date <= $to_date AND controller_control.end_date IS NULL) ";
			}

//_debug_array($sql);
			$this->db->query($sql);

			while ($this->db->next_record())
			{
				$check_list = new controller_check_list($this->unmarshal($this->db->f('check_list_id'), 'int'));
				$check_list->set_control_id($this->unmarshal($this->db->f('control_id'), 'int'));
				$check_list->set_title($this->unmarshal($this->db->f('title', true), 'string'));
				$check_list->set_description($this->unmarshal($this->db->f('description', true), 'string'));
				$check_list->set_start_date($this->unmarshal($this->db->f('start_date'), 'int'));
				$check_list->set_end_date($this->unmarshal($this->db->f('end_date'), 'int'));
				$check_list->set_deadline($this->unmarshal($this->db->f('deadline'), 'int'));
				$check_list->set_planned_date($this->unmarshal($this->db->f('planned_date'), 'int'));
				$check_list->set_completed_date($this->unmarshal($this->db->f('completed_date'), 'int'));
				$check_list->set_control_area_id($this->unmarshal($this->db->f('control_area_id'), 'int'));
				$check_list->set_location_code($this->unmarshal($this->db->f('location_code', true), 'string'));
				$check_list->set_assigned_to($this->unmarshal($user_id, 'int'));

				if ($return_type == "return_object")
				{
					$check_list_array[] = $check_list;
				}
				else
				{
					$check_list_array[] = $check_list->toArray();
				}
			}

			return $check_list_array;
		}
//---------

		/**
		 * Get components and populates array of controls that should be carried out on the components on a location within period
		 *
		 * @param $location_code the locaction code for the location the control should be carried out for   
		 * @param $from_date start date for period
		 * @param $to_date end date for period
		 * @param $repeat_type Dag, Uke, Måned, År 
		 * @param $return_type return data as objects or as arrays
		 * @param $role_id responsible role for carrying out the control  
		 * @return array of components as objects or arrays
		 */
		public function get_assigned_check_list_by_component( $from_date, $to_date, $repeat_type, $user_id, $completed = null, $return_type = "return_object" )
		{
			$repeat_type = $repeat_type;
			$user_id = (int)$user_id;


			$sql = "SELECT DISTINCT controller_check_list.location_code, controller_check_list.control_id, controller_check_list.id AS check_list_id,"
				. " controller_control.description, controller_control.start_date, end_date, deadline,planned_date, completed_date,"
				. " control_area_id,controller_check_list.location_id,title,controller_check_list.component_id"
				. " FROM controller_check_list"
				. " {$this->join} controller_control ON controller_check_list.control_id = controller_control.id"
				. " {$this->join} controller_control_component_list "
				. " ON (controller_control_component_list.control_id = controller_check_list.control_id"
				. " AND controller_control_component_list.location_id = controller_check_list.location_id"
				. " AND controller_control_component_list.component_id = controller_check_list.component_id)"
				. " WHERE controller_check_list.assigned_to = {$user_id} AND status = 0";

			if ($repeat_type)
			{
//				$sql .= "AND controller_control.repeat_type = $repeat_type ";
			}

			//FIXME
			if ($completed)
			{
				$sql .= " AND ( planned_date < $to_date AND controller_check_list.completed_date IS NULL) ";

//				$sql .= " AND ((deadline <= $to_date AND controller_control.end_date IS NULL) ";
//				$sql .= " OR (deadline <= $to_date AND deadline > $from_date ))";
//				$sql .= " AND controller_check_list.completed_date IS NULL ";			
			}
			else
			{
				$sql .= " AND (planned_date > $from_date AND planned_date <= $to_date AND controller_control.end_date IS NULL) ";
			}


			$this->db->query($sql);

			$check_list_array = array();

			while ($this->db->next_record())
			{
				$check_list = new controller_check_list($this->unmarshal($this->db->f('check_list_id'), 'int'));
				$check_list->set_control_id($this->unmarshal($this->db->f('control_id'), 'int'));
				$check_list->set_location_id($this->unmarshal($this->db->f('location_id'), 'int'));
				$check_list->set_component_id($this->unmarshal($this->db->f('component_id'), 'int'));
				$check_list->set_title($this->unmarshal($this->db->f('title', true), 'string'));
				$check_list->set_description($this->unmarshal($this->db->f('description', true), 'string'));
				$check_list->set_start_date($this->unmarshal($this->db->f('start_date'), 'int'));
				$check_list->set_end_date($this->unmarshal($this->db->f('end_date'), 'int'));
				$check_list->set_deadline($this->unmarshal($this->db->f('deadline'), 'int'));
				$check_list->set_control_area_id($this->unmarshal($this->db->f('control_area_id'), 'int'));
				$check_list->set_assigned_to($this->unmarshal($user_id, 'int'));
				$check_list->set_planned_date($this->unmarshal($this->db->f('planned_date'), 'int'));
				$check_list->set_completed_date($this->unmarshal($this->db->f('completed_date'), 'int'));
				$check_list->set_location_code($this->unmarshal($this->db->f('location_code', true), 'string'));

				if ($return_type == "return_object")
				{
					$check_list_array[] = $check_list;
				}
				else
				{
					$check_list_array[] = $check_list->toArray();
				}
			}

			return $check_list_array;
		}
//--------

		/**
		 * Get controls that should be carried out on a location within period
		 *
		 * @param $location_code the locaction code for the location the control should be carried out for   
		 * @param $from_date start date for period
		 * @param $to_date end date for period
		 * @param $repeat_type Dag, Uke, Måned, År 
		 * @param $return_type return data as objects or as arrays
		 * @param $role_id responsible role for carrying out the control  
		 * @return array with controls as objects or arrays
		 */
		public function get_controls_by_location( $location_code, $from_date, $to_date, $repeat_type, $return_type = "return_object", $role_id = 0 )
		{
			$role_id = (int)$role_id;
			$repeat_type = (int)$repeat_type;

			$controls_array = array();

			$sql = "SELECT distinct c.*, fm_responsibility_role.name AS responsibility_name,location_code ";
			$sql .= "FROM controller_control_location_list cll ";
			$sql .= "LEFT JOIN controller_control c on cll.control_id=c.id ";
			$sql .= "LEFT JOIN fm_responsibility_role ON fm_responsibility_role.id = c.responsibility_id ";
			//		$sql .= "WHERE cll.location_code = '$location_code' ";
			$sql .= "WHERE cll.location_code LIKE '$location_code%' ";
			if ($repeat_type)
			{
				$sql .= "AND c.repeat_type = $repeat_type ";
			}

			if ($role_id > 0)
			{
				$sql .= "AND c.responsibility_id = $role_id ";
			}

			$sql .= "AND ((c.start_date <= $to_date AND c.end_date IS NULL) ";
			$sql .= "OR (c.start_date <= $to_date AND c.end_date > $from_date ))";


			$this->db->query($sql);

			while ($this->db->next_record())
			{
				$_location_code = $this->db->f('location_code', true);

				$control = new controller_control($this->unmarshal($this->db->f('id'), 'int'));
				$control->set_title($this->unmarshal($this->db->f('title', true) . " [{$_location_code}]", 'string'));
				$control->set_description($this->unmarshal($this->db->f('description', true), 'string'));
				$control->set_start_date($this->unmarshal($this->db->f('start_date'), 'int'));
				$control->set_end_date($this->unmarshal($this->db->f('end_date'), 'int'));
				$control->set_procedure_id($this->unmarshal($this->db->f('procedure_id'), 'int'));
				$control->set_requirement_id($this->unmarshal($this->db->f('requirement_id'), 'int'));
				$control->set_costresponsibility_id($this->unmarshal($this->db->f('costresponsibility_id'), 'int'));
				$control->set_responsibility_id($this->unmarshal($this->db->f('responsibility_id'), 'int'));
				$control->set_responsibility_name($this->unmarshal($this->db->f('responsibility_name', true), 'string'));
				$control->set_control_area_id($this->unmarshal($this->db->f('control_area_id'), 'int'));
				$control->set_repeat_type($this->unmarshal($this->db->f('repeat_type'), 'int'));
				$control->set_repeat_type_label($this->unmarshal($this->db->f('repeat_type'), 'int'));
				$control->set_repeat_interval($this->unmarshal($this->db->f('repeat_interval'), 'int'));

				if ($return_type == "return_object")
				{
					$controls_array[] = $control;
				}
				else
				{
					$controls_array[] = $control->toArray();
				}
			}

			if (count($controls_array) > 0)
			{
				return $controls_array;
			}
			else
			{
				return null;
			}
		}

		/**
		 * Get controls that should be carried out on components on a location within period
		 *
		 * @param $location_code the locaction code for the location the control should be carried out for   
		 * @param $from_date start date for period
		 * @param $to_date end date for period
		 * @param $repeat_type Dag, Uke, Måned, År 
		 * @param $return_type return data as objects or as arrays
		 * @param $role_id responsible role for carrying out the control  
		 * @return array with controls as objects or arrays
		 */
		public function get_controls_for_components_by_location( $location_code, $from_date, $to_date, $repeat_type, $role_id = 0 )
		{
			$controls_array = array();

			$sql = "SELECT distinct c.*, fm_responsibility_role.name AS responsibility_name, ccl.location_id, ccl.component_id ";
			$sql .= "FROM controller_control_component_list ccl ";
			$sql .= "LEFT JOIN controller_control c on ccl.control_id = c.id ";
			$sql .= "LEFT JOIN fm_responsibility_role ON fm_responsibility_role.id = c.responsibility_id ";
			$sql .= "LEFT JOIN fm_bim_item ON fm_bim_item.id = ccl.component_id ";
			$sql .= "WHERE fm_bim_item.location_code LIKE '$location_code%' ";

			if ($repeat_type != null)
			{
				$repeat_type = (int)$repeat_type;
				$sql .= "AND c.repeat_type = $repeat_type ";
			}
			$role_id = (int)$role_id;

			if ($role_id > 1)
			{
				$sql .= "AND c.responsibility_id = $role_id ";
			}

			$sql .= "AND ((c.start_date <= $to_date AND c.end_date IS NULL) ";
			$sql .= "OR (c.start_date <= $to_date AND c.end_date > $from_date ))";

			$this->db->query($sql);

			while ($this->db->next_record())
			{

				$controls_array[] = array
					(
					'id' => $this->db->f('id'),
					'title' => $this->db->f('title', true),
					'description' => $this->db->f('description', true),
					'start_date' => $this->db->f('start_date'),
					'end_date' => $this->db->f('end_date'),
					'procedure_id' => $this->db->f('procedure_id'),
					'requirement_id' => $this->db->f('requirement_id'),
					'costresponsibility_id' => $this->db->f('costresponsibility_id'),
					'responsibility_id' => $this->db->f('responsibility_id'),
					'responsibility_name' => $this->db->f('responsibility_name', true),
					'control_area_id' => $this->db->f('control_area_id'),
					'repeat_type' => $this->db->f('repeat_type'),
					'repeat_interval' => $this->db->f('repeat_interval'),
					'component_id' => $this->db->f('component_id'),
					'location_id' => $this->db->f('location_id')
				);
			}

			return $controls_array;
		}

		/**
		 * Get components and populates array of controls that should be carried out on the components on a location within period
		 *
		 * @param $location_code the locaction code for the location the control should be carried out for   
		 * @param $from_date start date for period
		 * @param $to_date end date for period
		 * @param $repeat_type Dag, Uke, Måned, År 
		 * @param $return_type return data as objects or as arrays
		 * @param $role_id responsible role for carrying out the control  
		 * @return array of components as objects or arrays
		 */
		public function get_controls_by_component( $from_date, $to_date, $repeat_type, $return_type = "return_object", $role_id = 0, $filter = null )
		{
			$controls_array = array();

			$sql = "SELECT c.id as control_id, c.*, ";
			$sql .= "bim_item.type as component_type, bim_item.id as component_id, bim_item.location_code, bim_item.address, ";
			$sql .= "cl.location_id, fm_responsibility_role.name AS responsibility_name ";
			$sql .= "FROM controller_control_component_list cl ";
			$sql .= "JOIN fm_bim_item bim_item on cl.component_id = bim_item.id ";
			$sql .= "JOIN fm_bim_type bim_type on cl.location_id = bim_type.location_id ";
			$sql .= "JOIN controller_control c on cl.control_id = c.id ";
			$sql .= "JOIN fm_responsibility_role ON fm_responsibility_role.id = c.responsibility_id ";
			$sql .= "AND bim_item.type = bim_type.id ";

			if ($repeat_type != null)
			{
				$repeat_type = (int)$repeat_type;
				$sql .= "AND c.repeat_type = $repeat_type ";
			}
			if ($role_id)
			{
				$role_id = (int)$role_id;
				$sql .= "AND c.responsibility_id = $role_id ";
			}

			$sql .= "AND ((c.start_date <= $to_date AND c.end_date IS NULL) ";
			$sql .= "OR (c.start_date <= $to_date AND c.end_date > $from_date ))";

			if ($filter != null)
			{
				$sql .= "AND " . $filter;
			}

			$sql .= "ORDER BY bim_item.id ";

			$this->db->query($sql);

			$component_id = 0;
			$component = null;
			while ($this->db->next_record())
			{
				if ($this->db->f('component_id') != $component_id)
				{
					if ($component_id != 0)
					{
						$component->set_controls_array($controls_array);
						$controls_array = array();

						if ($return_type == "return_array")
						{
							$components_array[] = $component->toArray();
						}
						else
						{
							$components_array[] = $component;
						}
					}

					$component = new controller_component();
					$component->set_type($this->unmarshal($this->db->f('component_type'), 'int'));
					$component->set_id($this->unmarshal($this->db->f('component_id'), 'int'));
					$component->set_location_id($this->unmarshal($this->db->f('location_id'), 'int'));
					$component->set_guid($this->unmarshal($this->db->f('guid', true), 'string'));
					$component->set_location_code($this->unmarshal($this->db->f('location_code', true), 'string'));
					$component->set_loc_1($this->unmarshal($this->db->f('loc_1', true), 'string'));
					$component->set_address($this->unmarshal($this->db->f('address', true), 'string'));
				}

				$control = new controller_control($this->unmarshal($this->db->f('control_id'), 'int'));
				$control->set_title($this->unmarshal($this->db->f('title', true), 'string'));
				$control->set_description($this->unmarshal($this->db->f('description', true), 'string'));
				$control->set_start_date($this->unmarshal($this->db->f('start_date'), 'int'));
				$control->set_end_date($this->unmarshal($this->db->f('end_date'), 'int'));
				$control->set_procedure_id($this->unmarshal($this->db->f('procedure_id'), 'int'));
				$control->set_procedure_name($this->unmarshal($this->db->f('procedure_name', true), 'string'));
				$control->set_requirement_id($this->unmarshal($this->db->f('requirement_id'), 'int'));
				$control->set_costresponsibility_id($this->unmarshal($this->db->f('costresponsibility_id'), 'int'));
				$control->set_responsibility_id($this->unmarshal($this->db->f('responsibility_id'), 'int'));
				$control->set_responsibility_name($this->unmarshal($this->db->f('responsibility_name', true), 'string'));
				$control->set_control_area_id($this->unmarshal($this->db->f('control_area_id'), 'int'));
				$control->set_control_area_name($this->unmarshal($this->db->f('control_area_name', true), 'string'));
				$control->set_repeat_type($this->unmarshal($this->db->f('repeat_type'), 'int'));
				$control->set_repeat_type_label($this->unmarshal($this->db->f('repeat_type'), 'int'));
				$control->set_repeat_interval($this->unmarshal($this->db->f('repeat_interval'), 'int'));

				if ($return_type == "return_object")
				{
					$controls_array[] = $control;
				}
				else
				{
					$controls_array[] = $control->toArray();
				}

				$component_id = $component->get_id();
			}

			if ($component != null)
			{
				$component->set_controls_array($controls_array);

				if ($return_type == "return_array")
				{
					$components_array[] = $component->toArray();
				}
				else
				{
					$components_array[] = $component;
				}

				return $components_array;
			}
			else
			{
				return null;
			}
		}

		/**
		 * Get controls with a control area
		 *
		 * @param $control_area_id  
		 * @return array with controls as objects or arrays
		 */
		function get_controls_by_control_area( $control_area_id )
		{
			$control_area_id = (int)$control_area_id;
			$controls_array = array();

			$sql = "SELECT * FROM controller_control WHERE control_area_id=$control_area_id";
			$this->db->query($sql);

			while ($this->db->next_record())
			{
				$control = new controller_control($this->unmarshal($this->db->f('id'), 'int'));
				$control->set_title($this->unmarshal($this->db->f('title', true), 'string'));
				$control->set_description($this->unmarshal($this->db->f('description', true), 'string'));
				$control->set_start_date($this->unmarshal($this->db->f('start_date'), 'int'));
				$control->set_end_date($this->unmarshal($this->db->f('end_date'), 'int'));
				$control->set_procedure_id($this->unmarshal($this->db->f('procedure_id'), 'int'));
				$control->set_procedure_name($this->unmarshal($this->db->f('procedure_name', true), 'string'));
				$control->set_requirement_id($this->unmarshal($this->db->f('requirement_id'), 'int'));
				$control->set_costresponsibility_id($this->unmarshal($this->db->f('costresponsibility_id'), 'int'));
				$control->set_responsibility_id($this->unmarshal($this->db->f('responsibility_id'), 'int'));
				$control->set_control_area_id($this->unmarshal($this->db->f('control_area_id'), 'int'));
				$control->set_repeat_type($this->unmarshal($this->db->f('repeat_type'), 'int'));
				$control->set_repeat_interval($this->unmarshal($this->db->f('repeat_interval'), 'int'));

				$controls_array[] = $control->toArray();
			}

			if (count($controls_array) > 0)
			{
				return $controls_array;
			}
			else
			{
				return null;
			}
		}

		/**
		 * Get locations that a control should be carried out for
		 *
		 * @param int $control_id control id
		 * @param array $location_code_filter
		 * @return array with arrays of location info  
		 */
		function get_locations_for_control( $control_id )
		{
			$control_id = (int)$control_id;

			$controls_array = array();

			$sql = "SELECT c.id, c.title, cll.location_code, fm_locations.name as loc_name ";
			$sql .= "FROM controller_control c, controller_control_location_list cll, fm_locations ";
			$sql .= "WHERE cll.control_id = {$control_id} ";
			$sql .= "AND cll.control_id = c.id ";
			$sql .= "AND cll.location_code = fm_locations.location_code";

			$this->db->query($sql);

			while ($this->db->next_record())
			{
				$control_id = $this->unmarshal($this->db->f('id'), 'int');
				$title = $this->unmarshal($this->db->f('title', true), 'string');
				$location_code = $this->unmarshal($this->db->f('location_code', true), 'string');
				$loc_name = $this->db->f('loc_name', true);
				$loc_name_arr = explode(', ', $loc_name);

				/*
				  $location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code,'extra' => array('noattrib' => true)));

				  $location_arr = explode('-', $location_code);
				  $loc_name_arr = array();
				  $i = 1;
				  foreach ($location_arr as $_part)
				  {
				  $loc_name_arr[] = $location_array["loc{$i}_name"];
				  $i++;
				  }
				 */
				$controls_array[] = array
					(
					'id' => $control_id,
					'title' => $title,
					'location_code' => $location_code,
					'loc1_name' => $loc_name_arr[0],
					'loc_name' => $loc_name
				);
			}

			if (count($controls_array) > 0)
			{
				return $controls_array;
			}
			else
			{
				return null;
			}
		}

		//Not used ?
		/**
		 * Get arrays with component info that a control should be carried out on
		 *
		 * @param $control_id control id
		 * @return array with arrays of component info  
		 */
		function get_components_for_control( $control_id, $location_id = 0, $component_id = 0, $serie_id = 0, $user_id = 0 )
		{
			$control_id = (int)$control_id;
			$serie_id = (int)$serie_id;
			$user_id = (int)$user_id;

			$controls_array = array();

			$sql = "SELECT DISTINCT ccl.control_id, ccl.component_id as component_id,"
				. " ccl.location_id as location_id, ccs.id as serie_id, ccs.assigned_to, ccs.start_date,"
				. " ccs.repeat_type, ccs.repeat_interval, ccs.service_time, ccs.controle_time, ccs.enabled as serie_enabled,"
				. " bim_type.description, bim_item.location_code ";

			$sql .= "FROM controller_control_component_list ccl,controller_control_serie ccs, fm_bim_item bim_item, fm_bim_type bim_type ";
//controller_control_serie ON (controller_control_component_list.id = controller_control_serie.control_relation_id AND controller_control_serie.control_relation_type = 'component')"
			$sql .= "WHERE ccl.control_id = $control_id ";
			$sql .= "AND ccl.component_id = bim_item.id ";
			$sql .= "AND ccl.location_id = bim_type.location_id ";
			$sql .= "AND bim_type.id = bim_item.type ";
			$sql .= "AND ccl.id = ccs.control_relation_id ";
			$sql .= "AND ccs.control_relation_type = 'component'";

			if ($location_id && $component_id)
			{
				$sql .= " AND ccl.location_id = {$location_id} AND ccl.component_id = {$component_id}";
			}
			if ($serie_id)
			{
				$sql .= " AND ccs.id = {$serie_id}";
			}
			if ($user_id)
			{
				$sql .= " AND ccs.assigned_to = {$user_id}";
			}
//	_debug_array($sql);
			$this->db->query($sql);

			while ($this->db->next_record())
			{
				$control_relation = array
					(
					'serie_id' => $this->db->f('serie_id'),
					'assigned_to' => $this->db->f('assigned_to'),
					'start_date' => $this->db->f('start_date'),
					'repeat_type' => $this->db->f('repeat_type'),
					'repeat_interval' => $this->db->f('repeat_interval'),
					'service_time' => $this->db->f('service_time'),
					'controle_time' => $this->db->f('controle_time'),
					'serie_enabled' => (int)$this->db->f('serie_enabled')
				);

				$component = new controller_component();
				$component->set_type($this->unmarshal($this->db->f('type'), 'int'));
				$component->set_id($this->unmarshal($this->db->f('component_id'), 'int'));
				$component->set_location_id($this->unmarshal($this->db->f('location_id'), 'int'));
				$component->set_guid($this->unmarshal($this->db->f('guid', true), 'string'));
				$component->set_location_code($this->unmarshal($this->db->f('location_code', true), 'string'));
				$component->set_loc_1($this->unmarshal($this->db->f('loc_1', true), 'string'));
				$component->set_address($this->unmarshal($this->db->f('address', true), 'string'));
				$component->set_type_str($this->unmarshal($this->db->f('description', true), 'string'));
				$component->set_control_relation($control_relation);

				$components_array[] = $component;
			}
//	_debug_array($components_array);
			if (count($components_array) > 0)
			{
				return $components_array;
			}
			else
			{
				return null;
			}
		}

		/**
		 * Get arrays of control_location_list objects
		 *
		 * @param $control_id control id
		 * @param $location_code location code
		 * @return array with control_location_list objects  
		 */
		function get_control_location( $control_id, $location_code )
		{
			$control_id = (int)$control_id;
			$sql = "SELECT * ";
			$sql .= "FROM controller_control_location_list ";
			$sql .= "WHERE control_id = $control_id ";
			$sql .= "AND location_code = '$location_code'";

			$this->db->query($sql, __LINE__, __FILE__);

			if ($this->db->next_record())
			{
				$control_location = new controller_control_location($this->unmarshal($this->db->f('id'), 'int'));

				$control_location->set_location_code($this->unmarshal($this->db->f('location_code', true), 'string'));
				$control_location->set_control_id($this->unmarshal($this->db->f('control_id'), 'int'));

				return $control_location;
			}
			else
			{
				return null;
			}
		}

		public function check_control_component( $control_id, $location_id, $component_id )
		{
			$control_id = (int)$control_id;
			$location_id = (int)$location_id;
			$component_id = (int)$component_id;

			$sql = "SELECT * ";
			$sql .= "FROM controller_control_component_list ";
			$sql .= "WHERE control_id = {$control_id} ";
			$sql .= "AND location_id = {$location_id} ";
			$sql .= "AND component_id = {$component_id}";

			$this->db->query($sql, __LINE__, __FILE__);
			return $this->db->next_record();
		}

		public function get_assigned_control_components( $from_date, $to_date, $assigned_to = 0 , $control_id = 0)
		{
			if($assigned_to && is_array($assigned_to))
			{
				$_assigned_to = implode(',', $assigned_to);
				$filter_assigned_to = "AND (controller_control_serie.assigned_to IN ({$_assigned_to})  OR controller_check_list.assigned_to IN ({$_assigned_to}))";
			}
			else
			{
				$assigned_to = (int)$assigned_to;
				$filter_assigned_to = "AND (controller_control_serie.assigned_to = {$assigned_to}  OR controller_check_list.assigned_to = {$assigned_to})";
			}

			$location_id = (int)$location_id;
			$component_id = (int)$component_id;
			$control_id = (int)$control_id;


			$sql = "SELECT  distinct controller_control_component_list.component_id, controller_control_component_list.location_id"
				. " FROM controller_control_component_list"
				. " {$this->left_join} controller_control_serie ON (controller_control_component_list.id = controller_control_serie.control_relation_id)"
				. " {$this->left_join} controller_check_list ON controller_control_serie.id = controller_check_list.serie_id"
				. " WHERE controller_control_serie.control_relation_type = 'component'"
				. " {$filter_assigned_to}"
//				. " AND (controller_control_serie.assigned_to = {$assigned_to}  OR controller_check_list.assigned_to = {$assigned_to})"
				. " AND controller_control_serie.enabled = 1 AND controller_control_serie.start_date <= {$to_date}";

			if($control_id)
			{
				$sql .= " AND controller_control_component_list.control_id = {$control_id}";
			}

			$this->db->query($sql, __LINE__, __FILE__);
			$components = array();

			while ($this->db->next_record())
			{
				$location_id = $this->db->f('location_id');
				$components[$location_id][] = $this->db->f('component_id');
			}
			return $components;
		}

		/**
		 * Register that a control should be carried out on a location
		 *
		 * @param $data['control_id'] control id
		 * @param $data['component_id'] component id
		 * @param $data['location_id'] component id
		 * @return true or false if the execution was successful
		 */
		function register_control_to_location( $data )
		{

			$delete_component = array();
			$add_component = array();
			$this->db->transaction_begin();

			if (isset($data['register_location']) && is_array($data['register_location']))
			{
				foreach ($data['register_location'] as $location_info)
				{
					$location_arr = explode('_', $location_info);
					if (count($location_arr) != 2)
					{
						continue;
					}

					$control_id = (int)$location_arr[0];
					$location_code = $location_arr[1];

					if (!$control_id)
					{
						return false;
					}

					$sql = "SELECT * ";
					$sql .= "FROM controller_control_location_list ";
					$sql .= "WHERE control_id = {$control_id} ";
					$sql .= "AND location_code = '{$location_code}' ";

					$this->db->query($sql, __LINE__, __FILE__);

					if (!$this->db->next_record())
					{
						$sql = "INSERT INTO controller_control_location_list (control_id, location_code) ";
						$sql .= "VALUES ( {$control_id}, '{$location_code}')";

						$this->db->query($sql);
					}
				}
			}

			if (isset($data['delete']) && is_array($data['delete']))
			{
				foreach ($data['delete'] as $location_info)
				{
					$location_arr = explode('_', $location_info);
					if (count($location_arr) != 2)
					{
						continue;
					}

					$control_id = (int)$location_arr[0];
					$location_code = $location_arr[1];

					$sql = "DELETE FROM controller_control_location_list WHERE control_id = {$control_id} AND location_code = '{$location_code}'";
					$this->db->query($sql);
				}
			}

			return $this->db->transaction_commit();
		}

		/**
		 * Register that a control should be carried out on a component
		 *
		 * @param $data['control_id'] control id
		 * @param $data['component_id'] component id
		 * @param $data['location_id'] component id
		 * @return true or false if the execution was successful
		 */
		function register_control_to_component( $data )
		{
			$ret = 0;
			$assigned_to = isset($data['assigned_to']) && $data['assigned_to'] ? $data['assigned_to'] : null;
			$start_date = isset($data['start_date']) && $data['start_date'] ? $data['start_date'] : null;
			$repeat_type = isset($data['repeat_type']) && $data['repeat_type'] ? $data['repeat_type'] : null;
			$repeat_interval = isset($data['repeat_interval']) && $data['repeat_interval'] ? $data['repeat_interval'] : null;
			$controle_time = isset($data['controle_time']) && $data['controle_time'] ? $data['controle_time'] : null;
			$service_time = isset($data['service_time']) && $data['service_time'] ? $data['service_time'] : null;
			$duplicate = isset($data['duplicate']) && $data['duplicate'] ? $data['duplicate'] : null;

			$delete_component = array();
			$add_component = array();
			if ($this->db->get_transaction())
			{
				$this->global_lock = true;
			}
			else
			{
				$this->db->transaction_begin();
			}

			if (isset($data['register_component']) && is_array($data['register_component']))
			{
				foreach ($data['register_component'] as $component_info)
				{
					$component_arr = explode('_', $component_info);
					if (count($component_arr) != 3)
					{
						continue;
					}

					$control_id = (int)$component_arr[0];
					$location_id = (int)$component_arr[1];
					$component_id = (int)$component_arr[2];

					if (!$control_id)
					{
						return false;
					}

					$sql = "SELECT * ";
					$sql .= "FROM controller_control_component_list ";
					$sql .= "WHERE control_id = {$control_id} ";
					$sql .= "AND location_id = {$location_id} ";
					$sql .= "AND component_id = {$component_id}";

					$this->db->query($sql, __LINE__, __FILE__);
					$this->db->next_record();
					$relation_id = $this->db->f('id');
					if (!$relation_id || $duplicate)
					{
						if ($relation_id)
						{
							$sql = "SELECT * FROM controller_control_serie"
								. " WHERE control_relation_id = {$relation_id}"
								. " AND repeat_type = {$repeat_type}"
								. " AND repeat_interval = {$repeat_interval}";
							$this->db->query($sql, __LINE__, __FILE__);
							$this->db->next_record();
							$serie_id = $this->db->f('id');
							if ($serie_id)
							{
								$this->update_control_serie($data = array(
									'ids' => array($serie_id),
									'action' => 'edit',
									'assigned_to' => $assigned_to,
									'start_date' => $start_date,
									'repeat_type' => $repeat_type,
									'repeat_interval' => $repeat_interval,
									'controle_time' => $controle_time,
									'service_time' => $service_time,
								));
								$ret = $this->update_control_serie($data = array(
									'ids' => array($serie_id),
									'action' => 'enable',
								));
								continue;
							}
						}

						$values_insert = array
							(
							'control_id' => $control_id,
							'location_id' => $location_id,
							'component_id' => $component_id,
						);

						$this->db->query("INSERT INTO controller_control_component_list (" . implode(',', array_keys($values_insert)) . ') VALUES ('
							. $this->db->validate_insert(array_values($values_insert)) . ')', __LINE__, __FILE__);
						$relation_id = $this->db->get_last_insert_id('controller_control_component_list', 'id');

						$values_insert = array
							(
							'control_relation_id' => $relation_id,
							'control_relation_type' => 'component',
							'assigned_to' => $assigned_to,
							'start_date' => $start_date,
							'repeat_type' => $repeat_type,
							'repeat_interval' => $repeat_interval,
							'controle_time' => $controle_time,
							'service_time' => $service_time,
						);

						$this->db->query("INSERT INTO controller_control_serie (" . implode(',', array_keys($values_insert)) . ') VALUES ('
							. $this->db->validate_insert(array_values($values_insert)) . ')', __LINE__, __FILE__);

						$assigned_date = time();
						$serie_id = $this->db->get_last_insert_id('controller_control_serie', 'id');
						$this->db->query("INSERT INTO controller_control_serie_history (serie_id, assigned_to, assigned_date)"
							. " VALUES ({$serie_id}, {$assigned_to}, {$assigned_date})  ");

						$ret = PHPGW_ACL_ADD; // Bit - add
					}
				}
			}

			if (isset($data['delete']) && is_array($data['delete']))
			{
				foreach ($data['delete'] as $component_info)
				{
					$component_arr = explode('_', $component_info);
					if (count($component_arr) != 3)
					{
						continue;
					}

					$control_id = (int)$component_arr[0];
					$location_id = (int)$component_arr[1];
					$component_id = (int)$component_arr[2];

					$sql = "DELETE FROM controller_control_component_list WHERE control_id = {$control_id} AND location_id = {$location_id} AND component_id = {$component_id}";
					$this->db->query($sql);
				}
				$ret += PHPGW_ACL_DELETE; //bit - delete
			}

			if (!$this->global_lock)
			{
				$this->db->transaction_commit();
			}

			return $ret;
		}

		/**
		 * Register that a control should be carried out on a component
		 *
		 * @param $control_id control id
		 * @param $component_id component id
		 * @return void  
		 */
		function add_component_to_control( $control_id, $component_id )
		{
			$sql = "INSERT INTO controller_control_component_list (control_id, component_id) values($control_id, $component_id)";
			$this->db->query($sql);
		}

		/**
		 * Get all controls assosiated with a user
		 *
		 * @param type $assigned_to
		 * @return array controls assosiated with a assigned user
		 */

		function get_controls_for_assigned( $assigned_to )
		{
			$assigned_to = (int) $assigned_to;

			if($assigned_to)
			{
				$assigned_to_name = $GLOBALS['phpgw']->accounts->get($assigned_to)->__toString();
			}

			static $users = array(); // cache result

			$sql = "SELECT controller_control_component_list.* ,"
				. " controller_control.title, controller_control.enabled as control_enabled,"
				. " controller_control_component_list.enabled as relation_enabled,"
				. " controller_control_serie.enabled as serie_enabled,"
				. " controller_control_serie.id as serie_id,"
				. " controller_control_serie.assigned_to,controller_control_serie.start_date,"
				. " controller_control_serie.repeat_type,controller_control_serie.repeat_interval,"
				. " controller_control_serie.service_time,controller_control_serie.controle_time "
				. " FROM controller_control_component_list"
				. " {$this->db->join} controller_control ON controller_control.id = controller_control_component_list.control_id"
				. " {$this->db->left_join} controller_control_serie ON (controller_control_component_list.id = controller_control_serie.control_relation_id AND controller_control_serie.control_relation_type = 'component')"
				. " WHERE controller_control_serie.assigned_to = {$assigned_to}";
//			_debug_array($sql);
			$this->db->query($sql, __LINE__, __FILE__);
			$controls = array();

			while ($this->db->next_record())
			{
				$controls[] = array
					(
					'id' => $this->db->f('id'),
					'serie_id' => $this->db->f('serie_id'),
					'control_id' => $this->db->f('control_id'),
					'title' => $this->db->f('title', true),
					'location_id' => $this->db->f('location_id'),
					'component_id' => $this->db->f('component_id'),
					'assigned_to' => $this->db->f('assigned_to'),
					'assigned_to_name' => $assigned_to_name,
					'start_date' => $this->db->f('start_date'),
					'repeat_type' => $this->db->f('repeat_type'),
					'repeat_interval' => $this->db->f('repeat_interval'),
					'control_enabled' => $this->db->f('control_enabled'),
					'relation_enabled' => $this->db->f('relation_enabled'),
					'serie_enabled' => $this->db->f('serie_enabled'),
					'service_time' => (float)$this->db->f('service_time'),
					'controle_time' => (float)$this->db->f('controle_time'),
				);
			}
			return $controls;
		}

		/**
		 * Get all controls assosiated with a component
		 * 
		 * @param array $data location_id and component_id
		 * @return array controls assosiated with a component
		 * @throws Exception if missing valid input
		 */
		function get_controls_at_component( $data )
		{
			if (!isset($data['location_id']) || !$data['location_id'])
			{
				throw new Exception("controller_socontrol::get_controls_at_component - Missing location_id in input");
			}
			if (!isset($data['component_id']) || !$data['component_id'])
			{
				throw new Exception("controller_socontrol::get_controls_at_component - Missing component_id in input");
			}

			static $users = array(); // cache result

			$location_id = (int)$data['location_id'];
			$component_id = (int)$data['component_id'];

			$sql = "SELECT controller_control_component_list.* ,"
				. " controller_control.title, controller_control.enabled as control_enabled,"
				. " controller_control_component_list.enabled as relation_enabled,"
				. " controller_control_serie.enabled as serie_enabled,"
				. " controller_control_serie.id as serie_id,"
				. " controller_control_serie.assigned_to,controller_control_serie.start_date,"
				. " controller_control_serie.repeat_type,controller_control_serie.repeat_interval,"
				. " controller_control_serie.service_time,controller_control_serie.controle_time "
				. " FROM controller_control_component_list"
				. " {$this->db->join} controller_control ON controller_control.id = controller_control_component_list.control_id"
				. " {$this->db->left_join} controller_control_serie ON (controller_control_component_list.id = controller_control_serie.control_relation_id AND controller_control_serie.control_relation_type = 'component')"
				. " WHERE location_id = {$location_id} AND component_id = {$component_id}";
//			_debug_array($sql);
			$this->db->query($sql, __LINE__, __FILE__);
			$controls = array();

			while ($this->db->next_record())
			{
				$controls[] = array
					(
					'id' => $this->db->f('id'),
					'serie_id' => $this->db->f('serie_id'),
					'control_id' => $this->db->f('control_id'),
					'title' => $this->db->f('title', true),
					'location_id' => $this->db->f('location_id'),
					'component_id' => $this->db->f('component_id'),
					'assigned_to' => $this->db->f('assigned_to'),
					'start_date' => $this->db->f('start_date'),
					'repeat_type' => $this->db->f('repeat_type'),
					'repeat_interval' => $this->db->f('repeat_interval'),
					'control_enabled' => $this->db->f('control_enabled'),
					'relation_enabled' => $this->db->f('relation_enabled'),
					'serie_enabled' => $this->db->f('serie_enabled'),
					'service_time' => (float)$this->db->f('service_time'),
					'controle_time' => (float)$this->db->f('controle_time'),
				);
			}

			foreach ($controls as &$entry)
			{
				if ($entry['assigned_to'] && !isset($users[$entry['assigned_to']]))
				{
					$users[$entry['assigned_to']] = $GLOBALS['phpgw']->accounts->get($entry['assigned_to'])->__toString();
				}
				$entry['assigned_to_name'] = $users[$entry['assigned_to']];
			}
			return $controls;
		}


		/**
		 * Get all register types associated with a control
		 *
		 * @param type $control_id
		 * @return array
		 */
		function get_system_locations_related_to_control($control_id = 0)
		{
			$locations = array();
			if(!$control_id)
			{
				return $locations;
			}
			$sql = "SELECT DISTINCT location_id"
				. " FROM controller_control_component_list"
				. " WHERE  control_id = {$control_id} ";

			$this->db->query($sql, __LINE__, __FILE__);

			while ($this->db->next_record())
			{
				$locations[] = $this->db->f('location_id');
			}

			return $locations;

		}


		/**
		 * Get all controls associated with a component
		 *
		 * @param array $data location_id and component_id
		 * @return array controls assosiated with a component
		 * @throws Exception if missing valid input
		 */
		function get_controls_at_component2( $data , $control_id = 0)
		{
			if (!isset($data['location_id']) || !$data['location_id'])
			{
				throw new Exception("controller_socontrol::get_controls_at_component - Missing location_id in input");
			}
			if (!isset($data['id']) || !$data['id'])
			{
				throw new Exception("controller_socontrol::get_controls_at_component - Missing component_id in input");
			}

			$control_id = (int) $control_id;

			if($control_id)
			{
				$filter_control = "AND controller_control.id = {$control_id}";
			}

			static $users = array(); // cache result

			$location_id = (int)$data['location_id'];
			$component_id = (int)$data['id'];

			$sql = "SELECT DISTINCT controller_control_component_list.* ,"
				. " controller_control.id as control_id, controller_control.title, controller_control.enabled as control_enabled,"
				. " controller_control_component_list.enabled as relation_enabled,"
				. " controller_control_serie.enabled as serie_enabled,"
				. " controller_control_serie.id as serie_id,"
				. " controller_control_serie.assigned_to,controller_control_serie.start_date,"
				. " controller_control_serie.repeat_type,controller_control_serie.repeat_interval,"
				. " controller_control_serie.service_time,controller_control_serie.controle_time "
				. " FROM controller_control_component_list"
				. " {$this->db->join} controller_control ON controller_control.id = controller_control_component_list.control_id"
				. " {$this->db->left_join} controller_control_serie ON (controller_control_component_list.id = controller_control_serie.control_relation_id AND controller_control_serie.control_relation_type = 'component')"
				. " WHERE location_id = {$location_id} AND component_id = {$component_id} {$filter_control}"
				. " ORDER BY repeat_type, repeat_interval";
//			_debug_array($sql);
			$this->db->query($sql, __LINE__, __FILE__);

			$components_array = array();
			$control_relations = array();

			while ($this->db->next_record())
			{
				$control_relations[] = array
					(
					'control_id' => $this->db->f('control_id'),
					'serie_id' => $this->db->f('serie_id'),
					'assigned_to' => $this->db->f('assigned_to'),
					'start_date' => $this->db->f('start_date'),
					'repeat_type' => $this->db->f('repeat_type'),
					'repeat_interval' => $this->db->f('repeat_interval'),
					'service_time' => $this->db->f('service_time'),
					'controle_time' => $this->db->f('controle_time'),
					'serie_enabled' => (int)$this->db->f('serie_enabled')
				);
			}

			foreach ($control_relations as &$entry)
			{
				if ($entry['assigned_to'] && !isset($users[$entry['assigned_to']]))
				{
					$users[$entry['assigned_to']] = $GLOBALS['phpgw']->accounts->get($entry['assigned_to'])->__toString();
				}
				$entry['assigned_to_name'] = $users[$entry['assigned_to']];

				$component = new controller_component();
//				$component->set_type($this->unmarshal($data['bim_type'], 'int'));
				$component->set_id($component_id);
				$component->set_location_id($location_id);
				$component->set_guid($this->unmarshal($data['guid'], 'string'));
				$component->set_location_code($this->unmarshal($data['location_code'], 'string'));
				$component->set_loc_1($this->unmarshal($data['loc_1'], 'string'));
				$component->set_address($this->unmarshal($data['address'], 'string'));
//				$component->set_type_str($this->unmarshal($data['bim_type_description']), 'string'));
				$component->set_control_relation($entry);

				$components_array[] = $component;
			}



			return $components_array;
		}


		function get_checklist_at_time_and_place( $part_of_town_id , $control_id = 0, $timestamp_start, $timestamp_end)
		{

			$checklist_item = array();

			$control_id = (int) $control_id;

			if(!$control_id)
			{
				return $checklist_item;
			}

//			$sql = " SELECT DISTINCT controller_check_list.id AS check_list_id, end_date, deadline,planned_date, completed_date, controller_check_list.location_id,title,controller_check_list.component_id"
			$sql = " SELECT DISTINCT controller_check_list.location_id,controller_check_list.component_id"
				. " FROM controller_check_list"
				. " {$this->join} controller_control ON controller_check_list.control_id = controller_control.id"
				. " {$this->join} controller_control_component_list "
				. " ON (controller_control_component_list.control_id = controller_check_list.control_id"
				. " AND controller_control_component_list.location_id = controller_check_list.location_id"
				. " AND controller_control_component_list.component_id = controller_check_list.component_id)"
				. " {$this->join} fm_bim_item  ON (controller_control_component_list.location_id = fm_bim_item.location_id"
				. " AND controller_control_component_list.component_id = fm_bim_item.id)"
				. " {$this->join} fm_location1  ON (fm_bim_item.loc1 = fm_location1.loc1)"
				. " WHERE part_of_town_id = {$part_of_town_id}"
				. " AND controller_control.id = {$control_id}"
				. " AND (planned_date >= $timestamp_start AND planned_date <= $timestamp_end)";

//			_debug_array($sql);
			$this->db->query($sql, __LINE__, __FILE__);

			while($this->db->next_record())
			{
				$location_id = (int)$this->db->f('location_id');

				$checklist_item[$location_id][] = (int)$this->db->f('component_id');

			}

			return $checklist_item;
		}

		function get_id_field_name( $extended_info = false )
		{
			if (!$extended_info)
			{
				$ret = 'id';
			}
			else
			{
				$ret = array
					(
					'table' => 'controller_control', // alias
					'field' => 'id',
					'translated' => 'id'
				);
			}

			return $ret;
		}

		protected function get_query( string $sort_field, bool $ascending, string $search_for, string $search_type, array $filters, bool $return_count )
		{
			$clauses = array('1=1');

			$filter_clauses = array();

			// Search for based on search type
			if ($search_for)
			{
				$search_for = $this->marshal($search_for, 'field');
				$like_pattern = "'%" . $search_for . "%'";
				$like_clauses = array();
				switch ($search_type)
				{
					default:
						$like_clauses[] = "controller_control.title $this->like $like_pattern";
						$like_clauses[] = "controller_control.description $this->like $like_pattern";
						break;
				}

				if (count($like_clauses))
				{
					$clauses[] = '(' . join(' OR ', $like_clauses) . ')';
				}
			}

			if (isset($filters[$this->get_id_field_name()]))
			{
				$filter_clauses[] = "controller_control.id = {$this->marshal($filters[$this->get_id_field_name()], 'int')}";
			}
			if (isset($filters['control_areas']))
			{
//				$filter_clauses[] = "controller_control.control_area_id = {$this->marshal($filters['control_areas'],'int')}";

				$cat_id = (int)$filters['control_areas'];
				$cats = CreateObject('phpgwapi.categories', -1, 'controller', '.control');
				$cats->supress_info = true;
				$cat_list = $cats->return_sorted_array(0, false, '', '', '', false, $cat_id, false);
				$cat_filter = array($cat_id);
				foreach ($cat_list as $_category)
				{
					$cat_filter[] = $_category['id'];
				}

				$filter_clauses[] = "controller_control.control_area_id IN (" . implode(',', $cat_filter) . ')';
			}
			if (isset($filters['responsibilities']))
			{
				$filter_clauses[] = "controller_control.responsibility_id = {$this->marshal($filters['responsibilities'], 'int')}";
			}

			if ($filters['district_id'])
			{
				$sql = "SELECT DISTINCT control_id"
					. " FROM controller_control_location_list {$this->join} fm_locations ON controller_control_location_list.location_code = fm_locations.location_code"
					. " {$this->join} fm_location1 ON fm_locations.loc1 = fm_location1.loc1"
					. " {$this->join} fm_part_of_town ON fm_location1.part_of_town_id = fm_part_of_town.id"
					. " WHERE district_id =" . (int)$filters['district_id'];

				$db = & $GLOBALS['phpgw']->db;
				$db->query($sql, __LINE__, __FILE__);

				$control_at_district = array();
				while ($db->next_record())
				{
					$control_at_district[] = $db->f('control_id');
				}

				$sql = "SELECT DISTINCT control_id"
					. " FROM controller_control_component_list {$this->join} fm_bim_item ON controller_control_component_list.location_id = fm_bim_item.location_id AND controller_control_component_list.component_id = fm_bim_item.id"
					. " {$this->join} fm_location1 ON fm_bim_item.loc1 = fm_location1.loc1"
					. " {$this->join} fm_part_of_town ON fm_location1.part_of_town_id = fm_part_of_town.id"
					. " WHERE district_id =" . (int)$filters['district_id'];

				$db->query($sql, __LINE__, __FILE__);

				while ($db->next_record())
				{
					$control_at_district[] = $db->f('control_id');
				}

				if ($control_at_district)
				{
					$filter_clauses[] = "controller_control.id IN (" . implode(',', array_unique($control_at_district)) . ')';
				}
			}

			if (count($filter_clauses))
			{
				$clauses[] = join(' AND ', $filter_clauses);
			}

			$condition = join(' AND ', $clauses);

			$tables = "controller_control";
			$joins .= " {$this->left_join} controller_procedure ON (controller_control.procedure_id = controller_procedure.id)";
			$joins .= " {$this->left_join} fm_responsibility_role ON (controller_control.responsibility_id = fm_responsibility_role.id)";

			if ($return_count)
			{
				$cols = 'COUNT(DISTINCT(controller_control.id)) AS count';
			}
			else
			{
				$cols = 'controller_control.id, controller_control.title, controller_control.description, controller_control.start_date, controller_control.end_date, controller_control.procedure_id, controller_control.control_area_id, controller_control.requirement_id, controller_control.costresponsibility_id, controller_control.responsibility_id, controller_control.repeat_type, controller_control.repeat_interval, controller_control.enabled, controller_procedure.title AS procedure_name, fm_responsibility_role.name AS responsibility_name ';
			}

			$dir = $ascending ? 'ASC' : 'DESC';
			if ($sort_field == 'title')
			{
				$sort_field = 'controller_control.title';
			}
			else if ($sort_field == 'id')
			{
				$sort_field = 'controller_control.id';
			}
			$order = $sort_field ? "ORDER BY {$this->marshal($sort_field, 'field')} $dir " : '';

			return "SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}";
		}

		function populate( int $control_id, &$control )
		{
			if ($control == null)
			{
				$control = new controller_control((int)$control_id);

				$control->set_title($this->unmarshal($this->db->f('title', true), 'string'));
				$control->set_description($this->unmarshal($this->db->f('description', true), 'string'));
				$control->set_start_date($this->unmarshal($this->db->f('start_date'), 'int'));
				$control->set_end_date($this->unmarshal($this->db->f('end_date'), 'int'));
				$control->set_procedure_id($this->unmarshal($this->db->f('procedure_id'), 'int'));
				$control->set_procedure_name($this->unmarshal($this->db->f('procedure_name', true), 'string'));
				$control->set_requirement_id($this->unmarshal($this->db->f('requirement_id'), 'int'));
				$control->set_costresponsibility_id($this->unmarshal($this->db->f('costresponsibility_id'), 'int'));
				$control->set_responsibility_id($this->unmarshal($this->db->f('responsibility_id'), 'int'));
				$control->set_responsibility_name($this->unmarshal($this->db->f('responsibility_name', true), 'string'));
				$control->set_control_area_id($this->unmarshal($this->db->f('control_area_id'), 'int'));
				$control->set_repeat_type($this->unmarshal($this->db->f('repeat_type'), 'int'));
				$control->set_repeat_interval($this->unmarshal($this->db->f('repeat_interval'), 'int'));
				$control->set_ticket_cat_id($this->unmarshal($this->db->f('ticket_cat_id'), 'int'));

				$category = execMethod('phpgwapi.categories.return_single', $this->unmarshal($this->db->f('control_area_id'), 'int'));
				$control->set_control_area_name($category[0]['name']);
			}

			return $control;
		}

		/**
		 * Get single control
		 * 
		 * @param	$id	id of the control to return
		 * @return a controller_control object
		 */
		function get_single( $id )
		{
			$id = (int)$id;

			$joins .= " {$this->left_join} controller_procedure ON (c.procedure_id = controller_procedure.id)";
			$joins .= " {$this->left_join} fm_responsibility_role ON (c.responsibility_id = fm_responsibility_role.id)";

			$sql = "SELECT c.*, controller_procedure.title AS procedure_name, fm_responsibility_role.name AS responsibility_name ";
			$sql .= "FROM controller_control c {$joins} ";
			$sql .= "WHERE c.id = " . $id;

			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();

			$control = new controller_control((int)$id);
			$control->set_title($this->unmarshal($this->db->f('title', true), 'string'));
			$control->set_description($this->unmarshal($this->db->f('description', true), 'string'));
			$control->set_start_date($this->unmarshal($this->db->f('start_date'), 'int'));
			$control->set_end_date($this->unmarshal($this->db->f('end_date'), 'int'));
			$control->set_procedure_id($this->unmarshal($this->db->f('procedure_id'), 'int'));
			$control->set_procedure_name($this->unmarshal($this->db->f('procedure_name', true), 'string'));
			$control->set_requirement_id($this->unmarshal($this->db->f('requirement_id'), 'int'));
			$control->set_costresponsibility_id($this->unmarshal($this->db->f('costresponsibility_id'), 'int'));
			$control->set_responsibility_id($this->unmarshal($this->db->f('responsibility_id'), 'int'));
			$control->set_responsibility_name($this->unmarshal($this->db->f('responsibility_name', true), 'string'));
			$control->set_control_area_id($this->unmarshal($this->db->f('control_area_id'), 'int'));
			$control->set_repeat_type($this->unmarshal($this->db->f('repeat_type'), 'int'));
			$control->set_repeat_type_label($control->get_repeat_type());
			$control->set_repeat_interval($this->unmarshal($this->db->f('repeat_interval'), 'int'));
			$control->set_ticket_cat_id($this->unmarshal($this->db->f('ticket_cat_id'), 'int'));

			$category = execMethod('phpgwapi.categories.return_single', $this->unmarshal($this->db->f('control_area_id'), 'int'));
			$control->set_control_area_name($category[0]['name']);

			return $control;
		}

		function get_roles()
		{
			$ret_array = array();
			$ret_array[0] = array('id' => 0, 'name' => lang('Not selected'));
			$sql = "select * from fm_responsibility_role ORDER BY name";
			$this->db->query($sql, __LINE__, __FILE__);
			$i = 1;
			while ($this->db->next_record())
			{
				$ret_array[$i]['id'] = $this->db->f('id');
				$ret_array[$i]['name'] = $this->db->f('name');
				$i++;
			}
			return $ret_array;
		}

		function get_bim_types( $ifc = null )
		{
			$ret_array = array();
			if ($ifc != null)
			{
				if ($ifc == 1)
				{
					$where_clause = "WHERE is_ifc";
				}
				else
				{
					$where_clause = "WHERE NOT is_ifc";
				}
			}
			$sql = "select * from fm_bim_type {$where_clause} ORDER BY name";
			$this->db->query($sql, __LINE__, __FILE__);
			$i = 1;
			while ($this->db->next_record())
			{
				$ret_array[$i]['id'] = $this->db->f('id');
				$ret_array[$i]['name'] = $this->db->f('name', true);
				$i++;
			}
			return $ret_array;
		}

		public function get_control_component( $noOfObjects = null, $bim_type = null )
		{
			$filters = array();
			if ($noOfObjects != null && is_numeric($noOfObjects))
			{
				$limit = "LIMIT {$noOfObjects}";
			}
			else
			{
				$limit = "LIMIT 10";
			}

			$joins = " {$this->left_join} controller_control_component_list ON (c.id = controller_control_component_list.control_id)";
			$joins .= " {$this->left_join} fm_bim_item ON (controller_control_component_list.component_id = fm_bim_item.id)";
			$joins .= " {$this->left_join} fm_bim_type ON (fm_bim_item.type= fm_bim_type.id)";

			$sql = "SELECT c.id AS control_id, c.title AS control_title, fm_bim_type.name AS type_name, fm_bim_item.id AS bim_id, fm_bim_item.guid as bim_item_guid FROM controller_control c {$joins} {$limit}";

			$controlArray = array();
			$this->db->query($sql, __LINE__, __FILE__);
			$i = 1;
			while ($this->db->next_record())
			{
				$controlArray[$i]['id'] = $this->db->f('control_id');
				$controlArray[$i]['title'] = $this->db->f('control_title');
				$controlArray[$i]['bim_id'] = $this->db->f('bim_id');
				$controlArray[$i]['bim_item_guid'] = $this->db->f('bim_item_guid', true);
				$controlArray[$i]['bim_type'] = $this->db->f('type_name', true);
				$i++;
			}

			return $controlArray;
		}

		public function getBimItemAttributeValue( $bimItemGuid, $attribute )
		{
			$columnAlias = "attribute_values";
//			$sql = "select array_to_string(xpath('descendant-or-self::*[{$attribute}]/{$attribute}/text()', (select xml_representation from fm_bim_item where guid='{$bimItemGuid}')), ',') as $columnAlias";
			$sql = "SELECT json_representation->>'{$attribute}' AS $columnAlias FROM fm_bim_item"
				. " WHERE guid='{$bimItemGuid}'";

			$this->db->query($sql, __LINE__, __FILE__);
			if ($this->db->num_rows() > 0)
			{
				$this->db->next_record();
				$result = $this->db->f($columnAlias, true);
				return $result;
			}
		}

		public function getLocationCodeFromControl( $control_id )
		{
			$sql = "select location_code from controller_control_location_list where control_id={$control_id}";
			$this->db->query($sql, __LINE__, __FILE__);
			if ($this->db->num_rows() > 0)
			{
				$this->db->next_record();
				$result = $this->db->f('location_code');
				return $result;
			}
		}

		function update_control_serie( $data = array() )
		{
			if (!isset($data['ids']) || !$data['ids'])
			{
				throw new Exception("controller_socontrol::update_control_serie - Missing ids in input");
			}
			if (!isset($data['action']) || !$data['action'])
			{
				throw new Exception("controller_socontrol::update_control_serie - Missing action in input");
			}

			$ids = $data['ids'];
			$action = $data['action'];
			$value_set = array();
			$add_history = false;
			switch ($action)
			{
				case 'enable':
					$value_set['enabled'] = 1;
					break;
				case 'disable':
					$value_set['enabled'] = 0;
					break;
				case 'edit':
					if ($data['assigned_to'])
					{
						$value_set['assigned_to'] = $data['assigned_to'];
						$add_history = true;
					}
					if ($data['start_date'])
					{
						$value_set['start_date'] = $data['start_date'];
					}
					if ($data['repeat_type'])
					{
						$value_set['repeat_type'] = $data['repeat_type'];
					}
					if ($data['repeat_interval'])
					{
						$value_set['repeat_interval'] = $data['repeat_interval'];
					}
					if ($data['controle_time'])
					{
						$value_set['controle_time'] = $data['controle_time'];
					}
					if ($data['service_time'])
					{
						$value_set['service_time'] = $data['service_time'];
					}
					break;
				default:
					throw new Exception("controller_socontrol::update_control_serie - not av valid action: '{$action}'");
					break;
			}

			if(!$value_set)
			{
				return 0;
			}

			$value_set_update = $this->db->validate_update($value_set);

			$sql = "UPDATE controller_control_serie SET {$value_set_update} WHERE id IN (" . implode(',', $ids) . ')';
			if ($this->db->query($sql, __LINE__, __FILE__))
			{
				if ($add_history && $value_set['assigned_to'])
				{
					$assigned_date = time();

					foreach ($ids as $serie_id)
					{
						$this->db->query("SELECT assigned_to FROM controller_control_serie_history WHERE serie_id = {$serie_id} ORDER BY id DESC", __LINE__, __FILE__);
						$this->db->next_record();
						if ($value_set['assigned_to'] != $this->db->f('assigned_to'))
						{
							$this->db->query("INSERT INTO controller_control_serie_history (serie_id, assigned_to, assigned_date)"
								. " VALUES ({$serie_id}, {$value_set['assigned_to']}, {$assigned_date})  ");
						}
					}
				}

				return PHPGW_ACL_EDIT; // Bit - edit
			}
		}

		function get_next_start_date($start_date, $repeat_type, $repeat_interval)
		{
			$next_date = $start_date;
			$now = time();
			while ($next_date < $now)
			{
				$interval_date = $next_date;
				$return_date = $next_date;

				if ($repeat_type == 0)
				{
					$next_date = mktime(0, 0, 0, date("m", $interval_date), date("d", $interval_date) + $repeat_interval, date("Y", $interval_date));
				}
				else if ($repeat_type == 1)
				{
					$next_date = mktime(0, 0, 0, date("m", $interval_date), date("d", $interval_date) + ($repeat_interval * 7), date("Y", $interval_date));
				}
				else if ($repeat_type == 2)
				{
					$month = date("m", $interval_date) + $repeat_interval;
					$year = date("Y", $interval_date);
					if ($month > 12)
					{
						$month = $month % 12;
						$year += 1;
					}

					$num_days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
					$next_date = mktime(0, 0, 0, $month, $num_days_in_month, $year);
				}
				else if ($repeat_type == 3)
				{
					$next_date = mktime(0, 0, 0, date("m", $interval_date), date("d", $interval_date), date("Y", $interval_date) + $repeat_interval);
				}

//				$_next_date = date('d/m-Y', $next_date);

			}

			return $return_date;
		}

		function add_controll_to_component_from_master( $master_component, $targets = array() )
		{
			$master_component_arr = explode('_', $master_component);
			if (count($master_component_arr) != 3)
			{
				throw new Exception("controller_socontrol::add_controll_to_component_from_master - Missing master component");
			}

			$location_id = (int)$master_component_arr[0];
			$component_id = (int)$master_component_arr[1];
			$control_id = (int)$master_component_arr[2];

			$sql = "SELECT * FROM controller_control_component_list"
				. " {$this->db->join} controller_control_serie"
				. " ON controller_control_serie.control_relation_id = controller_control_component_list.id"
				. " AND controller_control_serie.control_relation_type = 'component'"
				. " WHERE location_id = {$location_id}"
				. " AND  component_id = {$component_id}"
				. " AND control_id = {$control_id}"
				. " AND controller_control_serie.enabled = 1";

			$this->db->query($sql, __LINE__, __FILE__);

			$series = array();
			while ($this->db->next_record())
			{
				$_start_date = $this->db->f('start_date');
				$repeat_type = $this->db->f('repeat_type');
				$repeat_interval = $this->db->f('repeat_interval');

				$start_date = $this->get_next_start_date($_start_date, $repeat_type, $repeat_interval);

				$series[] = array(
					'control_id' => $this->db->f('control_id'),
					'assigned_to' => $this->db->f('assigned_to'),
					'start_date' => $start_date,
					'repeat_type' => $repeat_type,
					'repeat_interval' => $repeat_interval,
					'service_time' => $this->db->f('service_time'),
					'controle_time' => $this->db->f('controle_time'),
					'duplicate' => true
				);
			}

			$this->db->transaction_begin();

			foreach ($targets as $target)
			{
				$target_component_arr = explode('_', $target);

				$target_location_id = (int)$target_component_arr[0];
				$target_component_id = (int)$target_component_arr[1];
				foreach ($series as $serie)
				{
					$values = array
						(
						'register_component' => array("{$serie['control_id']}_{$target_location_id}_{$target_component_id}"),
						'assigned_to' => $serie['assigned_to'],
						'start_date' => $serie['start_date'],
						'repeat_type' => $serie['repeat_type'],
						'repeat_interval' => $serie['repeat_interval'],
						'controle_time' => $serie['controle_time'],
						'service_time' => $serie['service_time'],
						'duplicate' => true
					);
					$this->register_control_to_component($values);
				}
			}
			return $this->db->transaction_commit();
		}

		function get_serie( $serie_id )
		{
			$serie_id = (int)$serie_id;
			$serie = array();
			$sql = "SELECT controller_control_component_list.* ,"
				. " controller_control.title, controller_control.enabled as control_enabled,"
				. " controller_control_component_list.enabled as relation_enabled,"
				. " controller_control_serie.enabled as serie_enabled,"
				. " controller_control_serie.id as serie_id,"
				. " controller_control_serie.assigned_to,controller_control_serie.start_date,"
				. " controller_control_serie.repeat_type,controller_control_serie.repeat_interval,"
				. " controller_control_serie.service_time,controller_control_serie.controle_time "
				. " FROM controller_control_component_list"
				. " {$this->db->join} controller_control ON controller_control.id = controller_control_component_list.control_id"
				. " {$this->db->join} controller_control_serie ON (controller_control_component_list.id = controller_control_serie.control_relation_id AND controller_control_serie.control_relation_type = 'component')"
				. " WHERE controller_control_serie.id = {$serie_id}";
//			_debug_array($sql);
			$this->db->query($sql, __LINE__, __FILE__);

			if ($this->db->next_record())
			{
				$serie = array
					(
					'id' => $this->db->f('id'),
					'serie_id' => $this->db->f('serie_id'),
					'control_id' => $this->db->f('control_id'),
					'title' => $this->db->f('title', true),
					'location_id' => $this->db->f('location_id'),
					'component_id' => $this->db->f('component_id'),
					'assigned_to' => $this->db->f('assigned_to'),
					'start_date' => $this->db->f('start_date'),
					'repeat_type' => $this->db->f('repeat_type'),
					'repeat_interval' => $this->db->f('repeat_interval'),
					'control_enabled' => $this->db->f('control_enabled'),
					'relation_enabled' => $this->db->f('relation_enabled'),
					'serie_enabled' => $this->db->f('serie_enabled'),
					'service_time' => (float)$this->db->f('service_time'),
					'controle_time' => (float)$this->db->f('controle_time'),
				);
			}
			return $serie;
		}

		public function get_check_list_id_for_deadline( $serie_id, $deadline_ts = 0 )
		{
			$sql = "SELECT id FROM controller_check_list WHERE deadline = {$deadline_ts} AND serie_id = " . (int)$serie_id;
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			return $this->db->f('id');
		}

		public function get_assigned_history( $data )
		{
			$serie_id = (int)$data['serie_id'];
			$sql = "SELECT * FROM controller_control_serie_history WHERE serie_id = {$serie_id} ORDER BY id";
			$this->db->query($sql, __LINE__, __FILE__);
			$history = array();
			while ($this->db->next_record())
			{
				$history[] = array
					(
					'assigned_to' => $this->db->f('assigned_to'),
					'assigned_date' => $this->db->f('assigned_date'),
				);
			}
			foreach ($history as &$entry)
			{
				$entry['assigned_to_name'] = $GLOBALS['phpgw']->accounts->get($entry['assigned_to'])->__toString();
			}
			return $history;
		}

		public function save_bulk_uppdate_assign( $data )
		{
			$from = (int)$data['from'];
			$to = (int)$data['to'];
			$serie_ids = (array)$data['serie_ids'];
			$check_list_ids = (array)$data['check_list_ids'];

			if($from == $to)
			{
				return;
			}

			if($serie_ids && is_array($serie_ids))
			{
				$sql = "SELECT id FROM controller_control_serie"
					. " WHERE assigned_to = {$from}"
					. " AND id IN (" . implode(',', $serie_ids) . ')';
				$this->db->query($sql, __LINE__, __FILE__);
				$ids = array();
				while ($this->db->next_record())
				{
					$ids[] = $this->db->f('id');
				}
				if ($ids)
				{
					$this->update_control_serie(array(
						'ids' => $ids,
						'action' => 'edit',
						'assigned_to' => $to,
	//					'start_date' => $start_date,
	//					'repeat_type' => $repeat_type,
	//					'repeat_interval' => $repeat_interval,
	//					'controle_time' => $controle_time,
	//					'service_time' => $service_time,
					));
	//				$ret = $this->update_control_serie($data = array(
	//					'ids' => array($serie_id),
	//					'action' => 'enable',
	//				));

				}
			}

			if($check_list_ids && is_array($check_list_ids))
			{
				$now = time();
				$sql = "UPDATE controller_check_list SET assigned_to = $to"
					. " WHERE deadline > {$now} AND assigned_to = {$from}"
					. " AND id IN (" . implode(',', $check_list_ids) . ')';
				$this->db->query($sql, __LINE__, __FILE__);
			}
		}
	}