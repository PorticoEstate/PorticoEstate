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

	include_class('controller', 'control_item', 'inc/model/');

	class controller_socontrol_item extends controller_socommon
	{
		protected static $so;

		/**
		 * Get a static reference to the storage object associated with this model object
		 *
		 * @return controller_soparty the storage object
		 */
		public static function get_instance()
		{
			if (self::$so == null) {
				self::$so = CreateObject('controller.socontrol_item');
			}
			return self::$so;
		}

		/**
		 * Function for adding a new activity to the database. Updates the activity object.
		 *
		 * @param activitycalendar_activity $activity the party to be added
		 * @return bool true if successful, false otherwise
		 */
		function add(&$control_item)
		{
			$cols = array(
					'title',
					'required',
					'what_to_do',
					'how_to_do',
					'control_group_id'
			);

			$values = array(
				$this->marshal($control_item->get_title(), 'string'),
				$this->marshal(($control_item->get_required() ? 'true' : 'false'), 'bool'),
				$this->marshal($control_item->get_what_to_do(), 'string'),
				$this->marshal($control_item->get_how_to_do(), 'string'),
				$this->marshal($control_item->get_control_group_id(), 'int')
			);

			$result = $this->db->query('INSERT INTO controller_control_item (' . join(',', $cols) . ') VALUES (' . join(',', $values) . ')', __LINE__,__FILE__);
			//$result = $this->db->query($sql, __LINE__,__FILE__);

			if(isset($result)) {
				// return the new control item ID
				return $this->db->get_last_insert_id('controller_control_item', 'id');
				// Forward this request to the update method
				//return $this->update($control_item);
			}
			else
			{
				return 0;
			}
		}

		/**
		 * Update the database values for an existing activity object.
		 *
		 * @param $activity the activity to be updated
		 * @return boolean true if successful, false otherwise
		 */

		function update($control_item)
		{
			$id = intval($control_item->get_id());

			$values = array(
				'title = ' . $this->marshal($control_item->get_title(), 'string'),
				'required = ' . $this->marshal(($control_item->get_required() ? 'true' : 'false'), 'bool'),
				'what_to_do = ' . $this->marshal($control_item->get_what_to_do(), 'string'),
				'how_to_do = ' . $this->marshal($control_item->get_how_to_do(), 'string'),
				'control_group_id = ' . $this->marshal($control_item->get_control_group_id(), 'int')
			);

			//var_dump('UPDATE controller_control_item SET ' . join(',', $values) . " WHERE id=$id");
			$result = $this->db->query('UPDATE controller_control_item SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);

			return isset($result);
		}

		/**
		 * Get single control item
		 * 
		 * @param	$id	id of the control_item to return
		 * @return a controller_control_item
		 */
		function get_single($id)
		{
			$id = (int)$id;
			$joins = " {$this->left_join} controller_control_group ON (p.control_group_id = controller_control_group.id)";
			$sql = "SELECT p.*, controller_control_group.group_name AS control_group_name FROM controller_control_item p {$joins} WHERE p.id = " . $id;
			$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
			$this->db->next_record();

			$control_item = new controller_control_item($this->unmarshal($this->db->f('id', true), 'int'));
			$control_item->set_title($this->unmarshal($this->db->f('title', true), 'string'));
			$control_item->set_required($this->unmarshal($this->db->f('required', true), 'bool'));
			$control_item->set_what_to_do($this->unmarshal($this->db->f('what_to_do', true), 'string'));
			$control_item->set_how_to_do($this->unmarshal($this->db->f('how_to_do', true), 'string'));
			$control_item->set_control_group_id($this->unmarshal($this->db->f('control_group_id', true), 'int'));
			$control_item->set_control_group_name($this->unmarshal($this->db->f('control_group_name', true), 'string'));
			$control_item->set_type($this->unmarshal($this->db->f('type', true), 'string'));
			
			return $control_item;
		}

		/**
		 * Get a list of procedure objects matching the specific filters
		 * 
		 * @param $start search result offset
		 * @param $results number of results to return
		 * @param $sort field to sort by
		 * @param $query LIKE-based query string
		 * @param $filters array of custom filters
		 * @return list of rental_composite objects
		 */
		function get_control_item_array($start = 0, $results = 1000, $sort = null, $dir = '', $query = null, $search_option = null, $filters = array())
		{
			$results = array();

			//$condition = $this->get_conditions($query, $filters,$search_option);
			$order = $sort ? "ORDER BY $sort $dir ": '';

			//$sql = "SELECT * FROM controller_procedure WHERE $condition $order";
			$sql = "SELECT * FROM controller_control_item $order";
			//var_dump($sql);
			$this->db->limit_query($sql, $start, __LINE__, __FILE__, $limit);

			while ($this->db->next_record()) {
				$control_item = new controller_control_item($this->unmarshal($this->db->f('id', true), 'int'));
				$control_item->set_title($this->unmarshal($this->db->f('title', true), 'string'));
				$control_item->set_required($this->unmarshal($this->db->f('required', true), 'boolean'));
				$control_item->set_what_to_do($this->unmarshal($this->db->f('what_to_do', true), 'string'));
				$control_item->set_how_to_do($this->unmarshal($this->db->f('how_to_do', true), 'string'));
				$control_item->set_control_group_id($this->unmarshal($this->db->f('control_group_id', true), 'int'));

				$results[] = $control_item;
			}

			return $results;
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
					'table'			=> 'controller', // alias
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
				switch($search_type){
					default:
						$like_clauses[] = "controller_control_item.title $this->like $like_pattern";
						$like_clauses[] = "controller_control_item.what_to_do $this->like $like_pattern";
						$like_clauses[] = "controller_control_item.how_to_do $this->like $like_pattern";
						break;
				}

				if(count($like_clauses))
				{
					$clauses[] = '(' . join(' OR ', $like_clauses) . ')';
				}
			}

			if(isset($filters[$this->get_id_field_name()]))
			{
				$filter_clauses[] = "controller_control_item.id = {$this->marshal($filters[$this->get_id_field_name()],'int')}";
			}
			if(isset($filters['available']))
			{
				$filter_clauses[] = "(controller_control_item.control_group_id IS NULL OR controller_control_item.control_group_id=0)";
			}
			if(isset($filters['control_groups']))
			{
				$filter_clauses[] = "controller_control_item.control_group_id = {$this->marshal($filters['control_groups'],'int')}";
			}

			if(count($filter_clauses))
			{
				$clauses[] = join(' AND ', $filter_clauses);
			}


			$condition =  join(' AND ', $clauses);

			$tables = "controller_control_item";
			$joins = " {$this->left_join} controller_control_group ON (controller_control_item.control_group_id = controller_control_group.id)";

			if($return_count)
			{
				$cols = 'COUNT(DISTINCT(controller_control_item.id)) AS count';
			}
			else
			{
				$cols = 'controller_control_item.id, controller_control_item.title, required, what_to_do, how_to_do, controller_control_item.control_group_id, controller_control_group.group_name AS control_group_name';
			}

			$dir = $ascending ? 'ASC' : 'DESC';
			if($sort_field == 'title')
			{
				$sort_field = 'controller_control_item.title';
			}
			$order = $sort_field ? "ORDER BY {$this->marshal($sort_field, 'field')} $dir ": '';

			//var_dump("SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}");
			//return "SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}";

			return "SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}";
		}

		function get_control_items($control_group_id, $return_type = "return_object")
		{
			$results = array();

			$sql = "SELECT * FROM controller_control_item WHERE control_group_id={$control_group_id}";
			$this->db->query($sql);

			while ($this->db->next_record()) {
				$control_item = new controller_control_item($this->unmarshal($this->db->f('id', true), 'int'));
				$control_item->set_title($this->unmarshal($this->db->f('title', true), 'string'));
				$control_item->set_required($this->unmarshal($this->db->f('required', true), 'boolean'));
				$control_item->set_what_to_do($this->unmarshal($this->db->f('what_to_do', true), 'string'));
				$control_item->set_how_to_do($this->unmarshal($this->db->f('how_to_do', true), 'string'));
				$control_item->set_control_group_id($this->unmarshal($this->db->f('control_group_id', true), 'int'));

				if($return_type == "return_object")
					$results[] = $control_item;
				else
					$results[] = $control_item->toArray();
			}

			return $results;
		}

		function get_control_items_by_control($control_id, $returnType = "return_object")
		{
			$results = array();

			$sql  = "SELECT ci.* ";
			$sql .= "FROM controller_control_item ci, controller_control_item_list cl ";
			$sql .= "WHERE cl.control_id=$control_id AND cl.control_item_id = ci.id ";
									
			$this->db->query($sql);

			while ($this->db->next_record()) {
				$control_item = new controller_control_item($this->unmarshal($this->db->f('id', true), 'int'));
				$control_item->set_title($this->unmarshal($this->db->f('title', true), 'string'));
				$control_item->set_required($this->unmarshal($this->db->f('required', true), 'boolean'));
				$control_item->set_what_to_do($this->unmarshal($this->db->f('what_to_do', true), 'string'));
				$control_item->set_how_to_do($this->unmarshal($this->db->f('how_to_do', true), 'string'));
				$control_item->set_control_group_id($this->unmarshal($this->db->f('control_group_id', true), 'int'));
				$control_item->set_type($this->unmarshal($this->db->f('type', true), 'string'));

				if($returnType == "return_array")
					$results[] = $control_item->toArray();
				else
					$results[] = $control_item;
			}
			
			return $results;
		}

		function get_control_items_by_control_and_group($control_id, $control_group_id)
		{
			$results = array();

			$sql = "SELECT ci.* FROM controller_control_item ci, controller_control_item_list cl, controller_control c ";
			$sql .= "WHERE c.id=$control_id AND c.id=cl.control_id AND cl.control_item_id=ci.id AND ci.control_group_id=$control_group_id";
			$this->db->limit_query($sql, $start, __LINE__, __FILE__, $limit);

			while ($this->db->next_record()) {
				$control_item = new controller_control_item($this->unmarshal($this->db->f('id', true), 'int'));
				$control_item->set_title($this->unmarshal($this->db->f('title', true), 'string'));
				$control_item->set_required($this->unmarshal($this->db->f('required', true), 'boolean'));
				$control_item->set_what_to_do($this->unmarshal($this->db->f('what_to_do', true), 'string'));
				$control_item->set_how_to_do($this->unmarshal($this->db->f('how_to_do', true), 'string'));
				$control_item->set_control_group_id($this->unmarshal($this->db->f('control_group_id', true), 'int'));
				//$control_item->set_control_group_name($this->unmarshal($this->db->f('control_group_name', true), 'string'));

				$results[] = $control_item->toArray();
			}

			return $results;
		}

		function populate(int $control_item_id, &$control_item)
		{
			if($control_item == null) {
				$control_item = new controller_control_item((int) $control_item_id);

				$control_item->set_title($this->unmarshal($this->db->f('title', true), 'string'));
				$control_item->set_required($this->unmarshal($this->db->f('required', true), 'boolean'));
				$control_item->set_what_to_do($this->unmarshal($this->db->f('what_to_do', true), 'string'));
				$control_item->set_how_to_do($this->unmarshal($this->db->f('how_to_do', true), 'string'));
				$control_item->set_control_group_id($this->unmarshal($this->db->f('control_group_id', true), 'int'));
				$control_item->set_control_group_name($this->unmarshal($this->db->f('control_group_name', true), 'string'));
			}

			return $control_item;
		}

	}
