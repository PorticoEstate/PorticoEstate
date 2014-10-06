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

include_class('controller', 'check_list', 'inc/model/');
include_class('controller', 'control_item', 'inc/model/');
include_class('controller', 'check_item', 'inc/model/');

class controller_socheck_list extends controller_socommon
{
	protected static $so;

	/**
	 * Get a static reference to the storage object associated with this model object
	 *
	 * @return controller_socheck_list the storage object
	 */
	public static function get_instance()
	{
		if (self::$so == null)
		{
			self::$so = CreateObject('controller.socheck_list');
		}
		return self::$so;
	}
	
	/**
	 * Get a check list and related check_items and returns it as an object
	 *
	 * @param $check_list_id
	 * @return check list object
	 */
	public function get_single($check_list_id)
	{
		$check_list_id = (int) $check_list_id;
		$sql = "SELECT cl.id as cl_id, cl.status as cl_status, cl.control_id, cl.comment as cl_comment, deadline, planned_date,assigned_to, billable_hours, "; 
		$sql .= "completed_date, location_code, component_id, num_open_cases, num_pending_cases, location_id, ci.id as ci_id, control_item_id "; 
		$sql .= "FROM controller_check_list cl ";
		$sql .= "LEFT JOIN controller_check_item as ci ON cl.id = ci.check_list_id ";
		$sql .= "WHERE cl.id = {$check_list_id}";
		
		$this->db->query($sql);
		$this->db->next_record();
			
		$check_list = new controller_check_list($this->unmarshal($this->db->f('cl_id'), 'int'));
		$check_list->set_control_id($this->unmarshal($this->db->f('control_id'), 'int'));
		$check_list->set_status($this->unmarshal($this->db->f('cl_status'), 'int'));
		$check_list->set_comment($this->unmarshal($this->db->f('cl_comment', true), 'string'));
		$check_list->set_deadline($this->unmarshal($this->db->f('deadline'), 'int'));
		$check_list->set_planned_date($this->unmarshal($this->db->f('planned_date'), 'int'));
		$check_list->set_completed_date($this->unmarshal($this->db->f('completed_date'), 'int'));
		$check_list->set_location_code($this->unmarshal($this->db->f('location_code', true), 'string'));
		$check_list->set_component_id($this->unmarshal($this->db->f('component_id'), 'int'));
		$check_list->set_location_id($this->unmarshal($this->db->f('location_id'), 'int'));
		$check_list->set_num_open_cases($this->unmarshal($this->db->f('num_open_cases'), 'int'));	
		$check_list->set_num_pending_cases($this->unmarshal($this->db->f('num_pending_cases'), 'int'));
		$check_list->set_assigned_to($this->unmarshal($this->db->f('assigned_to'), 'int'));
		$check_list->set_billable_hours($this->db->f('billable_hours'));

			
		if($check_list != null)
		{
			return $check_list;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * Get check lists from database with related check items and control items
	 *
	 * @param $check_list_id check list id
	 * @param $status status OPEN/CLOSED
	 * @param $type control items registration type (Radiobuttons, Checklist, textfield, just commentfield)
	 * @return returns a check list object
	 */
	public function get_single_with_check_items($check_list_id, $status, $type)
	{
		$check_list_id = (int) $check_list_id;
		$sql  = "SELECT cl.id as cl_id, cl.status as cl_status, cl.control_id, cl.comment as cl_comment, deadline, planned_date, completed_date,assigned_to, num_open_cases, location_code, num_pending_cases, ";
		$sql .= "ci.id as ci_id, control_item_id, check_list_id, "; 
		$sql .= "coi.title as coi_title, coi.required as coi_required, ";
		$sql .= "coi.what_to_do as coi_what_to_do, coi.how_to_do as coi_how_to_do, coi.control_group_id as coi_control_group_id, coi.type "; 
		$sql .= "FROM controller_check_list cl "; 
		$sql .= "LEFT JOIN controller_check_item as ci ON cl.id = ci.check_list_id ";
		$sql .= "LEFT JOIN controller_control_item as coi ON ci.control_item_id = coi.id ";
		$sql .= "WHERE cl.id = {$check_list_id} ";
		
		if($status == 'open')
		{
			$sql .= "AND ci.status = 0 ";
		}
		else if($status == 'handled')
		{
			$sql .= "AND ci.status = 1 ";
		}
			
		if($type != null)
		{
			$sql .= "AND coi.type = '$type'";
		}					

		$this->db->query($sql);
		
		$counter = 0;
		$check_list = null;
		while ($this->db->next_record())
		{
			if($counter == 0)
			{
				$check_list = new controller_check_list($this->unmarshal($this->db->f('cl_id'), 'int'));
				$check_list->set_status($this->unmarshal($this->db->f('cl_status'), 'bool'));
				$check_list->set_control_id($this->unmarshal($this->db->f('control_id'), 'int'));
				$check_list->set_comment($this->unmarshal($this->db->f('cl_comment', true), 'string'));
				$check_list->set_deadline($this->unmarshal($this->db->f('deadline'), 'int'));
				$check_list->set_planned_date($this->unmarshal($this->db->f('planned_date'), 'int'));
				$check_list->set_completed_date($this->unmarshal($this->db->f('completed_date'), 'int'));	
				$check_list->set_location_code($this->unmarshal($this->db->f('location_code', true), 'string'));
				$check_list->set_num_open_cases($this->unmarshal($this->db->f('num_open_cases'), 'int'));	
				$check_list->set_num_pending_cases($this->unmarshal($this->db->f('num_pending_cases'), 'int'));
				$check_list->set_assigned_to($this->unmarshal($this->db->f('assigned_to'), 'int'));
				
			}
						
			if($this->db->f('ci_id'))
			{
				$check_item = new controller_check_item($this->unmarshal($this->db->f('ci_id'), 'int'));
				$check_item->set_control_item_id($this->unmarshal($this->db->f('control_item_id'), 'int'));
				$check_item->set_check_list_id($this->unmarshal($this->db->f('check_list_id'), 'int'));
				
				$control_item = new controller_control_item($this->unmarshal($this->db->f('coi_id'), 'int'));
				$control_item->set_title($this->db->f('coi_title', true), 'string');
				$control_item->set_required($this->db->f('coi_required', true), 'string');
				$control_item->set_what_to_do($this->db->f('coi_what_to_do', true), 'string');
				$control_item->set_how_to_do($this->db->f('coi_how_to_do', true), 'string');
				$control_item->set_control_group_id($this->db->f('coi_control_group_id', true), 'string');
				$control_item->set_type($this->db->f('type', true), 'string');
				
				$check_item->set_control_item($control_item->toArray());
				$check_items_array[] = $check_item->toArray();
			}
			
			$counter++;
		}
		
		if($check_list != null)
		{
			$check_list->set_check_item_array($check_items_array);
			return $check_list->toArray();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * Get check list objects for a control
	 *
	 * @param $control_id
	 * @return array with check list objects
	 */
	function get_check_lists_for_control($control_id)
	{
		$control_id = (int) $control_id;

		$sql = "SELECT cl.id as cl_id, cl.status as cl_status, cl.comment as cl_comment, deadline, planned_date, assigned_to, "; 
		$sql .= "completed_date, component_id, location_code, num_open_cases, num_pending_cases ";
		$sql .= "ci.id as ci_id, control_item_id, check_list_id ";
		$sql .= "FROM controller_check_list cl, controller_check_item ci ";
		$sql .= "WHERE cl.control_id = {$control_id} ";
		$sql .= "AND cl.id = ci.check_list_id "; 
		$sql .= "ORDER BY cl.id;";

		$this->db->query($sql);
		
		$check_list_id = 0;
		$check_list = null;
		while ($this->db->next_record())
		{
			if( $this->db->f('cl_id') != $check_list_id )
			{
				if( $check_list_id )
				{
					$check_list->set_check_item_array($check_items_array);
					$check_list_array[] = $check_list->toArray();
				}
				
				$check_list = new controller_check_list($this->unmarshal($this->db->f('cl_id'), 'int'));
				$check_list->set_status($this->unmarshal($this->db->f('cl_status'), 'int'));
				$check_list->set_comment($this->unmarshal($this->db->f('cl_comment', true), 'string'));
				$check_list->set_deadline($this->unmarshal($this->db->f('deadline'), 'int'));
				$check_list->set_planned_date($this->unmarshal($this->db->f('planned_date'), 'int'));
				$check_list->set_completed_date($this->unmarshal($this->db->f('completed_date'), 'int'));	
				$check_list->set_component_id($this->unmarshal($this->db->f('component_id'), 'int'));
				$check_list->set_location_code($this->unmarshal($this->db->f('location_code', true), 'string'));
				$check_list->set_num_open_cases($this->unmarshal($this->db->f('num_open_cases'), 'int'));
				$check_list->set_num_pending_cases($this->unmarshal($this->db->f('num_pending_cases'), 'int'));
				$check_list->set_assigned_to($this->unmarshal($this->db->f('assigned_to'), 'int'));
				
				$check_items_array = array();
			}
			
			$check_item = new controller_check_item($this->unmarshal($this->db->f('ci_id'), 'int'));
			$check_item->set_control_item_id($this->unmarshal($this->db->f('control_item_id'), 'int'));
			$check_item->set_check_list_id($this->unmarshal($this->db->f('check_list_id'), 'int'));
			$check_items_array[] = $check_item->toArray();
			
			$check_list_id =  $check_list->get_id();
		}
		
		if($check_list != null)
		{
			$check_list->set_check_item_array($check_items_array);
			$check_list_array[] = $check_list->toArray();
		
			return $check_list_array;
		}
		else
		{
			return null;
		}
	}
	
	function get_check_list_for_control_by_date($control_id, $deadline_ts, $status = null, $location_code, $location_id, $component_id, $type)
	{
	  $sql  = "SELECT * "; 
		$sql .= "FROM controller_check_list ";
		$sql .= "WHERE control_id = {$control_id} ";
		$sql .= "AND deadline = {$deadline_ts} ";		

		if($type == "location")
		{
			$sql .= "AND location_code = '{$location_code}' ";	
		}
		else if($type == "component")
		{
			$sql .= "AND location_id = '{$location_id}' AND component_id = '{$component_id}' ";
		}
		
		if($status != null)
		{
			$sql .= "AND status = {$status} ";
		}  
	
		$sql .= "AND assigned_to IS NOT NULL";

		$this->db->query($sql);
		
		$check_list = null;
		if( $this->db->next_record() )
		{
			$check_list = new controller_check_list($this->unmarshal($this->db->f('id'), 'int'));
			$check_list->set_status($this->unmarshal($this->db->f('status'), 'int'));
			$check_list->set_comment($this->unmarshal($this->db->f('comment'), 'string'));
			$check_list->set_deadline($this->unmarshal($this->db->f('deadline'), 'int'));
			$check_list->set_planned_date($this->unmarshal($this->db->f('planned_date'), 'int'));
			$check_list->set_completed_date($this->unmarshal($this->db->f('completed_date'), 'int'));	
			$check_list->set_component_id($this->unmarshal($this->db->f('component_id'), 'int'));
			$check_list->set_location_code($this->unmarshal($this->db->f('location_code', true), 'string'));
			$check_list->set_num_open_cases($this->unmarshal($this->db->f('num_open_cases'), 'int'));	
			$check_list->set_num_pending_cases($this->unmarshal($this->db->f('num_pending_cases'), 'int'));
			$check_list->set_assigned_to($this->unmarshal($this->db->f('assigned_to'), 'int'));
		}
				
		return $check_list;
	}
	
	/**
	 * Get check list objects for a control on a location with set planned date
	 *
	 * @param $control_id control id
	 * @param $location_code location code representing physical locations
	 * @param $location_id location id representing logical system locations
	 * @param $component_id component id: entity within logical location
	 * @return array with check list objects
	 */
	function get_planned_check_lists_for_control($control_id, $location_code,$location_id, $component_id)
	{
		$control_id = (int) $control_id;
		
		$component_filter = ' AND component_id IS NULL ';
		if($component_id)
		{
			$location_id = (int)$location_id;
			$component_id = (int)$component_id;
			$component_filter = " AND component_id = {$component_id} AND location_id = {$location_id} ";
		}

		$sql = "SELECT cl.id as cl_id, cl.status as cl_status, cl.comment as cl_comment, deadline, planned_date, assigned_to,"; 
		$sql .= "completed_date, component_id, location_code, num_open_cases, num_pending_cases ";
		$sql .= "FROM controller_check_list cl ";
		$sql .= "WHERE cl.control_id = {$control_id} ";
		$sql .= "AND cl.location_code = '{$location_code}' "; 
		$sql .= "AND NOT cl.planned_date IS NULL ";
		$sql .= "AND cl.completed_date IS NULL ";
		$sql .= $component_filter;
		$sql .= "ORDER BY cl.id;";

		$this->db->query($sql);
		
		$check_list_id = 0;
		$check_list = null;
		while ($this->db->next_record())
		{
			if( $this->db->f('cl_id') != $check_list_id )
			{
				if($check_list_id)
				{
					$check_list_array[] = $check_list;
				}
				$check_list = new controller_check_list($this->unmarshal($this->db->f('cl_id'), 'int'));
				$check_list->set_status($this->unmarshal($this->db->f('cl_status'), 'int'));
				$check_list->set_comment($this->unmarshal($this->db->f('cl_comment'), 'string'));
				$check_list->set_deadline($this->unmarshal($this->db->f('deadline'), 'int'));
				$check_list->set_planned_date($this->unmarshal($this->db->f('planned_date'), 'int'));
				$check_list->set_completed_date($this->unmarshal($this->db->f('completed_date'), 'int'));	
				$check_list->set_component_id($this->unmarshal($this->db->f('component_id'), 'int'));
				$check_list->set_location_code($this->unmarshal($this->db->f('location_code', true), 'string'));
				$check_list->set_num_open_cases($this->unmarshal($this->db->f('num_open_cases'), 'int'));	
				$check_list->set_num_pending_cases($this->unmarshal($this->db->f('num_pending_cases'), 'int'));
				$check_list->set_assigned_to($this->unmarshal($this->db->f('assigned_to'), 'int'));
			}
			$check_list_id =  $check_list->get_id();
		}
		
		if($check_list != null)
		{
			$check_list_array[] = $check_list;
			return $check_list_array;
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * Get array with control id and number of open cases within time period
	 *
	 * @param $cl_criteria check list criteria object
	 * @param $from_date start time period
	 * @param $to_date end time period
	 * @return array with check list objects
	 */
	function get_num_open_cases_for_control( $cl_criteria, $from_date_ts, $to_date_ts )
	{
		
		$sql = 	"SELECT c.id as c_id, sum(cl.num_open_cases) as count ";
		$sql .= "FROM controller_check_list cl, controller_control c ";
		
		if($cl_criteria->get_component_id() > 0 && $cl_criteria->get_location_id() > 0)
		{
			$sql .= "WHERE cl.component_id = {$cl_criteria->get_component_id()} ";
			$sql .= "AND cl.location_id = {$cl_criteria->get_location_id()} ";
		}
		else
		{
			$sql .= "WHERE cl.location_code = '{$cl_criteria->get_location_code()}' ";
		}
		
		$sql .= "AND c.id = {$cl_criteria->get_control_id()} ";
		$sql .= "AND cl.control_id = c.id ";
		$sql .= "AND cl.deadline >= $from_date_ts AND $to_date_ts > cl.deadline ";
		$sql .= "GROUP BY c.id";
		
		$this->db->query($sql);
		
		if ($this->db->next_record() & $this->db->f('count') > 0)
		{
			$control_array = array
			(
				"id" 	=> $this->unmarshal($this->db->f('c_id'), 'int'),
				"count" => $this->db->f('count')
			);
		}
		
		return $control_array;
	}
	
	/**
	 * Get array with check lists for a location within time period and for a specified repeat type
	 *
	 * @param $location_code location code
	 * @param $from_date_ts start time period
	 * @param $to_date_ts end time period
	 * @param $repeat_type_expr repeat type expression
	 * @return array with check list objects
	 */
	function get_check_lists_for_location( $location_code, $from_date_ts, $to_date_ts, $repeat_type_expr = null )
	{
		$sql = 	"SELECT c.id as c_id, ";
		$sql .= "cl.id as cl_id, cl.status as cl_status, cl.comment as cl_comment, deadline, planned_date, completed_date, assigned_to, ";
		$sql .= "cl.component_id as cl_component_id, cl.location_code as cl_location_code, num_open_cases, num_pending_cases "; 
		$sql .= "FROM controller_control c ";
		$sql .= "LEFT JOIN controller_check_list cl on cl.control_id = c.id ";
		$sql .= "WHERE cl.location_code = '{$location_code}' ";
		
		if( $repeat_type != null )
		{
			$sql .= "AND c.repeat_type $repeat_type_expr ";
		}
		
	  $sql .= "AND (deadline > $from_date_ts) AND (deadline < $to_date_ts) ";
		$sql .= "ORDER BY c.id;";
		
		$this->db->query($sql);
		
		$control_id = 0;
		$control = null;
		$controls_array = array();
		while ($this->db->next_record())
		{
			if( $this->db->f('c_id') != $control_id )
			{	
				if($control_id)
				{
					$control->set_check_lists_array($check_lists_array);
					$controls_array[] = $control;
				}
			
				$control = new controller_control($this->unmarshal($this->db->f('c_id'), 'int'));
												
				$check_lists_array = array();
			}

			$check_list = new controller_check_list($this->unmarshal($this->db->f('cl_id'), 'int'));
			$check_list->set_status($this->unmarshal($this->db->f('cl_status'), 'int'));
			$check_list->set_comment($this->unmarshal($this->db->f('cl_comment', true), 'string'));
			$check_list->set_deadline($this->unmarshal($this->db->f('deadline'), 'int'));
			$check_list->set_planned_date($this->unmarshal($this->db->f('planned_date'), 'int'));
			$check_list->set_completed_date($this->unmarshal($this->db->f('completed_date'), 'int'));	
			$check_list->set_component_id($this->unmarshal($this->db->f('cl_component_id'), 'int'));
			$check_list->set_location_code($this->unmarshal($this->db->f('cl_location_code', true), 'string'));
			$check_list->set_num_open_cases($this->unmarshal($this->db->f('num_open_cases'), 'int'));
			$check_list->set_num_pending_cases($this->unmarshal($this->db->f('num_pending_cases'), 'int'));
			$check_list->set_assigned_to($this->unmarshal($this->db->f('assigned_to'), 'int'));
			
			$check_lists_array[] = $check_list;

			$control_id =  $control->get_id();
		}
		
		if($control != null)
		{
			$control->set_check_lists_array($check_lists_array);
			$controls_array[] = $control;
		}	
		
		return $controls_array;
	}
	
	/**
	 * Get array with check lists for a component within time period and for a specified repeat type
	 *
	 * @param $location_code location code
	 * @param $from_date_ts start time period
	 * @param $to_date_ts end time period
	 * @param $repeat_type_expr repeat type expression
	 * @return array with check list objects
	 */
	function get_check_lists_for_component( $location_id, $component_id, $from_date_ts, $to_date_ts, $repeat_type_expr = null )
	{
		$location_id = (int) $location_id;
		$component_id = (int) $component_id;

		$sql = 	"SELECT c.id as c_id, ";
		$sql .= "cl.id as cl_id, cl.status as cl_status, cl.comment as cl_comment, deadline, planned_date, completed_date, assigned_to, ";
		$sql .= "cl.component_id, cl.location_id, cl.location_code as cl_location_code, num_open_cases, num_pending_cases "; 
		$sql .= "FROM controller_control c ";
		$sql .= "LEFT JOIN controller_check_list cl on cl.control_id = c.id ";
		$sql .= "WHERE cl.location_id = {$location_id} ";
		$sql .= "AND cl.component_id = {$component_id} ";
		
		if( $repeat_type != null )
		{
			$sql .= "AND c.repeat_type $repeat_type_expr ";
		}
		
		$sql .= "AND (deadline > $from_date_ts) AND (deadline < $to_date_ts) ";
		$sql .= "ORDER BY c.id;";
		
		$this->db->query($sql);
		
		$control_id = 0;
		$control = null;
		$controls_array = array();
		while ($this->db->next_record())
		{
			if( $this->db->f('c_id') != $control_id )
			{
				if($control_id != 0)
				{
					$control->set_check_lists_array($check_lists_array);
					$controls_array[] = $control;
				}

				$control = new controller_control($this->unmarshal($this->db->f('c_id'), 'int'));
												
				$check_lists_array = array();
			}

			$check_list = new controller_check_list($this->unmarshal($this->db->f('cl_id'), 'int'));
			$check_list->set_status($this->unmarshal($this->db->f('cl_status'), 'int'));
			$check_list->set_comment($this->unmarshal($this->db->f('cl_comment', true), 'string'));
			$check_list->set_deadline($this->unmarshal($this->db->f('deadline'), 'int'));
			$check_list->set_planned_date($this->unmarshal($this->db->f('planned_date'), 'int'));
			$check_list->set_completed_date($this->unmarshal($this->db->f('completed_date'), 'int'));	
			$check_list->set_component_id($this->unmarshal($this->db->f('component_id'), 'int'));
			$check_list->set_location_id($this->unmarshal($this->db->f('location_id'), 'int'));
			$check_list->set_location_code($this->unmarshal($this->db->f('cl_location_code', true), 'string'));
			$check_list->set_num_open_cases($this->unmarshal($this->db->f('num_open_cases'), 'int'));
			$check_list->set_num_pending_cases($this->unmarshal($this->db->f('num_pending_cases'), 'int'));
			$check_list->set_assigned_to($this->unmarshal($this->db->f('assigned_to'), 'int'));

			$check_lists_array[] = $check_list;

			$control_id =  $control->get_id();
		}
		
		if($control != null)
		{
			$control->set_check_lists_array($check_lists_array);
			$controls_array[] = $control;
		}	
		
		return $controls_array;
	}
	
	/**
	 * Get array with check lists for a control on a location within time period and for a specified repeat type
	 *
	 * @param $control_id control id
	 * @param $location_code location code
	 * @param $from_date_ts start time period
	 * @param $to_date_ts end time period
	 * @param $repeat_type_expr repeat type expression
	 * @return array with check list objects
	*/
	function get_check_lists_for_control_and_location( $control_id, $location_code, $from_date_ts, $to_date_ts, $repeat_type = null , $filter_assigned_to = null)
	{
		$control_id = (int) $control_id;

		$sql = 	"SELECT cl.id as cl_id, cl.status as cl_status, cl.comment as cl_comment, deadline, planned_date, completed_date, assigned_to, ";
		$sql .= "cl.component_id as cl_component_id, cl.location_code as cl_location_code, num_open_cases, num_pending_cases "; 
		$sql .= "FROM controller_check_list cl ";
		$sql .= "LEFT JOIN controller_control c on cl.control_id = c.id ";
		$sql .= "WHERE cl.control_id = {$control_id} ";
		$sql .= "AND cl.location_code = '{$location_code}' ";
		
		if( $repeat_type != null )
		{
			$sql .= "AND c.repeat_type = $repeat_type ";
		}
		
		$sql .= "AND deadline BETWEEN $from_date_ts AND $to_date_ts ";
		if($filter_assigned_to)
		{
			$sql .= "AND assigned_to IS NULL";
		}

		$this->db->query($sql);
		
		while ($this->db->next_record())
		{
			$check_list = new controller_check_list($this->unmarshal($this->db->f('cl_id'), 'int'));
			$check_list->set_status($this->unmarshal($this->db->f('cl_status'), 'int'));
			$check_list->set_comment($this->unmarshal($this->db->f('cl_comment', true), 'string'));
			$check_list->set_deadline($this->unmarshal($this->db->f('deadline'), 'int'));
			$check_list->set_planned_date($this->unmarshal($this->db->f('planned_date'), 'int'));
			$check_list->set_completed_date($this->unmarshal($this->db->f('completed_date'), 'int'));	
			$check_list->set_component_id($this->unmarshal($this->db->f('cl_component_id'), 'int'));
			$check_list->set_location_code($this->unmarshal($this->db->f('cl_location_code', true), 'string'));
			$check_list->set_num_open_cases($this->unmarshal($this->db->f('num_open_cases'), 'int'));
			$check_list->set_num_pending_cases($this->unmarshal($this->db->f('num_pending_cases'), 'int'));
			$check_list->set_assigned_to($this->unmarshal($this->db->f('assigned_to'), 'int'));
			
			$check_lists_array[] = $check_list;
		}
		
		return $check_lists_array;
	}
	
	/**
	 * Get array with check lists for a control on a component within time period and for a specified repeat type
	 *
	 * @param $control_id control id
	 * @param $location_code location code
	 * @param $from_date_ts start time period
	 * @param $to_date_ts end time period
	 * @param $repeat_type_expr repeat type expression
	 * @return array with check list objects
	*/
	function get_check_lists_for_control_and_component( $control_id, $location_id, $component_id, $from_date_ts, $to_date_ts, $repeat_type = null )
	{
		$control_id = (int) $control_id;
		$location_id = (int) $location_id;
		$component_id = (int) $component_id;

		$sql = 	"SELECT cl.id as cl_id, cl.status as cl_status, cl.comment as cl_comment, deadline, planned_date, completed_date, assigned_to, ";
		$sql .= "cl.component_id as cl_component_id, cl.location_id as cl_location_id, cl.location_code as cl_location_code, num_open_cases, num_pending_cases "; 
		$sql .= "FROM controller_check_list cl ";
		$sql .= "LEFT JOIN controller_control c on cl.control_id = c.id ";
		$sql .= "WHERE cl.control_id = {$control_id} ";
		$sql .= "AND cl.component_id = {$component_id} ";
		$sql .= "AND cl.location_id = {$location_id} ";
		
		if( $repeat_type != null )
		{
			$sql .= "AND c.repeat_type = $repeat_type ";
		}
		
		$sql .= "AND deadline BETWEEN $from_date_ts AND $to_date_ts ";
		
		$this->db->query($sql);
		
		$check_lists_array = array();
		while ($this->db->next_record())
		{
			$check_list = new controller_check_list($this->unmarshal($this->db->f('cl_id'), 'int'));
			$check_list->set_status($this->unmarshal($this->db->f('cl_status'), 'int'));
			$check_list->set_comment($this->unmarshal($this->db->f('cl_comment', true), 'string'));
			$check_list->set_deadline($this->unmarshal($this->db->f('deadline'), 'int'));
			$check_list->set_planned_date($this->unmarshal($this->db->f('planned_date'), 'int'));
			$check_list->set_completed_date($this->unmarshal($this->db->f('completed_date'), 'int'));	
			$check_list->set_component_id($this->unmarshal($this->db->f('cl_component_id'), 'int'));
			$check_list->set_location_id($this->unmarshal($this->db->f('cl_location_id'), 'int'));
			$check_list->set_location_code($this->unmarshal($this->db->f('cl_location_code', true), 'string'));
			$check_list->set_num_open_cases($this->unmarshal($this->db->f('num_open_cases'), 'int'));
			$check_list->set_num_pending_cases($this->unmarshal($this->db->f('num_pending_cases'), 'int'));
			$check_list->set_assigned_to($this->unmarshal($this->db->f('assigned_to'), 'int'));
			
			$check_lists_array[] = $check_list;
		}
		
		return array( "location_code" => $location_code, "check_lists_array" => $check_lists_array);
	}
	
	function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count)
	{
		$current_time = time();
		$buffer_in_days = 3600*24*7*5;
		$buffer_time = $current_time - $buffer_in_days;

		$clauses = array('1=1');
		$clauses[] = "{$current_time} >= p.start_date AND p.start_date > {$buffer_time}"; 
		
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
					$like_clauses[] = "p.title $this->like $like_pattern";
					break;
			}
			
			if(count($like_clauses))
			{
				$clauses[] = '(' . join(' OR ', $like_clauses) . ')';
			}
		}
		
		if(isset($filters[$this->get_id_field_name()]))
		{
			$filter_clauses[] = "p.id = {$this->marshal($filters[$this->get_id_field_name()],'int')}";
		}
		
		if(count($filter_clauses))
		{
			$clauses[] = join(' AND ', $filter_clauses);
		}
		
		$condition =  join(' AND ', $clauses);

		$tables = "controller_control p";
		
		if($return_count)
		{
			$cols = 'COUNT(DISTINCT(p.id)) AS count';
		}
		else
		{
			$cols = 'p.* ';
		}
		
		$dir = $ascending ? 'ASC' : 'DESC';
		if($sort_field == 'id')
		{
			$sort_field = 'p.id';
		}
		$order = $sort_field ? "ORDER BY {$this->marshal($sort_field, 'field')} $dir ": '';
		
		return "SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}";
	}

	function populate(int $control_id, &$control)
	{
		if($control == null)
		{
			$start_date = date("d.m.Y",  $this->db->f('start_date'));
			$end_date = date("d.m.Y",  $this->db->f('end_date'));
			$control = new controller_control((int) $control_id);

			$control->set_title($this->unmarshal($this->db->f('title', true), 'string'));
			$control->set_description($this->unmarshal($this->db->f('description', true), 'string'));
			$control->set_start_date($start_date);
			$control->set_end_date($end_date);
			$control->set_procedure_id($this->unmarshal($this->db->f('procedure_id'), 'int'));
			$control->set_procedure_name($this->unmarshal($this->db->f('procedure_name', true), 'string'));
			$control->set_requirement_id($this->unmarshal($this->db->f('requirement_id'), 'int'));
			$control->set_costresponsibility_id($this->unmarshal($this->db->f('costresponsibility_id'), 'int'));
			$control->set_responsibility_id($this->unmarshal($this->db->f('responsibility_id'), 'int'));
			$control->set_control_area_id($this->unmarshal($this->db->f('control_area_id'), 'int'));
			$control->set_control_area_name($this->unmarshal($this->db->f('control_area_name', true), 'string'));
			$control->set_equipment_type_id($this->unmarshal($this->db->f('equipment_type_id'), 'int'));
			$control->set_equipment_id($this->unmarshal($this->db->f('equipment_id'), 'int'));
			$control->set_location_code($this->unmarshal($this->db->f('location_code', true), 'string'));
			$control->set_location_id($this->unmarshal($this->db->f('location_id'), 'string'));
			$control->set_repeat_type($this->unmarshal($this->db->f('repeat_type'), 'int'));
			$control->set_repeat_interval($this->unmarshal($this->db->f('repeat_interval'), 'int'));
		}
		
		return $control;
	}
	
	function add(&$check_list)
	{
		$cols = array(
				'control_id',
				'comment',
				'deadline',
				'planned_date',
				'completed_date',
				'component_id',
				'location_code',
				'num_open_cases',
				'num_pending_cases',
				'location_id',
				'status',
				'assigned_to'
		);
				
		$values = array(
			$this->marshal($check_list->get_control_id(), 'int'),
			$this->marshal($check_list->get_comment(), 'string'),
			$this->marshal($check_list->get_deadline(), 'int'),
			$this->marshal($check_list->get_planned_date(), 'int'),
			$this->marshal($check_list->get_completed_date(), 'int'),
			$this->marshal($check_list->get_component_id(), 'int'),
			$this->marshal($check_list->get_location_code(), 'string'),
			$this->marshal($check_list->get_num_open_cases(), 'int'),
			$this->marshal($check_list->get_num_pending_cases(), 'int'),
			$this->marshal($check_list->get_location_id(), 'int'),
			$check_list->get_status(),
			$this->marshal($check_list->get_assigned_to(), 'int'),
		);
		
		$result = $this->db->query('INSERT INTO controller_check_list (' . join(',', $cols) . ') VALUES (' . join(',', $values) . ')', __LINE__,__FILE__);
			
		return isset($result) ? $this->db->get_last_insert_id('controller_check_list', 'id') : 0;
	}
	
	function update($check_list)
	{
		$id = (int)$check_list->get_id();

		$sql = "SELECT billable_hours FROM controller_check_list WHERE controller_check_list.id = {$id}";
		
		$this->db->query($sql);
		$this->db->next_record();
			
		$old_billable_hours = (float) $this->db->f('billable_hours');

		$billable_hours = $old_billable_hours + $check_list->get_billable_hours();

//--------
		$so_check_item = CreateObject('controller.socheck_item');
		$check_items = $so_check_item->get_check_items_with_cases($id, $control_item_type = null, $status = null, $messageStatus = null);
	
		$num_open_cases = 0;
		$num_pending_cases = 0;
					
		foreach($check_items as $check_item)
		{
			foreach($check_item->get_cases_array() as $case)
			{
				
				if($case->get_status() == controller_check_item_case::STATUS_OPEN)
				{
					$num_open_cases++;
				}
					
				if($case->get_status() == controller_check_item_case::STATUS_PENDING)
				{
					$num_pending_cases++;
				}
			}	
		}
			
		if($num_open_cases > 0)
		{
//			$check_list->set_status(controller_check_list::STATUS_DONE);
		}
	  
		$check_list->set_num_open_cases($num_open_cases);
		$check_list->set_num_pending_cases($num_pending_cases);

//-------

		$values = array(
			'control_id = ' . $this->marshal($check_list->get_control_id(), 'int'),
			'status = ' . $check_list->get_status(),
			'comment = ' . $this->marshal($check_list->get_comment(), 'string'),
			'deadline = ' . $this->marshal($check_list->get_deadline(), 'int'),
			'planned_date = ' . $this->marshal($check_list->get_planned_date(), 'int'),
			'completed_date = ' . $this->marshal($check_list->get_completed_date(), 'int'),
			'location_code = ' . $this->marshal($check_list->get_location_code(), 'string'),
			'component_id = ' . $this->marshal($check_list->get_component_id(), 'int'),
			'location_id = ' . $this->marshal($check_list->get_location_id(), 'int'),
			'num_open_cases = ' . $this->marshal($check_list->get_num_open_cases(), 'int'),
			'num_pending_cases = ' . $this->marshal($check_list->get_num_pending_cases(), 'int'),
			'assigned_to = ' . $this->marshal($check_list->get_assigned_to(), 'int'),
			'billable_hours = ' . $billable_hours 
		);

		$sql = 'UPDATE controller_check_list SET ' . join(',', $values) . " WHERE id = {$id}";

		$result = $this->db->query($sql, __LINE__,__FILE__);

		if($result)
		{
			return $id;			
		}
		else
		{
			return 0;
		}
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
				'table'			=> 'control', // alias
				'field'			=> 'id',
				'translated'	=> 'id'
			);
		}
		
		return $ret;
	}	
}
