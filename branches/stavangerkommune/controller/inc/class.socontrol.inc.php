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
		function add(&$control)
		{
			$title = $control->get_title();

			$sql = "INSERT INTO controller_control (title) VALUES ('$title')";
			$result = $this->db->query($sql, __LINE__,__FILE__);

			if($result)
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
		 * @return boolean true if successful, false otherwise
		 */
		function update($control)
		{
			$id = intval($control->get_id());

			$values = array(
				'title = ' . $this->marshal($control->get_title(), 'string'),
				'description = ' . $this->marshal($control->get_description(), 'string'),
				'start_date = ' . $this->marshal($control->get_start_date(), 'int'),
				'end_date = ' . $this->marshal($control->get_end_date(), 'int'),
				'control_area_id = ' . $this->marshal($control->get_control_area_id()),
				'repeat_type = ' . $this->marshal($control->get_repeat_type(), 'string'),
				'repeat_interval = ' . $this->marshal($control->get_repeat_interval(), 'string'),
				'procedure_id = ' . $this->marshal($control->get_procedure_id(), 'int'),
				'responsibility_id = ' . $this->marshal($control->get_responsibility_id(), 'int')
			);

			$result = $this->db->query('UPDATE controller_control SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);

			if( $result )
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
		public function get_assigned_check_list_at_location($from_date, $to_date, $repeat_type, $user_id,$completed = null, $return_type = "return_object")
		{
			$user_id = (int) $user_id;
			$repeat_type = (int) $repeat_type;

			$check_list_array = array();
			
			$sql  = "SELECT DISTINCT controller_check_list.location_code, controller_check_list.control_id, controller_check_list.id AS check_list_id,"
				. " procedure_id,requirement_id,costresponsibility_id,description, start_date, end_date,deadline,planned_date, completed_date,"
				. " control_area_id, repeat_type,repeat_interval, title"
				. " FROM controller_check_list"
				. " {$this->join} controller_control ON controller_check_list.control_id = controller_control.id"
				. " {$this->join} controller_control_location_list ON controller_control_location_list.control_id = controller_control.id"
				. " WHERE assigned_to = {$user_id} AND status = 0";

//_debug_array($sql);
			if( $repeat_type )
			{
//				$sql .= "AND controller_control.repeat_type = $repeat_type ";
			}

			
			$sql .= " AND ((controller_control.start_date <= $to_date AND controller_control.end_date IS NULL) ";
			$sql .= " OR (controller_control.start_date <= $to_date AND controller_control.end_date > $from_date ))";

			//FIXME
			if($completed)
			{
				$sql .= " AND controller_check_list.completed_date IS NULL ";			
			}
			
//_debug_array($sql);
			$this->db->query($sql);

			while($this->db->next_record())
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

				if($return_type == "return_object")
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
		public function get_assigned_check_list_by_component($from_date, $to_date, $repeat_type, $user_id, $completed = null, $return_type = "return_object")
		{
			$repeat_type = $repeat_type;
			$user_id = (int)$user_id;

		
			$sql  = "SELECT DISTINCT controller_check_list.location_code, controller_check_list.control_id, controller_check_list.id AS check_list_id,"
				. " controller_control.description, start_date, end_date, deadline,planned_date, completed_date,"
				. " control_area_id,controller_check_list.location_id,title,controller_check_list.component_id"
				. " FROM controller_check_list"
				. " {$this->join} controller_control ON controller_check_list.control_id = controller_control.id"
				. " {$this->join} controller_control_component_list "
					. " ON (controller_control_component_list.control_id = controller_check_list.control_id"
					. " AND controller_control_component_list.location_id = controller_check_list.location_id"
					. " AND controller_control_component_list.component_id = controller_check_list.component_id)"
				. " WHERE assigned_to = {$user_id} AND status = 0";

			if( $repeat_type )
			{
//				$sql .= "AND controller_control.repeat_type = $repeat_type ";
			}

			//FIXME
			if($completed)
			{
				$sql .= " AND controller_check_list.completed_date IS NULL ";			
			}

			$sql .= " AND ((deadline <= $to_date AND controller_control.end_date IS NULL) ";
			$sql .= " OR (deadline <= $to_date AND deadline > $from_date ))";

			$this->db->query($sql);
			
			$check_list_array = array();

			while($this->db->next_record()) 
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

				if($return_type == "return_object")
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
		public function get_controls_by_location($location_code, $from_date, $to_date, $repeat_type, $return_type = "return_object", $role_id = 0)
		{
			$role_id = (int) $role_id;
			$repeat_type = (int) $repeat_type;

			$controls_array = array();
			
			$sql  = "SELECT distinct c.*, fm_responsibility_role.name AS responsibility_name,location_code "; 
			$sql .= "FROM controller_control_location_list cll "; 
			$sql .= "LEFT JOIN controller_control c on cll.control_id=c.id ";
			$sql .= "LEFT JOIN fm_responsibility_role ON fm_responsibility_role.id = c.responsibility_id ";
	//		$sql .= "WHERE cll.location_code = '$location_code' ";
			$sql .= "WHERE cll.location_code LIKE '$location_code%' ";			
			if( $repeat_type )
			{
				$sql .= "AND c.repeat_type = $repeat_type ";
			}

			if( $role_id )
			{
			    $sql .= "AND c.responsibility_id = $role_id ";
			}
			
			$sql .= "AND ((c.start_date <= $to_date AND c.end_date IS NULL) ";
			$sql .= "OR (c.start_date <= $to_date AND c.end_date > $from_date ))";
			

			$this->db->query($sql);

			while($this->db->next_record())
			{
				$_location_code = $this->db->f('location_code', true);

				$control = new controller_control($this->unmarshal($this->db->f('id'), 'int'));
				$control->set_title($this->unmarshal($this->db->f('title', true)  . " [{$_location_code}]", 'string'));
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

				if($return_type == "return_object")
				{
					$controls_array[] = $control;
				}
				else
				{
					$controls_array[] = $control->toArray();
				}
			}

			if( count( $controls_array ) > 0 )
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
		
		public function get_controls_for_components_by_location($location_code, $from_date, $to_date, $repeat_type, $role_id = 0)
		{			
			$controls_array = array();
			
			$sql  = "SELECT distinct c.*, fm_responsibility_role.name AS responsibility_name, ccl.location_id, ccl.component_id ";
			$sql .= "FROM controller_control_component_list ccl "; 
			$sql .= "LEFT JOIN controller_control c on ccl.control_id = c.id ";
			$sql .= "LEFT JOIN fm_responsibility_role ON fm_responsibility_role.id = c.responsibility_id ";
			$sql .= "LEFT JOIN fm_bim_item ON fm_bim_item.id = ccl.component_id ";
			$sql .= "WHERE fm_bim_item.location_code LIKE '$location_code%' ";

			if( $repeat_type != null)
			{
				$repeat_type = (int) $repeat_type;
				$sql .= "AND c.repeat_type = $repeat_type ";
			}
			if( $role_id)
			{
					$role_id = (int) $role_id;
			    $sql .= "AND c.responsibility_id = $role_id ";
			}
			
			$sql .= "AND ((c.start_date <= $to_date AND c.end_date IS NULL) ";
			$sql .= "OR (c.start_date <= $to_date AND c.end_date > $from_date ))";

			$this->db->query($sql);

			while($this->db->next_record())
			{
	
				$controls_array[] = array
				(
					'id'					=> $this->db->f('id'),
					'title'					=> $this->db->f('title', true),
					'description'			=> $this->db->f('description', true),
					'start_date'			=> $this->db->f('start_date'),
					'end_date'				=> $this->db->f('end_date'),
					'procedure_id'			=> $this->db->f('procedure_id'),
					'requirement_id'		=> $this->db->f('requirement_id'),
					'costresponsibility_id'	=> $this->db->f('costresponsibility_id'),
					'responsibility_id'		=> $this->db->f('responsibility_id'),
					'responsibility_name'	=> $this->db->f('responsibility_name', true),
					'control_area_id'		=> $this->db->f('control_area_id'),
					'repeat_type'			=> $this->db->f('repeat_type'),
					'repeat_interval'		=> $this->db->f('repeat_interval'),
					'component_id'			=> $this->db->f('component_id'),
					'location_id'			=> $this->db->f('location_id')
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
		public function get_controls_by_component($from_date, $to_date, $repeat_type, $return_type = "return_object", $role_id = 0, $filter = null)
		{
			$controls_array = array();
			
			$sql   = "SELECT c.id as control_id, c.*, ";
			$sql  .= "bim_item.type as component_type, bim_item.id as component_id, bim_item.location_code, bim_item.address, ";
			$sql  .= "cl.location_id, fm_responsibility_role.name AS responsibility_name ";
			$sql  .= "FROM controller_control_component_list cl ";
			$sql  .= "JOIN fm_bim_item bim_item on cl.component_id = bim_item.id ";
			$sql  .= "JOIN fm_bim_type bim_type on cl.location_id = bim_type.location_id ";
			$sql  .= "JOIN controller_control c on cl.control_id = c.id ";
			$sql  .= "JOIN fm_responsibility_role ON fm_responsibility_role.id = c.responsibility_id ";
			$sql  .= "AND bim_item.type = bim_type.id ";
			
			if( $repeat_type != null)
			{
				$repeat_type = (int) $repeat_type;
				$sql .= "AND c.repeat_type = $repeat_type ";
			}
			if( $role_id )
			{
				$role_id = (int) $role_id;
			  $sql .= "AND c.responsibility_id = $role_id ";
			}
			    
			$sql .= "AND ((c.start_date <= $to_date AND c.end_date IS NULL) ";
			$sql .= "OR (c.start_date <= $to_date AND c.end_date > $from_date ))";
			
			if($filter != null)
			{
				$sql .= "AND " . $filter;	
			}
			
			$sql .= "ORDER BY bim_item.id ";
			 
			$this->db->query($sql);
			
			$component_id = 0;
			$component = null;
			while($this->db->next_record()) 
			{
				if( $this->db->f('component_id') != $component_id )
				{
					if($component_id != 0)
					{
						$component->set_controls_array($controls_array);
						$controls_array = array();
						
						if($return_type == "return_array")
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
			
				if($return_type == "return_object")
				{
					$controls_array[] = $control;
				}
				else
				{
					$controls_array[] = $control->toArray();
				}
							
				$component_id = $component->get_id();
			}
					
			if($component != null)
			{
				$component->set_controls_array($controls_array);
				
				if($return_type == "return_array")
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
		function get_controls_by_control_area($control_area_id)
		{
			$control_area_id = (int) $control_area_id;
			$controls_array = array();

			$sql = "SELECT * FROM controller_control WHERE control_area_id=$control_area_id";
			$this->db->query($sql);

			while($this->db->next_record()) 
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

			if( count( $controls_array ) > 0 )
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
		 * @param $control_id control id
		 * @return array with arrays of location info  
		 */
		function get_locations_for_control($control_id)
		{
			$control_id = (int) $control_id;

			$controls_array = array();

			$sql =  "SELECT c.id, c.title, cll.location_code "; 
			$sql .= "FROM controller_control c, controller_control_location_list cll ";
			$sql .= "WHERE cll.control_id = {$control_id} ";
			$sql .= "AND cll.control_id = c.id";

			$this->db->query($sql);

			while($this->db->next_record())
			{
				$control_id = $this->unmarshal($this->db->f('id'), 'int');
				$title = $this->unmarshal($this->db->f('title', true), 'string');
				$location_code = $this->unmarshal($this->db->f('location_code', true), 'string');

				$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code,'extra' => array('noattrib' => true)));

				$location_arr = explode('-', $location_code);
				$loc_name_arr = array();
				$i = 1;
				foreach ($location_arr as $_part)
				{
					$loc_name_arr[] = $location_array["loc{$i}_name"];
					$i++;
				}

				$controls_array[] = array
				(
					'id'			=> $control_id, 
					'title'			=> $title, 
					'location_code'	=> $location_code, 
					'loc1_name'		=> $location_array['loc1_name'],
					'loc_name'		=> implode(', ', $loc_name_arr)
				);
			}

			if( count( $controls_array ) > 0 )
			{
				return $controls_array; 
			}
			else
			{
				return null;
			}
		}
		
		/**
		 * Get arrays with component info that a control should be carried out on
		 *
		 * @param $control_id control id
		 * @return array with arrays of component info  
		 */
		function get_components_for_control($control_id)
		{
			$control_id = (int) $control_id;

			$controls_array = array();

			$sql =  "SELECT ccl.control_id, ccl.component_id as component_id, ccl.location_id as location_id, bim_type.description, bim_item.location_code ";
			$sql .= "FROM controller_control_component_list ccl, fm_bim_item bim_item, fm_bim_type bim_type "; 
			$sql .= "WHERE ccl.control_id = $control_id ";
			$sql .= "AND ccl.component_id = bim_item.id ";
			$sql .= "AND ccl.location_id = bim_type.location_id ";
			$sql .= "AND bim_type.id = bim_item.type";

			$this->db->query($sql);

			while($this->db->next_record())
			{
				$component = new controller_component();
				$component->set_type($this->unmarshal($this->db->f('type'), 'int'));
				$component->set_id($this->unmarshal($this->db->f('component_id'), 'int'));
				$component->set_location_id($this->unmarshal($this->db->f('location_id'), 'int'));
				$component->set_guid($this->unmarshal($this->db->f('guid', true), 'string'));
				$component->set_location_code($this->unmarshal($this->db->f('location_code', true), 'string'));
				$component->set_loc_1($this->unmarshal($this->db->f('loc_1', true), 'string'));
				$component->set_address($this->unmarshal($this->db->f('address', true), 'string'));
				$component->set_type_str($this->unmarshal($this->db->f('description', true), 'string'));
				
				$components_array[] = $component;
			}

			if( count( $components_array ) > 0 )
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
		function get_control_location($control_id, $location_code)
		{
			$control_id = (int)$control_id;
			$sql =  "SELECT * ";
			$sql .= "FROM controller_control_location_list ";
			$sql .= "WHERE control_id = $control_id ";
			$sql .= "AND location_code = '$location_code'";
			
			$this->db->query($sql, __LINE__, __FILE__);

			if($this->db->next_record())
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
		

		public function check_control_component($control_id, $location_id, $component_id)
		{
			$control_id		= (int) $control_id;
			$location_id	= (int) $location_id;
			$component_id	= (int) $component_id;
			
			$sql  = "SELECT * ";
			$sql .= "FROM controller_control_component_list ";
			$sql .= "WHERE control_id = {$control_id} ";
			$sql .= "AND location_id = {$location_id} ";
			$sql .= "AND component_id = {$component_id}";
			
			$this->db->query($sql, __LINE__, __FILE__);
			return $this->db->next_record();
		}

		/**
		 * Register that a control should be carried out on a location
		 *
		 * @param $data['control_id'] control id
		 * @param $data['component_id'] component id
		 * @param $data['location_id'] component id
		 * @return true or false if the execution was successful  
		*/
		function register_control_to_location($data)
		{

			$delete_component = array();
			$add_component = array();
			$this->db->transaction_begin();

			if(isset($data['register_location']) && is_array($data['register_location']))
			{
				foreach($data['register_location'] as $location_info)
				{
					$location_arr = explode('_', $location_info);
					if(count($location_arr)!=2)
					{
						continue;
					}
					
					$control_id		= (int) $location_arr[0];
					$location_code	= $location_arr[1];

					if(!$control_id)
					{
						return false;
					}

					$sql  = "SELECT * ";
					$sql .= "FROM controller_control_location_list ";
					$sql .= "WHERE control_id = {$control_id} ";
					$sql .= "AND location_code = '{$location_code}' ";
					
					$this->db->query($sql, __LINE__, __FILE__);
			
					if(!$this->db->next_record())
					{
						$sql =  "INSERT INTO controller_control_location_list (control_id, location_code) ";
						$sql .= "VALUES ( {$control_id}, '{$location_code}')";
						
						$this->db->query($sql);
					}
				}
			}

			if(isset($data['delete']) && is_array($data['delete']))
			{
				foreach($data['delete'] as $location_info)
				{
					$location_arr = explode('_', $location_info);
					if(count($location_arr)!=2)
					{
						continue;
					}
					
					$control_id		= (int) $location_arr[0];
					$location_code	= $location_arr[1];
				
					$sql =  "DELETE FROM controller_control_location_list WHERE control_id = {$control_id} AND location_code = '{$location_code}'";
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
		function register_control_to_component($data)
		{

			$delete_component = array();
			$add_component = array();
			$this->db->transaction_begin();

			if(isset($data['register_component']) && is_array($data['register_component']))
			{
				foreach($data['register_component'] as $component_info)
				{
					$component_arr = explode('_', $component_info);
					if(count($component_arr)!=3)
					{
						continue;
					}
					
					$control_id		= (int) $component_arr[0];
					$location_id	= (int) $component_arr[1];
					$component_id	= (int) $component_arr[2];

					if(!$control_id)
					{
						return false;
					}

					$sql  = "SELECT * ";
					$sql .= "FROM controller_control_component_list ";
					$sql .= "WHERE control_id = {$control_id} ";
					$sql .= "AND location_id = {$location_id} ";
					$sql .= "AND component_id = {$component_id}";
					
					$this->db->query($sql, __LINE__, __FILE__);
			
					if(!$this->db->next_record())
					{
						$sql =  "INSERT INTO controller_control_component_list (control_id, location_id, component_id) ";
						$sql .= "VALUES ( {$control_id}, {$location_id}, {$component_id})";
						
						$this->db->query($sql);
					}
				}
			}

			if(isset($data['delete']) && is_array($data['delete']))
			{
				foreach($data['delete'] as $component_info)
				{
					$component_arr = explode('_', $component_info);
					if(count($component_arr)!=3)
					{
						continue;
					}
					
					$control_id		= (int) $component_arr[0];
					$location_id	= (int) $component_arr[1];
					$component_id	= (int) $component_arr[2];
				
					$sql =  "DELETE FROM controller_control_component_list WHERE control_id = {$control_id} AND location_id = {$location_id} AND component_id = {$component_id}";
					$this->db->query($sql);
				}
			}

			return $this->db->transaction_commit();
		}

		/**
		 * Register that a control should be carried out on a component
		 *
		 * @param $control_id control id
		 * @param $component_id component id
		 * @return void  
		 */
		function add_component_to_control($control_id, $component_id)
		{
			$sql =  "INSERT INTO controller_control_component_list (control_id, component_id) values($control_id, $component_id)";
			$this->db->query($sql);
		}

		function get_id_field_name($extended_info = false)
		{
			if(!$extended_info)
			{
				$ret = 'id';
			}
			else
			{
				$ret = array
				(
					'table'			=> 'controller_control', // alias
					'field'			=> 'id',
					'translated'	=> 'id'
				);
			}

			return $ret;
		}

		protected function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count)
		{
			$clauses = array('1=1');

			$filter_clauses = array();

			// Search for based on search type
			if($search_for)
			{
				$search_for = $this->marshal($search_for,'field');
				$like_pattern = "'%".$search_for."%'";
				$like_clauses = array();
				switch($search_type)
				{
					default:
						$like_clauses[] = "controller_control.title $this->like $like_pattern";
						$like_clauses[] = "controller_control.description $this->like $like_pattern";
						break;
				}

				if(count($like_clauses))
				{
					$clauses[] = '(' . join(' OR ', $like_clauses) . ')';
				}
			}

			if(isset($filters[$this->get_id_field_name()]))
			{
				$filter_clauses[] = "controller_control.id = {$this->marshal($filters[$this->get_id_field_name()],'int')}";
			}
			if(isset($filters['control_areas']))
			{
//				$filter_clauses[] = "controller_control.control_area_id = {$this->marshal($filters['control_areas'],'int')}";

				$cat_id = (int) $filters['control_areas'];
				$cats	= CreateObject('phpgwapi.categories', -1, 'controller', '.control');
				$cats->supress_info	= true;
				$cat_list	= $cats->return_sorted_array(0, false, '', '', '', false, $cat_id, false);
				$cat_filter = array($cat_id);
				foreach ($cat_list as $_category)
				{
					$cat_filter[] = $_category['id'];
				}

				$filter_clauses[] = "controller_control.control_area_id IN (" .  implode(',', $cat_filter) .')';
			}
			if(isset($filters['responsibilities']))
			{
				$filter_clauses[] = "controller_control.responsibility_id = {$this->marshal($filters['responsibilities'],'int')}";
			}

			if($filters['district_id'])
			{
				$sql  = "SELECT DISTINCT control_id"
				. " FROM controller_control_location_list {$this->join} fm_locations ON controller_control_location_list.location_code = fm_locations.location_code"
				. " {$this->join} fm_location1 ON fm_locations.loc1 = fm_location1.loc1"
				. " {$this->join} fm_part_of_town ON fm_location1.part_of_town_id = fm_part_of_town.part_of_town_id"
				. " WHERE district_id =" . (int)$filters['district_id'];
			
				$db	= & $GLOBALS['phpgw']->db;
				$db->query($sql, __LINE__, __FILE__);
				
				$control_at_district = array();
				while ($db->next_record())
				{
					$control_at_district[] = $db->f('control_id');
				}

				$sql  = "SELECT DISTINCT control_id"
				. " FROM controller_control_component_list {$this->join} fm_bim_item ON controller_control_component_list.location_id = fm_bim_item.location_id AND controller_control_component_list.component_id = fm_bim_item.id"
				. " {$this->join} fm_location1 ON fm_bim_item.loc1 = fm_location1.loc1"
				. " {$this->join} fm_part_of_town ON fm_location1.part_of_town_id = fm_part_of_town.part_of_town_id"
				. " WHERE district_id =" . (int)$filters['district_id'];

				$db->query($sql, __LINE__, __FILE__);
				
				while ($db->next_record())
				{
					$control_at_district[] = $db->f('control_id');
				}
				
				if($control_at_district)
				{
					$filter_clauses[] = "controller_control.id IN (" .  implode(',', array_unique($control_at_district)) .')';
				}

			}

			if(count($filter_clauses))
			{
				$clauses[] = join(' AND ', $filter_clauses);
			}

			$condition =  join(' AND ', $clauses);

			$tables = "controller_control";
			$joins .= " {$this->left_join} controller_procedure ON (controller_control.procedure_id = controller_procedure.id)";
			$joins .= " {$this->left_join} fm_responsibility_role ON (controller_control.responsibility_id = fm_responsibility_role.id)";

			if($return_count)
			{
				$cols = 'COUNT(DISTINCT(controller_control.id)) AS count';
			}
			else
			{
				$cols = 'controller_control.id, controller_control.title, controller_control.description, controller_control.start_date, controller_control.end_date, controller_control.procedure_id, controller_control.control_area_id, controller_control.requirement_id, controller_control.costresponsibility_id, controller_control.responsibility_id, controller_control.repeat_type, controller_control.repeat_interval, controller_control.enabled, controller_procedure.title AS procedure_name, fm_responsibility_role.name AS responsibility_name ';
			}

			$dir = $ascending ? 'ASC' : 'DESC';
			if($sort_field == 'title')
			{
				$sort_field = 'controller_control.title';
			}
			else if($sort_field == 'id')
			{
				$sort_field = 'controller_control.id';
			}
			$order = $sort_field ? "ORDER BY {$this->marshal($sort_field, 'field')} $dir ": '';
			
			return "SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}";
		}

		function populate(int $control_id, &$control)
		{
			if($control == null)
			{
				$control = new controller_control((int) $control_id);

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
		function get_single($id)
		{
			$id = (int)$id;

			$joins .= " {$this->left_join} controller_procedure ON (c.procedure_id = controller_procedure.id)";
			$joins .= " {$this->left_join} fm_responsibility_role ON (c.responsibility_id = fm_responsibility_role.id)";

			$sql  = "SELECT c.*, controller_procedure.title AS procedure_name, fm_responsibility_role.name AS responsibility_name "; 
			$sql .= "FROM controller_control c {$joins} "; 
			$sql .= "WHERE c.id = " . $id;
			
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			
			$control = new controller_control((int) $id);
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

			$category = execMethod('phpgwapi.categories.return_single', $this->unmarshal($this->db->f('control_area_id'), 'int'));
			$control->set_control_area_name($category[0]['name']);

			return $control;
		}

		function get_roles()
		{
			$ret_array = array();
			$ret_array[0] = array('id' =>  0,'name' => lang('Not selected'));
			$sql = "select * from fm_responsibility_role ORDER BY name";
			$this->db->query($sql, __LINE__, __FILE__);
			$i = 1;
			while($this->db->next_record())
			{
				$ret_array[$i]['id'] = $this->db->f('id');
				$ret_array[$i]['name'] = $this->db->f('name');
				$i++;
			}
			return $ret_array;
		}

		function get_bim_types($ifc = null)
		{
			$ret_array = array();
			if($ifc != null)
			{
				if($ifc == 1)
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
			while($this->db->next_record())
			{
				$ret_array[$i]['id'] = $this->db->f('id');
				$ret_array[$i]['name'] = $this->db->f('name', true);
				$i++;
			}
			return $ret_array;
		}
		
		public function get_control_component($noOfObjects = null, $bim_type = null)
		{
			$filters = array();
			if($noOfObjects != null && is_numeric($noOfObjects))
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

			$sql  = "SELECT c.id AS control_id, c.title AS control_title, fm_bim_type.name AS type_name, fm_bim_item.id AS bim_id, fm_bim_item.guid as bim_item_guid FROM controller_control c {$joins} {$limit}";
			
			$controlArray = array();
			$this->db->query($sql, __LINE__, __FILE__);
			$i=1;
			while($this->db->next_record())
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
		
		public function getBimItemAttributeValue($bimItemGuid, $attribute) 
		{
			$columnAlias = "attribute_values";
			$sql = "select array_to_string(xpath('descendant-or-self::*[{$attribute}]/{$attribute}/text()', (select xml_representation from fm_bim_item where guid='{$bimItemGuid}')), ',') as $columnAlias";
			
			$this->db->query($sql,__LINE__,__FILE__);
			if($this->db->num_rows() > 0)
			{
				$this->db->next_record();
				$result = $this->db->f($columnAlias,true);
				return preg_split('/,/', $result);
			}
		}
		
		public function getLocationCodeFromControl($control_id)
		{
			$sql = "select location_code from controller_control_location_list where control_id={$control_id}";
			$this->db->query($sql,__LINE__,__FILE__);
			if($this->db->num_rows() > 0)
			{
				$this->db->next_record();
				$result = $this->db->f('location_code');
				return $result;
			}
		}
	}
