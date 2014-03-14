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
 	* @version $Id: class.soproject.inc.php 10425 2012-11-02 09:13:14Z vator $
	*/

	phpgw::import_class('logistic.socommon');

	include_class('logistic', 'project', 'inc/model/');

	class logistic_soproject extends logistic_socommon
	{
		protected static $so;
		protected $db3;

		public function __construct()
		{
			parent::__construct();
			$this->db3 = clone $this->db;
		}

		/**
		 * Get a static reference to the storage object associated with this model object
		 *
		 * @return controller_soparty the storage object
		 */
		public static function get_instance()
		{
			if (self::$so == null)
			{
				self::$so = CreateObject('logistic.soproject');
			}
			return self::$so;
		}

		protected function add(&$project)
		{		
			$cols = array(
				'name',
				'project_type_id',
				'description',
				'create_user',
				'create_date',
				'start_date',
				'end_date'
			);

			$user_id = $GLOBALS['phpgw_info']['user']['id'];
			$now = time();
			
			$values = array(
				$this->marshal($project->get_name(), 'string'),
				$this->marshal($project->get_project_type_id(), 'int'),
				$this->marshal($project->get_description(), 'string'),
				$user_id,
				$now,
				$this->marshal($project->get_start_date(), 'int'),
				$this->marshal($project->get_end_date(), 'int')
			);

			$sql = 'INSERT INTO lg_project (' . join(',', $cols) . ') VALUES (' . join(',', $values) . ')';

			$result = $this->db->query($sql, __LINE__,__FILE__);

			if($result)
			{
				// Set the new project ID
				return $this->db->get_last_insert_id('lg_project', 'id');
			}
			else
			{
				return 0;
			}
		}


		protected function update($project)
		{
			$id = intval($project->get_id());

			$values = array(
				'name = ' . $this->marshal($project->get_name(), 'string'),
				'description = ' . $this->marshal($project->get_description(), 'string'),
				'project_type_id = ' . $this->marshal($project->get_project_type_id(), 'int'),
				'start_date = ' . $this->marshal($project->get_start_date(), 'int'),
				'end_date = ' . $this->marshal($project->get_end_date(), 'int'),
			);

			$result = $this->db->query('UPDATE lg_project SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);

			if( $result )
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
			$project_type = false;
			$table_alias = 'project';
			if($search_type && $search_type == 'project_type')
			{
				$project_type = true;
				$table_alias = 'project_type';
			}

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
				$filter_clauses[] = "{$table_alias}.id = {$this->marshal($filters[$this->get_id_field_name()],'int')}";
			}
			if(isset($filters['project_type']) && (!$filters['project_type'] == '' || !$filters['project_type'] == 0))
			{
				$filter_clauses[] = "{$table_alias}.project_type_id = {$this->marshal($filters['project_type'], 'int')}";
			}

			if(count($filter_clauses))
			{
				$clauses[] = join(' AND ', $filter_clauses);
			}

			$condition =  join(' AND ', $clauses);

			//$joins = " {$this->left_join} controller_control_area ON (controller_procedure.control_area_id = controller_control_area.id)";

			if($project_type)
			{
				$tables = "lg_project_type project_type";
			}
			else
			{
				$tables = "lg_project project";
			}

			if($return_count) // We should only return a count
			{
				$cols = 'COUNT(DISTINCT('.$table_alias.'.id)) AS count';
			}
			else
			{
				$cols .= "$table_alias.* ";
			}

			$dir = $ascending ? 'ASC' : 'DESC';
			$order = $sort_field ? "ORDER BY {$this->marshal($sort_field, 'field')} $dir ": '';

			//var_dump("SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}");

			return "SELECT {$cols} FROM {$tables} WHERE {$condition} {$order}";
		}

		protected function populate(int $project_id, &$project)
		{
			if($project == null)
			{
				$project = new logistic_project((int) $project_id);

				$project->set_name($this->unmarshal($this->db->f('name'), 'string'));
				$project->set_description($this->unmarshal($this->db->f('description'), 'string'));
				$project->set_project_type_id($this->unmarshal($this->db->f('project_type_id'), 'int'));
				if($project->get_project_type_id() && $project->get_project_type_id() > 0)
				{
					$project->set_project_type_label($this->get_project_type_label($this->unmarshal($this->db->f('project_type_id'), 'int')));
				}
				$project->set_start_date($this->unmarshal($this->db->f('start_date'), 'int'));
				$project->set_end_date($this->unmarshal($this->db->f('end_date'), 'int'));
			}

			return $project;
		}

		public function get_projects()
		{
			$project_array = array();
			$project_array[] = array(
				'id' => '',
				'name' => lang('all_types'),
				'selected' => 1
			);
			$sql = "SELECT id, name FROM lg_project";
			$this->db->query($sql, __LINE__, __FILE__);

			while ($this->db->next_record())
			{
				$project_array[] = array(
						'id' => $this->db->f('id'),
						'name' => $this->unmarshal($this->db->f('name'), 'string')
						);
			}
			return $project_array;
		}


		public function get_project_type_label($id)
		{
			$sql = "SELECT name FROM lg_project_type where id=$id";
			$this->db3->query($sql, __LINE__, __FILE__);

			while ($this->db3->next_record())
			{
				return $this->db3->f('name');
			}
		}

		public function get_project_types($selected_id = null)
		{
			$project_type_array = array();
			if(!$selected_id)
			{
				$project_type_array[] = array(
					'id' => '',
					'name' => lang('all_types'),
					'selected' => 1
				);
			}
			else
			{
				$project_type_array[] = array(
					'id' => '',
					'name' => lang('all_types')
				);
			}
			$sql = "SELECT * FROM lg_project_type";
			$this->db->query($sql, __LINE__, __FILE__);

			while ($this->db->next_record())
			{
				if(!$selected_id == null && $this->db->f('id') == $selected_id)
				{
				$project_type_array[] = array(
						'id' => $this->db->f('id'),
						'name' => $this->unmarshal($this->db->f('name'), 'string'),
						'selected' => 1
						);
				}
				else
				{
					$project_type_array[] = array(
						'id' => $this->db->f('id'),
						'name' => $this->unmarshal($this->db->f('name'), 'string')
						);
				}
			}
			return $project_type_array;
		}

		public function update_project_type($id, $name)
		{
			$sql = "UPDATE lg_project_type set name='{$name}' where id={$id}";
			$result = $this->db->query($sql, __LINE__,__FILE__);

			if( $result )
			{
				return $id;
			}
			else
			{
				return 0;
			}
		}

		public function add_project_type($name)
		{
			$user_id = $GLOBALS['phpgw_info']['user']['id'];
			$now = time();
			$sql = "INSERT INTO lg_project_type (name, create_user, create_date) VALUES ('{$name}', $user_id, $now)";
			$result = $this->db->query($sql, __LINE__,__FILE__);

			if($result)
			{
				return $this->db->get_last_insert_id('lg_project_type', 'id');
			}
			else
			{
				return 0;
			}
		}
	}