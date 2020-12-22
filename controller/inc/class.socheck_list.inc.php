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

		public $total_records;
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
		public function get_single( $check_list_id )
		{
			$check_list_id = (int)$check_list_id;
			$sql = "SELECT cl.id as cl_id, cl.status as cl_status, cl.control_id, cl.comment as cl_comment, deadline, original_deadline, planned_date,assigned_to, billable_hours, ";
			$sql .= "completed_date, location_code, component_id, num_open_cases, num_pending_cases,num_corrected_cases, location_id, ci.id as ci_id, control_item_id,serie_id ";
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
			$check_list->set_original_deadline($this->unmarshal($this->db->f('original_deadline'), 'int'));
			$check_list->set_planned_date($this->unmarshal($this->db->f('planned_date'), 'int'));
			$check_list->set_completed_date($this->unmarshal($this->db->f('completed_date'), 'int'));
			$check_list->set_location_code($this->unmarshal($this->db->f('location_code', true), 'string'));
			$check_list->set_component_id($this->unmarshal($this->db->f('component_id'), 'int'));
			$check_list->set_location_id($this->unmarshal($this->db->f('location_id'), 'int'));
			$check_list->set_num_open_cases($this->unmarshal($this->db->f('num_open_cases'), 'int'));
			$check_list->set_num_pending_cases($this->unmarshal($this->db->f('num_pending_cases'), 'int'));
			$check_list->set_num_corrected_cases($this->unmarshal($this->db->f('num_corrected_cases'), 'int'));
			$check_list->set_assigned_to($this->unmarshal($this->db->f('assigned_to'), 'int'));
			$check_list->set_billable_hours($this->db->f('billable_hours'));
			$check_list->set_serie_id($this->db->f('serie_id'));


			if ($check_list != null)
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
		public function get_single_with_check_items( $check_list_id, $status, $type )
		{
			$check_list_id = (int)$check_list_id;
			$sql = "SELECT cl.id as cl_id, cl.status as cl_status, cl.control_id, cl.comment as cl_comment, deadline, original_deadline, planned_date, completed_date,assigned_to, num_open_cases, location_code, num_pending_cases,num_corrected_cases, ";
			$sql .= "ci.id as ci_id, control_item_id, check_list_id, cl.serie_id";
			$sql .= "coi.title as coi_title, coi.required as coi_required, ";
			$sql .= "coi.what_to_do as coi_what_to_do, coi.how_to_do as coi_how_to_do, coi.control_group_id as coi_control_group_id, coi.type ";
			$sql .= "FROM controller_check_list cl ";
			$sql .= "LEFT JOIN controller_check_item as ci ON cl.id = ci.check_list_id ";
			$sql .= "LEFT JOIN controller_control_item as coi ON ci.control_item_id = coi.id ";
			$sql .= "WHERE cl.id = {$check_list_id} ";

			if ($status == 'open')
			{
				$sql .= "AND ci.status = 0 ";
			}
			else if ($status == 'handled')
			{
				$sql .= "AND ci.status = 1 ";
			}

			if ($type != null)
			{
				$sql .= "AND coi.type = '$type'";
			}

			$this->db->query($sql);

			$counter = 0;
			$check_list = null;
			while ($this->db->next_record())
			{
				if ($counter == 0)
				{
					$check_list = new controller_check_list($this->unmarshal($this->db->f('cl_id'), 'int'));
					$check_list->set_status($this->unmarshal($this->db->f('cl_status'), 'bool'));
					$check_list->set_control_id($this->unmarshal($this->db->f('control_id'), 'int'));
					$check_list->set_comment($this->unmarshal($this->db->f('cl_comment', true), 'string'));
					$check_list->set_deadline($this->unmarshal($this->db->f('deadline'), 'int'));
					$check_list->set_original_deadline($this->unmarshal($this->db->f('original_deadline'), 'int'));
					$check_list->set_planned_date($this->unmarshal($this->db->f('planned_date'), 'int'));
					$check_list->set_completed_date($this->unmarshal($this->db->f('completed_date'), 'int'));
					$check_list->set_location_code($this->unmarshal($this->db->f('location_code', true), 'string'));
					$check_list->set_num_open_cases($this->unmarshal($this->db->f('num_open_cases'), 'int'));
					$check_list->set_num_pending_cases($this->unmarshal($this->db->f('num_pending_cases'), 'int'));
					$check_list->set_num_corrected_cases($this->unmarshal($this->db->f('num_corrected_cases'), 'int'));
					$check_list->set_assigned_to($this->unmarshal($this->db->f('assigned_to'), 'int'));
					$check_list->set_serie_id($this->db->f('serie_id'));
				}

				if ($this->db->f('ci_id'))
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

			if ($check_list != null)
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
		function get_check_lists_for_control( $control_id )
		{
			$control_id = (int)$control_id;

			$sql = "SELECT cl.id as cl_id, cl.status as cl_status, cl.comment as cl_comment, deadline, original_deadline, planned_date, assigned_to, ";
			$sql .= "completed_date, component_id, location_code, num_open_cases, num_pending_cases,num_corrected_cases, ";
			$sql .= "ci.id as ci_id, control_item_id, check_list_id, cl.serie_id";
			$sql .= "FROM controller_check_list cl, controller_check_item ci ";
			$sql .= "WHERE cl.control_id = {$control_id} ";
			$sql .= "AND cl.id = ci.check_list_id ";
			$sql .= "ORDER BY cl.id;";

			$this->db->query($sql);

			$check_list_id = 0;
			$check_list = null;
			while ($this->db->next_record())
			{
				if ($this->db->f('cl_id') != $check_list_id)
				{
					if ($check_list_id)
					{
						$check_list->set_check_item_array($check_items_array);
						$check_list_array[] = $check_list->toArray();
					}

					$check_list = new controller_check_list($this->unmarshal($this->db->f('cl_id'), 'int'));
					$check_list->set_status($this->unmarshal($this->db->f('cl_status'), 'int'));
					$check_list->set_comment($this->unmarshal($this->db->f('cl_comment', true), 'string'));
					$check_list->set_deadline($this->unmarshal($this->db->f('deadline'), 'int'));
					$check_list->set_original_deadline($this->unmarshal($this->db->f('original_deadline'), 'int'));
					$check_list->set_planned_date($this->unmarshal($this->db->f('planned_date'), 'int'));
					$check_list->set_completed_date($this->unmarshal($this->db->f('completed_date'), 'int'));
					$check_list->set_component_id($this->unmarshal($this->db->f('component_id'), 'int'));
					$check_list->set_location_code($this->unmarshal($this->db->f('location_code', true), 'string'));
					$check_list->set_num_open_cases($this->unmarshal($this->db->f('num_open_cases'), 'int'));
					$check_list->set_num_pending_cases($this->unmarshal($this->db->f('num_pending_cases'), 'int'));
					$check_list->set_num_corrected_cases($this->unmarshal($this->db->f('num_corrected_cases'), 'int'));
					$check_list->set_assigned_to($this->unmarshal($this->db->f('assigned_to'), 'int'));
					$check_list->set_serie_id($this->db->f('serie_id'));

					$check_items_array = array();
				}

				$check_item = new controller_check_item($this->unmarshal($this->db->f('ci_id'), 'int'));
				$check_item->set_control_item_id($this->unmarshal($this->db->f('control_item_id'), 'int'));
				$check_item->set_check_list_id($this->unmarshal($this->db->f('check_list_id'), 'int'));
				$check_items_array[] = $check_item->toArray();

				$check_list_id = $check_list->get_id();
			}

			if ($check_list != null)
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

		function get_check_list_for_control_by_date( $control_id, $deadline_ts, $status = null, $location_code, $location_id, $component_id, $type )
		{
			$sql = "SELECT * ";
			$sql .= "FROM controller_check_list ";
			$sql .= "WHERE control_id = {$control_id} ";
			$sql .= "AND deadline = {$deadline_ts} ";

			if ($type == "location")
			{
				$sql .= "AND location_code = '{$location_code}' ";
			}
			else if ($type == "component")
			{
				$sql .= "AND location_id = '{$location_id}' AND component_id = '{$component_id}' ";
			}

			if ($status != null)
			{
				$sql .= "AND status = {$status} ";
			}

			$sql .= "AND assigned_to IS NOT NULL";

			$this->db->query($sql);

			$check_list = null;
			if ($this->db->next_record())
			{
				$check_list = new controller_check_list($this->unmarshal($this->db->f('id'), 'int'));
				$check_list->set_status($this->unmarshal($this->db->f('status'), 'int'));
				$check_list->set_comment($this->unmarshal($this->db->f('comment'), 'string'));
				$check_list->set_deadline($this->unmarshal($this->db->f('deadline'), 'int'));
				$check_list->set_original_deadline($this->unmarshal($this->db->f('original_deadline'), 'int'));
				$check_list->set_planned_date($this->unmarshal($this->db->f('planned_date'), 'int'));
				$check_list->set_completed_date($this->unmarshal($this->db->f('completed_date'), 'int'));
				$check_list->set_component_id($this->unmarshal($this->db->f('component_id'), 'int'));
				$check_list->set_location_code($this->unmarshal($this->db->f('location_code', true), 'string'));
				$check_list->set_num_open_cases($this->unmarshal($this->db->f('num_open_cases'), 'int'));
				$check_list->set_num_pending_cases($this->unmarshal($this->db->f('num_pending_cases'), 'int'));
				$check_list->set_num_corrected_cases($this->unmarshal($this->db->f('num_corrected_cases'), 'int'));
				$check_list->set_assigned_to($this->unmarshal($this->db->f('assigned_to'), 'int'));
				$check_list->set_serie_id($this->db->f('serie_id'));
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
		function get_planned_check_lists_for_control( $control_id, $location_code, $location_id, $component_id )
		{
			$control_id = (int)$control_id;

			$component_filter = ' AND component_id IS NULL ';
			if ($component_id)
			{
				$location_id = (int)$location_id;
				$component_id = (int)$component_id;
				$component_filter = " AND component_id = {$component_id} AND location_id = {$location_id} ";
			}

			$sql = "SELECT cl.id as cl_id, cl.status as cl_status, cl.comment as cl_comment, deadline, original_deadline, planned_date, assigned_to,";
			$sql .= "completed_date, component_id, location_code, num_open_cases, num_pending_cases,num_corrected_cases, cl.serie_id ";
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
				if ($this->db->f('cl_id') != $check_list_id)
				{
					if ($check_list_id)
					{
						$check_list_array[] = $check_list;
					}
					$check_list = new controller_check_list($this->unmarshal($this->db->f('cl_id'), 'int'));
					$check_list->set_status($this->unmarshal($this->db->f('cl_status'), 'int'));
					$check_list->set_comment($this->unmarshal($this->db->f('cl_comment'), 'string'));
					$check_list->set_deadline($this->unmarshal($this->db->f('deadline'), 'int'));
					$check_list->set_original_deadline($this->unmarshal($this->db->f('original_deadline'), 'int'));
					$check_list->set_planned_date($this->unmarshal($this->db->f('planned_date'), 'int'));
					$check_list->set_completed_date($this->unmarshal($this->db->f('completed_date'), 'int'));
					$check_list->set_component_id($this->unmarshal($this->db->f('component_id'), 'int'));
					$check_list->set_location_code($this->unmarshal($this->db->f('location_code', true), 'string'));
					$check_list->set_num_open_cases($this->unmarshal($this->db->f('num_open_cases'), 'int'));
					$check_list->set_num_pending_cases($this->unmarshal($this->db->f('num_pending_cases'), 'int'));
					$check_list->set_num_corrected_cases($this->unmarshal($this->db->f('num_corrected_cases'), 'int'));
					$check_list->set_assigned_to($this->unmarshal($this->db->f('assigned_to'), 'int'));
					$check_list->set_serie_id($this->db->f('serie_id'));
				}
				$check_list_id = $check_list->get_id();
			}

			if ($check_list != null)
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

			$sql = "SELECT c.id as c_id, sum(cl.num_open_cases) as count_open, sum(cl.num_corrected_cases) as count_corrected ";
			$sql .= "FROM controller_check_list cl, controller_control c ";

			if ($cl_criteria->get_component_id() > 0 && $cl_criteria->get_location_id() > 0)
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
			
			$this->db->next_record();
			
			$count = (int)$this->db->f('count_open') + (int)$this->db->f('count_corrected');

			if ($count > 0)
			{
				$control_array = array
					(
					"id" => $this->unmarshal($this->db->f('c_id'), 'int'),
					"count" => $count
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
			$sql = "SELECT c.id as c_id, ";
			$sql .= "cl.id as cl_id, cl.status as cl_status, cl.comment as cl_comment, deadline, original_deadline, planned_date, completed_date, assigned_to, ";
			$sql .= "cl.component_id as cl_component_id, cl.location_code as cl_location_code, num_open_cases, num_pending_cases,num_corrected_cases, cl.serie_id ";
			$sql .= "FROM controller_control c ";
			$sql .= "LEFT JOIN controller_check_list cl on cl.control_id = c.id ";
			$sql .= "WHERE cl.location_code LIKE '{$location_code}%' ";

			if ($repeat_type != null)
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
				if ($this->db->f('c_id') != $control_id)
				{
					if ($control_id)
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
				$check_list->set_original_deadline($this->unmarshal($this->db->f('original_deadline'), 'int'));
				$check_list->set_planned_date($this->unmarshal($this->db->f('planned_date'), 'int'));
				$check_list->set_completed_date($this->unmarshal($this->db->f('completed_date'), 'int'));
				$check_list->set_component_id($this->unmarshal($this->db->f('cl_component_id'), 'int'));
				$check_list->set_location_code($this->unmarshal($this->db->f('cl_location_code', true), 'string'));
				$check_list->set_num_open_cases($this->unmarshal($this->db->f('num_open_cases'), 'int'));
				$check_list->set_num_pending_cases($this->unmarshal($this->db->f('num_pending_cases'), 'int'));
				$check_list->set_num_corrected_cases($this->unmarshal($this->db->f('num_corrected_cases'), 'int'));
				$check_list->set_assigned_to($this->unmarshal($this->db->f('assigned_to'), 'int'));
				$check_list->set_serie_id($this->db->f('serie_id'));

				$check_lists_array[] = $check_list;

				$control_id = $control->get_id();
			}

			if ($control != null)
			{
				$control->set_check_lists_array($check_lists_array);
				$controls_array[] = $control;
			}

			return $controls_array;
		}

		function get_start_and_end_for_component( $location_id, $component_id )
		{
			$location_id = (int)$location_id;
			$component_id = (int)$component_id;

			$sql = "SELECT  MIN(deadline) AS start_timestamp, MAX(deadline) AS end_timestamp"
				. " FROM controller_check_list"
				. " WHERE location_id = {$location_id}"
				. " AND component_id = {$component_id}";

			$this->db->query($sql);
			$this->db->next_record();
			$start_timestamp = $this->db->f('start_timestamp');
			$end_timestamp = $this->db->f('end_timestamp');

			if($start_timestamp)
			{
				return array(
					'start_timestamp' => $start_timestamp,
					'end_timestamp' => $end_timestamp
				);
			}
			else
			{
				return array(
					'start_timestamp' => time(),
					'end_timestamp' => time()
				);
			}
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
			$location_id = (int)$location_id;
			$component_id = (int)$component_id;

			$sql = "SELECT c.id as c_id, ";
			$sql .= "cl.id as cl_id, cl.status as cl_status, cl.comment as cl_comment, deadline, original_deadline, planned_date, completed_date, assigned_to, ";
			$sql .= "cl.component_id, cl.location_id, cl.location_code as cl_location_code, num_open_cases, num_pending_cases,num_corrected_cases, cl.serie_id ";
			$sql .= "FROM controller_control c ";
			$sql .= "LEFT JOIN controller_check_list cl on cl.control_id = c.id ";
			$sql .= "WHERE cl.location_id = {$location_id} ";
			$sql .= "AND cl.component_id = {$component_id} ";

			if ($repeat_type != null)
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
				if ($this->db->f('c_id') != $control_id)
				{
					if ($control_id != 0)
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
				$check_list->set_original_deadline($this->unmarshal($this->db->f('original_deadline'), 'int'));
				$check_list->set_planned_date($this->unmarshal($this->db->f('planned_date'), 'int'));
				$check_list->set_completed_date($this->unmarshal($this->db->f('completed_date'), 'int'));
				$check_list->set_component_id($this->unmarshal($this->db->f('component_id'), 'int'));
				$check_list->set_location_id($this->unmarshal($this->db->f('location_id'), 'int'));
				$check_list->set_location_code($this->unmarshal($this->db->f('cl_location_code', true), 'string'));
				$check_list->set_num_open_cases($this->unmarshal($this->db->f('num_open_cases'), 'int'));
				$check_list->set_num_pending_cases($this->unmarshal($this->db->f('num_pending_cases'), 'int'));
				$check_list->set_num_corrected_cases($this->unmarshal($this->db->f('num_corrected_cases'), 'int'));
				$check_list->set_assigned_to($this->unmarshal($this->db->f('assigned_to'), 'int'));
				$check_list->set_serie_id($this->db->f('serie_id'));

				$check_lists_array[] = $check_list;

				$control_id = $control->get_id();
			}

			if ($control != null)
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
		function get_check_lists_for_control_and_location( $control_id, $location_code, $from_date_ts, $to_date_ts, $repeat_type = null, $filter_assigned_to = null )
		{
			$control_id = (int)$control_id;

			$sql = "SELECT cl.id as cl_id, cl.status as cl_status, cl.comment as cl_comment, deadline, original_deadline, planned_date, completed_date, assigned_to, ";
			$sql .= "cl.component_id as cl_component_id, cl.location_code as cl_location_code, num_open_cases, num_pending_cases,num_corrected_cases, cl.serie_id ";
			$sql .= "FROM controller_check_list cl ";
			$sql .= "LEFT JOIN controller_control c on cl.control_id = c.id ";
			$sql .= "WHERE cl.control_id = {$control_id} ";
			$sql .= "AND cl.location_code = '{$location_code}' ";

			if ($repeat_type != null)
			{
				$sql .= "AND c.repeat_type = $repeat_type ";
			}

			$sql .= "AND deadline BETWEEN $from_date_ts AND $to_date_ts ";
			if ($filter_assigned_to)
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
				$check_list->set_original_deadline($this->unmarshal($this->db->f('original_deadline'), 'int'));
				$check_list->set_planned_date($this->unmarshal($this->db->f('planned_date'), 'int'));
				$check_list->set_completed_date($this->unmarshal($this->db->f('completed_date'), 'int'));
				$check_list->set_component_id($this->unmarshal($this->db->f('cl_component_id'), 'int'));
				$check_list->set_location_code($this->unmarshal($this->db->f('cl_location_code', true), 'string'));
				$check_list->set_num_open_cases($this->unmarshal($this->db->f('num_open_cases'), 'int'));
				$check_list->set_num_pending_cases($this->unmarshal($this->db->f('num_pending_cases'), 'int'));
				$check_list->set_num_corrected_cases($this->unmarshal($this->db->f('num_corrected_cases'), 'int'));
				$check_list->set_assigned_to($this->unmarshal($this->db->f('assigned_to'), 'int'));
				$check_list->set_serie_id($this->db->f('serie_id'));

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
		function get_check_lists_for_control_and_component( $control_id, $location_id, $component_id, $from_date_ts, $to_date_ts, $repeat_type = null, $user_id = 0 )
		{
			$control_id = (int)$control_id;
			$location_id = (int)$location_id;
			$component_id = (int)$component_id;
			$user_id = (int)$user_id;

			$sql = "SELECT cl.id as cl_id, cl.status as cl_status, cl.comment as cl_comment, deadline, original_deadline, planned_date, completed_date, cl.assigned_to, ";
			$sql .= "cl.component_id as cl_component_id, cl.location_id as cl_location_id,"
				. " cl.location_code as cl_location_code, num_open_cases, num_pending_cases,num_corrected_cases ,cl.serie_id, cl.billable_hours, cs.repeat_type ";
			$sql .= "FROM controller_check_list cl ";
			$sql .= "LEFT JOIN controller_control c on cl.control_id = c.id ";
			$sql .= "LEFT JOIN controller_control_serie cs on cl.serie_id = cs.id ";
			$sql .= "WHERE cl.control_id = {$control_id} ";
			$sql .= "AND cl.component_id = {$component_id} ";
			$sql .= "AND cl.location_id = {$location_id} ";

			if ($repeat_type != null)
			{
				$sql .= "AND cs.repeat_type = $repeat_type ";
			}

//		if($user_id)
//		{
//			$sql .= " AND assigned_to = {$user_id} ";
//		}

			$sql .= "AND (deadline BETWEEN $from_date_ts AND $to_date_ts ";
			$sql .= "OR planned_date BETWEEN $from_date_ts AND $to_date_ts ";
			$sql .= "OR completed_date BETWEEN $from_date_ts AND $to_date_ts) ";

//		_debug_array($sql);

			$this->db->query($sql);

			$check_lists_array = array();
			while ($this->db->next_record())
			{
				$check_list = new controller_check_list($this->unmarshal($this->db->f('cl_id'), 'int'));
				$check_list->set_status($this->unmarshal($this->db->f('cl_status'), 'int'));
				$check_list->set_comment($this->unmarshal($this->db->f('cl_comment', true), 'string'));
				$check_list->set_deadline($this->unmarshal($this->db->f('deadline'), 'int'));
				$check_list->set_original_deadline($this->unmarshal($this->db->f('original_deadline'), 'int'));
				$check_list->set_planned_date($this->unmarshal($this->db->f('planned_date'), 'int'));
				$check_list->set_completed_date($this->unmarshal($this->db->f('completed_date'), 'int'));
				$check_list->set_component_id($this->unmarshal($this->db->f('cl_component_id'), 'int'));
				$check_list->set_location_id($this->unmarshal($this->db->f('cl_location_id'), 'int'));
				$check_list->set_location_code($this->unmarshal($this->db->f('cl_location_code', true), 'string'));
				$check_list->set_num_open_cases($this->unmarshal($this->db->f('num_open_cases'), 'int'));
				$check_list->set_num_pending_cases($this->unmarshal($this->db->f('num_pending_cases'), 'int'));
				$check_list->set_num_corrected_cases($this->unmarshal($this->db->f('num_corrected_cases'), 'int'));
				$check_list->set_assigned_to($this->unmarshal($this->db->f('assigned_to'), 'int'));
				$check_list->set_serie_id($this->db->f('serie_id'));
				$check_list->set_repeat_type($this->db->f('repeat_type'));
				$check_list->set_billable_hours((float)$this->db->f('billable_hours'));

				$check_lists_array[] = $check_list;
			}

			return array("location_code" => $location_code, "check_lists_array" => $check_lists_array);
		}

		function get_query( string $sort_field, bool $ascending, string $search_for, string $search_type, array $filters, bool $return_count )
		{
			$current_time = time();
			$buffer_in_days = 3600 * 24 * 7 * 5;
			$buffer_time = $current_time - $buffer_in_days;

			$clauses = array('1=1');
			$clauses[] = "{$current_time} >= p.start_date AND p.start_date > {$buffer_time}";

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
						$like_clauses[] = "p.title $this->like $like_pattern";
						break;
				}

				if (count($like_clauses))
				{
					$clauses[] = '(' . join(' OR ', $like_clauses) . ')';
				}
			}

			if (isset($filters[$this->get_id_field_name()]))
			{
				$filter_clauses[] = "p.id = {$this->marshal($filters[$this->get_id_field_name()], 'int')}";
			}

			if (count($filter_clauses))
			{
				$clauses[] = join(' AND ', $filter_clauses);
			}

			$condition = join(' AND ', $clauses);

			$tables = "controller_control p";

			if ($return_count)
			{
				$cols = 'COUNT(DISTINCT(p.id)) AS count';
			}
			else
			{
				$cols = 'p.* ';
			}

			$dir = $ascending ? 'ASC' : 'DESC';
			if ($sort_field == 'id')
			{
				$sort_field = 'p.id';
			}
			$order = $sort_field ? "ORDER BY {$this->marshal($sort_field, 'field')} $dir " : '';

			return "SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}";
		}

		function populate( int $control_id, &$control )
		{
			if ($control == null)
			{
				$start_date = date("d.m.Y", $this->db->f('start_date'));
				$end_date = date("d.m.Y", $this->db->f('end_date'));
				$control = new controller_control((int)$control_id);

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

		function add( &$check_list )
		{
			$cols = array(
				'control_id',
				'comment',
				'deadline',
				'original_deadline',
				'planned_date',
				'completed_date',
				'component_id',
				'location_code',
				'serie_id',
				'num_open_cases',
				'num_pending_cases',
				'num_corrected_cases',
				'location_id',
				'status',
				'assigned_to'
			);

			$values = array(
				$this->marshal($check_list->get_control_id(), 'int'),
				$this->marshal($check_list->get_comment(), 'string'),
				$this->marshal($check_list->get_deadline(), 'int'),
				$this->marshal($check_list->get_original_deadline(), 'int'),
				$this->marshal($check_list->get_planned_date(), 'int'),
				$this->marshal($check_list->get_completed_date(), 'int'),
				$this->marshal($check_list->get_component_id(), 'int'),
				$this->marshal($check_list->get_location_code(), 'string'),
				$this->marshal($check_list->get_serie_id(), 'int'),
				$this->marshal($check_list->get_num_open_cases(), 'int'),
				$this->marshal($check_list->get_num_pending_cases(), 'int'),
				$this->marshal($check_list->get_num_corrected_cases(), 'int'),
				$this->marshal($check_list->get_location_id(), 'int'),
				$check_list->get_status(),
				$this->marshal($check_list->get_assigned_to(), 'int'),
			);

			$result = $this->db->query('INSERT INTO controller_check_list (' . join(',', $cols) . ') VALUES (' . join(',', $values) . ')', __LINE__, __FILE__);

			return isset($result) ? $this->db->get_last_insert_id('controller_check_list', 'id') : 0;
		}

		function update( $check_list )
		{
			$id = (int)$check_list->get_id();
			/*
			  $sql = "SELECT billable_hours FROM controller_check_list WHERE controller_check_list.id = {$id}";

			  $this->db->query($sql);
			  $this->db->next_record();

			  $old_billable_hours = (float) $this->db->f('billable_hours');
			 */
			$old_billable_hours = (float)$check_list->get_billable_hours();

			$billable_hours = $old_billable_hours + $check_list->get_delta_billable_hours();

//--------
			$so_check_item = CreateObject('controller.socheck_item');
			$check_items = $so_check_item->get_check_items_with_cases($id, $control_item_type = null, $status = null, $messageStatus = null);

			$num_open_cases = 0;
			$num_pending_cases = 0;
			$num_corrected_cases = 0;

			foreach ($check_items as $check_item)
			{
				foreach ($check_item->get_cases_array() as $case)
				{

					if ($case->get_status() == controller_check_item_case::STATUS_OPEN)
					{
						$num_open_cases++;
					}

					if ($case->get_status() == controller_check_item_case::STATUS_PENDING)
					{
						$num_pending_cases++;
					}

					if ($case->get_status() == controller_check_item_case::STATUS_CORRECTED_ON_CONTROL)
					{
						$num_corrected_cases++;
					}
				}
			}

			if ($num_open_cases > 0)
			{
//			$check_list->set_status(controller_check_list::STATUS_DONE);
			}

			$check_list->set_num_open_cases($num_open_cases);
			$check_list->set_num_pending_cases($num_pending_cases);
			$check_list->set_num_corrected_cases($num_corrected_cases);

//-------

			$values = array(
				'control_id = ' . $this->marshal($check_list->get_control_id(), 'int'),
				'status = ' . $check_list->get_status(),
				'comment = ' . $this->marshal($check_list->get_comment(), 'string'),
				'deadline = ' . $this->marshal($check_list->get_deadline(), 'int'),
				'original_deadline = ' . $this->marshal($check_list->get_original_deadline(), 'int'),
				'planned_date = ' . $this->marshal($check_list->get_planned_date(), 'int'),
				'completed_date = ' . $this->marshal($check_list->get_completed_date(), 'int'),
				'location_code = ' . $this->marshal($check_list->get_location_code(), 'string'),
				'component_id = ' . $this->marshal($check_list->get_component_id(), 'int'),
				'location_id = ' . $this->marshal($check_list->get_location_id(), 'int'),
				'num_open_cases = ' . $this->marshal($check_list->get_num_open_cases(), 'int'),
				'num_pending_cases = ' . $this->marshal($check_list->get_num_pending_cases(), 'int'),
				'num_corrected_cases = ' . $this->marshal($check_list->get_num_corrected_cases(), 'int'),
				'assigned_to = ' . $this->marshal($check_list->get_assigned_to(), 'int'),
				'billable_hours = ' . $billable_hours
			);

			$sql = 'UPDATE controller_check_list SET ' . join(',', $values) . " WHERE id = {$id}";

			$result = $this->db->query($sql, __LINE__, __FILE__);

			if ($result)
			{
				return $id;
			}
			else
			{
				return 0;
			}
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
					'table' => 'control', // alias
					'field' => 'id',
					'translated' => 'id'
				);
			}

			return $ret;
		}

		function get_assigned_future_checklist( $assigned_to )
		{
			$assigned_to = (int) $assigned_to;
			$now = time();

			$sql = "SELECT controller_check_list.*, controller_control.title AS control_name  FROM controller_check_list"
				. " JOIN controller_control ON controller_control.id = controller_check_list.control_id"
				. " WHERE assigned_to = $assigned_to"
				. " AND planned_date > $now"
				. " AND completed_date IS NULL";

			$this->db->query($sql, __LINE__, __FILE__);
			$values = array();
			while ($this->db->next_record())
			{
				$values[] = array
				(
					'id' => $this->db->f('id'),
					'control_id' => $this->db->f('control_id'),
					'status'  => $this->db->f('status'),
					'comment'  => $this->db->f('comment', true),
					'deadline'  => $this->db->f('deadline'),
					'planned_date'  => $this->db->f('planned_date'),
					'component_id'  => $this->db->f('component_id'),
					'location_id'  => $this->db->f('location_id'),
					'billable_hours'  => $this->db->f('billable_hours'),
					'serie_id'  => $this->db->f('serie_id'),
					'control_name'  => $this->db->f('control_name', true),
				);
			}
			return $values;
		}

		function get_completed_item($check_list_id)
		{
			$check_list_id = (int) $check_list_id;
	//		$location_id = (int) $location_id;
			$table = 'controller_check_list_completed_item';
			$sql = "SELECT * FROM {$table} WHERE check_list_id  = {$check_list_id}"; // AND location_id = {$location_id}";
			$this->db->query($sql, __LINE__, __FILE__);
			$values = array();

			while ($this->db->next_record())
			{
				$location_id = $this->db->f('location_id');
				$item_id = $this->db->f('item_id');
				$values[$location_id][$item_id] = array(
					'completed_id' => $this->db->f('id'),
					'completed_ts' => $this->db->f('completed_ts'),
					'modified_by' => $this->db->f('modified_by'),
				);
			}

			return $values;

		}

		function set_completed_item($check_list_id, $location_id, $item_id)
		{
			$check_list_id = (int) $check_list_id;
			$location_id = (int) $location_id;
			$item_id = (int) $item_id;

			$account_id	= $GLOBALS['phpgw_info']['user']['account_id'];
			$table = 'controller_check_list_completed_item';
			$now = time();

			$ret = false;

			if($check_list_id && $location_id && $item_id)
			{
				$sql = "SELECT id FROM {$table} WHERE check_list_id  = {$check_list_id} AND location_id = {$location_id} AND item_id = {$item_id}";
				$this->db->query($sql, __LINE__, __FILE__);
				if ($this->db->next_record())
				{
					$sql = "UPDATE {$table} SET completed_ts = {$now}, modified_by = {$account_id}";
				}
				else
				{
					$sql = "INSERT INTO {$table} (check_list_id, location_id, item_id, completed_ts, modified_by) VALUES ( {$check_list_id}, {$location_id}, {$item_id}, {$now} , {$account_id})";
				}
				$ret = $this->db->query($sql, __LINE__, __FILE__);
			}

			return $ret;
		}


		function undo_completed_item($completed_id)
		{
			$completed_id = (int) $completed_id;
			$table = 'controller_check_list_completed_item';
			$sql = "DELETE FROM {$table} WHERE id  = {$completed_id}";
			return $this->db->query($sql, __LINE__, __FILE__);
		}

		function set_inspector($check_list_id, $user_id, $checked)
		{

			if($checked)
			{
				$add = array();
				$add[] = array
				(
					1	=> array
					(
						'value'	=> $check_list_id,
						'type'	=> PDO::PARAM_INT
					),
					2	=> array
					(
						'value'	=> $user_id,
						'type'	=> PDO::PARAM_INT
					),
					3	=> array
					(
						'value'	=> time(),
						'type'	=> PDO::PARAM_INT
					),
					4	=> array
					(
						'value'	=> $this->account,
						'type'	=> PDO::PARAM_INT
					)
				);

				$add_sql = "INSERT INTO controller_check_list_inspector (check_list_id, user_id, modified_on, modified_by) VALUES (?, ?, ? ,? )";
				$ok = $this->db->insert($add_sql, $add, __LINE__, __FILE__);
			}
			else
			{
				$delete = array();
				$delete[] = array
				(
					1	=> array
					(
						'value'	=> $check_list_id,
						'type'	=> PDO::PARAM_INT
					),
					2	=> array
					(
						'value'	=> $user_id,
						'type'	=> PDO::PARAM_INT
					)
				);
				$delete_sql = "DELETE FROM controller_check_list_inspector WHERE check_list_id =? AND user_id = ?";
				$ok = $this->db->delete($delete_sql, $delete, __LINE__, __FILE__);
			}

			return $ok;

		}

		function get_findings_summary( $check_list_id )
		{
			$check_list_id = (int)$check_list_id;
			$sql = "SELECT condition_degree, count(condition_degree) as cnt FROM controller_check_item"
					. " JOIN controller_check_item_case ON controller_check_item.id = controller_check_item_case.check_item_id"
					. " WHERE controller_check_item.check_list_id  = {$check_list_id}"
					. " GROUP BY condition_degree";
			$this->db->query($sql, __LINE__, __FILE__);

			$values = array('condition_degree' => array(), 'consequence' => array());

			while ($this->db->next_record())
			{
				$condition_degree = (int)$this->db->f('condition_degree');
				$cnt = (int)$this->db->f('cnt');
				$values['condition_degree'][$condition_degree] = $cnt;

			}

			$sql = "SELECT consequence, count(consequence) as cnt FROM controller_check_item"
					. " JOIN controller_check_item_case ON controller_check_item.id = controller_check_item_case.check_item_id"
					. " WHERE controller_check_item.check_list_id  = {$check_list_id}"
					. " GROUP BY consequence";
			$this->db->query($sql, __LINE__, __FILE__);

			while ($this->db->next_record())
			{
				$consequence =(int)$this->db->f('consequence');
				$cnt = (int)$this->db->f('cnt');
				$values['consequence'][$consequence] = $cnt;
			}

			ksort($values['condition_degree']);
			ksort($values['consequence']);

			return $values;
		}


		function get_historic_check_lists( $control_id, $selected_part_of_town, $start = 0, $query = '', $deviation = null, $allrows = null, $location_code = null, $results = null, $limit_date = null)
		{
			$control_id = (int)$control_id;
			if(!$selected_part_of_town && !$location_code)
			{
				return array();
			}

			$to_date_ts = time();
			$from_date_ts = $to_date_ts - ($age * 240 * 3600);

			$sql = "SELECT DISTINCT cl.id as cl_id, cl.status as cl_status, cl.comment as cl_comment,"
				. " deadline, original_deadline, planned_date, completed_date, cl.assigned_to,";
			$sql .= " cl.component_id as cl_component_id, cl.location_id as cl_location_id,"
				. " cl.location_code as cl_location_code, num_open_cases, num_pending_cases,num_corrected_cases,"
				. " cl.serie_id, cl.billable_hours, cs.repeat_type, fm_location1.loc1_name";
			$sql .= " FROM controller_check_list cl";
			$sql .= " {$this->left_join} controller_control c on cl.control_id = c.id";
			$sql .= " {$this->left_join} controller_control_serie cs on cl.serie_id = cs.id"
				. " {$this->join} fm_locations ON fm_locations.location_code = cl.location_code"
				. " {$this->join} fm_location1 ON fm_locations.loc1 = fm_location1.loc1"
				. " {$this->join} fm_part_of_town ON fm_location1.part_of_town_id = fm_part_of_town.id";
			$sql .= " WHERE cl.control_id = {$control_id}"
				. " AND completed_date IS NOT NULL";
			
			if($limit_date)
			{
				$sql .=	" AND completed_date > " . (int) $limit_date;
			}

			if($selected_part_of_town)
			{
				$sql .=  " AND fm_part_of_town.id IN (" . implode(',', $selected_part_of_town) . ")";	
			}
			if($location_code)
			{
				$location_arr = explode('-', $location_code);
				
				$sql .=  " AND cl.location_code {$this->like} '" .  $this->db->db_addslashes($location_arr[0]) . "%'";
			}

			if($deviation)
			{
				$sql .= " AND num_open_cases IS NOT NULL AND num_open_cases > 0";
			}
			if($query)
			{
				$query = $this->db->db_addslashes($query);
				$sql .= " AND (loc1_name {$this->like} '%{$query}%'"
				. " OR cl.location_code {$this->like} '{$query}%')";
			}

//			$sql .= " AND completed_date BETWEEN $from_date_ts AND $to_date_ts";
			$sql .= " ORDER BY completed_date DESC";


//		_debug_array($sql);

			$sql_arr = explode('FROM', $sql);

			$sql_cnt = "SELECT cl.id FROM " . $sql_arr[1];

			$this->db->query($sql_cnt,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();

			if($allrows)
			{
				$this->db->query($sql, __LINE__, __FILE__);
			}
			else
			{
				$this->db->limit_query($sql, $start, __LINE__, __FILE__, $results);
			}

			$values = array();
			while ($this->db->next_record())
			{
				$values[] = array(
					'id'				 => $this->db->f('cl_id'),
					'status'			 => $this->db->f('cl_status'),
					'comment'			 => $this->db->f('cl_comment', true),
					'deadline'			 => $this->db->f('deadline'),
					'original_deadline'	 => $this->db->f('original_deadline'),
					'planned_date'		 => $this->db->f('planned_date'),
					'completed_date'	 => $this->db->f('completed_date'),
					'component_id'		 => $this->db->f('cl_component_id'),
					'location_id'		 => $this->db->f('cl_location_id'),
					'location_code'		 => $this->db->f('cl_location_code', true),
					'loc1_name'			 => $this->db->f('loc1_name', true),
					'num_open_cases'	 => $this->db->f('num_open_cases'),
					'num_pending_cases'	 => $this->db->f('num_pending_cases'),
					'num_corrected_cases' => $this->db->f('num_corrected_cases'),
					'assigned_to'		 => $this->db->f('assigned_to'),
					'serie_id'			 => $this->db->f('serie_id'),
					'repeat_type'		 => $this->db->f('repeat_type'),
					'billable_hours'	 => (float) $this->db->f('billable_hours')
				);
			}

			foreach ($values as &$value)
			{
				$value['findings_summary'] = $this->get_findings_summary($value['id']);

			}
			return  $values;
		}
	}