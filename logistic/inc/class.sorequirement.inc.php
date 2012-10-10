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

	include_class('logistic', 'requirement', '/inc/model/');

	class logistic_sorequirement extends logistic_socommon
	{
		protected static $so;

		protected function add(&$requirement)
		{
			$cols = array(
				'activity_id',
				'date_from',
				'date_to',
				'no_of_elements',
				'location_id',
				'create_user',
				'create_date'
			);

			$values = array(
				$this->marshal($requirement->get_activity_id(), 'int'),
				$this->marshal($requirement->get_date_from(), 'string'),
				$this->marshal($requirement->get_date_to(), 'string'),
				$this->marshal($requirement->get_no_of_elements(), 'int'),
				$this->marshal($requirement->get_location_id(), 'int'),
				$this->marshal($requirement->get_create_user(), 'int'),
				$this->marshal(strtotime('now'), 'int')
			);

			$sql = 'INSERT INTO lg_requirement (' . join(',', $cols) . ') VALUES (' . join(',', $values) . ')';
			$result = $this->db->query($sql, __LINE__,__FILE__);

			if($result)
			{
				return $this->db->get_last_insert_id('lg_requirement', 'id');
			}
			else
			{
				return 0;
			}
		}

		protected function update($requirement)
		{
			$id = intval($requirement->get_id());

			$values = array(
				'activity_id=' . $this->marshal($requirement->get_activity_id(), 'int'),
				'date_from=' . $this->marshal($requirement->get_date_from(), 'int'),
				'date_to=' . $this->marshal($requirement->get_date_to(), 'int'),
				'no_of_elements=' . $this->marshal($requirement->get_no_of_elements(), 'int'),
				'location_id=' . $this->marshal($requirement->get_location_id(), 'int')
			);

			$result = $this->db->query('UPDATE lg_requirement SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);

			if($result)
			{
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
					'table'			=> 'requirement', // alias
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
						$like_clauses[] = "requirement.name $this->like $like_pattern";
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
				$filter_clauses[] = "requirement.id = {$this->marshal($filters[$this->get_id_field_name()],'int')}";
			}
			if(isset($filters['activity']) && !$filters['activity'] == '')
			{
				$filter_clauses[] = "requirement.activity_id = {$this->marshal($filters['activity'], 'int')}";
			}

			if(count($filter_clauses))
			{
				$clauses[] = join(' AND ', $filter_clauses);
			}

			$condition =  join(' AND ', $clauses);

			//$joins = " {$this->left_join} controller_control_area ON (controller_procedure.control_area_id = controller_control_area.id)";

			$tables = "lg_requirement requirement";

			if($return_count) // We should only return a count
			{
				$cols = 'COUNT(DISTINCT(requirement.id)) AS count';
			}
			else
			{
				$cols .= "* ";
			}

			$dir = $ascending ? 'ASC' : 'DESC';
			$order = $sort_field ? "ORDER BY {$this->marshal($sort_field, 'field')} $dir ": '';

			//var_dump("SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}");

			return "SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}";
		}

		protected function populate(int $requirement_id, &$requirement)
		{
			if($requirement == null)
			{
				$requirement = new logistic_requirement((int) $requirement_id);

				$requirement->set_activity_id($this->unmarshal($this->db->f('activity_id'), 'int'));
				$requirement->set_date_from($this->unmarshal($this->db->f('date_from'), 'int'));
				$requirement->set_date_to($this->unmarshal($this->db->f('date_to'), 'int'));
			}

			return $requirement;
		}

		public static function get_instance()
		{
			if (self::$so == null)
			{
				self::$so = CreateObject('logistic.sorequirement');
			}
			return self::$so;
		}
	}