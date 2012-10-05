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
 	* @version $Id: class.soproject.inc.php 10111 2012-10-04 08:53:35Z erikhl $
	*/

	phpgw::import_class('logistic.socommon');

	include_class('logistic', 'bim_item_type_requirement', 'inc/model/');

	class logistic_sobim_type_requirement extends logistic_socommon
	{
		protected static $so;
		private $local_db;

		public function __construct()
		{
			parent::__construct();
			$this->local_db = clone $this->db;
		}

		protected function add(&$object)
		{

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
					'table'			=> 'project', // alias
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
						$like_clauses[] = "{$table_alias}.name $this->like $like_pattern";
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
				$filter_clauses[] = "lg_bim_item_type_requirement.id = {$this->marshal($filters[$this->get_id_field_name()],'int')}";
			}

			if(count($filter_clauses))
			{
				$clauses[] = join(' AND ', $filter_clauses);
			}

			$condition =  join(' AND ', $clauses);

			//$joins = " {$this->left_join} controller_control_area ON (controller_procedure.control_area_id = controller_control_area.id)";

			$tables = "lg_bim_item_type_requirement type_requirement";

			if($return_count) // We should only return a count
			{
				$cols = 'COUNT(DISTINCT(type_requirement.id)) AS count';
			}
			else
			{
				$cols .= "type_requirement.* ";
			}

			$dir = $ascending ? 'ASC' : 'DESC';
			$order = $sort_field ? "ORDER BY {$this->marshal($sort_field, 'field')} $dir ": '';

			//var_dump("SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}");

			return "SELECT {$cols} FROM {$tables} WHERE {$condition} {$order}";
		}

		protected function populate(int $id, &$bim_item_type_requirement)
		{
			if($bim_item_type_requirement == null)
			{
				$bim_item_type_requirement = new logistic_bim_item_type_requirement((int) $id);

				$bim_item_type_requirement->set_location_id($this->unmarshal($this->db->f('location_id'), 'int'));
				$bim_item_type_requirement->set_cust_attribute_id($this->unmarshal($this->db->f('cust_attribute_id'), 'int'));
				$bim_item_type_requirement->set_project_type_id($this->unmarshal($this->db->f('project_type_id'), 'int'));
			}

			return $project;
		}

		protected function update($object)
		{

		}

		public static function get_instance()
		{
			if (self::$so == null)
			{
				self::$so = CreateObject('logistic.sobim_type_requirement');
			}
			return self::$so;
		}
	}
