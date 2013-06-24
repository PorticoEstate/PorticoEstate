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

	phpgw::import_class('controller.socommon');

	include_class('controller', 'control_group_list', 'inc/model/');
	include_class('controller', 'control_group', 'inc/model/');

	class controller_socontrol_group_list extends controller_socommon
	{
		protected static $so;

		/**
		 * Get a static reference to the storage object associated with this model object
		 *
		 * @return controller_socontrol_group the storage object
		 */
		public static function get_instance()
		{
			if (self::$so == null)
			{
				self::$so = CreateObject('controller.socontrol_group_list');
			}
			return self::$so;
		}

		/**
		 * Function for adding a new control_group_list to the database.
		 *
		 * @param $control_group_list the control_group_list group to be added
		 * @return int id of the new control_group_list object
		 */
		function add(&$control_group_list)
		{
			$cols = array(
					'control_id',
					'control_group_id',
					'order_nr'
			);

			$values = array(
				$this->marshal($control_group_list->get_control_id(), 'int'),
				$this->marshal($control_group_list->get_control_group_id(), 'int'),
				$this->marshal($control_group_list->get_order_nr(), 'int')
			);

			$result = $this->db->query('INSERT INTO controller_control_group_list (' . join(',', $cols) . ') VALUES (' . join(',', $values) . ')', __LINE__,__FILE__);

			if($result)
			{
				// Get the new control group ID and return it
				return $this->db->get_last_insert_id('controller_control_group_list', 'id');
			}
			else
			{
				return 0;
			}
		}

		/**
		 * Update the database values for an existing control_group_list object.
		 *
		 * @param $control_group_list the control_group_list to be updated
		 * @return boolean true if successful, false otherwise
		 */

		function update($control_group_list)
		{
			$id = intval($control_group_list->get_id());

			$values = array(
				'control_id = ' . $this->marshal($control_group_list->get_control_id(), 'string'),
				'control_group_id = '. $this->marshal($control_group_list->get_control_group_id(), 'int'),
				'order_nr = ' . $this->marshal($control_group_list->get_order_nr(), 'int')
			);

			//var_dump('UPDATE activity_activity SET ' . join(',', $values) . " WHERE id=$id");
			$result = $this->db->query('UPDATE controller_control_group_list SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);

			return $result;
		}

		/**
		 * Get single control_group_list object
		 * 
		 * @param	$id	id of the control_group_list to return
		 * @return a control_group_list
		 */
		function get_single($id)
		{
			$id = (int)$id;

			$sql = "SELECT p.* FROM controller_control_group_list p WHERE p.id = " . $id;
			$this->db->query($sql, __LINE__, __FILE__);

			if($this->db->next_record())
			{
				$control_group_list = new controller_control_group_list($this->unmarshal($this->db->f('id'), 'int'));
				$control_group_list->set_control_id($this->unmarshal($this->db->f('control_id'), 'int'));
				$control_group_list->set_control_group_id($this->unmarshal($this->db->f('control_group_id'), 'int'));
				$control_group_list->set_order_nr($this->unmarshal($this->db->f('order_nr'), 'int'));

				return $control_group_list; 
			}
			else
			{
				return null;
			}
		}

		/**
		 * Get single control_group_list object by specifying parameters control id and control group id 
		 * 
		 * @param	$control_id control id
		 * @param	$control group id control group id
		 * @return a control_group_list
		 */
		function get_group_list_by_control_and_group($control_id, $control_group_id)
		{
			$control_id = (int) $control_id;
			$control_group_id = (int) $control_group_id;

			$sql = "SELECT p.* FROM controller_control_group_list p WHERE p.control_id={$control_id} AND p.control_group_id={$control_group_id}";
			$this->db->query($sql, __LINE__, __FILE__);

			if($this->db->next_record())
			{
				$control_group_list = new controller_control_group_list($this->unmarshal($this->db->f('id'), 'int'));
				$control_group_list->set_control_id($this->unmarshal($this->db->f('control_id'), 'int'));
				$control_group_list->set_control_group_id($this->unmarshal($this->db->f('control_group_id'), 'int'));
				$control_group_list->set_order_nr($this->unmarshal($this->db->f('order_nr'), 'int'));

				return $control_group_list; 
			}
			else
			{
				return null;
			}
		}

		/**
		 * Delete a row in control_group_list table 
		 * 
		 * @param	$control_id control id
		 * @param	$control group id control group id
		 * @return a control_group_list
		 */
		function delete($control_id, $control_group_id)
		{
			$control_id = (int) $control_id;
			$control_group_id = (int) $control_group_id;

			$result = $this->db->query("DELETE FROM controller_control_group_list WHERE control_id = $control_id AND control_group_id = $control_group_id");

			return $result;
		}

		/**
		 * Delete several rows in control_group_list table 
		 * 
		 * @param	$control_id control id
		 * @return a control_group_list
		 */
		function delete_control_groups($control_id)
		{
			$control_id = (int) $control_id;
			$result = $this->db->query("DELETE FROM controller_control_group_list WHERE control_id = $control_id");

			return $result;
		}

		/**
		 * Get array with control group objects represented as objects or arrays   
		 * 
		 * @param	$control_id control id
		 * @param	$returnType representation of returned control grups, as objects or as arrays  
		 * @return a control_group_list
		 */
		function get_control_groups_by_control($control_id, $returnType = "object")
		{
			$control_id = (int) $control_id;
			$sql =  "SELECT cg.*, cgl.order_nr "; 
			$sql .= "FROM controller_control_group_list cgl, controller_control_group cg "; 
			$sql .= "WHERE cgl.control_id={$control_id} ";
			$sql .= "AND cgl.control_group_id=cg.id ";
			$sql .= "ORDER BY cgl.order_nr ASC";
			
			$this->db->query($sql);

			$control_group_list = array();

			while($this->db->next_record())
			{
				$control_group = new controller_control_group($this->unmarshal($this->db->f('id'), 'int'));
				$control_group->set_group_name($this->unmarshal($this->db->f('group_name', true), 'string'));
				$control_group->set_procedure_id($this->unmarshal($this->db->f('procedure_id'), 'int'));
				$control_group->set_control_area_id($this->unmarshal($this->db->f('control_area_id'), 'int'));
				$control_group->set_building_part_id($this->unmarshal($this->db->f('building_part_id'), 'int'));
				$control_group->set_component_location_id($this->unmarshal($this->db->f('component_location_id'), 'int'));

				$component_criteria = $this->db->f('component_criteria') ? unserialize($this->db->f('component_criteria',true)) : array();
				$control_group->set_component_criteria($component_criteria);

				if($returnType == "array")
				{
					$control_group_list[] = $control_group->toArray();
				}
				else
				{
					$control_group_list[] = $control_group;
				}
			}

			return $control_group_list;
		}


		protected function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count){}

		function get_id_field_name($extended_info = false){}

		function populate(int $control_group_id, &$control_group){}

	}
