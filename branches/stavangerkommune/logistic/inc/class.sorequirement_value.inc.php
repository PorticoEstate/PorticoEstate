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
 	* @version $Id: class.sorequirement_value.inc.php 11257 2013-08-10 11:40:56Z sigurdne $
	*/

	phpgw::import_class('logistic.socommon');

	include_class('logistic', 'requirement_value', '/inc/model/');

	class logistic_sorequirement_value extends logistic_socommon
	{
		protected static $so;

		protected function add(&$requirement_value)
		{
			$cols = array(
				'requirement_id',
				'operator',
				'value',
				'create_user',
				'create_date',
				'cust_attribute_id'
			);

			$values = array(
				$this->marshal($requirement_value->get_requirement_id(), 'int'),
				$this->marshal($requirement_value->get_operator(), 'string'),
				$this->marshal($requirement_value->get_value(), 'string'),
				$this->marshal($requirement_value->get_create_user(), 'int'),
				$this->marshal(strtotime('now'), 'int'),
				$this->marshal($requirement_value->get_cust_attribute_id(), 'int')
			);

			$sql = 'INSERT INTO lg_requirement_value (' . join(',', $cols) . ') VALUES (' . join(',', $values) . ')';

			$result = $this->db->query($sql, __LINE__,__FILE__);

			if($result)
			{
				return $this->db->get_last_insert_id('lg_requirement_value', 'id');
			}
			else
			{
				return 0;
			}
		}

		protected function update($requirement_value)
		{
			$id = intval($requirement_value->get_id());
			
			$values = array(
				'requirement_id=' . $this->marshal($requirement_value->get_requirement_id(), 'int'),
				'operator=' . $this->marshal($requirement_value->get_operator(), 'string'),
				'value=' . $this->marshal($requirement_value->get_value(), 'string'),
				'cust_attribute_id=' . $this->marshal($requirement_value->get_cust_attribute_id(), 'int')
			);

			$result = $this->db->query('UPDATE lg_requirement_value SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);

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
					'table'			=> 'requirement_value', // alias
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
						$like_clauses[] = "requirement_value.value $this->like $like_pattern";
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
				$filter_clauses[] = "requirement_value.id = {$this->marshal($filters[$this->get_id_field_name()],'int')}";
			}
			else if(isset($filters['requirement_id']))
			{
				$filter_clauses[] = "lg_requirement_value.requirement_id = {$this->marshal($filters['requirement_id'], 'int')}";
			}
			
			if(count($filter_clauses))
			{
				$clauses[] = join(' AND ', $filter_clauses);
			}

			$condition =  join(' AND ', $clauses);

			$tables = "lg_requirement_value";

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

		protected function populate(int $id, &$requirement_value)
		{
			if($requirement_value == null)
			{
				$requirement_value = new logistic_requirement_value((int) $id);
		
				$requirement_value->set_requirement_id($this->unmarshal($this->db->f('requirement_id'), 'int'));
				$requirement_value->set_value($this->unmarshal($this->db->f('value'), 'string'));
				$requirement_value->set_operator($this->unmarshal($this->db->f('operator'), 'string'));
				$requirement_value->set_cust_attribute_id($this->unmarshal($this->db->f('cust_attribute_id'), 'int'));
			}

			return $requirement_value;
		}
		
		public function delete_values($requirement_id)
		{
			$requirement_id = (int) $requirement_id;
			$status = $this->db->query("DELETE FROM lg_requirement_value WHERE requirement_id = $requirement_id");
					
			if( $status )
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		public static function get_instance()
		{
			if (self::$so == null)
			{
				self::$so = CreateObject('logistic.sorequirement_value');
			}
			return self::$so;
		}
	}
