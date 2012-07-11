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

	include_class('controller', 'control_area', 'inc/model/');

	class controller_socontrol_area extends controller_socommon
	{
		protected static $so;

		/**
		 * Get a static reference to the storage object associated with this model object
		 *
		 * @return controller_socontrol_area storage object
		 */
		public static function get_instance()
		{
			if (self::$so == null)
			{
				self::$so = CreateObject('controller.socontrol_area');
			}
			return self::$so;
		}

		/**
		 * Add new control area to the database.
		 *
		 * @param control area object 
		 * @return true if successful, false otherwise
		 */
		function add(&$control_area)
		{
			$control_area = $control_area->get_control_area();

			$sql = "INSERT INTO controller_control_area (type_name) VALUES ('$title')";
			$result = $this->db->query($sql, __LINE__,__FILE__);

			if(isset($result)) {
				// Set the new party ID
				$control_area->set_id($this->db->get_last_insert_id('controller_control_area', 'id'));
				// Forward this request to the update method
				return $this->update($control_area);
			}
			else
			{
				return false;
			}
		}

		/**
		 * Update the database values for an existing control area object.
		 *
		 * @param $control_area the control area to be updated
		 * @return boolean true if successful, false otherwise
		 */
		function update($control_area)
		{
			$id = intval($control_area->get_id());

			$values = array(
				'$type_name = ' . $this->marshal($control_area->get_type_name(), 'string')
			);

			$result = $this->db->query('UPDATE controller_control_area SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);

			return isset($result);
		}

		/**
		 * Get single control area
		 * 
		 * @param	$id	id of the control area to be returned
		 * @return control area object
		 */
		function get_single($id)
		{
			$id = (int)$id;

			$sql = "SELECT p.* FROM controller_control_area p WHERE p.id = " . $id;
			$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
			$this->db->next_record();

			$control_area = new controller_control_area($this->unmarshal($this->db->f('id', true), 'int'));
			$control_area->set_title($this->unmarshal($this->db->f('title', true), 'string'));

			return $control_area;
		}

		/**
		 * Get a list of control area objects matching the specific filters
		 * 
		 * @param $start search result offset
		 * @param $results number of results to return
		 * @param $sort field to sort by
		 * @param $query LIKE-based query string
		 * @param $filters array of custom filters
		 * @return list of rental_composite objects
		 */
		function get_control_area_array($start = 0, $results = 1000, $sort = null, $dir = '', $query = null, $search_option = null, $filters = array())
		{
			$results = array();

			$order = $sort ? "ORDER BY $sort $dir ": '';

			$sql = "SELECT * FROM controller_control_area $order";
			$this->db->limit_query($sql, $start, __LINE__, __FILE__, $limit);

			while ($this->db->next_record()) 
			{
				$control_area = new controller_control_area($this->unmarshal($this->db->f('id', true), 'int'));
				$control_area->set_title($this->unmarshal($this->db->f('title', true), 'string'));

				$results[] = $control_area;
			}

			return $results;
		}

		/**
		 * Get a list of control area arrays matching the specific filters
		 * 
		 * @param $start search result offset
		 * @param $results number of results to return
		 * @param $sort field to sort by
		 * @param $query LIKE-based query string
		 * @param $filters array of custom filters
		 * @return list of rental_composite objects
		 */
		function get_control_areas_as_array($start = 0, $results = 1000, $sort = null, $dir = '', $query = null, $search_option = null, $filters = array())
		{
			$results = array();

			$order = $sort ? "ORDER BY $sort $dir ": '';

			$sql = "SELECT * FROM controller_control_area $order";
			$this->db->limit_query($sql, $start, __LINE__, __FILE__, $limit);

			while ($this->db->next_record()) 
			{
				$control_area = new controller_control_area($this->unmarshal($this->db->f('id', true), 'int'));
				$control_area->set_title($this->unmarshal($this->db->f('title', true), 'string'));

				$results[] = $control_area->toArray();
			}

			return $results;
		}

		function get_control_area_select_array()
		{
				$results = array();
				$results[] = array('id' =>  0,'name' => lang('Not selected'));
				$this->db->query("SELECT id, title as name FROM controller_control_area ORDER BY name ASC", __LINE__, __FILE__);
				while ($this->db->next_record())
				{
					$results[] = array('id' => $this->db->f('id', false),
									   'name' => $this->db->f('name', false));
				}
				return $results;
		}

		function get_id_field_name($extended_info = false){}

		protected function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count){}
		protected function populate(int $object_id, &$object){}
	}
