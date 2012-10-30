<?php
	/**
	* phpGroupWare - logistic: a part of a Facilities Management System.
	*
	* @author Erik Holm-Larsen <erik.holm-larsen@bouvet.no>
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
	* @subpackage logistic
 	* @version $Id$
	*/
	phpgw::import_class('logistic.socommon');

	include_class('logistic', 'activity', '/inc/model/');

	class logistic_soactivity extends logistic_socommon
	{
		protected static $so;
		protected $activity_tree = array();

		/**
		 * Get a static reference to the storage object associated with this model object
		 *
		 * @return logistic_soactivity the storage object
		 */
		public static function get_instance()
		{
			if (self::$so == null)
			{
				self::$so = CreateObject('logistic.soactivity');
			}
			return self::$so;
		}

		protected function add(&$activity)
		{
			$cols = array(
				'parent_activity_id',
				'name',
				'description',
				'project_id',
				'start_date',
				'end_date',
				'responsible_user_id',
				'create_user',
				'create_date',
				'update_user',
				'update_date'
			);

			$values = array(
				$this->marshal($activity->get_parent_id(), 'int'),
				$this->marshal($activity->get_name(), 'string'),
				$this->marshal($activity->get_description(), 'string'),
				$this->marshal($activity->get_project_id(), 'int'),
				$this->marshal($activity->get_start_date(), 'int'),
				$this->marshal($activity->get_end_date(), 'int'),
				$this->marshal($activity->get_responsible_user_id(), 'int'),
				$this->marshal($activity->get_create_user(), 'int'),
				$this->marshal(strtotime('now'), 'int'),
				$this->marshal($activity->get_update_user(), 'int'),
				$this->marshal(strtotime('now'), 'int')
			);

			$sql = 'INSERT INTO lg_activity (' . join(',', $cols) . ') VALUES (' . join(',', $values) . ')';
			$result = $this->db->query($sql, __LINE__,__FILE__);

			if($result)
			{
				// Return the new activity ID
				return $this->db->get_last_insert_id('lg_activity', 'id');
			}
			else
			{
				return 0;
			}
		}

		protected function update($activity)
		{
			$id = intval($activity->get_id());

			$values = array(
				'name=' . $this->marshal($activity->get_name(), 'string'),
				'description=' . $this->marshal($activity->get_description(), 'string'),
				'parent_activity_id=' . $this->marshal($activity->get_parent_id(), 'int'),
				'project_id=' . $this->marshal($activity->get_project_id(), 'int'),
				'start_date=' . $this->marshal($activity->get_start_date(), 'int'),
				'end_date=' . $this->marshal($activity->get_end_date(), 'int'),
				'responsible_user_id=' . $this->marshal($activity->get_responsible_user_id(), 'int'),
				'update_user=' . $this->marshal($activity->get_update_user(), 'int'),
				'update_date=' . $this->marshal(strtotime('now'), 'int')
			);

			$result = $this->db->query('UPDATE lg_activity SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);

			if($result)
			{
				// Return the new activity ID
				return $id;
			}
			else
			{
				return 0;
			}
		}

		protected function get_id_field_name()
		{
			if(!$extended_info)
			{
				$ret = 'id';
			}
			else
			{
				$ret = array
				(
					'table'			=> 'activity', // alias
					'field'			=> 'id',
					'translated'	=> 'id'
				);
			}

			return $ret;
		}
		
		protected function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count)
		{
			$clauses = array('1=1');

			if($search_for)
			{
				$like_pattern = "'%" . $this->db->db_addslashes($search_for) . "%'";
				$like_clauses = array();
				switch($search_type)
				{
					default:
						$like_clauses[] = "activity.name $this->like $like_pattern";
						break;
				}
				if(count($like_clauses))
				{
					$clauses[] = '(' . join(' OR ', $like_clauses) . ')';
				}
			}

			$filter_clauses = array();
			if(isset($filters[$this->get_id_field_name()]))
			{
				$filter_clauses[] = "activity.id = {$this->marshal($filters[$this->get_id_field_name()],'int')}";
			}
			if(isset($filters['project']) && !$filters['project'] == '')
			{
				$filter_clauses[] = "activity.project_id = {$this->marshal($filters['project'], 'int')}";
			}
			if(isset($filters['user']) && !$filters['user'] == '')
			{
				$filter_clauses[] = "activity.responsible_user_id = {$this->marshal($filters['user'], 'int')}";
			}

			if(count($filter_clauses))
			{
				$clauses[] = join(' AND ', $filter_clauses);
			}

			$condition =  join(' AND ', $clauses);

			$tables = "lg_activity activity";

			if($return_count) // We should only return a count
			{
				$cols = 'COUNT(DISTINCT(activity.id)) AS count';
			}
			else
			{
				$cols .= "* ";
			}

			$dir = $ascending ? 'ASC' : 'DESC';
			$order = $sort_field ? "ORDER BY {$this->marshal($sort_field, 'field')} $dir ": '';

			return "SELECT {$cols} FROM {$tables} WHERE {$condition} {$order}";
		}


		/**
		 * Method for retreiving objects.
		 * 
		 * @param $start_index int with index of first object.
		 * @param $num_of_objects int with max number of objects to return.
		 * @param $sort_field string representing the object field to sort on.
		 * @param $ascending boolean true for ascending sort on sort field, false
		 * for descending.
		 * @param $search_for string with free text search query.
		 * @param $search_type string with the query type.
		 * @param $filters array with key => value of filters.
		 * @return array of objects. May return an empty
		 * array, never null. The array keys are the respective index numbers.
		 */
		public function get(int $start_index, int $num_of_objects, string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters)
		{
			$results = array();			// Array to store result objects
			$map = array();				// Array to hold number of records per target object
			$check_map = array();		// Array to hold the actual number of record read per target object
			$object_ids = array(); 		// All of the object ids encountered
			$added_object_ids = array();// All of the added objects ids

			// Retrieve information about the table name and the name and alias of id column
			// $break_on_limit - 	flag indicating whether to break the loop when the number of records 
			// 						for all the result objects are traversed
			$id_field_name_info = $this->get_id_field_name(true);
			if(is_array($id_field_name_info))
			{
				$break_on_limit = true;
				$id_field_name = $id_field_name_info['translated'];
			}
			else
			{
				$break_on_limit = false;
				$id_field_name = $id_field_name_info;
			}

			// Special case: Sort on id field. Always changed to the id field name.
			// $break_when_num_of_objects_reached - flag indicating to break the loop when the number of 
			//		results are reached and we are sure that the records are ordered by the id
			if($sort_field == null || $sort_field == 'id' || $sort_field == '')
			{
				$sort_field = $id_field_name;
				$break_when_num_of_objects_reached = true;
			}
			else
			{
				$break_when_num_of_objects_reached = false;
			}

			// Only allow positive start index
			if($start_index < 0)
			{
				$start_index = 0;
			}

			$sql = $this->get_query($sort_field, $ascending, $search_for, $search_type, $filters, false);
			$ret = $this->read_tree($sql, $filters);

			return $ret;
		}

		/**
		 * used for retrive the path for a particular node from a hierarchy
		 *
		 * @param integer $node is the id of the node we want the path of
		 * @return array $path Path
		 */

		public function get_path($node)
		{
			$node = (int) $node;
			$table = "lg_activity";
			$sql = "SELECT id, name, parent_activity_id FROM {$table} WHERE id = {$node}";

			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();

			$parent_id = $this->db->f('parent_activity_id');
			$name = $this->db->f('name', true);
			$path = array($name);
			if ($parent_id)
			{
				$path = array_merge($this->get_path($parent_id), $path);
			}
			return $path;
		}


		/**
		 * Method for retreiving hierarchy.
		 * 
		 * @param $sql string database query.
		 * @param $filters array with key => value of filters.
		 * @return array of objects. May return an empty
		 * array, never null. The array keys are the respective index numbers.
		 */

		public function read_tree($sql, $filters)
		{
			if($filters['activity'])
			{
				$filter_clause = "activity.id = {$this->marshal($filters['activity'], 'int')}";
			}
			else if($filters['id'])
			{
				$filter_clause = "activity.id = {$this->marshal($filters['id'], 'int')}";
			}
			else
			{
				$filter_clause = "(parent_activity_id = 0 OR parent_activity_id IS NULL)";
			}

			$sql_parts = explode('1=1',$sql); // Split the query to insert extra condition on test for break
			$sql = "{$sql_parts[0]} {$filter_clause} {$sql_parts[1]}";

			$this->db->query($sql,__LINE__,__FILE__);

			$this->activity_tree = array();
			while ($this->db->next_record())
			{
				$id	= $this->db->f('id');
				$activities[$id] = array
					(
						'id'			=> $id,
						'name'			=> $this->db->f('name',true),
						'parent_id'		=> 0
					);
			}

			foreach($activities as $activity)
			{
				$this->activity_tree[] = array
					(
						'id'	=> $activity['id'],
						'name'	=> $activity['name']
					);
				$this->get_children($activity['id'], 1);
			}

			$result = array();
			foreach($this->activity_tree as $_activity)
			{
				$this->db->query("SELECT * FROM lg_activity WHERE id ={$_activity['id']}",__LINE__,__FILE__);
				$this->db->next_record();
				$activity_obj = $this->populate($_activity['id']);
				$activity_obj->set_name($_activity['name']);
				$result[] = $activity_obj;
			}

			return $result;
		}

		/**
		 * Method for retreiving sublevels of a hierarchy.
		 * 
		 * @param $parent int any children belong to this parent
		 * @param $level int which level to search.
		 * @return array of children
		 */

		protected function get_children($parent, $level)
		{
			$db = clone($this->db);
			$table = "lg_activity";
			$sql = "SELECT id, name FROM {$table} WHERE parent_activity_id = {$parent} ORDER BY name ASC";
			$db->query($sql,__LINE__,__FILE__);

			while ($db->next_record())
			{
				$id	= $db->f('id');
				$this->activity_tree[] = array
					(
						'id'		=> $id,
						'name'		=> str_repeat('..',$level).$db->f('name',true),
						'parent_id'	=> $parent
					);
				$this->get_children($id, $level+1);
			}
			return $this->activity_tree;
		} 


		protected function populate(int $activity_id, &$activity)
		{
			if($activity == null)
			{
				$activity = new logistic_activity((int) $activity_id);

				$activity->set_name($this->unmarshal($this->db->f('name'), 'string'));
				$activity->set_description($this->unmarshal($this->db->f('description'), 'string'));
				$activity->set_parent_id($this->unmarshal($this->db->f('parent_activity_id'), 'int'));
				$activity->set_project_id($this->unmarshal($this->db->f('project_id'), 'int'));
				$activity->set_start_date($this->unmarshal($this->db->f('start_date'), 'int'));
				$activity->set_end_date($this->unmarshal($this->db->f('end_date'), 'int'));
				$activity->set_responsible_user_id($this->unmarshal($this->db->f('responsible_user_id'), 'int'));
				$activity->set_create_date($this->unmarshal($this->db->f('create_date'), 'int'));
				$activity->set_create_user($this->unmarshal($this->db->f('create_user'), 'int'));
				$activity->set_update_date($this->unmarshal($this->db->f('update_date'), 'int'));
				$activity->set_update_user($this->unmarshal($this->db->f('update_user'), 'int'));
			}

			return $activity;
		}

		public function get_project_name($id)
		{
			if($id && is_numeric($id))
			{
				$result = $this->db->query('SELECT name FROM lg_project WHERE id='.$id, __LINE__,__FILE__);

				while($this->db->next_record())
				{
					// Return the new activity ID
					return $this->db->f('name');
				}
			}
		}

		public function get_responsible_user($user_id)
		{
			if($user_id && is_numeric($user_id))
			{
				$account = $GLOBALS['phpgw']->accounts->get($user_id);
				if(isset($account))
				{
				 return $account->__toString();
				}
				else
				{
					return lang('nobody');
				}
			}
		}
	}
