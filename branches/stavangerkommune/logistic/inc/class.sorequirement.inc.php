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
 	* @version $Id: class.sorequirement.inc.php 11257 2013-08-10 11:40:56Z sigurdne $
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
				'start_date',
				'end_date',
				'no_of_elements',
				'location_id',
				'create_user',
				'create_date'
			);

			$values = array(
				$this->marshal($requirement->get_activity_id(), 'int'),
				$this->marshal($requirement->get_start_date(), 'int'),
				$this->marshal($requirement->get_end_date(), 'int'),
				$this->marshal($requirement->get_no_of_items(), 'int'),
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
				'start_date=' . $this->marshal($requirement->get_start_date(), 'int'),
				'end_date=' . $this->marshal($requirement->get_end_date(), 'int'),
				'no_of_elements=' . $this->marshal($requirement->get_no_of_items(), 'int'),
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


		/**
		* Called from uirequirement - where transactions are initiated
		*/
		public function delete($id)
		{
			$id = (int) $id;

			if ( !$this->db->get_transaction() )
			{
				throw new Exception('sorequirement::delete() really need to be part of a transaction');
				return false;	
			}

			$result = $this->db->query("DELETE FROM lg_requirement WHERE id={$id}", __LINE__,__FILE__);
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
			
			return "SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}";
		}

		protected function populate(int $requirement_id, &$requirement)
		{
			if($requirement == null)
			{
				$requirement = new logistic_requirement((int) $requirement_id);

				$requirement->set_activity_id($this->unmarshal($this->db->f('activity_id'), 'int'));
				$requirement->set_start_date($this->unmarshal($this->db->f('start_date'), 'int'));
				$requirement->set_end_date($this->unmarshal($this->db->f('end_date'), 'int'));
				$requirement->set_no_of_items($this->unmarshal($this->db->f('no_of_elements'), 'int'));
				$requirement->set_location_id($this->unmarshal($this->db->f('location_id'), 'int'));
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
