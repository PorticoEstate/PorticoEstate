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
 	* @version $Id:$
	*/
	phpgw::import_class('logistic.socommon');

	include_class('logistic', 'activity', 'inc/model/');
	
	class logistic_soactivity extends logistic_socommon
	{
		protected static $so;

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
		
		protected function add(logistic_activity &$activity)
		{
			$cols = array(
				'name',
				'parent_id',
				'project_id',
				'start_date',
				'end_date',
				'responsible_user_id',
				'update_user',
				'update_date'
			);
			
			$values = array(
				$this->marshal($activity->get_name(), 'string'),
				$this->marshal($activity->get_parent_id(), 'int'),
				$this->marshal($activity->get_project_id(), 'int'),
				$this->marshal($activity->get_start_date(), 'int'),
				$this->marshal($activity->get_end_date(), 'int'),
				$this->marshal($activity->get_responsible_user_id(), 'int'),
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
		
		protected function update(logistic_activity $activity)
		{
			$id = intval($activity->get_id());
			
			$values = array(
				'name=' . $this->marshal($activity->get_name(), 'string'),
				'parent_id=' . $this->marshal($activity->get_parent_id(), 'int'),
				'project_id=' . $this->marshal($activity->get_project_id(), 'int'),
				'start_date=' . $this->marshal($activity->get_start_date(), 'int'),
				'end_date=' . $this->marshal($activity->get_end_date(), 'int'),
				'responsible_user_id=' . $this->marshal($activity->get_responsible_user_id(), 'int'),
				'update_user=' . $this->marshal($activity->get_update_user(), 'int'),
				'update_date=' . $this->marshal(strtotime('now'), 'int')
			);
			
			$result = $this->db->query('UPDATE lg_activity SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);

			return $result;
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
			}/*
			if(isset($filters['project']) && !$filters['project'] == '')
			{
				$filter_clauses[] = "activity.project_id = {$this->marshal($filters['project'], 'int')}";
			}
			if(isset($filters['user']) && !$filters['user'] == '')
			{
				$filter_clauses[] = "activity.responsible_user_id = {$this->marshal($filters['user'], 'int')}";
			}
*/
			if(count($filter_clauses))
			{
				$clauses[] = join(' AND ', $filter_clauses);
			}

			$condition =  join(' AND ', $clauses);

			//$joins = " {$this->left_join} controller_control_area ON (controller_procedure.control_area_id = controller_control_area.id)";
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

			//var_dump("SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}");

			return "SELECT {$cols} FROM {$tables} WHERE {$condition} {$order}";
		}

		protected function populate(int $activity_id, &$activity)
		{
			if($activity == null)
			{
				$activity = new logistic_activity((int) $activity_id);
				
				$activity->set_name($this->unmarshal($this->db->f('name'), 'string'));
				$activity->set_parent_id($this->unmarshal($this->db->f('parent_id'), 'int'));
				$activity->set_project_id($this->unmarshal($this->db->f('project_id'), 'int'));
				$activity->set_start_date($this->unmarshal($this->db->f('start_date'), 'int'));
				$activity->set_end_date($this->unmarshal($this->db->f('end_date'), 'int'));
				$activity->set_responsible_user_id($this->unmarshal($this->db->f('responsible_user_id'), 'int'));
				$activity->set_update_date($this->unmarshal($this->db->f('update_date'), 'int'));
				$activity->set_update_user($this->unmarshal($this->db->f('update_user'), 'int'));
			}
		
			return $activity;
		}
	}
?>
